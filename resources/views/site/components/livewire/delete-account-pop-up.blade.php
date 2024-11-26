<div>
    <div class="modal fade show " style="display: block" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div>
                <div class="modal fade show " style="display: block" id="cancelModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">

                            <div class="modal-header">
                                <button type="button" class="modal-close" data-bs-dismiss="modal">
                                    <i class="fa-regular fa-xmark"></i>
                                </button>
                            </div>
                            <div class="modal-body">
            <span class="alert-icon remove-icon">
              <i class="fa-light fa-trash-can"></i>
            </span>
                                <h2 class="alert-title">@lang('site.heading.delete_account_text')</h2>
                                <p class="remove-description">
                                    @lang('site.heading.delete_account_description')
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="modal-btn main-btn"
                                        wire:click="handle"
                                >
                                    @lang('site.heading.confirm_delete_account')

                                </button>
                                <button
                                    type="button"
                                    class="modal-btn sec-btn"
                                    wire:click="$dispatch('closeModal')"
                                >
                                   @lang("site.buttons.back")
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>

    </div>
</div>

