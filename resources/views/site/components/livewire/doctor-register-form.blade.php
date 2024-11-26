<div id="registerForm" class="bs-stepper">
    <div class="steps-header" role="tablist">
        <div class="step @if($step==1)active  @endif" data-target="#step-1">
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
        <div class="step @if($step==2)active  @endif" data-target="#step-2">
            <button
                type="button"
                class="step-trigger"
                role="tab"
                aria-controls="step-2"
            >
                <span> 2 </span>
                @lang('site.heading.otp_verfication')
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
                @lang('site.heading.doctor_data')
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
                @lang('site.heading.clinic_data')
            </button>
        </div>
    </div>
    <div class="bs-stepper-content">
        <form>
            <div id="step-1" role="tabpanel" class="bs-stepper-pane  fade {{$step==1 ?'active':'fade dstepper-none'}}"
            >
                <div class="form-content">
                    <div class="form-group">
                        <label class="form-label"> @lang('site.fields.full_name')<span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" wire:model="full_name"/>
                        @error('full_name')
                        <span class="form-error">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"> @lang('site.fields.email')<span class="text-danger">*</span> </label>
                        <input type="email" class="form-control" wire:model="email"/>
                        @error('email')
                        <span class="form-error">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div wire:ignore class="w-100">
                            <label class="form-label">@lang("site.fields.phone")<span class="text-danger">*</span>
                            </label>
                            @include('site.components.livewire._phone-input')
                        </div>
                        @error('phone')
                        <span class="text-danger"> {{$message}}</span>
                        @enderror

                    </div>
                    <div class="form-group">
                        <label class="form-label"> @lang('site.fields.gender')<span class="text-danger">*</span>
                        </label>
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
                    <div class="form-group">
                        <label class="form-label"> @lang('site.fields.password')<span class="text-danger">*</span>
                        </label>
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
                        <label class="form-label"> @lang('site.fields.password_confirmation')<span
                                class="text-danger">*</span> </label>
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
                                <input type="checkbox" checked/>
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
                        <label class="form-label"> @lang('site.fields.doctor_name') <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model="doctor_name">
                        @error('doctor_name')
                        <span class="form-error">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label"> @lang('site.fields.profile_image') <span class="text-danger">*</span></label>
                        <div class="upload-content">
                            <label class="form-control">
                                <input type="file" accept="image/*" wire:model="doctor_image">
                                @if($doctor_image)
                                    <span class="preview">{{$doctor_image->getClientOriginalName()}}</span>
                                @endif
                                <span class="upload-text"> @lang('site.buttons.add_file') </span>

                            </label>
                        </div>
                        @error('doctor_image')
                        <span class="form-error">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label"> @lang('site.fields.license') <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" wire:model="license">
                        @error('license')
                        <span class="form-error">{{$message}}</span>
                        @enderror
                    </div>
                    <livewire:select2 name="nationality_id" :options="$nationalities" wire:model.live="nationality_id"
                                      label="{{__('site.fields.nationality')}}"/>
                    <div class="form-group">
                        <label class="form-label">@lang('site.fields.doctor_bio') <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" wire:model="bio"></textarea>
                        @error('bio')
                        <span class="form-error">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">@lang('site.fields.current_job')<span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" wire:model="job_title">
                        @error('job_title')
                        <span class="form-error">{{$message}}</span>
                        @enderror
                    </div>
                    <livewire:select2 name="specialization_id" :options="$specializations"
                                      wire:model.live="specialization_id"
                                      label="{{__('site.fields.main_speciality')}}"/>
                    <livewire:select2 name="specialization_ids" multiple="true" :options="$subSpecializations"
                                      wire:model.live="specialization_ids"
                                      label="{{__('site.fields.secondary_specialization')}}"/>


                    <div class="form-group">
                        <label class="form-label"> @lang('site.fields.exp_of_years') </label>
                        <input type="text" class="form-control" wire:model="experience_years">
                        @error('experience_years')
                        <span class="form-error">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label"> @lang('site.fields.certifications') </label>
                        @foreach(range(1,$certifications_count) as $one)
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <input type="text" class="form-control" wire:model="certifications.{{$one}}.name"
                                           placeholder="@lang('site.fields.certification_name')">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                           wire:model="certifications.{{$one}}.university_name"
                                           placeholder="@lang('site.fields.university_name')">
                                </div>
                            </div>
                        @endforeach

                        <button class="add-input main-btn" type="button"
                                wire:click.prevent="incrementCertificationsCount">
                            @lang('site.buttons.add_new_certification')
                        </button>
                    </div>

                    <button type="button" class="submit-btn main-btn"
                            wire:click="handleStepThree"
                    >
                        @lang('site.buttons.continue')
                    </button>
                </div>
            </div>
            <div id="step-4" role="tabpanel" class="bs-stepper-pane  {{$step==4 ?'active':'fade dstepper-none'}}">
                <div class="form-content">
                    <div class="form-group">
                        <label class="form-label"> @lang('site.fields.clinic_images') </label>
                        <div class="form-group">
                            <label class="file-content">
                                <input type="file" accept="image/*" wire:model="clinic_photos">
                                <div class="file-placeholder">
                                    <div class="placeholder-icon">
                                        <img src="images/icons/upload.svg" class="svg">
                                    </div>
                                    <h5 class="placeholder-text">
                                        @lang('site.fields.file_text')
                                        <span>@lang('site.fields.download_file')</span>
                                    </h5>
                                </div>
                            </label>
                        </div>
                        <div class="files-list">
                            @foreach($clinic_photos as $index=>$file)

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
                    </div>
                    <livewire:select2 name="state_id" :options="$states" wire:model.live="state_id"
                                      label="{{__('site.fields.state')}}"/>
                    <livewire:select2 name="city_id" :options="$cities" wire:model.live="city_id"
                                      label="{{__('site.fields.city')}}"/>
                    <a
                        wire:click="complete"
                        class="submit-btn main-btn">
                        @lang('site.buttons.register')
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('js')
    <script>
        $(function () {
            registerForm.to(1);
            Livewire.hook('morph.updated', ({el, component}) => {
                registerForm.to('{{$step}}');
            })

        });
        document.addEventListener('livewire:init', () => {

            Livewire.on('changeStep', (event) => {
                registerForm.to(event.step);
            });

        });


    </script>
@endpush
