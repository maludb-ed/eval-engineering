---
id: ev-7-03
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Online Evaluation and Drift Detection
task-statement: "4.6 Monitor system performance using logging and observability tools"
scenarios: [4, 5, 2]
---

## Lesson

Offline suites measure a fixed dataset; production shows behaviour on the distribution that actually arrives. **Online evaluation** closes that gap by sampling live traffic into graders — typically the cheap deterministic checks on everything (schema validity, tool-call legality, groundedness where a source is available) and an LLM judge on a sampled fraction. Sampling policy is a design decision with a cost ceiling attached: a judge call on every production request can approach the cost of serving the request itself, so set a percentage, cap the spend, and **stratify** rather than sampling only failures — a failures-only sample destroys the base rate and makes the quality trend uninterpretable. The judges used online are the same instruments M3 describes and carry the same obligation: calibrated against human labels, one dimension each, with an abstain option.

**Drift** is the second job of production measurement, and the exam-relevant skill is matching a drift type to the signal that detects it. *Input drift* — users asking new kinds of questions, or a new customer segment arriving — shows up in query clustering, embedding distributions, and a rising out-of-scope or refusal rate. *Corpus drift* — the retrieval index changing under the system — shows up in retrieval metrics, chunk-age distributions, and the confident-but-wrong pattern from M5. *Model drift* — a version change beneath you — shows up as a step change aligned to a deployment date, which is why version fields in the trace are what make the diagnosis possible. *Eval-set staleness* is the quiet one: the suite still passes while production degrades, because the dataset represents last year's traffic. The tell is a widening gap between offline scores and production signals, and the fix is refreshing the dataset from recent traffic, not tuning the system.

The practical arrangement is a loop with a cadence. Online checks run continuously and feed the dashboards; a sampled slice goes to judges; anomalies and low-scoring traces are pulled into scheduled **transcript review**; and confirmed failures become eval tasks, which keeps the offline suite tracking the live distribution. That loop is also the honest answer to "how do we know it still works" — not a number from launch week, but a measurement that renews itself as fast as the inputs change.

## Key facts

- Online evaluation samples live traffic into graders: cheap deterministic checks broadly, LLM judges on a sampled fraction.
- Sampling policy needs a defined rate and a cost ceiling; judge calls on all traffic can rival serving cost.
- Stratify the sample; sampling only failures destroys the base rate and the trend.
- Online judges need the same calibration, per-dimension isolation, and abstain option as offline ones.
- Input drift is detected by query clustering, embedding distributions, and out-of-scope or refusal rates.
- Corpus drift is detected by retrieval metrics and chunk-age distributions.
- Model drift appears as a step change aligned to a deployment — detectable only if versions are in the trace.
- Eval-set staleness shows as a widening gap between offline scores and production signals.
- Refresh the dataset from recent traffic when staleness is diagnosed; do not tune the system to close the gap.
- The loop: continuous checks → sampled judging → scheduled transcript review → new eval tasks.

## Exam traps

- An LLM judge run on 100% of production traffic with no cost ceiling.
- Sampling only failed or low-rated interactions, then reporting a quality rate from that sample.
- An online judge deployed without the calibration required of an offline one.
- Attributing a step-change quality drop to user behaviour when a model or index version changed on the same date.
- Tuning the system to close a gap between a passing offline suite and degrading production metrics, when the dataset is stale.
- Treating launch-week eval results as ongoing evidence of quality.
