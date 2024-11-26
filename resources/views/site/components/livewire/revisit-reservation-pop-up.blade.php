<div>
    <div class="modal fade show " style="display: block" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">@lang('site.heading.book_a_return_appointment')</h2>
                    <button type="button" class="modal-close" data-bs-dismiss="modal">
                        <i class="fa-regular fa-xmark"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-form">
                        <div class="select-day">
                            <h4 class="side-title">@lang('site.heading.determine_date') </h4>
                            <div class="days-list">
                                @foreach($periods as $period)
                                    <label class="day-select" wire:click="updateDatePeriods('{{$period}}')">
                                        <input type="radio"
                                               name="day"
                                               @if($loop->first) checked @endif
                                               wire:model="form.date"
                                               value="{{$period->format("Y-m-d")}}"
                                        />

                                        <div class="day-info">
                                            <span class="day"> {{$period->translatedFormat("l")}} </span>
                                            <span class="date"> {{$period->translatedFormat("m-d")}} </span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="select-time">
                            <h4 class="side-title">@lang('site.heading.determine_time') </h4>
                            <div class="time-list">
                                @foreach($slots as $slot )
                                    <label class="time-select">
                                        <input type="radio" name="time" wire:model.live="form.slot" value="{{$slot}}"/>
                                        <div class="time-info">
                                            <span>{{$slot}}</span>
                                        </div>
                                    </label>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a
                        wire:click.prevent="handle"
                        class="modal-btn main-btn"
                    >
                        @lang('site.buttons.send')
                    </a>
                    <button
                        type="button"
                        class="modal-btn sec-btn"
                        wire:click="$dispatch('closeModal')"
                    >
                        @lang('site.heading.cancel')
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
</div>

