<?php

namespace App\CatalogModule\Resources;

use App\CatalogModule\Models\Plan;
use App\CatalogModule\Models\PlanPrice;
use App\CatalogModule\Resources\PlanResource\Pages\CreatePlan;
use App\CatalogModule\Resources\PlanResource\Pages\EditPlan;
use App\CatalogModule\Resources\PlanResource\Pages\ListPlans;
use App\CatalogModule\Resources\PlanResource\Pages\ViewPlan;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class PlanResource extends Resource
{
    use HasTranslationLabel, Translatable;

    protected static ?string $model = Plan::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Label')
                    ->tabs([
                        Tab::make(__('panel.languages.arabic'))
                            ->schema([
                                TextInput::make('name.ar')
                                    ->label(__('forms.fields.name_ar'))
                                    ->required(),
                            ]),
                        Tab::make(__('panel.languages.english'))
                            ->schema([
                                TextInput::make('name.en')
                                    ->label(__('forms.fields.name_en'))
                                    ->required(),
                            ]),
                    ]),

                Section::make(__('forms.sections.plan_billing'))
                    ->schema([
                        Toggle::make('is_free')
                            ->label(__('forms.fields.is_free_plan'))
                            ->helperText(__('forms.help.is_free_plan'))
                            ->default(false),
                        TextInput::make('commission_percent')
                            ->label(__('forms.fields.commission_percent'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->placeholder('e.g. 5')
                            ->nullable()
                            ->helperText(__('forms.help.commission_percent')),
                    ])
                    ->columns(1),

                Section::make(__('forms.fields.features'))
                    ->schema([
                        Repeater::make('features')
                            ->schema([
                                Tabs::make('')->tabs([
                                    Tab::make('AR')->schema([
                                        TextInput::make('ar')
                                            ->label(__('forms.fields.name_ar'))
                                            ->required(),
                                    ]),
                                    Tab::make('EN')->schema([
                                        TextInput::make('en')
                                            ->label(__('forms.fields.name_en'))
                                            ->required(),
                                    ]),
                                ]),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel(__('panel.actions.add'))
                            ->reorderable()
                            ->collapsible(),
                    ])
                    ->collapsible(),

                Section::make(__('menu.plans') . ' - ' . __('forms.fields.price'))
                    ->schema([
                        Repeater::make('plan_prices_data')
                            ->label('')
                            ->schema([
                                TextInput::make('period')
                                    ->label(__('forms.fields.period'))
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('price')
                                    ->label(__('forms.fields.price'))
                                    ->numeric()
                                    ->suffix(__('forms.suffixes.sar'))
                                    ->required(),
                                TextInput::make('days_count')
                                    ->label(__('forms.fields.days_count'))
                                    ->numeric()
                                    ->required()
                                    ->default(30),
                            ])
                            ->default([
                                ['period' => PlanPrice::PERIOD_MONTHLY, 'price' => 0, 'days_count' => 30],
                                ['period' => PlanPrice::PERIOD_QUARTERLY, 'price' => 0, 'days_count' => 90],
                                ['period' => PlanPrice::PERIOD_YEARLY, 'price' => 0, 'days_count' => 365],
                            ])
                            ->addable(false)
                            ->reorderable(false),
                    ]),

                Toggle::make('status')
                    ->default(1)
                    ->onColor('success')
                    ->offColor('danger'),
            ])->columns(1);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('sections.basic_information'))
                    ->schema([
                        TextEntry::make('name_ar')
                            ->label(__('forms.fields.name_ar'))
                            ->state(fn (Plan $record): string => $record->translationString('name', 'ar')),
                        TextEntry::make('name_en')
                            ->label(__('forms.fields.name_en'))
                            ->state(fn (Plan $record): string => $record->translationString('name', 'en')),
                        TextEntry::make('is_free')
                            ->label(__('forms.fields.is_free_plan'))
                            ->formatStateUsing(function (?bool $state): string {
                                if ($state === true) {
                                    return __('forms.fields.is_free_plan');
                                }

                                return app()->getLocale() === 'ar' ? 'باقة مدفوعة' : 'Paid plan';
                            }),
                        TextEntry::make('commission_percent')
                            ->label(__('forms.fields.commission_percent'))
                            ->formatStateUsing(fn ($state) => $state !== null && $state !== '' ? $state.'%' : '—'),
                        TextEntry::make('status')
                            ->label(__('forms.fields.status'))
                            ->formatStateUsing(fn (?bool $state): string => $state ? __('panel.enums.active') : __('panel.enums.inactive')),
                    ])
                    ->columns(2),
                Section::make(__('forms.fields.features'))
                    ->schema([
                        TextEntry::make('features')
                            ->label('')
                            ->formatStateUsing(function ($state): string {
                                $features = is_array($state) ? $state : [];
                                if ($features === []) {
                                    return '';
                                }
                                $lines = [];
                                foreach ($features as $feature) {
                                    if (! is_array($feature)) {
                                        continue;
                                    }
                                    $ar = trim((string) ($feature['ar'] ?? ''));
                                    $en = trim((string) ($feature['en'] ?? ''));
                                    if ($ar === '' && $en === '') {
                                        continue;
                                    }
                                    $lines[] = ($ar !== '' ? $ar : '—').' / '.($en !== '' ? $en : '—');
                                }

                                return $lines !== [] ? implode("\n", $lines) : '';
                            })
                            ->markdown()
                            ->placeholder('—'),
                    ])
                    ->visible(fn (Plan $record): bool => ! empty($record->features)),
                Section::make(__('menu.plans').' - '.__('forms.fields.price'))
                    ->schema([
                        TextEntry::make('plan_price_summary')
                            ->label('')
                            ->state(fn (Plan $record): string => $record->planPrices
                                ->sortBy('period')
                                ->map(fn (PlanPrice $p) => $p->period_label.': '.$p->price->format())
                                ->join(' | ')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['planPrices'])->withCount('activeSubscriptions'))
            ->columns([
                TextColumn::make('id')
                    ->label(__('forms.fields.id'))
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                IconColumn::make('is_free')
                    ->label(__('forms.fields.is_free_plan'))
                    ->boolean(),
                TextColumn::make('commission_percent')
                    ->label(__('forms.fields.commission_percent'))
                    ->formatStateUsing(fn ($state) => $state !== null && $state !== '' ? $state.'%' : '—'),
                TextColumn::make('plan_prices_periods')
                    ->label(__('forms.fields.period'))
                    ->getStateUsing(fn ($record) => $record->planPrices->pluck('period')->unique()->implode(', ')),
                TextColumn::make('plan_prices_summary')
                    ->label(__('forms.fields.price'))
                    ->getStateUsing(fn ($record) => $record->planPrices->map(fn ($p) => $p->period . ': ' . $p->price->format())->join(' | ')),
                TextColumn::make('active_subscriptions_count')
                    ->label(__('panel.stats.active_subscriptions'))
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('status')
                            ->label(fn (Model $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
                            ->requiresConfirmation()
                            ->action(fn (Model $record) => $record->toggleStatus())
                    ),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlans::route('/'),
            'create' => CreatePlan::route('/create'),
            'edit' => EditPlan::route('/{record}/edit'),
            'view' => ViewPlan::route('/{record}/view'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('menu.payments');
    }

}
