---
id: ev-8-03
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Reporting Results and Owning the Practice
task-statement: "Cross-domain — evaluation evidence supporting Stakeholder Communication, Lifecycle and Enablement"
scenarios: [1, 3, 5]
---

## Lesson

An eval result becomes useful when a decision-maker can act on it, which puts real weight on how it is reported. An honest report states the number **with its scope**: what was measured, on how many tasks and trials, against which dataset, and — critically — what the number does not cover. A 92% task-fidelity score on a 200-task suite drawn from last quarter's traffic is a different claim from "the system is 92% accurate," and the second invites a stakeholder to generalise it to cases you never tested. Report variance alongside the point estimate, name the known blind spots, and distinguish a measured result from a projection. The credibility this buys is what lets you be believed later, when the number goes the other way.

Expectation-setting is the adjacent skill. LLM systems have an accuracy ceiling on ambiguous tasks, and a stakeholder promised a number the system cannot reach will experience every release as a failure. The productive framing is the multi-axis target from M1 translated into business terms — what the system will do, how often, at what cost, with what escalation path for the remainder — and an **SLA you can actually measure**, expressed in percentiles and rates with a defined measurement window and a named source of truth. An SLA whose terms your telemetry cannot evaluate is a commitment you cannot demonstrate you met.

Finally, the practice needs an owner and a home in the team's workflow. The suite runs in CI so quality is checked on every change; the fast subset gates a merge and the full run precedes a release; failures produce a readable report rather than a wall of output; and one named person owns the suite as living code — refreshing saturated tasks, retiring stale ones, folding in incidents, recalibrating judges. Where the organisation runs several Claude systems, this is where shared assets pay: a common harness, a scenario library, calibrated judges, and the conventions that let one team's eval be read by another. The domain ends where it started — a measurement nobody owns decays into a number nobody believes.

## Key facts

- Report the number with its scope: what was measured, on how many tasks and trials, against which dataset, and what it excludes.
- Report variance alongside the point estimate, and separate measured results from projections.
- Name known blind spots explicitly rather than leaving them to be discovered.
- Set expectations against the multi-axis target: what the system does, how often, at what cost, with what escalation path.
- An SLA must be measurable by existing telemetry, in percentiles and rates, with a defined window and source of truth.
- Run the suite in CI: fast subset gates a merge, full run precedes a release.
- Eval failures should produce a readable report, not raw output.
- One named owner maintains the suite: refreshing saturated tasks, retiring stale ones, folding in incidents, recalibrating judges.
- Shared harnesses, scenario libraries, and calibrated judges are how eval practice scales across teams.

## Exam traps

- A headline accuracy figure reported with no dataset, scope, or trial count.
- Committing to an SLA the available telemetry cannot evaluate.
- Promising an accuracy level above what the task's ambiguity permits, to secure approval.
- A point estimate from a small suite presented without variance or blind spots.
- Eval ownership assigned to "the team", leaving saturation, staleness, and recalibration to nobody.
- Quality gates that block on unreadable output, which teams learn to bypass.
