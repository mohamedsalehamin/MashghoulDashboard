<?php

namespace App\CatalogModule\Resources;

use App\CatalogModule\Models\Reservation\Rate;
use App\CatalogModule\Resources\RateResource\Pages\CreateRate;
use App\CatalogModule\Resources\RateResource\Pages\EditRate;
use App\CatalogModule\Resources\RateResource\Pages\ListRates;
use App\CatalogModule\Resources\RateResource\Pages\ViewRate;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\Models\User;
use App\UsersModule\Models\Provider;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RateResource extends Resource
{
    use HasTranslationLabel;

    protected static ?string $model = Rate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static ?int $navigationSort = 3;

    // public static function getNavigationGroup(): ?string
    // {
    //     return __('panel.groups.catalog');
    // }

    public static function getModelLabel(): string
    {
        return __('panel.rating');
    }

    public static function getPluralModelLabel(): string
    {
        return __('panel.ratings');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // For editing existing records (non-manual or single record edit)
                Section::make(__('panel.rating_details'))
                    ->schema([
                        Select::make('provider_id')
                            ->label(__('panel.provider'))
                            ->options(function () {
                                return Provider::with('user')->get()->pluck('user.name', 'user_id');
                            })
                            ->searchable()
                            ->required()
                            ->preload()
                            ->disabled(fn($record) => $record && $record->source !== 'manual'),

                        Select::make('user_id')
                            ->label(__('panel.customer'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled(fn($record) => $record && $record->source !== 'manual'),

                        Hidden::make('source')
                            ->default('manual'),
                    ])
                    ->columns(2)
                    ->visible(fn($record) => $record !== null),

                // For editing existing top-level ratings (edit service + place together)
                Section::make(__('panel.service_rating'))
                    ->schema([
                        TextInput::make('service_rate')
                            ->label(__('panel.service_rate'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->required()
                            ->helperText(__('panel.rate_1_to_5'))
                            ->afterStateHydrated(function (TextInput $component, $state, $record) {
                                if (!$record || $record->isReply()) {
                                    return;
                                }
                                $service = $record->type === 'service' ? $record : $record->service_rating;
                                $component->state($service?->rate);
                            }),

                        Textarea::make('service_comment')
                            ->label(__('panel.service_comment'))
                            ->required()
                            ->rows(3)
                            ->columnSpanFull()
                            ->afterStateHydrated(function (Textarea $component, $state, $record) {
                                if (!$record || $record->isReply()) {
                                    return;
                                }
                                $service = $record->type === 'service' ? $record : $record->service_rating;
                                $component->state($service?->comment);
                            }),
                    ])
                    ->columns(2)
                    ->visible(fn($record) => $record !== null && !$record->isReply()),

                Section::make(__('panel.place_rating'))
                    ->schema([
                        TextInput::make('place_rate')
                            ->label(__('panel.place_rate'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->required()
                            ->helperText(__('panel.rate_1_to_5'))
                            ->afterStateHydrated(function (TextInput $component, $state, $record) {
                                if (!$record || $record->isReply()) {
                                    return;
                                }
                                $place = $record->type === 'place' ? $record : $record->place_rating;
                                $component->state($place?->rate);
                            }),

                        Textarea::make('place_comment')
                            ->label(__('panel.place_comment'))
                            ->required()
                            ->rows(3)
                            ->columnSpanFull()
                            ->afterStateHydrated(function (Textarea $component, $state, $record) {
                                if (!$record || $record->isReply()) {
                                    return;
                                }
                                $place = $record->type === 'place' ? $record : $record->place_rating;
                                $component->state($place?->comment);
                            }),
                    ])
                    ->columns(2)
                    ->visible(fn($record) => $record !== null && !$record->isReply()),

                // For creating new manual ratings - Service Rating
                Section::make(__('panel.service_rating'))
                    ->schema([
                        Select::make('provider_id')
                            ->label(__('panel.provider'))
                            ->options(function () {
                                return Provider::with('user')->get()->pluck('user.name', 'user_id');
                            })
                            ->searchable()
                            ->required()
                            ->preload(),

                        Select::make('user_id')
                            ->label(__('panel.customer'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('service_rate')
                            ->label(__('panel.service_rate'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->required()
                            ->helperText(__('panel.rate_1_to_5')),

                        Textarea::make('service_comment')
                            ->label(__('panel.service_comment'))
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(fn($record) => $record === null),

                // For creating new manual ratings - Place Rating
                Section::make(__('panel.place_rating'))
                    ->schema([
                        TextInput::make('place_rate')
                            ->label(__('panel.place_rate'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->required()
                            ->helperText(__('panel.rate_1_to_5')),

                        Textarea::make('place_comment')
                            ->label(__('panel.place_comment'))
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(fn($record) => $record === null),

                Section::make(__('panel.approval'))
                    ->schema([
                        Toggle::make('is_approved')
                            ->label(__('panel.approved'))
                            ->default(true)
                            ->helperText(__('panel.approval_helper')),

                        Placeholder::make('approved_info')
                            ->label(__('panel.approved_by'))
                            ->content(fn($record) => $record?->approvedByUser?->name ?? '-')
                            ->visible(fn($record) => $record && $record->is_approved),

                        Placeholder::make('approved_at_info')
                            ->label(__('panel.approved_at'))
                            ->content(fn($record) => $record?->approved_at?->format('Y-m-d H:i') ?? '-')
                            ->visible(fn($record) => $record && $record->is_approved),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('provider_name')
                    ->label(__('forms.fields.provider'))
                    ->state(fn($record) => $record->provider?->name ?? $record->reservation?->reservable?->name ?? '-')
                    ->searchable(query: function ($query, $search) {
                        $query->whereHas('provider', fn($q) => $q->where('name', 'like', "%{$search}%"))
                              ->orWhereHas('reservation.reservable', fn($q) => $q->where('name', 'like', "%{$search}%"));
                    }),

                TextColumn::make('customer_name')
                    ->label(__('forms.fields.customer_name'))
                    ->state(fn($record) => $record->user?->name ?? $record->reservation?->customer?->name ?? '-')
                    ->searchable(query: function ($query, $search) {
                        $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                              ->orWhereHas('reservation.customer', fn($q) => $q->where('name', 'like', "%{$search}%"));
                    }),

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

                TextColumn::make('source')
                    ->label(__('panel.source'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'reservation' => 'success',
                        'manual' => 'warning',
                        'reply' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'reservation' => __('panel.reservation'),
                        'manual' => __('panel.manual'),
                        'reply' => __('panel.reply'),
                        default => $state,
                    }),

                IconColumn::make('is_approved')
                    ->label(__('panel.approved'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('replies_count')
                    ->label(__('panel.replies'))
                    ->counts('replies')
                    ->badge()
                    ->color('info'),

                TextColumn::make('reservation.id')
                    ->label(__('panel.reservation'))
                    ->placeholder('-')
                    ->url(fn($record) => $record->reservation_id
                        ? route('filament.admin.resources.reservations.view', $record->reservation_id)
                        : null),

                TextColumn::make('created_at')
                    ->label(__('forms.fields.created_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->label(__('panel.source'))
                    ->options([
                        'reservation' => __('panel.reservation'),
                        'manual' => __('panel.manual'),
                    ]),

                TernaryFilter::make('is_approved')
                    ->label(__('panel.approved'))
                    ->trueLabel(__('panel.approved'))
                    ->falseLabel(__('panel.pending')),

                SelectFilter::make('provider_id')
                    ->label(__('forms.fields.provider'))
                    ->relationship('provider', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('created_at')
                    ->form([
                        DateTimePicker::make('from')
                            ->label(__('forms.fields.from')),
                        DateTimePicker::make('to')
                            ->label(__('forms.fields.to')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['to'], fn($q) => $q->whereDate('created_at', '<=', $data['to']));
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),

                    Action::make('approve')
                        ->label(__('panel.approve'))
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn($record) => !$record->is_approved)
                        ->action(fn($record) => $record->approve()),

                    Action::make('reject')
                        ->label(__('panel.reject'))
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn($record) => $record->is_approved && $record->source === 'manual')
                        ->action(fn($record) => $record->reject()),

                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('approve_selected')
                        ->label(__('panel.approve_selected'))
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->approve()),

                    BulkAction::make('reject_selected')
                        ->label(__('panel.reject_selected'))
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->reject()),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('panel.rating_details'))
                    ->schema([
                        TextEntry::make('provider.name')
                            ->label(__('panel.provider'))
                            ->state(fn($record) => $record->provider?->name ?? $record->reservation?->reservable?->name ?? '-'),

                        TextEntry::make('user.name')
                            ->label(__('panel.customer'))
                            ->state(fn($record) => $record->user?->name ?? $record->reservation?->customer?->name ?? '-'),

                        TextEntry::make('source')
                            ->label(__('panel.source'))
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'reservation' => 'success',
                                'manual' => 'warning',
                                'reply' => 'info',
                                default => 'gray',
                            }),

                        TextEntry::make('is_approved')
                            ->label(__('panel.status'))
                            ->badge()
                            ->formatStateUsing(fn($state) => $state ? __('panel.approved') : __('panel.pending'))
                            ->color(fn($state) => $state ? 'success' : 'warning'),

                        TextEntry::make('reservation.reservation_number')
                            ->label(__('panel.reservation'))
                            ->placeholder('-')
                            ->url(fn($record) => $record->reservation_id
                                ? route('filament.admin.resources.reservations.view', $record->reservation_id)
                                : null),

                        TextEntry::make('created_at')
                            ->label(__('forms.fields.created_at'))
                            ->dateTime(),
                    ])
                    ->columns(3),

                // Service Rating Section
                Section::make(__('panel.service_rating'))
                    ->schema([
                        TextEntry::make('service_rate_display')
                            ->label(__('panel.rate')),

                        TextEntry::make('service_comment_display')
                            ->label(__('panel.comment'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(fn($record) => !$record->isReply()),

                // Place Rating Section
                Section::make(__('panel.place_rating'))
                    ->schema([
                        TextEntry::make('place_rate_display')
                            ->label(__('panel.rate')),

                        TextEntry::make('place_comment_display')
                            ->label(__('panel.comment'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(fn($record) => !$record->isReply()),

                Section::make(__('panel.replies'))
                    ->schema([
                        TextEntry::make('replies')
                            ->label('')
                            ->html()
                            ->formatStateUsing(function ($record) {
                                // Get replies for both ratings in the pair
                                $replies = collect();
                                
                                // Get replies for current rating
                                $replies = $replies->merge($record->replies()->with('user')->get());
                                
                                // Get replies for paired rating if exists
                                if ($record->paired_rating) {
                                    $replies = $replies->merge($record->paired_rating->replies()->with('user')->get());
                                }
                                
                                // Sort by created_at
                                $replies = $replies->sortBy('created_at');
                                
                                if ($replies->isEmpty()) {
                                    return '<p class="text-gray-500">' . __('panel.no_replies') . '</p>';
                                }
                                $html = '<div class="space-y-4">';
                                foreach ($replies as $reply) {
                                    $html .= '<div class="p-4 bg-gray-50 rounded-lg">';
                                    $html .= '<div class="font-semibold">' . ($reply->user?->name ?? 'Provider') . '</div>';
                                    $html .= '<div class="text-sm text-gray-500">' . $reply->created_at->format('Y-m-d H:i') . '</div>';
                                    $html .= '<div class="mt-2">' . e($reply->comment) . '</div>';
                                    $html .= '</div>';
                                }
                                $html .= '</div>';
                                return $html;
                            }),
                    ])
                    ->visible(fn($record) => !$record->isReply()),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRates::route('/'),
            'create' => CreateRate::route('/create'),
            'view' => ViewRate::route('/{record}'),
            'edit' => EditRate::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['provider', 'user', 'reservation.reservable', 'reservation.customer', 'replies'])
            // Show only 'service' type to avoid duplicate rows (place rating shown in same row)
            // Also show ratings without type. Always hide replies from the table.
            ->where(function ($query) {
                $query->where('type', 'service')
                      ->orWhereNull('type');
            });
    }
}
