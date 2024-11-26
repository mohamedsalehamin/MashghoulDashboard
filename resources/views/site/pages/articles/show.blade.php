@extends('site.layouts.app')
@section("title",__('site.heading.articles') . "-".$article->title)
@php($breadcrumb= site()->breadcrumbs()
->add(__('site.heading.articles'),route('articles.index'))
->add($article->title,route('articles.show',$article->id)))

@section('content')
    <section class="page-content post-page">
        <div class="container">
            <div class="post-content">
                <div class="post-info">
                    <div class="post-head">
                        <h1 class="page-title">{{$article->title}}</h1>
{{--                        <button class="post-share">--}}
{{--                            <i class="fa-regular fa-share-nodes"></i>--}}
{{--                        </button>--}}
                        <div class="post-features">
                            <a href="{{route('articles.index')}}" class="post-category"> {{$article->category->name}} </a>
                            <span class="post-date">{{$article->created_at->format("Y/m/d - H:i")}} </span>
                        </div>
                    </div>
                    <div class="post-body">
                        <a
                            href="{{$article->getFirstMediaUrl()}}"
                            data-fancybox="post"
                            class="main-img loading-img lazy-img-parent"
                        >
                            <img data-src="{{$article->getFirstMediaUrl()}}" class="img-fluid lazy-img"
                                 alt="{{$article->title}}" title="{{$article->title}}"/>
                        </a>

                        {!! $article->description !!}

                    </div>
                </div>
                <div class="side-categories">
                    <h3 class="categories-title">@lang('site.heading.categories')</h3>
                    <ul class="categories-list">
                        @foreach($categories as $category)
                            <li>
                                <a href="{{route('articles.index',['filters[category]'=>$category->id])}}">
                                    {{$category->name}}
                                    <span> {{$category->posts()->count()}} </span>
                                </a>
                            </li>
                        @endforeach


                    </ul>
                </div>
            </div>
            @if($otherArticles->count())
                <div class="related-blog">
                    <h2 class="section-title related-title">@lang('site.heading.similar_articles')</h2>
                    <div class="blog-slider custom-slider">
                        <div class="swiper">
                            <div class="swiper-wrapper">
                                @foreach($otherArticles as $article)
                                    <div class="swiper-slide">
                                        <div class="blog-item">
                                            <a href="{{route('articles.show',$article->id)}}"
                                               class="blog-img loading-img lazy-img-parent"
                                            >
                                                <img data-src="{{$article->getFirstMediaUrl()}}" class="lazy-img"
                                                     alt="{{$article->title}}"
                                                     title="{{$article->title}}"/>
                                            </a>
                                            <div class="blog-info">
                                                <span class="blog-date">{{$article->created_at->format("Y/m/d - H:i")}}</span>
                                                <h3 class="blog-title">
                                                    <a href="{{route('articles.show',$article->id)}}"> {{$article->title}} </a>
                                                </h3>
                                                <p class="blog-summary">
                                                    {{Str::sanitizeHtml(strip_tags($article->description),200)}}
                                                </p>
                                                <a href="{{route('articles.show',$article->id)}}"
                                                   class="blog-link"> @lang('site.buttons.show_more') </a>
                                                <a class="blog-category" > {{$article->category->name}}</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="slider-pagination"></div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
