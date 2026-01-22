<div class="container">
    <div class="d-flex align-items-center justify-content-between">
        <div class="menu-logo text-center">
            <a href="{{route('site.home')}}">
                <img
                    src="{{asset('storage/'.$landing_settings->logos[$locale]??'')}}"
                    alt="@lang("site.app_name")"
                    class="img-fluid logo"
                />
            </a>
        </div>
        <nav>
            <ul class="nav-list d-flex align-items-center">
                <li class="linkMenu">
                    <a href="{{url(App::getLocale() . '/')}}/#about"> @lang("site.heading.about_us") </a>
                </li>
                <li class="linkMenu">
                    <a href="{{url(App::getLocale() . '/')}}/#features">@lang("site.heading.mashghoul_features")</a>
                </li>
                <li class="linkMenu">
                    <a href="{{url(App::getLocale() . '/')}}/#faq">@lang("site.heading.faqs")</a>
                </li>
                <li class="linkMenu">
                    <a href="{{url(App::getLocale() . '/')}}/#contact_Us">@lang("site.heading.contact_us")</a>
                </li>
            </ul>
        </nav>
        @livewire('register-button')
    </div>
</div>
