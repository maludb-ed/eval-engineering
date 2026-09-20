---
id: ev-1-01
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Success Criteria That Hold Up
task-statement: "4.1 Define evaluation metrics (accuracy, latency, cost, safety, security)"
scenarios: [1, 3, 5]
---

## Lesson

Every item in this domain starts from the same failure: a stakeholder says the system "feels worse," or "needs to be more accurate," and there is no number that settles the argument. Anthropic's documented properties of good success criteria are four — **specific, measurable, achievable, relevant** — and the exam tests each one as a separate failure mode. *Specific* replaces "good performance" with "accurate sentiment classification." *Measurable* means a quantitative metric or a well-defined qualitative scale; the docs allow qualitative measures, but only when they are applied consistently and alongside quantitative ones, which in practice means a rubric with fixed levels, not an adjective. *Achievable* means the target is benchmarked against industry results, prior experiments, or demonstrated frontier-model capability — a target of 100% accuracy on an ambiguous classification task is not a stretch goal, it is a broken criterion. *Relevant* means the criterion earns its place in this application: strict citation accuracy is critical for a medical assistant and mostly overhead for a casual chatbot. Note what is absent — the docs never use the acronym SMART, and there is no "time-bound" property. Distractors are written from the source's wording, so borrow the source's wording.

The second half of the topic is that production systems are almost never single-criterion. The documented criteria categories span task fidelity, consistency, relevance and coherence, tone and style, privacy preservation, context utilisation, latency, and price, and a realistic target set names several at once: F1 at or above 0.85, **and** at least 99.5% of outputs non-toxic, **and** 95% of responses under 200 ms, **and** an error profile where 90% of errors cause inconvenience rather than critical failure. That last one is the pattern candidates miss — **error severity is itself a criterion**, not a footnote, because two systems with identical accuracy can have completely different risk profiles. When a stem gives you a business context and asks what to measure, the credited answer usually names more than one axis, and the single-axis option is the trap.

The third idea is procedural and easy to score: thresholds are **criterion-referenced** — fixed in advance against a standard, never curved to whatever the system happened to produce. A threshold chosen after seeing the results is not a criterion, it is a description. Each criterion also needs a stated measurement method and an owner; "we'll track accuracy" with no named metric, no dataset, and nobody responsible is the state the exam expects you to fix. Getting this right is what turns "the agent feels worse" into a hill the whole team can climb.

## Key facts

- The four documented properties of good success criteria are specific, measurable, achievable, relevant — four, not five; "time-bound" is not among them and the acronym SMART does not appear in the source.
- Measurable permits well-defined qualitative scales, but only applied consistently and alongside quantitative measures.
- Achievable means benchmarked against industry benchmarks, prior experiments, or current frontier-model capability — not aspiration.
- Relevant means tied to this application's purpose: citation accuracy is critical for a medical app, far less so for a casual chatbot.
- Documented criteria categories: task fidelity, consistency, relevance and coherence, tone and style, privacy preservation, context utilisation, latency, price.
- Most production use cases require several criteria evaluated simultaneously — a multidimensional target set, e.g. F1 ≥ 0.85 AND ≥ 99.5% non-toxic AND 95% under 200 ms.
- Error severity is a first-class criterion: "90% of errors cause inconvenience rather than critical failure" is a target, not commentary.
- Thresholds are criterion-referenced — set against a fixed standard before the run, never adjusted to match the observed result.
- Each criterion carries a metric, a measurement method, a dataset, and a named owner.

## Exam traps

- A target that names one axis only (usually accuracy) when the scenario states a cost, latency, or safety constraint — the credited answer names the constraint the stem planted.
- "Time-bound" offered as one of the properties of good success criteria, or the answer that frames them as the SMART framework — the source lists four properties and never uses that label.
- Targets like "100% accuracy" or "zero hallucinations" — unachievable against demonstrated model capability, so the criterion is broken regardless of how important the use case is.
- Setting the threshold after reviewing the first eval run, or moving it when the system misses — this is the criterion-referenced trap and the option always sounds pragmatic.
- Qualitative criteria stated as adjectives ("professional", "helpful", "concise") with no scale — measurable requires a defined scale applied consistently, not a better adjective.
