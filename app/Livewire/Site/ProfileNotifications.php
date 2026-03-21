<?php

namespace App\Livewire\Site;

use Livewire\Component;
use Livewire\WithPagination;

class ProfileNotifications extends Component
{
    use WithPagination;

    public function render()
    {
        $notifications = site()->user()->notifications()->paginate(20);
        // Use the route path so pagination links are correct (avoid /ar/account/ar/account/notifications on relative resolution)
        $notifications->withPath(parse_url(route('site.account.notifications'), PHP_URL_PATH));

        return view('livewire.site.profile-notifications', [
            'notifications' => $notifications,
        ]);
    }
}
