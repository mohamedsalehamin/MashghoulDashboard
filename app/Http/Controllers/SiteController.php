<?php

namespace App\Http\Controllers;


use App\ContentModule\Models\Page;

class SiteController extends Controller {
    public function page($slug) {
        $page = Page::where('slug->ar', $slug)
            ->orWhere('slug->en', $slug)
            ->firstOrFail();
        return view('site.pages.page', compact('page'));
    }
}
