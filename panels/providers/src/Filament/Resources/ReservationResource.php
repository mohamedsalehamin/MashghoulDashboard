<?php

namespace App\ProviderPanel\Filament\Resources;

use App\CatalogModule\Models\Reservation;
use App\CatalogModule\Resources\ConsultingReservationResource\Actions\CancelReservationAction;
use App\CatalogModule\Resources\ConsultingReservationResource\Actions\CompleteReservationAction;
use App\CatalogModule\Resources\ConsultingReservationResource\Actions\PatientNotAttendReservationAction;
use App\CatalogModule\Resources\ConsultingReservationResource\Actions\ScheduleReservationAction;
use App\CatalogModule\Resources\ConsultingReservationResource\Actions\WritePrescriptionAction;
use App\DefaultPanel\Actions\GetRefundTransactionStatusAction;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Enum\ServicesTypeEnum;
use App\DefaultPanel\Enum\TimesTypeEnum;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\ProviderPanel\Filament\Resources\ReservationResource\Pages\ListReservations;
use App\ProviderPanel\Filament\Resources\ReservationResource\Pages\ViewReservation;
use App\ProviderPanel\Filament\Resources\ReservationResource\RelationManagers\ItemsLineRelationManager;
use App\ProviderPanel\Filament\Resources\ReservationResource\Widgets\CalendarWidget;
use App\UsersModule\Models\Doctor;
use App\UsersModule\Models\Lab;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
            ->modifyQueryUsing(fn($query) => $query
                ->where('reservable_type', Lab::class)
                ->where('reservable_id', provider()->id)
                ->whereNull('parent_id')
            )
            ->columns([
                TextColumn::make('id')->searchable(),
                TextColumn::make('patient.name')->label(__('forms.fields.patient_name'))->searchable(),
                TextColumn::make('date')
                    ->date()
                    ->searchable(),
                TextColumn::make('period')->searchable(),
                TextColumn::make('price')
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('forms.fields.status'))
                    ->color(fn($record) => $record?->status?->getColor())
                    ->badge(),
                TextColumn::make('transaction.status')
                    ->label(__('forms.fields.payment_status'))
                    ->color(fn($record) => $record?->transaction?->status?->getColor())
                    ->badge(),

                TextColumn::make('price'),


            ])
            ->filters([
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

    public static function canView(Model $record): bool {
        return true;
    }

    static public function infolist(Infolist $infolist): Infolist {
        return $infolist
            ->schema([
                Grid::make()->schema([
                    Group::make([
                        Section::make("basic_information")
                            ->schema([
                                TextEntry::make('id'),
                                TextEntry::make('patient.name'),
                                TextEntry::make('date')->date(),
                                TextEntry::make('period'),
                                TextEntry::make('price'),
                                TextEntry::make('reserve_type'),
                                TextEntry::make('service_type'),
                                TextEntry::make('status')
                                    ->label(__('forms.fields.status'))
                                    ->color(fn($record) => $record?->status?->getColor())
                                    ->badge(),
                                TextEntry::make('transaction.status')
                                    ->label(__('forms.fields.payment_status'))
                                    ->helperText(fn($record) => isset($record->transaction->meta_data['refund_data']['RefundId']) ? "Refund status: " . GetRefundTransactionStatusAction::run($record->transaction->meta_data['refund_data']['RefundId']) : '')
                                    ->color(fn($record) => $record?->transaction?->status?->getColor())
                                    ->badge(),

                            ])
                            ->columns(4),
                    ])
                        ->columnSpan(3),
                    Group::make([
                        ActivitySection::make('timeline')
                            ->label(__('sections.timeline'))
                            ->schema(components: [
                                ActivityTitle::make('title.' . app()->getLocale()),
                                ActivityDate::make('created_at')
                                    ->date('F j, Y h:i a'),
                                ActivityIcon::make('status')
                                    ->icon(fn(string $state) => ReservationStatus::tryFrom($state)->getIcon())
                                    ->color(fn(string|null $state): string|null => ReservationStatus::tryFrom($state)->getColor()),
                            ]),

                        Section::make("schedule_data")
                            ->visible(fn(Model $record) => $record->schedule()->exists())
                            ->relationship('schedule')
                            ->schema([
                                TextEntry::make('date'),
                                TextEntry::make('period'),
                            ])->columns(3),

                        Section::make("cancellation_data")
                            ->visible(fn(Model $record) => $record->cancellation()->exists())
                            ->relationship('cancellation')
                            ->schema([
                                TextEntry::make('reason.name'),
                                TextEntry::make('comment'),
                            ])->columns(3),

                        Section::make("report_data")
                            ->visible(fn(Model $record) => $record->report()->exists())
                            ->relationship('report')
                            ->schema([
                                TextEntry::make('reason.name'),
                                TextEntry::make('comment'),
                            ])->columns(3),

                        Section::make("rate_data")
                            ->visible(fn(Model $record) => $record->report()->exists())
                            ->relationship('rate')
                            ->schema([
                                TextEntry::make('rate'),
                                TextEntry::make('comment'),
                            ])->columns(3),

                    ])
                        ->columnSpan(2)
                ])->columns(5)
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
        return static::getModel()::belongsToAuthUser()->count();

    }
    public static function getWidgets(): array {
        return [
            CalendarWidget::make(),
        ];
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

    public static function can(string $action, ?Model $record = null): bool {
        return true;
    }

}
