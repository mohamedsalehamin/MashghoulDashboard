<?php

namespace App\DefaultPanel\Api\V1\Content;


use App\ContentModule\Models\ChronicDisease;
use App\ContentModule\Models\Contact;
use App\ContentModule\Models\Faq;
use App\ContentModule\Models\Page;
use App\ContentModule\Models\Title;
use App\DefaultPanel\Requests\Api\ContactUsRequest;
use App\DefaultPanel\Resources\Api\FaqResource;
use App\DefaultPanel\Resources\Api\PageResource;
use App\DefaultPanel\Resources\Api\PatientChronicDiseasesDataResource;
use App\DefaultPanel\Resources\Api\TitleResource;
use App\DefaultPanel\Settings\GeneralSettings;
use Tasawk\Api\Core;
use Tasawk\Api\Facade\Api;

class ContentServices {
    public function contact(ContactUsRequest $request) {
        Contact::create($request->validated());
        return Api::isOk(__("Message sent successfully"));
    }

    public function page($page, GeneralSettings $settings) {
        $mapper = match ($page) {
            'about' => $settings->app_pages['about_us'],
            'terms' => $settings->app_pages['terms_and_conditions'],
            'privacy' => $settings->app_pages['privacy_policy'],
            'return-policy' => $settings->app_pages['return_policy'],
            default => null,
        };
        return Api::isOk(__("Page information"), PageResource::make(Page::findOrFail($mapper)));
    }

    public function faqs() {
        return Api::isOk(__("Frequently asked questions"), FaqResource::collection(Faq::enabled()->latest()->get()));
    }


}
