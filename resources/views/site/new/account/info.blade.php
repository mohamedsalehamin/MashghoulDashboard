@extends('site.layouts.app')

@section('content')
<div class="container">
    <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.my_account') ?? 'حسابي' }}</li>
        </ol>
    </nav>
</div>

<div class="common-page-wrapper pb-64">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                @include('site.components.new.account-sidebar')
            </div>
            <div class="col-lg-8 offset-lg-1">
                    <div class="section-title fz32">{{ __('site.heading.account_info')}}</div>
                @livewire('site.edit-profile-form')
            </div>
        </div>
    </div>
</div>
@endsection
