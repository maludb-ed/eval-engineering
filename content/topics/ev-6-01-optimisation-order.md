---
id: ev-6-01
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: The Order of Operations
task-statement: "4.5 Optimise token usage, latency, and cost-performance trade-offs"
scenarios: [1, 3, 5]
---

## Lesson

Optimisation has an order, and the documented first rule is to **engineer a prompt that works well before applying constraints** — trying to reduce latency or cost prematurely prevents you from discovering what top performance even looks like. An architect who tiers down the model before the system works well has no baseline to measure the degradation against. So the sequence is: establish quality, baseline the cost and latency, then apply levers in increasing order of what they cost you in quality.

That ordering groups the levers into two classes. **Free wins** change what you pay without changing what the model does: prompt caching, input-token hygiene, loop hygiene, output-token hygiene, and batch processing for work that is not interactive. **Trade-offs** change the model's behaviour: lowering thinking effort, tiering down the model, trimming retrieved context, capping output. Take every free win before any trade-off — an architecture that downgrades the model while re-sending an uncached 40,000-token system prompt on every call has taken the expensive decision and skipped the cheap one, and that is a favourite distractor shape.

Baselining is where candidates lose marks by measuring the wrong unit. **Judge cost per completed task, not per request.** A cheaper model that needs three turns and a retry where the larger one needed one is not cheaper, and the per-request figure hides that completely. The same applies to latency: a single call's p95 matters less than the wall-clock to a resolved ticket across a pipeline. The measurement sources are the API's own numbers — the `usage` fields on every response (input, output, and the cache read and creation counts), the token-counting endpoint for estimating before you spend, and organisation-level usage and cost reporting for the aggregate. Third-party tokenizers do not match Claude's tokenizer and should not be used to estimate spend. Finally, every optimisation is a change like any other: it goes through the M4 sequence with the eval suite and the guardrail metrics, because a cost win that quietly costs three points of accuracy is a regression wearing a cost-saving label.

## Key facts

- Engineer for quality first; apply latency and cost constraints afterwards.
- Free wins (caching, input/output token hygiene, loop hygiene, batch) come before quality trade-offs (effort, model tier, context trimming).
- Measure cost per completed task, not per request or per token.
- A cheaper model that needs more turns or retries to finish the job is not cheaper.
- Baseline from `response.usage`: input tokens, output tokens, cache read tokens, cache creation tokens.
- Use the API's token-counting endpoint to estimate before spending; third-party tokenizers do not match Claude's tokenizer.
- Organisation-level usage and cost reporting provides the aggregate picture across routes.
- Every optimisation is validated against the eval suite and the rollout guardrails before it ships.
- Tune per route rather than globally — different routes have different quality sensitivity.

## Exam traps

- A model downgrade or effort reduction proposed while obvious caching and context waste remains unaddressed.
- Cost compared per request or per token across two designs with different completion rates.
- An optimisation shipped on the strength of the cost saving alone, with no eval run and no guardrail metrics.
- Estimating spend with a third-party tokenizer.
- One global setting applied across every route regardless of each route's quality sensitivity.
- Optimising before the system reaches its quality target, so the baseline never existed.
