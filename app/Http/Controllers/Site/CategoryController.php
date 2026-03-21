<?php

namespace App\Http\Controllers\Site;

use App\ContentModule\Models\Category;
use App\ContentModule\Models\City;
use App\ContentModule\Models\Page;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Settings\LandingSettings;
use App\Http\Controllers\Controller;
use App\UsersModule\Models\Provider;
use MatanYadaev\EloquentSpatial\Objects\Point;

class CategoryController extends Controller
{
    public function index()
    {
        $settings = new GeneralSettings();
        $landingSettings = new LandingSettings();
        $appPages = $landingSettings->content['app_pages'] ?? [];
        $pages = collect($appPages)->mapWithKeys(function ($pageId, $pageName) {
            return [$pageName => Page::find($pageId)];
        })->filter();

        $categories = Category::parent()->enabled()->orderBy('sort')->get();
        $cities = City::enabled()->orderBy('name')->get();

        $loadProviders = request()->filled('q') || request()->filled('city_id') || request()->filled('category');
        $providers = collect();
        if ($loadProviders) {
            $user_location = [
                'lat' => (float) session('user_latitude', request()->get('latitude', 0)),
                'lng' => (float) session('user_longitude', request()->get('longitude', 0)),
            ];
            $point = new Point($user_location['lat'], $user_location['lng']);
            $providers = Provider::enabled()
                ->withoutTrashed()
                ->whereHas('user')
                ->when(request()->filled('q'), fn($q) => $q->where(function ($q) {
                    $q->where('name->ar', 'like', '%' . request('q') . '%')
                        ->orWhere('name->en', 'like', '%' . request('q') . '%');
                }))
                ->when(request()->filled('city_id'), fn($q) => $q->where('city_id', request('city_id')))
                ->when(request()->filled('category'), fn($q) => $q->where('category_id', request('category')))
                ->when(request()->get('sort') == 'rating_desc', fn($q) => $q->withAvg('rate', 'rate')->orderBy('rate_avg_rate', 'desc'))
                ->when(request()->get('sort') == 'rating_asc', fn($q) => $q->withAvg('rate', 'rate')->orderBy('rate_avg_rate', 'asc'))
                ->when(request()->get('sort') == 'date_desc', fn($q) => $q->orderBy('created_at', 'desc'))
                ->when(request()->get('sort') == 'date_asc', fn($q) => $q->orderBy('created_at', 'asc'))
                ->when(! in_array(request()->get('sort'), ['rating_desc', 'rating_asc', 'date_desc', 'date_asc'], true), fn($q) => $q->orderBy('created_at', 'desc'))
                ->withDistanceSphere('location', $point)
                ->get();
        }

        return view('site.new.categories', [
            'settings' => $settings,
            'landingSettings' => $landingSettings,
            'pages' => $pages,
            'categories' => $categories,
            'providers' => $providers,
            'cities' => $cities,
            'category' => null,
        ]);
    }

    public function show(string $category)
    {
        $category = Category::findBySlug($category);

        if (! $category || ! $category->status) {
            abort(404);
        }

        $settings = new GeneralSettings();
        $landingSettings = new LandingSettings();
        $appPages = $landingSettings->content['app_pages'] ?? [];
        $pages = collect($appPages)->mapWithKeys(function ($pageId, $pageName) {
            return [$pageName => Page::find($pageId)];
        })->filter();

        $categories = Category::parent()->enabled()->orderBy('sort')->get();
        $cities = City::enabled()->orderBy('name')->get();

        $user_location = [
            'lat' => (float) session('user_latitude', request()->get('latitude', 0)),
            'lng' => (float) session('user_longitude', request()->get('longitude', 0)),
        ];
        $point = new Point($user_location['lat'], $user_location['lng']);

        $providers = Provider::enabled()
            ->withoutTrashed()
            ->whereHas('user')
            ->where('category_id', $category->id)
            ->when(request()->filled('q'), fn ($q) => $q->where(function ($q) {
                $q->where('name->ar', 'like', '%' . request('q') . '%')
                    ->orWhere('name->en', 'like', '%' . request('q') . '%');
            }))
            ->when(request()->filled('city_id'), fn ($q) => $q->where('city_id', request('city_id')))
            ->when(request()->get('sort') == 'rating_desc', fn ($q) => $q->withAvg('rate', 'rate')->orderBy('rate_avg_rate', 'desc'))
            ->when(request()->get('sort') == 'rating_asc', fn ($q) => $q->withAvg('rate', 'rate')->orderBy('rate_avg_rate', 'asc'))
            ->when(request()->get('sort') == 'date_desc', fn ($q) => $q->orderBy('created_at', 'desc'))
            ->when(request()->get('sort') == 'date_asc', fn ($q) => $q->orderBy('created_at', 'asc'))
            ->when(! in_array(request()->get('sort'), ['rating_desc', 'rating_asc', 'date_desc', 'date_asc'], true), fn ($q) => $q->orderBy('created_at', 'desc'))
            ->withDistanceSphere('location', $point)
            ->paginate(4)
            ->withQueryString();

        return view('site.new.category-show', [
            'settings' => $settings,
            'landingSettings' => $landingSettings,
            'pages' => $pages,
            'categories' => $categories,
            'providers' => $providers,
            'cities' => $cities,
            'category' => $category,
        ]);
    }
}
