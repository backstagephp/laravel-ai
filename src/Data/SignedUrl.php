<?php

namespace Backstage\Laravel\AI\Data;

readonly class SignedUrl
{
    public function __construct(
        public string $signedUrl,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function from(array $data): self
    {
        return new self(
            signedUrl: (string) ($data['signed_url'] ?? ''),
        );
    }
}
