@extends('site.layouts.app')

@section('content')
<div class="container">
    <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.error') }}</li>
        </ol>
    </nav>
</div>

<div class="common-page-wrapper pb-64">
    <div class="container">
        <div class="book-success-wrapper">
            <div class="book-success-card">
                <div class="success-icon-wrapper text-danger">
                    <i class="fa-solid fa-circle-xmark success-check-icon"></i>
                </div>
                <h1 class="success-title">{{ __('site.heading.error') }}</h1>
                <p class="success-subtitle">
                    @if(!empty($showRetryCheckout) && !empty($tabbyMessage))
                        {{ $tabbyMessage }}
                    @else
                        {{ __('site.heading.something_went_wrong') }}
                    @endif
                </p>
                <div class="success-actions">
                    @if($retryUrl)
                        <a href="{{ $retryUrl }}" class="btn btn-green">{{ __('site.heading.try_again') }}</a>
                    @endif
                    <a href="{{ route('site.home') }}" class="btn btn-blue">{{ __('site.heading.home') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
