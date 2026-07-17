<?php

// config for Backstage\Laravel\AI
return [

    /*
    |--------------------------------------------------------------------------
    | ElevenLabs
    |--------------------------------------------------------------------------
    */
    'elevenlabs' => [
        'key' => env('ELEVENLABS_API_KEY'),
        'base_url' => env('ELEVENLABS_BASE_URL', 'https://api.elevenlabs.io/v1'),
        'agent_id' => env('ELEVENLABS_AGENT_ID'),
    ],

];
