<div class="account-body">
    <div class="notifications-head">
        <h2 class="account-title">@lang("site.heading.notifications")</h2>

        <button class="notifications-btn "
                wire:click="deleteAll"
                @if(!auth()->guard('site')->user()->notifications()->count())
                    style="cursor: not-allowed"
            @endif


        >
            <i class="fa-light fa-trash-can"></i>
            <span class="text"> @lang('site.buttons.delete_all') </span>
        </button>
    </div>
    <div class="accout-notifications">
        @forelse(auth()->guard('site')->user()->notifications()->latest()->get() as $notification)

            <a
                @if(isset($notification->data['viewData']['entity_type']))
                    href="{{$notification->data['viewData']['entity_type']??'' =='reservation'?route('profile.reservations.show',$notification->data['viewData']['entity_id']):'javascript:void(0)'}}"
                @endif
                class="notification-item"
                target="_blank"
            >
                <div class="notification-icon">
                    <img src="images/icons/notification.svg" class="svg"/>
                </div>
                <div class="notification-info">
                    <p class="notification-title">
                        {{$notification->body}}
                    </p>
                    <span class="notification-date">{{$notification->created_at->format("Y/m/d - H:i")}} </span>
                    <span class="notification-date"></span>
                </div>
            </a>
        @empty
            @lang('site.no_data')
        @endforelse
    </div>
</div>
