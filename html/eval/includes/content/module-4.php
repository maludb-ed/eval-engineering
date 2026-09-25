<?php
/**
 * module-4.php — study content for Module 4: A/B Testing and Iterative Improvement.
 *
 * Grounded in Anthropic's published guidance: "Create strong empirical
 * evaluations" and "Define your success criteria" (docs.claude.com), the
 * engineering post "Demystifying evals for AI agents", and standard online
 * experimentation practice as it applies to Claude systems.
 *
 * Returns: [ topic-id => ['body' => html, 'questions' => [...] ] ]
 */
return [

    // =====================================================================
    'ev-4-01' => [
        'body' => <<<'HTML'
<p>
Offline evals and online experiments answer different questions, and the exam expects you to
know which question each one can actually answer. An offline eval runs your system against a
fixed dataset with known graders — no users involved. An online experiment (A/B test, canary)
exposes real traffic to the change and measures what real users experience. Teams get into
trouble when they ask one to do the other's job.
</p>

<h3>What offline evals prove — and what they can't</h3>
<p>
Offline evals are fast, cheap, and reproducible: the same dataset, harness, and graders produce
comparable numbers run after run, so you can iterate on a prompt ten times in an afternoon and
attribute every delta to the change you made. They run <em>before</em> anything ships, so no
user is exposed to a regression. Their limit is coverage: an offline eval measures performance
on <strong>what the dataset contains</strong>, nothing more. If the eval set for the support
deflection agent under-represents angry multi-turn billing disputes, an offline win says
nothing about them. Offline results are also measured by your graders, not by user outcomes —
a judge score is a proxy for satisfaction, not satisfaction itself.
</p>

<h3>What online experiments prove — and what they cost</h3>
<p>
An online experiment measures the real distribution: real users, real phrasing, real edge
cases, real downstream behaviour (did the customer stop contacting support, or come back
angrier?). It is the only way to observe effects your dataset never encoded. But it is slow
(weeks to reach sample size), noisy (user behaviour varies for a hundred reasons besides your
change), and it <strong>exposes users to risk</strong> — a bad variant reaches real customers
before you learn it is bad. It is also a blunt diagnostic: an A/B test tells you variant B
resolved 3% fewer conversations, not <em>why</em>.
</p>

<h3>Offline gates, online confirms</h3>
<p>
Anthropic's guidance treats the two as a sequence, not alternatives. The offline eval is the
<strong>gate</strong>: no change reaches traffic until it beats (or matches) the baseline on
the suite with no axis below its floor. The online experiment is the
<strong>confirmation</strong>: it verifies the offline win survives contact with the real
distribution. Skipping the gate means experimenting on customers with changes you could have
rejected for free; skipping the confirmation means trusting that your dataset is the world —
which, per Module 2, it never fully is.
</p>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">The trap: "the eval improved, ship it everywhere"</div>
    <p>An offline improvement is a claim about your dataset. The larger the gap between the
    eval set and live traffic — new user segments, drifted topics, adversarial inputs — the
    weaker the claim. Equally trapped is the reverse team that A/B tests every prompt tweak
    directly in production "because only real data counts": they burn weeks of traffic and
    expose users to variants an afternoon of offline evaluation would have killed.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Given a scenario, ask which claim the team needs: "is this change safe and probably
    better?" → offline eval against the baseline. "Does the improvement hold for real users
    and business outcomes?" → online experiment. Distractors offer online testing as a
    substitute for an eval suite (risky, slow, unattributable) or offline scores as final
    proof of business impact (dataset ≠ world). The credited sequence is almost always:
    offline gate first, then a controlled online confirmation.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The support deflection agent team has a prompt change they believe improves resolution rate. They propose skipping the offline suite and going straight to a 50/50 A/B test "because only real-user data matters". What is the strongest objection?',
                'options' => [
                    'The offline gate would catch regressions (including safety and formatting failures) in an afternoon at no user risk; the A/B test exposes half of all customers to an unvetted variant for weeks to learn something the suite could partially answer for free',
                    'A/B tests are statistically invalid for LLM systems because outputs are non-deterministic',
                    'Online experiments can only measure latency, not quality',
                    '50/50 splits are never appropriate; canaries must start at 1%',
                ],
                'answer' => 0,
                'explain' => 'The sequence is offline gates, online confirms. Offline evaluation is fast, cheap, and risk-free, so it should filter out losers and regressions before any customer sees the change. The claim that A/B tests are statistically invalid for LLM systems is false — non-determinism just adds variance — as is the claim that online experiments can only measure latency. The canary-percentage point has a kernel of truth (progressive rollout is good practice) but is not the strongest objection to skipping evaluation entirely.',
            ],
            [
                'q' => 'A new retrieval strategy for the policy knowledge assistant beats the baseline by 6 points on the offline groundedness suite. Which statement most accurately describes what this result proves?',
                'options' => [
                    'The change will improve groundedness for real employee queries by about 6 points',
                    'The change is safe to ship permanently, since groundedness is the primary metric',
                    'Nothing — offline evals cannot measure groundedness because it requires human judgment',
                    'The change improves groundedness on the distribution the eval set represents; whether that transfers to live traffic depends on how well the set matches real queries, which an online check should confirm',
                ],
                'answer' => 3,
                'explain' => 'An offline result is a claim about the dataset, not the world. Six points on the suite is a strong gate-passing signal, but transfer to production depends on dataset representativeness — hence confirmation via canary or A/B. The "about 6 points for real queries" reading overstates (it assumes perfect representativeness), the claim that groundedness cannot be measured offline is false (graded groundedness suites are standard), and "safe to ship permanently" ignores both the other axes and the need for online confirmation.',
            ],
            [
                'q' => 'Which question can ONLY an online experiment answer for the claims triage assistant?',
                'options' => [
                    'Does the new prompt route the 500-claim eval set more accurately than the old one?',
                    'Does the new prompt produce valid JSON for every eval-set claim?',
                    'Does the new routing behaviour actually reduce end-to-end payout delays for real claimants over the following weeks?',
                    'Does the new prompt regress on the prompt-injection suite?',
                ],
                'answer' => 2,
                'explain' => 'Downstream business outcomes on live traffic — real payout delays for real claimants — exist only in production; no offline dataset encodes them. Options A, B and D are exactly what offline suites are for: fixed datasets, code-checkable formats, and adversarial regression suites all run pre-ship, cheaply and without exposing users.',
            ],
        ],
    ],

    // =====================================================================
    'ev-4-02' => [
        'body' => <<<'HTML'
<p>
Iterative improvement is a loop, and the loop has a fixed order. Anthropic's guidance on
empirical evaluation assumes every change — prompt wording, retrieval settings, tool
definitions, model version — travels the same path: <strong>hypothesis → offline eval against
the baseline → regression gate → staged rollout → online confirmation → feed new failures back
into the suite</strong>. The exam tests whether you can spot which step a struggling team
skipped.
</p>

<h3>The regression gate</h3>
<p>
The gate is not "did the target metric improve?" — it is "did <em>no</em> axis fall below its
floor?" A prompt change that lifts the contract clause extractor's recall by 2 points while its
JSON validity drops from 99.8% to 96% fails the gate: the improvement on the axis you were
watching does not buy back the regression on the one you weren't. That is why the gate reruns
the <em>whole</em> suite — accuracy, latency, cost, format, and always the safety and security
suites, because injection resistance and refusal behaviour are properties of the full prompt
and can silently shift with any wording change.
</p>

<h3>One change at a time, against a fixed baseline</h3>
<ul>
    <li><strong>Change one variable per iteration.</strong> If you swap the model tier and
        rewrite the system prompt together and quality moves, you have learned nothing
        attributable. When a stem shows a team unable to explain a delta, look for the
        bundled change.</li>
    <li><strong>Keep the baseline fixed and versioned.</strong> Comparing today's candidate to
        last week's candidate to the week before produces drift with no anchor. The comparison
        is always candidate vs. the current production baseline, on the same dataset, same
        graders, same harness.</li>
    <li><strong>Version everything that affects behaviour</strong> — prompts, tool schemas,
        retrieval configs, model IDs — so any eval result can be reproduced and any production
        incident traced to the exact configuration that shipped (Module 7 picks this up for
        logging).</li>
</ul>

<h3>Model upgrades go through the same gate</h3>
<p>
A new Claude model version is a change like any other — usually a beneficial one, but prompts
tuned around an old model's quirks can behave differently on a new one. The procedure: run the
full suite on the new model, compare against the current baseline, fix what regressed (often by
<em>removing</em> old workarounds), then re-baseline so future candidates compare against the
new configuration. Pinning a model version forever to avoid re-evaluation is the opposite
failure: the gate exists so upgrades are cheap, not so change is forbidden.
</p>

<h3>Close the loop</h3>
<p>
The final step is the one teams forget: every failure discovered during rollout or in
production becomes a new eval case, so the suite grows toward the real failure distribution
(Module 2's "keep the suite alive"). A shipping sequence without this step re-litigates the
same bugs forever.
</p>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">Why the sequence has this order</div>
    <p>Each stage is more expensive and riskier than the one before: an offline run costs
    dollars, a canary costs a slice of user trust, a full rollout costs all of it. The sequence
    exists to spend the cheap, safe stage first and reserve traffic for changes that have
    already earned it.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Three reliable stems: (1) "metric X improved, metric Y quietly regressed, team shipped"
    → the gate must check every axis floor, not just the target; (2) "quality dropped after
    the model upgrade" → the upgrade skipped the eval-and-re-baseline step, and the fix is
    running the suite and adjusting prompts, not refusing all upgrades; (3) "we changed three
    things and can\'t explain the improvement" → one change at a time, fixed baseline.
    The distractor pattern is any answer that ships on a single-axis win.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'After a system-prompt rewrite, the claims triage assistant\'s routing accuracy rises from 91% to 94% on the eval suite. The team ships it the same day. A week later, security flags that the assistant now follows instructions embedded in claim attachments. What step of the shipping sequence was skipped?',
                'options' => [
                    'Online confirmation — an A/B test would have caught the injection behaviour',
                    'Re-baselining — the 94% should have been compared to the original launch baseline, not last month\'s',
                    'Hypothesis formation — the team should have predicted the security impact before editing',
                    'The regression gate — the prompt-injection suite must be rerun on every prompt change, because injection resistance is a property of the full prompt and can shift with any rewording',
                ],
                'answer' => 3,
                'explain' => 'The gate reruns the whole suite — including safety and security — on every change, precisely because a wording change made for accuracy can loosen injection resistance. An A/B test measures user metrics, not adversarial robustness, and would likely miss it. Re-baselining and hypothesis formation describe real practices, but neither would have caught this: only rerunning the adversarial suite does.',
            ],
            [
                'q' => 'A team upgrades the support deflection agent to a new Claude model version and simultaneously rewrites the escalation instructions "while we\'re in there". Resolution rate drops 4 points. What is the principal methodological failure?',
                'options' => [
                    'They bundled two changes, so the regression cannot be attributed — the model swap and the prompt rewrite must be evaluated separately against the fixed baseline',
                    'They upgraded the model at all; production systems should pin model versions permanently',
                    'They measured resolution rate instead of CSAT',
                    'They should have increased the eval set size before upgrading',
                ],
                'answer' => 0,
                'explain' => 'One change per iteration is the rule precisely because attribution dies otherwise: the drop could be the model, the rewrite, or an interaction. The recovery is to test each change alone against the baseline. Never upgrading at all is the opposite error — the gate exists to make upgrades safe, not forbidden. The metric choice and the eval-set size are unrelated to why the team cannot now diagnose the drop.',
            ],
            [
                'q' => 'The contract clause extractor\'s candidate prompt improves recall from 96.1% to 97.4%, but JSON schema validity falls from 99.9% to 97.0% and p90 latency rises 15% (still under its floor). Under a correctly designed regression gate, what happens?',
                'options' => [
                    'Ship — the primary metric improved and latency is within its floor',
                    'Ship, but add a JSON repair step in post-processing next sprint',
                    'Block — schema validity fell below its floor; the recall gain cannot buy back a regression on another gated axis, so the candidate goes back for iteration',
                    'Run an A/B test to let production data decide between the two prompts',
                ],
                'answer' => 2,
                'explain' => 'The gate is "no axis below its floor", not "primary metric up". A 3-point drop in schema validity on a batch pipeline means thousands of failed parses; the win on recall does not offset it. A ships on a single-axis win (the classic trap), B ships a known regression on a promise, and D spends production traffic on a candidate the free offline gate has already disqualified.',
            ],
        ],
    ],

    // =====================================================================
    'ev-4-03' => [
        'body' => <<<'HTML'
<p>
An A/B test for an LLM system is ordinary experimentation with a few sharp edges specific to
conversational, high-variance workloads. The exam probes the edges: what to randomise, what to
declare in advance, and when the honest answer is "this test cannot detect the effect you care
about".
</p>

<h3>Randomise the user, not the request</h3>
<p>
For multi-turn systems like the support deflection agent, the unit of randomisation is the
<strong>user (or at minimum the session)</strong>, not the individual request. Randomising per
request lets one conversation straddle both variants — the user gets variant A's persona on
turn one and variant B's on turn two — which corrupts the experience and the measurement, and
contaminates session-level metrics like resolution rate. User-level assignment also keeps a
returning customer in the same arm, which matters whenever behaviour has memory.
</p>

<h3>Declare the decision rule before the data arrives</h3>
<ul>
    <li><strong>One primary metric</strong>, chosen in advance (e.g. resolution-without-escalation
        rate), which alone decides "did B win?".</li>
    <li><strong>Guardrail metrics</strong> — latency, cost per conversation, escalation rate,
        safety incidents — which cannot make B win but can veto it (Topic ev-4-04).</li>
    <li><strong>Sample size and duration computed up front</strong> from the minimum effect you
        care about. LLM quality deltas are usually small (a few points) and user metrics are
        high-variance, so honest tests often need weeks of traffic. An underpowered test does
        not "give a rough idea" — it reads noise as signal in whichever direction the noise
        happened to fall.</li>
</ul>

<h3>Peeking and post-hoc slicing</h3>
<p>
Checking results daily and stopping the moment B looks ahead inflates false positives —
favourable noise arrives eventually in any A/A test, and stopping on it enshrines the noise.
Run the test to its planned end (or use a sequential design built for interim looks). The same
discipline applies to segments: it is fine to <em>plan</em> a comparison of new vs. power
users; it is fishing to slice results twenty ways afterwards and report the one segment where
B "won" — at twenty slices, one spurious winner is expected by chance alone.
</p>

<h3>When traffic is scarce</h3>
<p>
Low-volume systems (the research report agent might serve dozens of users) cannot power a
classic A/B test. Alternatives: <strong>shadow evaluation</strong> — run the candidate on live
inputs without showing users its output, and grade both variants offline; or
<strong>interleaving</strong> — where the product allows, present outputs from both variants
and use preference signals, which detects differences with far less traffic. Both trade some
realism for feasibility, and both are more credible than an underpowered A/B test dressed up
as evidence.
</p>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">The trap: the three-day A/B test</div>
    <p>A stem that says "after three days, variant B leads by 2 points, so the team shipped it"
    is describing noise worship. Two-point deltas on high-variance user metrics need
    predeclared power calculations and usually far more data. The credited answer computes the
    required sample first — or admits the test cannot answer the question and reaches for
    shadow or interleaved evaluation.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Four discriminations recur: request- vs. user-level randomisation (user/session is
    credited for anything multi-turn); primary vs. guardrail metrics (declared in advance,
    one decides, others veto); stopping early on a favourable interim result (never credited);
    and the scarce-traffic escape hatch (shadow or interleaving, not a hopeless underpowered
    test). If the stem mentions "the team checked daily and stopped when significant", the
    flaw is peeking.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The support deflection agent team A/B tests a new system prompt by randomising each incoming <em>message</em> to variant A or B. Mid-conversation, users see the agent\'s tone and escalation behaviour change between turns. Beyond the odd experience, why is the measurement itself compromised?',
                'options' => [
                    'Message-level randomisation halves the effective sample size',
                    'Session-level outcomes like resolution and escalation cannot be attributed to either variant when a single conversation mixes both, so the primary metric is contaminated',
                    'Randomisation at any level below the data centre introduces network bias',
                    'It is not compromised — more granular randomisation always increases statistical power',
                ],
                'answer' => 1,
                'explain' => 'The primary metric (resolution without escalation) is a property of the whole conversation; when turns alternate between variants, neither arm owns the outcome, and the estimate of either variant\'s effect is biased toward the mixture. The fix is randomising by user or session. A and C are invented mechanics, and D ignores that granularity below the outcome\'s unit destroys attribution rather than adding power.',
            ],
            [
                'q' => 'Three days into a planned four-week A/B test, the claims triage assistant\'s variant B shows a 2.1-point routing-quality lead, nominally significant at p&lt;0.05. The PM wants to stop the test and ship B. What is the correct response?',
                'options' => [
                    'Ship — statistical significance is the predeclared decision criterion',
                    'Restart the test with a larger sample to be safe',
                    'Ship, but keep 10% of traffic on A indefinitely as insurance',
                    'Continue to the planned duration: repeatedly checking and stopping on a favourable interim result inflates false positives, so an early "significant" lead is disproportionately likely to be noise',
                ],
                'answer' => 3,
                'explain' => 'This is the peeking problem: with daily looks, even an A/A test will cross the significance threshold at some interim point far more than 5% of the time. Significance is only meaningful at the predeclared sample size (or under a sequential design built for interim analyses). Shipping on the nominal p-value mistakes the number for the procedure, restarting discards valid data for no reason, and the 10% holdback is an insurance strategy rather than an answer to the inference problem.',
            ],
            [
                'q' => 'The research report agent serves ~40 analysts, far too few for a powered A/B test on report quality. The team still wants live-traffic evidence before switching to a cheaper model tier. Which design is most appropriate?',
                'options' => [
                    'Shadow evaluation: run the candidate tier on the same live requests without exposing its output, and grade both variants\' reports with the calibrated judge and spot-check humans',
                    'Run the A/B test anyway for a quarter and treat whatever difference emerges as directional',
                    'Switch everyone to the cheaper tier and roll back if complaints arrive',
                    'Survey the analysts about whether they would mind a cheaper model',
                ],
                'answer' => 0,
                'explain' => 'Shadow evaluation gets real-input evidence with zero user exposure and no sample-size dependence on user-behaviour variance — ideal for low-traffic, high-stakes comparisons. Running the underpowered test anyway launders noise as "directional" evidence (in whichever direction it happened to fall). Switching everyone uses complaints as the detection mechanism, exposing all users to an unvetted change. A survey measures sentiment about the idea, not the quality of the output.',
            ],
        ],
    ],

    // =====================================================================
    'ev-4-04' => [
        'body' => <<<'HTML'
<p>
The last mile of shipping is where good experiment design either holds or folds: the win on
the primary metric must survive its guardrails, the rollout must be progressive, and the
rollback decision must be automatic. All three are decided <em>before</em> the rollout starts —
the recurring theme of this topic is that discretion under pressure loses to predeclared
thresholds.
</p>

<h3>Guardrail metrics can veto a win</h3>
<p>
A guardrail metric is one that cannot make the candidate win but can disqualify it: p90
latency, cost per conversation, escalation rate, refusal/over-refusal rate, safety-incident
count, format-error rate. The support deflection agent's variant B might lift resolution by 3
points while quietly doubling escalations from the elderly-customer segment or pushing p99
latency past the SLO — the primary metric says ship, the guardrails say no, and the guardrails
win. This is the online mirror of the offline regression gate: no single-axis victory buys a
regression elsewhere.
</p>

<h3>Progressive rollout with automatic rollback</h3>
<ul>
    <li><strong>Canary first:</strong> route a small slice (1–5%) of traffic to the new
        configuration; monitor primary, guardrail, and error metrics against the control.</li>
    <li><strong>Widen in stages:</strong> 5% → 25% → 50% → 100%, holding at each stage long
        enough for tail behaviour and slow feedback (escalations, complaints) to surface.</li>
    <li><strong>Predeclared rollback criteria:</strong> "if safety incidents &gt; 0, or
        escalation rate rises &gt; 2 points, or p99 latency exceeds 8 s for 15 minutes, revert
        automatically." The rollback decision belongs to a threshold written down before the
        rollout, not to a meeting convened while the graph is on fire — meetings under sunk-cost
        pressure talk themselves into "one more day".</li>
    <li><strong>Feature flags for prompts and models:</strong> the new prompt, model version,
        or retrieval config sits behind a flag, so reverting is a config change measured in
        seconds, not a redeploy measured in hours.</li>
</ul>

<h3>Online signals worth watching during rollout</h3>
<p>
Explicit feedback (thumbs up/down, CSAT) is sparse and biased toward extremes, so pair it with
behavioural signals: users rephrasing the same question repeatedly (the answer isn't landing),
abandonment mid-conversation, retry loops, escalation and handoff rates, and — for agentic
systems like the code migration agent — downstream hard outcomes such as PR revert rates.
Behavioural signals arrive at full traffic volume and don't depend on users volunteering an
opinion; a spike in rephrasing is often the earliest detectable symptom of a quality
regression.
</p>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">Why automatic beats deliberative</div>
    <p>By the time a human meeting concludes that the canary is unhealthy, the canary has been
    unhealthy for hours and the team that shipped it has argued for its survival. Predeclared
    thresholds convert the highest-pressure decision in the release cycle into a config rule —
    the same reasoning as ev-1-01's "write down the trade-off rule in advance".</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Credit flows to answers with three properties: guardrails that veto (not "weigh
    against") the primary metric; rollout that widens in monitored stages behind a flag; and
    rollback triggered by thresholds agreed before launch. Distractors include big-bang
    releases justified by a strong offline result, rollback "after the incident review", and
    treating sparse thumbs-down counts as the only quality signal while behavioural signals
    scream.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'During a 25% rollout of a new prompt for the support deflection agent, resolution rate is up 2.8 points (the primary metric), but escalations from one regional call centre have tripled and p99 latency breached the predeclared 8-second threshold twice. The team argues the primary metric justifies proceeding to 50%. What does correct guardrail discipline require?',
                'options' => [
                    'Proceed — the primary metric was declared in advance as the deciding metric',
                    'Proceed, but add the regional escalation rate to the dashboard for the 50% stage',
                    'Halt or roll back: guardrail breaches veto the primary-metric win by design; the predeclared thresholds exist precisely so this argument doesn\'t get relitigated mid-rollout',
                    'Split the difference by rolling out to 35%',
                ],
                'answer' => 2,
                'explain' => 'Guardrails are veto metrics: they cannot make a variant win, but breaching them disqualifies it regardless of the primary result. The team\'s argument is exactly the sunk-cost pressure predeclared thresholds are designed to overrule. A misstates the role of the primary metric (it decides among candidates that pass guardrails). B and D continue exposure to a configuration already in breach.',
            ],
            [
                'q' => 'A team ships a new model tier for the claims triage assistant to 100% of traffic overnight, reasoning that "the offline suite showed no regressions, so a canary would just delay value". Which risk does this reasoning ignore?',
                'options' => [
                    'None — a clean offline gate is sufficient evidence for full rollout',
                    'Offline suites cover only what the dataset encodes; a staged rollout exists to catch real-distribution failures (novel claim types, load-dependent latency, drifted inputs) while they affect 5% of claimants instead of all 40,000/day',
                    'Canaries are only needed for user-facing chat products, not classification pipelines',
                    'The offline suite should simply have been larger',
                ],
                'answer' => 1,
                'explain' => 'The offline gate and the canary answer different questions (ev-4-01): the gate proves suite performance, the staged rollout bounds the blast radius of everything the suite didn\'t encode. At 40k claims/day, a failure mode affecting 3% of claims hits 1,200 claimants on day one of a big-bang release. C is false — batch and classification systems drift and fail like any other. D helps but no finite dataset closes the gap the canary covers.',
            ],
            [
                'q' => 'Two weeks after a full rollout, thumbs-down rates for the policy knowledge assistant are flat, and the team concludes quality is stable. Which additional signal would most likely reveal a problem first, and why?',
                'options' => [
                    'Quarterly employee satisfaction survey results',
                    'The rate of users immediately rephrasing and resubmitting the same question, because explicit feedback is sparse and biased while rephrasing is an abundant behavioural symptom that the answer failed',
                    'Total query volume, because unhappy users ask more questions',
                    'The model\'s own confidence scores on each answer',
                ],
                'answer' => 1,
                'explain' => 'Explicit feedback (thumbs) is given by a small, extreme-skewed minority, so a real regression can hide under a flat thumbs-down rate. Rephrase/retry loops occur at full traffic volume and directly indicate the answer didn\'t satisfy the need. Surveys (A) lag by a quarter. Volume (C) is confounded in both directions. Self-reported confidence (D) is not a calibrated quality measure and misses retrieval-grounding failures entirely.',
            ],
        ],
    ],
];
