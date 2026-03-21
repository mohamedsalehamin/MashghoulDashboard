@extends('site.layouts.app')

@section('content')
@php $locale = app()->getLocale(); $assetBase = asset('assets/site'); @endphp
<div class="container">
    <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.blogs') }}</li>
        </ol>
    </nav>
</div>

<div class="common-page-wrapper pb-64">
    <div class="container">
        <div class="text-center">
            <h1 class="section-title mb-5">{{ __('site.heading.blogs') }}</h1>
        </div>
        <div class="filter-by d-flex justify-content-end mb-4">
            <form method="get" class="w-auto">
                <select name="sort" class="form-select custom-select w-auto" onchange="this.form.submit()">
                    <option value="" {{ request('sort', '') === '' ? 'selected' : '' }}>{{ __('site.heading.blog_sort_label') }}</option>
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>{{ __('site.heading.blog_sort_latest') }}</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>{{ __('site.heading.blog_sort_oldest') }}</option>
                </select>
            </form>
        </div>

        <div class="row">
            @forelse($posts as $post)
                <div class="col-lg-4 mb-4">
                    <div class="single-blog-card">
                        <div class="blog-img">
                            <div class="post-date">
                                <span>{{ $post->publish_date?->locale($locale)->translatedFormat('M j, Y') }}</span>
                                <i class="fa-light fa-calendar"></i>
                            </div>
                            <a href="{{ route('site.blog.show', $post->getTranslation('slug', $locale)) }}">
                                @if($post->getFirstMediaUrl('default'))
                                    <img src="{{ $post->getFirstMediaUrl('default') }}" alt="{{ $post->getTranslation('title', $locale) }}" class="img-fluid d-block">
                                @else
                                    <img src="{{ $assetBase }}/images/product-demo_optmized.webp" alt="{{ $post->getTranslation('title', $locale) }}" class="img-fluid d-block">
                                @endif
                            </a>
                        </div>
                        <div class="blog-content">
                            <h3 class="blog-title"><a href="{{ route('site.blog.show', $post->getTranslation('slug', $locale)) }}">{{ $post->getTranslation('title', $locale) }}</a></h3>
                            <p class="blog-description">{{ Str::limit(strip_tags($post->getTranslation('description', $locale)), 120) }}</p>
                            <a href="{{ route('site.blog.show', $post->getTranslation('slug', $locale)) }}" class="btn btn-green">{{ __('site.heading.view_article_details') }}</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">{{ __('site.no_data') }}</div>
            @endforelse
        </div>

        @if($posts->hasPages())
            <nav aria-label="Page navigation" class="mt-4">
                {{ $posts->links() }}
            </nav>
        @endif
    </div>
</div>
@endsection
