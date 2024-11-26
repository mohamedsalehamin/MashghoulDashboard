<div class="account-body">
    <h2 class="account-title">@lang('site.heading.my_reservations')</h2>
    <div class="accout-reservations">
        <div class="reservations-head">
            <div class="nav account-tabs">
                <button
                    type="button"
                    @if($activeTab == 'doctor') class="active" @endif
                    wire:click="changeTab('doctor')"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-1"
                >
                    @lang('site.heading.doctors')
                </button>
                <button
                    @if($activeTab == 'lab') class="active" @endif
                    wire:click="changeTab('lab')"
                    type="button"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-2"
                >
                    @lang('site.heading.labs')
                </button>
            </div>
            <div class="reservations-filters">
                <div class="search-filter search-sort">
                    <label class="sort-label">@lang('site.heading.reservation_status')</label>
                    <select class="search-select"
                            wire:model.live="filters.status"
                    >
                        <option value="all">@lang('site.heading.show_all')</option>
                        @foreach(\App\DefaultPanel\Enum\ReservationStatus::cases() as $case)
                            <option value="{{$case->value}}">{{$case->getLabel()}}</option>

                        @endforeach


                    </select>
                </div>

            </div>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade @if($activeTab == 'doctor')active show @endif" id="tab-1">
                <div class="reservations-tab">
                    <div class="reservations-list">
                        @foreach($doctorReservations as $reservation)
                            <div class="reservation-content">
                                <a
                                    href="{{route('profile.reservations.show',$reservation)}}"
                                    class="reservation-item"
                                >
                                    <div class="item-img " >
                                        <img

                                            src="{{$reservation->reservable?->getFirstMediaUrl()}}"

                                            alt="{{$reservation->reservable?->name}}"
                                        />
                                    </div>
                                    <div class="item-info">
                                        <div class="item-head">
                                            <span>#{{$reservation->id}}</span>
                                            <span>@lang('site.fields.created_at') : {{$reservation->date->format("Y-m-d")}}   {{$reservation->period}}</span>
                                            <span>@lang('site.fields.id'): {{$reservation->reservation_number}}</span>
                                            <span>@lang('site.fields.city') :{{$reservation->reservable->clinic->city->name}} </span>
                                            <span
                                                class="status {{$reservation->status->getClass()}}">{{$reservation->status->getLabel()}}</span>
                                        </div>
                                        <h3 class="item-name">{{$reservation->reservable->name}}</h3>
                                        <div class="item-extra">
                                <span class="item-specialty">
                                  <img
                                      src="{{$reservation->reservable->specialization->getFirstMediaUrl()}}"
                                      class="img-fluid"
                                      alt="{{$reservation->reservable->specialization->name}}"
                                  />
                                    {{$reservation->reservable->specialization->name}}
                                    ,
                                    {{$reservation->reservable->specializations->pluck('name')->implode(',')}}

                                </span>
                                            <span class="item-hint"> {{$reservation->service_type->getLabel()}} </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="tab-pane fade @if($activeTab == 'lab')active show @endif" id="tab-2">
                <div class="reservations-tab">
                    <div class="reservations-list">
                        @foreach($labReservations as $reservation)
                            <div class="reservation-content">
                                <a
                                    href="{{route('profile.reservations.show',$reservation)}}"
                                    class="reservation-item"
                                >
                                    <div class="item-img ">
                                        <img
                                            src="{{$reservation->reservable->getFirstMediaUrl()}}"
                                            alt="{{$reservation->reservable->title}}"
                                        />
                                    </div>
                                    <div class="item-info">
                                        <div class="item-head">
                                            <span>#{{$reservation->id}}</span>
                                            <span>@lang('site.fields.created_at') : {{$reservation->date->format("Y-m-d")}}   {{$reservation->period}}</span>
                                            <span>@lang('site.fields.id'): {{$reservation->reservation_number}}</span>
                                            <span>@lang('site.fields.city') :{{$reservation->reservable->city->name}} </span>
                                            <span
                                                class="status {{$reservation->status->getClass()}}">{{$reservation->status->getLabel()}}</span>
                                        </div>
                                        <h3 class="item-name">{{$reservation->reservable->title}}</h3>
                                        <div class="item-extra">
                                            <span class="item-price"> {{$reservation->price}} </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@assets
<style>
    .select2-container {
        width: 200px !important;
    }
</style>
@endassets
@script
<script>
    Livewire.hook('morph.updating', ({el, component}) => {
        $(".search-select").select2('destroy');
        $('.search-select').select2({
            minimumResultsForSearch: Infinity,
        });
    })
    $(document).ready(function () {
        $('.search-select').select2({
            minimumResultsForSearch: Infinity,
        }).on('change', function (e) {
            $wire.$set('filters.status', $('.search-select').select2('val'));
        });
    });


</script>
@endscript
