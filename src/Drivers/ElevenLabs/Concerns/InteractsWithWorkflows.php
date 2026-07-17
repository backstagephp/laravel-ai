<?php

namespace Backstage\Laravel\AI\Drivers\ElevenLabs\Concerns;

use Illuminate\Http\Client\Response;

/**
 * ElevenLabs agent workflows.
 *
 * Workflows are not a standalone resource; they live inside the agent config
 * under the `workflow` key and are managed through the agent create/update endpoints.
 *
 * @see https://elevenlabs.io/docs/api-reference/agents/update
 */
trait InteractsWithWorkflows
{
    /**
     * @param  array<string, mixed>  $workflow  AgentWorkflowRequestModel
     */
    public function setAgentWorkflow(string $agentId, array $workflow): Response
    {
        return $this->client->patch("convai/agents/{$agentId}", ['workflow' => $workflow]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getAgentWorkflow(string $agentId): array
    {
        return $this->client->get("convai/agents/{$agentId}")->json('workflow', []);
    }
}
