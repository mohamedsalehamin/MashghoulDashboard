<div class="shopping_table">
    <div class="join_form">
        @session("message")
        <div class="alert alert-success" role="alert">
            {{session()->get('message')}}
        </div>

        @endsession
        <form  method="POST" wire:submit.prevent="submit">
            <div class="shopping-step shopping-step-active">
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <label for="full_name">@lang("site.fields.first_name") <span
                                style="color: red;">*</span></label>
                        <input type="text" id="full_name" name="full_name"
                               placeholder="@lang("site.fields.first_name")"
                               wire:model="first_name"
                        >
                        @error('first_name')<p class="text-danger my-0">{{$message}}</p>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label for="last_name">@lang("site.fields.last_name") <span
                                style="color: red;">*</span></label>
                        <input type="text" id="last_name" name="last_name"
                               placeholder="@lang("site.fields.first_name") "
                               wire:model="last_name"
                        >
                        @error('last_name')<p class="text-danger">{{$message}}</p>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label for="email">@lang("site.fields.email")</label>
                        <input type="email" id="email" name="email"
                               placeholder="example@example.com"
                               wire:model="email"
                        >
                        @error('email')<p class="text-danger">{{$message}}</p>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label for="phone">@lang("site.fields.phone") <span style="color: red;">*</span></label>
                        <input type="tel" id="phone" name="phone"
                               placeholder="+9665xxxxxxxx"
                               wire:model="phone"
                        >
                        @error('phone')<p class="text-danger">{{$message}}</p>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label for="gender">@lang("site.fields.gender")</label>
                        <select id="gender" name="gender"
                                wire:model="gender"
                        >
                            <option value="">@lang("site.fields.choose")</option>
                            <option value="male">@lang("site.enum.male")</option>
                            <option value="female">@lang("site.enum.female")</option>
                        </select>
                        @error('gender')<p class="text-danger">{{$message}}</p>@enderror

                    </div>
                    <div class="col-lg-6">
                        <label for="password">@lang("site.fields.password")</label>
                        <input type="password" id="password" name="password"
                               wire:model="password"
                               placeholder="@lang("site.fields.password")" >
                        @error('password')<p class="text-danger">{{$message}}</p>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label for="confirm_password">@lang("site.fields.password_confirmation")</label>
                        <input type="password" id="confirm_password" name="password_confirmation"
                               placeholder="@lang("site.fields.password_confirmation")"
                               wire:model="password_confirmation"
                        >
                        @error('password_confirmation')<p class="text-danger">{{$message}}</p>@enderror
                    </div>
                </div>
                <label>
                    <input id="terms" type="checkbox" name="terms"  wire:model="terms">
                    @lang("site.heading.accept_terms")
                    @php($page=\App\ContentModule\Models\Page::find($settings->app_pages['terms_and_conditions']))
                    @if($page)
                    <a href="{{route('site.page',$page->slug)}}" target="_blank">@lang("site.heading.terms_and_conditions")</a>
                    @else
                        @lang("site.heading.terms_and_conditions")
                    @endif
                </label>
                @error('terms')<p class="text-danger">{{$message}}</p>@enderror
                <div class="btn  width-50 ml-auto button check_btn text-center mx-auto">
                    <button >
                        <div class="spinner-border spinner-border-sm" role="status" wire:loading>
                            <span class="visually-hidden">Loading...</span>
                        </div>

                        @lang("site.buttons.register")
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
