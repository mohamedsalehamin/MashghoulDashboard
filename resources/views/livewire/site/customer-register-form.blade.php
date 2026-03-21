@php $locale = app()->getLocale(); @endphp
<div>
    @if(!$otpSent)
        {{-- Step 1: Phone number --}}
        <form wire:submit="sendOtp" class="login-main-form">
            <div class="form-group mb-4">
                @include('livewire._phone-input')
                @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-green w-100 mb-4 d-flex justify-content-center align-items-center auth-btn">
                {{ __('site.heading.send_code') ?? 'إرسال رمز التحقق' }}
            </button>
        </form>
    @elseif(!$otpVerified)
        {{-- Step 2: OTP verification --}}
        <form wire:submit="verifyOtp" class="login-main-form">
            <p class="text-muted small mb-3">{{ __('site.heading.enter_verification_code') }} {{ '+' . ($country_code ?? '966') . preg_replace('/\D/', '', $phone ?? '') }}</p>
            <div class="form-group mb-4">
                <div class="otp-content" dir="ltr">
                    <div class="otp-inputs" id="otp-input" dir="ltr" wire:ignore>
                        <input type="text" autocomplete="off" class="otp-input" maxlength="1"
                               inputmode="numeric" dir="ltr" tabindex="0">
                        <input type="text" autocomplete="off" class="otp-input" maxlength="1"
                               inputmode="numeric" dir="ltr" tabindex="0" disabled>
                        <input type="text" autocomplete="off" class="otp-input" maxlength="1"
                               inputmode="numeric" dir="ltr" tabindex="0" disabled>
                        <input type="text" autocomplete="off" class="otp-input" maxlength="1"
                               inputmode="numeric" dir="ltr" tabindex="0" disabled>
                    </div>
                    <input type="hidden" name="otp" wire:model.live="codeString" id="otp-hidden">
                </div>
                @error('code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-green w-100 mb-4 d-flex justify-content-center align-items-center auth-btn otp-submit-btn" @disabled(strlen($codeString) !== 4)>
                {{ __('site.buttons.confirm') }}
            </button>
            <button type="button" wire:click="$set('otpSent', false)" class="btn btn-link w-100 text-muted">
                {{ __('site.heading.change_phone') ?? 'تغيير الرقم' }}
            </button>
        </form>
    @else
        {{-- Step 3: Data form (name, email, gender, etc.) --}}
        <form wire:submit="register" class="login-main-form">
            <p class="text-muted small mb-3">{{ __('site.heading.complete_your_profile') }}</p>
            <div class="row">
                <div class="col-md-6 form-group mb-3">
                    <input type="text" wire:model="first_name" class="form-control auth-input @error('first_name') is-invalid @enderror"
                           placeholder="{{ __('site.fields.first_name') ?? 'الاسم الأول' }}">
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 form-group mb-3">
                    <input type="text" wire:model="last_name" class="form-control auth-input @error('last_name') is-invalid @enderror"
                           placeholder="{{ __('site.fields.last_name') ?? 'الاسم الأخير' }}">
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group mb-3">
                <input type="email" wire:model="email" class="form-control auth-input @error('email') is-invalid @enderror"
                       placeholder="{{ __('site.fields.email') ?? 'البريد الإلكتروني (اختياري)' }}">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
                <select wire:model="gender" class="form-select auth-input @error('gender') is-invalid @enderror">
                    <option value="male">{{ __('panel.enums.male') ?? 'ذكر' }}</option>
                    <option value="female">{{ __('panel.enums.female') ?? 'أنثى' }}</option>
                </select>
                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
                <label class="form-label mb-1">{{ __('site.fields.country') }}</label>
                <select wire:model.live="country_id" class="form-select auth-input @error('country_id') is-invalid @enderror">
                    <option value="">{{ __('site.enum.select') }}</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->getTranslation('name', $locale) }}</option>
                    @endforeach
                </select>
                @error('country_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
                <label class="form-label mb-1">{{ __('site.fields.state') }}</label>
                <select wire:model.live="state_id" class="form-select auth-input @error('state_id') is-invalid @enderror">
                    <option value="">{{ __('site.enum.select') }}</option>
                    @foreach($states as $state)
                        <option value="{{ $state->id }}">{{ $state->getTranslation('name', $locale) }}</option>
                    @endforeach
                </select>
                @error('state_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
                <label class="form-label mb-1">{{ __('site.fields.city') }}</label>
                <select wire:model="city_id" class="form-select auth-input @error('city_id') is-invalid @enderror">
                    <option value="">{{ __('site.enum.select') }}</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->getTranslation('name', $locale) }}</option>
                    @endforeach
                </select>
                @error('city_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-green w-100 mb-4 d-flex justify-content-center align-items-center auth-btn">
                {{ __('site.heading.register') ?? 'إنشاء حساب' }}
            </button>
        </form>
    @endif
</div>
