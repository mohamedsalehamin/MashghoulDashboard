<div>
    <div class="modal fade show " style="display: block" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">@lang('site.heading.report_a_problem')</h2>
                    <button type="button"
                            class="modal-close"
                            wire:click="$dispatch('closeModal')"
                    >
                        <i class="fa-regular fa-xmark"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-form">
                        @foreach($reasons as $reason)
                            <div class="form-group">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="reason" wire:model="reason_id" value="{{$reason->id}}"/>
                                        <span class="mark"> </span>
                                        <span class="text"> {{$reason->name}} </span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                        <div class="form-group">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="reason" wire:model="reason_id" value="0"/>
                                    <span class="mark"> </span>
                                    <span class="text"> @lang('site.fields.other_reason') </span>
                                </label>
                            </div>
                            <textarea
                                class="modal-textarea"
                                wire:model="comment"
                                placeholder="@lang('site.heading.write_reason')"
                            ></textarea>
                        </div>


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
