@extends('site.layouts.app')
@section("title",__('site.heading.home'))
@section('content')
    <div class="mainBackground">
        <header class="header-home">
            @include('site.components.navbar')
        </header>
        <section class="slider">
            <div class="main-slider">
                <div class="mainItem-overlay d-flex flex-column justify-content-center">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-xl-4 col-lg-6 col-sm-12">
                                <div class="slid-body">
                                    <h3 class="slid-tit">{{data_get($landing_settings,"content.header.$locale.title")}}</h3>
                                    <div class="slid-desc">
                                        <p>{{data_get($landing_settings,"content.header.$locale.description")}}</p>
                                    </div>
                                    <div class="btns-url">
                                        <a href="#" class="button-wrapper">
                                            <div class="button">
                                                <img src="assets/img/play.png" alt="Google Play">
                                            </div>
                                            <div class="popover">@lang("site.fields.download_apps_now")</div>
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
                                    @php($image =data_get($landing_settings,"content.header.$locale.image"))
                                    @php($image =is_array($image)?array_values($image)[0]:$image)
                                    <img src="{{asset('storage/'.$image)}}" alt="slider-img">
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
                {{data_get($landing_settings,"content.about.$locale.about")}}
            </div>
        </div>
    </section>

    <section id="features" class="block features">
        <div class="container">
            @foreach(data_get($landing_settings,"content.features.$locale")??[] as $feature)

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
                                                @foreach(collect($feature['pros'])->pluck('title') as $pros)
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
            <h4 class="sec-tit">@lang('site.heading.faqs')</h4>
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

