<?php

namespace Backstage\Laravel\AI;

use Backstage\Laravel\AI\Commands\AICommand;
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
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_ai_table')
            ->hasCommand(AICommand::class);
    }
}
