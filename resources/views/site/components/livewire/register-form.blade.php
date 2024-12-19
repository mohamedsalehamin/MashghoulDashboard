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
                        <label for="full_name">الاسم الاول <span
                                style="color: red;">*</span></label>
                        <input type="text" id="full_name" name="full_name"
                               placeholder="الاسم الاول"
                               wire:model="first_name"
                        >
                        @error('first_name')<p class="text-danger my-0">{{$message}}</p>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label for="last_name">الاسم الاخير <span
                                style="color: red;">*</span></label>
                        <input type="text" id="last_name" name="last_name"
                               placeholder="الاسم الاخير"
                               wire:model="last_name"
                        >
                        @error('last_name')<p class="text-danger">{{$message}}</p>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label for="email">البريد الإلكتروني</label>
                        <input type="email" id="email" name="email"
                               placeholder="example@example.com"
                               wire:model="email"
                        >
                        @error('email')<p class="text-danger">{{$message}}</p>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label for="phone">رقم الجوال <span style="color: red;">*</span></label>
                        <input type="tel" id="phone" name="phone"
                               placeholder="+9665xxxxxxxx"
                               wire:model="phone"
                        >
                        @error('phone')<p class="text-danger">{{$message}}</p>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label for="gender">الجنس</label>
                        <select id="gender" name="gender"
                                wire:model="gender"
                        >
                            <option value="">اختر الجنس</option>
                            <option value="male">ذكر</option>
                            <option value="female">أنثى</option>
                        </select>
                        @error('gender')<p class="text-danger">{{$message}}</p>@enderror

                    </div>
                    <div class="col-lg-6">
                        <label for="password">رقم المرور</label>
                        <input type="password" id="password" name="password"
                               wire:model="password"
                               placeholder="أدخل رقم المرور" >
                        @error('password')<p class="text-danger">{{$message}}</p>@enderror
                    </div>
                    <div class="col-lg-6">
                        <label for="confirm_password">تأكيد رقم المرور</label>
                        <input type="password" id="confirm_password" name="password_confirmation"
                               placeholder="أدخل تأكيد رقم المرور"
                               wire:model="password_confirmation"
                        >
                        @error('password_confirmation')<p class="text-danger">{{$message}}</p>@enderror
                    </div>
                </div>
                <label>
                    <input id="terms" type="checkbox" name="terms"  wire:model="terms">
                    أوافق على
                    @php($page=\App\ContentModule\Models\Page::find($settings->app_pages['terms_and_conditions']))
                    @if($page)
                    <a href="{{route('site.page',$page->slug)}}" target="_blank">الشروط والأحكام</a>
                    @else
                        الشروط والأحكام
                    @endif
                </label>
                @error('terms')<p class="text-danger">{{$message}}</p>@enderror
                <div class="btn  width-50 ml-auto button check_btn text-center mx-auto">
                    <button >
                        <div class="spinner-border spinner-border-sm" role="status" wire:loading>
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        تسجبل

                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
