<?php

namespace Backstage\Laravel\AI\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Backstage\Laravel\AI\AI
 */
class AI extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Backstage\Laravel\AI\AI::class;
    }
}
