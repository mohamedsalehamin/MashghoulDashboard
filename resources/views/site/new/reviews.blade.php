@extends('site.layouts.app')

@section('content')
@php $locale = app()->getLocale(); @endphp
<div class="container">
    <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.customer_reviews') ?? 'آراء العملاء' }}</li>
        </ol>
    </nav>
</div>

<div class="common-page-wrapper pb-64">
    <div class="container">
        <h1 class="section-title mb-5">{{ __('site.heading.customer_reviews') ?? 'آراء العملاء' }}</h1>
        <div class="row">
            @forelse($reviews as $review)
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>{{ $review->getTranslation('customer_name', $locale) }}</strong>
                                @if($review->rate ?? null)
                                    <span>
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa-solid fa-star {{ $i <= ($review->rate ?? 0) ? '' : 'text-muted' }}"></i>
                                        @endfor
                                    </span>
                                @endif
                            </div>
                            <p class="card-text">{{ $review->getTranslation('review', $locale) }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">{{ __('site.no_data') }}</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
