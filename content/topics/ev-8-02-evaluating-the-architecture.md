---
id: ev-8-02
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Evaluating the Integration and Architecture Layers
task-statement: "Cross-domain — evaluation evidence supporting Integration and Solution Design"
scenarios: [4, 5, 6]
---

## Lesson

Most production failures live between components, so the eval design has to reach the seams. **Component-level evaluation** is the structural move: score retrieval separately from generation, as M5 requires; score tool selection separately from tool execution; score the router separately from the handlers it routes to. Without that separation an end-to-end number tells you the system is 71% and nothing about where the 29% went. Tool selection is the seam that most often needs its own eval as a system grows — with a large catalogue, possibly spanning several MCP servers, the questions "did it pick the right tool" and "did it call it correctly" are different failures with different fixes: overlapping descriptions and ambiguous scope cause the first, bad schemas and weak error channels the second.

Evals also feed architectural decisions rather than merely reporting on them. The **workflow-versus-agentic** choice is answerable with measurement: if a fixed sequence of steps scores as well as an agent that plans its own path, the workflow wins on cost, latency, and predictability, and the agent's flexibility is an unpaid premium. The same logic governs how much autonomy to grant, how many tools to expose, and whether a verification pass earns its cost — each is a hypothesis that an eval can test on the real task distribution. This is also the honest answer to "should we build an agent at all": complexity, value, viability, and cost of error are judgments, but the first three are measurable, and an architect who has measured them argues from evidence rather than taste.

Two cautions specific to the seams. **Harness parity extends to integrations** — evaluating an agent against mocked tools or a stubbed retrieval layer measures a system that does not exist, and integration behaviour under real latency, real errors, and real permissions is exactly where it will fail. And when a system integrates components you do not control — a third-party API, a shared index, an MCP server owned by another team — their changes are upstream changes you will experience as unexplained regressions, so the version fields in the trace and a contract test over the integration are what let you attribute the drop.

## Key facts

- Evaluate components separately: retrieval vs generation, tool selection vs tool execution, router vs handler.
- End-to-end scores detect problems; component scores localise them.
- Tool-selection accuracy deserves its own eval as the catalogue grows, especially across multiple MCP servers.
- Wrong-tool failures point at descriptions and scope; wrong-call failures point at schemas and error channels.
- The workflow-versus-agentic choice is testable: if a fixed sequence matches an agent's score, it wins on cost, latency, and predictability.
- Autonomy level, tool-surface size, and verification passes are each hypotheses an eval can settle.
- The agent-viability questions — complexity, value, viability, cost of error — are largely measurable.
- Harness parity extends to integrations: mocked tools and stubbed retrieval measure a system that does not ship.
- Components owned by other teams or vendors are upstream change sources; contract tests and version fields make their regressions attributable.

## Exam traps

- A single end-to-end accuracy metric proposed for a multi-component pipeline.
- An agentic pattern chosen over a deterministic workflow with no measurement showing the flexibility is used.
- Tool-selection problems addressed by adding tools or instructions rather than by disambiguating descriptions.
- Evaluating against mocked integrations and treating the result as production evidence.
- A regression investigated entirely within your own components when an upstream dependency changed.
- More autonomy granted to fix quality problems, where the measured failure is planning, not permission.
