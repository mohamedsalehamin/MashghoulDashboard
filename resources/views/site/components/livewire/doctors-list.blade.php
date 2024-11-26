<div class="container">
{{--    <h1 class="page-title">أسنان</h1>--}}
    @livewire('doctors-list-specialties-filter',['specialties'=>$specialties])
    <div class="archive-content">
        <div class="row">
            <div class="col-lg-3">
                @livewire('doctors-list-filter')
            </div>
            <div class="col-lg-9">
                @if($doctors->count())
                @livewire('doctors-list-search-filter')

                <div class="results-list">
                    @foreach($doctors as $doctor)
                        <livewire:doctor-card wire:key="{{$doctor->id}}" :doctor="$doctor">
                            @endforeach
                            <p class="mt-7">

                            </p>
                            <div x-intersect.half="$wire.more()">

                            </div>
                </div>
                @else
                    <div class="success-content empty-content">
                        <div class="success-icon">
                            <i class="fa-light fa-circle-xmark"></i>
                        </div>
                        <h2 class="success-title">@lang("site.heading.no_doctors_in_city")</h2>
                        <a
                            wire:click="$dispatch('openModal',{component:'change-city-pop-up'})"
                            class="success-btn main-btn"
                        >
                            @lang('site.heading.select_another_city')


                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
