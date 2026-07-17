# Conversational AI Agents

Agents live under `/v1/convai/agents`. An agent is a stored configuration; you manage it with standard CRUD plus a few extras (duplicate, shareable link, LLM usage estimate). All agents are versioned server side.

This package implements every endpoint below through the `HasAgents` capability. The methods return the raw `Illuminate\Http\Client\Response`; typed DTOs are available for the common response shapes (see [Package integration](06-package-integration.md)).

## Endpoint map

| Capability method | Verb | Path (after `/v1`) | Notes |
|-------------------|------|--------------------|-------|
| `createAgent(array $config)` | POST | `convai/agents/create` | `conversation_config` is the only required field. |
| `getAgent(string $id)` | GET | `convai/agents/{id}` | Optional `version_id`, `branch_id` query params. |
| `listAgents(array $query)` | GET | `convai/agents` | Paginated. `page_size` max 100, default 30. |
| `updateAgent(string $id, array $config)` | PATCH | `convai/agents/{id}` | Partial update (patch semantics). |
| `duplicateAgent(string $id, array $config)` | POST | `convai/agents/{id}/duplicate` | Optional `name`. |
| `getAgentlink(string $id)` | GET | `convai/agents/{id}/link` | Returns the shareable link token. |
| `calculateLlmUsage(string $id, array $config)` | POST | `convai/agent/{id}/llm-usage/calculate` | Note the singular `agent` in the path. |
| `deleteAgent(string $id)` | DELETE | `convai/agents/{id}` | Returns `204`. |

## Create agent

`POST /v1/convai/agents/create`

Request body (`Body_Create_Agent_...`):

| Field | Required | Description |
|-------|----------|-------------|
| `conversation_config` | yes | The orchestration and content config. See [Agent configuration](04-agent-configuration.md). |
| `platform_settings` | no | Everything not related to conversation orchestration (widget, auth, privacy, evaluation, guardrails, call limits, webhooks). |
| `workflow` | no | The workflow graph. See [Workflows](05-workflows.md). |
| `name` | no | A human name to find the agent by. |
| `tags` | no | String tags for classification and filtering. |

Response: `{ "agent_id": "..." }`.

> **Gotcha:** `conversation_config` must be a JSON object. An empty PHP array `[]` serialises to a JSON array `[]` and the API rejects it with `422 model_attributes_type`. Send `(object) []` (or a real config). This applies to any object valued field left empty.

```php
API::elevenLabs()->createAgent([
    'conversation_config' => (object) [],
    'name' => 'Support bot',
]);
```

## Get agent

`GET /v1/convai/agents/{agent_id}`

Returns the full `GetAgentResponseModel`: `agent_id`, `name`, `conversation_config`, `metadata` (created/updated unix seconds), `platform_settings`, `phone_numbers`, `whatsapp_accounts`, `workflow`, `access_info`, `tags`, and version/branch identifiers.

Optional query params: `version_id`, `branch_id`.

## List agents

`GET /v1/convai/agents`

Cursor paginated. Query params:

| Param | Default | Description |
|-------|---------|-------------|
| `page_size` | 30 | Max 100. |
| `search` | — | Filter by agent name. |
| `archived` | false | Include archived agents. |
| `created_by_user_id` | — | Filter by creator. Use `@me` for the caller. |
| `sort_by` | — | `name`, `created_at`, or `call_count_7d`. |
| `sort_direction` | — | `asc` or `desc`. |
| `cursor` | — | The `next_cursor` from the previous page. |

Response: `{ agents: AgentSummary[], has_more: bool, next_cursor: string|null }`. Each summary has `agent_id`, `name`, `tags`, `created_at_unix_secs`, `access_info`, `last_call_time_unix_secs`, `archived`.

Paginate by looping while `has_more` is true, passing `next_cursor` as the next `cursor`.

## Update agent

`PATCH /v1/convai/agents/{agent_id}`

Patch semantics: send only the fields you want to change. Accepts the same top level keys as create (`conversation_config`, `platform_settings`, `workflow`, `name`, `tags`) plus `version_description`. Returns the full agent.

## Duplicate agent

`POST /v1/convai/agents/{agent_id}/duplicate`

Optional body `{ "name": "..." }`. Returns `{ "agent_id": "..." }` for the new copy.

## Get shareable link

`GET /v1/convai/agents/{agent_id}/link`

Returns `{ agent_id, token }`, where `token` (when present) carries a `conversation_token`, its `purpose` (`shareable_link` or `signed_url`), and expiry. Use this to let a client start a conversation without the API key.

## Calculate expected LLM usage

`POST /v1/convai/agent/{agent_id}/llm-usage/calculate`

Body (`LLMUsageCalculatorRequestModel`, all optional): `prompt_length` (chars), `number_of_pages` (KB documents / URLs), `rag_enabled` (bool).

Response: `{ llm_prices: [{ llm, price_per_minute }] }`. In practice this returns a price row per available LLM (dozens of entries).

## Delete agent

`DELETE /v1/convai/agents/{agent_id}`

Returns `204` with an empty body on success.
