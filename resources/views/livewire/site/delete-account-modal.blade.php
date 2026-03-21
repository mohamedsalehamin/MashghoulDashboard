<div>
    <div class="modal fade custom-bootstrap-modal" id="delete-modal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header d-flex flex-column align-items-center position-relative pb-0">
                    <button type="button" class="btn-close position-absolute start-0 ms-3 top-0 mt-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h3 class="modal-title mb-2" id="deleteModalLabel">{{ __('site.heading.delete_account') }}</h3>
                    <p class="modal-subtitle">{{ __('site.heading.delete_account_text') }}</p>
                    @if(__('site.heading.delete_account_description'))
                        <p class="modal-subtitle small text-muted">{{ __('site.heading.delete_account_description') }}</p>
                    @endif
                </div>
                <div class="modal-footer d-flex flex-row justify-content-center gap-3 pt-0">
                    <button type="button" class="btn btn-red modal-confirm px-5" wire:click="deleteAccount">
                        {{ __('site.heading.confirm_delete_account') }}
                    </button>
                    <button type="button" class="btn modal-cancel px-5" data-bs-dismiss="modal">{{ __('site.buttons.cancel')  }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
