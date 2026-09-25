<?php
/**
 * module-8.php — study content for Module 8: Where Eval Meets the Other Domains.
 *
 * Grounded in Anthropic's published guidance: the usage policy and
 * trust-and-safety framing, model cards and release documentation as public
 * precedent, the Responsible Scaling Policy's risk-tiered logic as context,
 * and the evaluation guidance cited throughout Modules 1–7
 * (docs.claude.com "Create strong empirical evaluations", the engineering
 * posts on agent evals and building effective agents).
 *
 * Returns: [ topic-id => ['body' => html, 'questions' => [...] ] ]
 */
return [

    // =====================================================================
    'ev-8-01' => [
        'body' => <<<'HTML'
<p>
Governance without measurement is assurance theatre. When a risk committee asks "is the claims
triage assistant safe to deploy?", the only answer that survives scrutiny is a set of measured
numbers against pre-agreed floors — not "we tested it extensively and it looks good". This
topic is where the eval discipline from Modules 1–7 becomes the <em>evidence base</em> for the
Governance domain: every risk claim in a deployment review should trace back to a suite, a
dataset version, and a run you can reproduce.
</p>

<h3>Risk assessments cite failure rates, not adjectives</h3>
<p>
A governance-ready risk assessment replaces "hallucinations are rare" with "grounded-answer
rate 97.2% on the 400-item RAG suite v3, 4 trials per item; worst observed failure: fabricated
policy effective-date". The difference matters because governance decisions are trade-offs —
a 2.8% failure rate may be acceptable for the policy knowledge assistant with a visible
"verify with HR" disclaimer, and unacceptable for automated claim denials. The committee can
only make that call if the rate, the population it was measured on, and the severity of the
observed failures are all on the table. This mirrors how Anthropic's own model cards report
measured behaviour on named evaluation suites rather than blanket safety claims.
</p>

<h3>Risk tier determines evaluation rigour</h3>
<p>
Anthropic's Responsible Scaling Policy applies a simple logic worth internalising in miniature:
the higher the potential harm, the heavier the evaluation burden before deployment. Applied to
an application portfolio:
</p>
<div class="table-responsive">
<table class="table table-sm align-middle">
    <thead>
        <tr class="text-secondary small text-uppercase">
            <th>Risk tier</th><th>Example</th><th>Evaluation burden</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Low — internal, human-reviewed</td>
            <td>Research report agent drafting briefings an analyst edits</td>
            <td>Core quality suite in CI; spot-check reviews</td>
        </tr>
        <tr>
            <td>Medium — customer-facing, reversible</td>
            <td>Support deflection agent with escalation path</td>
            <td>Quality + safety + injection suites gated per release; canary rollout; online monitoring</td>
        </tr>
        <tr>
            <td>High — regulated, affects entitlements</td>
            <td>Claims triage routing payouts</td>
            <td>All of the above, plus larger suites, stricter floors, human review of sampled decisions, staged rollout with rollback criteria, documented sign-off</td>
        </tr>
    </tbody>
</table>
</div>
<p>
The point is not the specific rows — it is that evaluation rigour is a <em>function of risk
tier</em>, decided before launch, not an afterthought scaled to whatever budget remains.
</p>

<h3>Repeatability is the audit requirement</h3>
<p>
Regulated deployments add a constraint the eval harness must be built for: an auditor (or your
own incident review) will ask "show me the evidence this version met the bar when you shipped
it". That requires versioned datasets, versioned prompts and configs, stored run results tied
to release identifiers, and suites that can be re-run months later and produce comparable
numbers. The trace store from Module 7 completes the loop: when an incident occurs, you need
the trace to reconstruct what happened <em>and</em> a re-runnable suite to prove the fix works
and to demonstrate the failure class is now covered.
</p>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">The trap: sign-off on assurances</div>
    <p>When a stem shows a launch review where the team asserts quality ("we manually checked
    many outputs and they look strong"), the credited criticism is that governance sign-off
    requires <em>documented, repeatable measurement against pre-agreed criteria</em> — named
    suites, versioned data, floors set before results were seen. Ad-hoc inspection is neither
    auditable nor comparable across releases.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Cross-domain items reward three moves: (1) matching evaluation rigour to risk tier —
    heavier burden for regulated or irreversible actions; (2) replacing qualitative assurance
    with measured failure rates on named, versioned suites; (3) treating repeatability
    (versioned datasets, stored runs, re-runnable harness) as the thing that makes evidence
    audit-grade. Distractors offer "more testing" without versioning, or identical rigour for
    every application regardless of stakes.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The claims triage assistant is entering a regulatory audit. The auditor asks for evidence that the version deployed in March met the firm\'s stated accuracy and safety bar. Which artefact set actually answers the question?',
                'options' => [
                    'Current production dashboards showing this month\'s routing accuracy and latency',
                    'A fresh eval run of the current model on the current suite, since it supersedes the March version',
                    'Written attestations from the engineering lead and product owner that the release was tested',
                    'The stored eval run for the March release: suite results tied to the release identifier, the versioned dataset and prompts used, and the pre-agreed floors it was gated against',
                ],
                'answer' => 3,
                'explain' => 'The audit question is about the March version at ship time, so the evidence must be the versioned, stored run gated against floors agreed before results were seen. Current dashboards and a fresh run measure a different version at a different time, and attestations are exactly the unmeasured assurance that audit-grade governance replaces. This is why harnesses for regulated deployments must version datasets, configs, and results.',
            ],
            [
                'q' => 'An organisation runs both the research report agent (drafts reviewed by an analyst before use) and an automated claim-denial workflow. A governance lead proposes one standard evaluation checklist applied identically to both. What is the strongest architectural objection?',
                'options' => [
                    'Evaluation rigour should scale with risk tier: the human-reviewed internal tool and the irreversible entitlement decision warrant different suite sizes, floors, review requirements, and rollout gates',
                    'The checklist should be abandoned; teams closest to each system should decide their own testing informally',
                    'Both systems should instead be held to the claim-denial standard, since uniform maximum rigour is always safest',
                    'The research agent needs the stricter treatment, because open-ended tasks are harder to evaluate',
                ],
                'answer' => 0,
                'explain' => 'The responsible-scaling logic in miniature: burden proportional to potential harm. Uniform maximum rigour sounds safe but misallocates finite eval budget and slows low-risk work without reducing real risk; informal team-by-team testing removes governance entirely; and giving the research agent the stricter treatment confuses evaluation difficulty with deployment risk — the claim-denial flow is riskier because its errors are irreversible and affect entitlements, however easy it is to grade.',
            ],
            [
                'q' => 'After a production incident where the policy knowledge assistant gave an employee an outdated parental-leave answer, the incident review must (a) establish what happened and (b) prove the fix prevents recurrence. Which pairing serves those two needs?',
                'options' => [
                    '(a) Interviews with the employee who reported it · (b) a hotfix deployed and monitored for a week',
                    '(a) The stored trace of the incident interaction, including what was retrieved and generated · (b) the failure added as a regression case to the suite, with the suite re-run green on the fixed system',
                    '(a) The current retrieval index contents · (b) a written commitment to refresh documents quarterly',
                    '(a) Aggregate dashboard metrics for the incident day · (b) an increase in the judge\'s groundedness threshold',
                ],
                'answer' => 1,
                'explain' => 'Incident response leans on exactly two eval assets: the trace store (Module 7) to reconstruct the failing interaction — which document was retrieved, what the model generated — and the living suite (Module 2) to which the failure is added so the fix is proven by a re-runnable test rather than by the watchful waiting of a monitored hotfix. The retrieval-index and dashboard pairings address plausible contributing causes but neither establishes what actually happened nor demonstrates the specific failure is now covered.',
            ],
        ],
    ],

    // =====================================================================
    'ev-8-02' => [
        'body' => <<<'HTML'
<p>
Architecture questions — RAG or long-context? one agent or subagents? which tool boundary? —
are usually argued from fashion or intuition. The eval lens converts them into measurable
comparisons: every integration choice is a variant you can score on accuracy, latency, and
cost, and every integration seam is a place where a cheap targeted suite catches breakage
before the end-to-end metrics move.
</p>

<h3>Evaluate components separately and end-to-end</h3>
<p>
Module 5 established the diagnostic version of this principle; the design version says build
the measurement in from the start. For the policy knowledge assistant, that means a retrieval
suite (does the right chunk appear in the top-k for each eval question?) scored with recall@k,
a generation suite (given the correct chunks, is the answer grounded and complete?) scored by
a calibrated judge, and the whole-pipeline suite scoring final answers. Component metrics tell
you <em>where</em> to spend effort; the end-to-end metric tells you whether users are actually
better off. A team with only end-to-end numbers debugs by guesswork; a team with only
component numbers ships pipelines whose parts pass individually and fail jointly.
</p>

<h3>Tool integrations earn their own suites</h3>
<p>
An agent's tools are an API surface with their own failure modes, so they get targeted tests
that do not require running the whole agent: schema conformance (does the model emit valid
calls for every tool, including edge-case parameters?), error handling (when the tool returns
an error or empty result, does the agent recover rather than hallucinate a result?), and
permission boundaries (does the agent stay inside allowed actions — the support deflection
agent must never call the refund tool above its authorised limit, and the suite proves it).
These are cheap, code-graded, and fast enough to run on every change.
</p>

<h3>Integration changes are changes</h3>
<p>
Teams reliably gate prompt edits but wave through the changes that live at the seams: an MCP
server update that reworded tool descriptions, a retrieval-index refresh with re-chunked
documents, a dependency bump in the harness. Each of these can move quality as much as any
prompt edit — a tool description <em>is</em> prompt text to the model. The rule from Module 4
extends without exception: anything that alters what the model sees or what happens to its
outputs goes through the regression gates. Contract tests at each seam (fixed input →
expected tool result shape; fixed query → expected top-k documents) localise the breakage in
minutes when a gate goes red.
</p>

<h3>Architecture A/Bs are eval questions</h3>
<p>
"Should the research report agent use one agent with a big context or an orchestrator with
subagents?" is answered the same way as a prompt change: define the axes (coverage and
groundedness by judge, wall-clock latency, fully-loaded cost per report), run both variants
over the same task set with multiple trials, and read the trade-off table. Long-context
stuffing versus RAG is the same shape: stuffing may win accuracy on small corpora while
losing on cost and latency as the corpus grows — a measured curve, not a doctrine.
</p>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">Seams first, then the whole</div>
    <p>When designing evaluation for a new architecture, walk the data path and place a cheap
    suite at every seam the model crosses — retrieval boundary, each tool boundary, output
    parsing — then one end-to-end suite over the top. The seam suites run on every commit; the
    expensive end-to-end suite gates releases. This is the "cheapest grader that answers the
    question" principle applied to system structure.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Watch for stems where a non-prompt change (tool description edit, index refresh, MCP
    upgrade) precedes a quality drop — the credited answer treats it as an ungated change that
    should have run the same regression suite as a prompt edit. Architecture-choice stems
    credit "run both variants on the same suite and compare on all axes" over any option that
    picks a pattern on principle. Component-vs-end-to-end stems credit having both, with
    component metrics for localisation and end-to-end for the ship decision.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The policy knowledge assistant\'s grounded-answer rate drops from 96% to 84% in a week with no prompt, model, or code deployments. The platform team mentions the document corpus was re-ingested with a new chunking strategy the previous Friday. What does the eval lens say went wrong procedurally?',
                'options' => [
                    'Nothing procedural — content refreshes are routine operations outside the release process',
                    'The judge should have been recalibrated before the refresh',
                    'The retrieval-index refresh changed what the model sees and should have passed the regression gates (retrieval suite plus end-to-end suite) before going live, like any prompt or model change',
                    'The team should have switched from RAG to long-context stuffing to avoid chunking sensitivity entirely',
                ],
                'answer' => 2,
                'explain' => 'Re-chunking alters the retrieved context — the model\'s effective input — so it is a quality-affecting change that belongs behind the same gates as a prompt edit; a retrieval suite (recall@k on eval questions) would have caught the degradation before production. Treating the refresh as routine content operations is the exact blind spot the topic warns about; recalibrating the judge confuses grader maintenance with a pipeline change; and moving to long-context stuffing is an architecture decision that would itself need measurement, not a gate procedure.',
            ],
            [
                'q' => 'Two architects disagree on the research report agent: one wants a single agent with a large context window, the other an orchestrator with parallel subagents. Which resolution best reflects evaluation-driven architecture?',
                'options' => [
                    'Prototype both, run them over the same task set with multiple trials, and compare judge-scored coverage and groundedness alongside wall-clock latency and fully-loaded cost per report',
                    'Choose subagents, since multi-agent architectures are the current best practice for research tasks',
                    'Choose the single agent, since fewer moving parts always means fewer failure modes',
                    'Let each architect build their preferred version and merge the two designs afterwards',
                ],
                'answer' => 0,
                'explain' => 'Architecture choices are empirical questions: same task set, multiple trials (the pass@k/pass^k discipline from Module 1), all relevant axes including cost and latency — subagents often improve coverage while multiplying token spend, which only a fully-loaded per-task comparison reveals. Picking subagents or the single agent on principle decides by doctrine in opposite directions, and building both to merge later produces two unmeasured systems plus an unmeasured hybrid.',
            ],
            [
                'q' => 'The support deflection agent gains a new refund tool with a strict authorisation limit. Beyond the end-to-end quality suite, what targeted evaluation should the tool integration itself get?',
                'options' => [
                    'None — the end-to-end suite exercises the tool implicitly, so a separate suite is redundant',
                    'A latency benchmark of the refund API under peak load',
                    'An LLM judge that rates the elegance of the agent\'s tool-use reasoning',
                    'A code-graded suite covering schema conformance of the calls, recovery behaviour when the tool errors or returns empty, and permission boundaries — including cases proving the agent never invokes refunds above the authorised limit',
                ],
                'answer' => 3,
                'explain' => 'Tool seams get their own cheap, targeted, code-graded suites: valid call structure, graceful error handling, and — critical for a tool that moves money — explicit permission-boundary cases. End-to-end coverage alone is too sparse and too slow to reliably hit these edges on every change; a latency benchmark tests the API service rather than the agent\'s use of it; and judging reasoning elegance spends judge tokens on a question with exact, code-checkable answers.',
            ],
        ],
    ],

    // =====================================================================
    'ev-8-03' => [
        'body' => <<<'HTML'
<p>
The final architect skill in this domain is neither building suites nor reading dashboards —
it is telling the truth with numbers to people who will make decisions on them. An eval
practice that produces honest measurements and then reports them as a single cherry-picked
headline has wasted the honesty. This topic covers how results reach stakeholders, and who
keeps the practice alive afterwards.
</p>

<h3>Report against the criteria you agreed in advance</h3>
<p>
The reporting contract was written in Module 1: criteria and floors were fixed before results
existed, so the report's spine is simply the per-axis table — target, floor, measured value,
pass or fail — one row per axis, no blending. Alongside the numbers, three pieces of context
make the report interpretable rather than merely impressive:
</p>
<ul>
    <li><strong>Provenance and size</strong> — which dataset version, how many items, how many
        trials per item ("pass^4 = 91% over 380 items" reads very differently from "91%").</li>
    <li><strong>Known limitations</strong> — populations the suite under-represents, axes not
        yet measured, judge calibration status. Stated limitations build trust; discovered ones
        destroy it.</li>
    <li><strong>Offline vs. online status</strong> — "passed offline gates" means the change
        cleared the suite; "proven online" means the A/B or canary confirmed it with real
        traffic. Conflating the two overstates the evidence, and the exam treats the
        distinction as load-bearing (Module 4).</li>
</ul>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">The trap: the demo as evidence</div>
    <p>A polished live demo of the contract clause extractor finding every clause in one
    hand-picked agreement is marketing, not measurement. When a stem shows a stakeholder
    decision made on a demo or a handful of screenshots, the credited critique is that
    selected examples have no denominator — the suite result on the versioned dataset is the
    evidence; demos illustrate it.</p>
</div>

<h3>Translate to the business's units</h3>
<p>
Stakeholders act on money, risk, and time, not on F1. The translation is the architect's job:
a 70% → 76% deflection rate for the support agent is "roughly N fewer agent-handled tickets a
month at $9 each"; 98% clause recall is "about 2 in 100 in-scope clauses would still reach
counsel unflagged — here is the human-review step that catches them". Crucially, translation
runs both ways: when the business asks for "99.9% accuracy", the architect translates back
what that costs and whether humans achieve it (the achievability test from ev-1-01).
</p>

<h3>Someone must own the practice</h3>
<p>
Suites decay (Module 2), judges drift (Module 3), dashboards go stale (Module 7). None of the
maintenance happens without a named owner, a review cadence, and budgeted time — an eval
practice funded only at launch is a snapshot, not a practice. A useful maturity model for
where a team stands: <em>ad-hoc</em> (manual spot checks before releases) → <em>gated</em>
(versioned suites running in CI, releases blocked on floors) → <em>continuous</em> (online
evals and drift detection feeding a review loop that turns production failures into new suite
cases). The architect's remit is to move the team rightward and to make measurement the
shared language: engineering defends changes with suite results, the business states
requirements as measurable criteria, and disagreements resolve by running the eval.
</p>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Reporting stems credit: per-axis results against pre-agreed floors (never a blended
    score or a demo), dataset size/version and trial counts stated, limitations disclosed, and
    offline results labelled as offline. Ownership stems credit naming an owner with a cadence
    and budget over "everyone is responsible" or one-time launch testing. If an option
    translates a metric into business impact honestly — including what still fails — it is
    usually the credited one.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'An architect must present the contract clause extractor\'s readiness to the general counsel, who will decide whether lawyers can rely on it. Which presentation is most defensible?',
                'options' => [
                    'A live demonstration on three agreements chosen to showcase the extractor\'s strengths',
                    'The blended quality score (0.94) trending upward over the last five releases',
                    'Per-axis results on the versioned 300-contract suite — 98.4% recall and 91% precision against the pre-agreed floors, 4 trials per item — plus the stated limitation that handwritten amendments are out of scope, and the human-review step covering the ~1.6% of clauses still missed',
                    'A summary that the extractor "passed all internal testing" with sign-off from the engineering lead',
                ],
                'answer' => 2,
                'explain' => 'The per-axis presentation has everything honest reporting requires: numbers against floors agreed in advance, provenance and trial counts, disclosed limitations, and a truthful account of residual failure with its mitigation — exactly what a decision-maker weighing legal reliance needs. The showcase demo is selected examples with no denominator, the trending blended score hides which axis moved (the Module 1 trap), and "passed all internal testing" is unmeasured assurance.',
            ],
            [
                'q' => 'The support deflection team reports: "The new prompt passed all offline gates, so deflection is now 76%, up from 70%." What is the most important correction before this reaches leadership?',
                'options' => [
                    'The percentages should be converted to ticket counts for readability',
                    'Passing offline gates predicts but does not establish the online number — the 76% is an offline suite result, and the deflection claim needs the A/B or canary on real traffic to be stated as fact',
                    'The report should also include the judge\'s calibration statistics',
                    'Deflection rate should be replaced by CSAT as the headline metric',
                ],
                'answer' => 1,
                'explain' => 'The report commits the offline/online conflation: gates on the eval suite qualify a change to be trialled, while only online experimentation on production traffic proves the business metric moved (Module 4). Leadership reading "deflection is now 76%" would treat a prediction as a result. Converting to ticket counts is cosmetic, judge calibration statistics are worthwhile detail but not the load-bearing error, and swapping in CSAT changes the metric rather than fixing the evidential claim.',
            ],
            [
                'q' => 'Six months after a successful launch, the claims triage assistant\'s eval suite no longer reflects current claim types, the judge has not been recalibrated, and the dashboard\'s alert thresholds are widely ignored. What was the most likely root cause of this decay?',
                'options' => [
                    'The original suite was too small at launch',
                    'The team chose the wrong grader family in Module 3 terms',
                    'The model provider changed the underlying model without notice',
                    'No named owner, cadence, or budget was assigned to the eval practice after launch — maintenance work that predictably exists was left to nobody',
                ],
                'answer' => 3,
                'explain' => 'Every listed symptom — stale dataset, drifting judge, ignored alerts — is a maintenance failure, and maintenance does not happen without ownership, cadence, and budgeted time; an eval practice funded only at launch decays on exactly this schedule. A too-small suite or a wrong grader family are launch-time design flaws that would have shown up immediately, not at six months, and an unannounced model change would explain a metric shift but not the suite, judge, and dashboard all going stale.',
            ],
        ],
    ],
];
