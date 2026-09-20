---
id: ev-4-01
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: What Offline Evals and Online Experiments Each Prove
task-statement: "4.3 Conduct A/B testing and iterative improvements"
scenarios: [3, 4, 6]
---

## Lesson

Offline evals and online experiments answer different questions, and most items in this task statement turn on knowing which one a stem is asking for. An **offline eval** runs a fixed dataset through a candidate configuration: fast, cheap, repeatable, and safe, because no user is exposed. What it cannot do is tell you how real people respond, how the real input distribution differs from your dataset, or how the change interacts with everything else in the product. An **A/B test** answers exactly those questions with real users and real traffic, at the cost of time, exposure, and the risk that the variant is worse. The sequence follows from that: offline evals **gate**, online experiments **confirm**. Running an A/B on a change that has not passed offline exposes users to a configuration nobody has measured, and shipping on offline evidence alone assumes your dataset represents production — an assumption M2 spends a whole topic warning about.

The precondition for either being meaningful is **versioning**. A comparison requires both sides pinned: prompt version, model and settings, tool definitions, retrieval index snapshot, and scaffold version, recorded with every result. Without that record, a score change cannot be attributed — a drop after a prompt edit may equally be an index rebuild or a model upgrade that landed the same week — and there is nothing precise to roll back to. Versioning is also what makes a change *reversible*, which is why it sits first in the shipping sequence rather than being treated as hygiene.

The interesting exam case is the gap between the two: an offline gain that does not appear online. That has a small set of causes worth recognising — the eval set no longer reflects the production distribution, the suite is saturated so the gain was noise, the improvement was real but confined to a segment too small to move the aggregate, or the grader rewarded something users do not value. None of those is fixed by running the A/B longer. The credited response is usually to investigate which of those explains the gap, and the most common single answer is that the offline dataset and production have drifted apart.

## Key facts

- Offline evals gate a change; online experiments confirm it with real users. Neither substitutes for the other.
- Offline evals are fast, cheap, repeatable, and expose no users; they cannot capture real user response or distribution shift.
- A/B tests measure real-user impact at the cost of time, exposure, and risk.
- Never A/B a configuration that has not passed offline evaluation first.
- Versioning is the precondition for any comparison: prompt, model and settings, tool definitions, index snapshot, scaffold — recorded per result.
- Unversioned changes cannot be attributed and cannot be precisely rolled back.
- An offline gain that does not appear online usually means dataset drift, a saturated suite, a segment-limited effect, or a grader measuring something users do not value.
- Automated evals sit alongside production monitoring, A/B testing, user feedback, transcript review, and human studies — layered, not ranked.

## Exam traps

- Reaching for an A/B test as the first measurement of an unevaluated change.
- Shipping a user-visible quality change on offline evidence alone, with no online confirmation.
- Comparing this month's results against last quarter's when neither run pinned versions.
- Two changes shipped into one experiment, leaving the result unattributable.
- Extending an A/B test's duration to explain away a gap between offline and online results.
