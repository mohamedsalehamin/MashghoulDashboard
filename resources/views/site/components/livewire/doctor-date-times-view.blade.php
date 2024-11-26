<div class="single-card side-card">
    <div class="card-head">
        <h3 class="card-title">@lang('site.heading.reserve_now')</h3>
    </div>
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

    <div class="side-totals">
        <div class="side-total">
            <span>@lang('site.fields.reservation_duration')</span>
            <strong>{{$doctor->session_duration??'60'}} @lang('site.enum.min')</strong>
        </div>
        <div class="side-total">
            <span>@lang('site.heading.reserve_price')</span>
            <strong x-text="$wire.$parent.total"></strong>
        </div>
    </div>

    @if($errors->has('form.service'))
        @error('form.service') <p class="text-danger">{{$message}}</p> @enderror
    @else
        @error('form.slot') <p class="text-danger">{{$message}}</p> @enderror
    @endif

    <a class="single-btn" wire:click.prevent="handle">
        <i class="fa-regular fa-calendar"></i>
        @lang('site.heading.reserve')
    </a>
</div>
