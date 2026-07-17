<?php

namespace Backstage\Laravel\AI\Contracts;

use Illuminate\Http\Client\Response;

interface HasAgents
{
    /**
     * Create an agent from a config object.
     *
     * @param  array<string, mixed>  $config
     */
    public function createAgent(array $config = []): Response;

    /**
     * Retrieve the config for a single agent.
     */
    public function getAgent(string $agentId): Response;

    /**
     * List agents and their metadata.
     *
     * @param  array<string, mixed>  $query
     */
    public function listAgents(array $query = []): Response;

    /**
     * Patch an agent's settings.
     *
     * @param  array<string, mixed>  $config
     */
    public function updateAgent(string $agentId, array $config = []): Response;

    /**
     * Create a new agent by duplicating an existing one.
     *
     * @param  array<string, mixed>  $config
     */
    public function duplicateAgent(string $agentId, array $config = []): Response;

    /**
     * Get the current shareable link for an agent.
     */
    public function getAgentlink(string $agentId): Response;

    /**
     * Calculate the expected LLM usage for an agent.
     *
     * @param  array<string, mixed>  $config
     */
    public function calculateLlmUsage(string $agentId, array $config = []): Response;

    /**
     * Delete an agent.
     */
    public function deleteAgent(string $agentId): Response;
}
