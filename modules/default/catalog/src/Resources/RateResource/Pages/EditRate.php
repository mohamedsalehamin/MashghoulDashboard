<?php

namespace App\CatalogModule\Resources\RateResource\Pages;

use App\CatalogModule\Models\Reservation\Rate;
use App\CatalogModule\Resources\RateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditRate extends EditRecord
{
    protected static string $resource = RateResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If approval status changed to approved
        if (($data['is_approved'] ?? false) && !$this->record->is_approved) {
            $data['approved_at'] = now();
            $data['approved_by'] = auth()->id();
        }
        
        // If approval status changed to not approved
        if (!($data['is_approved'] ?? true) && $this->record->is_approved) {
            $data['approved_at'] = null;
            $data['approved_by'] = null;
        }

        // These are virtual fields used to edit service/place together.
        unset($data['service_rate'], $data['service_comment'], $data['place_rate'], $data['place_comment']);

        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        /** @var Rate $record */
        if ($record->isReply()) {
            return parent::handleRecordUpdate($record, $data);
        }

        // Pull the virtual fields from the form state (still available via $this->data)
        $serviceRate = $this->data['service_rate'] ?? null;
        $serviceComment = $this->data['service_comment'] ?? null;
        $placeRate = $this->data['place_rate'] ?? null;
        $placeComment = $this->data['place_comment'] ?? null;

        // Find both ratings in the pair (pair_id preferred, else reservation_id)
        $query = Rate::query()->whereNull('parent_id');
        if ($record->pair_id) {
            $query->where('pair_id', $record->pair_id);
        } elseif ($record->reservation_id) {
            $query->where('reservation_id', $record->reservation_id);
        } else {
            // No pair info, fall back to updating current record only
            return parent::handleRecordUpdate($record, $data);
        }

        $ratings = $query->get();
        $service = $ratings->firstWhere('type', 'service');
        $place = $ratings->firstWhere('type', 'place');

        // Update common fields on both (provider/user/approval)
        $commonUpdate = [
            'provider_id' => $data['provider_id'] ?? $record->provider_id,
            'user_id' => $data['user_id'] ?? $record->user_id,
            'is_approved' => $data['is_approved'] ?? $record->is_approved,
            'approved_at' => $data['approved_at'] ?? $record->approved_at,
            'approved_by' => $data['approved_by'] ?? $record->approved_by,
        ];

        if ($service) {
            $service->update([
                ...$commonUpdate,
                'rate' => $serviceRate,
                'comment' => $serviceComment,
                'type' => 'service',
            ]);
        }

        if ($place) {
            $place->update([
                ...$commonUpdate,
                'rate' => $placeRate,
                'comment' => $placeComment,
                'type' => 'place',
            ]);
        }

        // Return the service record if possible (keeps table grouping logic consistent)
        return $service ?: $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

