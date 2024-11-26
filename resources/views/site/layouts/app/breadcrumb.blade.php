@isset($breadcrumb)

    <section class="breadcrumb-section">
        <div class="container">
            <ul class="breadcrumb-content">
                <li>
                    <a href="{{route('home')}}">@lang('site.heading.home')</a>
                </li>
                @foreach($breadcrumb as $item)
                    <li class="item @if($breadcrumb->isLast()) active item-23 @endif">
                        @if($loop->last)
                            {{$item['title']}}
                        @else
                            @isset($item['url'])
                                <a href="{{$item['url']}}">{{$item['title']}}</a>
                            @else
                                {{$item['title']}}
                            @endisset
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endisset


