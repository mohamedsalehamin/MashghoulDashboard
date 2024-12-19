@extends('site.layouts.app')
@section("title",__('site.heading.home'))
@section('content')

    <div class="mainBackground">
        <header class="header-home">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="menu-logo text-center">
                        <a href="{{route('site.home')}}">
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
                                <a href="#about"> من نحن </a>
                            </li>
                            <li class="linkMenu">
                                <a href="#features">مميزات مشغول</a>
                            </li>
                            <li class="linkMenu">
                                <a href="#faq">الأسئلة الشائعة</a>
                            </li>
                        </ul>
                    </nav>
                    @livewire('register-button')
                </div>
            </div>
        </header>
        <section class="slider">
            <div class="main-slider">
                <div class="mainItem-overlay d-flex flex-column justify-content-center">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-xl-4 col-lg-6 col-sm-12">
                                <div class="slid-body">
                                    <h3 class="slid-tit">جمالك يبدأ معنا!</h3>
                                    <div class="slid-desc">
                                        <p>
                                            تطبيق التجميل الأول في السعودية. خدمات متكاملة، ولاء يكافئك، وحجوزات
                                            تفاعلية سهلة
                                        </p>
                                    </div>
                                    <div class="btns-url">
                                        <a href="#" class="button-wrapper">
                                            <div class="button">
                                                <img src="assets/img/play.png" alt="Google Play">
                                            </div>
                                            <div class="popover">حمل التطبيق الآن</div>
                                        </a>
                                        <a href="#">
                                            <div class="button">
                                                <img src="assets/img/store.png" alt="App Store">
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-sm-12">
                                <div class="image-slider">
                                    <img src="assets/img/slider.png" alt="slider-img">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <section id="about" class="block about text-center">
        <div class="container">
            <div class="about-img">
                <img src="assets/img/about-logo.png" alt="">
            </div>
            <div class="about-content">
                {{data_get($landing_settings,'content.about')}}
            </div>
        </div>
    </section>

    <section id="features" class="block features">
        <div class="container">
            @foreach(data_get($landing_settings,'content.features') as $feature)
                @php($imagePath = isset(array_values(data_get($feature,'image'))[0])?array_values(data_get($feature,'image'))[0]:data_get($feature,'image'))
                @php($title=data_get($feature,'title'))
                @php($description=data_get($feature,'description'))
                <div class="features_item">
                    <div class="row">
                        <div class="col-lg-6 wow animate__animated animate__bounceInLeft" data-wow-delay="0.2s">
                            <div class="feature-image-container">
                                <div class="features-img">
                                    <img src="{{asset("storage/$imagePath")}}" alt="Feature 1">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 wow animate__animated animate__bounceInRight" data-wow-delay="0.2s">
                            <div class="features-content">
                                <h4 class="features-title">{{$title}}</h4>
                                <p class="feature-desc">{{$description}}</p>
                                @if(isset($feature['pros'])&& count($feature['pros']))
                                    <div class="row itemsPro">

                                        <div class="col-6">
                                            <ul class="list-unstyled">
                                                @foreach(collect($feature['pros'])->pluck('en.title') as $pros)
                                                    <li>{{$pros}}</li>
                                                @endforeach

                                            </ul>
                                        </div>
                                        <div class="col-6">
                                            <ul class="list-unstyled">
                                                @foreach(collect($feature['pros'])->pluck('ar.title') as $pros)
                                                    <li>{{$pros}}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endisset
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </section>
    <section id="faq" class="block faq faq-container">
        <div class="container">
            <h4 class="sec-tit">الأسئلة الشائعة</h4>
            @foreach(\App\ContentModule\Models\Faq::get() as  $index =>$faq)
                @php($delay =$loop->iteration *0.2)
                <div class="faq-item wow animate__animated animate__bounceInRight" data-wow-delay="{{$delay}}s">
                    <div class="question">
                        <span>{{$faq->question}}</span>
                        <i class="icon fas fa-plus"></i>
                    </div>
                    <div class="answer">
                        <p>{{$faq->answer}}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

@endsection

