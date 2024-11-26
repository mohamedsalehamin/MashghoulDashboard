<?php

namespace App\CatalogModule\Resources;

use App\DefaultPanel\Enum\PaymentMethods;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\UsersModule\Models\User\Doctor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\CatalogModule\Models\Plan;
use App\CatalogModule\Models\Subscription;
use App\CatalogModule\Resources\SubscriptionResource\Pages\ListSubscriptions;
use App\CatalogModule\Resources\SubscriptionResource\Pages\ViewSubscription;

class SubscriptionResource extends Resource {
    use HasTranslationLabel, Translatable;

    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label(__('forms.fields.doctor_name'))
                    ->options(function ($record) {
                        return Doctor::doesntHave('activeSubscription')->get()->pluck('name', 'id')->toArray();
                    })
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('panel.messages.customers_only_have_inactive_subscription_will_appear_here'))
                    ->required(),
                Select::make('plan_id')
                    ->live()
                    ->afterStateUpdated(function ($get, $set) {
                        $plan = Plan::find($get('plan_id'));
                        $set('price', $plan?->price->formatByDecimal());
                        $set('meta_data', $plan->meta_data);
                    })
                    ->options(Plan::all()->pluck('name', 'id')->toArray()),
                DatePicker::make('start_date')->native(false)
                    ->closeOnDateSelection()
                    ->displayFormat('d M Y')
                    ->extraAlpineAttributes(['locale' => 'en']),

                DatePicker::make('end_date')
                    ->closeOnDateSelection()
                    ->native(false)
                    ->required()
                    ->displayFormat('d M Y')
                    ->after("start_date"),

                TextInput::make('price')
                    ->suffix(__("forms.suffixes.sar"))
                    ->formatStateUsing(fn($record) => $record ? $record->price?->formatByDecimal() : null)
                    ->required(),
                Hidden::make('meta_data')->default([]),


            ])->columns(1);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->translateLabel()
                    ->searchable(),
                TextColumn::make('subscriber.name')
                    ->searchable(),
                TextColumn::make('plan.name')
                    ->searchable(),
                TextColumn::make('price')
                    ->searchable(),
                TextColumn::make('status')
                    ->color(fn($record) => $record->status->getColor())
                    ->badge(),

                TextColumn::make('transaction.status')
                    ->label(__('forms.fields.payment_status'))
                    ->formatStateUsing(fn($record) =>$record?->transaction?->status->getLabel())
                    ->color(fn($record) => $record?->transaction?->status->getColor())
                    ->badge(),

                TextColumn::make('start_date')->date("d M Y"),
                TextColumn::make('end_date')->date("d M Y"),


            ])
            ->filters([

            ])
            ->actions([
                ActionGroup::make([
                    Action::make('create_invoice')
                        ->label(__('forms.actions.create_invoice'))
                        ->action(function ($record) {
                            $transaction = $record->pay();
                            return redirect()->to($transaction->meta_data['invoiceURL']);
                        })
                        ->icon('heroicon-o-banknotes')
                        ->openUrlInNewTab(),
                    Tables\Actions\ViewAction::make(),
//                    Tables\Actions\EditAction::make(),
//                    Tables\Actions\DeleteAction::make(),
                ]),


            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
//            ->checkIfRecordIsSelectableUsing(fn(Model $record): bool => !$record->orders()->count())
            ->emptyStateActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->striped();
    }

    static public function infolist(Infolist $infolist): Infolist {
        return $infolist
            ->schema([
                Grid::make()->schema([
                    Section::make("basic_information")
                        ->schema([
                            TextEntry::make('id'),
                            TextEntry::make('subscriber.name'),
                            TextEntry::make('plan.name'),
                            TextEntry::make('price'),
                            TextEntry::make('start_date')->date("d M Y"),
                            TextEntry::make('end_date')->date("d M Y"),
                            TextEntry::make('status')->badge(),
                            TextEntry::make('features')
                                ->label(__('forms.fields.features'))
                                ->listWithLineBreaks()
                                ->bulleted(),

                        ])->columns(1),

                ])->columns(2)
            ]);
    }

    public static function getRelations(): array {
        return [
        ];
    }

    public static function getPages(): array {
        return [
            'index' => ListSubscriptions::route('/'),
            'view' => ViewSubscription::route('/{record}/view'),
        ];
    }


    public static function getNavigationBadge(): ?string {
        return static::getModel()::count();
    }

    public static function getGlobalSearchResultTitle(Model $record): string {
        return $record->name;
    }

    /**
     * @return string|null
     */
    public static function getNavigationGroup(): ?string {
        return __('menu.payments');
    }


}
