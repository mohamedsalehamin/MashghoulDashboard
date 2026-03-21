<div>
    <div class="modal fade custom-bootstrap-modal" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header d-flex flex-column align-items-center position-relative pb-0">
                    <button type="button" class="btn-close position-absolute start-0 ms-3 top-0 mt-3" data-bs-dismiss="modal" aria-label="Close" wire:click="closeWithdrawModal"></button>
                    <h3 class="modal-title mb-2" id="withdrawModalLabel">{{ __('site.heading.withdrawal_request') }}</h3>
                    <p class="modal-subtitle">
                        {{ __('site.messages.withdrawal_transfer_notice') }}
                    </p>
                </div>
                <div class="modal-body text-start mt-4 px-4">
                    <div class="common-form">
                        <div class="mb-3">
                            <label for="withdraw-amount" class="form-label">{{ __('site.fields.amount') }}</label>
                            <input id="withdraw-amount" type="number" step="0.01" min="1" class="form-control {{ isset($withdrawErrors['amount']) ? 'is-invalid' : '' }}" placeholder="{{ __('site.fields.amount') }}" wire:model="amount">
                            @if(isset($withdrawErrors['amount']))
                                <div class="invalid-feedback d-block">{{ $withdrawErrors['amount'][0] }}</div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="withdraw-bank_name" class="form-label">{{ __('site.fields.bank_name') }}</label>
                            <input id="withdraw-bank_name" type="text" class="form-control {{ isset($withdrawErrors['bank_name']) ? 'is-invalid' : '' }}" placeholder="{{ __('site.fields.bank_name') }}" wire:model="bank_name">
                            @if(isset($withdrawErrors['bank_name']))
                                <div class="invalid-feedback d-block">{{ $withdrawErrors['bank_name'][0] }}</div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="withdraw-account_name" class="form-label">{{ __('site.fields.account_name') }}</label>
                            <input id="withdraw-account_name" type="text" class="form-control {{ isset($withdrawErrors['account_name']) ? 'is-invalid' : '' }}" placeholder="{{ __('site.fields.account_name') }}" wire:model="account_name">
                            @if(isset($withdrawErrors['account_name']))
                                <div class="invalid-feedback d-block">{{ $withdrawErrors['account_name'][0] }}</div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="withdraw-account_number" class="form-label">{{ __('site.fields.account_number') }}</label>
                            <input id="withdraw-account_number" type="text" class="form-control {{ isset($withdrawErrors['account_number']) ? 'is-invalid' : '' }}" placeholder="{{ __('site.fields.account_number') }}" wire:model="account_number">
                            @if(isset($withdrawErrors['account_number']))
                                <div class="invalid-feedback d-block">{{ $withdrawErrors['account_number'][0] }}</div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="withdraw-iban" class="form-label">{{ __('site.fields.iban') }}</label>
                            <input id="withdraw-iban" type="text" class="form-control {{ isset($withdrawErrors['iban']) ? 'is-invalid' : '' }}" placeholder="{{ __('site.fields.iban') }}" wire:model="iban">
                            @if(isset($withdrawErrors['iban']))
                                <div class="invalid-feedback d-block">{{ $withdrawErrors['iban'][0] }}</div>
                            @endif
                        </div>
                        <div class="modal-footer d-flex flex-row justify-content-center gap-3 pt-0 pb-4">
                            <button type="button" class="btn btn-green modal-confirm px-5" wire:click="requestWithdrawal" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="requestWithdrawal">{{ __('site.buttons.confirm') }}</span>
                                <span wire:loading wire:target="requestWithdrawal">{{ __('site.buttons.loading') }}</span>
                            </button>
                            <button type="button" class="btn modal-cancel px-5 bg-light" data-bs-dismiss="modal" wire:click="closeWithdrawModal">{{ __('site.buttons.cancel') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Withdrawal success modal --}}
    <div class="modal fade custom-bootstrap-modal" id="withdrawal-success-modal" tabindex="-1" aria-labelledby="withdrawalSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header d-flex flex-column align-items-center position-relative pb-0">
                    <h3 class="modal-title mb-2" id="withdrawalSuccessModalLabel">{{ __('site.messages.withdrawal_request_submitted') }}</h3>
                    <p class="modal-subtitle">{{ __('site.messages.withdrawal_request_success') }}</p>
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
    $wire.on('withdrawal-requested', () => {
        if (typeof bootstrap === 'undefined') return;
        const withdrawEl = document.getElementById('withdrawModal');
        const successEl = document.getElementById('withdrawal-success-modal');
        if (withdrawEl) bootstrap.Modal.getInstance(withdrawEl)?.hide();
        if (successEl) bootstrap.Modal.getOrCreateInstance(successEl).show();
    });

</script>
@endscript
