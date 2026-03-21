@php
    $assetBase = asset('assets/site');
    $locale = app()->getLocale();
@endphp
<!-- Start Header -->
<header class="header">
    <div class="container">
        <div class="header-main-content">
            <button class="bars" aria-label="barsIcon">
                <span class="line line1"></span>
                <span class="line line2"></span>
                <span class="line line3"></span>
            </button>

            <a href="{{ route('site.home') }}" class="logo-img" aria-label="logo">
                <img class="img-fluid" src="{{ $locale === 'ar' ? $assetBase.'/images/logo.png' : $assetBase.'/images/logo-en.png' }}" alt="logo">
            </a>

            <nav class="navigation">
                <ul class="main-menu list-unstyled">
                    <li><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
                    <li class="menu-item-has-children">
                        <a href="{{ route('site.categories') }}">{{ __('site.heading.categories') }}</a>
                        <ul class="sub-menu list-unstyled">
                            @isset($categories)
                                @foreach($categories->take(7) as $cat)
                                    <li><a href="{{ route('site.category.show', $cat->getSlugUrl()) }}">{{ $cat->name }}</a></li>
                                @endforeach
                            @endisset
                        </ul>
                    </li>
                    <li><a href="{{ route('site.faqs') }}">{{ __('site.heading.faqs') }}</a></li>
                    <li><a href="{{ route('site.contact') }}">{{ __('site.heading.contact_us') }}</a></li>
                    @if(!auth()->guard('site')->check())
                        <li><a href="{{ route('site.join') }}">{{ __('site.heading.join_us_as_a_provider') }}</a></li>
                    @endif
                </ul>
            </nav>

            <div class="header-icons">
                <a href="#" class="header-icon search-icon" aria-label="search"><i class="fa-light fa-magnifying-glass"></i></a>
                @auth('site')
                    <a href="{{ route('site.favorites') }}" class="header-icon wishlist-icon"><i class="fa-light fa-heart"></i></a>
                    <a href="{{ route('site.account.info') }}" class="header-icon user-icon"><i class="fa-light fa-user"></i></a>
                @else
                    <a href="{{ route('site.login') }}" class="header-icon user-icon"><i class="fa-light fa-user"></i></a>
                @endauth

                @php
                    $locationSet = session()->has('location_set') && session('location_set') === true;
                @endphp
                @if(! $locationSet && session()->get('show_location_modal') === true)
                    <button
                        type="button"
                        class="header-icon location-icon"
                        data-bs-toggle="modal"
                        data-bs-target="#locationModal"
                        aria-label="{{ __('site.heading.set_location') }}"
                    >
                        <i class="fa-light fa-location-dot"></i>
                    </button>
                @else
                    <span class="header-icon location-icon" aria-hidden="true">
                        <i class="fa-light fa-location-dot"></i>
                    </span>
                @endif
                @php
                    $otherLocale = $locale === 'ar' ? 'en' : 'ar';
                    $currentUrl = request()->url();
                @endphp
                <a href="{{ LaravelLocalization::getLocalizedURL($otherLocale, null, [], true) }}" class="lang-switcher">{{ $otherLocale === 'ar' ? 'Ar' : 'En' }}</a>
            </div>
        </div>

        <div class="search-form-wrapper">
            <form class="search-form" action="{{ route('site.categories') }}" method="get" role="search">
                <input type="text" name="q" class="search-input form-control" placeholder="{{ __('site.heading.search') }}">
                <button type="submit" class="search-btn btn"><i class="fa-light fa-magnifying-glass"></i></button>
            </form>
        </div>
    </div>
</header>
<!-- End Header -->
