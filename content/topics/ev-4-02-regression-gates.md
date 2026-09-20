---
id: ev-4-02
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Regression Gates and the Shipping Sequence
task-statement: "4.3 Conduct A/B testing and iterative improvements"
scenarios: [2, 4, 6]
---

## Lesson

Once a suite exists it becomes a gate, and the design question is what it is allowed to stop. The workable pattern mirrors any mature test pipeline: a **fast subset runs on every change** for quick feedback, and the **full suite runs before a release**, because a full-parity run with real tools is slow and expensive. What blocks should be narrow and defensible — a regression on the golden set, a safety violation, a failure in an explicitly named category — while soft or subjective signals warn without blocking. A gate that fails builds on vague or noisy criteria teaches the team to bypass it, and a bypassed gate protects nothing; this is the same trust dynamic that governs automated code review.

The sequence a change moves through is worth memorising as a shape: **version → regression suite → offline eval → A/B significance check → canary monitoring → rollback readiness.** One community guide calls this a six-gate pipeline; treat the sequence as sound engineering rather than official exam vocabulary, because the label is not confirmed in Anthropic's own material. What matters is the ordering logic: nothing is compared before it is versioned, nothing reaches users before it passes offline, nothing ramps before a small exposure has been watched, and nothing ships at all without a defined way back. **Rollback readiness is designed before launch** — the reversible artefact, the flag that switches it, the time it takes, and the criteria that trigger it. Deciding those during an incident is how a five-minute revert becomes an hour.

Suites also grow from this loop. **Every production failure becomes a new eval task**, which is what keeps the dataset tracking the real distribution instead of the team's imagination, and what stops the same incident recurring. Two practical cautions: a flaky suite must be stabilised, not silenced — flakiness from model variance is handled by running multiple trials and setting thresholds on the aggregate rather than on a single run, and flakiness from shared state is the M2 harness defect. And gates need maintenance like the rest of the suite, or the pipeline slowly fills with checks nobody trusts.

## Key facts

- Fast subset per change, full suite before release — a legitimate response to eval cost and runtime.
- Block only on narrow, defensible categories: golden-set regressions, safety violations, explicitly named failure categories.
- Soft or subjective signals should warn rather than block; gates that fire on noise get bypassed.
- Shipping sequence: version → regression → offline eval → A/B significance → canary → rollback readiness.
- The "six-gate pipeline" label comes from a community guide, not confirmed Anthropic material; the sequence is what matters.
- Rollback readiness is part of the design: a reversible artefact, a switch, a known revert time, and pre-agreed trigger criteria.
- Convert every production failure into a new eval task so the suite grows from real incidents.
- Model variance in CI is handled with multiple trials and aggregate thresholds, not by disabling the check.

## Exam traps

- Blocking merges on free-form or subjective findings rather than named categories.
- Disabling or deleting a flaky eval to unblock the pipeline, when trials and thresholds are the fix.
- A release process with no defined rollback artefact or trigger, where the plan is to "fix forward."
- Deciding rollback criteria during the incident.
- Treating the regression suite as complete once written, with no route from production incidents back into it.
- Running the full parity suite on every commit and then abandoning it when the cost bites, rather than splitting subset from full run.
