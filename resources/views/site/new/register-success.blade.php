@extends('site.layouts.app')

@section('content')
<!-- Start Breadcrumb -->
<div class="container">
    <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') ?? 'الرئيسية' }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.register_successfully') ?? 'تم إنشاء حسابك بنجاح' }}</li>
        </ol>
    </nav>
</div>
<!-- End Breadcrumb -->

<div class="common-page-wrapper pb-64">
    <div class="container">

        <div class="text-center">

            <div class="main-title text-blue fz32 mb-3">{{ __('site.heading.register_successfully') }}</div>
            <p class="text-center text-blue  ">{{ __('site.heading.register_successfully_text') }}</p>

            <img src="{{ asset('assets/site/images/registered-successfuly.svg') }}" class="img-fluid mx-auto mt-4" alt>
        </div>
    </div>

</div>

@push('scripts')
<script>
    setTimeout(function () {
        window.location.href = '{{ route('site.home') }}';
    }, 2000);
</script>
@endpush
@endsection
