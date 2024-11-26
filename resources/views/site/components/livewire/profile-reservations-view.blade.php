<div class="account-body">
    <div class="reservation-content">
        <div class="reservation-head">
            <h2 class="account-title">#{{$reservation->id}}</h2>
            <div class="reservation-tool">
                @if((!$sessionRunning && $reservation->isDoctorReservation() && $reservation->isOnline() && $reservation->isRunning()) || request()->has('VnaXGc5WGr8rIDfubolwTATQRDQ11MEx91DeYgJv'))
                    <button
                            id="startCallBtn"
                            class="reservation-btn book-btn"
                            wire:click="startSession"

                    >
                        <i class="fa-regular fa-messages"></i>
                        <span class="text">@lang('site.heading.start_session')</span>
                    </button>

                @endif
                @if($reservation->canRevisit())
                    <button
                            class="reservation-btn book-btn"
                            wire:click="$dispatch('openModal', { component: 'revisit-reservation-pop-up',arguments:{reservation:{{$reservation->id}}} })"

                    >
                        <i class="fa-regular fa-calendar"></i>
                        <span class="text">@lang('site.heading.book_a_return_appointment')</span>
                    </button>
                @endif
                @if(!$reservation->schedule()->exists() && !$reservation->completed() && $reservation->canReschedule())
                    <button
                            class="reservation-btn book-btn"
                            wire:click="$dispatch('openModal', { component: 'schedule-reservation-pop-up',arguments:{reservation:{{$reservation->id}}} })"

                    >
                        <i class="fa-regular fa-calendar"></i>
                        <span class="text">@lang('site.heading.reschedule_the_session_date')</span>
                        <span class="text"></span>
                    </button>
                @endif
                @if(!$reservation->rate()->exists() && $reservation->status == \App\DefaultPanel\Enum\ReservationStatus::COMPLETED )
                    <button
                            class="reservation-btn"
                            wire:click="$dispatch('openModal', { component: 'rate-reservation-pop-up',arguments:{reservation:{{$reservation->id}}} })"

                    >
                        <i class="fa-regular fa-star"></i>
                        <span class="text">@lang('site.heading.service_rate')</span>
                    </button>
                @endif
                @if($reservation->isDoctorReservation() && $reservation->completed() && $reservation->prescription()->exists())
                    <a class="reservation-btn"
                       href="{{route('profile.reservations.recommendations',$reservation->id)}}"
                    >
                        <i class="fa-regular fa-prescription"></i>
                        <span class="text">@lang('site.buttons.doctor_recommendations')</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="reservation-body" x-show="$wire.sessionRunning">
            <h3 class="reservation-title">@lang('site.heading.interactive_session')</h3>
            <div id="agora-react"></div>
        </div>

        <div class="reservation-body mt-2">
            @if(!$reservation->revisit()->exists() && $reservation->isDoctorReservation() &&$reservation->status == \App\DefaultPanel\Enum\ReservationStatus::COMPLETED)

                <div class="reservation-alert">
                    <i class="fa-regular fa-triangle-exclamation"></i>
                    @lang('site.heading.last_date_to_revisit',['date'=>$reservation->date->addDays(7)->translatedFormat("D d M Y")])
                </div>
            @endif
            <h3 class="reservation-title">@lang('site.heading.reservation_info')</h3>
            <div class="reservation-table">
                <div class="table-row">
                    <p>@lang('site.fields.id')</p>
                    <p>{{$reservation->reservation_number}}</p>
                </div>
                <div class="table-row">
                    <p>@lang('site.fields.created_at')</p>
                    <p>{{$reservation->created_at->translatedFormat("D d M Y")}}  </p>
                </div>
                <div class="table-row">
                    <p>@lang('site.heading.reservation_status')</p>
                    <p><span
                                class="status {{$reservation->status->getClass()}}"> {{$reservation->status->getLabel()}} </span>
                    </p>
                </div>
                @if($reservation->isLabReservation())
                    <div class="table-row">
                        <p>@lang('site.heading.lab')</p>
                        <p>
                            <a href="{{route('labs.show',$reservation->reservable->id)}}">{{$reservation->reservable->title}}</a>
                        </p>
                    </div>
                    <div class="table-row">
                        <p>@lang('site.fields.city')</p>
                        <p>
                            {{$reservation->reservable->city->name}}
                        </p>
                    </div>
                @endif
                @if($reservation->isDoctorReservation())
                    <div class="table-row">
                        <p>@lang('site.fields.doctor')</p>
                        <p>
                            <a href="{{route('doctors.show',$reservation->reservable->id)}}">{{$reservation->reservable->name}}</a>
                        </p>
                    </div>
                    <div class="table-row">
                        <p>@lang('site.fields.speciality')</p>
                        <p>
                            <a href="{{route('specialties.show',$reservation->reservable->specialization->id)}}">{{$reservation->reservable->specialization->name}} </a>
                        </p>
                    </div>
                    <div class="table-row">
                        <p>@lang('site.fields.secondary_specialization')</p>
                        <p>
                            <a>    {{$reservation->reservable->specializations->pluck('name')->implode(',')}}</a>
                        </p>
                    </div>
                    <div class="table-row">
                        <p>@lang('site.fields.city')  </p>
                        <p>{{$reservation->reservable->clinic->city->name}}  </p></div>
                    <div class="table-row">
                        <p>@lang('site.fields.service')</p>
                        <p>
                            @foreach($reservation->itemsline as $item)
                                @php($service=\App\UsersModule\Models\Service::find($item->model['id']))
                                {{$service->name[app()->getLocale()]??''}}
                            @endforeach
                        </p>
                    </div>
                    <div class="table-row">
                        <p>@lang('forms.fields.service_type')</p>
                        <p>{{$reservation->service_type->getLabel()}}</p>
                    </div>
                    <div class="table-row">
                        <p>@lang('forms.fields.reserve_type')</p>
                        <p>{{$reservation->reserve_type->getLabel()}}</p>
                    </div>
                    <div class="table-row">
                        <p>@lang('site.fields.reservation_duration')</p>
                        <p>{{$reservation->reservable?->session_duration}} @lang('site.enum.min')</p>
                    </div>
                @endif

                <div class="table-row">
                    <p>@lang('site.fields.date')</p>
                    <p>{{$reservation->date->translatedFormat("D d M Y")}}</p>
                </div>
                <div class="table-row">
                    <p>@lang('site.fields.time')</p>
                    <p>{{$reservation->period}}</p>
                </div>
                @if($reservation->revisit()->exists())
                    <div class="table-row">
                        <p>@lang('site.fields.revisit_date')</p>
                        <p>{{$reservation->revisit->date->translatedFormat("D d M Y")}}</p>
                    </div>
                    <div class="table-row">
                        <p>@lang('site.fields.revisit_time')</p>
                        <p>{{$reservation->revisit->period}}</p>
                    </div>
                @endif
                @if($reservation->schedule()->exists() && $reservation->schedule->status == \App\DefaultPanel\Enum\ScheduleStatusEnum::ACCEPTED->value)
                    <div class="table-row">
                        <p>@lang('site.fields.causer_id')</p>
                        <p>{{__("site.enum.{$reservation->schedule->causerLabel()}")}}</p>
                    </div>
                    <div class="table-row">
                        <p>@lang('site.heading.the_new_date_set_for_the_session')</p>
                        <p>{{\Carbon\Carbon::parse($reservation->schedule?->date)->translatedFormat("D d M Y")}}</p>
                    </div>
                    <div class="table-row">
                        <p>@lang('site.heading.the_new_time_set_for_the_session')</p>
                        <p>{{$reservation->schedule->period}}</p>
                    </div>
                @endif
            </div>
            @if($reservation->sharedAnalysis->count())
                <h3 class="reservation-title">
                    @lang('site.heading.shared_analysis')
                </h3>
            @endif
            <div class="tests-list">
                @foreach($reservation->sharedAnalysis as $service)
                    <label class="test-select" wire:click="shareAnalysis({{$service->id}})">
                        <input type="checkbox"/>
                        <div class="test-img " wire:ignore>
                            <img src="{{$service->reservation->reservable->getFirstMediaUrl()}}"
                                 alt="{{$service->reservation->reservable->title}}"/>
                        </div>
                        <div class="test-info">

                            <h5 class="test-title">{{$service->model['name'][app()->getLocale()]}}</h5>
                            <div class="test-features">
                                <span>@lang('site.fields.id')  : {{$service->reservation->id}} </span>
                                <span>@lang('site.fields.created_at') :{{$service->reservation->date->format("Y-m-d")}}  {{$service->reservation->slot}}</span>
                            </div>
                        </div>

                    </label>
                @endforeach

            </div>
            @if($reservation->schedule()->exists() && $reservation->schedule->status == \App\DefaultPanel\Enum\ScheduleStatusEnum::PENDING->value)
                <div class="reschedule-table-head">
                    <h3 class="reservation-title">@lang('site.heading.schedule_reservation')</h3>
                    @if($reservation->schedule->causerLabel() !== 'patient')
                        <div class="reschedule-tool">
                            <button class="reschedule-btn"
                                    wire:click="acceptScheduleReservationDate">@lang('site.buttons.accept')</button>
                            <button class="reschedule-btn"
                                    wire:click="rejectScheduleReservationDate">@lang('site.buttons.reject')</button>
                        </div>
                    @endif
                </div>

                <div class="reservation-table">
                    <div class="table-row">
                        <p>@lang('site.fields.causer_id')</p>
                        <p>{{__("site.enum.{$reservation->schedule->causerLabel()}")}}</p>
                    </div>
                    <div class="table-row">
                        <p>@lang('site.heading.the_new_date_set_for_the_session')</p>
                        <p>{{\Carbon\Carbon::parse($reservation->schedule?->date)->translatedFormat("D d M Y")}}</p>
                    </div>
                    <div class="table-row">
                        <p>@lang('site.heading.the_new_time_set_for_the_session')</p>
                        <p>{{$reservation->schedule->period}}</p>
                    </div>
                </div>
            @endif
            @if($reservation->isDoctorReservation() && site()->user()->medicalTests()->count())
                <h3 class="reservation-title">@lang('site.heading.tests_booked_on_the_platform')</h3>
                <div class="tests-list">
                    @foreach(site()->user()->medicalTests()->with('itemsLine')->get()->pluck('itemsLine')->unique("model->id")->flatten() as $service)

                        <label class="test-select" wire:click="shareAnalysis({{$service->id}})">
                            <input type="checkbox"
                                   :checked="$wire.sharedAnalysisIds.includes({{$service->id}})?'checked':'' "/>
                            <div class="test-img loading-img lazy-img-parent" wire:ignore>
                                <img data-src="{{$service->reservation->reservable?->getFirstMediaUrl()}}"
                                     class="lazy-img"
                                     alt="{{$service->reservation?->reservable?->title}}"/>
                            </div>
                            <div class="test-info">

                                <h5 class="test-title">{{$service->model['name'][app()->getLocale()]}}</h5>
                                <div class="test-features">
                                    <span>@lang('site.fields.id')  : {{$service->reservation->id}} </span>
                                    <span>@lang('site.fields.created_at') :{{$service->reservation->date->format("Y-m-d")}}  {{$service->reservation->slot}}</span>
                                </div>
                            </div>
                            <button type="button" class="test-mark">
                                <span> @lang('site.heading.share_with_doctor') </span>
                                <span class="selected">@lang('site.heading.unshared_with_doctor')</span>
                            </button>
                        </label>
                    @endforeach

                </div>
            @endif
            @if(!$reservation->isDoctorReservation())
                <h3 class="reservation-title">@lang('site.heading.lab_tests_required')</h3>
                <div class="tests-list">
                    @foreach($reservation->itemsline as $item)
                        @php($service=\App\UsersModule\Models\Lab\Service::find($item->model['id']))

                        @continue(!$service)
                        <div class="selected-test">
                            <div class="test-information">

                                <h3 class="test-title">{{$service->name[app()->getLocale()]}}</h3>
                                <span class="test-condition">{{$service->description[app()->getLocale()]}}</span>
                            </div>
                            <div class="test-tools">
                                <span class="test-price">{{\Cknow\Money\Money::parse($item->price)}} </span>
                                @if($item->getFirstMediaUrl())
                                    <a href="{{$item->getFirstMediaUrl()}}" download class="test-download"
                                       target="_blank">
                                        @lang('site.buttons.download_results')
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach


                </div>
            @endif
            @if($reservation->rate()->exists())
                <h3 class="reservation-title">@lang('site.heading.service_rate')</h3>
                <div class="reservation-reason">
                    <span class="reason-date">{{$reservation->rate->created_at->translatedFormat("D d M Y")}}</span>
                    <div class="reservation-rate">
                        @foreach(range(1,5) as $star)
                            <i class="fa-solid fa-star @if($reservation->rate->rate >= $star) active  @endif "></i>
                        @endforeach
                    </div>
                    <p class="reason-text">
                        {{$reservation->rate->comment}}
                    </p>
                </div>
            @endif
            @if($reservation->cancellation()->exists())
                <h3 class="reservation-title">@lang('site.heading.cancel_reason')</h3>
                <div class="reservation-reason">
                    <span
                            class="reason-date">{{$reservation->cancellation->created_at->translatedFormat("D d M Y")}}</span>
                    <p class="reason-text">
                        {{$reservation->cancellation?->reason?->name}}
                        <br/>
                        {{$reservation->cancellation->comment}}

                    </p>
                </div>
            @endif
            @if($reservation->report()->exists())
                <h3 class="reservation-title">@lang('site.heading.report_reason')</h3>
                <div class="reservation-reason">
                    <span class="reason-date">{{$reservation->report->created_at->translatedFormat("D d M Y")}}</span>

                    <p class="reason-text">
                        {{$reservation->report->reason?->name}}
                        <br>
                        {{$reservation->report->comment}}

                    </p>
                </div>
            @endif
            <h3 class="reservation-title">@lang('site.heading.payment_data')</h3>
            <div class="reservation-table">
                <div class="table-row">
                    <p>@lang('site.heading.reserve_price')</p>
                    <p>{{$reservation->price}}</p>
                </div>
                <div class="table-row">
                    <p>@lang('site.heading.payment_method')</p>
                    <p>
                        <span class="method">
                          ماستر كارت
                          <img src="images/payments/4.png"/>
                        </span>
                    </p>
                </div>
                <div class="table-row">

                    <p>@lang('site.heading.paid_at')</p>
                    @if(isset($reservation->transaction->meta_data['paid_at']))
                        <p>{{\Carbon\Carbon::parse($reservation->transaction->meta_data['paid_at'])->timezone('africa/cairo')->translatedFormat("D d M Y")}}</p>
                    @endif
                </div>
                <div class="table-row">
                    <p>@lang('site.heading.invoice')</p>
                    <p>
                        <a href="{{route('reservations.invoice',$reservation)}}" target="_blank"
                           class="invoice">@lang('site.fields.download_invoice')</a>
                    </p>
                </div>
            </div>
            <div class="reservation-footer">
                @if($reservation->canCancel())

                    <button
                            class="footer-btn"
                            wire:click="$dispatch('openModal', { component: 'cancel-reservation-pop-up',arguments:{reservation:{{$reservation->id}}} })"

                    >
                        <i class="fa-regular fa-xmark"></i>
                        @lang('site.buttons.cancel_reservation')

                    </button>
                @endif
                @if($reservation->canReport())
                    <button
                            class="footer-btn"
                            wire:click="$dispatch('openModal', { component: 'report-reservation-pop-up',arguments:{reservation:{{$reservation->id}}} })"

                    >
                        <i class="fa-regular fa-flag"></i>
                        @lang('site.buttons.report_problem')

                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@push('js')
    @viteReactRefresh
    @vite(['resources/js/index.jsx'])
    <script>

        window.reservation_id = @js($reservation->id);
        window.token = @js($reservation->patient->createToken("site-token")->plainTextToken);
        Livewire.hook('morph.added', ({el, component}) => {
            $("img.svg").each(function () {
                var $img = $(this);
                var imgID = $img.attr("id");
                var imgClass = $img.attr("class");
                var imgURL = $img.attr("src");

                $.get(
                    imgURL,
                    function (data) {
                        // Get the SVG tag, ignore the rest
                        var $svg = $(data).find("svg");

                        // Add replaced image's ID to the new SVG
                        if (typeof imgID !== "undefined") {
                            $svg = $svg.attr("id", imgID);
                        }
                        // Add replaced image's classes to the new SVG
                        if (typeof imgClass !== "undefined") {
                            $svg = $svg.attr("class", imgClass + " replaced-svg");
                        }

                        // Remove any invalid XML tags as per http://validator.w3.org
                        $svg = $svg.removeAttr("xmlns:a");

                        // Check if the viewport is set, else we gonna set it if we can.
                        if (
                            !$svg.attr("viewBox") &&
                            $svg.attr("height") &&
                            $svg.attr("width")
                        ) {
                            $svg.attr(
                                "viewBox",
                                "0 0 " + $svg.attr("height") + " " + $svg.attr("width")
                            );
                        }

                        // Replace image with new SVG
                        $img.replaceWith($svg);
                    },
                    "xml"
                );
            });
        })
    </script>
@endpush
