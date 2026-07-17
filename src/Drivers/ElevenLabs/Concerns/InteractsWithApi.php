<?php

namespace Backstage\Laravel\AI\Drivers\ElevenLabs\Concerns;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

trait InteractsWithApi
{
    protected PendingRequest $http;

    /**
     * @param  array<string, mixed>  $query
     */
    public function get(string $uri, array $query = []): Response
    {
        return $this->http->get($uri, $query);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function post(string $uri, array $data = []): Response
    {
        return $this->http->post($uri, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function patch(string $uri, array $data = []): Response
    {
        return $this->http->patch($uri, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function delete(string $uri, array $data = []): Response
    {
        return $this->http->delete($uri, $data);
    }

    /**
     * Access the underlying HTTP client for advanced requests (streaming, files, ...).
     */
    public function http(): PendingRequest
    {
        return $this->http;
    }
}
