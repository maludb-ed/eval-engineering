---
id: ev-5-03
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Hallucination and Groundedness
task-statement: "4.4 Diagnose system issues (prompt failure, hallucinations, model mismatch)"
scenarios: [4, 5, 2]
---

## Lesson

Hallucination is the failure where output is fluent, confident, and unsupported — and diagnosing it starts with distinguishing two cases that need different fixes. **Ungrounded generation** happens when supporting material was available and the model departed from it: the retrieved passage says one thing, the answer says another, or the answer adds specifics the source never contained. **Unsupported generation** happens when nothing relevant was available at all and the model answered anyway rather than declining. The first is a grounding problem; the second is frequently a retrieval problem wearing a hallucination costume, which is why M5's method insists on checking what was actually in context before blaming the model.

The documented mitigations are specific: **allow uncertainty** — explicitly permit the model to say it does not know, which removes the pressure to produce an answer at any cost; **ground responses in direct quotes** from the source material, so the answer is anchored to text that exists; and **verify claims with citations**, so each assertion is traceable to a source a reader or a grader can check. For research-style work the measurable properties are groundedness (is each claim supported by its cited source), coverage (are the key facts present), and source quality. A verification pass — a second call that checks the draft's claims against the sources — raises reliability and costs another inference, which is exactly the kind of trade M1 expects you to state rather than hide.

Measurement follows the same split. A single accuracy number cannot tell you whether a system is hallucinating; groundedness is graded per claim, usually by an LLM binary judge asking "is this claim supported by the provided source," with the escape hatch and calibration M3 requires. Two exam-relevant subtleties: citations can themselves be fabricated, so a citation-checking grader must verify the cited source exists and contains the claim, not merely that a citation is present; and a judge grading with access to the source is measuring something different from one grading on world knowledge. Finally, hallucination rate is a **monitored rate**, not a launch checkbox — it moves when the corpus changes, when the model is upgraded, and when users start asking new kinds of questions.

## Key facts

- Distinguish ungrounded generation (the source was present, the answer departed from it) from unsupported generation (nothing relevant was retrieved and the model answered regardless).
- Documented mitigations: allow uncertainty, ground responses in direct quotes, verify claims with citations.
- Research-agent quality splits into groundedness, coverage, and source quality.
- A verification pass improves reliability at the cost of an additional inference — state the trade.
- Groundedness is graded per claim, typically by an LLM binary judge with an abstain option, calibrated against humans.
- Citation presence is not citation validity: verify the cited source exists and actually contains the claim.
- Grading with the source in context measures faithfulness; grading without it measures world knowledge.
- Hallucination rate is a continuously monitored metric that moves with corpus, model, and query-mix changes.

## Exam traps

- "Instruct the model to be accurate and not make things up" offered as the mitigation — an adjective, not a control.
- Treating every wrong confident answer as a model hallucination without checking what was in context.
- Counting citations as evidence of groundedness without validating them.
- A single end-to-end accuracy metric used to track hallucination.
- Lowering temperature presented as a hallucination fix.
- Hallucination assessed once before launch and never monitored afterwards.
