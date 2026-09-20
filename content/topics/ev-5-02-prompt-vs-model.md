---
id: ev-5-02
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Prompt Failure or Model Mismatch
task-statement: "4.4 Diagnose system issues (prompt failure, hallucinations, model mismatch)"
scenarios: [2, 3, 6]
---

## Lesson

Two of the three failure types named in the task statement look alike from the outside — the output is wrong — and the distinguishing evidence is what the exam tests. A **prompt failure** shows up as inconsistency and instruction-following gaps: the model does the right thing sometimes and not others, ignores a constraint that appears late in a long instruction block, interprets a vague qualifier differently on each run, or produces the right content in the wrong shape. The tell is that the information needed was present and the model had the capability; what was missing was specificity. The fixes are the ones M1 already trained: replace subjective qualifiers with categorical criteria, add few-shot examples that demonstrate the boundary cases, define the output contract explicitly, and put the constraint where it will actually be read.

A **model mismatch** shows up differently: the failures cluster on the hardest instances rather than scattering, they persist across prompt rewrites, and they involve reasoning depth, long-range consistency, or multi-step planning rather than formatting or compliance. The honest test is the one people skip — run the same prompt on a larger model or with extended thinking enabled and see whether the failures disappear. If they do, it is capability; if they do not, the prompt or another layer is at fault and you have just avoided paying more for nothing. Mismatch runs in both directions: an oversized model on a trivial classification task is also a mismatch, wasting cost and latency for accuracy the task did not need, which is why M6 treats tiering as an optimisation lever rather than a one-way ratchet.

Two failure modes sit between the categories and are worth naming so they are not misfiled. **Missing context** is not a prompt failure in the usual sense — the instructions may be perfect while the information the model needed never arrived, which is a retrieval or context-assembly defect. And **conflicting instructions** — a system prompt and a tool description that disagree, or guidance accumulated over months that now contradicts itself — produce erratic behaviour that looks like model unreliability and is fixed by reconciliation, not escalation. Reading the assembled context exactly as the model received it settles all three cases faster than reasoning about them.

## Key facts

- Prompt failure signals: run-to-run inconsistency, ignored constraints, vague qualifiers interpreted differently, right content in the wrong format.
- Prompt fixes: categorical criteria in place of subjective qualifiers, few-shot examples covering boundary cases, an explicit output contract.
- Model mismatch signals: failures concentrated on the hardest instances, persistence across prompt rewrites, reasoning-depth or multi-step planning demands.
- The isolating test for mismatch: hold the prompt fixed, raise the model tier or increase thinking effort, and see whether the failures resolve.
- Mismatch also runs downward — an oversized model on a simple task wastes cost and latency without accuracy gain.
- Missing context is a retrieval or context-assembly defect, not a prompt-wording defect.
- Conflicting instructions across system prompt, tool descriptions, and accumulated guidance produce erratic behaviour that resembles model unreliability.
- Inspecting the fully assembled context as the model received it distinguishes these cases quickly.

## Exam traps

- Escalating the model tier for failures that are inconsistent run to run — that pattern points at the prompt.
- Rewriting the prompt repeatedly for failures concentrated on the genuinely hardest cases.
- Adding emphatic adjectives ("be accurate", "be careful") in place of explicit criteria.
- Treating a context-assembly gap as a prompt problem because both are "in the prompt".
- Keeping a larger model after it failed to change the outcome, on the theory that it cannot hurt.
- Ignoring downward mismatch: a frontier model retained for a task a small model handles at a fraction of the cost.
