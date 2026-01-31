<?php

namespace App\CatalogModule\Resources\RateResource\Pages;

use App\CatalogModule\Resources\RateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

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
        
        // Load both service and place ratings
        $serviceRating = null;
        $placeRating = null;
        
        if ($recordModel->pair_id) {
            // Load both ratings by pair_id
            $ratings = \App\CatalogModule\Models\Reservation\Rate::where('pair_id', $recordModel->pair_id)
                ->whereNull('parent_id')
                ->with(['replies.user'])
                ->get();
            
            $serviceRating = $ratings->firstWhere('type', 'service') ?? ($recordModel->type === 'service' ? $recordModel : null);
            $placeRating = $ratings->firstWhere('type', 'place') ?? ($recordModel->type === 'place' ? $recordModel : null);
        } elseif ($recordModel->reservation_id) {
            // Load both ratings by reservation_id
            $ratings = \App\CatalogModule\Models\Reservation\Rate::where('reservation_id', $recordModel->reservation_id)
                ->whereNull('parent_id')
                ->with(['replies.user'])
                ->get();
            
            $serviceRating = $ratings->firstWhere('type', 'service') ?? ($recordModel->type === 'service' ? $recordModel : null);
            $placeRating = $ratings->firstWhere('type', 'place') ?? ($recordModel->type === 'place' ? $recordModel : null);
        } else {
            // Fallback: if no pair_id or reservation_id, use current record if it matches
            if ($recordModel->type === 'service') {
                $serviceRating = $recordModel;
            } elseif ($recordModel->type === 'place') {
                $placeRating = $recordModel;
            }
        }
        
        // Set as relations for easy access in infolist
        if ($serviceRating) {
            $recordModel->setRelation('serviceRating', $serviceRating);
        }
        if ($placeRating) {
            $recordModel->setRelation('placeRating', $placeRating);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}

