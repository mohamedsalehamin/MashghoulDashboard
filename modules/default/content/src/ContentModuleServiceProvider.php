<?php

namespace App\ContentModule;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use App\ReportsModule\Commands\ContentModuleCommand;

class ContentModuleServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('contentmodule')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_contentmodule_table')
            ->hasCommand(ContentModuleCommand::class);
    }
}
