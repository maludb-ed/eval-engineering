# CCAR-P Exam Blueprint — Research Notes

Research date: 2026-09-15. Sources listed at the bottom; confidence marked per fact.

## The credential

**Claude Certified Architect: Professional (CCAR-P)** — the advanced tier of Anthropic's
Architect track, one of four role-based certifications (Associate: Foundations,
Developer: Foundations, Architect: Foundations, Architect: Professional). Announced by
Anthropic as covering "integration architecture, governance, and evaluation" at enterprise
scale. Designed with Anthropic's Applied AI team; delivered through Pearson
Professional Assessments (Pearson VUE), online-proctored or test centre; badge issued via
Credly. Registration runs through the Claude Partner Network / Partner Academy.

Where Foundations asks "can you design a working Claude system," Professional asks
"can you own one in production — build it, ship it, defend the decisions, keep it safe."

## Logistics (HIGH confidence unless noted)

| Item | Value | Confidence |
|---|---|---|
| Items | 63 scored | High (two sources agree) |
| Time | 120 minutes | High |
| Passing score | 720 on a 100–1000 scale | High |
| Item types | Multiple choice + multiple response ("Select TWO/THREE"); ~75/25 split | Medium |
| Scenario framing | Sources conflict — one says standalone items, one says predominantly scenario-based with business context | **LOW — verify** |
| Price | $125 or $175 USD (sources conflict) | **LOW — verify** |
| Validity | "1 year" per one source; unconfirmed | **LOW — verify** |
| Recommended background | 3+ yrs systems/platform architecture; 6+ months hands-on production Claude/LLM | High |

Practical implication for pacing: 63 items / 120 min = **114 seconds per item**. If a
quarter are multi-response scenarios, budget ~90s for recall items to bank time.

## Domain weights (Exam Guide v1.0, as reported by secondary sources)

| # | Domain | Weight | ≈ items |
|---|---|---|---|
| 1 | Integration | 19% | ~12 |
| 2 | Solution Design & Architecture | 17% | ~11 |
| 3 | **Evaluation, Testing & Optimisation** | **16%** | **~10** |
| 4 | Governance, Safety & Risk Management | 14% | ~9 |
| 5 | Stakeholder Communication & Lifecycle Management | 14% | ~9 |
| 6 | Claude Models, Prompting & Context Engineering | 13% | ~8 |
| 7 | Developer Productivity & Operational Enablement | 7% | ~4 |

Weights sum to 100%. Note the ordering differs between sources; weights agree.

## The eval engineering domain — six published task statements

Domain focus statement: *"Measuring quality, testing rigorously, tuning for cost and
performance."* / *"System validation and performance tuning."*

1. Define evaluation metrics (accuracy, latency, cost, safety, security)
2. Design evaluation datasets and test frameworks using mixed methodologies
3. Conduct A/B testing and iterative improvements
4. Diagnose system issues (prompt failure, hallucinations, model mismatch)
5. Optimise token usage, latency, and cost-performance trade-offs
6. Monitor system performance using logging and observability tools

Reported sub-emphases: mapping requirements to the five performance axes; assembling
datasets that represent real-world edge cases; combining programmatic checks, model-graded
evaluation, and human review; A/B validating changes before full rollout; root-cause
diagnosis that traces a symptom to the component that changed; token optimisation via
leaner context and model selection; comprehensive logging and observability.

One secondary guide describes a "six-gate shipping pipeline" — versioning, regression
testing, offline evaluation, A/B significance checks, canary monitoring, rollback
readiness. Not confirmed as official blueprint language; teach the concept, not the label.

A published study tip, verbatim: *"When a RAG system returns confident-but-wrong answers
after a document refresh, investigate the retrieval/indexing step first."*

## Primary technical source material (Anthropic-authored)

### "Demystifying evals for AI agents" (Anthropic engineering)
Canonical vocabulary the course should adopt:
- **Task** — one test with defined inputs and success criteria
- **Trial** — one attempt at a task; run several to absorb model variance
- **Grader** — scoring logic; a task may carry several graders/assertions
- **Transcript / trace** — full record of a trial: outputs, tool calls, reasoning, intermediates
- **Outcome** — the final state of the environment, not what the agent claimed
- **Evaluation harness** — infrastructure that runs evals end to end
- **Agent harness / scaffold** — the system that gives the model agency

Three grader families: **code-based** (fast, cheap, reproducible, brittle to valid
variation), **model-based** (flexible, non-deterministic, needs calibration),
**human** (gold standard, expensive, slow).

