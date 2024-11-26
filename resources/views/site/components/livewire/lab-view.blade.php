<section class="page-content single-page">
    <div class="container">
        <div class="single-content">
            <div class="single-body">
                <div class="single-card lab-card">
                    <div class="main-information" >
                        <div class="single-img loading-img lazy-img-parent" wire:ignore>
                            <img data-src="{{$lab->image}}" class="lazy-img" alt="{{$lab->title}}"/>
                        </div>
                        <div class="single-information">
                            <div class="information-head">
                                <h1 class="page-title">{{$lab->title}}</h1>
                                <span
                                        class="status {{$lab->isAvailableToday()?'available':'unavailable'}}"> {{$lab->isAvailableToday()?__('site.enum.available'):__('site.enum.unavailable')}}</span>
                            </div>
                            <div class="single-rate">
                                @foreach(range(1,5) as $star)
                                    <i class="fa-solid fa-star @if($lab->avgRate()>= $star) active  @endif "></i>
                                @endforeach
                            </div>
{{--                            <p class="single-description">--}}
{{--                                {{$lab->description}}--}}
{{--                            </p>--}}
                        </div>
                        <div class="single-tools">
{{--                            <button class="single-btn">--}}
{{--                                <i class="fa-regular fa-share-nodes"></i>--}}
{{--                            </button>--}}
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
                <div class="single-card lab-card">
                    <div class="single-services">
                        <div class="card-head services-head">
                            <h3 class="card-title services-title">@lang('site.heading.available_analysis')</h3>
                            <div class="search-input">
                    <span class="search-icon">
                      <i class="fa-regular fa-magnifying-glass"></i>
                    </span>
                                <input type="search" wire:model.live="q"
                                       placeholder="@lang('site.heading.search_with_analysis_name')"/>

                            </div>
                        </div>
                        <div class="services-list">
                            @foreach($services as $service)
                                <label class="service-select" wire:click="updateSelectedServices({{$service->id}})">
                                    <input type="checkbox"/>
                                    <div class="service-info">
                                        <h5 class="service-title">{{$service->name[app()->getLocale()]}}</h5>
                                        <strong class="service-price"> {{$service->finalPrice}} </strong>
                                        <button type="button" class="service-mark">
                                            <span> @lang('site.enum.select') </span>
                                            <span class="selected">@lang('site.enum.deselect')</span>
                                        </button>
                                    </div>
                                    <span class="service-condition">{{$service->description[app()->getLocale()]}}</span>
                                </label>
                            @endforeach


                        </div>
                    </div>
                </div>
                <div class="single-card lab-card">
                    <div class="single-description">
                        <div class="card-head description-head">
                            <h3 class="card-title description-title">@lang('site.heading.lab_bio')</h3>
                        </div>
                        <p>
                            {{$lab->description}}
                        </p>
                    </div>
                </div>
                <div class="single-card lab-card">
                    <div class="single-extras">
                        <div class="card-head extras-head">
                            <h3 class="card-title extras-title">@lang('site.heading.lab_data')</h3>
                        </div>
                        <div class="single-table">
                            <div class="table-row">
                                <strong> @lang('site.fields.city') </strong>
                                <span> {{$lab->city->name}} </span>
                            </div>
                            <div class="table-row">
                                <strong> @lang('site.fields.address') </strong>
                                <span>
                      <a href="https://www.google.com/maps/search/?api=1&query={{$lab->location->latitude}},{{$lab->location->longitude}}"
                         target="_blank">@lang('site.fields.location_on_map') </a>
                    </span>
                            </div>
                            <div class="table-row">
                                <strong> مواعيد الدوام </strong>
                                <span> {{$lab->workingDaysList()}} </span>
                            </div>
                        </div>
                        <div class="extras-imgs" wire:ignore>
                            @foreach($lab->getMedia() as $media)
                                <a
                                        href="{{$media->getUrl()}}"
                                        data-fancybox="lab"
                                        class="extra-img loading-img lazy-img-parent"
                                >
                                    <img
                                            data-src="{{$media->getUrl()}}"
                                            class="img-fluid lazy-img"
                                    />
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @livewire('lab-date-times-view',['lab' => $lab])
        </div>
    </div>
</section>
