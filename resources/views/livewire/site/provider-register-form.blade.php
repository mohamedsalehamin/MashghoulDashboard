@php
    $settings = app(\App\DefaultPanel\Settings\GeneralSettings::class);
    $providerPanelLoginUrl = \Filament\Facades\Filament::getPanel('lab-panel')->getLoginUrl();
@endphp
<div>
    <form wire:submit="register" class="login-main-form">
        <div class="row">
            <div class="col-md-6 form-group mb-3">
                <input type="text" wire:model="first_name" class="form-control auth-input @error('first_name') is-invalid @enderror"
                       placeholder="{{ __('site.fields.first_name') }} *">
                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 form-group mb-3">
                <input type="text" wire:model="last_name" class="form-control auth-input @error('last_name') is-invalid @enderror"
                       placeholder="{{ __('site.fields.last_name') }} *">
                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-group mb-3">
            <input type="text" wire:model="salon_name" class="form-control auth-input @error('salon_name') is-invalid @enderror"
                   placeholder="{{ __('site.fields.salon_name') }} *">
            @error('salon_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group mb-3">
            <input type="email" wire:model="email" class="form-control auth-input @error('email') is-invalid @enderror"
                   placeholder="{{ __('site.fields.email') }} *" autocomplete="email">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group mb-4">
            @include('livewire._phone-input')
            @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="form-group mb-3">
            <select wire:model="gender" class="form-select auth-input @error('gender') is-invalid @enderror">
                <option value="male">{{ __('panel.enums.male') }}</option>
                <option value="female">{{ __('panel.enums.female') }}</option>
            </select>
            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row">
            <div class="col-md-6 form-group mb-3">
                <input type="password" wire:model="password" class="form-control auth-input @error('password') is-invalid @enderror"
                       placeholder="{{ __('site.fields.password') }} *" autocomplete="new-password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 form-group mb-3">
                <input type="password" wire:model="password_confirmation" class="form-control auth-input"
                       placeholder="{{ __('site.fields.password_confirmation') }} *" autocomplete="new-password">
            </div>
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

        <div class="form-group mb-3">
            <label class="form-label mb-1">{{ __('menu.categories') }}</label>
            <select wire:model="category_id" class="form-select auth-input @error('category_id') is-invalid @enderror">
                <option value="">{{ __('site.enum.select') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->getTranslation('name', $locale) }}</option>
                @endforeach
            </select>
            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group mb-3">
            <label class="form-label mb-1">{{ __('site.fields.provider_activity') }}</label>
            <select wire:model="provider_activity_id" class="form-select auth-input @error('provider_activity_id') is-invalid @enderror">
                <option value="">{{ __('site.enum.select') }}</option>
                @foreach($providerActivities as $activity)
                    <option value="{{ $activity->id }}">{{ $activity->getTranslation('name', $locale) }}</option>
                @endforeach
            </select>
            @error('provider_activity_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group mb-4">
            <label class="d-flex align-items-start gap-2">
                <input type="checkbox" wire:model="terms" class="mt-1">
                <span>
                    @lang('site.heading.accept_terms')
                    @php($page = \App\ContentModule\Models\Page::find($settings->app_pages['terms_and_conditions'] ?? null))
                    @if($page)
                        <a href="{{ route('site.page', $page->getTranslation('slug', app()->getLocale())) }}" target="_blank" rel="noopener">@lang('site.heading.terms_and_conditions')</a>
                    @else
                        @lang('site.heading.terms_and_conditions')
                    @endif
                </span>
            </label>
            @error('terms')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-green w-100 mb-4 d-flex justify-content-center align-items-center auth-btn" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="register">{{ __('site.buttons.create_provider_account') }}</span>
            <span wire:loading wire:target="register">{{ __('site.buttons.please_wait') }}</span>
        </button>

        <p class="text-center text-muted small mb-0">
            {{ __('site.heading.already_have_account') }}
            <a href="{{ $providerPanelLoginUrl ?? '#' }}">{{ __('site.heading.provider_portal_login') }}</a>
        </p>
    </form>
</div>
