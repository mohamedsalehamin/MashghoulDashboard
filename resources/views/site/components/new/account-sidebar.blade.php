<aside class="user-menu">
    <div class="menu-head">
        <div class="user-info">
            @if(site()->user()?->getFirstMedia('avatar'))
            <img src="{{ site()->user()->getFirstMediaUrl('avatar') }}" class="img-fluid" alt>
            @endif
            <div class="user-name">{{ site()->user()->name }}</div>
        </div>
        <button class="user-menu-toggle" aria-label="Toggle Menu">
            <i class="fa-solid fa-chevron-down"></i>
        </button>
    </div>
    <ul class="menu-list list-unstyled">
        <li>
            <a href="{{ route('site.account.info') }}" class="{{ request()->routeIs('site.account.info') ? 'active' : '' }}">{{ __('site.heading.account_info') ?? 'معلومات الحساب' }}</a>
            </a>
        </li>
        <li>
            <a href="{{ route('site.account.notifications') }}" class="{{ request()->routeIs('site.account.notifications') ? 'active' : '' }}">{{ __('site.heading.notifications') ?? 'الإشعارات' }}</a>
            </a>
        </li>
        <li>
            <a href="{{ route('site.account.rewards') }}" class="{{ request()->routeIs('site.account.rewards') ? 'active' : '' }}">{{ __('site.heading.rewards') ?? 'المكافآت' }}</a>
            </a>
        </li>
        <li>
            <a href="{{ route('site.bookings') }}" class="{{ request()->routeIs('site.bookings') ? 'active' : '' }}">{{ __('site.heading.my_bookings') ?? 'حجوزاتي' }}</a>
        </li>
        <li>
            <a href="{{ route('site.account.wallet') }}" class="{{ request()->routeIs('site.account.wallet') ? 'active' : '' }}">{{ __('site.heading.wallet') ?? 'المحفظة' }}</a>
        </li>
        <li class="logout-list-item">
            <button class="btn" type="button" data-bs-toggle="modal"
                data-bs-target="#logout-modal">{{ __('site.heading.logout') ?? 'تسجيل الخروج' }}</button>
        </li>
        <li class="delete-list-item">
            <button class="btn" type="button" data-bs-toggle="modal"
                data-bs-target="#delete-modal">{{ __('site.heading.delete_account') ?? 'حذف الحساب' }}</button>
        </li>

    </ul>
</aside>
@livewire('site.logout-modal')
@livewire('site.delete-account-modal')