<footer class="footer-home">
    <div class="container">
        <div class="ready">
            <h5 class="ready-tit">
                {{data_get($landing_settings,"content.footer.$locale.title")}}
            </h5>
            <p class="ready-para">
                {{data_get($landing_settings,"content.footer.$locale.description")}}

            </p>
            <div class="btns-url">
                <a href="{{data_get($settings,'applications_links.client.google_play_link')}}">
                    <div class="button">
                        <img src="/assets/img/play.png" alt="Google Play">
                    </div>
                </a>
                <a href="{{data_get($settings,'applications_links.client.apple_store_link')}}">
                    <div class="button">
                        <img src="/assets/img/store.png" alt="App Store">
                    </div>
                </a>
            </div>
        </div>
        <div class="social-media">
            @foreach($social_links as $link)

                <a href="{{data_get($link,'link')}}">
                    <i class="fa-brands fa-{{Str::replace(" ","",data_get($link,'icon'))}}"></i>
                </a>
            @endforeach

        </div>
        <div class="copyWrite">
            <p>@lang('site.heading.copyright')</p>
            {{--            <p>--}}
            {{--                تم بواسطة--}}
            {{--                <a href="https://targetlines.com">--}}
            {{--                    <img src="assets/img/targetlines-logo.png" alt="Targetlines">--}}
            {{--                </a>--}}
            {{--            </p>--}}
        </div>
        <div class="foot-menu">
            <ul class="list-styled">
                @foreach($pages as $page)
                    @continue(!$page)
                    <li><a href="{{route('site.page',$page->slug)}}">{{$page->title}}</a></li>
                @endforeach
{{--                @foreach(\App\ContentModule\Models\Page::whereNotIn("id",collect($pages)->pluck('id')->toArray()) as $page)--}}
{{--                    <li><a href="{{route('site.page',$page->slug)}}">{{$page->title}}</a></li>--}}
{{--                @endforeach--}}
            </ul>
        </div>
        <a id="to_top" href="#top">
            <i class="fas fa-arrow-up"></i>
        </a>

    </div>
</footer>
