<?php

namespace Backstage\Laravel\AI\Data;

readonly class CreateAgentResponse
{
    public function __construct(
        public string $agentId,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function from(array $data): self
    {
        return new self(
            agentId: (string) ($data['agent_id'] ?? ''),
        );
    }
}
