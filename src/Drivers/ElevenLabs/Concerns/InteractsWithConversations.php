<?php

namespace Backstage\Laravel\AI\Drivers\ElevenLabs\Concerns;

use Illuminate\Http\Client\Response;

/**
 * ElevenLabs Conversational AI conversation runtime endpoints.
 *
 * Live text/voice turns run over a WebSocket the client opens directly against
 * ElevenLabs; the server's job is to mint a signed URL for that socket and to
 * inspect conversations after the fact.
 *
 * @see https://elevenlabs.io/docs/api-reference/conversations
 */
trait InteractsWithConversations
{
    /**
     * @param  array<string, mixed>  $query
     */
    public function getSignedUrl(string $agentId, array $query = []): Response
    {
        return $this->client->get('convai/conversation/get-signed-url', ['agent_id' => $agentId] + $query);
    }

    /**
     * @param  array<string, mixed>  $query
     */
    public function listConversations(array $query = []): Response
    {
        return $this->client->get('convai/conversations', $query);
    }

    public function getConversation(string $conversationId): Response
    {
        return $this->client->get("convai/conversations/{$conversationId}");
    }

    public function deleteConversation(string $conversationId): Response
    {
        return $this->client->delete("convai/conversations/{$conversationId}");
    }
}
