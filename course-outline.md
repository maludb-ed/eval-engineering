# Eval Engineering for the Claude Certified Architect: Professional (CCAR-P)
## Course Outline — v0.1 (2026-09-15)

Covers the exam's **Evaluation, Testing & Optimisation** domain: 16% of a 63-item exam,
roughly **10 scored items**. The course goes deeper than 10 items' worth because this
domain's ideas are load-bearing for items scored in Integration (RAG quality), Governance
(safety evals), and Lifecycle (reporting results) as well — realistically it influences
15–18 items.

Blueprint facts, sources, and unverified items: [research/exam-blueprint.md](research/exam-blueprint.md).

---

## 1. Course design

**Audience.** Solution architects, AI/ML engineers, and technical leads sitting CCAR-P.
Assumes 3+ years architecture experience and 6+ months shipping Claude/LLM systems.

**Prerequisites.** Comfortable with the Messages API, tool use, and a RAG or agent system in
production. Passing CCAR-F (or equivalent knowledge) is assumed — this course does not
re-teach agentic loop design or MCP basics.

**Format.** Self-paced. 8 modules, each = lesson + key-facts sheet + exam-traps sheet +
hands-on lab + quiz. Two timed checkpoints and one domain mock.

**Time budget.** ~14 hours: 7h lessons, 5h labs, 2h assessment. Deliverable in a 3-week
cadence (3 modules / 3 modules / 2 modules + mock).

**Learning outcomes.** By the end a learner can:
1. Turn a business requirement into measurable criteria across accuracy, latency, cost, safety, and security.
2. Design an eval dataset and harness whose results actually predict production behaviour.
3. Pick the cheapest grader that answers the question, and calibrate an LLM judge against humans.
4. Design an A/B or canary rollout that can prove a change is an improvement.
5. Trace a production symptom to the component that caused it, not the component that's easiest to blame.
6. Choose among token, latency, and cost levers with the real numbers (caching multipliers, model tiers, batch).
7. Specify what to log and which signals to alert on for a Claude system.
8. Recognise the domain's recurring distractor patterns under exam time pressure.

**Non-goals.** Not a Python eval-framework tutorial; not vendor tool training (Braintrust,
LangSmith, etc. appear only as categories). The exam tests judgment, not libraries.

---

## 2. Blueprint coverage map

| Official task statement | Module(s) | Depth |
|---|---|---|
| Define evaluation metrics (accuracy, latency, cost, safety, security) | M1 | Primary |
| Design evaluation datasets and test frameworks using mixed methodologies | M2, M3 | Primary |
| Conduct A/B testing and iterative improvements | M4 | Primary |
| Diagnose system issues (prompt failure, hallucinations, model mismatch) | M5 | Primary |
| Optimise token usage, latency, and cost-performance trade-offs | M6 | Primary |
| Monitor system performance using logging and observability tools | M7 | Primary |
| *(cross-domain: eval evidence in governance, integration, stakeholder comms)* | M8 | Secondary |

Every module traces to at least one task statement. No module exists without one.

---

## 3. Modules

### M0 — Orientation (30 min, no lab)
How the domain is scored and how its items are written.
- Domain weight, item count, and the 114-seconds-per-item pacing maths.
- The exam's point of view: an architect who must *defend* a measurement choice, not a
  researcher optimising a benchmark.
- Four recurring stems in this domain: "quality dropped after X," "prove this change is
  better," "cut cost/latency without losing accuracy," "what do we log."
- How to read distractors: plausible-but-expensive, plausible-but-unmeasurable,
  fixes-a-symptom, and right-answer-wrong-layer.

---

### M1 — Success criteria and metrics (1.5h)
*Task statement: define evaluation metrics (accuracy, latency, cost, safety, security).*

**Lesson**
- The four properties of good success criteria — specific, measurable, achievable, relevant
  (Anthropic's wording; note it is *not* the five-part SMART framework, and "time-bound" is not
  one of them) — applied to LLM systems; why "the agent feels worse" is not a criterion.
