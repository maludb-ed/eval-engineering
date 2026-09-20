---
id: ev-2-03
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Unambiguous, Solvable, Balanced Tasks
task-statement: "4.2 Design evaluation datasets and test frameworks using mixed methodologies"
scenarios: [1, 2, 6]
---

## Lesson

A task is only useful if it has one defensible verdict, and the documented test for that is blunt: **two domain experts, working independently, reach the same pass/fail decision**. If they disagree, the task is ambiguous and every number computed from it is noise — including the numbers you use to justify a model change. Ambiguous specifications are named as the most common defect in eval sets, and the diagnostic is memorable: a frontier model scoring **0% at pass@100** almost always means the task is broken, not that the agent is incapable. The companion discipline is to write a **reference solution** proving the task is solvable as specified. Writing one surfaces the missing constraint, the impossible threshold, or the requirement two people read differently, before the task pollutes a suite.

The second property is balance. A set of only positive cases — inputs where the behaviour *should* fire — cannot detect the failure mode where the system fires on everything. Include the negative cases: claims that should not be escalated, documents that contain no indemnity clause, questions the assistant should decline. Class imbalance quietly inflates scores in the same way it does in M1's accuracy metrics: on a corpus that is 5% positive, a system that never fires looks excellent, and a system that fires constantly looks fine on the positive subset. The exam's version of this is an option that proposes adding more examples of the behaviour you want, when what the suite is missing is examples of when the behaviour should not occur.

Third, eval sets have bugs, and they fail in a distinctive way: a task with an impossible threshold or a misconfigured success criterion reports failure forever, no matter what you change. When a task never passes across model upgrades, prompt rewrites, and scaffold changes, suspect the task. This is why the fix for a suite that produces unreliable numbers is almost never "add more tasks" — volume does not dilute ambiguity, it multiplies it, and a thousand cases with contested verdicts is a thousand arguments. Repair the tasks, then scale.

## Key facts

- The test for an unambiguous task: two domain experts independently reach the same pass/fail verdict.
- Write a reference solution for each task to prove it is solvable as specified.
- A frontier model at 0% pass@100 usually indicates a broken or impossible task rather than an incapable agent.
- Balanced sets include negative cases — scenarios where the behaviour should *not* occur — not only positive ones.
- Class imbalance inflates aggregate scores and hides the dominant failure mode.
- Eval bugs are a named pitfall: impossible thresholds and misconfigured success criteria produce permanent, uninformative failure.
- Adding test cases does not fix ambiguity; it scales it.
- Ambiguity is discovered by having a second person grade a sample independently, not by re-reading your own criteria.

## Exam traps

- "Add more test cases" offered for a suite whose results the team does not trust — the defect is task quality, not volume.
- Blaming the model for a task nothing has ever passed.
- A suite of only positive examples, with an option to add still more of them.
- Aggregate pass rates quoted on an imbalanced set with no per-class breakdown.
- Resolving expert disagreement by having the more senior expert decide, rather than rewriting the task so the disagreement cannot recur.
- Treating a task that two reviewers grade differently as a model consistency problem.
