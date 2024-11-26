<div>
    <div class="modal fade show " style="display: block" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div>
                <div class="modal fade show " style="display: block" id="cancelModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">

                            <div class="modal-header">
                                <button type="button" class="modal-close" wire:click="$dispatch('closeModal')">
                                    <i class="fa-regular fa-xmark"></i>
                                </button>
                            </div>
                            <div class="modal-body">
            <span class="alert-icon">
              <i class="fa-light fa-circle-check"></i>
            </span>
                                <h2 class="alert-title">{{$message}}</h2>
                            </div>
                            <div class="modal-footer">
                                <button
                                    wire:click="$dispatch('closeModal')"
                                    type="button" class="modal-btn main-btn">@lang('site.buttons.continue')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>

    </div>
</div>

