<?php
/**
 * module-6.php — study content for Module 6: Optimising Tokens, Latency, and Cost.
 *
 * Grounded in Anthropic's published guidance: prompt caching documentation and
 * pricing (write 1.25× / 1-hour write 2× / read 0.1×), the Message Batches API
 * (50% discount, results within 24 hours), model-tier selection guidance
 * (Haiku/Sonnet/Opus), extended thinking documentation, and the recurring
 * principle that optimisation follows — never precedes — an established
 * quality baseline.
 *
 * Returns: [ topic-id => ['body' => html, 'questions' => [...] ] ]
 */
return [

    // =====================================================================
    'ev-6-01' => [
        'body' => <<<'HTML'
<p>
Optimisation questions on the exam are rarely about knowing the levers — they are about
knowing the <em>order</em>. Anthropic's guidance is consistent: establish quality first,
measure where the spend actually goes second, and only then pull levers, re-running the eval
suite after each one. Teams that invert this order end up with a fast, cheap system and no
idea what they broke to get it.
</p>

<h3>Quality first, and the eval suite as the safety net</h3>
<p>
Before any optimisation you need two things: a quality bar the system currently meets
(Module 1's success criteria, measured by Module 2's suite) and a regression gate that will
catch a drop. Every optimisation in this module — cheaper tier, shorter context, truncated
output — is a controlled degradation risk. The eval suite is what converts "we think Haiku is
good enough here" into evidence. If there is no suite, the first optimisation task is building
one, not switching models.
</p>

<h3>Profile before you optimise</h3>
<p>
LLM spend and latency are rarely where intuition says. Break cost per task into input tokens,
output tokens, cache traffic, retries, and per-step agent calls; break latency into
time-to-first-token, generation time, and tool/retrieval round trips. The claims triage
assistant re-sending a 30k-token system prompt 40,000 times a day has an input problem; the
research report agent writing 8,000-token briefings has an output problem. The same lever
applied to the wrong profile does nothing.
</p>

<h3>The lever hierarchy</h3>
<ul>
    <li><strong>Eliminate work</strong> — fewer calls, shorter context, less output. Free wins
        first: work you do not do costs nothing and cannot regress quality by being wrong.</li>
    <li><strong>Cache repeated work</strong> — prompt caching turns a repeated prefix from
        full price into 0.1× (ev-6-02). No quality risk at all: the model sees identical
        input.</li>
    <li><strong>Route to a cheaper tier</strong> — big savings, real quality risk; requires
        eval evidence per task type (ev-6-03).</li>
    <li><strong>Tune generation</strong> — thinking budgets, output limits, decoding. Fine
        adjustments, made last.</li>
</ul>
<p>
Every lever trades something: a smaller tier trades accuracy risk, shorter context trades
recall of relevant facts, output truncation trades completeness, parallelism trades cost for
latency. An architect names the trade before pulling the lever and states which eval will
detect it.
</p>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">The trap: optimising an unmeasured system</div>
    <p>"We switched to a smaller model and nobody complained" is not evidence — complaint
    volume lags quality by weeks, and silent failures (a wrong claims route, a missed clause)
    may never surface as complaints. If a stem shows cost falling with no quality re-measure,
    the credited criticism is the missing regression gate, not the choice of lever.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Given a cost or latency complaint, the credited first step is almost always "profile
    where tokens/time go" or "confirm the quality baseline and gate" — not the flashiest
    lever. Distractors jump straight to switching models or trimming prompts. Second pattern:
    options that pull a high-risk lever (tier downgrade) when a zero-risk one (caching,
    eliminating redundant context) fits the profile; the zero-risk lever is credited.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'Finance flags the support deflection agent\'s API bill. The team has no eval suite — quality has been assessed by spot-checking transcripts. An engineer proposes switching the main prompt to a smaller, cheaper model tier this sprint. What should the architect require first?',
                'options' => [
                    'A/B test the smaller tier on 50% of live traffic and watch CSAT',
                    'Apply prompt caching first, since it is cheaper to implement than a model switch',
                    'Negotiate committed-use pricing before touching the architecture',
                    'Build a baseline eval suite and measure current quality, so the tier change can be gated on evidence rather than spot-checks',
                ],
                'answer' => 3,
                'explain' => 'A tier downgrade is a controlled quality risk, and the team currently has no instrument that would detect the regression — spot-checks are neither repeatable nor sensitive. The order of operations is: establish the quality baseline, then optimise against it. A live A/B exposes real customers to an unmeasured change and CSAT lags by weeks; caching is a fine lever but does not repair the missing safety net, which is the architect\'s actual blocker; and pricing negotiation changes the rate, not the risk.',
            ],
            [
                'q' => 'Profiling the claims triage assistant shows 85% of daily spend is input tokens: a 28k-token block of system prompt, tool definitions, and routing policy is re-sent unchanged with every one of 40k requests. Which lever should be pulled first?',
                'options' => [
                    'Move the workload to the Batch API for the 50% discount',
                    'Downgrade from the current tier to a smaller model to cut the per-token rate',
                    'Cache the stable 28k-token prefix with prompt caching, cutting repeated input to roughly a tenth of base price with zero quality risk',
                    'Enable extended thinking so each request resolves in fewer retries',
                ],
                'answer' => 2,
                'explain' => 'The profile points at repeated identical input — exactly what prompt caching eliminates: after the write, each reuse bills at 0.1× base input, and the model sees byte-identical context, so there is no quality risk to gate. Batch (A) conflicts with an interactive triage flow\'s latency needs; a tier downgrade (B) saves less on this profile and introduces accuracy risk; extended thinking (D) adds cost and addresses a failure mode the profile gives no evidence of.',
            ],
            [
                'q' => 'A team moves the contract clause extractor to a cheaper tier, sees cost fall 60% and latency improve, and ships to production the same day. Two weeks later counsel reports missed indemnification clauses. What was the process failure?',
                'options' => [
                    'The team optimised without re-running the recall-gated eval suite — cost and latency were measured, quality was assumed',
                    'The tier change should have been combined with the Batch API to save even more',
                    'No optimisation should ever change the model tier on a legal workload',
                    'The team should have monitored complaint volume for a week before full rollout',
                ],
                'answer' => 0,
                'explain' => 'Every optimisation must clear the same quality gate the original system cleared — here, the recall floor on named clause types that this precision-critical workload is gated on. Cost and latency improvements were verified; the axis the lever actually threatens was not. Tier changes on legal workloads are legitimate when eval-proven — a blanket ban is too absolute; complaint volume is a lagging, insensitive detector for silent misses; and stacking further savings on top compounds the unmeasured risk.',
            ],
        ],
    ],

    // =====================================================================
    'ev-6-02' => [
        'body' => <<<'HTML'
<p>
Prompt caching is the highest-leverage cost and latency optimisation in the Claude platform
for any workload that re-sends a stable prefix — which is nearly every production system:
system prompts, tool definitions, few-shot examples, reference documents, and growing
multi-turn conversation history. The exam tests both the economics (know the multipliers) and
the mechanics (know what silently breaks a cache).
</p>

<h3>The economics: three numbers to memorise</h3>
<div class="table-responsive">
<table class="table table-sm align-middle">
    <thead>
        <tr class="text-secondary small text-uppercase">
            <th>Operation</th><th>Price vs. base input</th><th>Note</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Cache write (5-minute TTL)</td><td><strong>1.25×</strong></td>
            <td>25% premium on the first request that stores the prefix</td>
        </tr>
        <tr>
            <td>Cache write (1-hour TTL)</td><td><strong>2×</strong></td>
            <td>Longer-lived cache for less frequent traffic</td>
        </tr>
        <tr>
            <td>Cache read (hit)</td><td><strong>0.1×</strong></td>
            <td>~90% cheaper than resending the prefix uncached</td>
        </tr>
    </tbody>
</table>
</div>
<p>
The breakeven arithmetic follows directly: the write premium is 0.25× of the prefix, and each
hit saves 0.9×, so <strong>a single reuse within the TTL already repays the premium</strong>.
At high reuse the repeated prefix cost collapses toward one tenth. Caching also cuts latency:
cached tokens are not reprocessed, so time-to-first-token drops — often dramatically for
large prefixes.
</p>

<h3>The mechanics: exact prefix match</h3>
<p>
A cache hit requires the prefix up to the cache breakpoint to be <em>byte-identical</em> to a
previous request, in the same order — tools, then system prompt, then messages. Practical
consequences:
</p>
<ul>
    <li><strong>Stable content first, volatile content last.</strong> Tool definitions,
        system prompt, policy documents, and few-shot examples go at the top; per-user data,
        retrieved chunks, and the current question go after the breakpoint.</li>
    <li><strong>Anything that varies the prefix kills every hit.</strong> A timestamp or
        request ID interpolated into the system prompt, tools serialised in nondeterministic
        order, or a per-user greeting at the top means a 0% hit rate — while the bill quietly
        shows 1.25× on every request, because each one is a fresh write.</li>
    <li><strong>Multi-turn is the sweet spot.</strong> In conversations and agent loops the
        entire growing history is the stable prefix; each turn re-reads the past at 0.1× and
        writes only the increment.</li>
</ul>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">Monitor the hit rate, not the hope</div>
    <p>The API response reports cache-read and cache-write token counts. A production
    dashboard should track hit rate per endpoint; a healthy stable-prefix workload runs high,
    and a sudden drop after a deploy is a one-cause symptom — something changed the prefix.
    Cache effectiveness is an observable, not an assumption.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Two reliable stems: (1) arithmetic — given a prefix size and reuse count, compute the
    saving with write 1.25×/read 0.1×; the answer lands near "~90% off the repeated prefix".
    (2) diagnosis — "we enabled caching and costs went <em>up</em>": look for the prefix-buster
    (timestamp, reordered tools, user data at the top), which turns every request into a
    1.25× write with no reads. Distractors blame the TTL or the model tier.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The policy knowledge assistant sends a stable 10,000-token prefix (system prompt + tools + policy summary) on every request, and handles 100 requests within each 5-minute cache window. Roughly what does caching make the prefix cost, relative to sending it uncached 100 times?',
                'options' => [
                    'About 11% — one write at 1.25× (12.5k token-equivalents) plus 99 reads at 0.1× (99k) ≈ 111.5k vs. 1,000k uncached',
                    'About 50% — caching halves input costs by design',
                    'About 25% — the 1.25× write premium dominates at this volume',
                    'About 90% — reads are only slightly discounted, so savings are modest',
                ],
                'answer' => 0,
                'explain' => 'One write costs 10k × 1.25 = 12.5k token-equivalents; the 99 subsequent hits cost 10k × 0.1 = 1k each, 99k total. That is ≈111.5k against 1,000k uncached — about 11%, i.e. a ~89% saving on the repeated prefix. B confuses caching with the Batch API\'s 50% discount; C misreads the write premium as recurring (it applies once per TTL window); D inverts the read multiplier — reads are 0.1×, not 0.9×.',
            ],
            [
                'q' => 'After a routine deploy, the support deflection agent\'s cache hit rate falls from 94% to ~0% and input spend jumps ~25%. The prompt "didn\'t change materially" — the deploy only added a line rendering the current date and time at the top of the system prompt. What happened?',
                'options' => [
                    'The 5-minute TTL is too short for the traffic pattern and should be raised to 1 hour',
                    'The timestamp varies per request, so the prefix is never byte-identical: every request is now a 1.25× cache write with no reads',
                    'The cache was invalidated once by the deploy and will repopulate within a day',
                    'Adding tokens to the system prompt pushed it over the maximum cacheable length',
                ],
                'answer' => 1,
                'explain' => 'Caching matches on an exact prefix; a per-request timestamp at the top means no two requests share a prefix, so nothing is ever read back — and each request still pays the 1.25× write premium, which is exactly the observed ~25% cost increase. The TTL (A) is irrelevant when no entry is ever reused; a deploy-time flush (C) would recover in minutes, not persist; and there is no "too long to cache" failure that behaves this way (D). The fix: render volatile values after the stable prefix, or drop them.',
            ],
            [
                'q' => 'Which request layout best exploits prompt caching for the research report agent, whose calls share tool definitions and a style guide but differ in the user\'s research question and freshly retrieved sources?',
                'options' => [
                    'Retrieved sources first (they are the largest block), then tools, style guide, and question',
                    'Alphabetise all blocks so the serialisation is deterministic',
                    'Put everything before a single breakpoint at the very end of the request',
                    'Tool definitions and style guide first with a cache breakpoint after them, then retrieved sources and the research question',
                ],
                'answer' => 3,
                'explain' => 'The rule is stable-first, volatile-last: the shared tools and style guide form a byte-identical prefix that hits on every call, while per-call sources and the question sit after the breakpoint where their variation costs nothing. Leading with retrieved sources puts the most volatile content at the top, guaranteeing misses. Alphabetising helps determinism, but ordering by stability is the operative principle. A breakpoint after everything tries to cache the volatile content too — the prefix would differ on every request.',
            ],
        ],
    ],

    // =====================================================================
    'ev-6-03' => [
        'body' => <<<'HTML'
<p>
The Claude family is a deliberate ladder — Haiku-class models for speed and volume,
Sonnet-class as the workhorse, Opus-class for the hardest reasoning — and Anthropic's
guidance is to treat tier choice as a routing decision made per task type, proven by evals,
not a single global setting chosen by reputation.
</p>

<h3>Route by demonstrated difficulty, escalate on doubt</h3>
<p>
The pattern the exam credits: classify or route incoming work, send the high-volume simple
cases (the claims triage assistant's routine categorisations, short summaries, formatting) to
the small fast tier, reserve the frontier tier for tasks that demonstrably need it, and
<strong>escalate on failure or low confidence</strong> — a Haiku-class first pass that hands
hard cases to a stronger model captures most of the savings with a measured quality floor.
The evidence for "Haiku is good enough here" is a per-tier run of the same eval suite: if the
small tier clears the gate on a task slice, the saving is free; if it misses, the gap is
quantified rather than guessed.
</p>

<h3>Extended thinking: buy accuracy where reasoning is the bottleneck</h3>
<p>
Extended thinking lets the model spend visible reasoning tokens before answering. It
reliably helps on genuinely hard reasoning — multi-step analysis, tricky diagnosis, maths —
and does nothing but add latency and cost on extraction or formatting tasks. The budget is
tunable: treat it as a dial set per task type, with the eval suite measuring the accuracy
delta each budget level actually buys. "Thinking everywhere, at maximum" is a cost bug;
"thinking nowhere" leaves accuracy on the table for the hard slice.
</p>

<h3>The Batch API: 50% off for patience</h3>
<p>
The Message Batches API processes asynchronous jobs at a <strong>50% discount</strong> on
both input and output tokens, with results returned <strong>within 24 hours</strong> (usually
much sooner). The fit test is simple: does anything wait on the answer in real time? The
contract clause extractor's nightly run over the day's agreements is the canonical fit —
thousands of independent tasks, nobody watching. Interactive chat, agent loops where each
step feeds the next, and anything with a latency SLO are non-fits regardless of the saving.
</p>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">Levers compound</div>
    <p>Tier, batch, and caching multiply: a workload moved from a frontier tier to an
    eval-proven Sonnet-class model, submitted through the Batch API, with its stable prefix
    cached, stacks all three discounts. When a stem asks for the largest saving "without
    quality risk", combine the no-risk levers (batch, caching) first; the tier lever joins
    only with eval evidence attached.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Match the workload signature: "high-volume, simple, latency-sensitive" → small tier;
    "hardest reasoning, low volume" → frontier tier, possibly with extended thinking;
    "large, overnight, nobody waiting" → Batch API. Distractors propose Opus-everywhere "to
    be safe" (unmeasured cost) or batch for interactive flows (breaks the latency contract).
    Any tier answer without eval evidence is suspect: the credited option usually contains
    the phrase "and verify against the eval suite".</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The contract clause extractor processes ~5,000 contracts every night; results are reviewed by lawyers the next morning. It currently calls a frontier-tier model synchronously. Which change captures the largest saving with the least quality risk?',
                'options' => [
                    'Switch to a Haiku-class model immediately, since extraction is a simple task',
                    'Move the nightly run to the Message Batches API for a 50% discount — the workload has no real-time consumer — and separately trial a smaller tier gated on the recall eval',
                    'Keep the synchronous frontier calls but enable extended thinking to reduce retries',
                    'Split contracts across two regions to parallelise the nightly run',
                ],
                'answer' => 1,
                'explain' => 'Batch is the textbook fit — thousands of independent tasks, an overnight window, morning consumption — and its 50% discount carries zero quality risk because the same model produces the same outputs. The immediate tier downgrade may also be justified, but only after the recall-gated eval proves it; "extraction is simple" is a guess on a precision-critical legal workload. Extended thinking adds cost with no evidence of a reasoning bottleneck, and regional parallelism changes speed, not spend.',
            ],
            [
                'q' => 'The claims triage assistant handles 40k claims/day. Analysis shows ~85% are routine categorisations a small model handles well, while ~15% (fraud indicators, multi-policy claims) need stronger reasoning. Which architecture does the exam credit?',
                'options' => [
                    'Frontier tier for all claims — routing errors are too costly to risk a smaller model anywhere',
                    'Small tier for all claims — the 85% majority proves it is mostly sufficient',
                    'Route to a Haiku-class tier by default and escalate flagged or low-confidence cases to a stronger tier, with each tier\'s slice cleared against the routing eval suite',
                    'Alternate tiers on alternate days and compare weekly cost reports',
                ],
                'answer' => 2,
                'explain' => 'Tiered routing with escalation captures the small-tier saving on the high-volume easy slice while preserving frontier-quality reasoning where the eval evidence says it is needed — and the per-tier eval gate is what makes the split defensible rather than hopeful. Opus-everywhere (A) pays frontier prices for 85% of traffic that measurably does not need it; small-everywhere (B) accepts unquantified failures exactly on the costliest 15%; (D) is an uncontrolled experiment with a lagging metric.',
            ],
            [
                'q' => 'A team enables maximum extended thinking on every call the support deflection agent makes, reasoning that "more thinking can only help accuracy." Latency and cost rise sharply; the eval suite shows resolution rate unchanged. What is the correct reading?',
                'options' => [
                    'The thinking budget is still too low — raise it until resolution improves',
                    'Extended thinking traded accuracy for speed, and the eval suite is too coarse to see it',
                    'The eval suite must be wrong, since more reasoning necessarily improves outcomes',
                    'Thinking budgets should be set per task type and sized by the measured accuracy delta; on this workload the delta is ~zero, so the budget is pure latency and cost',
                ],
                'answer' => 3,
                'explain' => 'Extended thinking buys accuracy only where reasoning depth is the bottleneck; most support-deflection turns are retrieval and policy application, not multi-step reasoning, so the measured delta of zero is a real result — and the eval suite doing its job. The lever should be scoped to the hard slice (if one exists) at a budget the evals justify. "Raise the budget further" and "the suite must be wrong" both treat more-thinking-helps as an axiom that overrides measurement, which is precisely the reasoning pattern this domain penalises; the traded-accuracy-for-speed reading invents an unmeasured effect to rescue the same axiom.',
            ],
        ],
    ],

    // =====================================================================
    'ev-6-04' => [
        'body' => <<<'HTML'
<p>
Token hygiene is the discipline of sending the model only what it needs and asking for only
what you need back. It is the "eliminate work" rung of the lever hierarchy, and it is doubly
valuable because bloat costs three ways at once: money (every token is billed), latency
(output tokens especially), and quality — long, cluttered contexts measurably degrade
instruction-following as attention spreads over irrelevant material.
</p>

<h3>Input-side hygiene</h3>
<ul>
    <li><strong>Prune boilerplate.</strong> Legacy caveats, duplicated instructions, and
        never-used few-shot examples accrete in prompts. Audit the prompt like code.</li>
    <li><strong>Deduplicate context.</strong> RAG pipelines routinely retrieve overlapping
        chunks; the policy knowledge assistant sending five near-identical policy excerpts
        pays five times for one fact — and gives contradiction a chance to creep in.</li>
    <li><strong>Retrieve less, better.</strong> Raising retrieval k "for safety" pads the
        context with weaker matches. Precision of the retrieved set is a quality lever, not
        just a cost one (Module 5 covers the failure side).</li>
    <li><strong>Summarise long histories.</strong> Multi-turn conversations and agent loops
        should compact old turns rather than replay them verbatim forever.</li>
    <li><strong>Trim tool definitions.</strong> An agent given 40 tools it never calls pays
        for their schemas on every request and picks among them less reliably.</li>
</ul>

<h3>Output-side hygiene</h3>
<p>
Output tokens are the expensive ones — priced several times higher than input on every tier —
and generation time scales with every token produced, so verbosity is simultaneously the
biggest cost-per-task and total-latency lever. The controls:
</p>
<ul>
    <li><strong>Ask for concise output in the prompt.</strong> Specify the format and length
        you want ("return only the JSON object", "three-sentence summary"). Prompt-level
        instruction is the steering wheel.</li>
    <li><strong>Use structured formats without redundancy.</strong> A schema with terse keys
        and no prose wrapper beats a chatty narrative that must be parsed anyway.</li>
    <li><strong>Treat <code>max_tokens</code> as a backstop, not a control.</strong> It caps
        runaway generations; it does not make the model concise — it makes long answers
        <em>truncated</em>, which is worse than long.</li>
    <li><strong>Never make the model repeat its input.</strong> "Restate the contract, then
        analyse it" doubles output spend for zero information.</li>
</ul>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">Hygiene is a quality lever too</div>
    <p>Teams frame token hygiene as penny-pinching and defer it. But instruction-following
    and retrieval-grounding measurably degrade as context bloats — the model attends to the
    padding as well as the signal. A leaner prompt is often <em>both</em> cheaper and more
    accurate, which is why hygiene sits above riskier levers in the hierarchy.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Know that output tokens out-price input tokens and dominate generation latency — stems
    that ask for "the biggest single lever" on a verbose system credit shortening the output.
    The <code>max_tokens</code> distractor recurs: any option using it to "control verbosity"
    is wrong because it truncates rather than condenses. And "tokens per task" as a tracked,
    first-class metric is the credited monitoring answer over raw monthly spend.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The research report agent\'s briefings average 9,000 output tokens; total generation latency and cost per report are both over budget, and reviewers say the reports "bury the findings in throat-clearing." What is the highest-leverage single change?',
                'options' => [
                    'Set max_tokens to 3,000 to enforce the budget mechanically',
                    'Prompt for a tight, structured format — findings first, fixed sections, target length — cutting output tokens, which drive both the latency and the cost overrun',
                    'Move report generation to a faster model tier to shrink generation time',
                    'Cache the report template as a stable prefix to reduce input costs',
                ],
                'answer' => 1,
                'explain' => 'Output tokens are the priciest tokens and generation time scales with each one produced, so a verbose-output system\'s biggest lever is producing less output — and here the reviewers confirm the extra tokens are padding, so quality improves too. max_tokens (A) would truncate reports mid-thought rather than condense them. A faster tier (C) attacks per-token speed while leaving 9,000 unnecessary tokens in place. Caching (D) helps input cost, which is not where this budget is bleeding.',
            ],
            [
                'q' => 'To control a chatty support agent, an engineer sets max_tokens to 150. Cost falls, but a new failure appears: answers stop mid-sentence, and multi-step instructions to customers arrive with the final steps missing. What principle was violated?',
                'options' => [
                    'max_tokens is a backstop against runaway generation, not a verbosity control — conciseness must come from prompt instructions, with the cap set safely above the intended length',
                    'max_tokens should be set per conversation rather than per request',
                    'The agent should have been moved to a smaller tier instead, which is naturally more concise',
                    'Truncation is acceptable if a retry loop regenerates any cut-off answer',
                ],
                'answer' => 0,
                'explain' => 'A hard cap does not change what the model plans to say — it amputates it, and a truncated instruction list is a worse failure than a wordy one. The credited pattern is prompt-level length and format guidance ("answer in at most three short steps") with max_tokens left as a generous safety net. B rearranges the same mistake; C attributes conciseness to tier, which is not a tier property; D pays for the answer twice and still races the same cap.',
            ],
            [
                'q' => 'The policy knowledge assistant retrieves k=20 chunks per question "to be safe"; inspection shows heavy overlap and many weak matches, and groundedness scores have been slipping. Which reading is correct?',
                'options' => [
                    'High k is harmless to quality — the model ignores irrelevant chunks — so this is purely a cost decision',
                    'The fix is compressing each chunk with a summarisation model before inclusion, keeping k=20',
                    'Padding the context with redundant and weak chunks raises cost <em>and</em> degrades quality, since attention spreads over near-miss material that invites unsupported answers; retrieve fewer, better chunks and deduplicate',
                    'Raise k further so the correct chunk is more likely to be present somewhere',
                ],
                'answer' => 2,
                'explain' => 'This is the "hygiene is a quality lever" point: bloated, overlapping context both bills more input tokens and measurably weakens grounding — near-miss chunks are raw material for confident-but-wrong answers, which matches the slipping groundedness scores. "High k is harmless" repeats the myth the evidence in the stem contradicts. Per-chunk compression adds a whole extra model call to preserve a padding habit. Raising k doubles down: recall of the right chunk was not the observed problem, precision of the set was.',
            ],
        ],
    ],

    // =====================================================================
    'ev-6-05' => [
        'body' => <<<'HTML'
<p>
Latency work starts with Module 1's vocabulary: know whether the complaint is about
<strong>time-to-first-token</strong> (perceived responsiveness) or <strong>total time</strong>
(throughput, agent-step completion), and measure percentiles, never means. Then pick the
lever that moves the milestone that is actually hurting — each lever below moves a different
one, and each has a price.
</p>

<h3>The lever board</h3>
<div class="table-responsive">
<table class="table table-sm align-middle">
    <thead>
        <tr class="text-secondary small text-uppercase">
            <th>Lever</th><th>Moves</th><th>Trades</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Streaming</td><td>Perceived latency (user sees tokens at TTFT)</td>
            <td>Nothing — total time unchanged; UI must handle partial output</td>
        </tr>
        <tr>
            <td>Shorter outputs</td><td>Total time — the biggest lever, generation is per-token</td>
            <td>Completeness, if cut by truncation instead of prompting</td>
        </tr>
        <tr>
            <td>Prompt caching</td><td>TTFT — cached prefix tokens are not reprocessed</td>
            <td>Essentially nothing (write premium; prefix discipline)</td>
        </tr>
        <tr>
            <td>Smaller/faster tier</td><td>Both TTFT and per-token generation speed</td>
            <td>Accuracy risk — requires the eval gate</td>
        </tr>
        <tr>
            <td>Parallelising independent calls</td><td>Wall-clock for fan-out work</td>
            <td>Cost unchanged or higher; only valid where no dependency exists</td>
        </tr>
        <tr>
            <td>Fewer agent round trips</td><td>Total time for agent tasks</td>
            <td>Engineering effort — batch tool calls, merge steps, cut redundant hops</td>
        </tr>
    </tbody>
</table>
</div>

<h3>Agent loops multiply everything</h3>
<p>
An agent task's latency is the <em>sum over steps</em> of model time plus tool time, so
round-trip count often dominates: a support flow taking six sequential model-tool exchanges
pays six TTFTs and six tool waits. The levers specific to loops: issue independent tool calls
in parallel in a single turn, merge steps that always co-occur, cache the growing history
(each turn re-reads the past at a discount and reduced processing time), and cut steps whose
output the next step never uses. Perceived latency also has UX levers — streaming
intermediate status ("searching the policy base…") keeps a multi-second agent feeling alive.
</p>

<h3>Budget per stage, measure percentiles</h3>
<p>
A latency SLO like "p90 &lt; 4 s end-to-end" should decompose into stage budgets — retrieval
300 ms, first model call p90 1.5 s, tool round trip 500 ms — so a breach points at the stage
that blew its budget rather than triggering a general hunt. Percentile dashboards per stage
are the observability side of this (Module 7); the optimisation side is that levers get aimed
at the stage that is actually over budget.
</p>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">Cheapest lever first, here too</div>
    <p>Streaming and caching improve felt latency with no quality trade at all — they come
    first. Output-shortening via prompting is next: small quality risk, big total-time win.
    The tier downgrade is last for the same reason it is last on cost: it is the lever that
    can silently spend accuracy, so it moves only with eval evidence.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Match the complaint to the milestone: "feels frozen, then the answer appears at once"
    → streaming (and caching for TTFT); "the answer takes too long to finish" → shorter
    outputs, then tier; "the agent takes 30 s per task" → count round trips and parallelise
    independent calls. Distractors offer levers that move the wrong milestone — a tier
    upgrade for a perceived-latency complaint, or streaming for a batch pipeline where no
    human is watching.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'Users say the support deflection agent "freezes for ages, then the whole answer appears at once." Total generation time is within budget; the product owner proposes a faster model tier. What should the architect do instead?',
                'options' => [
                    'Accept the tier change — faster generation shortens the freeze',
                    'Reduce max_tokens so the frozen period is shorter',
                    'Precompute answers to the most common questions overnight',
                    'Enable streaming so tokens render from time-to-first-token, and cache the stable prompt prefix to pull TTFT down — the complaint is perceived latency, and neither lever risks quality',
                ],
                'answer' => 3,
                'explain' => 'The symptom — silence, then a complete answer — is the signature of a non-streaming UI, a perceived-latency problem. Streaming makes the wait feel like progress from the first token, and prompt caching genuinely shortens TTFT by skipping prefix reprocessing; both are quality-risk-free, so they precede any tier change. The tier swap spends accuracy risk on a milestone already within budget; shrinking max_tokens truncates answers to shorten a wait users would no longer notice; overnight precomputation rebuilds the product to dodge a UI fix.',
            ],
            [
                'q' => 'A research report agent task takes ~40 s: profiling shows eight sequential steps, and four of them — fetching four independent sources — happen one after another at ~5 s each. Which change most reduces wall-clock time?',
                'options' => [
                    'Issue the four independent source fetches as parallel tool calls in a single turn, collapsing ~20 s of sequential waiting to ~5 s',
                    'Move the whole task to the Batch API, which processes steps concurrently',
                    'Enable extended thinking so the agent plans a shorter route through the steps',
                    'Stream the final report so the user perceives less waiting',
                ],
                'answer' => 0,
                'explain' => 'Four fetches with no dependencies are the textbook parallelisation target: same work, same cost, ~15 s of wall-clock removed. Batch (B) is the opposite trade — it is for workloads with no latency requirement at all and offers up-to-24h turnaround, not intra-task concurrency. Extended thinking (C) adds reasoning tokens and latency on the hope of a shorter plan. Streaming (D) improves how the wait feels but this stem asks for wall-clock reduction, and 35 s of agent work precedes any streamable output.',
            ],
            [
                'q' => 'To hit a p90 latency target, a team truncates the claims triage assistant\'s explanations aggressively and celebrates the green dashboard. Adjusters soon report routing decisions arriving with the justification cut off, forcing manual re-review. Which principle did the team miss?',
                'options' => [
                    'Latency targets should use p50, which the team would have passed without truncation',
                    'Explanations should be generated by a second, slower model call after routing',
                    'Every latency lever has a named trade — truncation trades completeness — and the trade must be checked by a quality gate, not just the latency dashboard',
                    'The p90 target itself was unrealistic for an LLM workload',
                ],
                'answer' => 2,
                'explain' => 'The team pulled a lever (hard truncation) and monitored only the axis it improves; the axis it spends — completeness of the justification, which adjusters depend on — had no gate, so the failure surfaced as downstream manual work. The credited discipline: name the trade when choosing the lever, and let the eval/quality gate confirm the trade is acceptable (prompt-shortened explanations may well pass it; amputated ones do not). A shops for a friendlier statistic instead of fixing the harm; B adds cost and a second wait to work around a self-inflicted cut; D declares defeat on a target the system could meet with a better lever.',
            ],
        ],
    ],
];
