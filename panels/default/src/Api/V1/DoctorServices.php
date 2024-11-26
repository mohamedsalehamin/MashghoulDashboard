<?php

namespace App\DefaultPanel\Api\V1;

use App\DefaultPanel\Actions\Doctor\BuildCartInstanceAction;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Requests\Api\Doctor\AppointmentDetailsRequest;
use App\DefaultPanel\Requests\Api\Doctor\AvailableTimesRequest;
use App\DefaultPanel\Requests\Api\Doctor\ReserveAppointmentRequest;
use App\DefaultPanel\Resources\Api\DoctorResource;
use App\DefaultPanel\Resources\Api\Doctors\ReservationResource;
use App\DefaultPanel\Resources\Api\DoctorServiceResource;
use App\DefaultPanel\Resources\Api\LightDoctorResource;
use App\UsersModule\Models\Doctor;
use App\UsersModule\Models\Service;
use Carbon\Carbon;
use DB;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Tasawk\Api\Core;
use Tasawk\Api\Facade\Api;

class DoctorServices {
    public function index(): Core {
        $user_location = [
            'lat' => request()->get('latitude', 0),
            'lng' => request()->get('longitude', 0),
        ];
        $name=request()->get('name');
        $doctors = Doctor::join("clinics", "doctors.id", "=", "clinics.doctor_id")
            ->select(['doctors.*', DB::raw('clinics.location as location')])

            ->when(request()->filled('specialty_id'), fn($query) => $query->where(fn($q) => $q->whereIn('specialty_id', request('specialty_id'))))
            ->when(request()->filled('city_id'), fn($query) => $query->whereHas('clinic', fn($q) => $q->where('city_id', request('city_id'))))
            ->when(request()->filled('name'), fn($query) => $query->where(fn($q) => $q->where('name', 'like', "%$name%")->orWhere('bio', 'like', "%$name%")))
            ->when(request()->filled('title_id'), fn($query) => $query->whereIn('title_id',request('title_id')))
            ->when(request()->filled('gender'), fn($query) => $query->whereHas('user',fn($q)=>$q->where('gender',request('gender'))))
            ->when(request()->filled('specializations'), fn($query) => $query->whereHas('specializations', fn($q) => $q->whereIn('specialization_id', request('specializations'))))
            ->withDistanceSphere('location', new Point($user_location['lat'], $user_location['lng']))
            ->whereHas('user.services')
            ->active()
            ->when(request()->has('available'), fn($doctors) => $doctors->filter->isAvailableToday())
            ->when(request()->get('order_by') == 'nearest', fn($query) => $query->orderBy('distance', 'asc'))
            ->when(request()->get('order_by') == 'farthest', fn($query) => $query->orderBy('distance', 'desc'))
            ->when(request()->get('order_by') == 'date', fn($query) => $query->orderBy('created_at', request()->get('order_dir', 'desc')))
            ->when(request()->get('order_by') == 'rating', fn($query) => $query->withAvg('rate', 'rate')->orderBy('rate_avg_rate', request()->get('order_dir', 'desc')))
            ->get();

        return Api::isOk('List of doctors', LightDoctorResource::collection($doctors));
    }

    public function show(Doctor $doctor): Core {
        return Api::isOk(__("doctor information"), DoctorResource::make($doctor));
    }

    public function toggleFavorite(Doctor $doctor): Core {
        $doctor->toggleFavorite();
        return Api::isOk(__("doctor information"), DoctorResource::make($doctor));

    }

    public function availableTimes(AvailableTimesRequest $request, Doctor $doctor): Core {

        return Api::isOk(__("Available times"), $doctor->availableTimes($request->date('date')));
    }

    public function appointmentDetails(AppointmentDetailsRequest $request, Doctor $doctor): Core {
        $cart = BuildCartInstanceAction::run();
        [$from, $to] = explode(" - ", $request->input('period'));

        return Api::isOk(__("Appointment details"), [
            'doctor' => LightDoctorResource::make($doctor),
            'service' => DoctorServiceResource::make(Service::find($request->input('service_id'))),
            'date' => $request->input('date'),
            'period' => $request->input('period'),
            'duration' => (string)Carbon::parse($to)->diffInMinutes($from),
            'totals' => $cart->formattedTotals()

        ]);
    }

    public function reserve(ReserveAppointmentRequest $request, Doctor $doctor): Core {
        $cart = BuildCartInstanceAction::run();

        $service = Service::find($request->input('service_id'));

        $reservation = $doctor->reservations()->create([
            'date' => $request->date('date'),
            'period' => $request->input('period'),
            'user_id' => auth()->id(),
            'service_type' => $service->type,
            'reserve_type' => $doctor->times_type,
            'status' => ReservationStatus::PENDING,
            'price' => $cart->getTotal(),
        ]);
        $cart->saveItemsToOrder($reservation->id);
        $reservation->pay();
        return Api::isOk(__("Appointment reserved"), ReservationResource::make($reservation));

    }

}
