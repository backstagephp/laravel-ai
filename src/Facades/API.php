<?php

namespace Backstage\Laravel\AI\Facades;

use Backstage\Laravel\AI\Drivers\ElevenLabs\ElevenLabsDriver;
use Backstage\Laravel\AI\Managers\APIManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ElevenLabsDriver elevenLabs()
 * @method static mixed driver(string|null $driver = null)
 *
 * @see APIManager
 */
class API extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'backstage.laravel-ai.api.client';
    }
}
