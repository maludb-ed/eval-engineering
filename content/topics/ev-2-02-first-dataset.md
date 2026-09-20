---
id: ev-2-02
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Building the First Dataset
task-statement: "4.2 Design evaluation datasets and test frameworks using mixed methodologies"
scenarios: [1, 3, 4]
---

## Lesson

The documented advice is to start early and start small: **20–50 simple tasks drawn from real failures** is a great start. This lands against a strong instinct to build a comprehensive suite before measuring anything, and the exam rewards the opposite instinct. Early in a system's life the changes you make have large effects, so a small sample is enough to see them; it is the mature system, where the remaining improvements are small, that needs a large, hard dataset to detect anything at all. The documented working range as a system matures is **50 to 1,000 cases** depending on the task. So "we need several thousand labelled examples before we can evaluate" is the wrong shape of answer at the start of a project and only becomes right much later.

Where the cases come from matters more than how many there are. The documented sources are the failures you already have: convert the manual checks developers run by hand into test cases, and turn user-reported failures into tasks, prioritised by impact. That is the fastest route to a dataset that mirrors the real task distribution, which is the first design principle — an eval is task-specific, and a dataset drawn from a generic benchmark measures something adjacent to your product. Alongside the ordinary distribution, deliberately include **edge cases**: irrelevant or nonexistent inputs, overly long inputs, harmful or poor-quality inputs, ambiguous cases where even human consensus is difficult, and the messy surface features of real traffic — sarcasm, typos, mixed sentiment, misleading titles, implicitly sensitive information. A suite made only of clean, representative cases will pass while production fails.

In practice a team ends up maintaining three sets with different jobs, and mixing them causes trouble. A **golden set** is human-labelled ground truth, small and carefully curated, used to calibrate graders and judges — if you tune a prompt against it you have lost the thing you calibrate with. A **regression set** is the larger automated suite that gates a change, run on every candidate. An **exploratory set** holds the new and hard cases you are actively failing, which graduate into the regression set once the system handles them. Keeping them distinct is what lets you answer two different questions — "is this change safe to ship" and "is the system getting better at the hard part" — without one contaminating the other.

## Key facts

- Start with 20–50 simple tasks drawn from real failures; large effect sizes early make small samples sufficient.
- Mature systems need larger and harder evals to detect the smaller improvements that remain; the documented working range is 50–1,000 cases.
- Prime sources for cases: manual checks developers already run, and user-reported failures, prioritised by impact.
- Evals are task-specific — the dataset should mirror the real-world task distribution, not a generic benchmark's.
- Deliberately include edge cases: irrelevant or nonexistent inputs, overly long inputs, harmful or low-quality inputs, and cases where human consensus is hard.
- Real-traffic surface features belong in the dataset: sarcasm, typos, mixed sentiment, misleading titles, implicitly sensitive information.
- Course framing (not from the source): keep a golden set for calibration, a regression set for gating changes, and an exploratory set for cases you currently fail.
- Tuning against the set you calibrate graders on destroys its value as ground truth.

## Exam traps

- Waiting for a large, complete, perfectly labelled dataset before running any eval — the credited answer starts with tens of real failures.
- A dataset built entirely from synthetic or generated examples, missing the distribution that actually reaches production.
- A dataset assembled from cases the system already handles, which produces a high score and no signal.
- Generic public benchmarks offered as a substitute for a task-specific set.
- Edge cases deferred to "a later phase" when the scenario already describes failures on messy input.
- One combined dataset used for calibration, gating, and development at once.
