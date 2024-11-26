<?php

namespace App\ProviderPanel\Filament\Resources\ReservationResource\Widgets;

use App\CatalogModule\Models\Reservation;
use App\ProviderPanel\Filament\Resources\ReservationResource;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget {
    protected static string $view = 'filament-fullcalendar::fullcalendar';

    public function fetchEvents(array $info): array {

        return Reservation::query()
            ->belongsToAuthUser()
            ->whereNull('parent_id')
            ->where('date', '>=', $info['start'])
            ->where('date', '<=', $info['end'])
            ->get()
            ->map(
                fn($reservation) => [
                    'title' => __("forms.fields.reservation_id") . ' ' . $reservation->id,
                    'start' => $reservation->date->setTimeFromTimeString(explode( ' - ',$reservation->period)[0]),
                    'end' =>$reservation->date->setTimeFromTimeString(explode( ' - ',$reservation->period)[1]),
                    'url' => ReservationResource::getUrl(name: 'view', parameters: ['record' => $reservation->id]),
                    'shouldOpenUrlInNewTab' => true
                ]
            )
            ->all();
    }
}
