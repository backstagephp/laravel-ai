# Authentication

Every request to the ElevenLabs API is authenticated with an API key, sent in the `xi-api-key` HTTP header.

```bash
xi-api-key: ELEVENLABS_API_KEY
```

The key is a secret. Keep it server side. Never embed it in browsers, mobile apps, or any client that ships to end users.

## Getting a key

Create keys in the ElevenLabs dashboard under your profile / API keys. Each key can be constrained:

1. **Scope restriction** — limit which API endpoints the key may call.
2. **Credit quota** — cap how many credits the key may spend.
3. **IP allowlisting** — restrict the key to specific IP addresses or CIDR ranges. Requests from other IPs are rejected with `403`.

## How this package supplies the key

The `ElevenLabsClient` reads the key from config and sets the header once when the authenticated HTTP client is built:

```php
Http::baseUrl(config('ai.elevenlabs.base_url'))
    ->acceptJson()
    ->asJson()
    ->withHeaders(['xi-api-key' => config('ai.elevenlabs.key')]);
```

Config lives in the package's `config/ai.php`, sourced from environment variables:

```php
'elevenlabs' => [
    'key' => env('ELEVENLABS_API_KEY'),
    'base_url' => env('ELEVENLABS_BASE_URL', 'https://api.elevenlabs.io/v1'),
],
```

If the key is empty the client throws a `RuntimeException` at construction, so a missing key fails fast and loudly instead of sending an unauthenticated request.

## Single use tokens

For client side use (for example a browser starting a voice conversation) you should not expose the API key. ElevenLabs provides short lived single use tokens for that. The server requests a token with the secret key, hands the token to the client, and the client connects with it. Tokens expire after a short window. See the `convai` link and token endpoints and the "Single use tokens" section of the ElevenLabs docs.

## Data residency

ElevenLabs exposes regional hosts. Pick the one that matches your compliance needs by changing `ELEVENLABS_BASE_URL` (keep the `/v1` suffix):

| Region | Host |
|--------|------|
| Global (default) | `https://api.elevenlabs.io` |
| United States | `https://api.us.elevenlabs.io` |
| European Union | `https://api.eu.residency.elevenlabs.io` |
| India | `https://api.in.residency.elevenlabs.io` |
| Singapore | `https://api.sg.residency.elevenlabs.io` |

The same key and header work against every host.
