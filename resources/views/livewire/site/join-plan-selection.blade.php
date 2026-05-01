@php
    /** @var string $selectedPeriod @var string $paymentMethod @var \Illuminate\Support\Collection $plansWithPrices @var int $totalPlansCount */
    $plansCount = $plansWithPrices->count();
    $assetBase = asset('assets/site');
@endphp

{{-- Single root element: Livewire needs one root for reliable morphing / wire:click updates. --}}
<div class="join-plan-selection-livewire">
{{-- طريقة الدفع --}}
<div class="container mb-5 mt-5">
    <div class="text-center mb-5">
        <h3 class="login-main-title">{{ __('panel.enums.payment_method') }}</h3>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="payment-card d-flex flex-column flex-md-row gap-4 justify-content-center align-items-center payment-methods">
                <label class="payment-method-item custom-radio w-100">
                    <input type="radio" wire:model.live="paymentMethod" value="myfatoorah">
                    <div class="method-content">
                        <span class="check-circle"></span>
                        <span class="method-name">{{ __('panel.enums.myfatoorah') }}</span>
                        <div class="method-icons">
                            <img src="{{ $assetBase }}/images/epay.png" class="img-fluid" alt="">
                        </div>
                    </div>
                </label>

                <label class="payment-method-item custom-radio w-100">
                    <input type="radio" wire:model.live="paymentMethod" value="tabby">
                    <div class="method-content">
                        <span class="check-circle"></span>
                        <span class="method-name">{{ __('panel.enums.tabby') }}</span>
                        <div class="method-icons">
                            <img src="{{ $assetBase }}/images/tabby.png" class="img-fluid" alt="">
                        </div>
                    </div>
                </label>
            </div>
        </div>
    </div>
</div>

{{-- الباقات المتوفرة --}}
<div class="container mb-5 mt-5">
    <div class="text-center mb-5">
        <h3 class="login-main-title">{{ __('menu.plans') }}</h3>
    </div>

    <ul class="desc-tabs d-flex list-unstyled justify-content-center" wire:key="join-desc-tabs-{{ $selectedPeriod }}">
        @foreach(['monthly' => __('panel.enums.period_monthly'), 'quarterly' => __('panel.enums.period_quarterly'), 'yearly' => __('panel.enums.period_yearly')] as $period => $label)
            <li class="nav-item">
                <button
                    type="button"
                    wire:click="selectPeriod('{{ $period }}')"
                    class="nav-link {{ $selectedPeriod === $period ? 'active' : '' }}"
                >
                    {{ $label }}
                </button>
            </li>
        @endforeach
    </ul>

    <div class="packages-container" wire:key="join-packages-{{ $selectedPeriod }}">
        <div class="row">
            @foreach($plansWithPrices as $row)
                @php
                    $plan = $row['plan'];
                    $planPrice = $row['planPrice'];
                    $planName = $plan->displayName();
                @endphp
                <div
                    class="col-lg-4 mb-4"
                    wire:key="join-plan-{{ $plan->id }}-{{ $selectedPeriod }}-{{ $planPrice->id }}"
                    @if($plansCount === 1) style="max-width:420px;margin-inline:auto;" @endif
                >
                    <div class="package-card {{ $plan->is_free ? 'feature-package' : '' }}">
                        <h4 class="package-title">{{ $planName }}</h4>

                        <div class="text-center mb-4 p-4">
                            {{-- package-price-amount / package-period — NOT annual-price / quarterly-price (main.js toggles those) --}}
                            <div class="package-price">
                                @if($plan->is_free)
                                    <div class="package-price-amount">{{ __('site.join.free_plan') }}</div>
                                    <div class="package-period small text-muted mt-1">&nbsp;</div>
                                @else
                                    <div class="package-price-amount">{{ $planPrice->price->formatByDecimal() }} <i class="icon-saudi_riyal"></i></div>

                                    <div class="package-period small text-muted mt-1">{{ $planPrice->period_label }}</div>
                                @endif
                               
                            </div>

                            <a
                                href="{{ route('site.join.register', ['plan' => $plan->id, 'plan_price' => $planPrice->id, 'payment' => $paymentMethod]) }}"
                                class="btn {{ $plan->is_free ? 'btn-green' : 'btn-blue' }} w-100 mt-2 d-inline-block text-center"
                            >
                            @if($plan->is_free)
                                {{ __('site.join.start_for_free') }}
                            @else
                                {{ __('site.join.start_now') }}
                            @endif
                            </a>
                        </div>

                        <h6 class="package-features-title mb-3 px-4">{{ __('forms.fields.features') }}</h6>
                        <ul class="list-unstyled mb-0 px-4">
                            @if($plan->commission_percent !== null && $plan->commission_percent !== '')
                                <li class="package-feature">
                                    <i class="fa-solid fa-percent"></i>
                                    {{ __('site.join.commission_label') }}: {{ rtrim(rtrim(number_format((float) $plan->commission_percent, 2, '.', ''), '0'), '.') }}%
                                </li>
                            @endif
                            @if(!empty($plan->features))
                                @foreach($plan->features ?? [] as $feature)
                                    @php
                                        $text = is_array($feature) ? ($feature[app()->getLocale()] ?? $feature['ar'] ?? $feature['en'] ?? '') : $feature;
                                        $text = is_string($text) ? $text : '';
                                    @endphp
                                    @if($text)
                                        <li class="package-feature">
                                            <i class="fa-solid fa-check"></i>
                                            <span>{{ $text }}</span>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if($totalPlansCount === 0)
        <div class="text-center py-5 text-muted">
            {{ __('panel.messages.no_data') ?? __('site.no_data') }}
        </div>
    @elseif($plansWithPrices->isEmpty())
        <div class="text-center py-5 text-muted">
            {{ __('site.join.no_plans_for_period') }}
        </div>
    @endif
</div>
</div>
