<?php

namespace Backstage\Laravel\AI\Data;

readonly class LlmPrice
{
    public function __construct(
        public string $llm,
        public float $pricePerMinute,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function from(array $data): self
    {
        return new self(
            llm: (string) ($data['llm'] ?? ''),
            pricePerMinute: (float) ($data['price_per_minute'] ?? 0.0),
        );
    }
}
