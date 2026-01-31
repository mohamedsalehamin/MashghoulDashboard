<?php

namespace App\ProviderPanel\Filament\Resources\RateResource\Pages;

use App\CatalogModule\Models\Reservation\Rate;
use App\ProviderPanel\Filament\Resources\RateResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;

class ViewRate extends ViewRecord
{
    protected static string $resource = RateResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        // Load the paired rating to ensure it's available in the view
        $recordModel = $this->getRecord();

        // Eager load relationships
        $recordModel->loadMissing([
            'provider',
            'user',
            'reservation.reservable',
            'reservation.customer',
            'replies.user'
        ]);

        // Load paired rating if pair_id exists
        if ($recordModel->pair_id) {
            $ratings = Rate::where('pair_id', $recordModel->pair_id)
                ->whereNull('parent_id')
                ->with(['replies.user'])
                ->get();

            $serviceRating = $ratings->firstWhere('type', 'service') ?? ($recordModel->type === 'service' ? $recordModel : null);
            $placeRating = $ratings->firstWhere('type', 'place') ?? ($recordModel->type === 'place' ? $recordModel : null);

            if ($serviceRating) {
                $recordModel->setRelation('serviceRating', $serviceRating);
            }
            if ($placeRating) {
                $recordModel->setRelation('placeRating', $placeRating);
            }
        } elseif ($recordModel->reservation_id) {
            $ratings = Rate::where('reservation_id', $recordModel->reservation_id)
                ->whereNull('parent_id')
                ->with(['replies.user'])
                ->get();

            $serviceRating = $ratings->firstWhere('type', 'service') ?? ($recordModel->type === 'service' ? $recordModel : null);
            $placeRating = $ratings->firstWhere('type', 'place') ?? ($recordModel->type === 'place' ? $recordModel : null);

            if ($serviceRating) {
                $recordModel->setRelation('serviceRating', $serviceRating);
            }
            if ($placeRating) {
                $recordModel->setRelation('placeRating', $placeRating);
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
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
                ->action(function (array $data) {
                    $record = $this->getRecord();
                    $record->createReply($data['comment'], auth()->id());
                    
                    Notification::make()
                        ->title(__('panel.reply_added_successfully'))
                        ->success()
                        ->send();

                    // Reload the record to show the new reply
                    $this->mount($record->id);
                })
                ->visible(fn() => !$this->getRecord()->isReply()),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('panel.rating_details'))
                    ->schema([
                        TextEntry::make('customer_name')
                            ->label(__('panel.customer'))
                            ->state(fn($record) => $record->user?->name ?? $record->reservation?->customer?->name ?? '-'),

                        TextEntry::make('reservation.reservation_number')
                            ->label(__('panel.reservation'))
                            ->placeholder('-')
                            ->url(fn($record) => $record->reservation_id
                                ? route('filament.providers.resources.reservations.view', $record->reservation_id)
                                : null),

                        TextEntry::make('created_at')
                            ->label(__('panel.created_at'))
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

                // Replies Section
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
                                if ($record->relationLoaded('serviceRating') && $record->serviceRating && $record->type !== 'service') {
                                    $replies = $replies->merge($record->serviceRating->replies()->with('user')->get());
                                }
                                if ($record->relationLoaded('placeRating') && $record->placeRating && $record->type !== 'place') {
                                    $replies = $replies->merge($record->placeRating->replies()->with('user')->get());
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
}





