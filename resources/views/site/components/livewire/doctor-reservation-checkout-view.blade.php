<div class="container">
    <div class="single-content">
        <div class="single-body">
            <div class="single-card doctor-card">
                <div class="single-description">
                    <div class="card-head description-head">
                        <h3 class="card-title description-title">
                            @lang('site.heading.reservation_summary')
                        </h3>
                    </div>
{{--                    <p>--}}
{{--                        هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما--}}
{{--                        سيلهي القارئ عن التركيز على الشكل الخارجي للنص أو شكل توضع--}}
{{--                        الفقرات في الصفحة التي يقرأها. ولذلك يتم استخدام طريقة لوريم--}}
{{--                        إيبسوم لأنها تعطي توزيعاَ طبيعياَ -إلى حد ما- للأحرف عوضاً عن--}}
{{--                        استخدام "هنا يوجد محتوى نص"--}}
{{--                    </p>--}}
                    <div class="single-table">
                        <div class="table-row">
                            <strong> @lang('site.fields.doctor') </strong>
                            <span> {{site()->reservation()->doctor()->name}} </span>
                        </div>
                        <div class="table-row">
                            <strong> @lang('site.fields.speciality') </strong>
                            <span>{{site()->reservation()->doctor()->specialization->name}} - {{site()->reservation()->doctor()->specializations->pluck('name')->implode(",")}} </span>
                        </div>
                        <div class="table-row">
                            <strong> @lang('site.fields.service') </strong>

                            <span>{{site()->reservation()->services()->first()->associatedModel->name[app()->getLocale()]}}</span>
                        </div>
                        <div class="table-row">
                            <strong> @lang('site.fields.date') </strong>
                            <span> {{site()->reservation()->formattedDate()}} </span>
                        </div>
                        <div class="table-row">
                            <strong> @lang('site.fields.time') </strong>
                            <span>  {{site()->reservation()->slot()}}</span>
                        </div>
                        <div class="table-row">
                            <strong>@lang('site.fields.reservation_duration') </strong>
                            <span>{{site()->reservation()->doctor()?->session_duration??60}} @lang('site.enum.min') </span>
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
            <div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox"  wire:model="terms"/>
                        <span class="mark">
                  <i class="fa-regular fa-check"></i>
                </span>
                        <span class="text">
                  @lang('site.heading.accept_terms')<a href="{{route('pages.show',$pages['terms_and_conditions']->id)}}">@lang('site.heading.terms_and_conditions')</a>
                </span>

                    </label>

                </div>

                    @error('terms')
                    <span class="text-danger"> {{$message}}</span>
                    @enderror

            </div>


            <button wire:click="handle" class="single-btn">
                <i class="fa-regular fa-check"></i>
                @lang('site.buttons.checkout')
            </button>
        </div>
    </div>
</div>
