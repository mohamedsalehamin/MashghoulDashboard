<?php

namespace App\Http\Controllers\Site;

use App\ContentModule\Models\CustomerReview;
use App\ContentModule\Models\Faq;
use App\ContentModule\Models\Page;
use App\ContentModule\Models\Post;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Settings\LandingSettings;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    protected function sharedData(): array
    {
        $settings = new GeneralSettings();
        $landingSettings = new LandingSettings();
        $appPages = $landingSettings->content['app_pages'] ?? [];
        $pages = collect($appPages)->mapWithKeys(function ($pageId, $pageName) {
            return [$pageName => Page::find($pageId)];
        })->filter();

        return [
            'settings' => $settings,
            'landingSettings' => $landingSettings,
            'pages' => $pages,
        ];
    }

    public function faqs()
    {
        $faqs = Faq::enabled()->latest()->get();
        return view('site.new.faqs', array_merge($this->sharedData(), ['faqs' => $faqs]));
    }

    public function contact()
    {
        return view('site.new.contact');
    }

    public function join()
    {
        return view('site.new.join-us', $this->sharedData());
    }

    public function joinPaymentFailed()
    {
        return view('site.new.join-payment-failed', $this->sharedData());
    }

    public function page(string $slug)
    {
        $page = Page::where('slug->ar', $slug)->orWhere('slug->en', $slug)->firstOrFail();
        $locale = app()->getLocale();
        $metaKeywords = $page->getTranslation('meta_keywords', $locale);
        $metaKeywords = is_array($metaKeywords) ? implode(', ', $metaKeywords) : ($metaKeywords ?? '');
        $data = array_merge($this->sharedData(), [
            'page' => $page,
            'title' => $page->getTranslation('title', $locale),
            'metaDescription' => $page->getTranslation('meta_description', $locale) ?: Str::limit(strip_tags($page->getTranslation('description', $locale)), 160),
            'metaKeywords' => $metaKeywords,
        ]);
        return view('site.new.page', $data);
    }

    public function blogs()
    {
        $query = Post::enabled();
        $sort = request('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest('publish_date');
        } else {
            $query->latest('publish_date');
        }
        $posts = $query->paginate(12)->withQueryString();
        return view('site.new.blogs', array_merge($this->sharedData(), ['posts' => $posts]));
    }

    public function blogShow(string $slug)
    {
        $locale = app()->getLocale();
        $post = Post::where('slug->ar', $slug)->orWhere('slug->en', $slug)->enabled()->firstOrFail();
        $title = $post->getTranslation('title', $locale);
        $metaDescription = $post->getTranslation('meta_description', $locale)
            ?: Str::limit(strip_tags($post->getTranslation('description', $locale)), 160);
        $metaKeywords = $post->getTranslation('meta_keywords', $locale) ?: '';

        $relatedPosts = Post::enabled()
            ->where('id', '!=', $post->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('site.new.blog-show', array_merge($this->sharedData(), [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'title' => $title,
            'metaDescription' => $metaDescription,
            'metaKeywords' => $metaKeywords,
        ]));
    }

    public function reviews()
    {
        $reviews = CustomerReview::enabled()->latest()->get();
        return view('site.new.reviews', array_merge($this->sharedData(), ['reviews' => $reviews]));
    }

    public function setLocation()
    {
        $savedLat = session('user_latitude');
        $savedLng = session('user_longitude');
        $hasSavedLocation = $savedLat !== null && $savedLng !== null;
        $data = $this->sharedData();
        if ($hasSavedLocation) {
            $data['savedLat'] = (float) $savedLat;
            $data['savedLng'] = (float) $savedLng;
        }
        return view('site.new.set-location', $data);
    }

    public function saveLocation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        session([
            'user_latitude' => (float) $validated['latitude'],
            'user_longitude' => (float) $validated['longitude'],
            'location_set' => true,
        ]);

        $intended = session('intended_url');
        session()->forget('intended_url');

        return redirect()->to($intended ?? route('site.home'));
    }
}
