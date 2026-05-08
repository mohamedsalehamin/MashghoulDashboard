@extends('site.layouts.app')

@section('content')
@php
    $assetBase = asset('assets/site');
    $locale = app()->getLocale();
    $showProviderResults = request()->filled('q') || request()->filled('city_id') || request()->filled('category');
@endphp
<div class="container">
    <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            @if($showProviderResults)
                <li class="breadcrumb-item"><a href="{{ route('site.categories') }}">{{ __('site.heading.categories') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.search_results') }}</li>
            @else
                <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.categories') }}</li>
            @endif
        </ol>
    </nav>
</div>

<div class="common-page-wrapper pb-64">
    <div class="container">
        @if($showProviderResults)
            <h1 class="section-title mb-4">{{ __('site.heading.search_results') }}</h1>

            <form method="get" action="{{ route('site.categories') }}" class="filter-by d-flex flex-wrap justify-content-end align-items-center gap-2 mb-4">
                @if(request()->filled('city_id'))
                    <input type="hidden" name="city_id" value="{{ request('city_id') }}">
                @endif
                @if(request()->filled('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="input-group w-auto">
                    <input type="text" name="q" class="form-control" placeholder="{{ __('site.heading.search') }}" value="{{ request('q') }}" aria-label="{{ __('site.heading.search') }}">
                    <button type="submit" class="btn btn-green" aria-label="{{ __('site.fields.search') }}"><i class="fa-light fa-magnifying-glass"></i></button>
                </div>
                <select name="sort" class="form-select custom-select w-auto" onchange="this.form.submit()" aria-label="{{ __('site.heading.category_sort_label') }}">
                    <option value="">{{ __('site.heading.category_sort_label') }}</option>
                    <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>{{ __('site.heading.category_sort_newest') }}</option>
                    <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>{{ __('site.heading.category_sort_oldest') }}</option>
                    <option value="rating_desc" {{ request('sort') == 'rating_desc' ? 'selected' : '' }}>{{ __('site.heading.category_sort_rating_high') }}</option>
                    <option value="rating_asc" {{ request('sort') == 'rating_asc' ? 'selected' : '' }}>{{ __('site.heading.category_sort_rating_low') }}</option>
                </select>
            </form>

            <div class="row">
                @forelse($providers as $provider)
                    <div class="col-lg-3 col-md-4 col-6 mb-4">
                        <x-site.provider-card :provider="$provider" :show-map-button="true" />
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-5">
                        @if(request()->filled('q'))
                            {{ __('site.no_matching_search_results') }}
                        @else
                            {{ __('site.no_data') }}
                        @endif
                    </div>
                @endforelse
            </div>
        @else
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
        @endif
    </div>
</div>
@endsection
