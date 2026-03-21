<?php

namespace App\CatalogModule\Resources;

use App\CatalogModule\Models\Plan;
use App\CatalogModule\Models\Subscription;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\UsersModule\Models\Provider;
use App\CatalogModule\Resources\SubscriptionResource\Pages\CreateSubscription;
use App\CatalogModule\Resources\SubscriptionResource\Pages\EditSubscription;
use App\CatalogModule\Resources\SubscriptionResource\Pages\ListSubscriptions;
use App\CatalogModule\Resources\SubscriptionResource\Pages\ListSubscriptionActivities;
use App\CatalogModule\Resources\SubscriptionResource\Pages\ViewSubscription;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SubscriptionResource extends Resource
{
    use HasTranslationLabel;

    protected static ?string $model = Subscription::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(__('forms.fields.provider_name'))
                    ->options(fn () => Provider::whereHas('user')
                        ->get()
                        ->keyBy('user_id')
                        ->map(fn ($p) => $p->name)
                        ->toArray())
                    ->searchable()
                    ->required()
                    ->visibleOn('create'),
                Select::make('plan_id')
                    ->label(__('menu.plan'))
                    ->options(Plan::all()->pluck('name', 'id'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($set) {
                        $set('plan_price_id', null);
                        $set('start_date', null);
                        $set('end_date', null);
                    })
                    ->visibleOn('create'),
                Select::make('plan_price_id')
                    ->label(__('forms.fields.period'))
                    ->options(fn ($get) => $get('plan_id')
                        ? \App\CatalogModule\Models\PlanPrice::where('plan_id', $get('plan_id'))->get()->keyBy('id')->map(fn ($p) => $p->period_label)->toArray()
                        : [])
                    ->required()
                    ->live()
                    ->visibleOn('create')
                    ->afterStateUpdated(function ($state, $set) {
                        if (!$state) {
                            return;
                        }
                        $planPrice = \App\CatalogModule\Models\PlanPrice::find($state);
                        if ($planPrice) {
                            $set('start_date', now()->format('Y-m-d'));
                            $set('end_date', now()->addDays($planPrice->days_count)->format('Y-m-d'));
                        }
                    }),
                DatePicker::make('start_date')
                    ->required()
                    ->native(false)
                    ->displayFormat('d M Y')
                    ->visibleOn('create'),
                DatePicker::make('end_date')
                    ->required()
                    ->native(false)
                    ->displayFormat('d M Y')
                    ->after('start_date')
                    ->visibleOn('create'),
            ])->columns(1);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('sections.basic_information'))
                    ->schema([
                        TextEntry::make('id')->label(__('forms.fields.id')),
                        TextEntry::make('subscriber.name')->label(__('forms.fields.provider_name')),
                        TextEntry::make('plan.name')->label(__('menu.plan')),
                        TextEntry::make('planPrice.period_label')->label(__('forms.fields.period')),
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
                                        $lines[] = '• ' . $text;
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
            ->columns([
                TextColumn::make('id')->label(__('forms.fields.id'))->searchable(),
                TextColumn::make('subscriber.name')->label(__('forms.fields.provider_name'))->searchable(),
                TextColumn::make('plan.name')->searchable(),
                TextColumn::make('planPrice.period_label')->label(__('forms.fields.period')),
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
            ->filters([])
            ->recordActions([
                Action::make('activities')
                    ->label(__('forms.actions.activities'))
                    ->url(fn ($record) => static::getUrl('activities', ['record' => $record])),
                EditAction::make(),
                ViewAction::make(),
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
            'index' => ListSubscriptions::route('/'),
            'create' => CreateSubscription::route('/create'),
            'edit' => EditSubscription::route('/{record}/edit'),
            'view' => ViewSubscription::route('/{record}/view'),
            'activities' => ListSubscriptionActivities::route('/{record}/activities'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->plan?->name . ' - ' . $record->subscriber?->name;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('menu.payments');
    }

    public static function getNavigationLabel(): string
    {
        return __('menu.subscriptions_log');
    }
}
