<div id="registerForm" class="bs-stepper">
    <div class="steps-header" role="tablist">
        <div class="step @if($step==1) active  @endif" data-target="#step-1">
            <button
                type="button"
                class="step-trigger"
                role="tab"
                aria-controls="step-1"
            >
                <span> 1 </span>
                @lang('site.heading.basic_info')
            </button>
        </div>
        <div class="step @if($step==2) active  @endif" data-target="#step-2">
            <button
                type="button"
                class="step-trigger"
                role="tab"
                aria-controls="step-2"
            >
                <span> 2 </span>
                @lang('site.heading.verification')
            </button>
        </div>
        <div class="step @if($step==3)active  @endif" data-target="#step-3">
            <button
                type="button"
                class="step-trigger"
                role="tab"
                aria-controls="step-3"
            >
                <span> 3 </span>
                @lang('site.heading.account_data')
            </button>
        </div>
        <div class="step @if($step==4)active  @endif" data-target="#step-4">
            <button
                type="button"
                class="step-trigger"
                role="tab"
                aria-controls="step-4"
            >
                <span> 4 </span>
                @lang('site.heading.health_data')
            </button>
        </div>
    </div>
    <div class="bs-stepper-content">
        <form>
            <div id="step-1" role="tabpanel" class="bs-stepper-pane {{$step==1 ?'active':'fade dstepper-none'}}">
                <div class="form-content">

                    <div class="form-group">
                        <div wire:ignore class="w-100">
                            <label class="form-label">@lang("site.fields.phone") </label>
                            @include('site.components.livewire._phone-input')
                        </div>
                        @error('phone')
                        <span class="text-danger"> {{$message}}</span>
                        @enderror

                    </div>
                    <div class="form-group">
                        <div wire:ignore class="w-100">
                            <label class="form-label">@lang("site.fields.id_number") </label>
                            <input type="number"
                                   class="form-control" wire:model="id_number"
                                   />
                        </div>
                        @error('id_number')
                        <span class="text-danger"> {{$message}}</span>
                        @enderror

                    </div>

                    <div class="form-group">
                        <label class="form-label"> @lang('site.fields.password') </label>
                        <div class="password-content">
                            <button class="password-toggle">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                            <input type="password" class="form-control" wire:model="password"
                                   autocomplete="new-password"/>
                        </div>
                        @error('password')
                        <span class="form-error">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label"> @lang('site.fields.password_confirmation') </label>
                        <div class="password-content">
                            <button class="password-toggle">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                            <input type="password" class="form-control" wire:model="password_confirmation"
                                   autocomplete="new-password"/>
                        </div>
                        @error('password_confirmation')
                        <span class="form-error">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" wire:model="terms"/>
                                <span class="mark">
                          <i class="fa-regular fa-check"></i>
                        </span>
                                <span class="text">
                          @lang('site.heading.accept_terms')
                                    <a
                                        href="{{route('pages.show',$pages['terms_and_conditions']->id)}}"
                                    >
                                        @lang('site.heading.terms_and_conditions')
                                    </a>
                        </span>
                            </label>
                        </div>
                        @error('terms')
                        <span class="form-error">{{$message}}</span>
                        @enderror
                    </div>
                    <button
                        type="button"
                        class="submit-btn main-btn"
                        wire:click.prevent="handleStepOne"
                        {{--                        onclick="registerForm.next()"--}}
                    >
                        @lang('site.buttons.continue')
                    </button>
                </div>
            </div>
            <div id="step-2" role="tabpanel" class="bs-stepper-pane  {{$step==2 ?'active':'fade dstepper-none'}}">
                <div class="otp-content">
                    <div id="otp-input" class="otp-input">
                        <input
                            type="number"
                            step="1"
                            min="0"
                            max="9"
                            wire:model="code.1"
                            autocomplete="no"
                            pattern="\d*"
                        />
                        <input
                            type="number"
                            step="1"
                            min="0"
                            max="9"
                            wire:model="code.2"
                            autocomplete="no"
                            pattern="\d*"

                        />
                        <input
                            type="number"
                            step="1"
                            min="0"
                            max="9"
                            wire:model="code.3"
                            autocomplete="no"
                            pattern="\d*"

                        />
                        <input
                            type="number"
                            step="1"
                            min="0"
                            max="9"
                            wire:model="code.4"
                            autocomplete="no"
                            pattern="\d*"

                        />
                    </div>
                    <input type="hidden" name="otp"/>
                    @error('code')
                    <span class="form-error">{{$message}}</span>
                    @enderror
                    <button
                        type="button"
                        class="submit-btn main-btn"
                        wire:click="handleStepTwo"
                    >
                        @lang('site.buttons.continue')
                    </button>
                </div>
            </div>
            <div id="step-3" role="tabpanel" class="bs-stepper-pane  {{$step==3 ?'active':'fade dstepper-none'}}">
                <div class="form-content">
                    <div class="form-group">
                        <label class="form-label"> @lang('site.fields.first_name') </label>
                        <input type="text" class="form-control" wire:model="first_name"/>
                        @error('first_name')
                        <span class="form-error">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label"> @lang('site.fields.last_name') </label>
                        <input type="text" class="form-control" wire:model="last_name"/>
                        @error('last_name')
                        <span class="form-error">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label"> @lang('site.fields.email') </label>
                        <input type="email" class="form-control" wire:model="email"/>
                        @error('email')
                        <span class="form-error">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label"> @lang('site.fields.gender') </label>
                        <div class="radios">
                            @foreach(\App\DefaultPanel\Enum\GenderEnum::cases() as $case)
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="gender" wire:model="gender" value="{{$case->value}}"/>
                                        <span class="mark"> </span>
                                        <span class="text">{{$case->getLabel()}} </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <livewire:flat-picker-date-input wire:model.live="dob" before-today />
                    @error('dob')
                    <span class="form-error"> {{$message}}</span>
                    @enderror
                    <livewire:select2 name="state_id" :options="$states" wire:model.live="state_id" label="{{__('site.fields.state')}}"/>
                    @error('state_id')
                    <span class="form-error"> {{$message}}</span>
                    @enderror
                    <livewire:select2 name="city_id" :options="$cities" wire:model.live="city_id" label="{{__('site.fields.city')}}"/>
                    @error('city_id')
                    <span class="form-error"> {{$message}}</span>
                    @enderror
                    <button
                        type="button"
                        class="submit-btn main-btn"
                        wire:click="stepTree"
                    >
                        @lang('site.buttons.continue')
                    </button>
                </div>
            </div>
            <div id="step-4" role="tabpanel" class="bs-stepper-pane  {{$step==4 ?'active':'fade dstepper-none'}}">
                <div class="reg-tabs-content">
                    <div class="nav account-tabs">
                        <button
                            type="button"
                            class="@if($activeSubStep ==1)active @endif"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-1"
                            wire:click="activeSubStep=1"
                        >
                            @lang('site.heading.basic_info')
                        </button>
                        <button
                            class="@if($activeSubStep ==2)active @endif"
                            type="button"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-2"
                            wire:click="activeSubStep=2"
                        >
                            @lang('site.heading.chronic_diseases')
                        </button>
                        <button
                            class="@if($activeSubStep ==3)active @endif"
                            type="button"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-3"
                            wire:click="activeSubStep=3"
                        >
                            @lang('site.heading.attached_tests')
                        </button>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade @if($activeSubStep ==1)active show @endif" id="tab-1">
                            <div class="account-tab">
                                <div class="form-grid-2">
                                    <div class="form-group">
                                        <label class="form-label"> @lang('site.fields.length') </label>
                                        <input type="text" class="form-control" wire:model="health_data.length"/>
                                        @error('health_data.length')
                                        <span class="form-error"> {{$message}}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label"> @lang('site.fields.blood_type') </label>
                                        <input type="text" class="form-control" wire:model="health_data.blood_type"/>
                                        @error('health_data.blood_type')
                                        <span class="form-error"> {{$message}}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">  @lang('site.fields.sugar') </label>
                                        <input type="text" class="form-control"
                                               wire:model="health_data.blood_sugar_rate"/>
                                        @error('health_data.blood_sugar_rate')
                                        <span class="form-error"> {{$message}}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label"> @lang('site.fields.blood_pressure_rate') </label>
                                        <input type="text" class="form-control"
                                               wire:model="health_data.blood_pressure_rate"/>
                                        @error('health_data.blood_pressure_rate')
                                        <span class="form-error"> {{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="tab-buttons">
                                    <button
                                        type="button"
                                        class="submit-btn main-btn btnNext"
                                        wire:click="continueTo(2)"
                                    >
                                        @lang('site.buttons.continue')
                                    </button>
                                    <a
                                        wire:click="continueTo(2)"
                                        class="submit-btn main-btn finish-btn"
                                    >
                                        @lang('site.buttons.continue_later')
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade @if($activeSubStep ==2)active show @endif" id="tab-2">
                            <div class="account-tab">
                                <div class="checkboxes">
                                    @foreach($diseases as $disease)
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox"

                                                           wire:model="chronic_diseases_ids"
                                                           value="{{$disease->id}}"
                                                    />
                                                    <span class="mark"><i class="fa-regular fa-check"></i></span>
                                                    <span class="text"> {{$disease->name}} </span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                                <div class="tab-buttons">
                                    <button
                                        wire:click="continueTo(3)"
                                        type="button"
                                        class="submit-btn main-btn btnNext"
                                    >
                                        @lang('site.buttons.continue')
                                    </button>
                                    <a
                                        wire:click="continueTo(3)"
                                        class="submit-btn main-btn finish-btn"
                                    >@lang('site.buttons.continue_later')</a>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade @if($activeSubStep ==3)active show @endif" id="tab-3">
                            <div class="account-tab">
                                <div class="form-group">
                                    <label class="file-content">
                                        <input type="file" wire:model="tests_files"/>
                                        <div class="file-placeholder">
                                            <div class="placeholder-icon">
                                                <img
                                                    src="images/icons/upload.svg"
                                                    class="svg"
                                                />
                                            </div>
                                            <h5 class="placeholder-text">
                                                @lang('site.fields.file_text')
                                                <span>@lang('site.fields.download_file')</span>
                                            </h5>
                                        </div>
                                    </label>
                                </div>
                                <div class="files-list">
                                    @foreach($tests_files as $index=>$file)

                                        <div class="file-item">
                                            <div class="file-info">
                                                <h5 class="file-name">
                                                    <a href="{{$file->temporaryUrl()}}" download>
                                                        {{$file->getClientOriginalName()}}
                                                    </a>
                                                </h5>
                                                <span class="file-date">
                                    @lang('site.fields.created_at') {{now()->format('Y-m-d')}}
                              </span>
                                            </div>
                                            <div class="file-tools">
                                                <a href="{{$file->temporaryUrl()}}" download class="file-btn">
                                                    <i class="fa-light fa-download"></i>
                                                </a>
                                                <button
                                                    type="button"
                                                    class="file-btn"
                                                    wire:click="removeAnalysis({{$index}})"
                                                >
                                                    <i class="fa-light fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="tab-buttons">
                                    <a
                                        class="submit-btn main-btn btnNext"
                                        wire:click="complete"
                                    >
                                        @lang('site.buttons.register')
                                    </a>
                                    <a
                                        wire:click="complete"
                                        class="submit-btn main-btn finish-btn"
                                    >
                                        @lang('site.buttons.continue_later')
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('js')
    <script>
        const registerForm = $("#registerForm")

        document.addEventListener('livewire:init', () => {
            registerForm.to(1);
            Livewire.hook('morph.updated', ({el, component}) => {
                registerForm.to('{{$step}}');
            })
            Livewire.on('changeStep', (event) => {
                // registerForm.to(event.step);
                registerForm.next()

            });

        });
    </script>
@endpush
