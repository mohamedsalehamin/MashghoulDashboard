<?php

namespace App\CatalogModule\Resources;

use App\CatalogModule\Models\Reservation;
use App\CatalogModule\Resources\ReservationResource\Actions\ChangeReservationStatusAction;
use App\CatalogModule\Resources\ReservationResource\RelationManagers\ItemsLineRelationManager;
use App\DefaultPanel\Actions\GetRefundTransactionStatusAction;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Enum\ServicesTypeEnum;
use App\DefaultPanel\Enum\TimesTypeEnum;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\CatalogModule\Resources\ReservationResource\Pages\ListReservations;
use App\CatalogModule\Resources\ReservationResource\Pages\ViewReservation;
use App\CatalogModule\Resources\ReservationResource\Widgets\CalendarWidget;
use App\UsersModule\Models\Doctor;
use App\UsersModule\Models\Lab;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use JaOcero\ActivityTimeline\Components\ActivityDate;
use JaOcero\ActivityTimeline\Components\ActivityIcon;
use JaOcero\ActivityTimeline\Components\ActivitySection;
use JaOcero\ActivityTimeline\Components\ActivityTitle;

class ReservationResource extends Resource {
    use HasTranslationLabel, Translatable;

    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form {

        return $form;
    }

    public static function table(Table $table): Table {

        return $table
            ->modifyQueryUsing(fn($query) => $query->paid()->latest())
            ->columns([
                TextColumn::make('id')->searchable(),
                TextColumn::make('reservable.name')->label(__('forms.fields.provider_name'))->searchable(),
                TextColumn::make('customer.name')->label(__('forms.fields.customer_name'))->searchable(),
                TextColumn::make('customer.phone')->label(__('forms.fields.phone'))->searchable(),
                TextColumn::make('seat.title')->label(__('forms.fields.seat_name'))->searchable(),
                TextColumn::make('duration')
                    ->formatStateUsing(fn($record) => $record->duration)
                    ->label(__('forms.fields.duration'))->searchable(false),


                TextColumn::make('from')->searchable(),
                TextColumn::make('to')->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->searchable(),
                TextColumn::make('date')
                    ->date()
                    ->searchable(),
                TextColumn::make('price')
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('forms.fields.status'))
                    ->color(fn($record) => $record?->status?->getColor())
                    ->badge(),
                TextColumn::make('transaction.status')
                    ->formatStateUsing(fn($record) => $record->getPaymentStatus()->getLabel())
                    ->label(__('forms.fields.payment_status'))
                    ->color(fn($record) => $record?->getPaymentStatus()?->getColor())
                    ->badge(),

                TextColumn::make('price'),


            ])
            ->filters([
                Filter::make('today_orders')
                    ->query(fn(Builder $query): Builder => $query->today())
                    ->default(),
                SelectFilter::make('status')
                    ->options(ReservationStatus::class),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('date_from'),
                        DatePicker::make('date_to'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),

                SelectFilter::make('payment_status')
                    ->query(fn(Builder $query, $data) => $query->when($data['value'], fn($query) => $query->whereHas('transaction', fn(Builder $query) => $query->where('status', $data['value']))))
                    ->options(ReservationPaymentStatus::class),

            ])
            ->actions([
                ChangeReservationStatusAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
//            ->checkIfRecordIsSelectableUsing(fn(Model $record): bool => !$record->orders()->count())
            ->emptyStateActions([
            ])
            ->striped();
    }


    static public function infolist(Infolist $infolist): Infolist {
        return $infolist
            ->schema([
                Grid::make()->schema([
                    Group::make([
                        Section::make("basic_information")
                            ->schema([
                                TextEntry::make('id'),
                                TextEntry::make('reservable.name')->label(__("forms.fields.provider_name"))
                                ->hint(fn($record)=>Reservation::where('user_id',$record->user_id)->first()->id == $record->id?__("forms.fields.first_reservation"):'')
                                ->hintColor('primary'),

                                TextEntry::make('customer.name'),
                                TextEntry::make('customer.phone'),
                                TextEntry::make('seat.title')->label(__("forms.fields.seat_name")),
                                TextEntry::make('date')->date(),
                                TextEntry::make('from'),
                                TextEntry::make('to'),

                                TextEntry::make('status')
                                    ->label(__('forms.fields.status'))
                                    ->color(fn($record) => $record?->status?->getColor())
                                    ->badge(),
                                TextEntry::make('transaction.status')
                                    ->formatStateUsing(fn($record) => $record->getPaymentStatus()->getLabel())
                                    ->label(__('forms.fields.payment_status'))
                                    ->helperText(fn($record) => isset($record->transaction->meta_data['refund_data']['RefundId']) ? "Refund status: " . GetRefundTransactionStatusAction::run($record->transaction->meta_data['refund_data']['RefundId']) : '')
                                    ->color(fn($record) => $record?->getPaymentStatus()->getColor())
                                    ->badge(),
                                TextEntry::make('duration')->label(__('forms.fields.duration')),
                                TextEntry::make('meta_data.points')->label(__('forms.fields.wining_points')),
                                Group::make()->schema(function ($record) {
                                    $totals = $record->as_cart->formattedTotals();

                                    return [
                                        TextEntry::make('services_total')->state(fn() => $totals['services_total']),
                                        TextEntry::make('products_total')->state(fn() => $totals['products_total']),
                                        TextEntry::make('coupon_discount')->state(function ($record) use ($totals) {
                                            $name = $record->as_cart->getConditions()->filter(fn($condition) => $condition->getType() == 'coupon')->first()?->getName();
                                            $code = $totals['discount'];
                                            if ($name) {
                                                return $name . "($code)";
                                            }

                                            return $code;
                                        }),
                                        TextEntry::make('reservation_fees')->state(fn() => $totals['reservation_fees']),
                                        TextEntry::make('wallet_discount')->state(fn() => $totals['wallet_discount']),
                                        TextEntry::make('total')->state(fn() => $totals['total']),
                                    ];
                                })
                                    ->columnSpan(4)
                                    ->columns(4)

                            ])
                            ->columns(4),
                        ActivitySection::make('timeline')
                            ->label(__('sections.timeline'))
                            ->schema(components: [
                                ActivityTitle::make('title.' . app()->getLocale()),
                                ActivityDate::make('created_at')
                                    ->date('F j, Y h:i a'),
                                ActivityIcon::make('status')
                                    ->icon(fn(string $state) => ReservationStatus::tryFrom($state)?->getIcon())
                                    ->color(fn(string|null $state): string|null => ReservationStatus::tryFrom($state)->getColor()),
                            ]),
                        Section::make("rate")
                            ->visible(fn($record) => $record->rate()->exists())
                            ->schema([
                                Fieldset::make('place')
                                    ->label(__('forms.fields.place_rate'))
                                    ->relationship('placeRate')
                                    ->schema([
                                        TextEntry::make('rate'),
                                        TextEntry::make('comment'),

                                    ])
                                    ->columnSpan(1),
                                Fieldset::make('service')
                                    ->label(__('forms.fields.service_rate'))
                                    ->relationship('serviceRate')
                                    ->schema([
                                        TextEntry::make('rate'),
                                        TextEntry::make('comment'),

                                    ])
                                    ->columnSpan(1)
                            ])
                            ->columnSpan(1)
                            ->columns(2),
                    ])


                ])->columns(1)
            ]);
    }

    public static function getRelations(): array {
        return [
            ItemsLineRelationManager::make(),

        ];
    }

    public static function getPages(): array {
        return [
            'index' => ListReservations::route('/'),
            'view' => ViewReservation::route('/{record}'),
        ];
    }


    public static function getNavigationBadge(): ?string {
        return static::getModel()::count();

    }

    public static function getGlobalSearchResultTitle(Model $record): string {
        return $record->name;
    }

    public static function getNavigationLabel(): string {
        return __('menu.reservations');
    }

    public static function getModelLabel(): string {
        return __('menu.reservation');
    }

    public static function getPluralModelLabel(): string {
        return __('menu.reservations');
    }


}
