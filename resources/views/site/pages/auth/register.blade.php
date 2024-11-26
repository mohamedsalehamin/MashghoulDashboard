@extends('site.layouts.guest')
@section('content')
    <header class="auth-header">
        <div class="container-fluid">
            <div class="header">
                <a href="{{url('/')}}" class="logo">
                    @if(app()->getLocale()=='ar')
                        <img src="images/logoColored.svg" class="img-fluid"/>
                    @else

                        <img src="images/logoColored-en.png" class="img-fluid"/>
                    @endif                </a>
                <div class="header-tools">
                    <h6 class="auth-btn-title">@lang('site.heading.registered_before')</h6>
                    <a href="{{route('auth.login')}}" class="header-btn header-link">
                        <i class="fa-regular fa-user"></i>
                        <span class="text"> @lang('site.buttons.login') </span>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <!-- End Header -->

    <!-- Start Page Content -->
    <section class="auth-body">
        <div class="container">
            <h1 class="auth-title">@lang('site.heading.register')</h1>
            <h3 class="auth-subtitle">
                @lang('site.heading.register_subtitle')
            </h3>

            @livewire('register-form')
        </div>
    </section>

@endsection
