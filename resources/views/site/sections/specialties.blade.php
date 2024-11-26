<section class="specialties-section">
    <div class="container">
        <div class="section-head">
            <h2 class="section-title">@lang('site.heading.find_your_doctor_by_specialty')</h2>
            <a href="{{route('specialties.index')}}" class="section-btn">
                @lang('site.heading.all_specialties')
            </a>
        </div>
        <div class="specialties-slider custom-slider">
            <div class="swiper">
                <div class="swiper-wrapper">
                    @foreach($specialties as $specialty)
                        <div class="swiper-slide">
                            <div class="specialty-item">
                                <a
                                        href="{{route('doctors.index',['filters[specialty_id]'=>$specialty->id])}}"
                                        class="specialty-img loading-img lazy-img-parent"
                                >
                                    <img data-src="{{$specialty->getFirstMediaUrl()}}" alt="{{$specialty->name}}"
                                         title="{{$specialty->name}}" class="lazy-img"/>
                                </a>
                                <h3 class="specialty-title">
                                    <a   href="{{route('doctors.index',[urldecode('filters[specialty_id]')=>$specialty->id])}}">{{$specialty->name}} </a>
                                </h3>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
            <div class="slider-pagination"></div>
        </div>
        <a href="specialtiesList.html" class="section-btn mobile">
            عرض جميع التخصصات
        </a>
    </div>
</section>
