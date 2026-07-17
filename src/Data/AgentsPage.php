<?php

namespace Backstage\Laravel\AI\Data;

readonly class AgentsPage
{
    /**
     * @param  list<AgentSummary>  $agents
     */
    public function __construct(
        public array $agents,
        public bool $hasMore,
        public ?string $nextCursor,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function from(array $data): self
    {
        return new self(
            agents: array_map(AgentSummary::from(...), $data['agents'] ?? []),
            hasMore: (bool) ($data['has_more'] ?? false),
            nextCursor: $data['next_cursor'] ?? null,
        );
    }
}
