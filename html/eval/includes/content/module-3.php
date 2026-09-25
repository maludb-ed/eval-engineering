<?php
/**
 * module-3.php — study content for Module 3: Graders and Mixed Methodologies.
 *
 * Grounded in Anthropic's published guidance: "Create strong empirical
 * evaluations" and "Define your success criteria" (docs.claude.com), and the
 * engineering post "Demystifying evals for AI agents" (outcome grading,
 * judge design and calibration).
 *
 * Returns: [ topic-id => ['body' => html, 'questions' => [...] ] ]
 */
return [

    // =====================================================================
    'ev-3-01' => [
        'body' => <<<'HTML'
<p>
Once you know <em>what</em> to measure, you must decide <em>what does the measuring</em>.
Anthropic's guidance compresses the decision into one principle: <strong>the cheapest grader
that answers the question wins</strong>. "Cheapest" means total cost of ownership — build
time, run time, tokens, and the human hours spent auditing it — and "answers the question"
is the constraint that stops you from grading a nuanced summary with a regex.
</p>

<h3>The three grader families</h3>
<div class="table-responsive">
<table class="table table-sm align-middle">
    <thead>
        <tr class="text-secondary small text-uppercase">
            <th>Family</th><th>Strengths</th><th>Limits</th><th>Typical use</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Code-based</strong><br><span class="text-secondary small">exact match, regex, schema validation, execution/tests</span></td>
            <td>Fast, near-free, deterministic, runs on every commit</td>
            <td>Only works when correctness is programmatically verifiable</td>
            <td>Routing labels, JSON validity, SQL results, CI test suites</td>
        </tr>
        <tr>
            <td><strong>Model-based</strong><br><span class="text-secondary small">LLM-as-judge</span></td>
            <td>Scales judgment to free text; flexible rubrics; cheap relative to humans</td>
            <td>Costs tokens; has biases; must be calibrated against humans</td>
            <td>Summary quality, groundedness, tone, completeness</td>
        </tr>
        <tr>
            <td><strong>Human</strong></td>
            <td>Gold standard for nuance and taste; the reference all others calibrate to</td>
            <td>Expensive, slow, inconsistent between raters, does not scale</td>
            <td>Calibration sets, high-stakes samples, disagreement audits</td>
        </tr>
    </tbody>
</table>
</div>

<h3>Mixed methodologies: layer, don't choose</h3>
<p>
The exam task statement says "mixed methodologies" because real systems use all three at once,
each doing the job it is cheapest at. For the research report agent: code checks that the
report parses, cites at least one source per section, and stays under the length cap; an LLM
judge scores groundedness and coverage against a rubric; humans review a rotating sample and
every case where the judge and the code checks disagree. The code layer runs on every change,
the judge on every candidate release, the humans on a schedule — cost per verdict rises as
frequency falls.
</p>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">The trap: judge by default</div>
    <p>Teams reach for an LLM judge because it is easy to write, then spend judge tokens — and
    inherit judge noise — on questions a program answers exactly. "Is the JSON valid?" "Did the
    claim land in the right queue?" "Do the extracted dates match the reference?" are code
    questions. A stem that shows a judge grading schema validity is testing whether you will
    push the check down to the cheaper, deterministic layer.</p>
</div>

<p>
The reverse error also appears: stretching a code grader past what it can verify. A regex that
checks a support reply "contains an apology and the refund policy link" says nothing about
whether the reply is <em>correct</em> — and models learn to satisfy the regex (Module 3.5).
When correctness is a judgment call, promote the check up a layer rather than encoding a proxy.
</p>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Given a grading design question, sort each property to its cheapest sufficient layer:
    format and verifiable facts → code; free-text quality → calibrated judge; taste, stakes, or
    calibration → human sample. Distractors either grade everything with one expensive layer
    ("humans review all 40k claims") or one insufficient layer ("regex the summary"). The
    credited answer almost always layers all three with different frequencies.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The claims triage assistant emits a JSON object with a queue label, a priority, and a one-paragraph justification. The team asks how to grade a 500-item eval run. Which design best follows Anthropic\'s guidance?',
                'options' => [
                    'An LLM judge scores each output holistically on a 1–10 quality scale covering format, routing, and justification',
                    'Senior adjusters review all 500 outputs to guarantee gold-standard quality',
                    'Code validates the JSON schema; the justification is left ungraded since free text cannot be measured objectively',
                    'Code validates the JSON schema and compares queue/priority to ground-truth labels; a rubric-based judge scores only the justification; humans review a sample plus judge–label disagreements',
                ],
                'answer' => 3,
                'explain' => 'The layered design assigns each property to the cheapest grader that answers it: schema and label matching are exactly verifiable (code), justification quality is a judgment call (calibrated judge), and humans audit where they add the most signal. The holistic judge pays judge tokens and judge noise for questions code answers deterministically. All-human review is the gold standard applied indiscriminately — unaffordable and unnecessary for label matching. Leaving the justification ungraded abandons a measurable axis; free text is measurable with a rubric-driven judge.',
            ],
            [
                'q' => 'Which check is the <em>weakest</em> candidate for a code-based grader?',
                'options' => [
                    'Whether the code migration agent\'s pull request passes the service\'s existing test suite',
                    'Whether the contract clause extractor\'s output contains exactly the clause types present in the reference labels',
                    'Whether the support deflection agent\'s reply resolved the customer\'s underlying problem',
                    'Whether the policy knowledge assistant\'s answer includes a citation to at least one retrieved document',
                ],
                'answer' => 2,
                'explain' => '"Did the reply resolve the underlying problem?" is a judgment about meaning and outcome that no program can verify from the text alone — it needs a rubric-driven judge (or downstream signals like reopen rate). A is execution-based grading, the strongest form of code grading. B is set comparison against references. D is a mechanical presence check. Note D verifies only that a citation <em>exists</em>, not that the answer is grounded in it — that residual question goes to a judge.',
            ],
            [
                'q' => 'A team proposes grading the research report agent entirely with human reviewers "because quality is subjective". What is the strongest architectural objection?',
                'options' => [
                    'It cannot run at iteration speed or scale — every prompt tweak would wait days for verdicts — while much of the checking (parses, length, citation presence) is programmatic and the judgment residue can be delegated to a judge calibrated against those same humans',
                    'Human review is too subjective to produce usable scores',
                    'Humans should only ever review failures, not successes',
                    'LLM judges are now more accurate than human experts, so human review adds nothing',
                ],
                'answer' => 0,
                'explain' => 'The objection is economic and operational, not about human ability: all-human grading throttles iteration to human turnaround and cost, and wastes expert hours on checks a program does for free. The layered design keeps humans where they are irreplaceable — building the calibration set and auditing samples. B is backwards (humans are the reference standard, managed with rubrics); C is a spot-checking anti-pattern (Module 3.5 shows why high scores need audits too); D overstates judges, which are calibrated <em>to</em> humans.',
            ],
        ],
    ],

    // =====================================================================
    'ev-3-02' => [
        'body' => <<<'HTML'
<p>
Code-based graders are the workhorse of an eval harness: deterministic, effectively free per
run, and fast enough to gate every commit. The craft is in making the check measure real
success rather than textual coincidence.
</p>

<h3>Exact match is stricter than correctness</h3>
<p>
The classic failure: string-comparing an output to a reference and failing semantically
identical answers. "$1,200.00" vs "1200", "N/A" vs "not applicable", a SQL query with a
different join order. The first line of defence is <strong>normalisation</strong> — lowercase,
strip whitespace and punctuation, canonicalise numbers and dates — but normalisation only
patches surface variance. The stronger move is to compare <strong>meaning by execution</strong>:
run the generated SQL against a fixture database and compare result sets; run the migrated
service's test suite; call the drafted API request against a sandbox and inspect the response.
Execution-based grading is why the code migration agent is the most cleanly evaluable scenario
in this course — the test suite already encodes ground truth.
</p>

<h3>Outcome over process for agents</h3>
<p>
Anthropic's agent-evaluation guidance is explicit: <strong>grade the end state of the
environment, not the trajectory</strong>. If the task is "file a claim adjustment and notify
the adjuster", the grader should inspect the resulting database row and the outbox — not
assert that the agent called <code>lookup_claim</code> before <code>update_claim</code>.
Trajectory assertions break every time the model finds a different valid path, and they
punish improvements: a newer model that reaches the goal in three calls instead of six fails
a step-sequence grader while doing strictly better work. Process signals (tool-call counts,
tokens spent) still belong in the report as <em>efficiency metrics</em> — they just should not
define success.
</p>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">Cheap first gates</div>
    <p>Order code checks from cheapest to most expensive and fail fast: schema/format validity
    first (does it parse? are required fields present?), then field-level comparisons, then
    execution. A malformed output should cost one validation call, not a database run. The
    same gate doubles as a production guardrail — the format check you wrote for the eval is
    the one you run on live traffic.</p>
</div>

<h3>Partial credit, deliberately</h3>
<p>
Binary pass/fail hides progress on multi-part outputs. If the contract clause extractor finds
six of seven clauses, a 0/1 grader reports the same failure as finding none — so per-item
scoring (precision/recall over the clause set, field-level accuracy over the JSON) gives the
iteration signal a binary gate destroys. The discipline is to define partial credit in the
grader spec up front, not to eyeball "close enough" after the run; and to keep the
<em>shipping gate</em> binary (the release criterion from Module 1) even while the
<em>diagnostic report</em> is granular.
</p>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Three reliable discriminations: string comparison vs execution comparison (execution is
    credited whenever the output is runnable); trajectory assertions vs end-state checks for
    agents (end-state is credited; "assert the agent called tools in this order" is the
    distractor); and binary vs partial credit (partial credit for diagnosis, hard floors for
    shipping). If a stem says a passing score dropped after a model upgrade while users report
    better results, look for a brittle exact-match or step-order grader.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'After upgrading the model behind the code migration agent, the eval pass rate falls from 78% to 41%, yet engineers reviewing the pull requests say the ports look better than before. The grader asserts the agent calls <code>read_service</code>, <code>write_module</code>, and <code>run_tests</code> in that order before opening a PR. What is the most likely cause?',
                'options' => [
                    'The new model is genuinely worse at code migration and should be rolled back',
                    'The eval set has become saturated and needs harder tasks',
                    'The test fixtures have drifted from the production schema',
                    'The grader scores the trajectory, not the outcome — the new model reaches good end states via different tool sequences, which the step-order assertions count as failures',
                ],
                'answer' => 3,
                'explain' => 'A grader that hard-codes a tool sequence fails any alternative valid path, so a more capable model that skips redundant steps scores <em>worse</em> — exactly the divergence in the stem (score down, human judgment up). The fix is outcome grading: does the migrated service pass its test suite? A contradicts the engineers\' review evidence; B (saturation) would show as scores stuck near the ceiling, not a collapse; C (fixture drift) would break old and new models alike.',
            ],
            [
                'q' => 'The policy knowledge assistant answers questions with a dollar figure drawn from HR tables. The current grader does <code>output == reference</code> and passes 52% of answers humans mark correct. Which fix best follows the guidance in this topic?',
                'options' => [
                    'Normalise both sides (strip currency symbols, commas, whitespace; canonicalise number formats) and compare the parsed values, keeping the check deterministic',
                    'Replace the code grader with an LLM judge that rates each answer\'s overall quality on a 5-point scale',
                    'Loosen the comparison to "reference value appears anywhere in the output"',
                    'Lower the pass threshold to 50% to match the grader\'s observed behaviour',
                ],
                'answer' => 0,
                'explain' => 'The failure is surface variance on a programmatically verifiable value — "$1,200.00" vs "1200" — so the cheapest sufficient fix is normalisation and value-level comparison, preserving determinism. B promotes a code question to the judge layer, buying noise and token cost for nothing. C invites false passes (the right number cited in a wrong sentence, or amid three candidate figures). D redefines success to match a broken instrument.',
            ],
            [
                'q' => 'For the contract clause extractor, which grading design gives the team the best iteration signal <em>and</em> a sound shipping decision?',
                'options' => [
                    'Binary pass/fail per contract (all clauses correct or the contract fails), used for both iteration and shipping',
                    'Per-clause precision and recall reported for diagnosis, with the release gated on the Module-1 criterion (e.g. recall ≥ 98% on named clause types)',
                    'An LLM judge assigns each contract a holistic 1–10 extraction quality score',
                    'Per-clause partial credit for both diagnosis and shipping, so near-misses count proportionally toward the release',
                ],
                'answer' => 1,
                'explain' => 'Granular metrics for iteration, hard floors for shipping. Per-clause scores show <em>which</em> clause types fail and whether a change helped, while the gate stays a non-negotiable recall floor aligned to business harm. A destroys the iteration signal (6-of-7 scores identically to 0-of-7). C grades a code-verifiable set comparison with a noisy judge. D lets partial credit erode the shipping bar — a system missing 2% of indemnification clauses "proportionally" is still leaking clauses to counsel.',
            ],
        ],
    ],

    // =====================================================================
    'ev-3-03' => [
        'body' => <<<'HTML'
<p>
When correctness is a judgment call — is this summary grounded, complete, appropriately
hedged? — the scalable grader is another model. But "ask Claude to rate it" is not a design.
Anthropic's guidance treats the judge as a component you engineer, version, and evaluate like
any other prompt.
</p>

<h3>Design rules that survive contact with data</h3>
<ul>
    <li><strong>Rubric-driven, not vibes-driven.</strong> The judge prompt states the exact
        criteria and what passing looks like, ideally with worked examples of a pass and a
        fail. "Rate the quality" produces drift; "Does every factual claim in the answer appear
        in the provided source excerpts? Answer PASS or FAIL with the unsupported claims
        listed" produces evidence.</li>
    <li><strong>Binary or few-point scales beat 1–10.</strong> Fine-grained scales feel
        precise but the distinctions between a 6 and a 7 are noise — verdicts become
        inconsistent across runs and impossible to calibrate. A binary verdict per criterion,
        or a 3-point scale with defined anchors, is more reliable and directly aggregable into
        a pass rate.</li>
    <li><strong>Reason first, verdict second.</strong> Have the judge think through the
        evidence (chain-of-thought) before emitting a structured verdict. The reasoning is
        also your audit trail when a human disputes a grade.</li>
    <li><strong>One criterion per judge.</strong> A single mega-judge scoring groundedness,
        completeness, and tone at once blurs them into one number you cannot act on. Separate
        judge calls per criterion cost more tokens and repay it in diagnosis: "groundedness
        fell, tone held" tells you where to look.</li>
    <li><strong>Give the judge the reference when one exists.</strong> Judging "is this answer
        consistent with this reference?" is a far easier, more reliable task than judging
        correctness from the judge model\'s own knowledge.</li>
    <li><strong>Use a capable model, and don\'t let the judge be the bottleneck.</strong>
        Judging is a hard comprehension task; grading with a weak model to save tokens buys
        systematic error. Spend on the judge, save on frequency.</li>
</ul>

<h3>Known biases, known mitigations</h3>
<p>
LLM judges exhibit <strong>position bias</strong> (in pairwise comparisons, favouring the
first- or last-presented answer — mitigate by scoring both orders and averaging, or randomising
order), <strong>length/verbosity bias</strong> (longer answers read as more thorough — mitigate
with rubric criteria that penalise padding and by spot-checking long high-scorers), and
<strong>self-preference</strong> (a judge favouring text in its own style — mitigate with
reference-anchored rubrics and human calibration). Pairwise comparison is sharper for "which
prompt is better?" experiments; absolute rubric scoring is what you can track over time.
</p>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">The judge is a prompt. Treat it like one.</div>
    <p>Version the judge prompt, pin the judge model, and re-run the calibration set when
    either changes. An unversioned judge silently redefines your metric: a score moving from
    71% to 76% means nothing if the judge moved too. Exam stems that show a metric shifting
    with no system change often hide a judge or judge-model change.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Credited judge designs are: rubric with explicit criteria, coarse scale, reasoning
    before a structured verdict, reference provided, one criterion per call, order-swapped
    pairwise when comparing. Distractors offer 1–10 holistic scores, judges grading from their
    own knowledge when a reference exists, a mega-judge for everything, or the smallest model
    "to keep eval costs down".</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The support deflection agent team asks an LLM judge to "rate each reply 1–10 for overall quality" and finds the same reply scores 5, 7, and 8 across three runs, and score trends do not match CSAT. Which redesign most directly addresses both problems?',
                'options' => [
                    'Average five judge runs per reply to smooth out the noise',
                    'Fine-tune the judge model on historical CSAT scores',
                    'Replace the holistic 1–10 scale with separate binary rubric checks (resolved the question? grounded in the help-centre article? correct escalation decision?), each with reasoning before a structured verdict',
                    'Switch to a larger judge model but keep the 1–10 holistic prompt',
                ],
                'answer' => 2,
                'explain' => 'The instability comes from asking for fine-grained holistic judgment with no criteria — the difference between a 6 and a 7 is noise, and "overall quality" need not track what CSAT measures. Explicit binary criteria fix reliability (coarse, anchored decisions) and validity (criteria chosen to match what matters). A averages the noise without fixing the meaningless scale; B is heavy machinery aimed at the wrong layer; D upgrades the model while keeping the design flaw that causes the variance.',
            ],
            [
                'q' => 'In pairwise A/B judging of two prompt variants for the research report agent, variant B wins 71% when presented second but only 44% when presented first. What is happening and what is the standard mitigation?',
                'options' => [
                    'Variant B is genuinely better; later presentation lets the judge read it with more context',
                    'Position bias — the judge systematically favours one slot; run every comparison in both orders and aggregate (or randomise order), counting inconsistent verdicts as ties',
                    'Length bias — variant B\'s reports are longer; truncate both to equal length before judging',
                    'Self-preference bias — use a judge from a different model family and keep single-order presentation',
                ],
                'answer' => 1,
                'explain' => 'A 27-point swing purely from presentation order is the signature of position bias. The mitigation is order-balancing: judge each pair in both orders and aggregate, treating order-dependent verdicts as ties. A rationalises the artefact. C invents a length problem the stem gives no evidence for, and truncation destroys the outputs being judged. D names a real bias but the diagnostic (same pair, different order, different verdict) specifically fingerprints position, which a different-family judge can also exhibit.',
            ],
            [
                'q' => 'For grading whether policy knowledge assistant answers are grounded, the assistant\'s answers are judged alongside the retrieved policy excerpts. Two judge-prompt designs are proposed. Design 1: "Using your knowledge of typical HR policy, is this answer correct?" Design 2: "Here are the source excerpts. Does every factual claim in the answer appear in or follow from them? Verdict PASS/FAIL; list unsupported claims." Why is Design 2 the credited choice?',
                'options' => [
                    'It is shorter and therefore cheaper to run at scale',
                    'It avoids chain-of-thought, which introduces bias into judge verdicts',
                    'Design 1 is better in fact, because the judge\'s world knowledge catches errors the excerpts cannot',
                    'It anchors the judgment to the provided reference material, turning an open-ended correctness question the judge may get wrong into a checkable consistency question, with a structured, auditable verdict',
                ],
                'answer' => 3,
                'explain' => 'Groundedness is by definition consistency with the retrieved sources — the company\'s actual policy may differ from "typical HR policy", so Design 1 grades against the wrong reference and inherits the judge\'s own knowledge gaps and hallucinations. Design 2 makes the task easier and the output auditable (listed unsupported claims are the evidence trail). A is false (Design 2 is longer) and irrelevant. B is backwards — reasoning before the verdict is recommended. C describes a different check (answer accuracy vs corpus currency), not groundedness.',
            ],
        ],
    ],

    // =====================================================================
    'ev-3-04' => [
        'body' => <<<'HTML'
<p>
An LLM judge is a measurement instrument, and an uncalibrated instrument is a random-number
generator with good manners. Calibration answers one question: <strong>when the judge says
PASS, would our humans say PASS?</strong> Until you know that, judge scores are not evidence.
</p>

<h3>The calibration workflow</h3>
<ol>
    <li><strong>Build a human-labelled calibration set.</strong> Sample real outputs across
        the difficulty range — including borderline cases, not just clean passes and fails —
        and have qualified humans label them with the same rubric the judge will use. For the
        claims triage justifications, that means adjusters, not whoever is free.</li>
    <li><strong>Measure human–human agreement first.</strong> Give an overlapping subset to
        two or more raters independently. Their agreement rate is your <em>ceiling</em>: no
        judge can meaningfully agree with "the humans" more than the humans agree with each
        other. If human agreement is low, the rubric is ambiguous — fix the rubric before
        blaming any judge.</li>
    <li><strong>Run the judge on the set and measure judge–human agreement.</strong> Percent
        agreement is the intuitive number; chance-corrected agreement (the idea behind
        Cohen\'s kappa) matters when labels are imbalanced — a judge that says PASS every time
        scores 90% raw agreement on a set that is 90% passes while carrying zero information.</li>
    <li><strong>Inspect every disagreement.</strong> Disagreements split into three buckets:
        the judge is wrong (fix the judge prompt, add worked examples), the human label is
        wrong (fix the label — this happens more than teams expect), or the rubric is
        ambiguous (fix the rubric, then relabel). Iterate until agreement approaches the
        human–human ceiling.</li>
</ol>

<h3>Calibration decays</h3>
<p>
Recalibrate when anything that defines the instrument moves: the judge prompt, the judge
model (including provider model upgrades), or the task distribution — a judge calibrated on
password-reset tickets is uncalibrated for the billing-dispute tickets the product team just
onboarded. A standing sample of production outputs flowing to human review (Module 7) gives
you drift detection between deliberate recalibrations.
</p>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">The acceptance rule</div>
    <p>Trust the judge for routine measurement when judge–human agreement is close to
    human–human agreement on the same set. Below that, the judge adds noise to every number
    downstream of it — including your regression gates and A/B verdicts (Module 4), which
    silently inherit the judge\'s error.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Four things get tested: (1) the ceiling — compare the judge to human–human agreement,
    and fix the rubric when humans themselves disagree; (2) chance-corrected agreement on
    imbalanced sets — the always-PASS judge with high raw agreement is a classic distractor;
    (3) disagreement review as the improvement loop; (4) recalibration triggers — judge
    prompt, judge model, or task-mix changes. "We calibrated once at launch" in a stem is the
    tell that the metric has quietly become fiction.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'A judge for the contract clause extractor\'s "clause summary quality" reaches 68% agreement with human labels. Two senior lawyers labelling the same 100-item subset independently agree with each other 71% of the time. What should the team do first?',
                'options' => [
                    'Replace the judge with a more capable model to push agreement above 80%',
                    'Accept the judge — 68% against a 71% human ceiling is near the achievable maximum',
                    'Treat the low human–human agreement as the primary problem: the rubric is ambiguous, so clarify it, relabel, and only then re-evaluate the judge against the new ceiling',
                    'Average the two lawyers\' labels to create a more reliable ground truth',
                ],
                'answer' => 2,
                'explain' => 'When the experts agree only 71% of the time, the task as specified is ambiguous — no judge can be calibrated against ground truth that isn\'t stable. Fixing the rubric raises the ceiling and typically lifts both human–human and judge–human agreement. A chases a number the ceiling forbids. B settles for measuring an ill-defined property. D papers over disagreement instead of resolving why qualified raters diverge, and with two raters "averaging" binary labels isn\'t even well-defined.',
            ],
            [
                'q' => 'A groundedness judge for the policy knowledge assistant shows 91% raw agreement with human labels on a calibration set where 92% of items are labelled PASS. Why is this insufficient evidence to trust the judge, and what check exposes the problem?',
                'options' => [
                    'With 92% passes, a judge that answers PASS unconditionally scores ~92% agreement; check chance-corrected agreement (the Cohen\'s-kappa idea) and, concretely, the judge\'s recall on the FAIL items',
                    'It is sufficient — 91% agreement is above any reasonable threshold',
                    'The set is too small; collect ten times more items before deciding',
                    'Raw agreement is invalid because the humans saw the retrieved excerpts and the judge did not',
                ],
                'answer' => 0,
                'explain' => 'On an imbalanced set, raw agreement is dominated by the majority class — the degenerate always-PASS judge matches the 91% figure while detecting zero groundedness failures, which are the entire point of the metric. Chance-corrected agreement, or simply the judge\'s performance on the minority FAIL class, exposes it. B falls for exactly this trap. C adds data without fixing the statistic. D invents a protocol difference the stem doesn\'t state.',
            ],
            [
                'q' => 'Six months ago the support deflection team calibrated their resolution judge (87% agreement, near the human ceiling). Since then the provider upgraded the judge\'s underlying model, and the product expanded from password resets to billing disputes. Weekly judge scores have been stable at ~80%. What is the correct interpretation?',
                'options' => [
                    'Stable scores confirm the judge is still calibrated; no action needed',
                    'Both recalibration triggers have fired — judge model change and task-distribution change — so the current scores are of unknown validity until the judge is re-run against a refreshed, billing-inclusive human-labelled set',
                    'Only the model upgrade matters; task mix does not affect judge calibration',
                    'The judge should be recalibrated only if scores move by more than five points',
                ],
                'answer' => 1,
                'explain' => 'Calibration is a property of a specific judge prompt + judge model measured on a specific task distribution; changing either invalidates the certificate. A stable topline is exactly what silent miscalibration looks like — the judge could now be lenient on billing failures and strict on resets, netting out to a familiar number. A and D treat score stability as evidence of validity, which it is not. C ignores that a judge calibrated on one task type has simply never been validated on the new one.',
            ],
        ],
    ],

    // =====================================================================
    'ev-3-05' => [
        'body' => <<<'HTML'
<p>
Every grader is an approximation of the thing you actually care about, and optimisation
pressure finds the gap. This topic is the failure catalogue — and the one tell-tale signal
that should trigger an audit: <strong>the eval score improves while human judgment or
production outcomes do not</strong>. When the number and the reality diverge, believe the
reality and indict the grader.
</p>

<h3>The failure catalogue</h3>
<ul>
    <li><strong>Grader gaming / reward hacking.</strong> The system under test satisfies the
        letter of the grader, not the goal. A regex grader that checks for policy keywords
        teaches keyword stuffing; a verbosity-biased judge teaches padding; an "apology
        present" check produces replies that apologise and resolve nothing. Iterating prompts
        against a fixed grader is optimisation pressure, and the grader\'s loopholes are what
        gets optimised.</li>
    <li><strong>Judge drift.</strong> The judge prompt or judge model changes — sometimes via
        a provider upgrade nobody announced to the eval owner — and the metric silently means
        something new. Pin and version the judge (Module 3.3); recalibrate on change
        (Module 3.4).</li>
    <li><strong>Saturated graders.</strong> The suite sits at 96–100% and every candidate
        change "passes". A saturated eval measures nothing — it can no longer rank options or
        catch regressions except catastrophic ones. Retire solved cases to a regression pack
        and add harder ones drawn from current production failures (Module 2.5).</li>
    <li><strong>Grading the wrong thing.</strong> Process assertions instead of outcomes
        (Module 3.2), proxies instead of goals (Module 1.1): the grader is precise, stable,
        and pointed at the wrong target.</li>
    <li><strong>Noisy ground truth.</strong> Mislabelled references put a hard ceiling on
        measurable performance and — worse — make real improvements look like regressions.
        When a strong model "fails" a case, check the label before the model; disagreement
        review (Module 3.4) routinely finds label errors.</li>
    <li><strong>Silent grader bugs.</strong> A parsing bug that scores empty outputs as
        passes, a comparison that always returns true, a fixture DB that stopped loading:
        grader code is code, and a bug that <em>inflates</em> scores is the dangerous kind
        because nothing looks wrong. Graders need their own tests — known-good inputs that
        must pass and known-bad inputs that must fail.</li>
</ul>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">Audit the passes, not just the failures</div>
    <p>Teams read failing transcripts to find model bugs and never read passing ones — but
    gaming, label noise, and score-inflating grader bugs all hide in the <em>passes</em>.
    Anthropic\'s guidance is to routinely read transcripts on a sample of high-scoring runs.
    A suspiciously easy 100% on a hard task is a grader incident until proven otherwise.</p>
</div>

<h3>The standing remedy</h3>
<p>
Keep a human in the loop on a schedule, not just at launch: a rotating transcript sample
(passes and failures), judge–human spot checks against the calibration set, and grader unit
tests in CI. The cost is small; the alternative is discovering at the quarterly review that
three months of "improvement" was the grader being farmed.
</p>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>The stem pattern is divergence: "offline score rose, CSAT flat", "pass rate 100% for
    six weeks", "scores improved after the team tuned prompts against the judge". The credited
    move is to audit the grader and read transcripts — especially passing ones — before
    celebrating or shipping. Distractors credit the score (ship it), blame the users, or add
    more eval cases without questioning the instrument that scores them.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'Over six weeks the support deflection team tunes prompts against their LLM judge and lifts the offline "resolution quality" score from 71% to 88%. CSAT and escalation rate are unchanged. Reading recent transcripts shows replies have grown from ~90 to ~260 tokens, with long empathetic preambles. What happened?',
                'options' => [
                    'The improvements are real but CSAT is a lagging indicator; wait another quarter',
                    'The judge has a verbosity bias and the tuning loop optimised into it — the team was training against the grader\'s loophole, not the goal; fix the judge (length-aware rubric, recalibrate) and re-baseline',
                    'The eval set is stale and needs new cases from production',
                    'Customers dislike empathetic language; add a tone criterion to the judge',
                ],
                'answer' => 1,
                'explain' => 'Score up, reality flat, outputs ballooning — the signature of grader gaming against a length-biased judge. Iterating prompts against a fixed judge is optimisation pressure, and padding is the classic exploit. A rationalises the divergence; escalation rate is not lagging and it is flat too. C doesn\'t explain why token counts tripled. D misreads the evidence — the problem isn\'t empathy, it\'s that the metric rewarded padding, so the instrument needs fixing before any new criterion means anything.',
            ],
            [
                'q' => 'The code migration agent\'s eval has reported 100% pass for five consecutive weeks across three different prompt variants and two model tiers, all scoring identically. What should the team suspect <em>first</em>, and what is the immediate check?',
                'options' => [
                    'The agent has mastered the task; expand to a harder service portfolio',
                    'The model tiers are secretly the same model; file a ticket with the provider',
                    'Either the suite is saturated or the grader is silently broken — identical perfect scores across different systems is the tell; run known-bad outputs through the grader and confirm they fail, and read a sample of passing transcripts',
                    'Five weeks is too short a window to conclude anything; keep collecting data',
                ],
                'answer' => 2,
                'explain' => 'A grader that passes <em>everything</em> — including systems that should differ — is indistinguishable from a bug (fixture not loading, comparison returning true) or full saturation, and both mean the eval currently measures nothing. The cheapest decisive check is grader unit-testing: feed it outputs known to be wrong and see if it notices, and audit passing transcripts. A acts on a number that may be fictional. B is an exotic explanation ahead of the mundane one. D collects more of a signal that carries no information.',
            ],
            [
                'q' => 'After a model upgrade, the contract clause extractor\'s recall on the eval set <em>drops</em> three points, but lawyers reviewing the "new failures" report that in most of them the model is right and the reference label is wrong. What does this reveal, and what is the correct response?',
                'options' => [
                    'Noisy ground truth: the stronger model is exposing label errors that the weaker model happened to agree with; fix the labels via disagreement review, then re-score both models before any rollback decision',
                    'The upgrade genuinely regressed recall; roll back and re-run',
                    'The lawyers are second-guessing the eval set; ground truth must stay fixed or metrics lose comparability',
                    'Add the disputed cases to a separate "hard" suite and exclude them from the headline metric',
                ],
                'answer' => 0,
                'explain' => 'When qualified humans side with the model against the labels, the ground truth is the defect — mislabelled references cap measurable performance and make improvements register as regressions, exactly as here. The remedy is label repair through disagreement review, then re-scoring. B trusts a broken instrument over expert review. C inverts the hierarchy: labels serve the truth, comparability doesn\'t justify preserving errors. D quarantines the evidence instead of correcting it, leaving every future model scored against known-wrong answers.',
            ],
        ],
    ],
];
