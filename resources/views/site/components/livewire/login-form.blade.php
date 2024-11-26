<div class="form-content">
    <div class="form-group" wire:ignore>
        <label class="form-label"> @lang('site.fields.phone') </label>
        @include('site.components.livewire._phone-input')

    </div>
    <div class="form-group">
        <label class="form-label">  @lang('site.fields.password')</label>
        <div class="password-content">
            <button class="password-toggle">
                <i class="fa-regular fa-eye"></i>
            </button>
            <input type="password" class="form-control" wire:model="password" autocomplete="new-password"/>
        </div>
    </div>
    @error('credentials')
    <p class="text-danger">{{ $message }}</p>
    @enderror
    <a href="{{route('auth.forget-password')}}" class="form-link">
        @lang('site.heading.are_you_forget_password')

    </a>

    <a class="submit-btn main-btn" wire:click="handle" wire:loading.attr="disabled">
        <div wire:loading class="mx-1">
            @include("site.components.loader")
        </div>
        @lang("site.buttons.login")
    </a>
</div>

