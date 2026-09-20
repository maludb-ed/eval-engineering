---
id: ev-3-01
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Choosing a Grader Family
task-statement: "4.2 Design evaluation datasets and test frameworks using mixed methodologies"
scenarios: [2, 5, 6]
---

## Lesson

Three families of grader exist and each buys something at a price. **Code-based graders** are fast, cheap, objective, and reproducible, and they are brittle to valid variation — they reject the correct answer that arrives in an unexpected shape. **Model-based graders** are flexible and handle open-ended work, and they are non-deterministic and require calibration before their verdicts mean anything. **Human graders** are the gold standard and are expensive and slow, which is why they are spent on calibration and spot checks rather than on volume. "Mixed methodologies" in the task statement means combining these deliberately, and the selection rule the exam rewards is simple: **use the cheapest grader that can actually answer the question**. An LLM judge asked to decide whether a routing label equals `fraud_review` is the wrong tool — slower, dearer, and less reproducible than a string comparison. A regex asked to decide whether a support reply was appropriately empathetic is equally wrong in the other direction.

The sharper version of that rule is the hybrid norm now standard for agentic work: pair **verifiable rewards** with **LLM rubrics**. The verifiable reward answers "did this actually solve the problem" — the tests pass, the reservation row exists, the extracted clause matches the annotation. The rubric answers the questions no assertion can reach — is the code readable, is the summary coherent, was the tone right for a distressed customer. Neither substitutes for the other, and an option offering only one when the scenario cares about both is usually the trap. Human judgment sits underneath both, sampled rather than exhaustive: expert review of a slice, inter-annotator agreement studies, spot checks on production traffic.

Agent type also points at a default grading strategy, and the exam scenarios line up with these. A **coding agent** grades deterministically first — the test suite is the outcome check — with optional transcript-level rubrics for code quality. A **conversational agent** combines state checks, tool-call verification, rubrics for tone and helpfulness, and turn limits, often with a simulated user persona driving the multi-turn exchange. A **research agent** balances groundedness (is each claim supported by a source), coverage (are the key facts present), and source quality, with exact match reserved for the objectively checkable answers. A **computer-use agent** verifies through page or DOM state, backend confirmation that the action landed, or file-system inspection. When a stem names the agent type, it has already narrowed the grader family; the credited answer usually matches that default rather than inventing a new apparatus.

## Key facts

- Code-based graders: fast, cheap, objective, reproducible; brittle to valid variation. Techniques include string matching, binary tests, static analysis, outcome verification, tool-call verification, transcript analysis.
- Model-based graders: flexible, handle open-ended tasks; non-deterministic and require calibration. Techniques include rubric scoring, natural-language assertions, pairwise comparison, reference-based evaluation, multi-judge consensus.
- Human graders: gold standard, expensive, slow. Used for expert review, crowdsourced judgment, spot-check sampling, A/B testing, inter-annotator agreement studies.
- Selection rule: the cheapest grader that can answer the question; escalate only when the cheaper family cannot.
- The hybrid norm pairs verifiable rewards ("did it solve the problem?") with LLM rubrics ("is it readable, efficient, secure?").
- Coding agents: deterministic graders and test suites as the outcome check, optional rubrics for code quality.
- Conversational agents: state checks, tool-call verification, rubrics for tone and quality, turn limits, simulated user personas for multi-turn runs.
- Research agents: groundedness, coverage, and source-quality checks, with exact match for objectively correct answers.
- Computer-use agents: DOM or page state, backend verification that the action landed, file-system inspection.

## Exam traps

- An LLM judge proposed where a deterministic check settles the question — the default over-engineering trap in this module.
- A purely deterministic grading scheme for open-ended output such as a summary, a briefing, or a customer reply.
- Rubric-only grading of a coding agent, with no test suite confirming the code actually works.
- Human review proposed as the primary grading mechanism at production volume rather than as calibration and sampling.
- A single grader family declared sufficient when the scenario states both a correctness requirement and a quality requirement.
