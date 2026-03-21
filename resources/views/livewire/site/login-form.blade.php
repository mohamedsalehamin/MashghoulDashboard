<div>
    @if(!$otpSent)
        <form wire:submit="sendOtp" class="login-main-form">
            <div class="form-group mb-4">
                @include('livewire._phone-input')
                @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-green w-100 mb-4 d-flex justify-content-center align-items-center auth-btn">
                {{ __('site.heading.login')  }}
            </button>
        </form>
    @else
        <form wire:submit="verifyAndLogin" class="login-main-form">
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
                {{ __('site.buttons.confirm')  }}
            </button>
            <button type="button" wire:click="$set('otpSent', false)" class="btn btn-link w-100 text-muted">
                {{ __('site.heading.change_phone')  }}
            </button>
        </form>
    @endif
</div>