- The five exam axes as a checklist: accuracy, latency, cost, safety, security. Most
  credited answers in this domain name **more than one axis**; single-axis answers are
  usually the trap.
- Accuracy metric selection: exact match, precision/recall/F1 (and when a class-imbalanced
  F1 lies), ROUGE-L F1 for summarisation, cosine similarity (SBERT) for consistency,
  rubric scores for open-ended work.
- **pass@k vs pass^k** — one success matters vs consistency essential; identical at k=1,
  divergent by k=10. Mapping each to a product requirement is a high-probability item.
- Latency: baseline latency vs **TTFT**; why percentiles (p50/p95/p99) and not means;
  perceived vs actual latency with streaming.
- Cost: cost per task/session/resolved-ticket, not cost per token. Amortising cache writes.
- Safety and security as *measured* properties: refusal correctness, toxicity rate,
  PII/PHI leakage rate, prompt-injection resistance, tool-permission violations.
- Multidimensional targets and thresholds: "F1 ≥ 0.85 AND ≥ 99.5% non-toxic AND p95 < 200ms."
- Criterion-referenced scoring: thresholds fixed in advance, never curved to the result.

**Lab.** Take three provided requirement statements (support triage bot, contract-clause
extractor, research agent) and write a full criteria table — axis, metric, target,
measurement method, who owns it.

**Key facts / traps.** Means hide tail latency. A single-axis target invites a regression
on the axis you didn't name. "Achievable" means benchmarked against a frontier model's
demonstrated ceiling, not aspiration. Thresholds set after seeing results are not criteria.

**Quiz:** 8 items.

---

### M2 — Eval datasets and harness design (2h)
*Task statement: design evaluation datasets and test frameworks using mixed methodologies (part 1).*

**Lesson**
- Canonical vocabulary — task, trial, grader, transcript/trace, outcome, evaluation
  harness, agent harness/scaffold. Use these words exactly; several distractors work by
  swapping transcript for outcome.
- Start early and small: **20–50 tasks drawn from real failures** beats a perfect suite
  that never gets built. Scale to 50–1000 as effect sizes shrink.
- Sourcing cases: production failures, user reports, manual dev checks, edge cases
  (irrelevant input, oversized input, ambiguous/sarcastic input, adversarial input).
- **Unambiguous tasks**: two domain experts independently reach the same verdict; write a
  reference solution to prove the task is solvable.
- **Balanced sets**: negative cases where the behaviour *should not* fire. Class imbalance
  turns a useless grader into a 95%-scoring one.
- **Harness parity**: the eval agent must be the production agent — same tools, same
  scaffold, same context assembly. Clean state per trial; shared state produces correlated
  failures that look like model regressions.
- Trials per task and variance: why single-trial comparisons mislead.
- **Saturation**: scores near 100% carry no signal; refresh or harden the suite.
- Maintenance and ownership: eval suites are living code with a named owner.
- Golden set vs regression set vs exploratory set — three artefacts, three lifecycles.

**Lab.** Build a 25-task eval set for a provided support-agent transcript corpus: label,
balance, write reference solutions, and document the harness contract.

**Key facts / traps.** A frontier model at 0% pass@100 means a broken task. Synthetic-only
datasets miss the distribution that matters. "Add more test cases" is not the answer when
the existing ones are ambiguous.

**Quiz:** 10 items.

---

### M3 — Graders and mixed methodologies (2h)
*Task statement: design evaluation datasets and test frameworks using mixed methodologies (part 2).*

**Lesson**
- Three grader families and their economics: **code-based** (fast, cheap, reproducible,
  brittle to valid variation), **model-based** (flexible, non-deterministic, needs
  calibration), **human** (gold standard, expensive, slow, the calibration source).
