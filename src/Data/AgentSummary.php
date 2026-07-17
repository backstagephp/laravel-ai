<?php

namespace Backstage\Laravel\AI\Data;

readonly class AgentSummary
{
    /**
     * @param  list<string>  $tags
     */
    public function __construct(
        public string $agentId,
        public string $name,
        public array $tags,
        public int $createdAtUnixSecs,
        public bool $archived,
        public ?int $lastCallTimeUnixSecs = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function from(array $data): self
    {
        return new self(
            agentId: (string) ($data['agent_id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            tags: array_values($data['tags'] ?? []),
            createdAtUnixSecs: (int) ($data['created_at_unix_secs'] ?? 0),
            archived: (bool) ($data['archived'] ?? false),
            lastCallTimeUnixSecs: $data['last_call_time_unix_secs'] ?? null,
        );
    }
}
