---
id: ev-8-01
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Eval Evidence in Governance and Risk
task-statement: "Cross-domain — evaluation evidence supporting Governance, Safety & Risk Management"
scenarios: [1, 2, 4]
---

## Lesson

Governance decisions are supposed to rest on measurements, and this domain supplies them. Three connections carry most of the exam weight. First, **safety and security evals are eval design applied to adversarial inputs**: a dataset of jailbreak attempts, prompt-injection payloads, and requests that should be refused, graded for both over- and under-refusal, run as a standing suite rather than a pre-launch exercise. Red-teaming produces the cases; the suite is what stops them regressing. Second, **human-in-the-loop placement is an argument from measured error severity**, not from general caution — the M1 criterion "90% of errors cause inconvenience rather than critical failure" is precisely the evidence that decides whether a step can run unattended, and the consistency metric that belongs with it is pass^k, because an unattended irreversible action needs every run to be right rather than one of them.

Third, evals are the **evidence pack** when someone must approve a system. A defensible pack states what was measured, on what dataset, with which graders, how those graders were calibrated, what the residual failure modes are, and what production monitoring will catch that the offline suite cannot. The calibration record matters here more than anywhere else: an approver is entitled to ask why a grading model's opinion should be believed, and "75–90% agreement with expert labels on a held-out golden set" is an answer while "we used an LLM judge" is not. Where a regime requires it, the same pack supports audit — versioned datasets, retained results, and the traceability from a claim to the run that produced it.

The boundary is worth stating plainly, because exam items test it from both sides. This domain decides **how something is measured and what the numbers mean**; the governance domain decides **what is permitted, by whom, under which regime, and with what retention**. An architect who answers a residency or lawful-basis question with an eval design has crossed the line, and one who answers "how do we know the guardrail works" with a policy document has crossed it the other way. Keep them consistent: a control that cannot be measured cannot be shown to work, and a measurement with no policy behind it changes nothing.

## Key facts

- Safety and security evals are ordinary eval design applied to adversarial inputs: refusal correctness, jailbreak and injection resistance, leakage.
- Grade both over-refusal and under-refusal; driving unsafe output to zero by refusing broadly fails the criterion.
- Red-teaming supplies cases; the standing suite is what prevents regression.
- Human-in-the-loop placement is justified by measured error severity, not by general caution.
- Unattended irreversible actions are a pass^k requirement, not a pass@k one.
- An evidence pack states: what was measured, on what data, with which graders, how calibrated, residual failure modes, and what monitoring covers the gap.
- Grader calibration evidence is what makes a model-graded result admissible to an approver.
- Audit support needs versioned datasets, retained results, and traceability from claim to run.
- This domain owns how things are measured; governance owns what is permitted, by whom, and for how long.

## Exam traps

- Safety assessed once before launch rather than maintained as a standing suite.
- A human review step added or removed on intuition, with no error-severity measurement behind the decision.
- pass@k cited as evidence for an unattended, irreversible workflow.
- An eval result presented to an approver with no statement of grader calibration or known blind spots.
- Answering a jurisdictional, residency, or lawful-basis question with an evaluation design.
- Claiming a guardrail works on the strength of policy documentation, with no measurement of it.
