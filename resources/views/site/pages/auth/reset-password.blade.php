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
                    <a
                            href="{{route('auth.login')}}"
                            class="header-btn header-link back-btn"
                    >
                        <span class="text">@lang("site.buttons.back") </span>
                        <i class="fa-regular fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <section class="auth-body">
        <div class="container">
            <h1 class="auth-title">@lang('site.heading.reset_password')</h1>
            <h3 class="auth-subtitle">
                @lang('site.heading.reset_password_text')

            </h3>
            @livewire('reset-password-form')
        </div>
    </section>

@endsection
