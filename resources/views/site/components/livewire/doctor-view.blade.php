<div class="container">
    <div class="single-content">
        <div class="single-body">
            <div class="single-card doctor-card">
                <div class="main-information">
                    <div class="single-img loading-img lazy-img-parent" wire:ignore>
                        <img data-src="{{$doctor->image}}" class="lazy-img"/>
                    </div>

                    <div class="single-information">
                        <div class="information-head">
                            <h1 class="page-title">{{$doctor->name}}</h1>
                            <span
                                    class="status {{$doctor->isAvailableToday()?'available':'unavailable'}}"> {{$doctor->isAvailableToday()?__('site.enum.available'):__('site.enum.unavailable')}}</span>

                        </div>
                        <a href="{{route('specialties.show',$doctor->specialization->id)}}"
                           class="single-specialty">
                            {{$doctor->specialization->name}}
                        </a>
                        <div class="single-rate">
                            @foreach(range(1,5) as $star)
                                <i class="fa-solid fa-star @if($doctor->avgRate()>= $star) active  @endif "></i>
                            @endforeach
                        </div>
                        <p class="single-description">
                            @foreach($doctor->specializations as $one)
                                <a href="{{route('specialties.show',$one->id)}}">{{$one->name}}</a>
                                @if(!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </p>
                    </div>
                    <div class="single-tools">
                        {{--                        <button class="single-btn">--}}
                        {{--                            <i class="fa-regular fa-share-nodes"></i>--}}
                        {{--                        </button>--}}
                        <a class="single-btn "
                           wire:click="toggleFavorite"
                           @if($this->isFavorite())
                               style="color: #fff;background-color: #52cbc0;border-color: #52cbc0;"
                                @endif
                        >
                            <i class="fa-regular fa-heart"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="single-card doctor-card">
                <div class="single-services">
                    <div class="card-head services-head">
                        <h3 class="card-title services-title">@lang('site.heading.available_services')</h3>
                    </div>
                    <div class="services-list">
                        @foreach($doctor->services()->enabled()->get() as $service)
                            <label class="service-select" wire:click="toggleSelectService({{$service->id}})">
                                <input name="service" type="checkbox" value="{{$service->id}}"
                                       :checked="$wire.selectedService == '{{$service->id}}'?'checked':'' "
                                />
                                <div class="service-info">
                                    <h5 class="service-title">{{$service->name[app()->getLocale()]}}</h5>
                                    <span class="service-hint"> {{$service->type->getLabel()}} </span>
                                    <strong class="service-price"> {{$service->final_price}} </strong>

                                    <button class="service-mark">
                                        <span> @lang('site.enum.select') </span>
                                        <span class="selected">@lang('site.enum.deselect')</span>
                                    </button>
                                </div>
                            </label>
                        @endforeach

                    </div>
                </div>
            </div>
            <div class="single-card doctor-card">
                <div class="single-description">
                    <div class="card-head description-head">
                        <h3 class="card-title description-title">@lang('site.heading.doctors_bio')</h3>
                    </div>
                    <p>
                        {{$doctor->bio}}
                    </p>
                    <div class="single-table">
                        <div class="table-row">
                            <strong> @lang('site.fields.certifications')</strong>

                            <span>
                                @foreach($doctor->user->certificates as $certification)
                                    {{$certification->name}} {{$certification->university_name}}
                                    @if(!$loop->last)
                                        ,
                                    @endif

                                @endforeach
                            </span>

                        </div>
                        <div class="table-row">
                            <strong>@lang('site.fields.exp_of_years')</strong>
                            <span> {{$doctor->experience_years}} </span>
                        </div>
                        <div class="table-row">
                            <strong> @lang('site.fields.nationality') </strong>
                            <span>  {{$doctor->nationality->name}} </span>
                        </div>
                        <div class="table-row">
                            <strong> @lang('site.fields.language') </strong>
                            <span>  {{$doctor->language->name}} </span>

                        </div>
                    </div>
                </div>
            </div>
            <div class="single-card doctor-card">
                <div class="single-extras">
                    <div class="card-head extras-head">
                        <h3 class="card-title extras-title">@lang('site.heading.clinic_data')</h3>
                    </div>
                    <div class="single-table">
                        <div class="table-row">
                            <strong> @lang('forms.fields.city_name') </strong>
                            <span>{{$doctor->clinic->city->name}} </span>
                        </div>
                        <div class="table-row">
                            <strong> @lang('site.fields.address') </strong>
                            <span>
                      <a href="https://www.google.com/maps/search/?api=1&query={{$doctor->clinic->location->latitude}},{{$doctor->clinic->location->longitude}}"
                         target="_blank"> @lang('site.heading.show_on_map') </a>
                    </span>
                        </div>
                        <div class="table-row">
                            <strong>@lang('site.heading.reserve_type') </strong>
                            <span> {{$doctor->times_type->getLabel()}}</span>
                        </div>
                        {{--                                <div class="table-row">--}}
                        {{--                                    <strong> مواعيد الدوام </strong>--}}
                        {{--                                    <span> الأحد - الأربعاء ( 2 مساءًا - 8 مساءًا ) </span>--}}
                        {{--                                </div>--}}
                    </div>
                    <div class="extras-imgs">
                        @foreach($doctor->user->getMedia('clinic') as $image)
                            <a
                                    href="{{$image->getUrl()}}"
                                    data-fancybox="doctor"
                                    class="extra-img loading-img lazy-img-parent"
                            >
                                <img
                                        data-src="{{$image->getUrl()}}"
                                        class="img-fluid lazy-img"
                                />
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @livewire('doctor-date-times-view', ['doctor' => $doctor])
    </div>
    <div class="related-content">
        <div class="section-head">
            <h2 class="section-title">@lang('site.heading.similar_doctors')</h2>
        </div>
        <div class="doctors-slider custom-slider">
            <div class="swiper">
                <div class="swiper-wrapper">

                    @foreach(\App\UsersModule\Models\Doctor::whereHas('user.services')

->whereHas('clinic',fn($q)=>$q->where('city_id',$doctor->clinic?->city_id))
->where('specialty_id',$doctor->specialty_id)
->where('id','!=',$doctor->id)->limit(5)->get() as $doctor)
                        <div class="swiper-slide">
                            @livewire("doctor-card",['doctor'=>$doctor])
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="slider-pagination"></div>
        </div>
    </div>
</div>