- Code-based techniques: string/exact match, unit and integration tests, static analysis,
  schema validation, tool-call verification, **outcome verification against the environment**.
- Model-based techniques: rubric scoring, natural-language assertions, pairwise comparison,
  reference-based grading, multi-judge consensus.
- **Trajectory vs outcome**: grade what the agent produced, not the path it took — but
  read trajectories to understand cost, turn count, and tool misuse. The flight-booking
  case: the transcript claims success; the grader checks the reservation row exists.
- **LLM-as-judge design**: one isolated judge per rubric dimension; an "Unknown" escape
  hatch; a grading model separate from the generating model; positional/verbosity bias.
- **Judge calibration**: validate against a human-labelled golden set before scaling;
  target 75–90% agreement; the "intern test" (two humans agree 80%+ or the rubric is too
  vague to automate). Recalibrate on a schedule.
- The hybrid norm: verifiable rewards ("did it solve the problem?") + LLM rubric ("is it
  readable, efficient, secure?").
- Partial credit for multi-component tasks; grading rigidity as an anti-pattern
  ("96.12" rejected against "96.124991…").
- **Eval cheating / reward hacking**: agents exploiting loopholes; detect by transcript review.
- Sampling humans into the loop cheaply: spot checks, inter-annotator agreement studies.

**Lab.** Write and calibrate an LLM judge: draft a 3-dimension rubric, hand-label 30
outputs, measure agreement, revise the rubric, re-measure. Report the agreement delta.

**Key facts / traps.** Cheapest grader that answers the question wins; "use an LLM judge"
is wrong when a unit test settles it. One judge grading five dimensions at once is a trap.
An uncalibrated judge is evidence of nothing.

**Quiz:** 10 items.

---

### M4 — A/B testing, regression, and iterative improvement (1.5h)
*Task statement: conduct A/B testing and iterative improvements.*

**Lesson**
- Offline evals gate; online experiments confirm. Neither replaces the other.
- Prompt/config **versioning** as the precondition for any comparison.
- Regression suites in CI: what blocks a merge vs what warns.
- A/B design for LLM systems: unit of randomisation (user vs session vs request),
  sample size and significance, minimum detectable effect, guardrail metrics that must
  *not* move (cost, p95 latency, refusal rate), and run length vs novelty effects.
- Why per-request randomisation corrupts multi-turn agent experiments.
- **Shadow mode, canary, staged rollout, champion/challenger** — what each proves and costs.
- Rollback readiness as part of the design, not the incident.
- A shipping pipeline to reason with: version → regression → offline eval → A/B
  significance → canary monitoring → rollback path.
- Closing the loop: production failures become new eval tasks; the suite grows from incidents.
- Human feedback signals (thumbs, escalation rate, edit distance) and their biases.

**Lab.** Given an eval delta of +3% on 40 tasks, design the rollout: decide whether it
justifies an A/B, choose randomisation unit, set guardrails and stop conditions, write the
rollback trigger.

**Key facts / traps.** A win on a saturated eval is not a win. "Ship it, the eval improved"
without guardrail metrics is the classic distractor. Comparing a new prompt on a new
dataset proves nothing.

**Quiz:** 8 items.

---

### M5 — Diagnosis and root-cause analysis (2h)
*Task statement: diagnose system issues (prompt failure, hallucinations, model mismatch).*

**Lesson**
- The domain's core heuristic: **trace the symptom to the component that changed.**
- Failure taxonomy with distinguishing evidence for each:
  - prompt failure (vague criteria, missing examples, conflicting instructions)
  - hallucination / ungrounded generation
  - **retrieval or indexing failure** (stale index, chunking change, embedding-model mismatch)
  - model mismatch (task above or below the chosen tier; reasoning-depth mismatch)
  - tool/schema failure (bad descriptions, silent errors, missing error channel)
  - context failures (truncation, lost-in-the-middle, compaction dropping state)
  - agent-loop failures (looping, premature stop, unrecoverable tool error)
- The canonical exam scenario: **confident-but-wrong RAG answers after a document refresh →
  investigate retrieval/indexing first**, not the prompt and not the model.
- Splitting RAG metrics: retrieval recall/precision vs generation faithfulness. A single
  end-to-end accuracy number cannot localise the fault.
- Ablation as method: change one layer at a time; hold the eval set fixed.
- Transcript review as a first-class practice, not a last resort.
- Hallucination controls: allow uncertainty, ground in quotes, require citations,
  verification passes — and their cost.
- When the answer really *is* model mismatch: escalating tiers, extended thinking, and
  when a bigger model is the cheap fix.

**Lab.** Five annotated failure transcripts (RAG, agent loop, tool schema, prompt,
model-tier). Diagnose each, name the evidence, propose the minimal fix.

**Key facts / traps.** Swapping models to fix a retrieval bug. Adding "be accurate" to fix
hallucination. Rebuilding the pipeline when one component changed. Escalating to a larger
model before isolating the layer.

**Quiz:** 10 items.

---

### M6 — Optimising tokens, latency, and cost (2h)
*Task statement: optimise token usage, latency, and cost-performance trade-offs.*

**Lesson**
- Optimise only after quality is established — the documented order of operations.
- **Model selection and routing**: tier by task; Haiku 4.5 for speed-critical paths;
  cascades and escalation; per-stage model choice in a pipeline.
- **Prompt caching economics with the real multipliers**: writes 1.25x base input (5-min
  TTL) or 2x (1-hour TTL); reads 0.1x. Break-even maths for how many reads justify a write.
- Cache mechanics that generate items: minimum cacheable prefix by model (512 / 1,024 /
  4,096 tokens), up to 4 explicit breakpoints, 20-block lookback, hierarchy
  tools → system → messages, breakpoint on the last *stable* block, what invalidates a cache.
- Context reduction: retrieval trimming, summarisation/compaction, structured over prose,
  dropping stale tool output.
- Output-side levers: `max_tokens`, asking for sentence/paragraph limits (not word counts),
  concise-output instructions, structured output.
- Throughput levers: batch processing for non-interactive work, parallel tool calls,
  streaming for perceived latency.
- Extended thinking as a cost/quality dial.
- Building a defensible cost model: cost per resolved task across a pipeline; where the
  10x differences actually live.
- Stating the trade-off explicitly — the credited answer usually names what it gives up.

**Lab.** Cost-and-latency teardown of a provided 3-stage pipeline: compute current cost per
task, apply caching and a model-tier change, recompute, and state the accuracy risk.

**Key facts / traps.** Caching a per-request block (timestamps, the user message) yields
zero hits. Prompts under the model minimum silently don't cache. Downgrading the model
before trimming context. Optimising a stage that isn't the cost driver.

**Quiz:** 10 items.

---

### M7 — Monitoring and observability in production (1.5h)
*Task statement: monitor system performance using logging and observability tools.*

**Lesson**
- What a Claude system must emit: full traces (prompt version, model, tool calls, token
  counts, cache hit/miss, stop reason), latency percentiles, error and refusal rates,
  cost per session, human-escalation rate.
- Traces as the bridge between production and evals — the log format should make a failed
  interaction replayable as a new eval task.
- Online evals: sampling live traffic into judges, with sampling rates and cost ceilings.
- Drift: input distribution drift, retrieval corpus drift, model-version change,
  eval-set staleness. Which signal catches each.
- Alerting that survives a pager rotation: thresholds on rates and percentiles, not counts;
  guardrail alerts tied to the A/B guardrail metrics.
- Dashboards by audience: engineer (traces), architect (quality + cost trend), executive
  (outcome per dollar).
- Logging and privacy: PII in prompts, retention windows, redaction before storage —
  where this domain touches Governance.
- OpenTelemetry-style instrumentation and what a span should carry for an agent turn.
- Scheduled transcript review as a standing practice with a named owner.

**Lab.** Specify the observability plan for a provided architecture: event schema, 6
dashboard metrics, 4 alerts with thresholds, sampling policy, retention/redaction rules.

**Key facts / traps.** Logging outputs without inputs or versions makes diagnosis
impossible. Alerting on absolute error counts. Sampling only failures (you lose the base
rate). Monitoring the model but not retrieval.

**Quiz:** 8 items.

---

### M8 — Where eval meets the other domains (1h)
*Cross-domain reinforcement; no new task statement.*

**Lesson**
- **Governance**: safety evals, red-teaming, human-in-the-loop placement justified by
  measured error severity; evidence packs for compliance review.
- **Integration**: evaluating RAG and tool layers separately; MCP tool-selection accuracy.
- **Solution design**: using eval results to choose workflow vs agentic patterns.
- **Stakeholder communication**: reporting eval results honestly — confidence intervals,
  known blind spots, what the number does not cover; setting SLAs you can measure;
  expectation-setting when accuracy has a ceiling.
- **Developer productivity**: evals in CI, eval ownership in the team model.

**Lab.** Write a one-page eval report for an executive sponsor from a provided results table.

**Quiz:** 6 items.

---

## 4. Assessment plan

| Instrument | When | Items | Purpose |
|---|---|---|---|
| Module quizzes | End of each module | 70 total | Recall + application |
| Checkpoint A (timed) | After M3 | 15 in 28 min | Dataset/grader judgment under pacing |
| Checkpoint B (timed) | After M6 | 15 in 28 min | Diagnosis + optimisation under pacing |
| Domain mock | After M8 | 30 in 57 min | Exam-condition simulation, weighted to blueprint |
| Weak-area retest | Post-mock | Adaptive | Close gaps found by the mock |

Mock weighting mirrors the six task statements proportionally, with ~25% multi-response
items and scenario stems that carry business constraints.

**Readiness bar:** ≥ 80% on the domain mock with no task statement below 70%.

---

## 5. Content conventions

Authored to drop into the existing `learn-claude` plugin structure so the quiz and
mock-exam skills can consume it without changes.

```
content/
  topics/ev-M-NN-slug.md     # frontmatter: id, domain, domain-name, weight,
                             # title, task-statement, scenarios
                             # body: ## Lesson / ## Key facts / ## Exam traps
  bank/domain-eval.md        # ### Q ev-M-NN-qNN — topic, scenario, Stem, A–D,
                             # Answer, Explanation (explains every distractor)
labs/lab-M-slug.md           # brief, starter data, rubric, worked solution
```

Rules for question writing: one credited answer defensible from a cited source; every
distractor explained; no trick wording; scenario stems carry a constraint that decides the
answer; multi-response items flag the count in the stem.

---

## 6. Build plan

| Phase | Output | Est. |
|---|---|---|
| 0 | Verify blueprint against the official Exam Guide PDF; resolve the open questions | 2h |
| 1 | M1–M3 lessons + key facts + traps | 8h |
| 2 | M4–M7 lessons + key facts + traps | 10h |
| 3 | M8 + all 7 labs with worked solutions | 8h |
| 4 | 70 quiz items + 60 checkpoint/mock items | 10h |
| 5 | Pass for accuracy against sources; pilot with 2–3 candidates; revise | 6h |

---

## 7. Known gaps

- The blueprint here is reconstructed from secondary sources quoting Anthropic's Exam
  Guide v1.0. **Phase 0 must verify it** before content is written against it.
- Sources disagree on price ($125 vs $175), validity (1 year, unconfirmed), and whether
  Professional items are scenario-framed or standalone. The outline assumes scenario-framed
  because it is the harder preparation; if items are standalone, drills get easier, not wrong.
- Anthropic may publish an official Professional prep track that supersedes parts of this.
