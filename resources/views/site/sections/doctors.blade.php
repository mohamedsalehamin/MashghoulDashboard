<section class="doctors-section">
    <div class="container">
        <div class="section-head">
            <h2 class="section-title">@lang("site.heading.most_rated_doctors")</h2>
            <a href="{{route('doctors.index')}}" class="section-btn"> @lang("site.heading.all_doctors")</a>
        </div>
        <div class="doctors-slider custom-slider">
            <div class="swiper">
                <div class="swiper-wrapper">
                    @foreach($doctors as $doctor)
                        <div class="swiper-slide">
                            @livewire('doctor-card',['doctor'=>$doctor])
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="slider-pagination"></div>
        </div>
        <a href="{{route('doctors.index')}}" class="section-btn mobile">
            @lang("site.heading.all_doctors")
        </a>
    </div>
</section>
