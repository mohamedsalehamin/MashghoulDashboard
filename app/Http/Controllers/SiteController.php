<?php

namespace App\Http\Controllers;


use App\ContentModule\Models\Page;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Settings\LandingSettings;

use App\UsersModule\Models\Provider;
use Jenssegers\Agent\Agent;
use Str;

class SiteController extends Controller {
    public function index() {
        $settings = new GeneralSettings();
        $landingSettings = new LandingSettings();
        $pages = collect($settings->app_pages)->mapWithKeys(function ($page, $pageName) {
            return [$pageName => Page::find($page)];

        });
        return view('site.pages.index',[
            'landing_settings' => $landingSettings,
            'settings' => $settings,
            'social_links' => $settings->social_links,
            'locale' => app()->getLocale(),
            'pages' => $pages
        ]);

    }

    public function register() {
        $settings = new GeneralSettings();
        $landingSettings = new LandingSettings();
        $pages = collect($settings->app_pages)->mapWithKeys(function ($page, $pageName) {
            return [$pageName => Page::find($page)];

        });

        return view('site.pages.register',[

                'landing_settings' => $landingSettings,
                'settings' => $settings,
                'social_links' => $settings->social_links,
                'locale' => app()->getLocale(),
                'pages' => $pages
        ]);
}
    public function page($slug) {
        $settings = new GeneralSettings();
        $landingSettings = new LandingSettings();
        $pages = collect($settings->app_pages)->mapWithKeys(function ($page, $pageName) {
            return [$pageName => Page::find($page)];

        });
        $page = Page::where('slug->ar', $slug)
            ->orWhere('slug->en', $slug)
            ->firstOrFail();
        return view('site.pages.page', [
            'landing_settings' => $landingSettings,
            'settings' => $settings,
            'social_links' => $settings->social_links,
            'locale' => app()->getLocale(),
            'pages' => $pages,
            'page' => $page
        ]);
    }
    
    public function share_provider($provider_name) {
        $provider_name = str_replace("&", " ", $provider_name);
        // Find the service provider by slug
        $provider = Provider::where('name->en', $provider_name)
            ->orWhere('name->ar', $provider_name)
            ->firstOrFail();

        if (!$provider) {
            abort(404);
        }

        // Get user agent details
        $agent = new Agent();

        // Get your app's details
        $settings = new GeneralSettings();
        $appScheme = 'mashghoul://';
        $androidAppId = Str::after(data_get($settings->applications_links,'client.google_play_link'), 'id=');
        $iosAppId = Str::after(data_get($settings->applications_links,'client.apple_store_link'), 'id');
        $webUrl = route('site.home');

        // Prepare deep link to provider's page
        $deepLink = $appScheme . "providers/{$provider->id}";
        
        if ($agent->isAndroidOS()) {
            // Intent URL format for Android
            $intentUrl = "intent://providers/{$provider->id}#Intent;scheme=" . str_replace('://', '', $appScheme) . ";package={$androidAppId};S.browser_fallback_url=" . urlencode("https://play.google.com/store/apps/details?id={$androidAppId}") . ";end";
            return redirect($intentUrl);
        }elseif ($agent->isiOS()) {
            // Universal Link or App Store redirect for iOS
            return view('site.pages.redirect-ios', [
                'deepLink' => $deepLink,
                'appStoreUrl' => "https://apps.apple.com/app/id{$iosAppId}"
            ]);
        }else {
            // For web browsers, redirect to the website
            return redirect($webUrl);
        }
    }
}
