---
id: ev-5-01
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: The Diagnostic Method
task-statement: "4.4 Diagnose system issues (prompt failure, hallucinations, model mismatch)"
scenarios: [1, 4, 5]
---

## Lesson

The organising heuristic for this task statement is short: **trace the symptom to the component that changed.** Production LLM systems have many moving parts — prompt, model, tools, retrieval corpus and index, context assembly, scaffold — and when quality drops, the component that changed most recently is the first hypothesis, not the component that is easiest to modify. The reason this needs teaching is that prompts and models are the easiest things to modify, so the instinctive response to almost any failure is to rewrite the prompt or upgrade the model. The exam is built to punish that reflex: a large share of its diagnosis items describe a change somewhere else in the pipeline and then offer a prompt fix as the plausible-sounding distractor.

The method has three moves. **Locate the change**: what shipped, what was rebuilt, what upstream data was refreshed, what version moved — which is only answerable if the versioning discipline from M4 is in place. **Read the transcripts**: aggregate metrics tell you something is wrong, traces tell you what the system actually did — which tool it called, what came back, what context it had, where the reasoning left the rails. A handful of failing traces usually identifies the layer faster than any dashboard. **Ablate one layer at a time** against a fixed eval set: hold everything constant, revert or swap a single component, and see whether the score returns. Changing two things at once reproduces the M4 attribution problem inside your own debugging.

Two disciplines make the method reliable. First, **state the evidence that would distinguish your hypotheses** before you start changing things — if it is a retrieval failure the retrieved chunks will not contain the answer; if it is a prompt failure they will, and the model will still answer wrongly. That single check separates two hypotheses that produce identical user-visible symptoms. Second, **resist compound fixes**. Shipping a new prompt, a bigger model, and a reranker together may restore quality without teaching you anything, and it leaves you paying for two of the three forever. When a stem describes a team that fixed a problem but cannot say why, the credited answer is nearly always the isolating experiment they skipped.

## Key facts

- First hypothesis for a quality drop is the component that changed most recently, not the component that is easiest to edit.
- Locating the change requires version records for prompt, model, tools, index, and scaffold.
- Transcripts identify the failing layer; aggregate metrics only detect that a failure exists.
- Ablate one layer at a time against a fixed eval set; a fixed dataset is what makes the comparison valid.
- Name the evidence that would distinguish competing hypotheses before making changes — e.g. do the retrieved chunks contain the answer.
- Identical user-visible symptoms can come from different layers; the distinguishing evidence lives in the trace.
- Compound fixes restore quality without attribution and leave permanent unnecessary cost.
- Diagnosis feeds the suite: the confirmed failure becomes an eval task so the same regression is caught next time.

## Exam traps

- Rewriting the prompt as the first response to a failure that followed an infrastructure, data, or index change.
- Upgrading to a larger model before isolating which layer is failing.
- Shipping several fixes simultaneously and declaring the issue resolved.
- Diagnosing from dashboards alone when the scenario describes behaviour no metric distinguishes.
- Ablating against a dataset that is also being changed, so the comparison proves nothing.
