<?php

namespace App\Livewire\Site;

use App\UsersModule\Models\Provider;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class ProfileFavoritesList extends Component
{
    use WithPagination;

    public function render()
    {
        $locale = app()->getLocale();
        $assetBase = asset('assets/site');
        $user = site()->user();

        $favoriteIdsOrdered = $user
            ->favorites()
            ->where('favoriteable_type', Provider::class)
            ->orderByDesc('created_at')
            ->pluck('favoriteable_id');

        if ($favoriteIdsOrdered->isEmpty()) {
            $providers = new LengthAwarePaginator(
                [],
                0,
                15,
                LengthAwarePaginator::resolveCurrentPage(),
                ['path' => route('site.favorites'), 'pageName' => 'page']
            );
        } else {
            $idOrder = $favoriteIdsOrdered->values()->all();
            $providers = Provider::query()
                ->enabled()
                ->withoutTrashed()
                ->whereHas('user')
                ->whereIn('id', $favoriteIdsOrdered)
                ->get()
                ->sortBy(fn (Provider $provider) => array_search($provider->id, $idOrder, true))
                ->values()
                ->paginate(15)
                ->withPath(route('site.favorites'));
        }

        return view('livewire.site.profile-favorites-list', [
            'providers' => $providers,
            'locale' => $locale,
            'assetBase' => $assetBase,
        ]);
    }
}
