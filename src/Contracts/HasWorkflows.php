<?php

namespace Backstage\Laravel\AI\Contracts;

use Illuminate\Http\Client\Response;

interface HasWorkflows
{
    /**
     * Attach or replace the workflow on an agent.
     *
     * @param  array<string, mixed>  $workflow  AgentWorkflowRequestModel (nodes, edges, prevent_subagent_loops)
     */
    public function setAgentWorkflow(string $agentId, array $workflow): Response;

    /**
     * Get the workflow currently configured on an agent.
     *
     * @return array<string, mixed>
     */
    public function getAgentWorkflow(string $agentId): array;
}
