<?php

namespace App\ReportsModule\Resources;

use App\CatalogModule\Models\Commission;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Filters\CommissionLocationFilter;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\Notifications\AdminSendEntitlementsNotification;
use App\ReportsModule\Resources\LabCommissionResource\Pages\ListLabCommissionsResource;
use App\ReportsModule\Resources\LabCommissionResource\Pages\ListReservationCommissionsResource;
use App\UsersModule\Models\Lab;
use App\UsersModule\Models\Provider;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Money\Money;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ReservationCommissionResource extends Resource {
    use HasTranslationLabel;

    protected static ?string $model = Commission::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->modifyQueryUsing(fn($query) => $query
                ->whereHas('reservation', fn($builder) => $builder
                    ->paid()
                    // ->whereHas("conditions",fn($builder) => $builder->where('type', 'reservation_fees')->where("value", ">", 0))
                )
                ->where('amount', ">", 0))
            ->columns([


                TextColumn::make('id')
                    ->label(__('forms.fields.id'))
                    ->searchable(['id']),

                TextColumn::make('reservation.id')
                    ->label(__('forms.fields.reservation_id'))
                    // ->formatStateUsing(fn($record) => $record->reservation->reservation_number)
                    ->searchable(),


                TextColumn::make('reservation.reservable.name')
                    ->label(__('forms.fields.provider_name'))
                ,


                TextColumn::make('reservation.price')
                    ->label(__("forms.fields.reservation_total"))
                    ->formatStateUsing(fn($record) => \Cknow\Money\Money::parse($record->reservation->as_cart->getNetProfitTotal())->format())
                    ->searchable(),

                TextColumn::make('percentage')
                    ->formatStateUsing(fn($record) => $record->percentage . '%')
                    ->label(__('forms.fields.provider_commission_percentage'))
                    ->searchable(),


                TextColumn::make('amount')
                    ->label(__('forms.fields.commission_total'))
                    ->searchable(),


                TextColumn::make('doctor_total_gross_profit')
                    ->label(__('forms.fields.total_gross_profit'))
                    ->state(fn($record) => $record->profit())
//                    ->money()
                    ->searchable(false),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(false),
                TextColumn::make('created_at')
                    ->date()
                    ->searchable(),
            ])
            ->filters([
                CommissionLocationFilter::make(),
                Filter::make('created_at')
                    ->form([
                        Select::make('provider_id')
                            ->options(Provider::pluck('name', 'id'))
                            ->label(__('forms.fields.provider'))
                            ->nullable()
                            ->searchable(),
                        DatePicker::make('date_from'),
                        DatePicker::make('date_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['provider_id'] ?? '', fn(Builder $query, $provider_id): Builder => $query->whereHas('reservation.reservable', fn($builder) => $builder->where('id', $provider_id)))
                            ->when($data['date_from'] ?? '', fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),)
                            ->when(
                                $data['date_until'] ?? '',
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\Action::make('transfer')
                    ->icon('heroicon-o-currency-dollar')
                    ->hidden(fn($record) => $record->transferred)
                    ->requiresConfirmation()
                    ->action(function (Commission $record) {
                        $record->reservation->reservable->user->notify(new AdminSendEntitlementsNotification());
                        $record->reservation->reservable->deposit(
                            amount: $record->amount->formatByDecimal(),
                            meta: [
                                'description' => [
                                    'ar' => __('panel.messages.admin_transfer_lab_commission', ['AMOUNT' => $record->amount, 'ID' => $record->reservation_id], 'ar'),
                                    'en' => __('panel.messages.admin_transfer_lab_commission', ['AMOUNT' => $record->amount, 'ID' => $record->reservation_id], 'en'),
                                ],
                            ]
                        );
                        $record->update(['transferred' => true, 'confirmed' => true]);

                    })
                    ->label(__('forms.actions.transfer'))
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()->exports([
                        ExcelExport::make("CSV")
                            ->fromTable()
                            ->withFilename(fn() => static::getPluralLabel() . '-' . now()->format('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX),


                    ]),
                ]),
            ]);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return array(
            'index' => ListReservationCommissionsResource::route('/'),
        );
    }

    public static function getPluralLabel(): ?string {
        return __('menu.providers_reservations_dues');
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.reports');
    }

}
