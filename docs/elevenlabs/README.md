# ElevenLabs

Reference documentation for the ElevenLabs integration shipped in `backstage/laravel-ai`.

These notes cover what ElevenLabs is, how its API authenticates, and the Conversational AI Agents surface that this package implements. They are written to be accurate against the OpenAPI specification used to build the driver; anything model or pricing related changes often, so treat the official docs at [elevenlabs.io/docs](https://elevenlabs.io/docs) as the source of truth for those.

## Index

1. [Overview](01-overview.md) — the platform, its products, and API basics.
2. [Authentication](02-authentication.md) — API keys, the `xi-api-key` header, scopes, single use tokens, data residency.
3. [Conversational AI Agents](03-conversational-ai-agents.md) — the agent endpoints, request and response shapes.
4. [Agent configuration](04-agent-configuration.md) — the `conversation_config` and `platform_settings` structure.
5. [Workflows](05-workflows.md) — agent workflow nodes, edges, and conditions.
6. [Package integration](06-package-integration.md) — how this package wraps the API (facade, drivers, DTOs, config, command).

## At a glance

- Base URL: `https://api.elevenlabs.io`
- API version prefix: `/v1`
- Auth header: `xi-api-key: <YOUR_KEY>`
- Agents live under `/v1/convai/agents`
- Workflows are not a standalone resource; they live inside an agent under the `workflow` key.
