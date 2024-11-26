<div class="accout-form">
    <form wire:submit.prevent="handle ">
        <div class="account-img">
{{--            @php($avatar=method_exists($avatar,'getUrl')?$avatar?->getUrl():$avatar?->temporaryUrl())--}}
            <img src="{{site()->user()->getFirstMediaUrl()}}" class="img-fluid" />
            <div class="edit-img">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                    width="20px"
                    height="20px"
                >
                    <path
                        fill-rule="evenodd"
                        d="M18.658,5.177 L10.638,13.196 C10.553,13.281 10.446,13.343 10.331,13.375 L6.523,14.429 C6.463,14.446 6.401,14.454 6.338,14.454 C6.154,14.454 5.974,14.380 5.844,14.251 C5.667,14.073 5.598,13.812 5.665,13.571 L6.720,9.765 C6.752,9.649 6.814,9.543 6.899,9.458 L14.919,1.440 C15.348,1.011 15.918,0.775 16.525,0.775 C17.131,0.775 17.701,1.011 18.130,1.440 L18.658,1.967 C19.541,2.852 19.541,4.292 18.658,5.177 ZM7.338,12.757 L8.847,12.339 L7.756,11.249 L7.338,12.757 ZM14.617,3.713 L8.404,9.925 L10.170,11.691 L16.384,5.479 L14.617,3.713 ZM17.672,2.952 L17.144,2.425 C16.803,2.084 16.247,2.084 15.905,2.425 L15.603,2.727 L17.370,4.493 L17.672,4.192 C18.013,3.850 18.013,3.294 17.672,2.952 ZM7.263,3.918 L3.272,3.918 C2.665,3.918 2.171,4.412 2.171,5.019 L2.171,16.730 C2.171,17.336 2.665,17.830 3.272,17.831 L15.886,17.831 C16.492,17.830 16.986,17.336 16.987,16.730 L16.987,12.740 C16.987,12.355 17.299,12.043 17.684,12.043 C18.068,12.043 18.381,12.355 18.381,12.740 L18.381,16.730 C18.379,18.104 17.260,19.223 15.886,19.225 L3.272,19.225 C1.897,19.223 0.778,18.104 0.776,16.731 L0.776,5.018 C0.778,3.644 1.897,2.525 3.271,2.524 L7.263,2.524 C7.648,2.524 7.960,2.836 7.960,3.221 C7.960,3.605 7.648,3.918 7.263,3.918 Z"
                    />
                </svg>
                <input
                    type="file"
                    accept="image/*"
                    wire:model="avatar"
                />
            </div>
        </div>
        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label"> @lang("site.fields.first_name") </label>

                <input type="text" class="form-control" wire:model="first_name"/>
                @error('first_name')
                <span class="text-danger"> {{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label"> @lang("site.fields.last_name") </label>
                <input type="text" class="form-control" wire:model="last_name"/>
                @error('last_name')
                <p class="text-danger"> {{$message}}</p>
                @enderror
            </div>
            <div class="form-group">
                    <label class="form-label">@lang("site.fields.id_number") </label>
                    <input type="text" class="form-control" wire:model="id_number"/>
                    @error('id_number')
                    <p class="text-danger"> {{$message}}</p>
                    @enderror
                @error('phone')
                <span class="text-danger"> {{$message}}</span>
                @enderror

            </div>
            <div class="form-group">
                <div wire:ignore>
                    <label class="form-label">@lang("site.fields.phone") </label>
                    @include('site.components.livewire._phone-input')
                </div>
                @error('phone')
                <span class="text-danger"> {{$message}}</span>
                @enderror

            </div>
            <div class="form-group">
                <label class="form-label"> @lang("site.fields.email") </label>
                <input
                    type="email"
                    class="form-control"
                    wire:model="email"
                />
                @error('email')
                <span class="text-danger"> {{$message}}</span>
                @enderror
            </div>


            <livewire:select2 name="state_id" :options="$states" wire:model.live="state_id" label="{{__('site.fields.state')}}"/>
            <livewire:select2 name="city_id" :options="$cities" wire:model.live="city_id" label="{{__('site.fields.city')}}"/>
            <div class="form-group">
                <label class="form-label"> @lang('site.fields.dob') </label>
                <input
                    type="text"
                    class="form-control"
                    wire:model="dob"
                    disabled
                    readonly
                />
                @error('dob')
                <span class="text-danger"> {{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label"> @lang('site.fields.gender') </label>
                <div class="radios">
                    <div class="radio">
                        <label>
                            <input
                                type="radio"
                                name="gender"
                                @if($gender =='male')checked @endif
                                disabled
                            />
                            <span class="mark"> </span>
                            <span class="text"> @lang('site.enum.male') </span>
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="gender" disabled @if($gender =='female')checked @endif/>
                            <span class="mark"> </span>
                            <span class="text"> @lang('site.enum.female') </span>
                        </label>
                    </div>
                </div>
                @error('gender')
                <span class="text-danger"> {{$message}}</span>
                @enderror
            </div>
        </div>
        <button class="submit-btn main-btn">
            @lang('site.buttons.save_changes')
        </button>
    </form>
</div>

