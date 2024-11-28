<?php

namespace App\CatalogModule\Resources\ReservationResource\Widgets;

use App\DefaultPanel\Enum\ReservationStatus;
use App\UsersModule\Models\Doctor;
use App\UsersModule\Models\Users\Customer;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Cknow\Money\Money;
use DB;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReservationStats extends BaseWidget {
//    use HasWidgetShield;

    protected static ?int $sort = 1;

    protected function getStats(): array {
        $totalsStats = DB::table('reservations')
            ->where('reservable_id', provider()?->id)
            ->select(
                DB::raw("
                SUM(price) AS 'all',
                SUM(
		CASE WHEN status = 'pending'
		THEN
			price
		ELSE
			0
		END) AS 'pending',
		SUM(
		CASE WHEN status in( 'processing')
		THEN
			price
		ELSE
			0
		END) AS 'in_processing',
		SUM(
		CASE WHEN status = 'completed'
		THEN
			price
		ELSE
			0
		END) AS 'completed',

			SUM(
		CASE WHEN status in( 'canceled')
        THEN
            price
        ELSE
            0
        END) AS 'canceled'
"))->first();

        $sumStats = DB::table('reservations')
            ->where('reservable_id', provider()?->id)
            ->select(
                DB::raw("
                count(id) AS 'all',
                sum(
		CASE WHEN status = 'pending'
		THEN
			1
		ELSE
			0
		END) AS 'pending',
		sum(
		CASE WHEN status in( 'processing' )
		THEN
		1
		ELSE
			0
		END) AS 'in_processing',
		sum(
		CASE WHEN status = 'completed'
		THEN
			1
		ELSE
			0
		END) AS 'completed',
     sum(
		CASE WHEN status ='canceled'
        THEN
           1
        ELSE
            0
        END) AS 'canceled'

"))->first();
        return [

            Stat::make(__('panel.stats.customers'), Customer::whereHas('reservations', fn($q) => $q->where("reservable_id", provider()->id))->count()),
            Stat::make(__('panel.stats.reservations_total'), Money::parse($totalsStats->all)->format()),
            Stat::make(__('panel.stats.new_reservations_total'), Money::parse($totalsStats->pending)->format()),
            Stat::make(__('panel.stats.in_processing_reservations_total'), Money::parse($totalsStats->in_processing)->format()),
            Stat::make(__('panel.stats.completed_reservations_total'), Money::parse($totalsStats->completed)->format()),
            Stat::make(__('panel.stats.canceled_reservations_total'), Money::parse($totalsStats->canceled)->format()),
            Stat::make(__('panel.stats.reservations_count'), $sumStats->all),
            Stat::make(__('panel.stats.pending_reservations_count'), $sumStats->pending ?? 0)
                ->url(route('filament.lab-panel.resources.reservations.index', ['tableFilters[status][value]' => 'pending'])),
            Stat::make(__('panel.stats.in_processing_reservations_count'), $sumStats->in_processing ?? 0)
                ->url(route('filament.lab-panel.resources.reservations.index', ['tableFilters[status][value]' => 'processing'])),
            Stat::make(__('panel.stats.completed_reservations_count'), $sumStats->completed ?? 0)
                ->url(route('filament.lab-panel.resources.reservations.index', ['tableFilters[status][value]' => 'completed'])),


        ];
    }


}
