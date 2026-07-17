# Workflows

A workflow turns an agent from a single prompt into a graph. It routes the conversation through nodes (sub agents, tool runs, transfers, endings) based on conditions on the edges between them.

## Where workflows live

Workflows are **not a standalone resource**. There are no `/workflows` endpoints. A workflow is a field on the agent, under the `workflow` key, and you manage it through the agent create and update endpoints.

This package exposes it as the `HasWorkflows` capability so it reads as its own concern, but under the hood it patches the agent:

| Method | What it does |
|--------|--------------|
| `setAgentWorkflow(string $id, array $workflow)` | `PATCH convai/agents/{id}` with `{ "workflow": ... }`. |
| `getAgentWorkflow(string $id)` | `GET convai/agents/{id}` and returns the `workflow` subtree as an array. |

## Structure (`AgentWorkflowRequestModel`)

```json
{
  "nodes": { "<node_id>": { "type": "...", ... } },
  "edges": { "<edge_id>": { "source": "...", "target": "...", "forward_condition": { ... } } },
  "prevent_subagent_loops": false
}
```

Both `nodes` and `edges` are objects (dictionaries) keyed by id, not arrays. In PHP that means associative arrays, which serialise to JSON objects.

## Node types

Discriminated by `type`. Every node has a `position` (`{x, y}`) and `edge_order` (the ids of outgoing edges in evaluation order).

| `type` | Purpose |
|--------|---------|
| `start` | Entry point of the graph. |
| `end` | Terminates the conversation branch. |
| `override_agent` | Runs a sub agent with config, prompt, knowledge base, and tool overrides for that step. Has a `label` and `entry_behavior` (`auto` / `generate_immediately` / `wait_for_user`). |
| `standalone_agent` | Transfers to another agent (by `agent_id`), optionally starting at a specific `node_id`. |
| `tool` | Runs one or more tools in parallel; the node succeeds if all tools succeed. |
| `phone_number` | Transfers the call to a phone number or SIP URI, with transfer type, custom SIP headers, UUI, and post dial digits. |

## Edges and conditions

An edge connects a `source` node to a `target` node. It carries a `forward_condition` (traversed source to target) and optionally a `backward_condition`. Conditions are discriminated by `type`:

| Condition `type` | Meaning |
|------------------|---------|
| `unconditional` | Always traversable. |
| `llm` | An LLM evaluates a natural language `condition` to decide. |
| `result` | Based on whether the previous tool node was `successful` (bool). |
| `expression` | A boolean expression AST (see below). |

### Expression AST

`expression` conditions hold a small typed tree (`ASTNode`). Leaf and operator node types include: `boolean_literal`, `string_literal`, `number_literal`, `null_literal`, `dynamic_variable` (by name), `llm` (extract a value via schema), and operators `and`, `or`, `eq`, `neq`, `gt`, `gte`, `lt`, `lte`, `add`, `sub`, `mul`, `div`, and `conditional` (ternary). This lets you branch on dynamic variables and computed values without an LLM call.

Example: transfer to phone if a dynamic variable is set, or an LLM says so, or a mode equals `dev`:

```json
{
  "type": "or_operator",
  "children": [
    { "type": "dynamic_variable", "name": "force_phone_transfer" },
    { "type": "llm", "value_schema": { "type": "boolean", "description": "Phone condition" }, "prompt": "Phone condition" },
    { "type": "eq_operator",
      "left":  { "type": "dynamic_variable", "name": "mode" },
      "right": { "type": "string_literal", "value": "dev" } }
  ]
}
```

## Minimal workflow

The smallest valid graph is a start node flowing unconditionally into an end node:

```php
$workflow = [
    'nodes' => [
        'start_node' => ['type' => 'start', 'edge_order' => ['start_to_end'], 'position' => ['x' => 0, 'y' => 0]],
        'end_node'   => ['type' => 'end',   'edge_order' => [],              'position' => ['x' => 200, 'y' => 0]],
    ],
    'edges' => [
        'start_to_end' => [
            'source' => 'start_node',
            'target' => 'end_node',
            'forward_condition' => ['type' => 'unconditional'],
        ],
    ],
    'prevent_subagent_loops' => false,
];

API::elevenLabs()->setAgentWorkflow($agentId, $workflow);
$current = API::elevenLabs()->getAgentWorkflow($agentId);
```

`prevent_subagent_loops` guards against cycles when sub agents can transfer between each other.
