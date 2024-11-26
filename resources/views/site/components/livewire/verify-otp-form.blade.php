<div class="form-content">
    <div class="form-group">
        <label class="form-label" for="code"> @lang('site.fields.otp_code') </label>
        <input type="text" wire:model="code" class="form-control" id="code"/>

        @error('code')
        <p class="text-danger">{{ $message }}</p>
        @enderror
    </div>
    <a class="submit-btn main-btn" wire:click="handle"
       wire:loading.attr="disabled"
    >
        <div wire:loading class="mx-1">
            @include("site.components.loader")
        </div>
        @lang('site.buttons.confirm')
    </a>
</div>
