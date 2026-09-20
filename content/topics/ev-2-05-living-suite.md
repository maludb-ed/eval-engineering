---
id: ev-2-05
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Keeping the Suite Alive
task-statement: "4.2 Design evaluation datasets and test frameworks using mixed methodologies"
scenarios: [2, 3, 5]
---

## Lesson

An eval suite that never changes stops being useful, and the exam tests the two ways that happens. The first is **saturation**: as scores approach 100%, the suite stops producing improvement signal — every candidate change looks equally good, and the team loses the ability to tell progress from noise. A saturated suite is not a victory to protect, it is a prompt to refresh it with harder tasks drawn from what the system still gets wrong. The second is rot: the product changes, the tasks do not, and the suite starts gating on behaviour nobody wants any more. Both are why evaluation suites are described as **living code requiring ongoing iteration**, with **dedicated ownership** — a named owner, not "the team."

The practice that keeps the suite honest is **reading transcripts regularly**. It verifies that graders are doing what you think they are doing and that failures are fair — a task marked failed because the agent solved the problem in a way the grader did not anticipate is a grader defect, and only transcript review finds it. It is also how grader drift, silent tool errors, and agents exploiting loopholes come to light. The exam treats transcript review as essential practice rather than an optional nicety, and options that replace it with a summary dashboard are not equivalent: aggregate numbers cannot tell you that a pass was earned the wrong way.

Finally, evals are one layer among several. The documented framing is the **Swiss cheese model** — no single evaluation layer catches every issue — and the layers named alongside automated evals are production monitoring, which reveals real behaviour; A/B testing, which validates with real users; user feedback, which surfaces what nobody anticipated; transcript review, which builds intuition; and systematic human studies, which calibrate model graders. The loop closes when production failures become new eval tasks, so the suite grows from incidents rather than from imagination. When a stem asks how to be confident in an agent's quality, the credited answer usually adds a layer rather than perfecting one.

## Key facts

- Saturation: when scores approach 100% the suite yields no improvement signal and should be refreshed with harder tasks.
- Eval suites are living code — they require ongoing iteration and a named owner.
- Read transcripts regularly to verify that graders work correctly and that failures are fair.
- A task failed because the agent found a valid unanticipated solution is a grader defect, discoverable only by reading the transcript.
- Swiss cheese model: no single evaluation layer catches every issue.
- The layers that complement automated evals: production monitoring, A/B testing, user feedback, transcript review, systematic human studies.
- Human studies are what calibrate model-based graders; production monitoring is what reveals real behaviour.
- Production failures should be converted into new eval tasks, so the suite grows from real incidents.

## Exam traps

- Treating a near-100% score as proof of quality rather than as evidence the suite is saturated.
- Deleting or disabling persistently failing tasks to restore a clean dashboard.
- A dashboard or summary report offered as a substitute for reading transcripts.
- Relying on the automated suite alone, with no production monitoring, user feedback, or human review layer.
- Assigning suite maintenance to "the team" rather than a named owner — the source calls for dedicated ownership.
- Building new eval tasks from imagined scenarios while a backlog of real production failures goes unconverted.
