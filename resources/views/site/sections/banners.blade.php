<main class="main-section">
    <div class="container-fluid">
        <div class="main-slider">
            <div class="swiper">
                <div class="swiper-wrapper">
                    @foreach($banners as $banner)
                        <div class="swiper-slide">
                            <div class="slide-content loading-img lazy-img-parent">
                                <img data-src="{{$banner->getFirstMediaUrl(app()->getLocale())}}" class="lazy-img"/>
                                <h2 class="slide-title">{{$banner->name}}</h2>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="main-pagination"></div>
        </div>
    </div>
</main>
