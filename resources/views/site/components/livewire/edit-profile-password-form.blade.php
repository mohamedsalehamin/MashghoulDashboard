<div class="account-body">
    <h2 class="account-title">@lang("site.heading.edit_account_data")</h2>
    <div class="accout-form">
        <form wire:submit.prevent="handle">
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label"> @lang("site.fields.current_password") </label>
                    <div class="password-content">
                        <button class="password-toggle">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                        <input type="password" class="form-control" wire:model="current_password"/>
                    </div>
                    @error('current_password')
                    <span class="form-error">{{$message}}</span>
                    @enderror

                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label"> @lang("site.fields.new_password") </label>
                    <div class="password-content">
                        <button class="password-toggle">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                        <input type="password" class="form-control" wire:model="password"/>
                    </div>
                    @error('password')
                    <span class="form-error">{{$message}}</span>
                    @enderror
                </div>

            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">
                        @lang("site.fields.new_password_confirmation")
                    </label>
                    <div class="password-content">
                        <button class="password-toggle">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                        <input type="password" class="form-control" wire:model="password_confirmation"/>
                    </div>
                    @error('new_password_confirmation')
                    <span class="form-error">{{$message}}</span>
                    @enderror
                </div>

            </div>
            <button type="submit" class="submit-btn main-btn">
                @lang("site.buttons.save_changes")
            </button>
        </form>
    </div>
</div>
