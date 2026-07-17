<?php

namespace Backstage\Laravel\AI\Data;

readonly class LlmUsage
{
    /**
     * @param  list<LlmPrice>  $prices
     */
    public function __construct(
        public array $prices,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function from(array $data): self
    {
        return new self(
            prices: array_map(LlmPrice::from(...), $data['llm_prices'] ?? []),
        );
    }
}
