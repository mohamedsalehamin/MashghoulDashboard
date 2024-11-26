<div class="form-content">
    <div class="form-group">
        <label class="form-label"> @lang("site.fields.new_password") </label>
        <div class="password-content">
            <button class="password-toggle">
                <i class="fa-regular fa-eye"></i>
            </button>
            <input type="password"
                   class="form-control"
                   wire:model="password"
                   placeholder="@lang("site.fields.new_password")"/>
        </div>
        @error('password')
        <p class="text-danger">{{ $message }}</p>
        @enderror
    </div>
    <div class="form-group">
        <label class="form-label">@lang("site.fields.new_password_confirmation") </label>
        <div class="password-content">
            <button class="password-toggle">
                <i class="fa-regular fa-eye"></i>
            </button>
            <input type="password"
                   placeholder="@lang("site.fields.new_password_confirmation")"
                   wire:model="password_confirmation"
                   class="form-control"/>
        </div>
        @error('password_confirmation')
        <p class="text-danger">{{ $message }}</p>
        @enderror
    </div>
    <a class="submit-btn main-btn" wire:click="handle" wire:loading.attr="disabled">
        <div wire:loading class="mx-1">
            @include("site.components.loader")
        </div>
        @lang("site.buttons.reset")
    </a>

</div>


