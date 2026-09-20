---
id: ev-1-03
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Consistency Metrics — pass@k and pass^k
task-statement: "4.1 Define evaluation metrics (accuracy, latency, cost, safety, security)"
scenarios: [3, 5, 6]
---

## Lesson

Model outputs vary between runs, so a task is not scored once, it is run several times — each attempt is a **trial** — and the two metrics that summarise those trials answer opposite questions. **pass@k** is the probability that at least one of k attempts succeeds: the metric for "one success is enough." **pass^k** is the probability that *all* k attempts succeed: the metric for "this has to work every time." At k=1 they are the same number. As k rises they diverge sharply — pass@k climbs toward 100% while pass^k falls — which means the same eval run can be reported as a triumph or a failure depending on which one you quote. The exam tests whether you pick the metric from the product requirement rather than from the result.

Mapping is the skill. A migration agent that opens a pull request a human reviews and can re-run is a **pass@k** system: one good attempt out of three is a win, and the cost of a retry is a few cents. An unattended agent that issues a refund, sends a customer email, or merges to main is a **pass^k** system: the ninth run that hallucinates an amount is not averaged away by eight good ones, because it already happened. Anything irreversible, anything unsupervised, anything a customer sees without a human in between — consistency is the requirement, so the consistency metric is the one that gates the launch. The corollary is that adding retries improves pass@k and does nothing for pass^k, which is why "just retry on failure" is a credited answer in one architecture and a trap in the other.

Two reporting disciplines come with this. First, **always state k**: a pass rate with no trial count is uninterpretable, and comparisons across different k are meaningless. Second, a **single-trial comparison between two prompts proves nothing** — the difference you are looking at may be run-to-run variance. When a stem shows someone declaring victory after one run each on the old and new prompt, the defect is the experimental design, not the prompt. This is also where consistency as a documented success criterion meets its metric: "similar responses to similar inputs" is measured across trials, not within one.

## Key facts

- A trial is one attempt at a task; multiple trials per task absorb run-to-run model variance.
- pass@k = probability that at least one of k attempts succeeds — use when one success matters and retries are cheap and safe.
- pass^k = probability that all k attempts succeed — use when consistency is essential: unattended, irreversible, or customer-facing actions.
- The two are identical at k=1 and diverge as k grows; by k=10 pass@k approaches 100% while pass^k falls sharply.
- Retries raise pass@k and leave pass^k unchanged.
- Always report k alongside the score; pass rates at different k are not comparable.
- Single-trial comparisons between two configurations are indistinguishable from variance.
- Mature systems need more trials and harder tasks to detect the smaller improvements that remain.

## Exam traps

- pass@k quoted for an unattended or irreversible workflow — the classic wrong-metric item; the consistency requirement demands pass^k.
- Raising k and reporting the improved pass@k as a quality gain.
- A pass rate reported with no k, or two results compared that were measured at different k.
- Declaring a prompt change an improvement from one run of each.
- "Add automatic retries" offered as the fix for an agent that must be right the first time — it moves the metric that is not the requirement.
