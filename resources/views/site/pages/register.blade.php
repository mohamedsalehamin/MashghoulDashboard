@extends('site.layouts.app')
@section("title",__('site.heading.home'))
@section('content')
    <header class="head-inside">
        @include('site.components.navbar')
    </header>
    <div class="breadcrumb-container py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-2" style="direction: rtl; text-align: right;">
                    <li class="breadcrumb-item">
                        <a href="{{route('site.home')}}" class="text-decoration-none text-primary">
                            @lang('site.heading.home')
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        @lang('site.heading.register_as_provider')
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <section class="custom-content-section">
        <div class="single_page archive_shopping">
            <div class="container">
                <h1 class="sec-tit">@lang("site.heading.register")</h1>

                @livewire('register-form')
            </div>
        </div>
    </section>

@endsection

