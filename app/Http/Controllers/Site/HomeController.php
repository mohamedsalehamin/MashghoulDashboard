<?php

namespace App\Http\Controllers\Site;

use App\CatalogModule\Models\Reservation\Rate;
use App\ContentModule\Models\Banner;
use App\ContentModule\Models\Category;
use App\ContentModule\Models\Faq;
use App\ContentModule\Models\Page;
use App\ContentModule\Models\Slider;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Settings\LandingSettings;
use App\Http\Controllers\Controller;
use App\UsersModule\Models\Provider;
use MatanYadaev\EloquentSpatial\Objects\Point;

class HomeController extends Controller
{
    public function index()
    {
        $settings = new GeneralSettings;
        $landingSettings = new LandingSettings;
        $appPages = $landingSettings->content['app_pages'] ?? [];
        $pages = collect($appPages)->mapWithKeys(function ($pageId, $pageName) {
            return [$pageName => Page::find($pageId)];
        })->filter();

        $heroBanners = Slider::placement('website')->enabled()->orderBy('sort')->get();
        $categoryBanners = Banner::placement('website')->enabled()->orderBy('sort')->get();
        $categories = Category::parent()->enabled()->orderBy('sort')->get();
        $aboutUs = data_get($landingSettings->content, 'about', []);
        $appDownload = data_get($landingSettings->content, 'app_download', []);
        $faqs = Faq::enabled()->get();
        $testimonials = data_get($landingSettings->content, 'testimonials', []);

        $locationSet = session()->has('location_set')
            && session('location_set') === true
            && session()->has('user_latitude')
            && session()->has('user_longitude');

        $userLat = (float) session('user_latitude', request()->get('latitude', 0));
        $userLng = (float) session('user_longitude', request()->get('longitude', 0));
        $point = new Point($userLat, $userLng);

        $providersTable = (new Provider)->getTable();
        $reservableType = Provider::class;

        $customerAvgSql = '(select avg(`rr`.`rate`) from `reservation_rates` as `rr`
            left join `reservations` as `res` on `rr`.`reservation_id` = `res`.`id`
            where `rr`.`parent_id` is null and `rr`.`is_approved` = 1 and `rr`.`rate` is not null
            and (
                (`res`.`reservable_type` = ? and `res`.`reservable_id` = `'.$providersTable.'`.`id`)
                or (`rr`.`provider_id` = `'.$providersTable.'`.`user_id` and `rr`.`source` = ?)
            ))';

        $nearestProviders = $locationSet
            ? Provider::enabled()
                ->withoutTrashed()
                ->whereHas('user')
                ->withDistanceSphere('location', $point)
                ->orderBy('distance', 'asc')
                ->limit(10)
                ->get()
            : collect();

        $mostRatedProviders = Provider::enabled()
            ->withoutTrashed()
            ->whereHas('user')
            ->withDistanceSphere('location', $point)
            ->orderByRaw($customerAvgSql.' desc', [$reservableType, Rate::SOURCE_MANUAL])
            ->limit(10)
            ->get();

        return view('site.new.home', [
            'settings' => $settings,
            'landingSettings' => $landingSettings,
            'pages' => $pages,
            'heroBanners' => $heroBanners,
            'categoryBanners' => $categoryBanners,
            'categories' => $categories,
            'aboutUs' => $aboutUs,
            'appDownload' => $appDownload,
            'faqs' => $faqs,
            'testimonials' => $testimonials,
            'nearestProviders' => $nearestProviders,
            'mostRatedProviders' => $mostRatedProviders,
        ]);
    }
}
