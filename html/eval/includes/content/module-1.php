<?php
/**
 * module-1.php — study content for Module 1: Success Criteria and Metrics.
 *
 * Grounded in Anthropic's published guidance: "Define your success criteria"
 * and "Create strong empirical evaluations" (docs.claude.com), and the
 * engineering post "Demystifying evals for AI agents" (pass@k vs pass^k).
 *
 * Returns: [ topic-id => ['body' => html, 'questions' => [...] ] ]
 */
return [

    // =====================================================================
    'ev-1-01' => [
        'body' => <<<'HTML'
<p>
Every evaluation programme starts before a single test case exists: with a statement of what
"good" means for <em>this</em> system. Anthropic's guidance is blunt about the most common
failure — teams write goals like "the assistant should give helpful, accurate answers", which
cannot be tested, and then argue about vibes when quality drops. A success criterion earns its
place only if a second engineer could apply it to an output and reach the same verdict.
</p>

<h3>The four properties of a criterion that holds up</h3>
<ul>
    <li><strong>Specific</strong> — it names the behaviour, the input population, and the bar.
        "Correctly routes claims" is vague; "routes &ge; 95% of claims in the eval set to the
        adjuster queue a senior adjuster would choose" is specific.</li>
    <li><strong>Measurable</strong> — there is a defined procedure (a grader) that turns an
        output into a score. If you cannot say <em>how</em> it will be measured, it is a wish,
        not a criterion. Quantitative where possible; where quality is inherently judgment-based,
        make it measurable with a rubric-driven judge or human panel rather than dropping it.</li>
    <li><strong>Achievable</strong> — anchored to a baseline: current human performance, the
        previous system, or a quick pilot with the model. A 99.99% accuracy target for a task
        humans do at 92% is a political number, not an engineering one.</li>
    <li><strong>Relevant</strong> — tied to what the business actually loses when the system is
        wrong. For the claims triage assistant, a mis-route delays a payout; the criterion that
        matters is routing quality on <em>high-value</em> claims, not average BLEU on paraphrases.</li>
</ul>

<h3>Multidimensional by default</h3>
<p>
Anthropic's docs stress that success is almost never one number. A realistic definition for the
support deflection agent might read: task resolution &ge; 70% without escalation, <em>and</em>
p90 time-to-first-token under 1.5&nbsp;s, <em>and</em> cost per conversation under $0.12,
<em>and</em> zero policy-violating responses in the safety suite, <em>and</em> no regression on
the prompt-injection suite. Miss any one axis and the release is not shippable — that is what
the exam means when it calls single-axis targets "the trap": optimising accuracy alone quietly
sells out latency, cost, or safety, and the question stem will hand you the symptom.
</p>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">The trap: proxy metrics that drift from the goal</div>
    <p>A criterion can be specific and measurable and still wrong. "Average response length
    under 150 tokens" is a proxy for concision; teams that gate on it get truncated answers.
    When a stem shows a team hitting its metric while users complain, the diagnosis is usually
    a proxy metric standing in for the real outcome — fix the criterion, not the model.</p>
</div>

<h3>From requirement to criterion: the working sequence</h3>
<ol>
    <li>Ask what failure costs, and for whom (wrong payout route vs. slightly stiff wording are
        different universes).</li>
    <li>Pick the axes that failure implicates — accuracy/quality, latency, cost, safety,
        security — and set a target <em>and a floor</em> per axis. Targets are what you tune
        toward; floors are what you refuse to ship below.</li>
    <li>Name the measurement procedure for each: which dataset, which grader, which statistic
        (mean, p90, pass rate). A p99 latency criterion measured on a 20-item eval set is
        theatre — the statistic must be supportable by the sample.</li>
    <li>Write down the trade-off rule in advance: e.g. "we accept +20% cost for +5 points of
        resolution rate". Deciding this after results arrive invites motivated reasoning.</li>
</ol>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Stems love to offer four criteria and ask which is best. Eliminate options that (a) have
    no measurement procedure, (b) measure a proxy rather than the outcome, or (c) collapse five
    axes into one blended score. The credited answer is nearly always the one that is specific,
    tied to business harm, and paired with an explicit floor on a second axis.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'A product owner for the contract clause extractor proposes the success criterion: "The extractor should reliably find important clauses." What is the <em>most important</em> revision an architect should make?',
                'options' => [
                    'Restate it as a measurable target on a defined population, e.g. "&ge; 98% recall on the seven named clause types across the 300-contract eval set"',
                    'Add a latency target so the criterion covers more than one axis',
                    'Replace it with a cost-per-contract ceiling, since batch extraction is cost-dominated',
                    'Defer the criterion until six months of production data establishes a baseline',
                ],
                'answer' => 0,
                'explain' => '"Reliably" and "important" are unmeasurable and unspecific — the first fix is always to make the criterion specific and measurable against a defined population. Adding axes (B, C) matters later but does not repair an untestable criterion, and waiting for production data (D) leaves the team shipping with no bar at all. Note the metric choice too: for a precision-critical legal task where a <em>missed</em> clause reaches counsel, recall on named clause types is the harm-aligned statistic.',
            ],
            [
                'q' => 'The support deflection agent team gates releases on a single blended score: 0.6·resolution + 0.2·CSAT + 0.2·(1/latency). After a prompt change, the blended score improves but customers begin receiving fabricated refund policies. What is the root flaw?',
                'options' => [
                    'The weights were chosen incorrectly; safety should get at least 0.3',
                    'Latency should never appear in a quality score',
                    'CSAT is a lagging indicator and should be replaced with an offline judge score',
                    'A blended single score lets one axis buy back a failure on another; safety needs an independent floor that cannot be traded away',
                ],
                'answer' => 3,
                'explain' => 'The failure mode of composite scores is exactly this: an improvement on weighted axes masks a regression on an unweighted (or under-weighted) one. Re-weighting (A) just moves the problem — any finite weight still allows trades against fabrication. The fix is per-axis floors: some criteria (safety, groundedness) are gates, not terms in a sum.',
            ],
            [
                'q' => 'Which success criterion for the claims triage assistant best satisfies all four properties (specific, measurable, achievable, relevant)?',
                'options' => [
                    '"Route 100% of claims correctly, as verified by senior adjusters"',
                    '"Achieve a state-of-the-art score on a public insurance-claims benchmark"',
                    '"Match or exceed the current human routing team\'s 94% agreement rate with senior-adjuster ground truth on the 500-claim eval set, with p90 routing latency under 30 seconds"',
                    '"Reduce claim-processing complaints, as tracked by the support team"',
                ],
                'answer' => 2,
                'explain' => 'Option C is anchored to a baseline (the human team\'s 94%), names the population and grader (500-claim set, senior-adjuster ground truth), and adds a floor on a second axis. A fails "achievable" (100% on a judgment task), B fails "relevant" (a public benchmark is not this workload), and D fails "measurable/specific" (complaints are confounded and lag by weeks).',
            ],
        ],
    ],

    // =====================================================================
    'ev-1-02' => [
        'body' => <<<'HTML'
<p>
"Accuracy" is not one metric; it is a family, and choosing the wrong member quietly measures the
wrong thing. Anthropic's evaluation guidance frames the choice by output type: the more
constrained the output, the cheaper and more exact the metric can be.
</p>

<h3>Match the metric to the output shape</h3>
<div class="table-responsive">
<table class="table table-sm align-middle">
    <thead>
        <tr class="text-secondary small text-uppercase">
            <th>Output shape</th><th>Primary metrics</th><th>Watch out for</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Single label (classification, routing)</td>
            <td>Exact match / accuracy; per-class precision, recall, F1</td>
            <td>Class imbalance — 97% accuracy is meaningless if 97% of claims are the majority class</td>
        </tr>
        <tr>
            <td>Set of items (clauses found, entities extracted)</td>
            <td>Precision, recall, F1 over the set</td>
            <td>Decide up front whether misses or false alarms are costlier, and weight accordingly</td>
        </tr>
        <tr>
            <td>Structured object (JSON, SQL, API call)</td>
            <td>Schema validity, field-level exact match, execution result equivalence</td>
            <td>String-comparing two SQL queries penalises harmless formatting; compare <em>results</em></td>
        </tr>
        <tr>
            <td>Free text (summary, answer, report)</td>
            <td>Rubric-based LLM judge; human review; similarity scores as weak signals</td>
            <td>ROUGE/BLEU and embedding cosine similarity reward overlap, not correctness</td>
        </tr>
    </tbody>
</table>
</div>

<h3>Precision vs. recall is a business decision</h3>
<p>
The exam repeatedly probes whether you can pick the error type that matters. For the contract
clause extractor, a missed indemnification clause reaches counsel unnoticed — recall is the
star metric and you accept more false positives for a lawyer to dismiss. For the support agent
issuing account actions, a wrong action is worse than a handoff — precision (and abstention)
lead. Any option that maximises F1 "because it balances both" without asking which error is
costlier is a distractor: F1's equal weighting is itself a choice, and often the wrong one.
</p>

<h3>Why n-gram and embedding scores are weak evidence</h3>
<p>
ROUGE, BLEU, and cosine similarity measure surface or semantic <em>overlap</em> with a
reference. A hallucinated summary that paraphrases the source's vocabulary can outscore a
correct summary phrased differently. Anthropic's guidance treats these as cheap smoke tests —
useful for detecting catastrophic drift at near-zero cost — not as accuracy measures. When a
stem shows a team gating free-text quality on ROUGE, the credited fix is a rubric-driven LLM
judge calibrated against human labels (Module 3), not a better n-gram metric.
</p>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">Rule of thumb</div>
    <p>Ask two questions in order: (1) Can a program verify the answer exactly or by execution?
    If yes, use code-based grading and an exactness metric. (2) If not, which error direction
    hurts more? That picks precision- or recall-weighted reporting, and the residual free-text
    quality goes to a judge. This mirrors Anthropic's "cheapest grader that answers the
    question" principle.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Given a scenario, identify the harm direction first. "Missed clause reaches counsel" →
    recall. "Wrong route delays a payout" → per-class precision on the sensitive routes.
    "High-volume, imbalanced classes" → per-class F1 or macro-average, never raw accuracy.
    Distractors bank on you reaching for the most familiar metric instead of the harm-aligned one.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The claims triage assistant routes claims into 12 queues; 88% of real traffic lands in the two highest-volume queues. The team reports 93% overall accuracy and calls it a win. What should the architect examine first?',
                'options' => [
                    'Per-class precision and recall, especially on the low-volume, high-severity queues',
                    'The p90 latency of the routing call',
                    'Inter-rater agreement among the adjusters who labelled the eval set',
                    'Whether the eval set is large enough for statistical significance',
                ],
                'answer' => 0,
                'explain' => 'With 88% of traffic in two classes, a model that only ever handles those well can score ~93% overall while badly failing rare queues — which in insurance are usually the expensive ones (fraud, litigation). Per-class metrics expose this immediately. B is a different axis; C and D are legitimate hygiene checks but not the first move when the reported statistic is structurally capable of hiding the failure.',
            ],
            [
                'q' => 'A team evaluates generated SQL by string-comparing it to a reference query and sees a 41% pass rate despite users reporting the answers look right. What is the best correction?',
                'options' => [
                    'Normalise whitespace and keyword casing before comparing strings',
                    'Execute both queries against a fixture database and compare result sets',
                    'Switch to an LLM judge that rates query similarity on a 1–5 scale',
                    'Raise the temperature to zero so the model reproduces the reference style',
                ],
                'answer' => 1,
                'explain' => 'Many semantically identical queries differ textually (join order, aliases, equivalent predicates), so exact-match grading undercounts real success. Execution-result comparison is the code-based grader that measures what matters. A only fixes trivial variants; C spends judge tokens on a question code answers exactly; D confuses decoding settings with grading design.',
            ],
            [
                'q' => 'For the contract clause extractor, legal review agrees that a missed clause reaching counsel is roughly 20× costlier than a false positive a lawyer dismisses in seconds. Which reporting choice best reflects this?',
                'options' => [
                    'F1, because it balances precision and recall symmetrically',
                    'Precision as the primary gated metric, since false positives erode lawyer trust',
                    'ROUGE-L between extracted clause text and the reference clause text',
                    'Recall as the primary gated metric, with precision monitored against a lower floor',
                ],
                'answer' => 3,
                'explain' => 'When misses are 20× costlier, the gate belongs on recall; precision still gets a floor so the tool doesn\'t bury lawyers in noise, but it is not the gated axis. F1 (A) hard-codes a 1:1 cost ratio that the scenario explicitly contradicts. ROUGE-L (C) measures textual overlap, not whether the right clauses were found.',
            ],
        ],
    ],

    // =====================================================================
    'ev-1-03' => [
        'body' => <<<'HTML'
<p>
LLM systems are non-deterministic: the same prompt can succeed on one run and fail on the next,
and for agents the variance compounds across every step of a long trajectory. A single-run eval
therefore measures luck as much as capability. Anthropic's agent-evaluation guidance introduces
two statistics that separate the questions a single pass rate conflates.
</p>

<h3>pass@k — "can it ever do this?"</h3>
<p>
<strong>pass@k</strong> asks: across <em>k</em> independent attempts, did <em>at least one</em>
succeed? It measures the capability ceiling. It is the right lens when one success is all you
need — a research agent that can retry, a code agent whose output is checked by CI before
merging, or during development when you want to know whether the task is solvable at all before
investing in reliability work. pass@k can only go up as k grows.
</p>

<h3>pass^k — "can it do this every time?"</h3>
<p>
<strong>pass^k</strong> asks: across <em>k</em> attempts, did <em>all k</em> succeed? It
measures consistency, and it is brutal: if the per-attempt success rate is <em>p</em>, then
pass^k &asymp; p<sup>k</sup>. A model that passes 90% of single attempts passes all 10 of 10
only about 35% of the time (0.9<sup>10</sup> &asymp; 0.349). This is the statistic that matches
production reality for customer-facing systems, where every individual interaction must work
and there is no human retrying until it succeeds.
</p>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">The gap is the diagnosis</div>
    <p>A large spread between pass@k and pass^k is informative on its own: the system
    <em>can</em> do the task but does not do it <em>reliably</em>. That points at
    non-determinism-sensitive components — ambiguous prompt instructions, flaky tools,
    order-dependent context — rather than at raw capability, so the fix is tightening the
    system, not upgrading the model.</p>
</div>

<h3>Practical consequences for harness design</h3>
<ul>
    <li><strong>Run multiple trials per task.</strong> Anthropic's guidance is to treat
        single-trial evals on agentic tasks as noise-dominated; report pass rate with the trial
        count (e.g. "pass^4 = 62% over 5 tasks × 4 trials").</li>
    <li><strong>Pick k from the product contract, not habit.</strong> If the SLA is "works
        every time for every customer", gate on pass^k with k sized so the implied per-attempt
        rate meets the bar. If the workflow tolerates retries, pass@k with modest k is honest.</li>
    <li><strong>Don't average away variance.</strong> A mean score of 80% could be every task
        at 80% (consistency problem) or 80% of tasks at 100% and the rest at 0% (capability
        problem on a subset). The two need different fixes; per-task trial breakdowns
        distinguish them.</li>
</ul>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Two reliable stems: (1) numbers — given per-attempt p and k, know that consistency
    collapses exponentially (0.9<sup>10</sup> &asymp; 35% is the canonical figure); (2) metric
    choice — retry-tolerant/CI-gated workflows credit pass@k, always-must-work customer flows
    credit pass^k. A distractor will offer "average success rate across runs", which hides
    exactly the variance the question is about.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The code migration agent runs in CI: it attempts a port, and the pipeline\'s test suite rejects bad pull requests automatically, after which the agent may retry. Which consistency statistic best matches this deployment for a "is it useful?" decision?',
                'options' => [
                    'pass^k, because production systems must succeed every time',
                    'pass@k, because a verifier gates outputs and retries are cheap — one success out of k attempts delivers the value',
                    'Mean unit-test pass percentage across all attempts',
                    'pass^k on the test suite and pass@k on code style',
                ],
                'answer' => 1,
                'explain' => 'This workflow is exactly the retry-tolerant, externally-verified case where pass@k is the honest measure: a failed attempt costs compute, not customer harm, because CI blocks it. pass^k (A) would understate the agent\'s real usefulness. Averaging attempts (C) blends successes and failures into a number that answers neither the capability nor the consistency question.',
            ],
            [
                'q' => 'The support deflection agent passes 90% of single-attempt trials on the eval suite. Leadership asks: "if a customer interaction involves this flow ten times, how often does it work all ten times?" What is the approximate answer, and what does it imply?',
                'options' => [
                    'About 90% — per-attempt rate carries over to sequences',
                    'About 65% — reliability degrades linearly with attempts',
                    'About 35% — consistency decays exponentially (0.9¹⁰), so a 90% per-attempt rate is far from production-reliable',
                    'It cannot be estimated without knowing the temperature setting',
                ],
                'answer' => 2,
                'explain' => '0.9¹⁰ ≈ 0.349. This is the canonical pass^k arithmetic: per-attempt success compounds multiplicatively, so seemingly strong single-run rates imply weak all-runs reliability. It is why Anthropic\'s agent-eval guidance pushes teams to report pass^k for always-must-work flows rather than single-trial pass rates.',
            ],
            [
                'q' => 'An eval report shows pass@5 = 96% but pass^5 = 41% on the research report agent. What is the most supported conclusion?',
                'options' => [
                    'The tasks are ambiguous or the harness is flaky — capability is demonstrated, so the gap points to sources of run-to-run variance',
                    'The model lacks the capability for these tasks and a stronger model tier is required',
                    'The eval set is too small to draw conclusions',
                    'The judge grading the reports is miscalibrated',
                ],
                'answer' => 0,
                'explain' => 'High pass@k proves the capability ceiling is fine — the system can do nearly every task. The collapse in pass^k means it doesn\'t do them dependably, which indicts variance sources: underspecified prompts, unreliable tools, or genuinely ambiguous task definitions. A stronger model (B) targets capability, which isn\'t the deficit. C and D are possible in general but nothing in the evidence points to them, while the pass@k/pass^k spread points specifically at consistency.',
            ],
        ],
    ],

    // =====================================================================
    'ev-1-04' => [
        'body' => <<<'HTML'
<p>
Accuracy tells you whether the system is right; the other four axes tell you whether it is
<em>viable</em>. The exam expects an architect to define, measure, and set floors on all five.
This topic gives each remaining axis its operational definition and its standard trap.
</p>

<h3>Latency: measure the distribution, name the milestone</h3>
<ul>
    <li><strong>TTFT (time to first token)</strong> governs perceived responsiveness in
        streaming UIs — a user watching text begin to appear tolerates a long total time.</li>
    <li><strong>Total/end-to-end time</strong> governs machine-to-machine flows and agent
        steps, where nothing downstream starts until the output completes.</li>
    <li>Report <strong>percentiles (p50/p90/p99), never the mean</strong> — LLM latency is
        long-tailed, and the mean hides the tail your angriest users live in. An SLO belongs on
        a percentile: "p90 TTFT &lt; 1.5 s".</li>
</ul>
<p>
Because output tokens dominate generation time, latency and verbosity are coupled: the cheapest
latency fix is often "generate fewer tokens", which is also a cost fix (Module 6).
</p>

<h3>Cost: per-task, fully loaded</h3>
<p>
The unit that matters is <strong>cost per task</strong> (or per conversation/resolved case),
not cost per token: input tokens, output tokens, cache writes and reads, retries, and every
sub-agent or judge call the task triggers. A per-token view hides the agent that burns 40 tool
calls to answer one question. Cost criteria then live at the margin the business feels:
"&lt; $0.12 per deflected conversation against a $9 human handle cost" is a criterion;
"minimise spend" is not.
</p>

<h3>Safety: does the system's behaviour cause harm?</h3>
<p>
Safety axes are gated with <em>floors</em>, not traded on averages: harmful-content rate on an
adversarial suite, groundedness/hallucination rate for RAG systems, and — easy to forget —
<strong>over-refusal</strong>: a system that refuses legitimate requests fails users too, so a
refusal-correctness measure keeps the pendulum honest. Safety suites are deliberately
adversarial and unbalanced (heavy on edge cases) because their job is probing failure, not
estimating average behaviour.
</p>

<h3>Security: can an adversary make the system misbehave?</h3>
<p>
Distinct from safety: security asks what an <em>attacker</em> can extract or trigger. Standard
measured behaviours: prompt-injection resistance (instructions embedded in retrieved docs,
tool results, or user uploads), system-prompt and secret leakage, data exfiltration across
tenant or session boundaries, and abuse of tool permissions. These are evaluated with dedicated
adversarial suites, and — like safety — gated with floors and re-run on every change to
prompts, tools, or retrieval, because injection resistance is a property of the whole pipeline,
not the model alone.
</p>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">Axis interactions are the architect's job</div>
    <p>The five axes trade against each other: extended thinking buys accuracy with latency and
    cost; a smaller model tier buys cost with accuracy risk; tighter refusal rules buy safety
    with over-refusal. The discipline from ev-1-01 applies — pick target axes to optimise and
    put explicit floors under the rest, so a win on one axis cannot silently buy a loss on
    another.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Three recurring discriminations: mean vs. percentile latency (percentile is credited);
    per-token vs. per-task cost (per-task is credited); safety vs. security (harm from the
    system's own behaviour vs. behaviour an adversary induces). If a stem mentions instructions
    hidden in a retrieved document, it is a security/prompt-injection item even if the visible
    symptom looks like a quality bug.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The support deflection agent reports mean response latency of 1.8 s, comfortably under the 3 s target, yet complaint volume about "the bot hanging" is rising. What is the most likely explanation an architect should check first?',
                'options' => [
                    'Users are on slow networks, which the team cannot control',
                    'Latency is long-tailed: the mean hides a p99 of tens of seconds, so a meaningful share of interactions blows past the target — check percentiles',
                    'The 3 s target was too generous and should be lowered to 1 s',
                    'TTFT is fine, so the complaints must concern answer quality, not speed',
                ],
                'answer' => 1,
                'explain' => 'LLM latency distributions are heavily right-skewed — long generations, retries, and tool-call chains create a tail that a mean of 1.8 s can completely conceal. The standard fix is to define SLOs on percentiles (p90/p99) and monitor them. The other options either guess without evidence or change the target instead of measuring the distribution correctly.',
            ],
            [
                'q' => 'Which pair correctly distinguishes a <strong>safety</strong> evaluation from a <strong>security</strong> evaluation for the policy knowledge assistant (internal RAG over HR docs)?',
                'options' => [
                    'Safety: rate of answers contradicting current policy · Security: p99 latency under adversarial load',
                    'Safety: cost per query staying under budget · Security: schema validity of tool calls',
                    'Safety: hallucinated-policy rate and over-refusal rate on sensitive HR questions · Security: whether instructions embedded in an uploaded document can make the assistant reveal another employee\'s records',
                    'They are the same axis measured on different datasets',
                ],
                'answer' => 2,
                'explain' => 'Safety measures harm from the system\'s own behaviour (fabricated policy answers, wrongly refusing legitimate questions); security measures what an adversary can induce — prompt injection via uploaded content leading to cross-employee data disclosure is the classic RAG security case. Latency-under-load (A) is a performance concern, and B mixes in cost and format validity, which are different axes entirely.',
            ],
            [
                'q' => 'A team optimising the research report agent tracks "average cost per 1K output tokens" and celebrates a 30% reduction after switching prompts. Finance later reports the monthly bill went <em>up</em>. What most plausibly happened?',
                'options' => [
                    'The provider raised prices mid-month',
                    'The new prompt made each token cheaper to serve',
                    'Cache reads were misattributed to a different cost centre',
                    'Per-token cost fell but tokens per task rose — longer agent loops, more tool calls, more retries — so fully-loaded cost per task increased; per-task cost was the right unit',
                ],
                'answer' => 3,
                'explain' => 'This is the per-token vs. per-task trap. A prompt change can shift the token mix toward cheaper rates while causing the agent to take more steps or produce longer outputs, raising total consumption per completed task. Cost criteria should be stated per task (or per resolved conversation), fully loaded with retries, sub-agent calls, and cache traffic — that is the number the business pays.',
            ],
        ],
    ],
];
