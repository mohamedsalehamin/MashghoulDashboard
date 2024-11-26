<div class="account-nav">
    <ul class="account-list">
        <li>
            <a class="main-link has-list active" role="button">
                <i class="fa-light fa-user"></i>
                @lang('site.heading.profile')
            </a>
            <ul class="sub-list" style="display: block">
                <li>
                    <a href="{{route('profile.edit')}}"
                       class="sub-link @if(request()->route()->getName() =='profile.edit') active @endif">
                        @lang('site.heading.edit_account_data')
                    </a>
                </li>
                <li>
                    <a href="{{route('profile.edit-password')}}"
                       class="sub-link @if(request()->route()->getName() =='profile.edit-password') active @endif">
                        @lang('site.heading.edit_password')

                    </a>
                </li>
                <li>
                    <a href="{{route('profile.edit-health-data')}}"
                       class="sub-link @if(request()->route()->getName() =='profile.edit-health-data') active @endif">
                        @lang('site.heading.health_data')

                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a class="main-link @if(request()->route()->getName() =='profile.reservations') active @endif"
               href="{{route('profile.reservations')}}">
                <i class="fa-light fa-list-check"></i>
                @lang('site.heading.my_reservations')

            </a>
        </li>
        <li>
            <a class="main-link @if(request()->route()->getName() =='profile.transactions') active @endif"
               href="{{route('profile.transactions')}}">
                <i class="fa-light fa-rectangle-history"></i>

                @lang('site.heading.transactions')

            </a>
        </li>
        <li>
            <a class="main-link @if(request()->route()->getName() =='profile.favorites') active @endif"
               href="{{route('profile.favorites')}}">
                <i class="fa-light fa-heart"></i>
                @lang('site.heading.my_favorite')
            </a>
        </li>
        <li>
            <a class="main-link @if(request()->route()->getName() =='profile.notifications') active @endif"
               href="{{route('profile.notifications')}}">
                <i class="fa-light fa-bell"></i>
                @lang('site.heading.notifications')
            </a>
        </li>
        <li>
            <button class="main-link p-0" wire:click="logout">
                <i class="fa-light fa-right-from-bracket"></i>
                @lang('site.heading.logout')
            </button>
        </li>

        <li>
            <button class="main-link  p-0" wire:click="$dispatch('openModal', { component: 'delete-account-pop-up' })">
                <i class="fa-light fa-trash-can"></i>
                @lang('site.heading.delete_account')
            </button>
        </li>

    </ul>
    <button class="accountNav-trigger">
        <i class="fa-light fa-user-gear"></i>
    </button>
</div>
