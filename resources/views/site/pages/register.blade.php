@extends('site.layouts.app')
@section("title",__('site.heading.home'))
@section('content')
    <header class="head-inside">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <div class="menu-logo text-center">
                    <a href="{{url('/')}}">
                        <img
                            src="assets/img/logo.png"
                            alt="الشعار"
                            class="img-fluid logo"
                        />
                    </a>
                </div>
                <nav>
                    <ul class="nav-list d-flex align-items-center">
                        <li class="linkMenu">
                            <a href="{{ route('site.home') }}#about"> من نحن </a>
                        </li>
                        <li class="linkMenu">
                            <a href="{{ route('site.home') }}#features">مميزات مشغول</a>
                        </li>
                        <li class="linkMenu">
                            <a href="{{ route('site.home') }}#faq">الأسئلة الشائعة</a>
                        </li>
                    </ul>
                </nav>
                <div class="lastSide">
                    <a href="#" class="join_Us">
                        <div>
                            انضم كمقدم خدمة
                        </div>
                    </a>
                    <div class="menu-icons open-me d-lg-none">
                        <label for="check" class="">
                            <input type="checkbox" id="check">
                            <span></span>
                            <span></span>
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="breadcrumb-container py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-2" style="direction: rtl; text-align: right;">
                    <li class="breadcrumb-item">
                        <a href="{{route('site.home')}}" class="text-decoration-none text-primary">
                            الرئيسية
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        التسجيل كمقدم خدمة
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <section class="custom-content-section">
        <div class="single_page archive_shopping">
            <div class="container">
                <h1 class="sec-tit">التسجيل (حساب جديد)</h1>

                @livewire('register-form')
            </div>
        </div>
    </section>

@endsection

