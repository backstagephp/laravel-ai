<?php

use Backstage\Laravel\AI\Data\SignedUrl;
use Backstage\Laravel\AI\Facades\API;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('ai.elevenlabs.key', 'sk_test');
    config()->set('ai.elevenlabs.base_url', 'https://api.elevenlabs.io/v1');
});

it('mints a signed url for an agent', function () {
    Http::fake([
        '*convai/conversation/get-signed-url*' => Http::response(['signed_url' => 'wss://api.elevenlabs.io/v1/convai/conversation?agent_id=agent_1&conversation_signature=xyz']),
    ]);

    $response = API::elevenLabs()->getSignedUrl('agent_1', ['include_conversation_id' => 'true']);

    expect($response->json('signed_url'))->toContain('conversation_signature');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'convai/conversation/get-signed-url')
            && str_contains($request->url(), 'agent_id=agent_1')
            && str_contains($request->url(), 'include_conversation_id=true')
            && $request->method() === 'GET';
    });
});

it('parses a signed url into a DTO', function () {
    $dto = SignedUrl::from(['signed_url' => 'wss://example.test/socket']);

    expect($dto)->toBeInstanceOf(SignedUrl::class)
        ->and($dto->signedUrl)->toBe('wss://example.test/socket');
});

it('defaults the signed url DTO to an empty string when absent', function () {
    expect(SignedUrl::from([])->signedUrl)->toBe('');
});

it('lists conversations', function () {
    Http::fake(['*convai/conversations*' => Http::response(['conversations' => [], 'has_more' => false, 'next_cursor' => null])]);

    API::elevenLabs()->listConversations(['agent_id' => 'agent_1', 'page_size' => 50]);

    Http::assertSent(fn ($request) => str_contains($request->url(), 'convai/conversations')
        && str_contains($request->url(), 'page_size=50')
        && $request->method() === 'GET');
});

it('gets a single conversation', function () {
    Http::fake(['*convai/conversations/conv_1*' => Http::response(['conversation_id' => 'conv_1', 'transcript' => []])]);

    $response = API::elevenLabs()->getConversation('conv_1');

    expect($response->json('conversation_id'))->toBe('conv_1');

    Http::assertSent(fn ($request) => str_ends_with($request->url(), 'convai/conversations/conv_1')
        && $request->method() === 'GET');
});

it('deletes a conversation', function () {
    Http::fake(['*convai/conversations/conv_1*' => Http::response([], 204)]);

    $response = API::elevenLabs()->deleteConversation('conv_1');

    expect($response->status())->toBe(204);

    Http::assertSent(fn ($request) => str_ends_with($request->url(), 'convai/conversations/conv_1')
        && $request->method() === 'DELETE');
});
