
<section class="labs-section">
    <div class="container">
        <div class="section-head">
            <h2 class="section-title">@lang('site.heading.most_rated_labs')</h2>
            <a href="{{route('labs.index')}}" class="section-btn"> @lang("site.heading.all_labs") </a>
        </div>
        <div class="labs-slider custom-slider">
            <div class="swiper">
                <div class="swiper-wrapper">
                    @foreach($labs as $lab)
                        @livewire('lab-card',['lab'=>$lab])
                    @endforeach
                </div>
            </div>
            <div class="slider-pagination"></div>
        </div>
        <a href="{{route('labs.index')}}" class="section-btn mobile">
            @lang('site.heading.all_labs')
        </a>
    </div>
</section>
