@extends('site.layouts.app')

@section('content')
@php
    $assetBase = asset('assets/site');
    $locale = app()->getLocale();
@endphp
<div class="container">
    <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.categories') }}</li>
        </ol>
    </nav>
</div>

<div class="common-page-wrapper pb-64">
    <div class="container">
        <h1 class="section-title mb-4">{{ __('site.heading.categories') }}</h1>
        <div class="row">
            @forelse($categories as $cat)
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="single-category-card">
                        <a href="{{ route('site.category.show', $cat->getSlugUrl()) }}">
                            <div class="cat-img">
                                @if($cat->getFirstMediaUrl('icon'))
                                    <img src="{{ $cat->getFirstMediaUrl('icon') }}" class="img-fluid" alt="{{ $cat->getTranslation('name', $locale) }}">
                                @else
                                    <img src="{{ $assetBase }}/images/category-1.webp" class="img-fluid" alt="{{ $cat->getTranslation('name', $locale) }}">
                                @endif
                            </div>
                            <div class="cat-title">{{ $cat->getTranslation('name', $locale) }}</div>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">{{ __('site.no_data') }}</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
