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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
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

    protected static ?int $navigationSort = 15;

    public static function getNavigationGroup(): ?string
    {
        return __('panel.groups.catalog');
    }

    public static function getModelLabel(): string
    {
        return __('panel.rating');
    }

    public static function getPluralModelLabel(): string
    {
        return __('panel.ratings');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                            ->visible(fn($record) => !$record || $record->source === 'manual'),

                        Select::make('user_id')
                            ->label(__('panel.customer'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn($record) => !$record || $record->source === 'manual'),

                        TextInput::make('rate')
                            ->label(__('panel.rate'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->required()
                            ->visible(fn($record) => !$record || !$record->isReply()),

                        Textarea::make('comment')
                            ->label(__('panel.comment'))
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),

                        Select::make('type')
                            ->label(__('panel.type'))
                            ->options([
                                'service' => __('panel.service'),
                                'product' => __('panel.product'),
                                'general' => __('panel.general'),
                            ])
                            ->default('general')
                            ->visible(fn($record) => !$record || !$record->isReply()),

                        Hidden::make('source')
                            ->default('manual'),
                    ])
                    ->columns(2),

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
                    ->columns(3)
                    ->visible(fn($record) => !$record || $record->source === 'manual'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('provider.name')
                    ->label(__('panel.provider'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label(__('panel.customer'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rate')
                    ->label(__('panel.rate'))
                    ->formatStateUsing(fn($state) => $state ? str_repeat('⭐', $state) : '-')
                    ->sortable(),

                TextColumn::make('comment')
                    ->label(__('panel.comment'))
                    ->limit(50)
                    ->tooltip(fn($record) => $record->comment)
                    ->wrap(),

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

                TextColumn::make('reservation.reservation_number')
                    ->label(__('panel.reservation'))
                    ->placeholder('-')
                    ->url(fn($record) => $record->reservation_id
                        ? route('filament.admin.resources.reservations.view', $record->reservation_id)
                        : null),

                TextColumn::make('created_at')
                    ->label(__('panel.created_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->label(__('panel.source'))
                    ->options([
                        'reservation' => __('panel.reservation'),
                        'manual' => __('panel.manual'),
                        'reply' => __('panel.reply'),
                    ]),

                TernaryFilter::make('is_approved')
                    ->label(__('panel.approved'))
                    ->trueLabel(__('panel.approved'))
                    ->falseLabel(__('panel.pending')),

                SelectFilter::make('provider_id')
                    ->label(__('panel.provider'))
                    ->relationship('provider', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('top_level')
                    ->label(__('panel.top_level_only'))
                    ->query(fn(Builder $query) => $query->whereNull('parent_id'))
                    ->default(true),

                Filter::make('created_at')
                    ->form([
                        DateTimePicker::make('from')
                            ->label(__('panel.from')),
                        DateTimePicker::make('to')
                            ->label(__('panel.to')),
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

                    Action::make('view_replies')
                        ->label(__('panel.view_replies'))
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('info')
                        ->visible(fn($record) => $record->replies()->count() > 0)
                        ->url(fn($record) => static::getUrl('index', [
                            'tableFilters[parent_id][value]' => $record->id
                        ])),

                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    \Filament\Tables\Actions\BulkAction::make('approve_selected')
                        ->label(__('panel.approve_selected'))
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->approve()),

                    \Filament\Tables\Actions\BulkAction::make('reject_selected')
                        ->label(__('panel.reject_selected'))
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->reject()),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make(__('panel.rating_details'))
                    ->schema([
                        TextEntry::make('provider.name')
                            ->label(__('panel.provider')),

                        TextEntry::make('user.name')
                            ->label(__('panel.customer')),

                        TextEntry::make('rate')
                            ->label(__('panel.rate'))
                            ->formatStateUsing(fn($state) => $state ? str_repeat('⭐', $state) . " ($state/5)" : '-'),

                        TextEntry::make('source')
                            ->label(__('panel.source'))
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'reservation' => 'success',
                                'manual' => 'warning',
                                'reply' => 'info',
                                default => 'gray',
                            }),

                        TextEntry::make('comment')
                            ->label(__('panel.comment'))
                            ->columnSpanFull(),

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
                            ->label(__('panel.created_at'))
                            ->dateTime(),
                    ])
                    ->columns(3),

                InfoSection::make(__('panel.replies'))
                    ->schema([
                        TextEntry::make('replies')
                            ->label('')
                            ->html()
                            ->formatStateUsing(function ($record) {
                                $replies = $record->replies()->with('user')->get();
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
            ->with(['provider', 'user', 'reservation', 'replies']);
    }
}

