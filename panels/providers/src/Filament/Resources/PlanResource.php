<?php

namespace App\ProviderPanel\Filament\Resources;

use App\CatalogModule\Models\Plan;
use App\CatalogModule\Models\PlanPrice;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Enum\SubscriptionsStatusEnum;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\ProviderPanel\Filament\Resources\PlanResource\Pages\ListPlans;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PlanResource extends Resource
{
    use HasTranslationLabel;

    protected static ?string $model = Plan::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->enabled())
            ->columns([
                TextColumn::make('id')->label(__('forms.fields.id'))->searchable(),
                TextColumn::make('name')->searchable(),
                IconColumn::make('is_free')
                    ->label(__('forms.fields.is_free_plan'))
                    ->boolean(),
                TextColumn::make('commission_percent')
                    ->label(__('forms.fields.commission_percent'))
                    ->formatStateUsing(fn ($state) => $state !== null && $state !== '' ? $state.'%' : '—'),
                TextColumn::make('planPrices.price')
                    ->label(__('forms.fields.price'))
                    ->formatStateUsing(fn ($record) => $record->planPrices->map(fn ($p) => $p->period_label . ': ' . $p->price->format())->join(' | ')),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('subscribe_online')
                        ->label(__('forms.actions.subscribe_via_online'))
                        ->form(fn (Model $record) => [
                            \Filament\Forms\Components\Select::make('plan_price_id')
                                ->label(__('forms.fields.period'))
                                ->options($record->planPrices->keyBy('id')->map(fn ($p) => $p->period_label)->toArray())
                                ->required(),
                        ])
                        ->action(function (Model $record, array $data) {
                            $planPrice = PlanPrice::find($data['plan_price_id']);
                            if (!$planPrice || $planPrice->plan_id !== $record->id) {
                                Notification::make()->title(__('panel.messages.warning'))->danger()->send();
                                return;
                            }
                            $url = $record->subscribe($planPrice, 'myfatoorah');
                            if ($url) {
                                return redirect()->away($url);
                            }
                            Notification::make()->title(__('panel.messages.warning'))->danger()->send();
                        })
                        ->icon('heroicon-o-credit-card'),
                    Action::make('subscribe_wallet')
                        ->label(__('forms.actions.subscribe_via_wallet'))
                        ->form(fn (Model $record) => [
                            \Filament\Forms\Components\Select::make('plan_price_id')
                                ->label(__('forms.fields.period'))
                                ->options($record->planPrices->keyBy('id')->map(fn ($p) => $p->period_label)->toArray())
                                ->required(),
                        ])
                        ->action(function (Model $record, array $data) {
                            $planPrice = PlanPrice::find($data['plan_price_id']);
                            if (!$planPrice || $planPrice->plan_id !== $record->id) {
                                Notification::make()->title(__('panel.messages.warning'))->danger()->send();
                                return;
                            }
                            $provider = provider();
                            $amount = (float) $planPrice->price->formatByDecimal();

                            if ($record->is_free || $amount <= 0) {
                                $record->createSubscriptionForProvider($planPrice, SubscriptionsStatusEnum::PROCESSING->value);
                                Notification::make()->title(__('panel.messages.success'))->success()->send();

                                return;
                            }

                            if ($provider->balance < $amount) {
                                Notification::make()
                                    ->title(__("panel.messages.you_dont_have_enough_money_to_pay"))
                                    ->danger()
                                    ->send();
                                return;
                            }
                            $provider->withdraw($amount, [
                                'description' => [
                                    'ar' => __("panel.messages.pay_subscription_via_wallet", ['amount' => $amount, 'id' => $record->id], 'ar'),
                                    'en' => __("panel.messages.pay_subscription_via_wallet", ['amount' => $amount, 'id' => $record->id], 'en'),
                                ],
                            ]);
                            $subscription = $record->createSubscriptionForProvider($planPrice, SubscriptionsStatusEnum::PROCESSING->value);
                            $subscription->transactions()->create([
                                'user_id' => $subscription->user_id,
                                'price' => $planPrice->price->formatByDecimal(),
                                'status' => ReservationPaymentStatus::PAID->value,
                            ]);
                            Notification::make()->title(__("panel.messages.success"))->success()->send();
                        })
                        ->icon('heroicon-o-banknotes'),
                ]),
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
            'index' => ListPlans::route('/'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('menu.subscriptions');
    }
}
