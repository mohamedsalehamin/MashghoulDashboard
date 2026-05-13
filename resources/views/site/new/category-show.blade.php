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
            <li class="breadcrumb-item"><a href="{{ route('site.categories') }}">{{ __('site.heading.categories') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->getTranslation('name', $locale) }}</li>
        </ol>
    </nav>
</div>

<div class="common-page-wrapper pb-64">
    <div class="container">
        <h1 class="section-title mb-4">{{ $category->getTranslation('name', $locale) }}</h1>

        <form method="get" action="{{ route('site.category.show', $category->getSlugUrl()) }}" class="filter-by d-flex flex-wrap justify-content-end align-items-center gap-2 mb-4">
            <div class="input-group w-auto">
                <input type="text" name="q" class="form-control" placeholder="{{ __('site.heading.search') }}" value="{{ request('q') }}" aria-label="{{ __('site.heading.search') }}">
                <button type="submit" class="btn btn-green d-flex align-items-center justify-content-center" aria-label="{{ __('site.fields.search') }}"><i class="fa-light fa-magnifying-glass"></i></button>
            </div>
            <select name="sort" class="form-select custom-select w-auto" onchange="this.form.submit()" aria-label="{{ __('site.heading.category_sort_label') }}">
                <option value="">{{ __('site.heading.category_sort_label') }}</option>
                <option value="nearest" {{ request('sort') === 'nearest' || (! filled(request('sort')) && ($sortDefaultsToNearest ?? false)) ? 'selected' : '' }}>{{ __('site.heading.category_sort_nearest') }}</option>
                <option value="farthest" {{ request('sort') == 'farthest' ? 'selected' : '' }}>{{ __('site.heading.category_sort_farthest') }}</option>
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
                        {{ __('site.no_providers_in_category') }}
                    @endif
                </div>
            @endforelse
        </div>

        @if($providers->hasPages())
            <div class="mt-4">
                {{ $providers->links('vendor.pagination.categories') }}
            </div>
        @endif
    </div>
</div>
@endsection
