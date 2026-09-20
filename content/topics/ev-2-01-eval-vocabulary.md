---
id: ev-2-01
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: The Vocabulary of an Eval Run
task-statement: "4.2 Design evaluation datasets and test frameworks using mixed methodologies"
scenarios: [3, 5, 6]
---

## Lesson

This domain has a precise vocabulary and several distractors are built by swapping two of its terms. A **task** is a single test with defined inputs and success criteria. A **trial** is one attempt at that task; you run several because model outputs vary between runs. A **grader** is the logic that scores the attempt, and one task can carry several graders or assertions — correctness, format, and cost can each be checked separately on the same trial. The **transcript** (or trace) is the complete record of a trial: outputs, tool calls, reasoning, intermediate results. The **outcome** is the final state of the environment. And two different pieces of infrastructure share a similar name: the **evaluation harness** is what runs evals end to end, while the **agent harness** or **scaffold** is the system that gives the model agency — its tools, its loop, its context assembly. The scaffold is the thing under test; the eval harness is the thing doing the testing. An option that proposes "fixing the harness" is only credited when the stem makes clear which one it means.

The distinction that carries the most exam weight is **transcript versus outcome**. A transcript records what the agent said it did; an outcome records what actually happened. The canonical illustration is a booking agent whose transcript reads like a clean success — it called the reservation tool, it reported a confirmation number — while the database contains no reservation. A grader that reads only the transcript passes that trial. A grader that checks the environment fails it, which is the correct result. So outcome verification is the backbone of agent evals, and transcripts are what you read to understand *why* a failure happened: how many turns it burned, which tool it misused, where the reasoning went sideways. Both matter, and they answer different questions — transcripts tell you how the agent works, outcomes tell you what it actually did.

Getting the words right is not pedantry, it is how you parse a stem under time pressure. "Our eval passes but customers still report failures" points at a grader reading transcripts instead of outcomes. "Results are inconsistent between runs" points at trials and variance. "The eval agent behaves differently from production" points at scaffold parity, not at the dataset. Each phrase maps to a different fix, and the exam expects you to route the symptom to the right layer rather than proposing a general improvement.

## Key facts

- Task: one test with defined inputs and success criteria. Trial: one attempt at a task.
- Multiple trials per task exist to absorb run-to-run variance in model outputs.
- A single task can carry multiple graders or assertions scoring different properties of the same trial.
- Transcript (trace): the complete record of a trial — outputs, tool calls, reasoning, intermediate results.
- Outcome: the final state of the environment, independent of what the agent claimed.
- Evaluation harness: infrastructure that runs evals end to end. Agent harness / scaffold: the system that gives the model agency — the system under test.
- Outcome verification is the backbone of agent evals; transcript review explains failures rather than scoring them.
- An agent can report success in its transcript while the environment shows the action never took effect.

## Exam traps

- A grader that scores the agent's own claim of success — the transcript-versus-outcome swap, and the most common distractor in this module.
- Options using "harness" ambiguously, where one reading means the eval runner and the other means the agent scaffold.
- Treating transcript review as a scoring mechanism rather than a diagnostic one.
- Scoring a task from a single trial and treating the result as the task's score.
- Assuming one task means one grader, so a format failure and a correctness failure cannot be distinguished.
