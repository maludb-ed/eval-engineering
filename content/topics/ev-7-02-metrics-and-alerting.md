---
id: ev-7-02
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Metrics, Dashboards, and Alerting
task-statement: "4.6 Monitor system performance using logging and observability tools"
scenarios: [1, 3, 4]
---

## Lesson

Production metrics are the M1 axes measured continuously instead of at eval time, and the set is predictable: latency percentiles (p50, p95, p99) rather than means, error rate, refusal rate with categories, escalation or human-handoff rate, cost per session and per completed task, cache hit rate, token usage per turn, and whatever business outcome the system exists to move — resolution rate, cycle time, documents processed. The continuity that matters for the exam is that **the guardrail metrics from the M4 rollout become the standing production alerts**. A metric worth stopping a canary for is worth paging on afterwards; if the two sets differ, one of them was chosen carelessly.

Alerting design is where items are won or lost. **Alert on rates and percentiles, not absolute counts** — "50 errors in an hour" means one thing at low traffic and nothing at peak, while "error rate above 2% for ten minutes" holds at any volume. Give thresholds a duration so a single spike does not page anyone, and separate severity by consequence: a safety or leakage signal pages immediately, a cost or latency drift opens a ticket, a quality dip routes to the next review. Alert on the leading indicators too — refusal rate, escalation rate, and cache hit rate move before the business metric does, which is the difference between noticing a regression in an hour and noticing it in a quarterly report.

Dashboards should be built per audience, because the same data answers different questions. An engineer needs traces, error breakdowns, and per-tool failure rates. An architect needs quality and cost trends together, so a cost win that cost accuracy is visible as one picture. An executive needs outcome per dollar and the trend against the targets that were promised — which is where M8's reporting discipline picks up. Two standing cautions: monitoring **detects** while evals **explain**, so an alert should always be able to hand over a trace; and a dashboard nobody has agreed to watch is not monitoring. Ownership and a review cadence are part of the design, exactly as they are for the eval suite.

## Key facts

- Core production metrics: latency percentiles, error rate, refusal rate with categories, escalation rate, cost per session and per completed task, cache hit rate, tokens per turn, business outcome.
- The rollout's guardrail metrics become the standing production alerts.
- Alert on rates and percentiles, never absolute counts, so thresholds hold at any traffic volume.
- Give every threshold a duration to suppress single-spike pages.
- Separate alert severity by consequence: safety pages, cost and latency ticket, quality routes to review.
- Leading indicators — refusal rate, escalation rate, cache hit rate — move before business metrics do.
- Dashboards differ by audience: traces for engineers, quality-and-cost trend for architects, outcome per dollar for executives.
- Monitoring detects; evals and transcripts explain. An alert must be able to hand over a trace.
- A dashboard with no named owner and no review cadence is not monitoring.

## Exam traps

- Alert thresholds expressed as absolute error or failure counts.
- Mean or average latency on the dashboard where a tail problem is described.
- Guardrail metrics used during the A/B that quietly disappear after full rollout.
- Every alert routed at the same severity, so a cost drift wakes the same person as a safety event.
- Quality and cost tracked on separate dashboards owned by separate teams, so a trade between them is invisible.
- Monitoring presented as a replacement for the eval suite rather than a complementary layer.
