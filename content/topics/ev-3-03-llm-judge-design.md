---
id: ev-3-03
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Designing an LLM Judge
task-statement: "4.2 Design evaluation datasets and test frameworks using mixed methodologies"
scenarios: [3, 4, 5]
---

## Lesson

When the property being graded is genuinely open-ended, the grader becomes a model, and the documented design rules are specific. First, **one isolated judge per rubric dimension**: create clear, structured rubrics that grade each dimension of a task, and grade each with its own judge rather than asking a single judge to score everything at once. A judge asked to rate accuracy, tone, completeness, and safety in one call produces correlated scores — a well-written answer scores high on all four — and you lose the ability to see that the system is polite and wrong. Second, give the judge an **escape hatch**: an explicit option to return "Unknown" when the evidence does not support a verdict. Without one, a judge forced to choose will fabricate a confident answer, and hallucinated verdicts are worse than missing ones because they look like data.

Third, the judge is a **different model instance from the one being evaluated**, which the documented examples follow throughout. Beyond the obvious self-preference concern, it keeps a model's failure from being scored by the same failure. Fourth, choose the judging format that fits the question: **rubric scoring** on a fixed ordinal scale for graded quality, **natural-language assertions** for specific yes/no properties, **reference-based grading** where a gold answer exists, **pairwise comparison** when you need to know which of two candidates is better — humans and models are both more reliable at comparison than at absolute scoring — and **multi-judge consensus** where the stakes justify the cost. Pairwise is the right answer when the stem is comparing two prompt versions and absolute scores are clustered; consensus is the right answer when one judge's variance is material and budget exists.

The known biases are exam material because they generate plausible distractors. Judges favour longer and more confident answers, favour the response in a particular position when comparing, and favour text that resembles their own style. The mitigations are structural: fix the scale and define every level concretely, randomise presentation order in pairwise comparisons, require the judge to cite the evidence for its verdict, and keep the rubric's criteria mechanically checkable wherever possible — the same discipline that makes a good human rubric makes a gradeable one. Grading calls are also short by design; the documented examples cap grading output tightly, because a judge that writes an essay is paying for prose you will not read.

## Key facts

- Use clear, structured rubrics and grade each dimension with its own isolated judge rather than one judge for all dimensions.
- Give judges an escape hatch — an explicit "Unknown" — to prevent fabricated verdicts under uncertainty.
- The grading model should be a different instance from the model being evaluated.
- Model-based grading methods: rubric scoring, natural-language assertions, reference-based evaluation, pairwise comparison, multi-judge consensus.
- Pairwise comparison is more reliable than absolute scoring when candidates are close.
- Multi-judge consensus reduces single-judge variance at proportional cost.
- Known judge biases: verbosity, confidence, position in a pairwise prompt, stylistic similarity to the judge's own output.
- Mitigations: concretely defined scale levels, randomised order, required evidence citations, mechanically checkable criteria.
- Judge calls are short by design; grading outputs are capped tightly in the documented examples.

## Exam traps

- One judge scoring several dimensions in a single call — the correlated-score trap, and the most frequently credited fix is splitting them.
- A judge with no "Unknown" or abstain option, offered as "forcing a decision for cleaner data."
- The generating model grading its own output.
- Absolute 1–5 scoring used to separate two nearly identical candidates, where pairwise comparison is the reliable instrument.
- Fixing judge disagreement by raising the temperature, changing the phrasing, or adding "be objective" instead of defining the scale levels.
- Adding more judges to consensus as the fix for a vague rubric — consensus reduces variance, not ambiguity.
