<?php

namespace Tasawk\Api;

use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider {
    public function boot() {
        if (request()->acceptsJson()) {
            $acceptLanguage = request()->header('accept-language');
            if (request()->acceptsJson() && $acceptLanguage && in_array($acceptLanguage, ['ar','en'])) {
                app()->setLocale($acceptLanguage);
            }
        }

        include __DIR__ . "/Helpers/Helper.php";
    }

    public function register() {
        $this->app->bind('api', function () {
            return new Core();
        });
    }
}
