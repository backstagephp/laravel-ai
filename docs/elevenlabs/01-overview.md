# Overview

ElevenLabs is an audio AI platform. It started with text to speech and voice cloning and has grown into a broader suite. The REST API is organised per product area under a single base host.

## Products

| Area | What it does |
|------|--------------|
| Text to Speech (TTS) | Turns text into natural speech in many voices and languages. |
| Voices / Voice Library | Prebuilt voices, community voices, and custom cloned voices. |
| Voice cloning | Instant cloning from a short sample, and professional cloning from longer datasets. |
| Speech to Text (Scribe) | Transcription, including a realtime variant used by Conversational AI. |
| Conversational AI (ConvAI) | Voice and text agents that combine ASR, an LLM, TTS, tools, and workflows. This is what the package integrates. |
| Sound effects | Generates sound effects from a text prompt. |
| Dubbing | Translates and re-voices audio and video across languages. |
| Music | Generates music from prompts. |

This package currently implements the **Conversational AI Agents** surface. The other areas share the same host and authentication, so they can be added as additional drivers or capabilities later.

## API basics

- **Host:** `https://api.elevenlabs.io`
- **Version prefix:** every path is under `/v1`, for example `/v1/models`, `/v1/convai/agents`.
- **Format:** JSON request and response bodies. Send `Content-Type: application/json` on writes.
- **Auth:** an API key in the `xi-api-key` header. See [Authentication](02-authentication.md).

### First request

```bash
curl 'https://api.elevenlabs.io/v1/models' \
  -H 'Content-Type: application/json' \
  -H 'xi-api-key: $ELEVENLABS_API_KEY'
```

### Official SDKs

ElevenLabs ships first party SDKs (`@elevenlabs/elevenlabs-js` for Node, `elevenlabs` for Python) built with Fern. This package does not use them; it talks to the REST API directly through Laravel's HTTP client so the surface stays small and Laravel native.

## Conversational AI in one paragraph

A ConvAI agent is a configuration object. It bundles the speech recognition settings (ASR), turn taking behaviour, the text to speech voice, the LLM prompt and model, tools, a knowledge base, guardrails, privacy settings, a widget, and optionally a workflow graph. You create the agent once, then start conversations against it (voice via WebSocket or phone, or text). The agent config is what the endpoints in this package read and write.
