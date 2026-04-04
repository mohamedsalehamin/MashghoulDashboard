<div>
    @if($completeOrderText)
    <div class="cancellation-alert mb-4">
        {{ $completeOrderText }}
    </div>
    @endif

    {{-- Operator Details --}}
    <div class="row">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="data-card">
                <h3 class="data-card-title">{{ __('site.heading.operator_details') }}</h3>
                <div class="data-list operator-data-list">
                    <div class="data-item">
                        <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.fields.provider_name') }}</span>
                        <span class="value">{{ is_object($provider) ? $provider->getTranslation('name', app()->getLocale()) : ($provider['name'] ?? '') }}</span>
                    </div>
                    <div class="data-item">
                        <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.fields.city') }}</span>
                        <span class="value">{{ is_object($provider) ? ($provider->city?->getTranslation('name', app()->getLocale()) ?? '') : ($provider['city']['name'] ?? '') }}</span>
                    </div>
                    <div class="data-item">
                        <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.chair_name') }}</span>
                        <span class="value">{{ $seat['title'] ?? '' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Services List --}}
        <div class="col-lg-6">
            <div class="data-card">
                <h3 class="data-card-title">{{ __('site.heading.list_of_services') }}</h3>
                <div class="data-list services-data-list">
                    @foreach($cartServices as $service)
                    <div class="data-item">
                        <span class="label"><i class="fa-solid fa-angles-left"></i> {{ $service['name'] ?? '' }} ( {{ $service['duration'] ?? 0 }} {{ __('site.minutes') }} )</span>
                        <span class="price">{{ $service['sale_price'] ?? $service['service_price'] ?? '' }} <i class="icon-saudi_riyal"></i></span>
                    </div>
                    @foreach($service['products'] ?? [] as $product)
                    <div class="data-item">
                        <span class="label"><i class="fa-solid fa-angles-left"></i> {{ $product['name'] ?? '' }} x{{ $product['quantity'] ?? 1 }}</span>
                        <span class="price">{{ $product['total_price'] ?? '' }} <i class="icon-saudi_riyal"></i></span>
                    </div>
                    @endforeach
                    @endforeach
                </div>
                <div class="total-duration-row">
                    <span class="label">{{ __('site.heading.total_execution_time') }}</span>
                    <span class="value">{{ $this->totalDuration }} {{ __('site.minutes') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Date & Time Selection --}}
    <div class="booking-section mt-5">
        <h3 class="data-card-title mb-4">{{ __('site.heading.reservation_time') }}</h3>
        <p class="text-muted mb-3">{{ __('site.heading.choose_date_time') }}</p>
        <div class="d-flex gap-2 mb-4 overflow-auto pb-2">
            @foreach($nextDays as $day)
            <button type="button" class="btn d-flex flex-column align-items-center justify-content-center {{ $date === $day['date'] ? 'btn-blue' : 'btn-outline-secondary' }} px-3 py-2 rounded-4 {{ $day['disable'] ? 'disabled opacity-50' : '' }}"
                wire:click="$set('date', '{{ $day['date'] }}')"
                {{ $day['disable'] ? 'disabled' : '' }}>
                <div class="small">{{ $day['title'] }}</div>
                <div class="fw-bold">{{ $day['dateText'] }}</div>
            </button>
            @endforeach
        </div>

        @if($date)
        @if($dateLoader)
        <div class="text-center py-4"><span class="spinner-border text-primary"></span></div>
        @elseif(count($availableTimes) > 0)
        <div class="time-slots-grid">
            @foreach($availableTimes as $index => $slot)
            @php $slotFrom = trim($slot['from'] ?? ''); $slotTo = trim($slot['to'] ?? ''); $isReserved = $slot['reserved'] ?? false; @endphp
            <label wire:key="slot-{{ $index }}-{{ $slotFrom }}-{{ $slotTo }}" class="time-slot-btn {{ $isReserved ? 'booked' : '' }}"
                for="time-slot-{{ $index }}"
                @if(!$isReserved) wire:click="selectTime({{ $index }})" @endif>
                <input type="radio" name="time_slot" id="time-slot-{{ $index }}" value="{{ $index }}"
                    wire:model.live="selectedTimeIndex"
                    {{ $isReserved ? 'disabled' : '' }}
                    hidden>
                <span class="time">
                    {{ \Carbon\Carbon::parse($slot['from'] ?? '00:00')->locale(app()->getLocale())->translatedFormat('h:i A') }}
                    -
                    {{ \Carbon\Carbon::parse($slot['to'] ?? '00:00')->locale(app()->getLocale())->translatedFormat('h:i A') }}
                </span>
                @if($isReserved)
                <span class="status-badge">{{ __('site.heading.reserved') }}</span>
                @endif
            </label>
            @endforeach
        </div>
        @else
        <p class="text-muted">{{ __('site.heading.no_slots_available') }}</p>
        @endif
        @endif
    </div>

    {{-- Coupon & Points --}}
    <div class="booking-section mt-5">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="coupon-section">
                    <h3 class="data-card-title mb-4">{{ __('site.heading.discount_coupon') }}</h3>
                    <p class="coupon-hint">{{ __('site.heading.enter_coupon_if_you_have') }}</p>
                    <div class="input-action-wrapper d-flex gap-2">
                        <input type="text" class="form-control" placeholder="{{ __('site.placeholder.enter_code') }}"
                            wire:model="couponCode" {{ $couponApplied ? 'readonly' : '' }}>
                        @if($couponApplied)
                        <button type="button" class="btn btn-outline-secondary action-btn" wire:click="removeCoupon" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="removeCoupon">{{ __('site.buttons.remove') }}</span>
                            <span wire:loading wire:target="removeCoupon"><span class="spinner-border spinner-border-sm"></span></span>
                        </button>
                        @else
                        <button type="button" class="btn btn-blue action-btn" wire:click="applyCoupon" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="applyCoupon">{{ __('site.buttons.apply') }}</span>
                            <span wire:loading wire:target="applyCoupon"><span class="spinner-border spinner-border-sm"></span></span>
                        </button>
                        @endif
                    </div>
                    @if($couponError)
                    <p class="text-danger small mt-1">{{ $couponError }}</p>
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                <div class="loyalty-section">
                    <h3 class="data-card-title mb-4">{{ __('site.heading.loyalty_points') }}</h3>
                    <p class="coupon-hint">{{ __('site.heading.your_points_balance') }} <span class="points fw-bold">{{ $userPointsBalance ?? 0 }}</span> {{ __('site.heading.you_can_pay_with_points') }}</p>
                    <div class="input-action-wrapper d-flex gap-2">
                        <input type="number" class="form-control" placeholder="{{ __('site.placeholder.enter_points') }}" wire:model="points" {{ $pointsApplied ? 'readonly' : '' }}>
                        @if($pointsApplied)
                        <button type="button" class="btn btn-outline-secondary action-btn" wire:click="removePoints" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="removePoints">{{ __('site.buttons.remove') }}</span>
                            <span wire:loading wire:target="removePoints"><span class="spinner-border spinner-border-sm"></span></span>
                        </button>
                        @else
                        <button type="button" class="btn btn-blue action-btn" wire:click="applyPoints" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="applyPoints">{{ __('site.buttons.apply') }}</span>
                            <span wire:loading wire:target="applyPoints"><span class="spinner-border spinner-border-sm"></span></span>
                        </button>
                        @endif
                    </div>
                    @if($pointsError)
                    <p class="text-danger small mt-1">{{ $pointsError }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-6">
                <div class="loyalty-section">
                    <h3 class="data-card-title mb-4">{{ __('site.heading.wallet_balance') }}</h3>
                    <p class="coupon-hint">{{ __('site.heading.your_wallet_balance') }} <span class="points fw-bold">{{ number_format($userWalletBalance ?? 0, 2) }}</span> <i class="icon-saudi_riyal"></i></p>
                    <div class="input-action-wrapper d-flex gap-2">
                        <input type="number" step="0.01" class="form-control" placeholder="{{ __('site.placeholder.enter_wallet') }}" wire:model="wallet" {{ $walletApplied ? 'readonly' : '' }}>
                        @if($walletApplied)
                        <button type="button" class="btn btn-outline-secondary action-btn" wire:click="removeWallet" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="removeWallet">{{ __('site.buttons.remove') }}</span>
                            <span wire:loading wire:target="removeWallet"><span class="spinner-border spinner-border-sm"></span></span>
                        </button>
                        @else
                        <button type="button" class="btn btn-blue action-btn" wire:click="applyWallet" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="applyWallet">{{ __('site.buttons.apply') }}</span>
                            <span wire:loading wire:target="applyWallet"><span class="spinner-border spinner-border-sm"></span></span>
                        </button>
                        @endif
                    </div>
                    @if($walletError)
                    <p class="text-danger small mt-1">{{ $walletError }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Cost Details & Payment --}}
    <div class="booking-section mt-5">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="data-card cost-details">
                    <h3 class="data-card-title mb-4">{{ __('site.heading.cost_details') }}</h3>
                    <div class="data-list">
                        @if(!empty($totals['services_total'] ?? null) && $totals['services_total'] != '0.00')
                        <div class="data-item border-0 p-0">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.total_services') }}</span>
                            <span class="value">{{ $totals['services_total'] ?? '' }} <i class="icon-saudi_riyal"></i></span>
                        </div>
                        @endif
                        @if(!empty($totals['products_total'] ?? null) && $totals['products_total'] != '0.00')
                        <div class="data-item border-0 p-0">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.total_products') }}</span>
                            <span class="value">{{ $totals['products_total'] ?? '' }} <i class="icon-saudi_riyal"></i></span>
                        </div>
                        @endif
                        <div class="data-item border-0 p-0">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.reservation_fees') }}</span>
                            <span class="value">{{ $totals['reservation_fees'] ?? '' }} <i class="icon-saudi_riyal"></i></span>
                        </div>
                        @if(!empty($totals['discount'] ?? null) && $totals['discount'] != '0.00')
                        <div class="data-item border-0 p-0">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.discount_code') }}</span>
                            <span class="value">-{{ $totals['discount'] ?? '' }} <i class="icon-saudi_riyal"></i></span>
                        </div>
                        @endif
                        @if(!empty($totals['points_discount'] ?? null) && $totals['points_discount'] != '0.00')
                        <div class="data-item border-0 p-0">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.points_balance') }}</span>
                            <span class="value">-{{ $totals['points_discount'] ?? '' }} <i class="icon-saudi_riyal"></i></span>
                        </div>
                        @endif
                        @if(!empty($totals['wallet_discount'] ?? null) && $totals['wallet_discount'] != '0.00')
                        <div class="data-item border-0 p-0">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.wallet_discount') }}</span>
                            <span class="value">-{{ $totals['wallet_discount'] ?? '' }} <i class="icon-saudi_riyal"></i></span>
                        </div>
                        @endif
                        @if(!empty($totals['taxes'] ?? null) && $totals['taxes'] != '0.00')
                        <div class="data-item border-0 p-0">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.fields.taxes') }}</span>
                            <span class="value">{{ $totals['taxes'] ?? '' }} <i class="icon-saudi_riyal"></i></span>
                        </div>
                        @endif
                    </div>
                    <div class="total-duration-row">
                        <span class="label">{{ __('site.heading.final_total') }}</span>
                        <span class="value">{{ $totals['total'] ?? '' }} <i class="icon-saudi_riyal"></i></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="label">{{ __('site.heading.points_earned') }}</span>
                        <span class="value">{{ $pointsEarned }} {{ __('site.heading.point') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 offset-lg-1">
                <div class="data-card payment-card">
                    <h3 class="data-card-title mb-4">{{ __('site.heading.payment_method') }}</h3>
                    <p class="text-muted small mb-3">{{ __('site.heading.choose_payment_method') }}</p>
                    <div class="payment-methods">
                        <label class="payment-method-item custom-radio">
                            <input type="radio" name="payment_method" value="myfatoorah" wire:model="paymentMethod">
                            <div class="method-content">
                                <span class="check-circle"></span>
                                <span class="method-name">{{ __('site.heading.online_payment') }}</span>
                                <div class="method-icons">
                                    <img src="{{ asset('assets/site/images/visa.png') }}" alt="Visa">
                                    <img src="{{ asset('assets/site/images/mastercard.png') }}" alt="Mastercard">
                                </div>
                            </div>
                        </label>
                        <label class="payment-method-item custom-radio">
                            <input type="radio" name="payment_method" value="tabby" wire:model="paymentMethod">
                            <div class="method-content">
                                <span class="check-circle"></span>
                                <span class="method-name">{{ __('site.heading.pay_with_tabby') }}</span>
                                <div class="method-icons">
                                    <img src="{{ asset('assets/site/images/tabby.png') }}" alt="Tabby">
                                </div>
                            </div>
                        </label>
                    </div>
                    @if($tabbyError)
                    <p class="text-danger small mt-2">{{ $tabbyError }}</p>
                    @endif
                    <div class="booking-total-row mt-4">
                        <span class="label">{{ __('site.heading.booking_cost') }}</span>
                        <span class="total-price">{{ $totals['total'] ?? '' }} <i class="icon-saudi_riyal"></i></span>
                    </div>
                    <div class="terms-check mt-4">
                        <label class="custom-checkbox">
                            <input type="checkbox" wire:model.live="approveTerms">
                            <span class="checkmark"></span>
                            <span class="text">{{ __('site.heading.agree_to') }} <a href="{{ $termsUrl }}" target="_blank" @click.stop>{{ __('site.heading.terms_and_conditions') }}</a></span>
                        </label>
                    </div>
                    @if($date && $timeFrom && $approveTerms)
                    <button type="button" class="btn btn-green confirm-btn w-100 mt-4" wire:click="checkout" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ __('site.buttons.confirm_reservation') }}</span>
                        <span wire:loading><span class="spinner-border spinner-border-sm"></span> {{ __('site.buttons.processing') }}</span>
                    </button>
                    @else
                    <button type="button" class="btn btn-secondary confirm-btn w-100 mt-4" disabled>
                        @if($date && $timeFrom)
                            {{ __('site.buttons.agree_to_terms_first') }}
                        @else
                            {{ __('site.buttons.select_date_time_first') }}
                        @endif
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
