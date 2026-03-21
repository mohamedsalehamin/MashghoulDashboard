<div>
    @if($successMessage)
        <div class="alert alert-success mb-4" role="alert">{{ $successMessage }}</div>
    @endif

    <form wire:submit.prevent="save" class="contact-form">
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('site.fields.name') }}</label>
            <input type="text" class="form-control" id="name" wire:model="name" placeholder="{{ __('site.fields.name') }}">
            @error('name')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">{{ __('site.fields.phone') }}</label>
            @include('livewire._phone-input')
            @error('phone')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('site.fields.email') }}</label>
            <input type="email" class="form-control" id="email" wire:model="email" placeholder="{{ __('site.fields.email') }}">
            @error('email')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-3">
            <label for="messageType" class="form-label">{{ __('site.fields.contact_type') }}</label>
            <select class="form-select" id="messageType" wire:model="contact_type_id">
                <option value="">{{ __('site.enum.select') }}</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
            @error('contact_type_id')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-3">
            <label for="messageTitle" class="form-label">{{ __('site.fields.message_title') }}</label>
            <input type="text" class="form-control" id="messageTitle" wire:model="title" placeholder="{{ __('site.fields.message_title') ?? 'العنوان' }}">
            @error('title')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-3">
            <label for="messageBody" class="form-label">{{ __('site.fields.message') }}</label>
            <textarea class="form-control" id="messageBody" rows="5" wire:model="message" placeholder="{{ __('site.fields.message') ?? 'أخبرنا بما تريده' }}"></textarea>
            @error('message')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn btn-green w-100" wire:loading.attr="disabled">
            <span wire:loading.class="invisible">{{ __('site.buttons.send')  }}</span>
            <span wire:loading class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </button>
    </form>
</div>

@script
<script>
    $wire.on('resetContactForm', () => {
        document.querySelector('.contact-form')?.reset();
    });
</script>
@endscript
