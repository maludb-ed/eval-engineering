---
id: ev-7-01
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: What to Log — the Trace as the Unit
task-statement: "4.6 Monitor system performance using logging and observability tools"
scenarios: [1, 3, 6]
---

## Lesson

The unit of observability for an LLM system is the **trace**, not the log line. A trace covers one user-facing interaction end to end, with a span per model call and per tool call, and it is what makes a failure explicable after the fact. Each span should carry the inputs and outputs, the **versions** that produced them — model ID and settings, prompt version, tool definitions, retrieval index snapshot, scaffold version — and the API's own accounting: the `usage` fields for input, output, and cache read and creation tokens, and the `stop_reason` that ended the turn. Correlation IDs tie the spans to a session, a user, and a request so the trace can be reassembled, and for agent turns the span should also record which tool was called with which arguments, what came back, and whether an error path was taken.

The design test to apply is **replayability**: a logged failure should contain enough to reconstruct it as an eval task without going back to the user. That single requirement settles most "what should we log" items, because it forces inputs as well as outputs, the assembled context rather than the template, and the versions rather than "production at the time". Logging outputs alone is the classic insufficiency — you can see that the answer was wrong and you cannot tell why, and you cannot add it to the suite. This is the loop M4 and M2 both depend on: incidents become tasks, and the suite tracks reality because production keeps feeding it.

Several fields repay specific attention because they answer questions no aggregate can. `stop_reason` distinguishes a normal completion from one truncated at `max_tokens`, a turn that ended to call a tool, and a **refusal** — where the accompanying detail carries the category, making refusals a monitorable safety signal rather than an anecdote. The cache token counts are the live check on M6's caching work: a `cache_read_input_tokens` that falls to zero in production is a silent regression with a direct cost consequence. Token counts per turn and per session give the cost axis its data. And where the platform reports which speed setting or inference geography served the request, those belong in the span too, because they are exactly the attributes a later cost or compliance question will ask about. Aggregate metrics come *from* traces; if the trace is thin, every downstream number is unexplainable.

## Key facts

- The trace — one interaction, with a span per model call and per tool call — is the unit of observability.
- Each span records inputs, outputs, and the versions that produced them: model and settings, prompt version, tool definitions, index snapshot, scaffold version.
- Record the API `usage` fields: input tokens, output tokens, cache read tokens, cache creation tokens.
- Record `stop_reason`; a refusal carries category detail and is a monitorable safety signal.
- `stop_reason` also distinguishes normal completion from `max_tokens` truncation and tool-use turns.
- Correlation IDs link spans to request, session, and user.
- Design test: a logged failure must be replayable as an eval task without contacting the user.
- Log the assembled context, not the template that was supposed to produce it.
- A production `cache_read_input_tokens` that drops to zero is a silent cost regression.
- Aggregates are derived from traces; thin traces make every downstream metric unexplainable.

## Exam traps

- Logging model outputs without inputs, context, or version metadata.
- Retaining only aggregate metrics, so no individual failure can be reconstructed.
- Logging the prompt template rather than the context the model actually received.
- Treating refusals as user-support anecdotes rather than a measured rate with categories.
- No cache-hit telemetry in production after a caching optimisation shipped.
- Traces without correlation IDs, so multi-step agent behaviour cannot be reassembled.
