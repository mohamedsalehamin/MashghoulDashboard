<div>
    <div class="modal fade custom-bootstrap-modal" id="redeem-modal" tabindex="-1" aria-labelledby="redeemModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header d-flex flex-column align-items-center position-relative pb-0">
                    <button type="button" class="btn-close position-absolute start-0 ms-3 top-0 mt-3" data-bs-dismiss="modal" aria-label="Close" wire:click="closeModal"></button>
                    <h3 class="modal-title mb-2" id="redeemModalLabel">{{ __('site.heading.redeem_points') }}</h3>
                    <p class="modal-subtitle">{{ __('site.heading.redeem_modal_subtitle', ['points' => number_format($totalPoints)]) }}</p>
                </div>
                <div class="modal-body text-start">
                    @if(isset($errors['selectedPlanId']))
                    <div class="alert alert-danger mb-3">{{ $errors['selectedPlanId'][0] }}</div>
                    @endif
                    <div class="tier-options">
                        @foreach($canExchangePlans as $plan)
                        <label class="tier-option">
                            <input type="radio" name="tier" value="{{ $plan->id }}" wire:model="selectedPlanId">
                            <div class="tier-option-content">
                                <span class="tier-option-name">{{ $plan->title }}</span>
                                <span class="tier-option-value">{{ number_format($plan->value) }} = {{ $plan->price instanceof \Cknow\Money\Money ? number_format((float) $plan->price->formatByDecimal(), 0) : number_format((float) $plan->price, 0) }}<i class="icon-saudi_riyal"></i></span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @if($canExchangePlans->isEmpty())
                    <p class="text-muted">{{ __('site.heading.not_enough_points') }}</p>
                    @endif
                </div>
                <div class="modal-footer d-flex flex-row justify-content-center gap-3 pt-0">
                    <button type="button" class="btn btn-green modal-confirm px-5" wire:click="confirmExchange" wire:loading.attr="disabled" @if($canExchangePlans->isEmpty()) disabled @endif>
                        <span wire:loading.remove wire:target="confirmExchange">{{ __('site.buttons.confirm') }}</span>
                        <span wire:loading wire:target="confirmExchange">{{ __('site.buttons.loading') }}</span>
                    </button>
                    <button type="button" class="btn modal-cancel px-5" data-bs-dismiss="modal" wire:click="closeModal">{{ __('site.buttons.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Redeem success modal --}}
    <div class="modal fade custom-bootstrap-modal" id="redeem-success-modal" tabindex="-1" aria-labelledby="redeemSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header d-flex flex-column align-items-center position-relative pb-0">
                    <h3 class="modal-title mb-2" id="redeemSuccessModalLabel">{{ __('site.messages.exchange_success') }}</h3>
                </div>
                <div class="modal-footer d-flex flex-row justify-content-center gap-3 pt-0">
                    <button type="button" class="btn btn-green modal-confirm px-5" data-bs-dismiss="modal" aria-label="Close">{{ __('site.buttons.ok') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('points-redeemed', () => {
        if (typeof bootstrap === 'undefined') return;
        const redeemEl = document.getElementById('redeem-modal');
        const successEl = document.getElementById('redeem-success-modal');
        if (redeemEl) bootstrap.Modal.getInstance(redeemEl)?.hide();
        if (successEl) bootstrap.Modal.getOrCreateInstance(successEl).show();
    });
</script>
@endscript
