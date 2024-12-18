<?php

namespace App\DefaultPanel\Api\V1\Customer\Content;


use App\ContentModule\Models\Contact;
use App\ContentModule\Models\ContactType;
use App\ContentModule\Models\Faq;
use App\ContentModule\Models\Level;
use App\ContentModule\Models\Page;
use App\ContentModule\Models\Point;
use App\DefaultPanel\Requests\Api\Customer\ContactUsRequest;
use App\DefaultPanel\Resources\Api\Customer\ContactTypeResource;
use App\DefaultPanel\Resources\Api\Customer\FaqResource;
use App\DefaultPanel\Resources\Api\Customer\PageResource;
use App\DefaultPanel\Resources\Api\Customer\PointResource;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Models\PointsExchange;
use Tasawk\Api\Facade\Api;

class ContentServices {
    public function contact(ContactUsRequest $request) {
        Contact::create($request->validated());
        return Api::isOk(__("Message sent successfully"));
    }

    public function types() {
        return Api::isOk(__("Contact types"), ContactTypeResource::collection(ContactType::enabled()->paginate()));
}
    public function page($page, GeneralSettings $settings) {
        $pages = request()->get('target') == 'provider' ? $settings->provider_pages : $settings->app_pages;
        $mapper = match ($page) {
            'about' => $pages['about_us'] ?? 0,
            'terms' => $pages['terms_and_conditions'] ?? 0,
            'privacy' => $pages['privacy_policy'] ?? 0,
            'return-policy' => $pages['return_policy'] ?? 0,
            default => null,
        };
        return Api::isOk(__("Page information"), PageResource::make(Page::findOrFail($mapper)));
    }

    public function faqs() {
        return Api::isOk(__("Frequently asked questions"), FaqResource::collection(Faq::enabled()->latest()->get()));
    }

    public function points() {
        return Api::isOk(__("Points"), PointResource::collection(Level::enabled()->latest()->get()));
    }



}
