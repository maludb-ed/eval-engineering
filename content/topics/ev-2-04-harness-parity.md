---
id: ev-2-04
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Harness Parity and Clean State
task-statement: "4.2 Design evaluation datasets and test frameworks using mixed methodologies"
scenarios: [4, 5, 6]
---

## Lesson

An eval predicts production only if the thing being evaluated is the thing that runs in production. The documented requirement is that **the agent in the eval functions roughly the same as the agent used in production** — same tools, same scaffold, same context assembly, same model and settings. Teams break this for understandable reasons: they stub the slow tool, they drop the retrieval step to make runs cheaper, they evaluate the prompt in isolation because it is easier to iterate on. Each shortcut moves the eval further from the system whose behaviour you are trying to predict, and the result is the scenario the exam likes to describe — a suite that passes consistently while users report failures. When a stem contains that contradiction, check parity before you question the dataset.

The second requirement is isolation between trials. **Start each trial from a clean environment.** Shared state — leftover files, warm caches, a database row a previous trial created, a session that never closed — produces correlated failures that look exactly like a model regression: several tasks degrade together, the timing matches a deploy, and the obvious conclusion is wrong. This is a named pitfall precisely because the false signal is so convincing. It also cuts the other way: a trial that passes only because a previous trial left the right file in place is a false pass that survives until production, where no previous trial ran.

Parity has a version dimension too. Record what produced each result — model version, prompt version, tool definitions, retrieval index snapshot — because an eval is a comparison and a comparison needs both sides pinned. Without that, a score drop after a model upgrade is indistinguishable from a score drop after an index rebuild, and you have lost the ability to attribute the change. That record is also what makes a production failure replayable as a new eval task later, which is the loop M7 closes. The practical tension the exam sometimes surfaces is cost: a full-parity suite with real tools is slow and expensive, so teams run a fast subset per change and the full suite before a release. That is a legitimate design, and it is different from permanently evaluating a stripped-down agent.

## Key facts

- The eval agent must function roughly the same as the production agent: same tools, scaffold, context assembly, model, and settings.
- Divergence between eval and production scaffolds produces evals that pass while production fails.
- Start every trial from a clean environment; shared state causes correlated failures unrelated to agent performance.
- Leftover files, caches, and database rows can also produce false passes that do not reproduce in production.
- Correlated failures across several tasks at once are a signal to check the harness before concluding the model regressed.
- Pin and record model version, prompt version, tool definitions, and index snapshot with every result.
- A fast subset per change plus the full suite before release is a legitimate cost strategy; permanently evaluating a reduced agent is not.

## Exam traps

- Evaluating a prompt in isolation, with tools or retrieval stubbed, and treating the result as a system-level prediction.
- Concluding "the model regressed" from a run where several unrelated tasks failed together — check for state pollution first.
- Reusing one environment across all trials to save setup time.
- Comparing this week's results with last month's when neither run pinned a model or prompt version.
- Adding tasks or trials to make an unstable suite more reliable when the instability comes from the environment.
