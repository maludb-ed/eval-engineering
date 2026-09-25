<?php
/**
 * module-7.php — study content for Module 7: Monitoring and Observability.
 *
 * Grounded in Anthropic's published guidance on observability and evaluation
 * for Claude systems ("Create strong empirical evaluations", "Demystifying
 * evals for AI agents") and standard LLM-observability practice: the trace as
 * the unit of logging, layered metrics and alerting, online evals and drift
 * detection, and log privacy with a human review loop.
 *
 * Returns: [ topic-id => ['body' => html, 'questions' => [...] ] ]
 */
return [

    // =====================================================================
    'ev-7-01' => [
        'body' => <<<'HTML'
<p>
Traditional services log events; LLM systems must log <em>traces</em>. A trace is the full
lifecycle of one request: the user input, the retrieved context, the system prompt (by
version), the model and its configuration, every tool call and its result, every intermediate
model response, token counts and latency per step, the final output, and any user feedback
that follows. The reason is diagnostic: Module 5's entire method — replay the trace, read what
the model actually saw — is only possible if that record exists. A quality bug you cannot
replay is a bug you cannot attribute, and an unattributable bug gets "fixed" by guessing.
</p>

<h3>What a complete trace contains</h3>
<ul>
    <li><strong>Inputs as the model saw them</strong> — not the user's message alone, but the
        assembled context: retrieved chunks with their source ids, conversation history, tool
        definitions. For the policy knowledge assistant, "which chunks were retrieved" is the
        difference between diagnosing a retrieval failure and a generation failure.</li>
    <li><strong>Versions of everything</strong> — prompt version, model id, temperature and
        other config, retrieval index snapshot, tool schema version. Results are only
        attributable if you can say which combination produced them; "quality dropped after X"
        is unanswerable when X was never recorded.</li>
    <li><strong>Every step, not just the ends</strong> — for agents, each tool invocation with
        arguments and returned payload, and the model's response after each. Agent failures are
        usually mid-trajectory; input/output-only logging hides the step that went wrong.</li>
    <li><strong>Correlation ids</strong> — one id threaded through the gateway, the
        orchestrator, the retrieval service, and the model call, so a support ticket or an
        alert can be joined back to its trace across services.</li>
    <li><strong>Outcomes</strong> — final output, user feedback (thumbs, escalation, edit),
        and downstream signals such as "the human adjuster re-routed this claim".</li>
</ul>

<h3>Sampling without losing the evidence</h3>
<p>
At the claims triage assistant's 40k claims/day, storing every full trace may be unaffordable.
The standard compromise: sample complete traces (say, a few percent) for the healthy
population, but <strong>always retain 100% of failures, escalations, negative feedback, and
guardrail triggers</strong>. The traces you will need for diagnosis are precisely the
non-representative ones; uniform sampling optimises storage by discarding the evidence.
Aggregate metrics (Topic ev-7-02) cover the healthy majority cheaply.
</p>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">The trap: logging outputs but not context</div>
    <p>Teams often log the user question and the final answer and consider themselves covered.
    When a wrong answer surfaces, they re-run the question — and get a different, correct
    answer, because retrieval returned different chunks or the prompt has since changed. The
    bug is now unreproducible. The context the model saw is part of the input; if it is not
    stored, the trace is not a trace.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>When a stem asks "what should we log?", the credited answer names the full trace —
    assembled context, versions, per-step tool calls — not just inputs and outputs. When a stem
    describes an unreproducible production bug, the missing ingredient is almost always the
    stored context or the recorded prompt/model version. Sampling questions credit "sample the
    healthy traffic, keep all failures", never uniform sampling of everything.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'A user reports that the policy knowledge assistant gave a wrong answer about parental leave yesterday. The team re-runs the question today and gets a correct, well-grounded answer. Their logs contain the user question and the final response text. What is the most important logging change to make this class of bug diagnosable?',
                'options' => [
                    'Log the user\'s browser and session metadata so the environment can be reproduced',
                    'Increase log retention from 30 to 90 days so older incidents remain available',
                    'Add thumbs-up/thumbs-down capture so bad answers are flagged in real time',
                    'Store the full trace: the retrieved chunks, assembled prompt, prompt and model versions, and per-step outputs the model actually saw at the time',
                ],
                'answer' => 3,
                'explain' => 'The re-run succeeded because the model saw different inputs today — a different retrieval result or an updated prompt. Without the assembled context and versions from the original request, the failure cannot be replayed or attributed. Browser metadata rarely affects model behaviour; longer retention preserves more of the same insufficient logs; feedback capture flags failures but still leaves them unreproducible.',
            ],
            [
                'q' => 'The claims triage assistant processes 40k claims/day and full traces are too expensive to store for all of them. Which sampling policy best preserves diagnostic value?',
                'options' => [
                    'Sample a small percentage of healthy traffic, but retain 100% of failures, mis-routes flagged by adjusters, guardrail triggers, and escalations',
                    'Store a uniform 5% random sample of all traces to keep the dataset statistically representative',
                    'Store traces only for claims above a monetary threshold, since those matter most to the business',
                    'Store aggregate metrics only and re-run any claim that needs investigation',
                ],
                'answer' => 0,
                'explain' => 'Diagnosis needs the anomalous traces, which uniform sampling mostly discards — at a 2% failure rate and 5% sampling, you keep one failure in a thousand claims. Storing only high-value claims misses systematic failures on routine ones. Re-running claims on demand is exactly what non-determinism and changing context make unreliable. Sampling the healthy majority while keeping every failure gives cheap coverage plus complete evidence.',
            ],
            [
                'q' => 'After a quality drop in the support deflection agent, the team finds they cannot tell which of three prompt revisions deployed that week produced each logged conversation. What practice would have prevented this?',
                'options' => [
                    'Deploying prompt changes only once per week',
                    'Running the offline eval suite before each deployment',
                    'Recording the prompt version, model id, and configuration on every trace so each result is attributable to the exact combination that produced it',
                    'Keeping all three prompt revisions in the repository under version control',
                ],
                'answer' => 2,
                'explain' => 'Attribution requires the trace itself to carry version identifiers — repository version control records what the prompts were but not which one served a given request. Slower deploys reduce ambiguity but don\'t create attribution and cost iteration speed. Offline gates are good practice yet the question is about tracing a production symptom to a change, which needs versioned traces.',
            ],
        ],
    ],

    // =====================================================================
    'ev-7-02' => [
        'body' => <<<'HTML'
<p>
Traces answer "what happened on this request?"; metrics answer "how is the system doing?" A
Claude system needs metrics in four layers, because failures announce themselves in different
vocabularies: an outage speaks in error rates, a cost blowup in cache hit rates, a quality
regression in escalation rates, and an attack in guardrail triggers.
</p>

<h3>The four metric layers</h3>
<div class="table-responsive">
<table class="table table-sm align-middle">
    <thead>
        <tr class="text-secondary small text-uppercase">
            <th>Layer</th><th>Example metrics</th><th>What it catches</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>System health</td>
            <td>API error rate, timeout rate, rate-limit (429) hits, latency percentiles (p50/p90/p99)</td>
            <td>Outages, capacity limits, degraded dependencies</td>
        </tr>
        <tr>
            <td>Usage &amp; cost</td>
            <td>Tokens per task, cost per task, cache hit rate, traffic volume by route</td>
            <td>Cost regressions, runaway agent loops, cache invalidation</td>
        </tr>
        <tr>
            <td>Quality proxies</td>
            <td>Escalation rate, retry/rephrase rate, thumbs-down rate, refusal rate, conversation length</td>
            <td>Quality drift users feel before anyone files a ticket</td>
        </tr>
        <tr>
            <td>Safety &amp; security</td>
            <td>Guardrail/policy-violation flags, injection-canary results, anomalous tool-use patterns</td>
            <td>Harmful outputs, prompt-injection attempts, abuse</td>
        </tr>
    </tbody>
</table>
</div>

<p>
Quality proxies deserve emphasis because no direct "accuracy" signal exists in production —
there is no ground truth attached to live traffic. For the support deflection agent, rising
escalation rate, users rephrasing the same question, and lengthening conversations are the
observable shadows of falling answer quality. They are proxies, so they can be confounded (a
product launch raises escalations for reasons unrelated to the model), but a sustained move in
several proxies at once is a strong quality alarm.
</p>

<h3>Alerting: page on floors, ticket on drifts</h3>
<p>
Alert design decides whether monitoring works at 3 a.m. Two rules keep it honest. First, alert
on <strong>symptoms users feel</strong> (SLO breaches: p99 latency, error rate, escalation
spike) and on <strong>leading indicators</strong> of imminent damage — a collapsing cache hit
rate predicts a cost blowup hours before the invoice does; a spike in injection-canary hits
predicts a security incident. Second, tier the response: a breached floor pages a human; a
slow drift opens a ticket for working hours. Paging on every fluctuation trains on-call to
ignore the pager — alert fatigue is how real incidents get missed.
</p>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">Design dashboards around on-call questions</div>
    <p>A good dashboard answers, in order, the questions an on-call engineer actually asks: Is
    it up? Is it fast? Is it on budget? Is it good? Is it safe? — each drillable from the
    aggregate to the offending route to example traces (which is why trace correlation ids from
    ev-7-01 matter). A wall of forty unprioritised charts is storage, not observability.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Expect stems that hand you a symptom and ask which metric layer catches it: cost blowup
    → cache hit rate and tokens per task; silent quality drop → escalation/retry/thumbs-down
    proxies; injection attempt → guardrail canaries. Distractors offer paging on everything
    (alert fatigue) or a single blended "health score" (hides which axis broke — the same
    single-number trap as ev-1-01).</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The support deflection agent\'s offline eval scores are unchanged, but over three weeks escalation rate has climbed from 22% to 31%, users increasingly rephrase the same question, and average conversation length has grown. What do these signals most likely indicate?',
                'options' => [
                    'Nothing actionable — offline evals are unchanged, so quality is stable and traffic mix explains the rest',
                    'A production quality regression the offline suite does not capture: several independent quality proxies moving together is a strong alarm even without ground-truth labels',
                    'A latency problem, since longer conversations imply slower responses',
                    'An alerting misconfiguration inflating the escalation counter',
                ],
                'answer' => 1,
                'explain' => 'Production has no ground-truth accuracy signal, so quality is monitored through proxies — and escalations, rephrasing, and lengthening conversations are three independent proxies all pointing the same way. An unchanged offline score means the eval set no longer represents live traffic (a Module 2/ev-7-03 problem), not that quality is fine. C confuses conversation turns with response latency; D is conceivable but does not explain three separate signals agreeing.',
            ],
            [
                'q' => 'Which pairing of signal and response best reflects sound alerting design for the claims triage assistant?',
                'options' => [
                    'Page on-call for every thumbs-down and every 1% fluctuation in routing accuracy proxies',
                    'Alert only on a single blended health score combining latency, cost, and quality into one number',
                    'Send all alerts to a shared email folder reviewed each Monday to avoid interrupting engineers',
                    'Page on-call when p99 latency or error rate breaches its SLO floor or the cache hit rate collapses; open a working-hours ticket for a slow multi-week drift in escalation rate',
                ],
                'answer' => 3,
                'explain' => 'Tiered response is the core discipline: hard floors and leading indicators of imminent damage (cache collapse → cost blowup) page a human; slow drifts become tickets. Paging on every fluctuation produces alert fatigue and ignored pagers; weekly batch review means SLO breaches go unseen for days; a blended score fires without saying which axis broke and lets one axis mask another.',
            ],
            [
                'q' => 'Monday\'s invoice shows the research report agent\'s daily cost tripled starting Thursday, with no traffic increase and no quality complaints. Which metric, had it been alerted on, would have caught this earliest, and why?',
                'options' => [
                    'Cache hit rate and tokens per task — a cache invalidation or a lengthening agent loop shows up in these hours before the bill does',
                    'Thumbs-down rate — cost problems eventually surface as user dissatisfaction',
                    'p99 end-to-end latency — expensive requests are slow requests',
                    'Guardrail violation rate — a jailbreak campaign could be driving expensive traffic',
                ],
                'answer' => 0,
                'explain' => 'With flat traffic, a 3× cost jump means each task consumes more: a prompt edit that broke the cache prefix (writes billed at a premium, reads no longer discounted) or an agent looping through more steps. Both appear immediately in cache hit rate and tokens per task — the usage/cost layer\'s leading indicators. Users never see cost, so thumbs-down rate stays flat; latency correlates only loosely and pages for the wrong reason; nothing suggests abuse, and volume was flat.',
            ],
        ],
    ],

    // =====================================================================
    'ev-7-03' => [
        'body' => <<<'HTML'
<p>
Offline evals grade a frozen dataset; production traffic never freezes. Online evaluation
closes the gap by running graders on samples of live traffic, so quality is measured where it
matters and drift is caught while it is still a trend rather than an incident.
</p>

<h3>The graded-sampling pyramid</h3>
<p>
The economics follow Module 3's "cheapest grader that answers the question" principle, applied
in layers:
</p>
<ul>
    <li><strong>Code checks on everything</strong> — schema validity, required citations
        present, response non-empty, length within bounds, no leaked system-prompt markers.
        Near-zero cost, so they run on 100% of traffic.</li>
    <li><strong>LLM judge on a sample</strong> — a calibrated rubric judge scores a few percent
        of traces for groundedness, resolution, or tone. Sampled because judge tokens cost real
        money at production volume.</li>
    <li><strong>Human review on a smaller sample</strong> — the top of the pyramid, anchoring
        the judge\'s calibration (ev-3-04) and catching what neither code nor judge can see.</li>
</ul>
<p>
Scores from this pyramid, compared against the offline baseline established at release, are
the drift detector: when the production judge score slides while the offline suite still
passes, the world has moved away from the eval set.
</p>

<h3>Where drift comes from</h3>
<ul>
    <li><strong>Input distribution shift</strong> — new user segments, seasonal topics (open
        enrolment hits the policy knowledge assistant every autumn), a product launch changing
        what support customers ask about, refreshed document versions changing what retrieval
        returns.</li>
    <li><strong>Dependency changes</strong> — a model version update, a retrieval index
        rebuild, an embedding model swap, a tool API changing its response format. The system
        "didn\'t change" from the team\'s perspective, but its behaviour did.</li>
    <li><strong>Configuration accretion</strong> — many small prompt tweaks, each individually
        gated, whose cumulative interaction was never evaluated as a whole.</li>
</ul>

<h3>Detection mechanics: trends and golden traces</h3>
<p>
Two complementary instruments. First, <strong>trend monitoring</strong>: track online grader
scores and input-feature distributions (topic mix, input length, retrieval-score distribution)
over time; a shift in inputs often precedes the quality drop it will cause. Second,
<strong>golden traces</strong>: a canary set of fixed, representative requests replayed on a
schedule against the live stack. Because the inputs are constant, any score change isolates
the system side — a model update or index rebuild moves golden-trace scores while pure input
drift does not. Read together, the two localise the cause: live scores down + golden traces
stable → the inputs changed; both down → the system changed.
</p>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">Drift response is diagnosis, not quiet re-tuning</div>
    <p>The wrong response to drift is nudging the prompt until dashboards look green again —
    that treats the symptom, leaves the offline suite stale, and guarantees the next change is
    gated against a dataset that no longer represents production. The right sequence: diagnose
    the drift source (Module 5), harvest representative new traces into the eval set (Module
    2\'s living suite), then fix and gate against the refreshed suite.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Credited patterns: layered online grading (code on all, judge on a sample, humans on
    less); golden traces to separate input drift from system drift; and "update the eval set,
    then fix" as the drift response. Distractors include running the full judge on 100% of
    traffic (cost-blind), concluding from a passing offline suite that production quality is
    fine (stale-suite trap), and silent prompt re-tuning.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The policy knowledge assistant\'s online judge score has declined for three weeks, but the nightly replay of its golden-trace canary set scores exactly as it did at release. What is the most supported conclusion?',
                'options' => [
                    'The judge has drifted and should be recalibrated before anything else is investigated',
                    'The system is fine, since golden traces prove the stack is unchanged',
                    'The input distribution has shifted — live questions have moved away from what the eval set and canaries represent — so new production traces should be harvested into the eval set and the drop diagnosed against them',
                    'A model version update degraded quality and the deployment should be rolled back',
                ],
                'answer' => 2,
                'explain' => 'Golden traces hold inputs constant, so stable canary scores mean the system side (model, prompt, index) still performs as at release — which rules out a degrading model update. The live decline must therefore come from the inputs: new topics, new document versions, new user segments. Nor is the system "fine": real users are getting worse answers, and the eval set no longer represents them. Judge drift would typically move both live and canary scores, since the same judge grades both.',
            ],
            [
                'q' => 'A team proposes running their calibrated LLM judge on 100% of the claims triage assistant\'s 40k daily claims to get complete quality coverage. What is the strongest objection, and the better design?',
                'options' => [
                    'Judges cannot run on production data for privacy reasons; use offline evals only',
                    'Judge grading at full volume costs roughly as much as a second production system; run cheap code checks on everything, the judge on a sample, and human review on a smaller sample, sized to detect the drifts that matter',
                    'The judge would slow every claim down; run it only at night',
                    'Full coverage is correct — quality signals should never be sampled',
                ],
                'answer' => 1,
                'explain' => 'A judge call per claim means 40k extra model calls a day — the cost pyramid exists precisely because trend detection does not need a census; a well-sized sample detects a multi-point score drift with high confidence. Privacy concerns are handled by access controls and redaction (ev-7-04), not by abandoning online evaluation. Latency is a red herring: online grading runs asynchronously on stored traces, not in the request path.',
            ],
            [
                'q' => 'After detecting a genuine quality drift in the support deflection agent, an engineer tweaks the system prompt until the online dashboard returns to green and closes the incident. Why is this response inadequate?',
                'options' => [
                    'Prompt changes require a full A/B test before deployment under all circumstances',
                    'The engineer should have switched to a larger model tier instead, since drift indicates capability shortfall',
                    'Dashboards should be recalibrated rather than the prompt changed',
                    'The drift was never diagnosed and the offline suite was never updated — the symptom is patched, the cause is unknown, and every future change will be gated against a dataset that no longer represents production',
                ],
                'answer' => 3,
                'explain' => 'Green dashboards after blind tuning prove only that the proxy moved, not that the cause was found — the same drift source (new document versions, a changed traffic mix) may be degrading other behaviours unmeasured. The credited loop is diagnose → harvest new representative traces into the living suite → fix → gate. Demanding a full A/B for every prompt change overstates process, recalibrating dashboards treats measurement as the problem, and a larger model tier reaches for capability when nothing indicates it.',
            ],
        ],
    ],

    // =====================================================================
    'ev-7-04' => [
        'body' => <<<'HTML'
<p>
The traces that make a Claude system observable are also a concentration of user data: claim
details, HR questions, customer account information, contract text. Observability design is
therefore privacy design — and the final piece of the monitoring story is the human loop that
turns all this logging back into a better system.
</p>

<h3>Privacy principles for trace stores</h3>
<ul>
    <li><strong>Minimise</strong> — log what diagnosis needs, not everything you can capture.
        If a field never features in debugging or evaluation, it is pure liability.</li>
    <li><strong>Redact and mask</strong> — strip or tokenise PII (names, account and policy
        numbers, contact details) where the diagnostic value survives redaction. A trace can
        prove a routing failure without the claimant\'s real identity attached.</li>
    <li><strong>Control and audit access</strong> — trace stores hold what users told the
        system in confidence; access should be role-restricted and itself logged, so "who read
        this transcript" is always answerable. This matters doubly for the policy knowledge
        assistant, whose traces contain employees\' HR questions.</li>
    <li><strong>Split retention by data class</strong> — raw traces on a short window,
        long enough for diagnosis and eval harvesting; aggregate metrics, which carry no user
        content, kept long-term for trend analysis. Regulated domains invert the pressure:
        the claims triage assistant may face record-keeping rules that <em>mandate</em>
        multi-year retention of decision records — retention windows are set by policy and
        regulation, not by disk cost.</li>
    <li><strong>Be transparent about feedback data</strong> — if thumbs-down conversations get
        human review, users should know that; consent and disclosure obligations extend to the
        review pipeline, not just the product.</li>
</ul>

<h3>The review loop: where monitoring pays for itself</h3>
<p>
Dashboards detect; humans understand. A regular transcript-review session — engineers, and
periodically domain experts, reading a sample of real traces — is the highest-yield ritual in
the whole monitoring stack, because it is where logged failures become eval cases. Every
reviewed failure that survives triage enters the offline suite (Module 2\'s living suite),
which is how the eval set tracks production instead of fossilising at launch.
</p>
<p>
Review the successes too. A sample of <em>high-scoring</em> traces checks whether the online
judge\'s top marks are deserved — if reviewers keep finding mediocre answers wearing perfect
scores, the judge is being gamed or has drifted (ev-3-05), and that discovery only happens if
someone looks at the "good" pile. Reviewing only failures audits the system; reviewing both
tails audits the system <em>and its graders</em>.
</p>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">Make review cheap enough to happen</div>
    <p>Review loops die of friction. A tool that renders a full trace readably — context,
    steps, output, scores, one click to "add to eval set" — turns an hour of review into ten
    new eval cases. If harvesting a failure into the suite takes manual copy-paste across four
    systems, the living suite quietly stops living.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Retention questions credit split policies (short-lived raw traces, long-lived
    aggregates) and recognise that regulation can force retention up as well as down. Privacy
    questions credit minimisation, redaction, and audited access over "don\'t log" (which
    destroys diagnosability) or "log everything forever" (which is a breach in waiting).
    Review-loop questions credit sampling both failures and high-scoring traces — the
    grader-gaming check is a favourite.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'Legal asks the claims triage team to minimise stored user data, while insurance regulators require claim-decision records to be retained for seven years. Which trace-store design best satisfies both?',
                'options' => [
                    'Split by data class: redacted raw traces on a short diagnostic window with audited access; durable decision records (input summary, route, versions, rationale) retained seven years in the regulated system; long-term trends kept as content-free aggregate metrics',
                    'Stop storing traces entirely and keep only the final routing decision in the claims system of record',
                    'Keep all raw traces for seven years in the observability store, since regulation overrides privacy preferences',
                    'Anonymise all traces on ingestion so both retention questions become moot',
                ],
                'answer' => 0,
                'explain' => 'The two pressures apply to different data classes: regulation mandates the decision record, not every intermediate model response, while privacy pushes raw diagnostic traces toward short retention, redaction, and audited access. Dropping traces entirely destroys diagnosability and arguably under-complies (the decision record alone may not show the basis of the decision); keeping all raw traces for seven years over-retains sensitive content, maximising breach exposure; full anonymisation is often impossible — anonymising claim narratives typically strips the very details diagnosis and compliance need.',
            ],
            [
                'q' => 'A monthly review of the support deflection agent samples only conversations with thumbs-down or escalation flags. The online judge reports 94% quality. What important failure mode does this review design miss?',
                'options' => [
                    'Seasonal variation in complaint volume',
                    'Failures on conversations where users gave no feedback at all',
                    'Judge gaming or drift: if high-scoring conversations are never human-reviewed, the judge\'s 94% could be crediting confident, well-formatted answers that are actually mediocre or wrong, and nobody would ever notice',
                    'Nothing — reviewing failures is precisely what review sessions are for',
                ],
                'answer' => 2,
                'explain' => 'Reviewing only flagged failures audits the system but never audits the grader. The judge\'s top scores are a claim that needs spot-checking: a sample of high-scoring traces is the only way to discover that the judge rewards style over substance (ev-3-05). No-feedback conversations are a real gap, but they are at least partly covered by judge sampling — whereas the judge\'s own top marks have no check at all under this design, making grader gaming the failure mode this specific setup structurally cannot detect.',
            ],
            [
                'q' => 'During transcript review, an engineer finds a novel failure: the policy knowledge assistant confidently cited a superseded policy document. The fix is deployed the same day. According to the review-loop discipline, what essential step remains?',
                'options' => [
                    'File the incident in the postmortem archive and schedule a retrospective',
                    'Add the failing case (and variants probing the same superseded-document weakness) to the offline eval suite, so the failure class is gated against in every future release',
                    'Increase the online judge sampling rate for a week to confirm the fix worked',
                    'Notify affected users that they may have received outdated policy information',
                ],
                'answer' => 1,
                'explain' => 'The review loop\'s purpose is converting logged failures into permanent eval coverage — a fix without a regression test is protection only until the next prompt or index change reintroduces the bug silently. Postmortem filing and user notification may be appropriate process steps (notification especially if harm occurred) but neither prevents recurrence; a week of extra judge sampling offers statistical reassurance where an eval case offers a permanent gate. The living suite grows from production failures, and this is the moment that growth happens.',
            ],
        ],
    ],
];
