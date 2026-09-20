---
id: ev-5-05
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Tool, Context, and Agent-Loop Failures
task-statement: "4.4 Diagnose system issues (prompt failure, hallucinations, model mismatch)"
scenarios: [3, 5, 6]
---

## Lesson

Agentic systems add three failure families that prompt-level debugging never reaches. **Tool failures** usually trace to the tool's interface rather than the model: a description that does not make clear when the tool applies, parameters whose names or types invite the wrong value, two tools whose purposes overlap so selection is a coin flip, or an error that returns as an empty result instead of a structured message. That last one matters most — an agent that receives silence cannot recover, so it guesses, and the guess reaches the user as a confident wrong answer. The diagnostic evidence is in the trace: which tool was called, with what arguments, what came back. Tool-call verification from M3 turns that evidence into an assertion so the failure is caught by the suite rather than by a customer.

**Context failures** are the second family. The information was retrieved or produced and then lost: truncated when the window filled, buried mid-context where attention is weakest, or dropped by a summarisation or compaction step that discarded a detail it judged unimportant — a decision from six turns ago, a constraint the user stated once. These present as an agent that "forgets" mid-task or contradicts something established earlier, and no amount of prompt rewriting fixes them, because the text is simply not there any more. The check is to inspect the assembled context at the failing turn rather than the prompt template that was supposed to produce it.

**Loop failures** are the third. An agent repeats the same action because nothing in its state marks the attempt as already tried; it stops early, reporting partial work as complete; it burns turns exploring without converging; or it hits an unrecoverable tool error and neither retries sensibly nor escalates. These are measured with the transcript assertions M3 named — turn count, repeated identical calls, stop reason — and their fixes are scaffold-level: state tracking, turn and budget limits, explicit escalation paths, and a defined terminal condition. Two cost-shaped variants belong here too, because they are failures even when the outcome is correct: an agent that reaches the right answer in forty turns instead of six, and one that re-reads the same document on every turn. Both pass an outcome-only grader, which is precisely why cost and turn assertions exist.

## Key facts

- Tool failures usually originate in the tool interface: ambiguous descriptions, misleading parameter names or types, overlapping tools, unstructured errors.
- Errors returned as empty results leave an agent unable to recover; structured error messages let it retry or escalate.
- Tool-call verification converts trace evidence into a standing assertion.
- Context failures: window truncation, information buried mid-context, and summarisation or compaction discarding state.
- Context failures present as forgetting or self-contradiction and are not fixable by prompt wording.
- Inspect the assembled context at the failing turn, not the template that generated it.
- Loop failures: repeated identical actions, premature stop, non-convergence, unrecoverable tool errors with no escalation path.
- Scaffold-level fixes: state tracking, turn and budget limits, explicit escalation, defined terminal conditions.
- Correct-but-wasteful trajectories are failures on the cost axis and pass an outcome-only grader.

## Exam traps

- Prompt edits offered for an agent that contradicts something established earlier in a long session — the context was dropped, not misread.
- Raising the model tier for an agent looping on the same tool call.
- Tools returning empty results on error, with the fix framed as instructing the model to handle failures gracefully.
- An outcome-only grader on an agent whose cost per task has tripled while accuracy held.
- Adding more tools when the trace shows the agent choosing wrongly between two that already overlap.
- Increasing the turn limit for an agent that is not converging, with no state tracking or terminal condition added.
