@extends('site.layouts.app')

@section('content')
<div class="container">
    <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.faqs') }}</li>
        </ol>
    </nav>
</div>

<div class="common-page-wrapper pb-64">
    <div class="container">
        <div class="text-center">
            <h1 class="section-title mb-5">{{ __('site.heading.faqs') }}</h1>
        </div>
        <div class="row align-items-md-center">
            <div class="col-lg-10 offset-lg-1">
                <div class="faqs-wrapper">
                    @forelse($faqs as $faq)
                        <div class="single-faq-item">
                            <div class="faq-title">{{ $faq->getTranslation('question', app()->getLocale()) }}</div>
                            <div class="faq-content">{{ $faq->getTranslation('answer', app()->getLocale()) }}</div>
                        </div>
                    @empty
                        <p class="text-muted">{{ __('site.no_data') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
