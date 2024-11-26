@extends('site.layouts.guest')
@section('content')
    <header class="success-header">
        <div class="container-fluid">
            <div class="header">
                <a href="{{url('/')}}" class="logo">
                    <img src="images/logoColored.svg" class="img-fluid"/>
                </a>
            </div>
        </div>
    </header>

    <section class="success-body success-page">
        <div class="container">
            <div class="success-content">
                <div class="success-icon">
                    <i class="fa-light fa-circle-check"></i>
                </div>
                <h1 class="success-title">
                    @lang('site.heading.thanks_to_join_tamona')</h1>
                <p class="success-description">
                    @lang('site.heading.thanks_to_join_tamona_subtitle')
                </p>
                <a href="{{route('auth.login')}}" class="success-btn main-btn">
                    @lang('site.heading.login')
                </a>
            </div>
        </div>
    </section>
@endsection
