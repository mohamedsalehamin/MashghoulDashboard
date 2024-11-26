<div class="archive-content">
    <div class="row">
        {{--        @livewire('labs-list-filter')--}}
        <div class="col-lg-12">
            @if($labs->count())
            @livewire('labs-list-search-filter')

            <div class="results-list">
                @foreach($labs as $index=>$lab)
                    <livewire:lab-card :lab="$lab" wire:key="{{$index}}"/>
                @endforeach
            </div>
            <p class="mt-7">

            </p>
            <div x-intersect.half="$wire.more()">

            </div>
            @else
                <div class="success-content empty-content">
                    <div class="success-icon">
                        <i class="fa-light fa-circle-xmark"></i>
                    </div>
                    <h2 class="success-title">@lang("site.heading.no_labs_in_city")</h2>
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




@push("js")
    <script>

        Livewire.hook('morph.updated', ({el, component}) => {

            lazyLoad()
        })

    </script>
@endpush

