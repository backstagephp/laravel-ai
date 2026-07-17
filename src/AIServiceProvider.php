<?php

namespace Backstage\Laravel\AI;

use Backstage\Laravel\AI\Managers\APIManager;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AIServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('ai')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('backstage.laravel-ai.api.client', fn ($app): APIManager => new APIManager($app));
    }
}
