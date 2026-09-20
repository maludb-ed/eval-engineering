---
id: ev-1-02
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Choosing an Accuracy Metric
task-statement: "4.1 Define evaluation metrics (accuracy, latency, cost, safety, security)"
scenarios: [1, 2, 4, 5]
---

## Lesson

"Accuracy" is not a metric, it is a category, and the exam's accuracy items are nearly always **match the metric to the shape of the output**. Categorical output — a sentiment label, a routing class, an intent — is graded by **exact match** after normalising whitespace and case; reaching for a model-based grader here is over-engineering and the distractor knows it. Free text with a reference answer is graded by overlap: **ROUGE-L** for summarisation, which scores the longest common subsequence between candidate and reference and reports an F1 of precision and recall. Consistency across paraphrased inputs is measured by **cosine similarity** over sentence embeddings (SBERT in the documented example) — a FAQ bot that answers the same question three ways scores poorly without anyone judging whether any single answer was wrong. Subjective attributes — empathy, professionalism, tone — go to an **LLM grader on a fixed ordinal scale (Likert 1–5)**, and yes/no determinations like "does this response contain PHI" go to **LLM binary classification**, which catches implicit and paraphrased cases that a regex cannot. In every documented example the grading model is a separate instance from the generating model.

The second thing tested here is that a single aggregate number hides the failure that matters, and **class imbalance** is the standard vehicle. On a claims corpus where 5% of items are fraud, a model that never predicts fraud scores 95% accurate; the exam expects you to reach for precision, recall, and F1 per class, and then to pick which of precision and recall the business actually needs. Those two pull in opposite directions and the scenario decides: a clause extractor that hands counsel a fabricated non-compete is a precision failure, while one that silently misses a real indemnity clause is a recall failure, and only one of those is discovered downstream. Say which you are optimising and what you are trading away — the credited answer usually states the trade-off explicitly rather than promising both.

Third, design for volume. The documented guidance is to prioritise **volume over per-item polish** — more test cases with automated grading beats a handful of hand-graded ones — with 50 to 1,000 cases depending on the task, and to structure outputs so grading can be automated at all. That structuring decision belongs at design time: a model asked to "explain its reasoning and give a verdict" in prose is expensive to grade, while the same model asked for a verdict field plus a reasoning field is gradeable by exact match on one and a rubric on the other. When a stem says grading is too slow or too expensive, the answer is usually to change the output contract, not to hire more reviewers.

## Key facts

- Exact match (with whitespace and case normalisation) is the metric for categorical outputs — classification, routing, intent, structured field values.
- ROUGE-L compares the longest common subsequence against a reference summary and reports an F1 of its precision and recall; it measures overlap with a reference, not truth.
- Cosine similarity over sentence embeddings (e.g. SBERT) measures consistency across paraphrased inputs, not correctness.
- LLM Likert scoring (fixed 1–5 ordinal scale) handles subjective attributes; LLM binary classification handles yes/no determinations such as PHI or policy compliance.
- Use a different model instance for grading than for generation.
- Accuracy alone is misleading under class imbalance; report precision, recall, and F1, and read them per class.
- Precision and recall trade against each other — the business context decides which one the target is set on.
- Documented eval-design principles: be task-specific, automate grading wherever possible, prioritise volume over per-item polish; 50–1,000 test cases is the working range.
- Output structure is an eval decision: separating verdict from reasoning makes the verdict machine-gradeable.

## Exam traps

- Reporting overall accuracy on an imbalanced dataset, or an option that "improves accuracy" without touching the minority class.
- ROUGE-L or embedding similarity offered as a check on factual correctness — both measure overlap with a reference, and a fluent fabrication can score well.
- An LLM judge proposed where exact match or a unit test settles the question — always more expensive and less reproducible than the deterministic option.
- Grading with the same model instance that produced the output.
- "Collect fewer but higher-quality hand-graded examples" when the documented preference is volume with automated grading.
- Promising a gain in precision and recall simultaneously with no change to the system — the credited answer names the trade-off.
