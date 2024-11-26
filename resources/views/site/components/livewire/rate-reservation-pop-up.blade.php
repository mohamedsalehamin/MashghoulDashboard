<div>
    <div class="modal fade show " style="display: block" id="rateModel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">@lang('site.heading.order_rate')</h2>
                    <button type="button"
                            class="modal-close"
                            wire:click="$dispatch('closeModal')"
                    >
                        <i class="fa-regular fa-xmark"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-form">
                        <div class="rating-stars">
                            @foreach(range(5,1) as $rate)
                            <input id="rate{{$rate}}" name="rating" type="radio" wire:model="rate" value="{{$rate}}" />
                            <label class="rating-star" for="rate{{$rate}}" title="{{$rate}}">
                                <img src="images/icons/star.svg" class="svg" />
                            </label>
                            @endforeach

                        </div>
                        <textarea
                            class="modal-textarea"
                            wire:model="comment"
                            placeholder="@lang('site.heading.rate_text')"
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <a
                        wire:click.prevent="handle"
                        class="modal-btn main-btn"
                    >
                        @lang('site.buttons.send')
                    </a>
                    <button
                        type="button"
                        class="modal-btn sec-btn"
                        wire:click="$dispatch('closeModal')"
                    >
                        @lang('site.heading.cancel')
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
</div>

