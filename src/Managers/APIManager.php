<?php

namespace Backstage\Laravel\AI\Managers;

use Backstage\Laravel\AI\Drivers\ElevenLabs\ElevenLabsDriver;
use Backstage\Laravel\AI\Support\Manager;

class APIManager extends Manager
{
    public function createElevenLabsDriver(): ElevenLabsDriver
    {
        return $this->container->make(ElevenLabsDriver::class);
    }
}
