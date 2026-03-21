
@php
    $assetBase = asset('assets/site');
@endphp
<form class="common-form" wire:submit.prevent="save">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="position-relative">
                    @if($avatar)
                        <img src="{{ $avatar->temporaryUrl() }}" class="rounded-circle object-fit-cover" alt="" style="width: 80px; height: 80px;">
                    @elseif($user->getFirstMediaUrl('avatar'))
                        <img src="{{ $user->getFirstMediaUrl('avatar') }}" class="rounded-circle object-fit-cover" alt="" style="width: 80px; height: 80px;">
                    @else
                        <img src="{{ $assetBase }}/images/user.webp" class="rounded-circle object-fit-cover" alt="" style="width: 80px; height: 80px;">
                    @endif
                </div>
                <div>
                    <label class="form-label mb-1">{{ __('site.fields.avatar') ?? 'الصورة الشخصية' }}</label>
                    <input type="file" class="form-control form-control-sm" accept="image/*" wire:model="avatar">
                    @error('avatar')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-3">
                <input type="text" class="form-control auth-input" id="name" placeholder="{{ __('site.fields.name') }}" wire:model="name">
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-3">
                @include('livewire._phone-input')
                @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-3">
                <input type="email" class="form-control auth-input" id="email" placeholder="{{ __('site.fields.email') }}" wire:model="email">
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-3">
                @if($gender)
                    <input type="text" class="form-control auth-input" value="{{ $gender === 'male' ? __('site.enum.male') : __('site.enum.female') }}" readonly disabled>
                    <input type="hidden" wire:model="gender">
                @else
                    <select class="form-select auth-input" name="gender" wire:model="gender">
                        <option value="" selected disabled>{{ __('site.enum.select') }}</option>
                        <option value="male">{{ __('site.enum.male') }}</option>
                        <option value="female">{{ __('site.enum.female') }}</option>
                    </select>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-3">
                <label class="form-label mb-1">{{ __('site.fields.country') }}</label>
                <select class="form-select auth-input" name="country_id" wire:model.live="country_id">
                    <option value="" selected disabled>{{ __('site.enum.select') }}</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->getTranslation('name', $locale ?? app()->getLocale()) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-3">
                <label class="form-label mb-1">{{ __('site.fields.state') }}</label>
                <select class="form-select auth-input" name="region_id" wire:model.live="region_id">
                    <option value="" selected disabled>{{ __('site.enum.select') }}</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}">{{ $region->getTranslation('name', $locale ?? app()->getLocale()) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-3">
                <label class="form-label mb-1">{{ __('site.fields.city') }}</label>
                <select class="form-select auth-input" name="city_id" wire:model="city_id">
                    <option value="" selected disabled>{{ __('site.enum.select') }}</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->getTranslation('name', $locale ?? app()->getLocale()) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-green w-auto fz18">{{ __('site.buttons.save') }}</button>
</form>

{{-- Success modal: shown via JS when profile-updated is dispatched --}}
<div class="modal fade custom-bootstrap-modal" id="profile-success-modal" tabindex="-1" aria-labelledby="profileSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header d-flex flex-column align-items-center position-relative pb-0">
                <h3 class="modal-title mb-2" id="profileSuccessModalLabel">{{ __('site.messages.profile_updated') ?? 'تم تحديث الملف الشخصي' }}</h3>
                <p class="modal-subtitle">{{ __('site.messages.profile_updated_success') ?? 'تم حفظ التغييرات بنجاح.' }}</p>
            </div>
            <div class="modal-footer d-flex flex-row justify-content-center gap-3 pt-0">
                <button type="button" class="btn btn-green modal-confirm px-5" data-bs-dismiss="modal" wire:click="closeSuccessModal">
                    {{ __('site.buttons.ok') ?? 'حسناً' }}
                </button>
            </div>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('profile-updated', () => {
        const el = document.getElementById('profile-success-modal');
        if (el && typeof bootstrap !== 'undefined') {
            bootstrap.Modal.getOrCreateInstance(el).show();
        }
    });
</script>
@endscript