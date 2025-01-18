<section id="contact_Us" class="block custom-content-section">
    <div class="single_page">
        <div class="container">

            <h4 class="sec-tit">@lang("site.heading.contact_us")</h4>
            @if($successMessage)
            <div class="alert alert-success" role="alert">
                {{$successMessage}}
            </div>
            @endif

            <div class="join_form">
                <form wire:submit.prevent="save" id="ContactForm">
                    <div class="row mb-3">
                        <div class="col-lg-6 mb-2">
                            <label for="full_name">@lang("site.fields.full_name") <span
                                    style="color: red">*</span></label>
                            <input
                                type="text"
                                id="full_name"
                                name="full_name"
                                wire:model="name"
                                class="mb-0"
                                placeholder="@lang('site.fields.full_name')"

                            />
                            @error('name')
                            <p class="text-danger mt-0">{{$message}}</p>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="phone">@lang('site.fields.phone')<span style="color: red">*</span></label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                wire:model="phone"
                                class="mb-0"
                                placeholder="+9665xxxxxxxx"

                            />
                            @error('phone')
                            <p class="text-danger mt-0">{{$message}}</p>
                            @enderror
                        </div>
                        <div class="col-lg-12 mb-2">
                            <label for="email">@lang('site.fields.email')<span style="color: red">*</span></label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                wire:model="email"
                                class="mb-0"
                                placeholder="example@example.com"
                            />
                            @error('email')
                            <p class="text-danger mt-0">{{$message}}</p>
                            @enderror
                        </div>
                        <div class="col-lg-12 mb-2">
                            <label for="message_type">@lang('site.fields.contact_type') <span
                                    style="color: red">*</span></label>
                            <select id="message_type"
                                    name="message_type"
                                    wire:model="contact_type_id"
                                    class="mb-0" >
                                <option value="">@lang('site.enum.select')</option>
                                @foreach($types as $type)
                                    <option value="{{$type->id}}">{{$type->name}}</option>
                                @endforeach
                            </select>
                            @error('contact_type_id')
                            <p class="text-danger mt-0">{{$message}}</p>
                            @enderror
                        </div>
                        <div class="col-lg-12 mb-2">
                            <label for="message_subject">@lang('site.fields.message_title') <span
                                    style="color: red">*</span></label>
                            <input
                                class="mb-0"
                                type="text"
                                id="message_subject"
                                name="message_subject"
                                wire:model="title"
                                placeholder="@lang('site.fields.message_title')"
                            />
                            @error('title')
                            <p class="text-danger mt-0">{{$message}}</p>
                            @enderror
                        </div>
                        <div class="col-lg-12 mb-2">
                            <label for="message">@lang('site.fields.message')<span style="color: red">*</span></label>
                            <textarea
                                class="mb-0"
                                id="message"
                                name="message"
                                placeholder="@lang('site.fields.message')"
                                wire:model="message"
                            ></textarea>
                            @error('message')
                            <p class="text-danger mt-0">{{$message}}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="btn button">
                        <button type="submit">
                            <div class="spinner-border spinner-border-sm" role="status" wire:loading>
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            @lang('site.buttons.send')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@script
<script>
    $wire.on('resetContactForm', () => {
       $("#ContactForm")[0].reset();
    });
</script>
@endscript
