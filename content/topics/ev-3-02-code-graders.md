---
id: ev-3-02
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Code-Based Graders and Outcome Verification
task-statement: "4.2 Design evaluation datasets and test frameworks using mixed methodologies"
scenarios: [1, 2, 6]
---

## Lesson

Code-based grading is where agent evals should start, because the strongest available evidence is usually mechanical: the test suite passed, the row exists, the file changed, the JSON validated against the schema. The technique that distinguishes agent evals from prompt evals is **outcome verification** — querying the environment rather than parsing the response. An agent that reports a completed refund is checked by reading the refund record; a migration agent is checked by running the target project's tests, not by reading its summary of what it changed. Alongside it sits **tool-call verification**: asserting that the right tool was called, with the right arguments, in a defensible order — which catches the agent that produced an acceptable answer through an unacceptable route, such as reading a customer record it had no reason to open.

The complementary technique is **transcript analysis** — mechanical checks over the trace rather than the environment. Turn count, token usage, retry count, whether an error path was hit, whether a forbidden tool appeared. These are cheap assertions and they carry the cost and efficiency axes from M1 into the eval itself, which is how a suite detects that a change made the agent correct but three times more expensive. Schema validation belongs in the same family: when output feeds a downstream system, "parses and conforms" is a separate assertion from "is right," and separating them tells you whether a failure is a formatting regression or a reasoning one.

The limitation is the one the family is named for: brittleness. A code-based grader rejects valid variation, and the documented illustration is grading rigidity in its purest form — rejecting `96.12` when the reference said `96.124991…`. The defences are normalisation (whitespace, case, numeric tolerance, key order), asserting on the property rather than the literal, and **partial credit** on multi-component tasks so an agent that completed four of five steps is not scored identically to one that did nothing. Partial credit is documented guidance, and it matters practically: a suite that only reports pass/fail on composite tasks cannot show progress, so improvements look like noise and the team loses its signal.

## Key facts

- Code-based techniques: string and exact match, binary tests, static analysis, schema validation, outcome verification, tool-call verification, transcript analysis.
- Outcome verification queries the environment for the final state instead of trusting the agent's report.
- Tool-call verification asserts the right tool, arguments, and sequence — catching right answers reached by unacceptable routes.
- Transcript analysis supplies mechanical cost and efficiency assertions: turn count, token usage, retries, forbidden-tool checks.
- Schema validation is a separate assertion from correctness; keeping them apart distinguishes format regressions from reasoning failures.
- Grading rigidity is a named pitfall — rejecting `96.12` against a reference of `96.124991…` is the documented example.
- Defences against brittleness: normalisation, numeric tolerance, asserting the property rather than the literal string.
- Build partial credit into multi-component tasks; all-or-nothing scoring hides real progress.

## Exam traps

- A grader that scores the agent's summary of what it did rather than the state of the system it acted on.
- Exact string matching on free-text output, with no normalisation or tolerance, presented as rigour.
- Loosening a correctness threshold to stop valid answers failing, when normalisation or tolerance is the actual fix.
- All-or-nothing scoring on a five-step task, then concluding from a flat score that a change had no effect.
- Ignoring tool-call verification when the scenario describes an agent taking an unauthorised or wasteful path to a correct answer.
