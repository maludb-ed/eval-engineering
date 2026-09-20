---
id: ev-3-04
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Calibrating a Judge Against Humans
task-statement: "4.2 Design evaluation datasets and test frameworks using mixed methodologies"
scenarios: [2, 3, 5]
---

## Lesson

A model-based grader is an instrument, and an uncalibrated instrument produces numbers, not evidence. The documented requirement is that **LLM-based rubrics be frequently calibrated against expert human judgment**, particularly for subjective tasks, so that you have confidence there is little divergence between the human grading and the model grading. The mechanism is a **golden set**: a modest set of examples labelled by people who know the domain, held separate from the data used to develop prompts. You run the judge against it, measure agreement, revise the rubric where they diverge, and measure again. Field practice targets **75–90% agreement** with human labels before a judge is trusted at scale.

Before automating at all, there is a cheaper gate: the **intern test**. Have two people grade the same sample independently against your rubric. If they agree 80% or more of the time, the rubric is specific enough to hand to a model. If they do not, the problem is the rubric, and no model will resolve what two humans reading the same words cannot. This is the same defect M2 names for tasks — ambiguity — arriving one layer up, and the fix is the same: rewrite the criterion until it has a yes-or-no answer, rather than instructing the grader to try harder. Disagreement between judge and human is diagnostic too: read the cases where they differ, because they usually cluster on one rubric level whose boundary is undefined.

Calibration is not a one-time gate. Judges drift as the underlying model version changes, as the rubric is edited, and as the input distribution shifts, so recalibration goes on a schedule and is repeated after any change to the judging model or prompt. This has a governance consequence worth carrying into M8: when an eval result is used to justify a launch, the calibration evidence is part of the argument, because a stakeholder is entitled to ask why the grading model's opinion should be believed. And when a judge's scores and the humans' scores diverge after previously agreeing, treat it as a regression in the instrument — investigate before you accept its verdict on the system.

## Key facts

- LLM-based rubrics require frequent calibration against expert human judgment, especially on subjective tasks.
- Calibration runs against a human-labelled golden set kept separate from prompt-development data.
- Field target: 75–90% agreement between judge and human labels before scaling a judge.
- Intern test: if two humans grading independently against the rubric agree 80%+ of the time, it is specific enough to automate.
- Judge/human disagreements usually cluster on one poorly defined rubric boundary — read them rather than averaging them.
- Recalibrate on a schedule and after any change to the judging model, the rubric, or the input distribution.
- A calibration record is part of the evidence when an eval result justifies a launch decision.
- A previously agreeing judge that starts diverging is an instrument regression, not necessarily a system regression.

## Exam traps

- Deploying an LLM judge at scale with no human-labelled comparison — the verdicts are unvalidated opinion.
- Calibrating once at build time and never again, across model upgrades and rubric edits.
- Using the same examples to develop the judge prompt and to measure its agreement.
- Resolving low human-human agreement by adding instructions to the judge instead of rewriting the rubric.
- Reporting a judge score to stakeholders with no statement of how well it tracks expert judgment.
- Treating a sudden judge/human divergence as proof the system regressed, without checking the judge.
