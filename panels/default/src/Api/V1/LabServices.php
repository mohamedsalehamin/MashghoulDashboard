<?php

namespace App\DefaultPanel\Api\V1;

use App\DefaultPanel\Actions\Labs\BuildCartInstanceAction;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Enum\ServicesTypeEnum;
use App\DefaultPanel\Enum\TimesTypeEnum;
use App\DefaultPanel\Requests\Api\Labs\AppointmentDetailsRequest;
use App\DefaultPanel\Requests\Api\Doctor\AvailableTimesRequest;
use App\DefaultPanel\Requests\Api\Labs\ReserveAppointmentRequest;
use App\DefaultPanel\Resources\Api\Labs\ReservationResource;
use App\DefaultPanel\Resources\Api\LabResource;
use App\DefaultPanel\Resources\Api\LabServiceResource;
use App\DefaultPanel\Resources\Api\LightLabResource;
use App\UsersModule\Models\Lab;
use App\UsersModule\Models\Lab\Service;
use Carbon\Carbon;
use Tasawk\Api\Core;
use Tasawk\Api\Facade\Api;

class LabServices {
    public function index(): Core {
        $labs = Lab::filtered()
            ->active()
            ->whereHas('user.services')
            ->latest()
            ->get()
            ->when(request()->has('available'), fn($labs) => $labs->filter->isAvailableToday())
            ->paginate(15);

        return Api::isOk('List of labs', LightLabResource::collection($labs));
    }

    public function show(Lab $lab) {
        return Api::isOk(__("lab information"), LabResource::make($lab));
    }

    public function toggleFavorite(Lab $lab): Core {
        $lab->toggleFavorite();
        return Api::isOk(__("lab information"), LabResource::make($lab));
    }

    /**
     * @param AvailableTimesRequest $request
     * @param Lab $lab
     * @return Core
     */
    public function availableTimes(AvailableTimesRequest $request, Lab $lab): Core {
        return Api::isOk(__("Available times"), $lab->availableTimes($request->date('date')));
    }

    /**
     * @param AppointmentDetailsRequest $request
     * @param Lab $lab
     * @return Core
     */
    public function appointmentDetails(AppointmentDetailsRequest $request, Lab $lab): Core {
        $cart = BuildCartInstanceAction::run();
        [$from, $to] = explode(" - ", $request->input('time'));
        return Api::isOk(__("Appointment details"), [
            'lab' => LightLabResource::make($lab),
            'services' => LabServiceResource::collection(Service::findMany($request->input('services'))),
            'date' => $request->input('date'),
            'time' => $request->input('time'),
            'duration' =>(string) Carbon::parse($to)->diffInMinutes($from),
            'totals' => $cart->formattedTotals()

        ]);
    }

    /**
     * @param ReserveAppointmentRequest $request
     * @param Lab $lab
     * @return Core
     */
    public function reserve(ReserveAppointmentRequest $request, Lab $lab): Core {
        $cart = BuildCartInstanceAction::run();

        $reservation = $lab->reservations()->create([
            'date' => $request->date('date'),
            'period'=>$request->get('time'),
            'user_id' => auth()->id(),
            'service_type' => ServicesTypeEnum::OFFLINE,
            'reserve_type' => TimesTypeEnum::PRIORITY_OF_RESERVATION,
            'status' => ReservationStatus::PENDING,
            'price' => $cart->getTotal(),
        ]);
        $cart->saveItemsToOrder($reservation->id);
        $reservation->pay();
        return Api::isOk(__("Appointment reserved"), ReservationResource::make($reservation));
    }


}
