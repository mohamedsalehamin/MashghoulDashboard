@extends('site.layouts.app')

@section('content')
@php $locale = app()->getLocale(); @endphp

<!-- Start Breadcrumb -->
<div class="container">
    <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $page->getTranslation('title', $locale) }}</li>
        </ol>
    </nav>
</div>
<!-- End Breadcrumb -->

<div class="common-page-wrapper pb-64">
    <div class="container">
        <div class="text-center">
            <h1 class="section-title mb-5">{{ $page->getTranslation('title', $locale) }}</h1>
        </div>

        <div class="page-content">
            {!! $page->getTranslation('description', $locale) !!}
        </div>
    </div>
</div>

@endsection
