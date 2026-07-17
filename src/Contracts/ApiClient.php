<?php

namespace Backstage\Laravel\AI\Contracts;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

interface ApiClient
{
    /**
     * @param  array<string, mixed>  $query
     */
    public function get(string $uri, array $query = []): Response;

    /**
     * @param  array<string, mixed>  $data
     */
    public function post(string $uri, array $data = []): Response;

    /**
     * @param  array<string, mixed>  $data
     */
    public function patch(string $uri, array $data = []): Response;

    /**
     * @param  array<string, mixed>  $data
     */
    public function delete(string $uri, array $data = []): Response;

    /**
     * Access the underlying HTTP client for advanced requests (streaming, files, ...).
     */
    public function http(): PendingRequest;
}
