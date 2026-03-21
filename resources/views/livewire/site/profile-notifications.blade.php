<div class="notifications-wrapper">
    @foreach($notifications as $notification)
    @php
        $notificationUrl = $notification->site_url;
    @endphp
    @if($notificationUrl)
        <a href="{{ $notificationUrl }}" class="single-notification white-wrapper mb-3 py-3 px-4 d-flex justify-content-between align-items-center gap-2 text-decoration-none text-body">
    @else
        <div class="single-notification white-wrapper mb-3 py-3 px-4 d-flex justify-content-between align-items-center gap-2">
    @endif
        <div class="title d-flex align-items-center gap-2">
            <img src="{{ asset('assets/site/images/icon.png') }}" alt>
            <p class="m-0 fz18">{{ $notification->title }}</p>
        </div>
        <div class="date">
            {{ $notification->created_at->format('H:i - d\ M\ Y') }}
        </div>
    @if($notificationUrl)
        </a>
    @else
        </div>
    @endif
    @endforeach
    @if($notifications->hasPages())
        <div class="mt-4">
            {{ $notifications->links('vendor.pagination.categories') }}
        </div>
    @endif
</div>