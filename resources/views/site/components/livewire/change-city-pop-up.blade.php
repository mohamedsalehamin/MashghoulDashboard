<div>
    <div class="modal fade show " style="display: block" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div>
                <div class="modal fade show " style="display: block" id="cancelModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="modal-title">@lang('site.heading.choose_cities')</h2>
                                <button type="button" class="modal-close"
                                        wire:click="$dispatch('closeModal')"
                                >
                                    <i class="fa-regular fa-xmark"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="city-grid">
                                    @if($step==0)
                                        @foreach($countries as $id=>$country)
                                            <div class="city-item">
                                                <input type="radio" name="country_id"
                                                       wire:model.live="country_id"
                                                       wire:click="updateStep(1)"
                                                       value="{{$id}}">
                                                <div class="city-content">
                                                    <span class="city-mark"> </span>
                                                    <span class="city-name">{{$country}}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    @if($step==1)
                                        @foreach($states as $id=>$state)
                                            <div class="city-item">
                                                <input type="radio" name="country_id" wire:model.live="state_id"
                                                       wire:click="updateStep(2)"
                                                       value="{{$id}}">
                                                <div class="city-content">
                                                    <span class="city-mark"> </span>
                                                    <span class="city-name">{{$state}}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    @if($step==2)
                                        @foreach($cities as $id=>$city)
                                            <div class="city-item">
                                                <input type="radio" name="city" wire:model.live="city_id"
                                                       value="{{$id}}">
                                                <div class="city-content">
                                                    <span class="city-mark"> </span>
                                                    <span class="city-name">{{$city}}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                </div>

                            </div>
                            @if($step==2)
                                <div class="modal-footer">
                                    <button type="button" class="modal-btn main-btn"
                                            wire:click="handle">@lang('site.buttons.save_data')</button>
                                    <button
                                        type="button" class="modal-btn sec-btn"
                                        wire:click="$dispatch('closeModal')"
                                    >
                                        @lang('site.heading.cancel')
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-backdrop fade show"></div>
            </div>

        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
</div>
