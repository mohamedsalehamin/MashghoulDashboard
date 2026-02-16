<?php

namespace App\CatalogModule\Resources\RateResource\Pages;

use App\CatalogModule\Models\Reservation\Rate;
use App\CatalogModule\Resources\RateResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Str;

class CreateRate extends CreateRecord
{
    protected static string $resource = RateResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Generate a unique pair_id to group service and place ratings together
        $pairId = Str::uuid()->toString();

        // Extract common data
        $commonData = [
            'provider_id' => $data['provider_id'],
            'user_id' => $data['user_id'] ?? auth()->id(),
            'pair_id' => $pairId, // Links service and place ratings together
            'source' => 'manual',
            'is_approved' => $data['is_approved'] ?? true,
            'approved_at' => ($data['is_approved'] ?? true) ? now() : null,
            'approved_by' => ($data['is_approved'] ?? true) ? auth()->id() : null,
        ];

        // Create service rating
        $serviceRate = Rate::create([
            ...$commonData,
            'type' => 'service',
            'rate' => $data['service_rate'],
            'comment' => $data['service_comment'],
        ]);

        // Create place rating
        Rate::create([
            ...$commonData,
            'type' => 'place',
            'rate' => $data['place_rate'],
            'comment' => $data['place_comment'],
        ]);

        Notification::make()
            ->title(__('panel.ratings_created_successfully'))
            ->body(__('panel.service_and_place_ratings_created'))
            ->success()
            ->send();

        // Return the first one for redirect purposes
        return $serviceRate;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
