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
                    @endif
                </a>
                <div class="header-tools">
                    <h6 class="auth-btn-title">@lang('site.heading.not_registered_yet')</h6>
                    <a
                        href="{{route('auth.register')}}"
                        class="header-btn header-link"
                    >
                        <i class="fa-regular fa-user"></i>
                        <span class="text"> @lang('site.heading.register') </span>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <section class="auth-body">
        <div class="container">
            <h1 class="auth-title">@lang("site.heading.login")</h1>
            <h3 class="auth-subtitle">@lang('site.heading.login_text')</h3>
            @livewire('login-form')
        </div>
    </section>
@endsection

