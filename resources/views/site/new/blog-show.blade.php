@extends('site.layouts.app')

@push('meta')
@php
    $locale = app()->getLocale();
    $ogImage = $post->getFirstMediaUrl('default');
    $ogImage = $ogImage ? (\Illuminate\Support\Str::startsWith($ogImage, 'http') ? $ogImage : asset($ogImage)) : null;
    $ogUrl = url()->current();
    $ogTitle = $post->getTranslation('title', $locale);
    $ogDescription = $metaDescription ?? \Illuminate\Support\Str::limit(strip_tags($post->getTranslation('description', $locale)), 160);
@endphp
<meta property="og:type" content="article">
<meta property="og:url" content="{{ $ogUrl }}">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
@if($ogImage)
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $ogUrl }}">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDescription }}">
@if($ogImage)
<meta name="twitter:image" content="{{ $ogImage }}">
@endif
@endpush

@section('content')
@php $locale = app()->getLocale(); $assetBase = asset('assets/site'); @endphp

 <!-- Start Breadcrumb -->
 <div class="container">
    <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('site.blog') }}">{{ __('site.heading.blogs') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $post->getTranslation('title', $locale) }}</li>
        </ol>
    </nav>
</div>
<!-- End Breadcrumb -->

<div class="common-page-wrapper pb-64">
        <div class="container">

            <div class="blog-post-content page-content">

                <h1>{{ $post->getTranslation('title', $locale) }}</h1>

                <img src="{{ $post->getFirstMediaUrl('default') }}" alt="{{ $post->getTranslation('title', $locale) }}" class="img-fluid post-image">

                <div class="content">{!! $post->getTranslation('description', $locale) !!}</div>
            </div>

            <!-- Start Share Button -->
            @php
                $shareUrl = url()->current();
                $shareTitle = $post->getTranslation('title', $locale);
                $shareText = $shareTitle . ' - ' . $shareUrl;
                $encodedUrl = rawurlencode($shareUrl);
                $encodedTitle = rawurlencode($shareTitle);
                $encodedText = rawurlencode($shareText);
            @endphp
            <div class="share-btn">
                <div class="back">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://twitter.com/intent/tweet?url={{ $encodedUrl }}&text={{ $encodedTitle }}" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                    <a href="https://wa.me/?text={{ $encodedText }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <div class="front">
                    {{ __('site.heading.share_article') }}
                </div>
            </div>
            <!-- End Share Button -->
        </div>
    </div>

    @if($relatedPosts->isNotEmpty())
    <div class="related-posts mb-5">
        <div class="container">
            <hr>
            <div class="text-center">
                <div class="section-title">{{ __('site.heading.you_may_also_like') }}</div>
            </div>
            <div class="row">
                @foreach($relatedPosts as $relatedPost)
                <div class="col-lg-4 mb-4">
                    <div class="single-blog-card">
                        <div class="blog-img">
                            <div class="post-date">
                                <span>{{ $relatedPost->publish_date?->locale($locale)->translatedFormat('M j, Y') }}</span>
                                <i class="fa-light fa-calendar"></i>
                            </div>
                            <a href="{{ route('site.blog.show', $relatedPost->getTranslation('slug', $locale)) }}">
                                @if($relatedPost->getFirstMediaUrl('default'))
                                    <img src="{{ $relatedPost->getFirstMediaUrl('default') }}" alt="{{ $relatedPost->getTranslation('title', $locale) }}" class="img-fluid d-block">
                                @else
                                    <img src="{{ $assetBase }}/images/product-demo_optmized.webp" alt="{{ $relatedPost->getTranslation('title', $locale) }}" class="img-fluid d-block">
                                @endif
                            </a>
                        </div>
                        <div class="blog-content">
                            <h3 class="blog-title"><a href="{{ route('site.blog.show', $relatedPost->getTranslation('slug', $locale)) }}">{{ $relatedPost->getTranslation('title', $locale) }}</a></h3>
                            <p class="blog-description">{{ Str::limit(strip_tags($relatedPost->getTranslation('description', $locale)), 120) }}</p>
                            <a href="{{ route('site.blog.show', $relatedPost->getTranslation('slug', $locale)) }}" class="btn btn-green">{{ __('site.heading.view_article_details') }}</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    @endsection