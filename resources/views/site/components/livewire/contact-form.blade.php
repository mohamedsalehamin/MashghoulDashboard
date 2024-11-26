<div>

    <div class="contact-form">
        @if(session()->has('success'))
        <div class="alert alert-success" role="alert">
            {{session()->get('success')}}
        </div>
        @endif
        <h3 class="contact-title">@lang('site.heading.contact_with_us')</h3>
        <form wire:submit.prevent="handle">
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label"> @lang('site.fields.full_name') </label>
                    <input type="text" class="form-control" wire:model="name"/>
                    @error('name')
                    <span class="form-error"> {{$message}}  </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label"> @lang('site.fields.phone') </label>
                    <input
                        type="text"
                        class="form-control"
                        placeholder="5xxxxxxxx"
                        wire:model="phone"
                    />
                    @error('phone')
                    <span class="form-error"> {{$message}}  </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label"> @lang('site.fields.email')</label>
                    <input type="email" class="form-control" wire:model="email"/>
                    @error('email')
                    <span class="form-error"> {{$message}}  </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">@lang('site.fields.subject') </label>
                    <input type="text" class="form-control" wire:model="title"/>
                    @error('title')
                    <span class="form-error"> {{$message}}  </span>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label"> @lang('site.fields.message') </label>
                <textarea class="form-control" wire:model="message"></textarea>
                @error('message')
                <span class="form-error"> {{$message}}  </span>
                @enderror
            </div>
            <button class="contact-btn main-btn">
                @lang('site.buttons.send')
            </button>
        </form>

    </div>
</div>

