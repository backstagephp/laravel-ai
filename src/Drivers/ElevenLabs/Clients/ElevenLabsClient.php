<?php

namespace Backstage\Laravel\AI\Drivers\ElevenLabs\Clients;

use Backstage\Laravel\AI\Contracts\ApiClient;
use Backstage\Laravel\AI\Drivers\ElevenLabs\Concerns\InteractsWithApi;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ElevenLabsClient implements ApiClient
{
    use InteractsWithApi;

    public function __construct()
    {
        $apiKey = config('ai.elevenlabs.key');

        if (blank($apiKey)) {
            throw new RuntimeException('Missing ElevenLabs API key. Set ELEVENLABS_API_KEY in your environment.');
        }

        $this->http = Http::baseUrl(config('ai.elevenlabs.base_url'))
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'xi-api-key' => $apiKey,
            ]);
    }
}
