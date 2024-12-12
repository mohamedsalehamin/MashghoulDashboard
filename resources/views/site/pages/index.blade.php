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
        <section class="slider">
            <div class="main-slider">
                <div class="mainItem-overlay d-flex flex-column justify-content-center">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-xl-4 col-lg-6 col-sm-12">
                                <div class="slid-body">
                                    <h3 class="slid-tit">{{data_get($landing_settings,'content.header.title')}}</h3>
                                    <div class="slid-desc">
                                        <p>
                                            {{data_get($landing_settings,'content.header.description')}}
                                        </p>
                                    </div>

                                    <div class="btns-url">
                                        <a href="{{data_get($settings,'applications_links.google_play_link')}}">
                                            <div class="button">
                                                <img src="assets/img/play.png" alt="Google Play">
                                            </div>
                                        </a>
                                        <a href="{{data_get($settings,'applications_links.apple_store_link')}}">
                                            <div class="button">
                                                <img src="assets/img/store.png" alt="App Store">
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-sm-12">
                                <div class="image-slider">
                                    <img src="assets/img/sliderImg.png" alt="slider-img">
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
                <div class="features_item">
                    <div class="row">
                        <div class="col-lg-6 wow animate__animated animate__bounceInLeft" data-wow-delay="0.2s">
                            <div class="feature-image-container">
                                <div class="features-img">
                                    @php($imagePath = isset(array_values(data_get($feature,'image'))[0])?array_values(data_get($feature,'image'))[0]:data_get($feature,'image'))
                                    <img src="{{asset("storage/$imagePath")}}"
                                         alt="Feature 1">
                                </div>
                                <div class="feature-icon">
                                    <img src="assets/img/air.png" alt="Feature 2">
                                </div>
                                <div class="feature-icon">
                                    <img src="assets/img/woman.png" alt="Feature 3">
                                </div>
                                <div class="feature-icon">
                                    <img src="assets/img/hair.png" alt="Feature 4">
                                </div>
                                <div class="feature-icon">
                                    <img src="assets/img/hair2.png" alt="Feature 5">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 wow animate__animated animate__bounceInRight"
                             data-wow-delay="0.2s">
                            <div class="features-content">
                                <h4 class="features-title">{{data_get($feature,'title')}}</h4>
                                <p class="feature-desc">{{data_get($feature,'description')}}</p>
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

