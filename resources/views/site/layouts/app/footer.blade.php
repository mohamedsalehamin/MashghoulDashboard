<footer>
    <div class="pattern">
        <img src="images/pattern/footer.svg"/>
    </div>
    <div class="container">
        <div class="footer">
            <div class="row">
                <div class="col-lg-3 col-12">
                    <div class="footer-information">
                        <div class="footer-logo">
                            <img src="images/logoFooter.svg" class="img-fluid"/>
                        </div>
                        <div class="socials">

                            @foreach($social_links as $social_link)
                                <a href="{{$social_link['link']}}" class="social">
                                    <i class="fa-brands fa-{{$social_link['icon']}}"></i>
                                </a>
                            @endforeach
                        </div>
                        <div class="payments">
                            <img src="images/payment.png" class="img-fluid"/>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5 col-md-6">
                    <div class="footer-nav">
                        <h4 class="footer-title acc-title">@lang('site.fields.important_links')</h4>
                        <div class="footer-lists">
                            <ul class="footer-list">
                                @if($pages['about_us'])
                                    <li>
                                        <a href="{{route('pages.show',$pages['about_us']->id)}}"
                                           class="nav-foot-link">{{$pages['about_us']->title}}</a>
                                    </li>
                                @endif
                                {{--                                <li>--}}
                                {{--                                    <a--}}
                                {{--                                        href="{{route('pages.show',$pages['faqs']->id)}}"--}}
                                {{--                                        class="nav-foot-link">@lang("site.heading.faqs")</a>--}}
                                {{--                                </li>--}}
                                @if($pages['terms_and_conditions'])
                                    <li>
                                        <a
                                            href="{{route('pages.show',$pages['terms_and_conditions']->id)}}"
                                            class="nav-foot-link">{{$pages['terms_and_conditions']->title}}</a>
                                    </li>
                                @endif
                                @if($pages['privacy_policy'])
                                    <li>
                                        <a
                                            href="{{route('pages.show',$pages['privacy_policy']->id)}}"
                                            class="nav-foot-link">{{$pages['privacy_policy']->title}}</a>
                                    </li>
                                @endif

                                @if($pages['return_policy'])
                                    <li>
                                        <a
                                            href="{{route('pages.show',$pages['return_policy']->id)}}"
                                            class="nav-foot-link">{{$pages['return_policy']->title}}</a>
                                    </li>
                                @endif
                                <li>
                                    <a
                                        href="{{route('faqs')}}"
                                        class="nav-foot-link">@lang('site.heading.faqs')</a>
                                </li>
                                <li>
                                    <a href="{{route('contact')}}"
                                       class="nav-foot-link">@lang("site.heading.contact_us")</a>
                                </li>
                            </ul>
                            <ul class="footer-list">

                                <li>
                                    <a href="{{route('auth.register.doctors')}}">
                                        @lang('site.heading.join_us_as_a_doctor')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{route('auth.register.labs')}}">
                                        @lang('site.heading.join_us_as_an_analysis_laboratory')
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 offset-xl-1 col-md-6">
                    <div class="footer-contacts">
                        <h4 class="footer-title acc-title">@lang('site.heading.contact_information')</h4>
                        <ul class="contacts-list">
                            <li>
                                <a href="tel:{{$settings->app_phone}}" class="contact-box">
                                    <i class="fa-regular fa-phone"></i>
                                    <span style="direction: ltr"> {{$settings->app_phone}} </span>
                                </a>

                            </li>
                            <li>
                                <a href="mailto:{{$settings->app_email}}" class="contact-box">
                                    <i class="fa-regular fa-envelope"></i>
                                    <span style="direction: ltr"> {{$settings->app_email}} </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="footer-download">
                        <h4 class="footer-title">@lang('site.fields.download_apps_now')</h4>
                        <div class="download-btns">
                            @if(isset($settings->applications_links['google_play_link']))
                                <a href="{{$settings->applications_links['google_play_link']}}" target="_blank"
                                   class="download-btn">
                                    <img src="images/download/1.svg" alt="Google Play"/>
                                </a>
                            @endif
                            @if(isset($settings->applications_links['apple_store_link']))
                                <a href="{{$settings->applications_links['apple_store_link']}}" target="_blank"
                                   class="download-btn">
                                    <img src="images/download/2.svg" alt="Apple store"/>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
{{--        <div class="copyrights">--}}
{{--            <p>@lang('site.app_name') {{date("Y")}} ©</p>--}}
{{--            <p>--}}
{{--                @lang('site.heading.powered_by')--}}
{{--                <a href="https://targetlines.com/" target="_blank">--}}
{{--                    <img--}}
{{--                        src="images/targetlines.png"--}}
{{--                        alt="targetlines"--}}
{{--                        class="img-fluid"--}}
{{--                    />--}}
{{--                </a>--}}
{{--            </p>--}}
{{--        </div>--}}
    </div>
</footer>
<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/swiper-bundle.min.js"></script>
<script src="js/select2.min.js"></script>
<script src="js/fancybox.umd.js"></script>
<script src="js/intlTelInput.min.js"></script>
<script src="js/flatpicker.min.js"></script>
<script src="js/ar.js"></script>
<script src="js/lazyload.js"></script>
<script src="js/main.js"></script>
<script src="js/svg.js"></script>

@livewireScripts
@stack('js')
<script>
    @if(!session()->has('city_id') && !in_array(request()->route()->getName(),['checkout.success','checkout.error']))
    $(function () {
        Livewire.dispatch('openModal', {component: 'change-city-pop-up'})
    })
    @endif
</script>
@livewire('wire-elements-modal')
