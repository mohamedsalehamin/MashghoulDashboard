<?php

namespace App\ReportsModule\Resources;

use App\CatalogModule\Models\Subscription;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\ReportsModule\Filters\SubscriptionPaymentsLocationFilter;
use App\ReportsModule\Models\SubscriptionPayment;
use App\ReportsModule\Resources\SubscriptionPaymentResource\Pages\ListSubscriptionPayments;
use App\UsersModule\Models\Provider;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Excel;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class SubscriptionPaymentResource extends Resource
{
    use HasTranslationLabel;

    protected static ?string $model = SubscriptionPayment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $slug = 'subscription-payments';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->paid())
            ->columns([
                TextColumn::make('transactionable_id')
                    ->label(__('forms.fields.subscription_id'))
                    ->url(fn ($record) => \App\CatalogModule\Resources\SubscriptionResource::getUrl('view', ['record' => $record->transactionable_id]), true)
                    ->searchable(['id']),

                TextColumn::make('user.name')
                    ->label(__('forms.fields.provider_name'))
                    ->searchable(),

                TextColumn::make('subscription_plan_display')
                    ->label(__('menu.plan'))
                    ->state(fn ($record) => $record->transactionable instanceof Subscription
                        ? $record->transactionable->resolvedPlanName()
                        : '-'),

                TextColumn::make('subscription_period_display')
                    ->label(__('forms.fields.period'))
                    ->state(fn ($record) => $record->transactionable instanceof Subscription
                        ? $record->transactionable->resolvedPeriodLabel()
                        : '-'),

                TextColumn::make('user.phone')
                    ->label(__('forms.fields.phone'))
                    ->searchable(),

                TextColumn::make('meta_data.gateway')
                    ->label(__('forms.fields.payment_data_method'))
                    ->badge()
                    ->formatStateUsing(function ($record) {
                        $gateway = $record->meta_data['gateway'] ?? $record->meta_data['method'] ?? 'system';

                        return __('panel.gateways.'.$gateway);
                    }),

                TextColumn::make('meta_data.paid_at')
                    ->label(__('forms.fields.payment_data_paid_at'))
                    ->state(function ($record) {
                        $date = $record->meta_data['paid_at'] ?? $record->created_at?->toIso8601String() ?? $record->created_at;

                        return Carbon::parse($date)->timezone('africa/cairo')->translatedFormat('Y-m-d h:i a');
                    }),

                TextColumn::make('price')
                    ->formatStateUsing(fn ($record) => $record->price->format())
                    ->summarize(Sum::make()->money('SAR')),

                TextColumn::make('e_invoice_url')
                    ->state(fn ($record) => ($record->meta_data['gateway'] ?? '') !== 'tabby' && isset($record->meta_data['invoiceURL']) ? __('forms.fields.show_invoice') : __('forms.fields.no_invoice'))
                    ->url(fn ($record) => ($record->meta_data['gateway'] ?? '') !== 'tabby' && isset($record->meta_data['invoiceURL']) ? $record->meta_data['invoiceURL'] : '', true),

                TextColumn::make('created_at')
                    ->formatStateUsing(fn ($record) => $record->created_at->format('Y-m-d'))
                    ->searchable(),
            ])
            ->filters([
                SubscriptionPaymentsLocationFilter::make(),
                Filter::make('created_at')
                    ->schema([
                        Select::make('provider_id')
                            ->options(fn () => Provider::pluck('name', 'id'))
                            ->label(__('forms.fields.provider'))
                            ->nullable()
                            ->searchable(),
                        DatePicker::make('date_from'),
                        DatePicker::make('date_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['provider_id'] ?? '', fn (Builder $q, $provider_id): Builder => $q->whereHas('transactionable.subscriber.provider', fn ($b) => $b->where('id', $provider_id)))
                            ->when($data['date_from'] ?? '', fn (Builder $q, $date): Builder => $q->whereDate('created_at', '>=', $date))
                            ->when($data['date_until'] ?? '', fn (Builder $q, $date): Builder => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()->exports([
                        ExcelExport::make('CSV')
                            ->fromTable()
                            ->withFilename(fn () => static::getPluralLabel().'-'.now()->format('Y-m-d'))
                            ->withWriterType(Excel::XLSX),
                    ]),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubscriptionPayments::route('/'),
        ];
    }

    public static function getPluralLabel(): ?string
    {
        return __('menu.subscription_payments');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('menu.reports');
    }

    public static function getMaxContentWidth(): Width
    {
        return Width::Full;
    }
}
