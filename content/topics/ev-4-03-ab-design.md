---
id: ev-4-03
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Designing an A/B Test for an LLM System
task-statement: "4.3 Conduct A/B testing and iterative improvements"
scenarios: [1, 3, 5]
---

## Lesson

The first decision in an LLM A/B test is the **unit of randomisation**, and it is the one the exam tests most directly because LLM systems break a rule that ordinary web experiments do not have to think about. Randomising **per request** inside a multi-turn conversation means a single user's exchange is served by two different configurations — the tone shifts mid-conversation, context assembled by one variant is consumed by the other, and neither arm measures a coherent experience. For anything conversational or agentic, randomise by **user or account**; by session only when sessions are genuinely independent. Per-request assignment is defensible for single-shot, stateless calls such as a classification endpoint, and nowhere else.

Second is power. A change worth shipping usually moves a metric by a small amount, and small effects need large samples: an underpowered test that reports "no significant difference" has not shown the variants are equivalent, it has shown the test could not tell. Decide the **minimum detectable effect** you care about and the sample size that gives it before starting, and resist **peeking** — checking repeatedly and stopping the moment a threshold is crossed inflates false positives, which is why the stopping rule belongs in the design. Statistical significance is also not the same as practical significance: a reliably detected 0.3% improvement that costs 40% more per task fails on the axes M1 told you to name.

Third is duration and contamination. Run across at least one full business cycle so weekday and weekend traffic are both represented, and allow for **novelty effects**, where a visible change moves behaviour briefly before reverting. Keep one change per experiment, or use a factorial design deliberately — two changes shipped into one test cannot be attributed. Fix the primary decision metric in advance, with secondary metrics and guardrails named alongside it; choosing the metric after seeing which one moved is the most common way an experiment produces a conclusion it cannot support.

## Key facts

- Randomise by user or account for multi-turn and agentic systems; by session only when sessions are independent.
- Per-request randomisation corrupts multi-turn experiments — it splits one conversation across both arms.
- Per-request assignment is acceptable for stateless single-shot calls such as classification.
- Decide the minimum detectable effect and required sample size before the test starts.
- "No significant difference" from an underpowered test means undetectable, not equivalent.
- Peeking and stopping early inflate false positives; fix the stopping rule in the design.
- Statistical significance is not practical significance — judge the effect against cost, latency, and safety.
- Run at least one full business cycle; allow for novelty effects on visible changes.
- One change per experiment unless the design is deliberately factorial.
- Name the primary decision metric, secondaries, and guardrails before launch.

## Exam traps

- Per-request randomisation in a conversational or agentic system.
- Stopping the test the first time the primary metric crosses significance.
- Concluding equivalence from an underpowered result.
- A prompt change and a model change shipped into the same experiment.
- The primary metric chosen or swapped after the results are in.
- A 24-hour run used to decide a change whose traffic pattern varies by day of week.
