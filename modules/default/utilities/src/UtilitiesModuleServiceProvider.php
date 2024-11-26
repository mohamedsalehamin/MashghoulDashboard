<?php

namespace App\UtilitiesModule;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use App\UtilitiesModule\Commands\UtilitiesModuleCommand;

class UtilitiesModuleServiceProvider extends PackageServiceProvider {
    public function configurePackage(Package $package): void {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name('utilities_module');
    }
}
