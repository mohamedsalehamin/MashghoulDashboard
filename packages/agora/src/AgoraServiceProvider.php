<?php

namespace Packages\Agora;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Packages\Agora\Commands\AgoraCommand;

class AgoraServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('agora')
            ->hasConfigFile()
            ->hasViews();
    }
}