Eight-step dataset roadmap: start early (20–50 tasks from real failures) → convert manual
tests → make tasks unambiguous (two experts reach the same verdict) → balance positive and
negative cases → build a harness matching production, clean state per trial → design
graders that grade what was produced, not the path, with partial credit → read transcripts
regularly → watch for saturation → maintain with named ownership.

Metrics: **pass@k** (at least one of k trials succeeds — "one success matters") vs
**pass^k** (all k succeed — "consistency essential"). They are identical at k=1 and
diverge sharply as k grows.

Pitfalls: ambiguous specs (0% pass@100 on a frontier model usually means a broken task,
not a weak agent), grading rigidity (rejecting "96.12" when "96.124991…" was specified),
shared-state pollution across trials, eval cheating/loophole exploitation, eval bugs with
impossible thresholds. "Swiss cheese model" — no single layer catches everything.

### Anthropic docs — success criteria and eval design
Success criteria are "specific, measurable, achievable, relevant" — four properties, quoted
verbatim; the docs never use the acronym SMART and there is no "time-bound" criterion, so the
course should not import that label. Criteria categories
(task fidelity, consistency, relevance/coherence, tone/style, privacy preservation,
context utilisation, latency, price); design principles (task-specific, automate where
possible, volume over per-item polish); grading modes with worked examples: exact match,
cosine similarity (SBERT), ROUGE-L F1, LLM Likert 1–5, LLM binary classification; use a
different model instance for grading than for generation; 50–1000 test cases depending on
task; multidimensional targets (e.g. F1 ≥ 0.85 AND 99.5% non-toxic AND p95 < 200ms).

### Anthropic docs — optimisation levers (exact numbers for the cost/latency module)
Prompt caching: cache **writes** cost 1.25x base input (5-minute TTL) or 2x (1-hour TTL);
cache **reads** cost 0.1x base input. Minimum cacheable prefix is model-dependent —
512 tokens (Opus 5, Fable 5.1, Mythos 5.1, Fable 5, Mythos 5), 1,024 (Opus 4.8, Sonnet 5,
Sonnet 4.6, Sonnet 4.5), 4,096 (Haiku 4.5). Up to 4 explicit breakpoints per request;
20-block lookback; cache hierarchy order tools → system → messages; put the breakpoint on
the last block that stays identical, never on a per-request block.

Latency: distinguish **baseline latency** from **time to first token (TTFT)**; lever order
is model choice (Haiku 4.5 for speed-critical paths) → prompt/output token reduction
(`max_tokens`, sentence/paragraph limits rather than word counts) → streaming for perceived
responsiveness. Documented caution: engineer for quality first, optimise latency after.

### LLM-as-judge calibration (Anthropic guidance + field practice)
Grade each rubric dimension with its own isolated judge rather than one judge for
everything; give the judge an escape hatch ("Unknown") to suppress hallucinated verdicts;
validate against a golden human-labelled set before scaling — field guidance targets
75–90% judge/human agreement, and the "intern test" heuristic says a rubric specific enough
to automate produces 80%+ agreement between two human graders. Pair verifiable rewards
("did the code solve it?") with LLM rubrics ("is it readable, efficient, secure?").

## Open questions to verify before publishing the course

1. Obtain the **official Anthropic Exam Guide v1.0 PDF** from Partner Academy and diff
   the task statements and weights against this file. All domain detail above is from
   secondary sources that claim to quote it.
2. Confirm price, validity period, retake policy, and whether items are scenario-framed.
3. Confirm the domain's official number/ordering (we call it Domain 3 by weight rank here;
   other sources number it 4).
4. Check whether Anthropic ships an official Professional prep track — one source says the
   "dedicated Professional prep track is on the way," which may supersede parts of this course.

## Sources

- Anthropic — Four role-based Claude certifications: https://claude.com/blog/four-role-based-claude-certifications
- Anthropic engineering — Demystifying evals for AI agents: https://www.anthropic.com/engineering/demystifying-evals-for-ai-agents
- Anthropic docs — Create strong empirical evaluations: https://platform.claude.com/docs/en/test-and-evaluate/eval-tool
- Anthropic docs — Reducing latency: https://platform.claude.com/docs/en/test-and-evaluate/strengthen-guardrails/reduce-latency
- Anthropic docs — Prompt caching: https://platform.claude.com/docs/en/build-with-claude/prompt-caching
- Claude Certification Guide — CCAR-P exam guide & blueprint: https://claudecertificationguide.com/ccar-p
- Preporato — CCAR-P complete guide 2026: https://preporato.com/blog/claude-certified-architect-professional-complete-guide-2026
- FlashGenius — CCAR-P 2026 interactive guide: https://flashgenius.net/guides/claude-certified-architect-professional-ccar-p-2026-interactive-guide
