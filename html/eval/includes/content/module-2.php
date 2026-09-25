<?php
/**
 * module-2.php — study content for Module 2: Eval Datasets and Harness Design.
 *
 * Grounded in Anthropic's published guidance: "Create strong empirical
 * evaluations" (docs.claude.com), the engineering post "Demystifying evals
 * for AI agents" (task quality, trials, harness design), and Claude Code
 * best practices (evals as living artifacts).
 *
 * Returns: [ topic-id => ['body' => html, 'questions' => [...] ] ]
 */
return [

    // =====================================================================
    'ev-2-01' => [
        'body' => <<<'HTML'
<p>
Before a team can argue about results, it has to agree on what the words mean. Half of the
"our eval says X" disputes in practice are two people using <em>run</em>, <em>case</em>, and
<em>score</em> for different things. Anthropic's evaluation guidance uses a small, consistent
vocabulary, and the exam expects you to read result tables in exactly these terms.
</p>

<h3>The nouns, from smallest to largest</h3>
<ul>
    <li><strong>Task (or case)</strong> — one unit of work with a defined input and a defined
        way to decide success: "route claim #4411", "extract clauses from contract 17". The
        task owns the <em>grading procedure</em>, not just the input.</li>
    <li><strong>Trial (or attempt)</strong> — one execution of one task. Because LLM systems
        are non-deterministic, a task is usually run as several trials; the trial is what
        produces a transcript.</li>
    <li><strong>Grader</strong> — the procedure (code, model, or human) that turns a trial's
        output into a <strong>score</strong>. One task can have several graders: a schema
        check <em>and</em> a rubric judge.</li>
    <li><strong>Run</strong> — one execution of a whole set of tasks against one system
        configuration (model, prompt version, tool set). Comparing "run 41 vs run 42" is only
        meaningful if exactly one thing changed between them.</li>
    <li><strong>Suite (or dataset)</strong> — the versioned collection of tasks itself,
        independent of any run over it.</li>
</ul>

<h3>The anatomy of a single task</h3>
<div class="table-responsive">
<table class="table table-sm align-middle">
    <thead>
        <tr class="text-secondary small text-uppercase">
            <th>Field</th><th>What it holds</th><th>Why it matters</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Input</td>
            <td>The prompt payload: user message, documents, tool fixtures, starting state</td>
            <td>Must be complete enough to reproduce the trial exactly</td>
        </tr>
        <tr>
            <td>Target / reference</td>
            <td>Expected answer, rubric, or checkable end-state</td>
            <td>Defines success independent of who is grading today</td>
        </tr>
        <tr>
            <td>Grading procedure</td>
            <td>Which grader(s) run and how scores combine</td>
            <td>The same output can pass one grader and fail another</td>
        </tr>
        <tr>
            <td>Metadata</td>
            <td>Category, difficulty, source (production incident? synthetic?), version</td>
            <td>Enables slicing results and auditing where cases came from</td>
        </tr>
    </tbody>
</table>
</div>

<h3>Trials vs. tasks — where the statistics attach</h3>
<p>
The pass@k and pass^k statistics from ev-1-03 attach at the <em>task</em> level and are
computed over <em>trials</em>: "pass^4 = 62% over 50 tasks × 4 trials" means each task ran
four times and 62% of tasks succeeded in all four. A report that says "the eval passed 85%"
without stating tasks, trials per task, and the aggregation rule is unreadable — 85% of
trials, 85% of tasks on best-of-k, and 85% of tasks on all-of-k are three different systems.
</p>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">One change per comparison</div>
    <p>A run is a measurement of a <em>configuration</em>. If run 42 changed the prompt and
    the model and two tasks, no arithmetic on the score difference attributes the movement to
    anything. The vocabulary exists so that "what changed between these two runs?" always has
    a one-line answer.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Expect result-table stems: given "50 tasks, 4 trials each, 170/200 trials passed,
    41/50 tasks passed all trials", know which number is the trial pass rate (85%) and which
    is pass^4 (82%). Distractors swap levels — quoting a trial-level rate as if it were
    task-level reliability. Also expect "what is wrong with this report?" items where the
    answer is a missing trial count or two variables changed between runs.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'An eval report for the claims triage assistant reads: "Run 18: 88% pass." Which single addition makes the number interpretable?',
                'options' => [
                    'The p90 latency of the run, so quality can be traded against speed',
                    'The name of the engineer who executed the run',
                    'The cost per task, since pass rates must be cost-normalised',
                    'The number of tasks, trials per task, and whether 88% aggregates trials or tasks (and by what rule)',
                ],
                'answer' => 3,
                'explain' => '"88% pass" is ambiguous across three readings: share of trials that passed, share of tasks that passed at least once (pass@k), or share of tasks that passed every time (pass^k) — and these describe very different systems. Tasks, trials, and the aggregation rule resolve it. Latency (A) and cost (C) are other axes, not fixes for an uninterpretable accuracy number; the engineer\'s name (B) is provenance, not semantics.',
            ],
            [
                'q' => 'Between run 30 and run 31 of the support deflection agent suite, the team upgraded the model tier, rewrote the escalation section of the system prompt, and added six new tasks. Run 31 scores 5 points lower. What can be concluded?',
                'options' => [
                    'The model upgrade caused a regression and should be rolled back',
                    'The new tasks are too hard and should be rebalanced',
                    'The prompt rewrite hurt escalation behaviour',
                    'Nothing attributable — three variables changed at once; the comparison must be re-run isolating one change at a time on the same task set',
                ],
                'answer' => 3,
                'explain' => 'A run measures a configuration, and comparisons attribute differences only when exactly one thing changed. Here even the denominator changed (six new tasks), so the two scores are not over the same population. Each of A, B, and C is a plausible story, and that is precisely the problem — the evidence supports all and none of them equally.',
            ],
            [
                'q' => 'In the code migration agent suite, task ev-mig-07 is executed 5 times; 3 attempts produce a PR that passes the fixture test suite. Which statement uses the vocabulary correctly?',
                'options' => [
                    'The task had 5 trials; it passes under pass@5 but fails under pass^5',
                    'The task ran 5 runs and passed 3 suites',
                    'The grader had 5 trials and scored 3 tasks',
                    'The suite had 5 cases and the run passed 60% of graders',
                ],
                'answer' => 0,
                'explain' => 'One task, five executions — those executions are trials. At least one trial succeeded, so pass@5 is satisfied; not all five succeeded, so pass^5 is not. The other options scramble the nouns: runs are whole-set executions, graders are scoring procedures, and cases are tasks — none of them are things a single task "has five of" here.',
            ],
        ],
    ],

    // =====================================================================
    'ev-2-02' => [
        'body' => <<<'HTML'
<p>
The most common dataset mistake is waiting: teams postpone evals until they can build
something "statistically rigorous", ship on vibes in the meantime, and never come back.
Anthropic's guidance inverts this — <strong>start small and real</strong>. Twenty to fifty
cases drawn from genuine usage beat five hundred synthetic ones, because the failure modes
that matter are the ones your traffic actually produces, and a small suite you run on every
change beats a large one you run quarterly.
</p>

<h3>Where the first cases come from</h3>
<ul>
    <li><strong>Real production failures.</strong> Every incident, bad ticket, and thumbs-down
        is a candidate case: it is realistic by construction and it protects against the exact
        regression you already paid for once. For the support deflection agent, the first
        twenty cases should be twenty real conversations that went wrong.</li>
    <li><strong>The happy path.</strong> A handful of bread-and-butter cases that must never
        break — the suite's smoke test.</li>
    <li><strong>Known failure modes and edge cases.</strong> Ambiguous inputs, unusually long
        inputs, inputs in the wrong language or format, adversarial phrasings, empty or
        contradictory documents. Anthropic's docs explicitly recommend testing edge cases the
        happy path never exercises, because production traffic will find them for you
        otherwise.</li>
</ul>

<h3>Synthetic expansion — useful, but reviewed</h3>
<p>
Once the seed set exists, using Claude to generate variations is a legitimate way to expand
coverage: paraphrases of real tickets, harder versions of solved cases, systematic sweeps over
a parameter (contract length, claim type). Two disciplines keep it honest. First,
<strong>human-review every generated case</strong> before it enters the suite — generators
produce ambiguous, unsolvable, or subtly mislabelled tasks at a meaningful rate, and an
unreviewed wrong label converts into a permanent phantom failure. Second, keep provenance
metadata (real vs. synthetic) so you can check that the system's score on synthetic cases
tracks its score on real ones; if they diverge, the synthetic slice is measuring something
else.
</p>

<h3>Holding out, and the overfitting ratchet</h3>
<p>
If engineers iterate a prompt against the full suite daily, the prompt gradually specialises
to those fifty cases — score climbs, production doesn't. The standard defences: keep a
<strong>held-out set</strong> that is only consulted at release gates, rotate fresh cases in
from production, and treat a growing gap between dev-set and held-out scores as an alarm, not
a curiosity. This is the eval analogue of test-set leakage in classical ML, and the exam
treats it as such.
</p>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">The trap: impressive-looking synthetic scale</div>
    <p>A stem will offer "generate 1,000 cases with an LLM this week" against "collect 30 real
    failure cases this month". The big number is the distractor: unreviewed synthetic cases
    import label noise and distribution mismatch, and a suite nobody trusts is a suite nobody
    gates on. Real, small, and run-on-every-change is the credited shape.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Three recurring discriminations: (1) start with 20–50 real cases now vs. a large
    synthetic or "rigorous" set later — small-and-real wins; (2) synthetic generation is
    acceptable <em>with human review and provenance tracking</em>, never as a raw dump;
    (3) score up on the dev suite while production complaints persist → suspect overfitting to
    the eval set, and the fix is held-out data plus fresh production cases, not more prompt
    iteration.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The policy knowledge assistant is two weeks from pilot and has no eval dataset. Which starting point best follows Anthropic\'s guidance?',
                'options' => [
                    'Generate 800 synthetic Q&amp;A pairs from the HR documents with Claude and run them nightly',
                    'License a public open-domain question-answering benchmark to get a statistically large baseline',
                    'Collect 25–40 real employee questions (including known-tricky ones about outdated policies), write expected grounded answers, and run that suite on every change',
                    'Wait for the pilot to produce a month of traffic so the dataset reflects true usage',
                ],
                'answer' => 2,
                'explain' => 'Small, real, and run-on-every-change is the credited pattern: real questions carry the ambiguity and phrasing the system will actually face, and a suite this size is cheap enough to gate every prompt change. A is scale without review or realism; B measures a different distribution entirely; D ships the pilot with no measurement at all — the failure the guidance exists to prevent.',
            ],
            [
                'q' => 'A team expands the contract clause extractor suite by having Claude generate 200 synthetic contracts with labelled clauses. Which control is <em>most essential</em> before these enter the suite?',
                'options' => [
                    'Human review of the generated contracts and labels, with provenance metadata distinguishing synthetic from real cases',
                    'Running the generator at temperature 0 so the contracts are reproducible',
                    'Making sure the 200 cases are split evenly across the seven clause types',
                    'Compressing the contracts so the suite stays cheap to run',
                ],
                'answer' => 0,
                'explain' => 'Unreviewed generated cases carry mislabels and unsolvable tasks at a meaningful rate, and every wrong label becomes a permanent false failure (or false pass) in the suite; provenance lets the team later check whether synthetic scores track real ones. Balance (C) matters but is secondary to the cases being correct at all. Temperature (B) and cost (D) do not address label validity.',
            ],
            [
                'q' => 'Over six weeks of prompt iteration, the support deflection agent\'s score on the 60-case dev suite rises from 71% to 93%, but escalation complaints in production are unchanged. What is the most likely explanation and remedy?',
                'options' => [
                    'The model is drifting; pin the model version and re-run',
                    'The prompt has overfitted to the dev suite; check against a held-out set and rotate fresh production failures into the suite',
                    'The suite is too small for the gain to be significant; expand it tenfold with synthetic cases',
                    'Production users are an unrepresentative minority; trust the eval',
                ],
                'answer' => 1,
                'explain' => 'Score climbing on a fixed, frequently-iterated-against set while the production signal stays flat is the signature of eval-set overfitting — the prompt has specialised to those 60 cases. A held-out set exposes it (the gap between dev and held-out scores) and fresh real cases repair it. A invents a cause the evidence doesn\'t show; C adds noisy scale without fixing leakage; D inverts the epistemics — production is the ground truth the eval exists to predict.',
            ],
        ],
    ],

    // =====================================================================
    'ev-2-03' => [
        'body' => <<<'HTML'
<p>
A suite is only as trustworthy as its worst task. Anthropic's agent-evaluation guidance is
emphatic that most "model failures" surfaced by immature suites are actually <em>task
failures</em>: the case was ambiguous, unsolvable with the information provided, or graded
against a reference that reasonable experts dispute. Before a low score indicts the system,
the task has to survive three checks.
</p>

<h3>Solvable — by a competent human, with the same resources</h3>
<p>
The standard is concrete: could a competent human, given exactly the same instructions, tools,
and information the system gets, produce the expected answer? If the claims triage task
expects "route to fraud review" but the fraud indicator lives in a database table the
assistant has no tool for, the task tests telepathy, not triage. This check catches missing
context, broken fixtures, references that depend on information outside the input, and tasks
whose expected answer changed since they were written.
</p>

<h3>Unambiguous — one defensible answer, or a rubric that forces agreement</h3>
<p>
For closed tasks, there should be a single defensible correct answer. For open tasks (the
research report agent), ambiguity is managed with a rubric specific enough that two graders
agree: not "is the report good?" but "does it cite &ge; 5 of the 8 seed sources, state the
opposing finding in section 2, and avoid claims absent from the sources?". The operational
test is <strong>grader agreement</strong>: if two humans (or the judge run twice) disagree on
the same output, the task is measuring the grader's mood. Fix the task or drop it — a case
that flips verdicts contributes pure noise while looking like signal.
</p>

<h3>Balanced — so the aggregate means something</h3>
<p>
The suite-level score is an average, and averages are hostage to composition. If 60% of the
clause-extractor suite is termination clauses, the headline number mostly measures termination
clauses, and a regression on indemnification hides inside a stable aggregate. Balance across
<strong>category</strong> (clause types, claim queues, question topics) and
<strong>difficulty</strong> (the suite needs cases the current system fails, or it cannot
detect improvement — a suite passing at 100% is a smoke test, not an eval). Balance does not
mean uniformity: weighting can mirror traffic or mirror harm, but the choice must be explicit,
and results should be sliceable by category so the aggregate never has to be trusted alone.
</p>

<h3>Pilot before trusting</h3>
<p>
The cheap ritual that catches all three defects: before a task enters the suite, have a human
attempt it cold and have two graders score a sample of outputs independently. Tasks the human
cannot solve, or the graders disagree on, go back to the shop. Anthropic's guidance frames
this as testing the test — a step teams skip exactly once.
</p>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">Read low scores as a fork</div>
    <p>When a task fails persistently across models, prompts, and trials, the prior should
    shift toward "bad task" before "bad system". Persistent, invariant failure is the
    fingerprint of unsolvability or a wrong reference; real capability failures usually move
    when the system changes.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Stems present a suspicious task or a suspicious aggregate. For tasks: "graders
    disagree" or "no human could answer from the given input" → repair or remove the task,
    never tune the model to it. For aggregates: a stable headline hiding a category regression
    → the credited answer slices by category or rebalances, not "the suite is fine". And a
    suite at 100% pass is saturated — it can no longer detect improvement or regression.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'Task 23 of the policy knowledge assistant suite asks "How many vacation days do I get?" and the reference answer is "25 days". The HR documents provided to the assistant give different figures by seniority band and country, and the employee\'s band is not stated. The task has failed on every model and prompt for three months. What is the correct action?',
                'options' => [
                    'Add the task to the hard-case slice and weight it higher, since hard cases drive improvement',
                    'Fix or remove the task: it is unsolvable as posed — the correct answer is either a clarifying question or conditional, so the reference itself is wrong',
                    'Upgrade to a stronger model tier, since persistent failure indicates a capability gap',
                    'Lower the pass threshold for this category to compensate',
                ],
                'answer' => 1,
                'explain' => 'A competent human with these documents could not answer "25 days" either — the input underdetermines the answer, so the task fails the solvability check and its invariant failure across every configuration is the fingerprint. Treating it as a hard case (A) or a capability gap (C) tunes the system toward a wrong reference; threshold games (D) hide the noise instead of removing it.',
            ],
            [
                'q' => 'Two senior adjusters independently grade 20 outputs from a claims triage task and disagree on 7 of them. What does this most directly tell the team?',
                'options' => [
                    'The task or its rubric is too ambiguous to produce signal; scores on it reflect grader variance, and it needs a tighter rubric or removal',
                    'One of the adjusters needs retraining before grading continues',
                    'An LLM judge should replace the humans, since it is more consistent',
                    'The model\'s outputs are borderline, which proves the task has good difficulty calibration',
                ],
                'answer' => 0,
                'explain' => 'Grader agreement is the operational test of ambiguity: if two domain experts cannot agree on the same outputs, the task is measuring the grader, and any score computed from it is noise dressed as signal. B assumes one grader is right without evidence. C inherits the same ambiguity — an LLM judge applying an ambiguous rubric is consistently arbitrary. D mistakes a defect for a feature.',
            ],
            [
                'q' => 'The contract clause extractor suite holds steady at 91% while counsel reports a spike in missed indemnification clauses. Investigation shows indemnification is 4 of the suite\'s 120 cases. What is the best remedy?',
                'options' => [
                    'Trust the suite — 91% on 120 cases outweighs anecdotal reports',
                    'Retire the suite and rely on counsel\'s production feedback going forward',
                    'Rebalance the suite across clause types (adding indemnification cases from the reported misses) and report per-category scores alongside the aggregate',
                    'Raise the overall pass threshold from 91% to 95%',
                ],
                'answer' => 2,
                'explain' => 'A 3%-weight category can regress badly while the aggregate barely moves — the headline number is hostage to composition. Rebalancing plus per-category reporting makes the regression visible and turns the real production misses into permanent test cases. A privileges a structurally blind metric over ground truth; B abandons regression protection entirely; D tightens a threshold on the same blind aggregate.',
            ],
        ],
    ],

    // =====================================================================
    'ev-2-04' => [
        'body' => <<<'HTML'
<p>
An eval's entire value is predictive: the score is worth something only if it forecasts how
the system behaves in production. That forecast rests on <strong>parity</strong> — the harness
must exercise the same system users get — and on <strong>clean state</strong> — each trial
must start from the same world. Break either and you get numbers that are precise,
repeatable, and about some other system.
</p>

<h3>Parity: evaluate the system you ship</h3>
<p>
Every element of the production request path belongs in the harness:
</p>
<ul>
    <li><strong>Same model and version</strong> — a suite run on a different tier or an older
        snapshot predicts nothing about the deployed one.</li>
    <li><strong>Same system prompt and prompt-assembly code</strong> — not a hand-copied
        approximation that drifted three releases ago. Ideally the harness imports the exact
        production prompt-construction function.</li>
    <li><strong>Same tools with the same schemas</strong> — an agent evaluated with a mocked
        search tool that never errors will score above an agent whose real tool times out 2%
        of the time.</li>
    <li><strong>Same context construction</strong> — retrieval settings, chunking, history
        truncation. For the policy knowledge assistant, evaluating with hand-picked "relevant"
        documents instead of the production retriever measures the model's reading, not the
        system's answering.</li>
    <li><strong>Same sampling configuration</strong> — temperature, max tokens, thinking
        settings. A common false regression: the harness pinned an option the production
        config later changed.</li>
</ul>
<p>
Divergences are sometimes forced (production data you cannot copy, side effects you must
stub). The discipline is to make each divergence <em>explicit and justified</em>, and to
treat any surprising eval/production gap as "check parity first".
</p>

<h3>Clean state: every trial starts from the same world</h3>
<p>
Trials contaminate each other through anything mutable: files a previous attempt wrote,
database rows it inserted, conversation context that leaked, caches warmed by earlier runs.
The code migration agent is the canonical case — trial 2 starting in trial 1's half-migrated
repository is testing a different task. The standard tooling: fixtures rebuilt per trial,
sandboxed or containerised workspaces, fresh sessions, and side-effecting external calls
(emails, payments) isolated behind test doubles <em>that still simulate realistic failures</em>.
Symptoms of state leakage read like model nondeterminism — scores that depend on execution
order, or pass rates that drift across the afternoon — which is why it goes undiagnosed.
</p>

<h3>Log the whole transcript</h3>
<p>
A harness that records only pass/fail can tell you <em>that</em> trial 7 failed, never
<em>why</em>. Persist the full trace per trial: every prompt as assembled, every model
response, every tool call and its result, timing and token counts, and the grader's verdict
with its reasoning. This is what turns an eval from a scoreboard into a diagnostic instrument
— and it is the same trace-first habit Module 7 applies to production logging.
</p>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">The trap: the flattering harness</div>
    <p>Mocked tools that never fail, hand-fed retrieval, generous timeouts, temperature pinned
    to 0 while production runs higher — each makes the score better and the forecast worse.
    When a stem shows "eval 94%, production complaints rising", enumerate the parity gaps
    before blaming the model or the users.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Two reliable shapes: (1) eval/production divergence → the credited answer names a
    specific parity gap (mocked tool, different retriever, drifted prompt copy); (2) flaky,
    order-dependent scores on an agentic suite → suspect state leakage between trials, and the
    fix is per-trial isolation, not more trials. "Log full transcripts" is the credited
    logging answer over "log scores".</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The support deflection agent scores 94% in the eval harness but generates rising complaint volume in production. The harness uses the production prompt and model, but its ticket-lookup tool is a mock that always returns a well-formed record in 50 ms; the real tool times out on about 3% of calls and returns partial records for legacy accounts. What is the most likely explanation of the gap?',
                'options' => [
                    'Production users ask harder questions than the eval set contains',
                    'The harness lacks tool parity: the system was never evaluated on timeout and partial-record handling, which is exactly where production is failing',
                    'The model is nondeterministic, so a 94% eval score is compatible with any production outcome',
                    'Complaints are a lagging indicator and will fall once users adapt',
                ],
                'answer' => 1,
                'explain' => 'A mock that never fails removes an entire class of production behaviour — error handling — from measurement, so the score is about a friendlier system than the one shipped. The stem hands you the specific divergence, which is stronger evidence than the generic possibilities in A (possible, but not indicated) and C (nondeterminism does not explain a systematic gap). D is not an engineering position.',
            ],
            [
                'q' => 'In the code migration agent suite, pass rates for the same tasks differ depending on the order tasks run, and afternoon runs score lower than morning runs. What should the team suspect first?',
                'options' => [
                    'Provider-side model drift over the course of the day',
                    'The tasks are too difficult and should be simplified',
                    'State leaking between trials — shared workspaces, residual files, or fixtures not rebuilt per trial — so trials are not starting from a clean, identical state',
                    'The temperature is set too high for reproducible results',
                ],
                'answer' => 2,
                'explain' => 'Order dependence is the fingerprint of shared mutable state: earlier trials leave files, branches, or database rows that change later trials\' starting conditions, and the effect compounds through the day. Model drift (A) and temperature (D) produce randomness, not order-correlated patterns. The fix is per-trial isolation (fresh sandbox/fixtures), not simplifying tasks (B) or averaging more trials over a contaminated harness.',
            ],
            [
                'q' => 'A team\'s harness records, per trial: task id, pass/fail, and total latency. A release-gating run fails 9 of 60 tasks and the team must decide whether to ship. What is the practical consequence of this logging design?',
                'options' => [
                    'None — pass/fail is sufficient for a gate decision, and the team should simply not ship',
                    'Latency should also have been recorded per token to complete the picture',
                    'The team should re-run the 9 failures at temperature 0 to get deterministic verdicts',
                    'The failures cannot be diagnosed: without full transcripts (assembled prompts, model outputs, tool calls, grader reasoning), the team cannot tell task defects from system regressions, so the gate result is unactionable',
                ],
                'answer' => 3,
                'explain' => 'A gate that fails demands a diagnosis: are the 9 failures a real regression, flaky tasks, or a grader problem? Score-only logging cannot answer, so the team is stuck between shipping blind and blocking blind. Full per-trial transcripts are what make an eval a diagnostic instrument. Treating pass/fail as sufficient (A) makes the gate an oracle; per-token latency (B) adds a metric without adding diagnosability; re-running at temperature 0 (C) changes the system under test instead of examining the evidence.',
            ],
        ],
    ],

    // =====================================================================
    'ev-2-05' => [
        'body' => <<<'HTML'
<p>
A suite built once and frozen decays on two fronts at the same time: the product moves (new
features, new tools, new traffic) so coverage shrinks, and the system improves so old cases
saturate. Anthropic's guidance — consistent from the evals docs to Claude Code best practices
— is to treat the suite as a <strong>living artifact</strong> with an owner, a version
history, and a maintenance loop, exactly like a production codebase's test suite.
</p>

<h3>The intake loop: incidents become cases</h3>
<p>
The cheapest, highest-value source of new cases never dries up: production. Every incident,
escalation, thumbs-down, and support ticket that traces to a bad output should be triaged
into the suite as a minimal reproducing case. This gives the suite the same property a
regression test suite has — <em>the same failure cannot ship twice silently</em> — and it
continuously re-anchors the dataset to the current traffic distribution. A suite fed this way
also stays honest about difficulty: production failures are, by construction, cases the
system got wrong.
</p>

<h3>The pruning loop: saturation and staleness</h3>
<ul>
    <li><strong>Saturated cases</strong> — passed by every configuration for months — no
        longer discriminate. Keep a thin smoke-test layer of them; archive the rest, or the
        suite's headline number drifts toward a flattering constant and real regressions move
        it by fractions of a point.</li>
    <li><strong>Stale cases</strong> — the policy document was updated, the API the task
        exercises was removed, the "correct" route no longer exists. A stale case with an
        outdated reference is worse than no case: it penalises correct current behaviour.
        Quarterly reference review is the boring, necessary ritual (the policy knowledge
        assistant's quarterly document refresh should trigger one automatically).</li>
</ul>

<h3>Version everything, re-baseline deliberately</h3>
<p>
Dataset changes move scores with no change to the system: add ten hard cases and the score
"drops"; prune saturated ones and it "drops" again. Version the suite, record which suite
version every run used, and never compare scores across suite versions as if they were the
same measurement. Model upgrades get the same treatment: when the underlying model changes,
re-run the full suite to establish a <strong>new baseline</strong> before resuming
delta-hunting — the upgrade shifts many scores at once, and stale baselines convert into
weeks of phantom regressions and phantom wins.
</p>

<h3>Suite health is itself a metric</h3>
<p>
Mature teams track the suite the way they track the system: saturation rate (share of cases
at 100% over trailing runs), flake rate (cases whose verdict flips across identical
configurations — candidates for the ev-2-03 ambiguity check), coverage (does every feature,
tool, and traffic category have cases?), and age mix (when did a real production case last
enter?). A suite whose health metrics are decaying is quietly losing its power to say
anything.
</p>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">Ownership, not archaeology</div>
    <p>Suites decay fastest when they are everyone's job. The working pattern is a named owner
    and a small recurring budget: incident triage into cases weekly, flake and saturation
    review monthly, full reference audit quarterly. The cost is hours; the alternative is a
    gate nobody believes.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Watch for four cues: a production incident with no follow-up → credited answer adds it
    to the suite; a suite passing ~100% for months → saturation, prune and add harder cases;
    a score change right after dataset edits or a model upgrade → version/baseline artifact,
    not a system change; verdicts flipping run-to-run on identical configs → flaky cases to
    fix or remove. Distractors treat the suite as fixed ground truth — the domain treats it as
    maintained infrastructure.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The claims triage assistant mis-routes a large fire-damage claim to the auto queue, delaying a payout and triggering an executive escalation. The prompt is patched the same day and the fix verified manually. According to living-suite practice, what step is missing?',
                'options' => [
                    'A post-mortem document circulated to stakeholders',
                    'Reducing the suite to make room for newer cases',
                    'Adding a minimal reproduction of the mis-route to the eval suite, so the regression is caught automatically if any future change reintroduces it',
                    'Retraining the adjusters who labelled the original dataset',
                ],
                'answer' => 2,
                'explain' => 'The incident-to-case loop is the core of the living suite: a manually verified patch protects you today, but only a permanent test case ensures the same failure cannot ship twice silently. A post-mortem (A) is good practice but does not gate future releases. B and D are unrelated to closing the regression loop.',
            ],
            [
                'q' => 'The support deflection agent\'s 140-case suite has passed at 99–100% for five consecutive months, and last week a prompt change that noticeably worsened escalation behaviour sailed through the gate. What best explains the miss, and what is the remedy?',
                'options' => [
                    'The suite is saturated: nearly all cases are ones every configuration passes, so it has lost discriminating power — prune to a smoke-test layer and add current hard cases from production',
                    'The gate threshold was set too low and should be raised to 100%',
                    'The prompt change was too small for any eval to detect',
                    'Escalation behaviour is inherently unevaluable and needs human review only',
                ],
                'answer' => 0,
                'explain' => 'A suite at a months-long ceiling cannot detect regressions — everything passes, so the score cannot move. The remedy is composition, not thresholds: keep a few saturated cases as smoke tests, archive the rest, and add cases the current system finds hard (ideally the very escalation failures just observed). B tightens a blind instrument; C is contradicted by the stem (the change was noticeable); D gives up on a measurable behaviour.',
            ],
            [
                'q' => 'After upgrading the policy knowledge assistant to a new model version, the team sees the suite score move from 87% to 84% and files it as a regression to fix before any other work. Which practice did they skip?',
                'options' => [
                    'Pinning temperature to 0 before comparing runs',
                    'Re-baselining: a model upgrade shifts many scores at once, so the full suite result establishes a new baseline to examine case-by-case — some drops are real, some cases are now stale or graded against style assumptions of the old model — before treating the aggregate delta as a single regression',
                    'Running the suite twice and averaging the two scores',
                    'Freezing the dataset version, which must never change after launch',
                ],
                'answer' => 1,
                'explain' => 'Cross-model comparison against the old baseline treats a broad distribution shift as one bug. The practice is to re-run, inspect which cases moved and why (real capability change vs. stale references vs. rubric/style sensitivity), and set the new baseline deliberately — possibly repairing cases in the process. A and C address sampling noise, not distribution shift; D is backwards — datasets are versioned precisely because they must be allowed to change.',
            ],
        ],
    ],
];
