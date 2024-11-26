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
                        <span class="text"> رجوع </span>
                        <i class="fa-regular fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <section class="auth-body">
        <div class="container">
            <h1 class="auth-title">ادخل كود التفعيل</h1>
            <h3 class="auth-subtitle">
                اكد كود التفعيل الخاص بك
            </h3>
            @livewire('verify-otp-form')
        </div>
    </section>

@endsection
