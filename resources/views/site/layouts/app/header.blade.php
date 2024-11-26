<div class="container-fluid">
    <div class="header">
        <button class="menu-btn">
            <i class="fa-regular fa-bars"></i>
        </button>
        <a href="{{url('/')}}" class="logo">
            @if(app()->getLocale()=='ar')
            <img src="images/logo.svg" class="img-fluid" alt="@lang("site.app_name")"/>
            @else
            <img src="images/logo-en.png" class="img-fluid" alt="@lang("site.app_name")"/>
            @endif
        </a>
        <div class="overlay"></div>
        <nav class="header-nav">
            <div class="nav-head">
                <div class="nav-logo">
                    @if(app()->getLocale()=='ar')
                    <img src="images/logoColored.svg" class="img-fluid" title="@lang("site.app_name")"
                         alt="@lang("site.app_name")"/>
                    @else
                        <img src="images/logoColored-en.png" class="img-fluid" title="@lang("site.app_name")"
                             alt="@lang("site.app_name")"/>
                    @endif
                </div>
                <button class="menu-close">
                    <i class="fa-regular fa-xmark"></i>
                </button>
            </div>
            <ul class="header-list">
                <li>
                    <a href="{{url('/')}}"> @lang('site.heading.home') </a>
                </li>
                @if($pages['about_us'])
                    <li>
                        <a href="{{route('pages.show',$pages['about_us']->id)}}">@lang('site.heading.about_us')</a>
                    </li>
                @endif

                <li>
                    <a href="{{route('specialties.index')}}"> @lang('site.heading.specialties') </a>
                </li>
{{--                <li>--}}
{{--                    <a href="{{route('labs.index')}}">@lang('site.heading.labs') </a>--}}
{{--                </li>--}}
                <li>
                    <a href="{{route('articles.index')}}"> @lang('site.heading.articles') </a>
                </li>
                <li>
                    <a href="{{route('contact')}}">@lang('site.heading.contact_us') </a>
                </li>
                <li class="nav-lang">
                    <div class="lang-content">
                        <button type="button" class="lang-head header-link">
                            <i class="fa-regular fa-globe"></i>

                            <span class="text"> {{app()->getLocale()=='ar'?'العربية':'English'}} </span>
                        </button>
                        <div class="lang-list">
                            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)

                                <a rel="alternate" hreflang="{{ $localeCode }}"
                                   href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                    {{ $properties['native'] }}
                                </a>

                            @endforeach

                        </div>
                    </div>
                </li>
            </ul>
        </nav>
        <div class="header-tools">
            <div class="lang-content">
                <button type="button" class="lang-head header-link">
                    <i class="fa-regular fa-globe"></i>
                    <span class="text"> {{app()->getLocale()=='ar'?'العربية':'English'}} </span>
                </button>
                <div class="lang-list">
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)

                        <a rel="alternate" hreflang="{{ $localeCode }}"
                           href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                            {{ $properties['native'] }}
                        </a>

                    @endforeach
                </div>
            </div>
          @livewire('change-location-button')
            @auth("site")
                <a
                    href="{{route('profile.notifications')}}"
                    class="notifications-link header-link active"
                >
                    <i class="fa-regular fa-bell"></i>
                    <span class="text">@lang('site.heading.notifications')</span>
                </a>
                <a href="{{route('profile.edit')}}" class="header-btn header-link">
                    <i class="fa-regular fa-user"></i>
                    <span class="text"> @lang('site.heading.my_account') </span>
                </a>
            @else
                <a href="{{route('auth.login')}}" class="header-btn header-link">
                    <i class="fa-regular fa-user"></i>
                    <span class="text"> @lang('site.buttons.login') </span>
                </a>
            @endauth

        </div>
    </div>
</div>

