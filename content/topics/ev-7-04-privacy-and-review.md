---
id: ev-7-04
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Log Privacy, Retention, and the Review Loop
task-statement: "4.6 Monitor system performance using logging and observability tools"
scenarios: [1, 2, 4]
---

## Lesson

Full-fidelity traces are the most useful thing an LLM system produces and the most sensitive. Prompts contain whatever users type — account numbers, medical details, contract terms — and retrieved context can pull regulated data into the log even when the user typed nothing sensitive. So the observability design carries a privacy design with it: **redact before storage** rather than filtering on read, since a redaction applied at query time leaves the raw data sitting in the store; classify fields so that a trace can be useful without being complete; and set **retention windows** by purpose, with debugging traces kept short and aggregated metrics kept long. Access control belongs on the trace store like any other system of record, and access to it should itself be logged in regulated contexts.

This is the seam with the Governance domain, and the exam can approach it from either side. Detection of sensitive content is the M1 measurement problem — an LLM binary classifier catches implicit and paraphrased disclosures that keyword filters miss — while the decisions about lawful basis, residency, and retention duration belong to governance and legal. The architect's job is to make the two consistent: a system operating under a regulated regime cannot have a debugging practice that quietly copies regulated data into a third-party observability vendor, and a redaction pipeline that strips so much that failures become unreproducible has traded away the reason the traces exist. Where the platform reports which inference geography served a request, that attribute belongs in the trace precisely because residency questions are asked retrospectively.

The human half of the loop is the practice M2 already named, now on a schedule: **regular transcript review with a named owner**. Sampled traces, anomalies, low-scoring interactions, and any incident get read by a person, and what they find turns into eval tasks, prompt fixes, or tool-interface changes. Two habits make it work: review a stratified sample rather than only the disasters, so the base rate stays visible; and record what was found, so the same defect is not rediscovered quarterly. This is where the whole domain closes — criteria from M1, a suite from M2 and M3, an experiment from M4, a diagnosis from M5, an optimisation from M6, and the production signal that tells you whether any of it held.

## Key facts

- Prompts and retrieved context can both carry regulated data into logs, even when the user typed none.
- Redact before storage; filtering at read time leaves raw sensitive data in the store.
- Set retention by purpose: short for full-fidelity debugging traces, longer for aggregated metrics.
- Access controls on the trace store, with access logging in regulated contexts.
- LLM binary classification detects implicit or paraphrased sensitive content that keyword filters miss.
- Residency and processing-location attributes belong in the trace because they are asked about retrospectively.
- Exporting traces to a third-party observability vendor is a data-processing decision, not just a tooling one.
- Over-redaction defeats the purpose: a trace that cannot reproduce a failure has lost its value.
- Scheduled transcript review, with a named owner, converts production signal into eval tasks and fixes.
- Review a stratified sample, not only failures, and record findings so defects are not rediscovered.

## Exam traps

- Redaction implemented as a view or query filter over stored raw prompts.
- Full prompt and response bodies exported to an external monitoring vendor in a regulated deployment, with no processing assessment.
- One retention period applied to every log class regardless of purpose.
- Regex or keyword filtering relied on as the sole sensitive-data control.
- Transcript review scheduled only after incidents, so nothing is known about normal behaviour.
- Redaction tuned so aggressively that logged failures can no longer be reproduced or replayed as eval tasks.
