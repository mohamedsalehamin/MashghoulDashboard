<?php

namespace App\Http\Controllers;


use App\ContentModule\Models\Page;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Settings\LandingSettings;

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
}
