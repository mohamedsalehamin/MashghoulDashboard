<div class="container">
    <div class="single-content">
        <div class="single-body">
            <div class="single-card lab-card">
                <div class="single-description">
                    <div class="card-head description-head">
                        <h3 class="card-title description-title">
                            @lang('site.heading.reservation_summary')
                        </h3>
                    </div>
                    <div class="single-table">
                        <div class="table-row">
                            <strong> @lang('site.heading.lab') </strong>
                            <span> {{site()->reservation()->provider()->title}} </span>
                        </div>
                        <div class="table-row">
                            <strong> @lang('site.fields.date') </strong>
                            <span> {{site()->reservation()->formattedDate()}} </span>
                        </div>
                        <div class="table-row">
                            <strong> @lang('site.fields.time') </strong>
                            <span>  {{site()->reservation()->slot()}}</span>
                        </div>
                    </div>
                    <div class="selected-services">
                        <h4 class="side-title">@lang('site.heading.selected_analysis')</h4>
                        <div class="selected-list">

                            @foreach(site()->reservation()->services() as $service)
                                <div class="selected-item">
                                    <div class="service-info">

                                        <h5 class="service-title">{{$service->associatedModel->name[app()->getLocale()]}}</h5>
                                        <strong class="service-price">{{$service->associatedModel->final_price}}</strong>
                                    </div>
                                    <span class="service-condition">
                        {{$service->associatedModel->description[app()->getLocale()]}}
                  </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="single-card side-card">
            <div class="card-head">
                <h3 class="card-title">@lang('site.heading.payment_methods')</h3>
            </div>
            <div class="select-payment">
                <div class="payment-list">
                    <label class="payment-select">
                        <input type="radio" name="payment" checked/>
                        <div class="payment-info">
                            <span class="text">@lang('site.heading.visa_mastercard') </span>
                            <img src="images/payments/1.png" alt="@lang('site.heading.visa_mastercard')"/>
                        </div>
                    </label>
                    <label class="payment-select">
                        <input type="radio" name="payment"/>
                        <div class="payment-info">
                            <span class="text"> @lang('site.heading.mada') </span>
                            <img src="images/payments/2.png" alt="@lang('site.heading.mada')"/>
                        </div>
                    </label>
                    <label class="payment-select">
                        <input type="radio" name="payment"/>
                        <div class="payment-info">
                            <span class="text"> @lang('site.heading.apple_pay') </span>
                            <img src="images/payments/3.png" alt="@lang('site.heading.visa_mastercard')"/>
                        </div>
                    </label>
                </div>
            </div>
            <div class="side-totals">
                <div class="side-total">
                    <span>@lang('site.heading.reserve_price')</span>
                    <strong>{{site()->reservation()->total()}}</strong>
                    <span class="hint"> @lang('site.heading.include_taxes') </span>
                </div>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" wire:model="terms"/>
                    <span class="mark">
                  <i class="fa-regular fa-check"></i>
                </span>
                    <span class="text">@lang('site.heading.accept_terms')<a href="{{route('pages.show',$pages['terms_and_conditions']->id)}}">@lang('site.heading.terms_and_conditions')</a>
                </span>
                </label>

            </div>
            @error('terms')
            <span class="text-danger"> {{$message}}</span>
            @enderror
            <button wire:click="handle" class="single-btn">
                <i class="fa-regular fa-check"></i>
                @lang('site.buttons.checkout')
            </button>
        </div>
    </div>
</div>
