# Agent configuration

The create and update endpoints accept two large objects: `conversation_config` (orchestration and content) and `platform_settings` (everything else). This page maps the structure so you know which keys exist. The full schema is deep; this package passes it through as arrays rather than modelling every field, so you can send exactly what the API documents.

## `conversation_config`

`ConversationalConfigAPIModel`. Groups:

### `asr` — speech recognition

| Key | Default | Notes |
|-----|---------|-------|
| `quality` | `high` | Transcription quality. |
| `provider` | `scribe_realtime` | `elevenlabs` or `scribe_realtime`. |
| `user_input_audio_format` | `pcm_16000` | pcm 8000/16000/22050/24000/44100/48000 or `ulaw_8000`. |
| `keywords` | — | Words to boost recognition probability. |

### `turn` — turn taking

Key fields: `turn_timeout` (default 7s), `silence_end_call_timeout` (-1 disables), `turn_eagerness` (`patient`/`normal`/`eager`), `spelling_patience` (`auto`/`off`), `turn_model` (`turn_v2`/`turn_v3`), `interruption_ignore_terms`, `soft_timeout_config` (filler messages while the LLM thinks). Speculative turns and retranscription toggles also live here.

### `tts` — text to speech

| Key | Default | Notes |
|-----|---------|-------|
| `model_id` | `eleven_flash_v2` | Conversational TTS model. |
| `voice_id` | `cjVigY5qzO86Huf0OWal` | The speaking voice. |
| `agent_output_audio_format` | `pcm_16000` | Output PCM/ulaw formats. |
| `stability` | 0.5 | |
| `speed` | 1 | |
| `similarity_boost` | 0.8 | |
| `supported_voices` | — | Extra voices the agent may switch to. |
| `pronunciation_dictionary_locators` | — | Custom pronunciations. |

### `conversation` — events and limits

`text_only` (skip audio to avoid audio pricing), `max_duration_seconds` (default 600), `client_events` (which events stream to the client), `monitoring_enabled`, `background_sound`, `file_input` (image/PDF uploads), `source_attribution`.

### `agent` — the brain

- `first_message` — what the agent opens with; empty means it waits for the user.
- `language` — default `en`.
- `dynamic_variables` — placeholders resolved at runtime.
- `prompt` (`PromptAgentAPIModel`):
  - `prompt` — the system prompt text.
  - `llm` — the model (default `gemini-2.5-flash`). Many are supported: GPT (4o, 4.1, 5.x), Gemini (1.5, 2.x, 3.x), Claude (3.x, 4.x), Grok, Qwen, GPT-OSS, plus `custom-llm`.
  - `temperature`, `max_tokens`, `reasoning_effort`, `thinking_budget`.
  - `tool_ids`, `built_in_tools`, `mcp_server_ids`, `native_mcp_server_ids`.
  - `knowledge_base`, `rag` (RAG config with embedding model and retrieval limits).
  - `custom_llm` — endpoint, key, headers when `llm` is `custom-llm`.
  - `backup_llm_config` — cascade to a backup model on failure.
  - `timezone` — include current time in the system prompt.

### `language_presets`, `vad`

Per language overrides and voice activity detection tuning.

## `platform_settings`

`AgentPlatformSettingsRequestModel`. Not related to the conversation content itself:

- `evaluation` — success criteria to grade conversations against.
- `widget` — the embeddable web widget (colours, avatar, text, placement, feedback).
- `data_collection` / `data_collection_scopes` — structured fields to extract post call.
- `overrides` — which fields a client may override when starting a conversation.
- `workspace_overrides` — conversation initiation webhook and post call webhooks.
- `testing` — attached test suites.
- `guardrails` — focus, prompt injection, content thresholds, custom guardrails.
- `auth` — require signed tokens, host allowlist, origin header enforcement.
- `call_limits` — concurrency and daily limits, bursting.
- `privacy` — recording, retention days, PII/audio deletion, zero retention mode, transcript redaction.
- `trust_context` — `unknown` / `low` / `high`; signals how much to trust the participant.
- `analysis_llm`, `summary_language`, `topic_discovery`, `sentiment_analysis`.

## Tools

The `agent.prompt.tools` array (and `built_in_tools`) support several tool types, discriminated by `type`:

- `webhook` — calls an external HTTP endpoint (URL, method, param and body schemas, response filter).
- `client` — sends an event to the client to trigger something client side.
- `api_integration_webhook` — a managed integration webhook.
- `system` — built in behaviours: `end_call`, `language_detection`, `transfer_to_agent`, `transfer_to_number`, `skip_turn`, `play_keypad_touch_tone` (DTMF), `voicemail_detection`, `start_procedure`, `end_procedure`, `run_subagent`, `knowledge_base_rag`.
- `mcp` — Model Context Protocol server tools.

Prefer `tool_ids` (references to tools defined once) over inline `tools` where possible.

## Input vs Output shapes

The spec distinguishes `-Input` (what you send) from `-Output` (what you get back). They mostly match; outputs include server computed extras (metadata, access info, resolved defaults). When you round trip an agent (get, tweak, patch) you can send back the relevant subtree; you do not need to resend everything.
