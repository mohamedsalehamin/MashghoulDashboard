<?php

namespace App\ProviderPanel\Filament\Resources;

use Filament\Schemas\Schema;
use App\CatalogModule\Models\Reservation\Rate;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\ProviderPanel\Filament\Resources\RateResource\Pages\ListRates;
use App\ProviderPanel\Filament\Resources\RateResource\Pages\ViewRate;
use App\ProviderPanel\Filament\Resources\RateResource\Widgets\RateSummary;
use App\ProviderPanel\Filament\Resources\ReservationResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RateResource extends Resource
{
    use HasTranslationLabel;

    protected static ?string $model = Rate::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-star';
    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('panel.rating');
    }

    public static function getPluralModelLabel(): string
    {
        return __('panel.ratings');
    }


    public static function getNavigationLabel(): string
    {
        return __('panel.ratings');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $provider = provider();
                if (!$provider) {
                    return $query->whereRaw('1 = 0'); // Return empty if no provider
                }

                // Get ratings for this provider (both reservation-based and manual)
                return $query->where(function ($q) use ($provider) {
                    // Reservation-based ratings
                    $q->whereHas('reservation', function ($subQ) use ($provider) {
                        $subQ->where('reservable_type', \App\UsersModule\Models\Provider::class)
                            ->where('reservable_id', $provider->id);
                    })
                    // OR manual ratings with this provider
                    ->orWhere(function ($subQ) use ($provider) {
                        $subQ->where('provider_id', $provider->user_id)
                            ->where('source', 'manual');
                    });
                })
                ->whereNull('parent_id') // Only top-level ratings, not replies
                ->where('is_approved', true)
                // Show only service type to avoid duplicates (place shown in same row)
                ->where(function ($q) {
                    $q->where('type', 'service')
                      ->orWhereNull('type');
                });
            })
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('customer_name')
                    ->label(__('forms.fields.customer_name'))
                    ->state(fn($record) => $record->reviewerDisplayName())
                    ->searchable(),

                // Service Rating Column
                TextColumn::make('service_rate_display')
                    ->label(__('panel.service_rate'))
                    ->state(function ($record) {
                        $serviceRating = $record->type === 'service' ? $record : $record->service_rating;
                        return $serviceRating?->rate ? str_repeat('⭐', $serviceRating->rate) : '-';
                    })
                    ->tooltip(function ($record) {
                        $serviceRating = $record->type === 'service' ? $record : $record->service_rating;
                        return $serviceRating?->comment ?? '';
                    }),

                // Place Rating Column
                TextColumn::make('place_rate_display')
                    ->label(__('panel.place_rate'))
                    ->state(function ($record) {
                        $placeRating = $record->type === 'place' ? $record : $record->place_rating;
                        return $placeRating?->rate ? str_repeat('⭐', $placeRating->rate) : '-';
                    })
                    ->tooltip(function ($record) {
                        $placeRating = $record->type === 'place' ? $record : $record->place_rating;
                        return $placeRating?->comment ?? '';
                    }),

                TextColumn::make('replies_count')
                    ->label(__('panel.replies'))
                    ->counts('replies')
                    ->badge()
                    ->color('info'),

                TextColumn::make('reservation.reservation_number')
                    ->label(__('panel.reservation'))
                    ->placeholder('-')
                    ->url(fn($record) => $record->reservation_id
                        ? ReservationResource::getUrl('view', ['record' => $record->reservation_id])
                        : null),

                TextColumn::make('created_at')
                    ->label(__('forms.fields.created_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->groups([
                Group::make('reservation_id')
                    ->label(__('panel.reservation')),
            ])
            ->groupingDirectionSettingHidden(true)
            ->defaultGroup('reservation_id')
            ->actions([
                ActionGroup::make([
                    Action::make('view')
                        ->label(__('sections.view'))
                        ->icon('heroicon-o-eye')
                        ->url(fn($record) => static::getUrl('view', ['record' => $record])),

                    Action::make('reply')
                        ->label(__('panel.reply'))
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('info')
                        ->form([
                            Textarea::make('comment')
                                ->label(__('panel.reply_comment'))
                                ->required()
                                ->rows(4)
                                ->columnSpanFull(),
                        ])
                        ->action(function (Rate $record, array $data) {
                            $record->createReply($data['comment'], auth()->id());
                            \Filament\Notifications\Notification::make()
                                ->title(__('panel.reply_added_successfully'))
                                ->success()
                                ->send();
                        })
                        ->visible(fn($record) => !$record->isReply()),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRates::route('/'),
            'view' => ViewRate::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationBadge(): ?string
    {
        $provider = provider();
        if (!$provider) {
            return null;
        }

        // Keep in sync with table()->modifyQueryUsing(): one row per reservation (service only; place is merged in UI).
        return static::getModel()::query()
            ->where(function ($q) use ($provider) {
                $q->whereHas('reservation', function ($subQ) use ($provider) {
                    $subQ->where('reservable_type', \App\UsersModule\Models\Provider::class)
                        ->where('reservable_id', $provider->id);
                })
                    ->orWhere(function ($subQ) use ($provider) {
                        $subQ->where('provider_id', $provider->user_id)
                            ->where('source', 'manual');
                    });
            })
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->where(function ($q) {
                $q->where('type', 'service')
                    ->orWhereNull('type');
            })
            ->count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['provider', 'user', 'reservation.reservable', 'reservation.customer', 'replies.user']);
    }

}
