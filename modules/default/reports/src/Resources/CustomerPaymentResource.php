<?php

namespace App\ReportsModule\Resources;

use App\DefaultPanel\Enum\ReservationStatus;
use App\ReportsModule\Filters\CustomerPaymentsLocationFilter;
use App\UsersModule\Models\Provider;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use App\DefaultPanel\Enum\PaymentMethods;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\ReportsModule\Models\CustomersPayment;

class CustomerPaymentResource extends Resource {
    use HasTranslationLabel;

    protected static ?string $model = CustomersPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->modifyQueryUsing(fn($query) => $query->paid())
            ->columns([


                TextColumn::make('transactionable_id')
                    ->label(__('forms.fields.reservation_id'))
                    ->url(fn($record) => route('filament.admin.resources.reservations.view', $record->transactionable_id), true)
                    ->searchable(['id']),

                TextColumn::make('user.name')
                    ->label(__('forms.fields.customer_name'))
                    ->searchable(),

                TextColumn::make('transactionable.reservable.name')
                    ->label(__("forms.fields.provider_name"))
                    ->searchable(false),

                TextColumn::make('user.phone')
                    ->label(__('forms.fields.phone'))
                    ->searchable(),


                TextColumn::make('meta_data.gateway')
                    ->label(__('forms.fields.payment_data_method'))
                    ->searchable(false)
                    ->badge()
                    ->formatStateUsing(function ($record) {
                        return __('panel.gateways.' . $record->meta_data['gateway']);
                    }),
                TextColumn::make('meta_data.paid_at')
                    ->label(__('forms.fields.payment_data_paid_at'))
                    ->state(function ($record) {
                        return Carbon::parse($record->meta_data['paid_at'] ?? $record->date)->timezone("africa/cairo")->translatedFormat("Y-m-d h:i a");
                    }),
                TextColumn::make('price')
                    ->searchable(),

                TextColumn::make('e_invoice_url')
                    ->state(fn($record) => $record->meta_data['gateway'] != 'tabby' && isset($record->meta_data['invoiceURL']) ? __('forms.fields.show_invoice') : __('forms.fields.no_invoice'))
                    ->url(fn($record) => $record->meta_data['gateway'] != 'tabby' && isset($record->meta_data['invoiceURL']) ? $record->meta_data['invoiceURL'] : '', true)
                    ->searchable(false),
                TextColumn::make('price')
                    ->formatStateUsing(fn($record) => $record->price->format())
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->money('SAR')),
                TextColumn::make('created_at')
                    ->formatStateUsing(fn($record) => $record->created_at->format('Y-m-d'))
                    ->searchable(),
            ])
            ->filters([


                CustomerPaymentsLocationFilter::make(),
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
                            ->when($data['provider_id'] ?? '', fn(Builder $query, $provider_id): Builder => $query->whereHas('transactionable.reservable', fn($builder) => $builder->where('id', $provider_id)))
                            ->when(
                                $data['date_from'] ?? '',
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['date_until'] ?? '',
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label(__("site.fields.download_invoice"))
                    ->action(fn($record) => redirect()->route('reservations.invoice', $record->transactionable_id))
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
        return [
            'index' => \App\ReportsModule\Resources\CustomerPaymentResource\Pages\ListCustomerPayments::route('/'),
        ];
    }

    public static function getPluralLabel(): ?string {
        return __('menu.customer_payments');
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.reports');
    }

}
