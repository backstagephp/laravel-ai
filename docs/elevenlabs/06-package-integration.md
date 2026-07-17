# Package integration

How `backstage/laravel-ai` wraps the ElevenLabs API. The design is multi provider: a thin transport client, a driver manager resolved by name, and capability interfaces that each provider implements only if it supports them.

## Layers

```
Facades\API            -> facade over the manager (also aliased globally as `API`)
Managers\APIManager    -> resolves a driver by name (extends Support\Manager)
Support\Manager        -> generic driver manager (no default driver)
Drivers\ElevenLabs\
  Clients\ElevenLabsClient   -> builds the authenticated HTTP client (xi-api-key)
  Concerns\InteractsWithApi        -> get/post/patch/delete/http  (implements Contracts\ApiClient)
  Concerns\InteractsWithAgents     -> agent endpoints  (implements Contracts\HasAgents)
  Concerns\InteractsWithWorkflows  -> workflow embed   (implements Contracts\HasWorkflows)
  ElevenLabsDriver           -> composes the client, uses the traits, implements the contracts
Contracts\{ApiClient, HasAgents, HasWorkflows}   -> provider agnostic capability surface
Data\{CreateAgentResponse, AgentsPage, AgentSummary, LlmPrice, LlmUsage}  -> typed response DTOs
```

- The **client** does the real HTTP work; the `InteractsWithApi` trait gives it `get/post/patch/delete/http`.
- The **driver** composes the client, mixes in the agent and workflow traits, and implements the capability contracts. `@mixin ElevenLabsClient` documents the forwarded HTTP methods for IDEs.
- The **manager** resolves drivers by name via `create{Studly}Driver()`. There is no default driver; calling `driver()` with no name throws.

## Resolving a driver

```php
use Backstage\Laravel\AI\Facades\API;

API::elevenLabs();            // dynamic: resolves the 'eleven-labs' driver
API::driver('eleven-labs');   // explicit by key
API::driver();                // throws: no default driver
```

`API::elevenLabs()` works because the manager's `__call` maps a method name to a driver when a matching `create{Studly}Driver()` exists (`elevenLabs` -> `createElevenLabsDriver`).

The facade resolves the container binding `backstage.laravel-ai.api.client`, registered as a singleton in `AIServiceProvider::packageRegistered()`. The `API` alias is registered through the package's `extra.laravel.aliases`.

## Capabilities

```php
// HasAgents
API::elevenLabs()->createAgent(['conversation_config' => (object) [], 'name' => 'Bot']);
API::elevenLabs()->getAgent($id);
API::elevenLabs()->listAgents(['page_size' => 50]);
API::elevenLabs()->updateAgent($id, ['name' => 'New name']);
API::elevenLabs()->duplicateAgent($id, ['name' => 'Copy']);
API::elevenLabs()->getAgentlink($id);
API::elevenLabs()->calculateLlmUsage($id, ['rag_enabled' => false]);
API::elevenLabs()->deleteAgent($id);

// HasWorkflows
API::elevenLabs()->setAgentWorkflow($id, $workflow);
API::elevenLabs()->getAgentWorkflow($id);
```

All `HasAgents` / `HasWorkflows` methods return `Illuminate\Http\Client\Response`, except `getAgentWorkflow` which returns the workflow array. Raw responses keep the full payload available; the agent config is too large to model exhaustively.

## DTOs

Typed value objects wrap the common response shapes. Each has a static `from(array)` factory and is defensive about missing fields.

```php
use Backstage\Laravel\AI\Data\CreateAgentResponse;
use Backstage\Laravel\AI\Data\AgentsPage;
use Backstage\Laravel\AI\Data\LlmUsage;

$id   = CreateAgentResponse::from(API::elevenLabs()->createAgent([...])->json())->agentId;
$page = AgentsPage::from(API::elevenLabs()->listAgents(['page_size' => 50])->json());
foreach ($page->agents as $agent) {
    // $agent->agentId, $agent->name, $agent->tags, $agent->createdAtUnixSecs, ...
}
$usage = LlmUsage::from(API::elevenLabs()->calculateLlmUsage($id)->json());
```

| DTO | Wraps |
|-----|-------|
| `CreateAgentResponse` | `{ agent_id }` (create, duplicate) |
| `AgentSummary` | one list item |
| `AgentsPage` | `{ agents[], has_more, next_cursor }` |
| `LlmPrice` / `LlmUsage` | `{ llm_prices[] }` |

## Configuration

`config/ai.php`:

```php
'elevenlabs' => [
    'key' => env('ELEVENLABS_API_KEY'),
    'base_url' => env('ELEVENLABS_BASE_URL', 'https://api.elevenlabs.io/v1'),
],
```

Set `ELEVENLABS_API_KEY` (and optionally `ELEVENLABS_BASE_URL` for data residency) in the host app's environment.

## Artisan command demo

`elevenlabs:agent-lifecycle` runs the whole flow end to end against the live API: create, get, list, update, set workflow, get workflow, duplicate, get link, calculate usage, delete both agents.

```bash
php artisan elevenlabs:agent-lifecycle
php artisan elevenlabs:agent-lifecycle --name="Support bot" --keep
```

`--keep` leaves the created agents in place instead of deleting them. Without a valid `ELEVENLABS_API_KEY` the first step fails fast.

## Adding another provider

The multi provider design means a new provider is additive:

1. Add `Drivers/Foo/Clients/FooClient` (auth + base URL) and a `FooDriver`.
2. Implement whichever capabilities it supports (`Contracts\HasAgents`, `Contracts\HasWorkflows`, or new ones).
3. Add `createFooDriver()` to `APIManager`.

Then `API::driver('foo')` (or `API::foo()`) resolves it. The facade, DTOs, and capability contracts stay unchanged.

## Consuming the package as a path dependency

The host app links the package as a symlinked path repository:

```json
"repositories": [
    { "type": "path", "url": "/var/www/packages/cms/packages/laravel-ai", "options": { "symlink": true } }
],
"require": {
    "backstage/laravel-ai": "@dev"
}
```

The `@dev` stability flag allows the package's dev branch version without lowering the app's global `minimum-stability`.
