<?php

namespace App\ProviderPanel\Filament\Resources;

use App\CatalogModule\Models\Subscription;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\ProviderPanel\Filament\Resources\SubscriptionResource\Pages\ListSubscriptions;
use App\ProviderPanel\Filament\Resources\SubscriptionResource\Pages\ViewSubscription;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    use HasTranslationLabel;

    protected static ?string $model = Subscription::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function canView($record): bool
    {
        return auth()->check() && (int) $record->user_id === (int) auth()->id();
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('sections.basic_information'))
                    ->schema([
                        TextEntry::make('id')->label(__('forms.fields.id')),
                        TextEntry::make('plan_display')
                            ->label(__('menu.plan'))
                            ->state(fn (Subscription $record): string => $record->resolvedPlanName()),
                        TextEntry::make('period_display')
                            ->label(__('forms.fields.period'))
                            ->state(fn (Subscription $record): string => $record->resolvedPeriodLabel()),
                        TextEntry::make('price')->formatStateUsing(fn ($record) => $record->price->format()),
                        TextEntry::make('status')->color(fn ($record) => $record->status->getColor())->badge(),
                        TextEntry::make('transaction.status')
                            ->label(__('forms.fields.payment_status'))
                            ->formatStateUsing(fn ($record) => $record->transaction?->status?->getLabel())
                            ->color(fn ($record) => $record->transaction?->status?->getColor())
                            ->badge(),
                        TextEntry::make('start_date')->date('d M Y'),
                        TextEntry::make('end_date')->date('d M Y'),
                    ])
                    ->columns(2),
                Section::make(__('forms.fields.features'))
                    ->schema([
                        TextEntry::make('features')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                $features = $record->features ?? [];
                                if (empty($features)) {
                                    return null;
                                }
                                $locale = app()->getLocale();
                                $lines = [];
                                foreach ($features as $feature) {
                                    $text = is_array($feature)
                                        ? ($feature[$locale] ?? $feature['ar'] ?? $feature['en'] ?? '')
                                        : $feature;
                                    if (is_string($text) && $text !== '') {
                                        $lines[] = '• '.$text;
                                    }
                                }

                                return implode("\n", $lines);
                            })
                            ->markdown()
                            ->visible(fn ($record) => ! empty($record->features)),
                    ])
                    ->visible(fn ($record) => ! empty($record->features)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->belongsToAuthUser())
            ->columns([
                TextColumn::make('id')->label(__('forms.fields.id'))->searchable(),
                TextColumn::make('plan_display')
                    ->label(__('menu.plan'))
                    ->state(fn (Subscription $record): string => $record->resolvedPlanName()),
                TextColumn::make('period_display')
                    ->label(__('forms.fields.period'))
                    ->state(fn (Subscription $record): string => $record->resolvedPeriodLabel()),
                TextColumn::make('price')->formatStateUsing(fn ($record) => $record->price->format()),
                TextColumn::make('status')
                    ->color(fn ($record) => $record->status->getColor())
                    ->badge(),
                TextColumn::make('transaction.status')
                    ->label(__('forms.fields.payment_status'))
                    ->formatStateUsing(fn ($record) => $record->transaction?->status?->getLabel())
                    ->color(fn ($record) => $record->transaction?->status?->getColor())
                    ->badge(),
                TextColumn::make('start_date')->date('d M Y'),
                TextColumn::make('end_date')->date('d M Y'),
            ])
            ->actions([
                Action::make('renew')
                    ->label(__('forms.actions.renew'))
                    ->visible(fn ($record) => $record->status->value === 'pending')
                    ->action(function ($record) {
                        $result = $record->pay($record->price->formatByDecimal(), 'myfatoorah');
                        if (is_object($result) && isset($result->meta_data['invoiceURL'])) {
                            return redirect($result->meta_data['invoiceURL']);
                        }
                    })
                    ->icon('heroicon-o-banknotes'),
                ViewAction::make(),
            ])
            ->emptyStateActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubscriptions::route('/'),
            'view' => ViewSubscription::route('/{record}/view'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('menu.subscriptions_log');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('menu.subscriptions');
    }
}
