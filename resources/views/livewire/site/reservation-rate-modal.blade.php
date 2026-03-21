<div class="modal fade custom-bootstrap-modal" id="rating-modal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-center position-relative">
                <h3 class="modal-title w-100 text-center" id="ratingModalLabel">{{ __('site.heading.rate_order') ?? 'تقييم الطلب' }}</h3>
                <button type="button" class="btn-close position-absolute start-0 ms-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($error)
                <div class="alert alert-danger">{{ $error }}</div>
                @endif
                <form wire:submit.prevent="submit">
                    <div class="rating-group mb-4">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <label class="rating-label">{{ __('site.heading.service') ?? 'الخدمة' }}</label>
                            <div class="star-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $serviceRating >= $i ? 'star-active' : '' }}" data-value="{{ $i }}" wire:click="$set('serviceRating', {{ $i }})" style="cursor:pointer"></i>
                                @endfor
                            </div>
                        </div>
                        <input type="text" class="form-control mt-2" placeholder="{{ __('site.placeholder.service_comment') ?? 'اضف ملاحظتك عن الخدمة' }}" wire:model="serviceComment">
                    </div>
                    <div class="rating-group">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <label class="rating-label">{{ __('site.heading.place') ?? 'المكان' }}</label>
                            <div class="star-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $placeRating >= $i ? 'star-active' : '' }}" data-value="{{ $i }}" wire:click="$set('placeRating', {{ $i }})" style="cursor:pointer"></i>
                                @endfor
                            </div>
                        </div>
                        <input type="text" class="form-control mt-2" placeholder="{{ __('site.placeholder.place_comment') ?? 'اضف ملاحظتك عن المكان' }}" wire:model="placeComment">
                    </div>
                    <div class="modal-footer d-flex flex-row justify-content-center gap-3 mt-4">
                        <button type="submit" class="btn btn-green modal-confirm px-5" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ __('site.buttons.confirm') ?? 'تأكيد' }}</span>
                            <span wire:loading><span class="spinner-border spinner-border-sm"></span></span>
                        </button>
                        <button type="button" class="btn modal-cancel px-5" data-bs-dismiss="modal">{{ __('site.buttons.cancel') ?? 'إلغاء' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
