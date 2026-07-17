<?php

namespace Backstage\Laravel\AI\Drivers\ElevenLabs\Concerns;

use Illuminate\Http\Client\Response;

/**
 * ElevenLabs Conversational AI agent endpoints.
 *
 * @see https://elevenlabs.io/docs/api-reference/agents
 */
trait InteractsWithAgents
{
    /**
     * @param  array<string, mixed>  $config  Body_Create_Agent_v1_convai_agents_create_post
     */
    public function createAgent(array $config = []): Response
    {
        return $this->client->post('convai/agents/create', $config);
    }

    public function getAgent(string $agentId): Response
    {
        return $this->client->get("convai/agents/{$agentId}");
    }

    /**
     * @param  array<string, mixed>  $query
     */
    public function listAgents(array $query = []): Response
    {
        return $this->client->get('convai/agents', $query);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public function updateAgent(string $agentId, array $config = []): Response
    {
        return $this->client->patch("convai/agents/{$agentId}", $config);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public function duplicateAgent(string $agentId, array $config = []): Response
    {
        return $this->client->post("convai/agents/{$agentId}/duplicate", $config);
    }

    public function getAgentlink(string $agentId): Response
    {
        return $this->client->get("convai/agents/{$agentId}/link");
    }

    /**
     * @param  array<string, mixed>  $config  LLMUsageCalculatorRequestModel
     */
    public function calculateLlmUsage(string $agentId, array $config = []): Response
    {
        return $this->client->post("convai/agent/{$agentId}/llm-usage/calculate", $config);
    }

    public function deleteAgent(string $agentId): Response
    {
        return $this->client->delete("convai/agents/{$agentId}");
    }
}
