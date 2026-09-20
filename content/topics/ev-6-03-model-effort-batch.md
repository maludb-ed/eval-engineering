---
id: ev-6-03
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Model Tier, Thinking Effort, and Batch
task-statement: "4.5 Optimise token usage, latency, and cost-performance trade-offs"
scenarios: [1, 2, 5]
---

## Lesson

Three levers move cost by large multiples, and they are ordered by how much quality they put at risk. **Batch processing** risks none: non-interactive work submitted asynchronously runs at **50% of standard cost**, which makes it the obvious answer for nightly classification, bulk extraction, and backfills — and the wrong answer for anything a user is waiting on. **Thinking effort** is the first genuine trade: current models use adaptive thinking with an effort setting spanning low through max, defaulting to high, and lowering it on routine routes cuts token spend within the same model. Note that the older fixed thinking-budget parameter is deprecated or rejected on current models — the concept persists, the mechanism has changed. Effort earns its cost differently by workload: coding and long-horizon agentic work respond strongly to higher effort, while chat, classification, and high-volume routes often hold quality at low or medium.

**Model tier** is the largest lever and the most frequently misused. Current list prices run roughly Opus 5 at $5/$25 per million input/output tokens, Sonnet 5 at $2/$10, and Haiku 4.5 at $1/$5 — so the spread across tiers is several-fold, not marginal. The non-obvious guidance, and a strong candidate for an exam item, is to **measure the most capable model at lower effort before building a multi-model cascade**: lower effort on a newer model often matches or beats a previous generation at high effort, and one model means one cache namespace. A cascade forfeits cache reuse across its models and adds routing logic, a second eval surface, and a second failure mode. Tiering down is still right when a task is genuinely simple — classification, routing, extraction with a fixed schema — and the evidence for "genuinely simple" is an eval run, not intuition.

The composite answer the exam usually wants is per-stage assignment inside a pipeline rather than one global choice: a small model for extraction and routing, a larger one for the reasoning step that needs it, batch for whatever is not interactive, and effort tuned per route. Two cautions. Anything that changes the model or the speed setting **invalidates the prompt cache**, so a routing change can quietly undo a caching win. And the decision criterion stays cost per completed task — a cheaper model that escalates, retries, or needs an extra verification pass may cost more in total while looking cheaper per call.

## Key facts

- Batch processing runs asynchronously at 50% of standard cost; unsuitable for interactive work.
- Current models use adaptive thinking with an effort setting (low, medium, high, xhigh, max), defaulting to high; the older fixed thinking-budget parameter is deprecated or rejected on current models.
- Lower effort suits chat, classification, and high-volume routes; coding and long-horizon agentic work repay higher effort.
- Approximate list prices per million tokens (input/output): Opus 5 $5/$25, Sonnet 5 $2/$10, Haiku 4.5 $1/$5 — verify against current documentation.
- Before building a multi-model cascade, measure the most capable model at lower effort; newer models at low effort often match older ones at high effort.
- Cascades forfeit cache reuse because caches are model-scoped, and add routing logic plus a second eval surface.
- Per-stage model assignment inside a pipeline usually beats one global model choice.
- Changing model or speed setting invalidates the prompt cache.
- Thinking tokens are billed whether or not the reasoning is displayed.
- The decision criterion remains cost per completed task.

## Exam traps

- Batch processing proposed for an interactive, user-facing path.
- A cascade built without first measuring the stronger model at reduced effort.
- A model downgrade justified by per-token price while escalation and retry rates rise.
- One model tier applied uniformly across a pipeline whose stages differ in difficulty.
- Effort raised globally to maximum "for quality", with no measurement showing headroom above the level below.
- A routing change presented as a pure saving, ignoring the cache invalidation it causes.
