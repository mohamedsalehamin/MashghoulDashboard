@php
    $assetBase = asset('assets/site');
    $landingSettings = app(\App\DefaultPanel\Settings\LandingSettings::class);
    $appPages = $landingSettings->content['site_pages'] ?? [];
    
    $pages = collect($appPages)->mapWithKeys(fn($pageId, $name) => [$name => \App\ContentModule\Models\Page::find($pageId)])->filter();
    $locale = app()->getLocale();
    $aboutSlug = $pages['about_us']->slug ?? 'about';
    $termsSlug = $pages['terms_and_conditions']->slug ?? 'terms';
    $privacySlug = $pages['privacy_policy']->slug ?? 'privacy';
@endphp
<!-- Start Footer -->
<footer style="background: linear-gradient(rgba(0, 47, 135, 0.9), rgba(0, 47, 135, 0.9)), url({{ $assetBase }}/images/footer-bg.webp) no-repeat center center;">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="footer-about">
                    <img src="{{ $assetBase }}/images/logo_optmized.webp" class="img-fluid" alt="Mashghol">
                    <p>{{ __('site.heading.contact_us_text') }}</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="footer-menu">
                    <h4 class="footer-title">{{ __('site.heading.quick_links') }}</h4>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
                        <li><a href="{{ route('site.categories') }}">{{ __('site.heading.categories') }}</a></li>
                        <li><a href="{{ route('site.blog') }}">{{ __('site.heading.articles') }}</a></li>
                        <li><a href="{{ route('site.join') }}">{{ __('site.heading.join_us_as_a_provider') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="footer-menu">
                    <h4 class="footer-title">{{ __('site.fields.important_links') }}</h4>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('site.page', $aboutSlug) }}">{{ __('site.heading.about_us') }}</a></li>
                        <li><a href="{{ route('site.page', $termsSlug) }}">{{ __('site.heading.terms_and_conditions') }}</a></li>
                        <li><a href="{{ route('site.page', $privacySlug) }}">{{ __('site.heading.privacy_policy') }}</a></li>
                        <li><a href="{{ route('site.faqs') }}">{{ __('site.heading.faqs') }}</a></li>
                        <li><a href="{{ route('site.contact') }}">{{ __('site.heading.contact_us') }}</a></li>
                        <li><a href="{{ route('site.join') }}">{{ __('site.heading.join_us_as_a_provider') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="footer-info footer-menu">
                    <h4 class="footer-title">{{ __('site.heading.contact_information') }}</h4>
                    <ul class="list-unstyled">
                        @if(isset($settings) && ($settings->app_phone ?? null))
                            <li><a href="tel:{{ $settings->app_phone }}">{{ $settings->app_phone }}</a></li>
                        @endif
                        @if(isset($settings) && ($settings->app_email ?? null))
                            <li><a href="mailto:{{ $settings->app_email }}">{{ $settings->app_email }}</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="copyrights">
        <div class="container">
            <div class="text-center">{{ __('site.heading.copyright') }} <span>Mashghol</span> {{ date('Y') }}</div>
        </div>
    </div>
</footer>
<!-- End Footer -->
