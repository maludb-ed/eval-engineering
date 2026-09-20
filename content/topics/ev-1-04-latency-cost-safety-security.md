---
id: ev-1-04
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Latency, Cost, Safety and Security as Measured Axes
task-statement: "4.1 Define evaluation metrics (accuracy, latency, cost, safety, security)"
scenarios: [1, 2, 3, 4]
---

## Lesson

The four non-accuracy axes each have a documented way to be measured wrongly, and that is what gets tested. **Latency** splits into *baseline latency* — the time to process the prompt and produce the response — and *time to first token (TTFT)*, the time until the first token arrives. They optimise differently and they matter to different products: TTFT governs perceived responsiveness in a streaming chat interface, while a batch clause extractor cares only about total time per document. Streaming improves perceived latency and changes total latency not at all, which is why "enable streaming" is a credited answer for a chat UI and a distractor for a batch pipeline. Express latency targets as **percentiles, never means** — a p50 of 400 ms with a p99 of nine seconds is a system with a visible tail, and the mean hides it. The documented target style is a percentile statement: 95% of responses under 200 ms.

**Cost** is measured per unit of business value — cost per resolved ticket, per processed claim, per merged pull request — not per token or per API call. The reason is that the cheap-looking option often loses: a smaller model that resolves 60% of tickets and escalates the rest can cost more per *resolved* ticket than a larger one that resolves 85%. A defensible cost figure includes everything the task actually consumes: retries, the judge calls in an online eval, cache writes, and the tokens spent by tool results returning into context. Techniques for moving the number belong to the optimisation task statement; here the job is only to define the metric so an optimisation can be judged.

**Safety** and **security** are distinct axes and both are *measured*, not merely reviewed at launch. Safety metrics cover unsafe-output rate, refusal correctness in both directions (over-refusal is a failure too, and one users notice faster), and the error-severity distribution. Security metrics cover prompt-injection resistance, sensitive-data leakage rate, and tool-permission violations — how often an agent attempted an action outside its granted scope. The documented example worth carrying is PHI detection: an LLM binary classifier catches implicit and paraphrased disclosures that a regex or keyword filter misses entirely, which is why rule-based-only leakage checks are a standing distractor. Privacy preservation is one of the documented success-criteria categories in its own right. All four axes then get stated together — that multi-axis target set is what makes a later optimisation defensible, because you can show what did *not* regress when cost came down.

## Key facts

- Baseline latency is total processing time; TTFT is time to the first token — different metrics, different optimisations.
- Streaming improves perceived latency and TTFT-driven experience; it does not reduce total latency.
- State latency targets as percentiles (p50/p95/p99), e.g. "95% of responses under 200 ms" — means conceal the tail.
- Cost is measured per unit of business value (per resolved ticket, per processed document), not per token or per call.
- A complete cost figure includes retries, grader/judge calls, cache writes, and tool results re-entering context.
- Safety metrics: unsafe-output rate, refusal correctness in both directions, error-severity distribution (e.g. 90% of errors merely inconvenient).
- Security metrics: prompt-injection resistance, sensitive-data leakage rate, tool-permission violations.
- LLM binary classification detects implicit or paraphrased PHI/PII that keyword and regex filters miss.
- Privacy preservation is a documented success-criteria category alongside latency and price.
- Every axis needs a measurement method and an owner; the multi-axis target set is what makes a later optimisation defensible.

## Exam traps

- A mean or average latency target — the percentile option is the credited one whenever a tail is described.
- "Enable streaming to reduce latency" for a batch or non-interactive workload, where perceived responsiveness is irrelevant.
- Cost stated per token or per API call, or a model downgrade justified on per-token price while the resolution rate drops.
- Cost models that omit retries and judge calls, then claim a saving the invoice will not show.
- Regex or keyword filtering offered as sufficient PII/PHI leakage measurement.
- Safety treated as a one-time pre-launch review rather than a continuously measured rate.
- Over-refusal ignored: an option that drives unsafe outputs to zero by refusing broadly still fails the safety criterion.
