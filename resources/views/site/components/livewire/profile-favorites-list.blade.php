<div class="account-body">
    <h2 class="account-title">@lang('site.heading.my_favorite')</h2>
    <div class="accout-favorites">
        <div class="nav account-tabs">
            <button
                    type="button"
                    class="active"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-1"
            >
                @lang('site.heading.doctors')
            </button>
            <button
                    type="button"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-2"
            >
                @lang('site.heading.labs')
            </button>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-1">
                <div class="favorites-tab">
                    <div class="results-list">
                        @foreach($doctors as $doctor)
                            <livewire:doctor-card :doctor="$doctor" wire:key="{{$doctor->id}}"/>
                        @endforeach

                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-2">
                <div class="favorites-tab">
                    <div class="results-list">
                        @foreach($labs as $lab)
                            <livewire:lab-card :lab="$lab" wire:key="{{$lab->id}}"/>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
