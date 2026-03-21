@php
    $livewire = $livewire ?? $this;
    /** @var \Illuminate\Database\Eloquent\Collection $plans Injected in ListPlans::content() viewData */
    /** @var float $providerBalance */
    $assetBase = asset('assets/site');
    $plansCount = $plans->count();
@endphp

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
<link rel="stylesheet" href="{{ asset('assets/site/css/provider-plans.css') }}" />

<div class="mashghoul-provider-plans join-us-page pb-64 max-w-7xl mx-auto px-4">
    {{-- Page title --}}
    <div class="text-center mb-8">
        <h2 class="login-main-title">{{ __('menu.plans') }}</h2>
    </div>

    {{-- Payment method (same block order as join-us.html: payment before packages) --}}
    <div class="mb-10 mt-6">
        <div class="text-center mb-8">
            <h3 class="login-main-title text-2xl md:text-3xl">{{ __('panel.enums.payment_method') }}</h3>
        </div>
        @php
            $anyWallet = false;
            foreach ($plans as $p) {
                $pp = $livewire->getPriceForPeriod($p);
                if ($pp && $providerBalance >= (float) $pp->price->formatByDecimal()) {
                    $anyWallet = true;
                    break;
                }
            }
        @endphp
        <div class="max-w-6xl mx-auto">
            <div class="payment-card">
                <div class="payment-methods payment-methods-row {{ $anyWallet ? 'payment-methods--three' : 'payment-methods--two' }}">
                    <label class="payment-method-item w-full">
                        <input type="radio" wire:model.live="paymentMethod" value="myfatoorah" />
                        <div class="method-content">
                            <span class="check-circle"></span>
                            <span class="method-name">{{ __('panel.enums.myfatoorah') }}</span>
                            <div class="method-icons">
                                <img src="{{ $assetBase }}/images/epay.png" class="img-fluid" alt="" />
                            </div>
                        </div>
                    </label>
                    <label class="payment-method-item w-full">
                        <input type="radio" wire:model.live="paymentMethod" value="tabby" />
                        <div class="method-content">
                            <span class="check-circle"></span>
                            <span class="method-name">{{ __('panel.enums.tabby') }}</span>
                            <div class="method-icons">
                                <img src="{{ $assetBase }}/images/tabby.png" class="img-fluid" alt="" />
                            </div>
                        </div>
                    </label>
                    @if($anyWallet)
                        <label class="payment-method-item w-full">
                            <input type="radio" wire:model.live="paymentMethod" value="wallet" />
                            <div class="method-content">
                                <span class="check-circle"></span>
                                <span class="method-name">
                                    {{ __('forms.actions.subscribe_via_wallet') }}
                                    ({{ number_format($providerBalance, 2) }} {{ __('forms.suffixes.sar') }})
                                </span>
                                <div class="method-icons">
                                    <i class="fa-solid fa-wallet text-[#002f87] text-xl"></i>
                                </div>
                            </div>
                        </label>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Available packages + period tabs (join-us: الباقات المتوفرة) --}}
    <div class="mb-8 mt-10">
        <div class="text-center mb-8">
            <h3 class="login-main-title text-2xl md:text-3xl">{{ __('menu.plans') }}</h3>
        </div>

        <ul class="desc-tabs" wire:key="desc-tabs-{{ $livewire->selectedPeriod }}">
            @foreach(['monthly' => __('panel.enums.period_monthly'), 'quarterly' => __('panel.enums.period_quarterly'), 'yearly' => __('panel.enums.period_yearly')] as $period => $label)
                <li class="nav-item">
                    <button
                        type="button"
                        wire:click.prevent="selectPeriod('{{ $period }}')"
                        class="nav-link {{ $livewire->selectedPeriod === $period ? 'active' : '' }}"
                    >
                        {{ $label }}
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="packages-container" wire:key="packages-{{ $livewire->selectedPeriod }}">
            {{-- Use .plans-grid from provider-plans.css — Filament bundle does not include arbitrary Tailwind classes from this view --}}
            <div class="plans-grid">
                @foreach($plans as $plan)
                    @php
                        $planName = is_string($plan->name) ? $plan->name : ($plan->name[app()->getLocale()] ?? $plan->name['ar'] ?? $plan->name['en'] ?? '');
                        $planPrice = $livewire->getPriceForPeriod($plan);
                    @endphp
                    @if($planPrice)
                        <div
                            wire:key="plan-card-{{ $plan->id }}-{{ $livewire->selectedPeriod }}-{{ $planPrice->id }}"
                            class="plans-grid__item"
                            style="{{ $plansCount === 1 ? 'max-width:420px; width:100%; margin-inline:auto;' : '' }}"
                        >
                            <div class="package-card {{ $plan->is_free ? 'feature-package' : '' }}">
                                <h4 class="package-title">{{ $planName }}</h4>
                                <div class="package-content">
                                    <div class="text-center mb-4 px-4">
                                        <div class="package-price">
                                            <div class="annual-price">
                                                @if($plan->is_free)
                                                    {{ __('site.join.free_plan') }}
                                                @else
                                                    {{ $planPrice->price->format() }}
                                                @endif
                                            </div>
                                            <div class="package-period">
                                                {{ $planPrice->period_label }}
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            wire:click="subscribeToPlan({{ $plan->id }}, {{ $planPrice->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="subscribeToPlan"
                                            class="provider-plan-subscribe-btn mt-2 w-full inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm transition focus:outline-none focus:ring-2 disabled:opacity-60 bg-primary-600 text-white hover:bg-primary-500 focus:ring-primary-500/50 dark:bg-primary-500 dark:hover:bg-primary-400 dark:focus:ring-primary-400/50"
                                        >
                                            <span wire:loading.remove wire:target="subscribeToPlan">{{ __('forms.actions.subscribe') }}</span>
                                            <span wire:loading wire:target="subscribeToPlan">…</span>
                                        </button>
                                    </div>

                                    @if(!empty($plan->features))
                                        <h6 class="package-features-title mb-3 px-4">{{ __('forms.fields.features') }}</h6>
                                        <ul class="list-unstyled mb-0 px-4">
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
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    @if($plans->isEmpty())
        <div class="text-center py-12 text-gray-500">
            {{ __('panel.messages.no_data') ?? __('site.no_data') }}
        </div>
    @endif
</div>
