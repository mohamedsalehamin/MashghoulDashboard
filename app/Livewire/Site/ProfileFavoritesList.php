<?php

namespace App\Livewire\Site;

use App\UsersModule\Models\Provider;
use Livewire\Component;
use Livewire\WithPagination;

class ProfileFavoritesList extends Component
{
    use WithPagination;

    public function render()
    {
        $locale = app()->getLocale();
        $assetBase = asset('assets/site');
        $providers = site()->user()->favorite(Provider::class)->paginate(15)
            ->withPath(route('site.favorites'));

        return view('livewire.site.profile-favorites-list', [
            'providers' => $providers,
            'locale' => $locale,
            'assetBase' => $assetBase,
        ]);
    }
}
