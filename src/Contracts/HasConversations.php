<?php

namespace Backstage\Laravel\AI\Contracts;

use Illuminate\Http\Client\Response;

interface HasConversations
{
    /**
     * Mint a short-lived signed WebSocket URL for a client to start a
     * conversation with an agent without exposing the API key.
     *
     * @param  array<string, mixed>  $query  Optional extras (include_conversation_id, branch_id, environment)
     */
    public function getSignedUrl(string $agentId, array $query = []): Response;

    /**
     * List conversations and their metadata.
     *
     * @param  array<string, mixed>  $query
     */
    public function listConversations(array $query = []): Response;

    /**
     * Retrieve a single conversation, including its transcript.
     */
    public function getConversation(string $conversationId): Response;

    /**
     * Delete a conversation.
     */
    public function deleteConversation(string $conversationId): Response;
}
