<?php
/**
 * module-5.php — study content for Module 5: Diagnosis and Root-Cause Analysis.
 *
 * Grounded in Anthropic's published guidance: tool-use best practices
 * (docs.claude.com), RAG and citations guidance, and the engineering posts
 * "Building effective agents" and "Demystifying evals for AI agents".
 *
 * Returns: [ topic-id => ['body' => html, 'questions' => [...] ] ]
 */
return [

    // =====================================================================
    'ev-5-01' => [
        'body' => <<<'HTML'
<p>
When quality drops, the pressure is to fix something — anything — fast. The discipline the exam
rewards is the opposite: slow down and trace the symptom to the component that actually caused
it. Anthropic's guidance is consistent across its evaluation and agent-building material:
<strong>look at your data first</strong>. Read real transcripts and traces before forming a
theory. Teams that skip this step end up rewriting prompts to fix what turns out to be a stale
retrieval index, or swapping models to fix a truncation bug in their own post-processing.
</p>

<h3>Binary-search the pipeline</h3>
<p>
A Claude system is a pipeline: inputs are assembled into context, the model generates, and the
output is parsed, post-processed, and acted on. Diagnosis is a binary search over that pipeline
with three load-bearing questions:
</p>
<ul>
    <li><strong>Is the right context reaching the model?</strong> Inspect the assembled prompt
        for a failing case — the retrieved chunks, tool results, conversation history, and
        system prompt exactly as sent. A surprising fraction of "model failures" die here:
        the model never saw the information it is blamed for ignoring.</li>
    <li><strong>Is the model using the context correctly?</strong> If the context is right and
        the raw completion is wrong, the failure is in prompting or model behaviour — now
        prompt-vs-model discrimination (ev-5-02) applies.</li>
    <li><strong>Is the output handled correctly downstream?</strong> A correct completion can
        be mangled by parsing, truncation, or a formatting change. Compare the raw model output
        to what the user finally saw.</li>
</ul>

<h3>What changed, and one variable at a time</h3>
<p>
Production quality rarely decays spontaneously; it drops after a change — a prompt edit, a
model version bump, a document refresh, a new tool, a dependency upgrade. The most recently
changed component is the first suspect, so keep a change log you can line up against the metric
timeline. But correlation is not causation: traffic itself changes. A new customer segment, a
seasonal shift in question types, or a marketing launch can move metrics with zero system
changes. Segment the metric by input type before blaming a deploy that merely coincided.
</p>
<p>
Once you have a suspect, <strong>reproduce the failure in the eval harness before fixing it</strong>.
Pull failing production cases into a small repro set, confirm the failure reproduces, then
change one variable at a time and re-run. A fix applied without a repro is a guess; if the
metric later recovers you will not know whether your fix worked or the traffic shifted back.
The repro cases then join the regression suite so the failure stays fixed (Module 2's living
suite).
</p>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">The trap: fixing the easiest component, not the causal one</div>
    <p>The prompt is the easiest thing to edit, so it absorbs blame for everything. When the
    claims triage assistant started mis-routing after a queue was renamed upstream, the team
    spent a week on prompt variants; the actual cause was the routing tool's enum still
    carrying the old queue name. If you have not looked at the assembled context and the raw
    output for a failing case, you are not diagnosing — you are guessing.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Stems open with "quality dropped after X" and offer four responses. Eliminate options
    that act before observing (rewrite the prompt, switch models, retrain embeddings) and
    options that observe the wrong layer. The credited answer usually starts with "examine the
    traces / retrieved context / raw outputs for failing cases" or "reproduce the failure in
    the eval harness" — evidence before intervention, one variable at a time.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'Two weeks after the policy knowledge assistant\'s quarterly document refresh, answer-correctness scores drop 12 points. The team lead proposes immediately upgrading to a stronger model tier. What should the architect do first?',
                'options' => [
                    'Approve the upgrade — a stronger model is the fastest way to recover quality',
                    'Roll back the document refresh, since it is the most recent change',
                    'Pull failing transcripts and inspect what was actually retrieved and sent to the model for those queries',
                    'Rewrite the system prompt to emphasise accuracy and grounding',
                ],
                'answer' => 2,
                'explain' => 'The drop coincides with a document refresh, which makes the retrieval layer the prime suspect — but the first move is evidence, not action: read the traces and see whether the right chunks reached the model. A and D intervene on components with no evidence against them. B acts on the right suspicion but destroys the business value of the refresh without confirming causation; inspecting traces confirms or clears retrieval in minutes.',
            ],
            [
                'q' => 'The support deflection agent\'s resolution rate fell from 71% to 63% over a month with no deploys, no prompt changes, and no model version change. Which explanation should be investigated first?',
                'options' => [
                    'The model provider silently degraded the model',
                    'Random variance; wait another month before concluding anything',
                    'The eval suite has gone stale and no longer reflects production',
                    'The traffic mix changed — segment the metric by question category and customer cohort to see whether harder or novel question types are arriving',
                ],
                'answer' => 3,
                'explain' => 'When nothing in the system changed, the input distribution is the leading suspect — new products, seasonal issues, or a new customer segment shift the difficulty of incoming questions. Segmenting the metric tests this cheaply and immediately. A is unfalsifiable without first ruling out traffic; B ignores a sustained trend; C concerns offline evals, but the observed drop is in the online production metric, so suite staleness cannot explain it.',
            ],
            [
                'q' => 'An architect confirms via traces that the contract clause extractor receives the correct contract text but the raw model output misses termination clauses that appear verbatim in the input. A prompt fix is drafted. What is the correct next step before shipping it?',
                'options' => [
                    'Add the failing contracts to a repro set, confirm the failure reproduces in the harness, apply the fix alone, and re-run to show those cases now pass without regressing the rest of the suite',
                    'Ship it behind a feature flag and watch production metrics',
                    'Apply the prompt fix together with a model-tier upgrade to maximise the chance of recovery',
                    'Have three lawyers review the new prompt wording for legal accuracy',
                ],
                'answer' => 0,
                'explain' => 'Reproduce-then-fix is the core method: without a repro you cannot attribute recovery to the fix, and without re-running the full suite you cannot see what the fix broke. C changes two variables at once, so even success teaches nothing about cause. B tests in production what could be tested offline first. D reviews wording but never establishes that the change fixes the observed failure.',
            ],
        ],
    ],

    // =====================================================================
    'ev-5-02' => [
        'body' => <<<'HTML'
<p>
Once traces show that correct context reached the model and the raw output is still wrong, the
diagnosis narrows to two hypotheses the exam loves to make you distinguish: the
<strong>prompt failed</strong>, or the <strong>model is mismatched</strong> to the task. They
have different fixes and different costs, so confusing them wastes either an engineering sprint
or a model upgrade budget.
</p>

<h3>Prompt failure: the model did what you asked — the ask was wrong</h3>
<p>
Prompt failures come from instructions that are ambiguous ("be concise" — how concise?),
conflicting ("always cite sources" and "never quote documents"), buried (a critical rule
in the middle of a 4,000-token system prompt), or simply absent (the model was never told what
to do with an empty search result). The distinguishing evidence: the model's behaviour is
<em>consistent with a reasonable reading</em> of the prompt, just not the reading you intended.
Fixes are prompt engineering per Anthropic's guidance: explicit, unambiguous instructions;
well-chosen examples; structure (XML tags, ordered sections); and stating what to do in edge
cases rather than assuming.
</p>

<h3>Model mismatch: the ask was fine — the model can't deliver it</h3>
<p>
Model mismatch appears in two forms. First, the task exceeds the tier's capability: multi-step
legal reasoning routed to a small fast model chosen for cost will fail no matter how polished
the prompt. Second, a model version change shifted behaviour: the same prompt that worked on
the old version follows instructions differently on the new one. The distinguishing evidence
is that clarifying the prompt does not move the failure, and errors look like capability
limits — dropped constraints on complex instructions, shallow reasoning on deep problems —
rather than misreadings.
</p>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">The cheap discriminating test</div>
    <p>Run the identical prompt on a stronger model tier against the failing cases. If the
    stronger model succeeds, you have model mismatch — then decide between upgrading the tier
    and simplifying the task (decomposition, pre-processing). If the stronger model fails the
    same way, the prompt is the problem, and upgrading would burn money to keep the same bug.
    One experiment, one variable, direct evidence.</p>
</div>

<h3>The long-context confounder</h3>
<p>
A third pattern mimics both: instruction-following degrades as context grows very large. A rule
the model follows reliably in a 2K-token prompt gets dropped when the same rule sits inside
100K tokens of retrieved documents and conversation history. The tell is that failures
correlate with context length, not with task difficulty or instruction wording. Fixes are
architectural — restate critical instructions near the end of the context, trim or summarise
history, move rules out of the haystack — not a model upgrade and not ordinary rewording.
</p>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Given a failure description, classify it: behaviour consistent with a misreadable
    instruction → prompt failure; polished prompt failing on genuinely hard reasoning,
    especially on a small/fast tier → model mismatch; failures that appear only on long inputs
    → context-length degradation. The credited diagnostic action is almost always "same prompt,
    stronger model, failing cases" — distractors either upgrade without testing or iterate
    prompts without a hypothesis.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The claims triage assistant, running on a small fast model tier for cost, mis-handles claims that involve multiple policies with conflicting coverage dates — cases adjusters describe as "genuinely tricky". Simple claims route correctly. The prompt already contains clear instructions and worked examples for multi-policy cases. What is the most likely diagnosis and confirming test?',
                'options' => [
                    'Prompt failure — add more multi-policy examples and re-run',
                    'Model mismatch — run the same prompt on a stronger tier against the failing claims; success there confirms the small model lacks the reasoning capacity for these cases',
                    'Retrieval failure — check whether the correct policy documents were fetched',
                    'Context-length degradation — trim the conversation history',
                ],
                'answer' => 1,
                'explain' => 'The failure profile — hard-reasoning cases fail while simple ones succeed, under an already-clear prompt on a deliberately small tier — is the signature of model mismatch, and the same-prompt-stronger-model test confirms it with one variable changed. A iterates on a component that shows no evidence of ambiguity. C and D invent components: the scenario says nothing about retrieval or long contexts, and simple claims succeeding through the same pipeline weakens both.',
            ],
            [
                'q' => 'After a prompt instructing "answer in under 100 words" and, later, "always include the full relevant policy excerpt", the policy knowledge assistant sometimes truncates excerpts and sometimes blows past the word limit. Runs on a stronger model tier show the same erratic behaviour. What is the diagnosis?',
                'options' => [
                    'Model mismatch — neither tier can handle the task',
                    'Hallucination — the model is fabricating excerpts to fit the limit',
                    'Sampling variance — set temperature to zero',
                    'Prompt failure — the two instructions conflict, so any model must violate one; the fix is resolving the conflict (e.g. word limit excludes excerpts), not more capability',
                ],
                'answer' => 3,
                'explain' => 'The stronger tier failing identically is the discriminating evidence: capability is not the constraint, the instructions are unsatisfiable together. A misreads that same evidence. B mislabels the failure — the excerpts are truncated, not invented. C would at most make the violations consistent rather than erratic; the conflict remains, so some instruction is still violated every time.',
            ],
            [
                'q' => 'The research report agent follows its citation-format rule perfectly in short test runs but drops it in production runs where 150K tokens of gathered sources fill the context. Which fix targets the actual mechanism?',
                'options' => [
                    'Upgrade to the strongest available model tier',
                    'Add "IMPORTANT: follow the citation format" at the top of the system prompt',
                    'Restate the citation rule immediately before the drafting step and summarise or prune gathered sources so critical instructions are not buried in a huge context',
                    'Fine-tune the model on correctly cited reports',
                ],
                'answer' => 2,
                'explain' => 'Failures that correlate with context length — working at 2K, failing at 150K — indicate instruction-following degradation in long contexts, and the remedy is architectural: keep critical rules near the point of use and shrink the haystack. A spends money on a mechanism upgrades don\'t reliably fix; B strengthens wording in the position already being lost; D is a heavyweight response to a placement problem the harness can fix today.',
            ],
        ],
    ],

    // =====================================================================
    'ev-5-03' => [
        'body' => <<<'HTML'
<p>
A hallucination is a <strong>confident fabrication</strong>: the model states something false
with the same fluent assurance it uses for facts. That confidence is what makes the failure
dangerous — users and downstream systems cannot distinguish an invented policy clause from a
real one by tone. The exam treats hallucination as a diagnosable, mitigable failure mode with
known triggers, not as random noise.
</p>

<h3>When models fabricate</h3>
<ul>
    <li><strong>Missing context</strong> — the answer isn't in the provided material, but the
        question demands one. Fabrication fills the gap.</li>
    <li><strong>Questions beyond the material</strong> — the policy knowledge assistant is
        asked about a benefit the HR docs never mention; it composes a plausible-sounding
        policy from adjacent facts.</li>
    <li><strong>Pressure to answer</strong> — prompts that demand a definitive response
        ("always give the customer an answer") implicitly forbid the honest "I don't know",
        so the model complies with an invention.</li>
</ul>

<h3>Groundedness: the operational standard</h3>
<p>
The measurable form of "doesn't hallucinate" is <strong>groundedness</strong>: every factual
claim in the output is traceable to the provided context. This is deliberately stricter than
"true" — a claim can be correct from the model's training data yet ungrounded, and for
regulated or versioned domains (current HR policy, current contract terms) only the provided
documents count as truth. Groundedness turns hallucination from a vibe into a per-claim,
gradeable property.
</p>

<h3>Mitigations that work — and one that mostly doesn't</h3>
<ul>
    <li><strong>Explicitly permit "I don't know."</strong> Anthropic's guidance is direct:
        tell the model that admitting insufficient information is a correct answer. This
        removes the pressure that manufactures fabrications.</li>
    <li><strong>Require citations or direct quotes.</strong> Making the model quote the source
        passage for each claim forces the generation to stay attached to the context and makes
        violations mechanically checkable.</li>
    <li><strong>Retrieve, then answer.</strong> Have the model first extract the relevant
        passages, then compose the answer only from what it extracted — grounding becomes a
        structural property of the pipeline rather than a hope.</li>
    <li><strong>Lowering temperature helps little.</strong> Fabrication is not a sampling
        artefact; a model that lacks the answer will fabricate deterministically at
        temperature 0 — just the <em>same</em> fabrication every time. Grounding, not
        decoding settings, is the lever.</li>
</ul>

<h3>Measuring it</h3>
<p>
Two graders cover most needs. A <strong>groundedness judge</strong> decomposes the answer into
claims and checks each against the retrieved context — is it supported, contradicted, or
absent? A <strong>citation-validity check</strong> verifies mechanically that cited passages
exist in the sources and (via judge or string match) actually support the sentence citing
them. Track over-refusal alongside (Module 1): a system tuned to never fabricate by refusing
everything has traded one failure for another.
</p>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">Grounded is not the same as correct</div>
    <p>Groundedness checks the answer against the <em>provided</em> context. If retrieval
    fetched a stale policy, a perfectly grounded answer is still wrong. A clean groundedness
    score with wrong answers points the diagnosis upstream at retrieval — the subject of
    ev-5-04.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Credited mitigations are the grounding trio: permit "I don't know", require
    citations/quotes, restructure to retrieve-then-answer. The reliable distractor is
    "lower the temperature" — it addresses variance, not fabrication. And when a stem says
    answers are grounded but wrong, the tested move is to look at retrieval, not to add more
    anti-hallucination prompting.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The policy knowledge assistant confidently describes a "childcare travel stipend" that does not exist in any HR document. Traces show the retrieved chunks discuss travel expenses and childcare benefits separately, and the user\'s question asked specifically about combining them. Which mitigation most directly targets this failure?',
                'options' => [
                    'Instruct the model that when the provided documents do not answer the question, saying the policy does not address it is the correct response — and evaluate that behaviour',
                    'Reduce temperature to 0 so the model stops inventing benefits',
                    'Increase top-k so more documents are retrieved',
                    'Add a disclaimer to every answer that users should verify with HR',
                ],
                'answer' => 0,
                'explain' => 'The question falls outside the provided material, and the model filled the gap by fusing adjacent facts — the classic beyond-the-material fabrication. Licensing and rewarding "the documents don\'t address this" removes the pressure that produced the invention. B is the standard distractor: at temperature 0 the model fabricates the same stipend every time. C retrieves more of a corpus that does not contain the answer. D shifts the burden to users instead of fixing the behaviour.',
            ],
            [
                'q' => 'To measure hallucination in the research report agent, a team proposes: "an LLM judge rates each report\'s trustworthiness 1–10." What is the strongest improvement, per Anthropic\'s guidance?',
                'options' => [
                    'Use two judges and average their trustworthiness scores',
                    'Replace the judge with ROUGE overlap between the report and its sources',
                    'Decompose each report into individual factual claims and have the judge check each claim against the gathered sources as supported, contradicted, or absent — plus a mechanical check that cited passages exist',
                    'Have the judge also rate writing quality so trustworthiness is contextualised',
                ],
                'answer' => 2,
                'explain' => 'A holistic 1–10 "trustworthiness" score is exactly the vague, uncalibratable judge design the guidance warns against; per-claim verification against sources turns groundedness into discrete, auditable checks, and citation-existence is verifiable in code for free. A averages two vague numbers into one vague number. B measures n-gram overlap, which a fabricating paraphrase can score well on. D adds an unrelated axis to a metric that needs sharpening, not broadening.',
            ],
            [
                'q' => 'After adding a strict grounding prompt, the support deflection agent\'s fabrication rate drops to near zero — but escalations to human agents rise 30%, including many questions the docs clearly answer. What should the team conclude?',
                'options' => [
                    'The mitigation worked; rising escalations show appropriate caution',
                    'The pendulum overshot into over-refusal: the system now declines answerable questions, so refusal-correctness must be measured and the grounding instruction rebalanced',
                    'The documentation is inadequate and should be expanded',
                    'Escalation volume is a staffing metric, not an evaluation concern',
                ],
                'answer' => 1,
                'explain' => 'Escalating questions the docs clearly answer is a new failure mode, not caution — anti-hallucination tuning traded fabrication for over-refusal. The fix is to measure both directions (fabrication rate and refusal correctness) and tune against the pair, per Module 1\'s floors-on-every-axis discipline. A ignores the "clearly answerable" evidence; C blames content the scenario says is sufficient; D excludes a model behaviour from evaluation because it surfaces as an ops number.',
            ],
        ],
    ],

    // =====================================================================
    'ev-5-04' => [
        'body' => <<<'HTML'
<p>
The signature RAG incident — the one the exam returns to repeatedly — is
<strong>confident-but-wrong</strong>: the answer is fluent, well-structured, faithfully
grounded in the documents the model was given… and wrong, because retrieval handed it the
wrong, stale, or partial documents. Generation gets the blame; retrieval committed the crime.
The architectural insight is that a RAG system is two systems, and each needs its own metrics.
</p>

<h3>Separate the metrics, separate the diagnosis</h3>
<div class="table-responsive">
<table class="table table-sm align-middle">
    <thead>
        <tr class="text-secondary small text-uppercase">
            <th>Layer</th><th>Question</th><th>Metrics</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Retrieval</td>
            <td>Did the needed information reach the context?</td>
            <td>recall@k (is the answer-bearing chunk in the top k?), precision@k (how much of
                the context is relevant?), rank of the needed document</td>
        </tr>
        <tr>
            <td>Generation</td>
            <td>Given this context, is the answer right?</td>
            <td>Groundedness, answer correctness <em>given the provided context</em>,
                citation validity</td>
        </tr>
        <tr>
            <td>End-to-end</td>
            <td>Is the final answer actually correct?</td>
            <td>Answer correctness against ground truth, independent of what was retrieved</td>
        </tr>
    </tbody>
</table>
</div>
<p>
The diagnosis order follows the pipeline: <strong>check what was retrieved first</strong>. If
recall@k failed for the query, no amount of prompt or model work can fix the answer, and any
generation-side "fix" that appears to help is coincidence. Only when retrieval is confirmed
good does the investigation move to generation.
</p>

<h3>The retrieval failure catalogue</h3>
<ul>
    <li><strong>Stale index</strong> — documents were refreshed but the embedding index wasn't
        rebuilt, so the system retrieves and faithfully cites last quarter's policy. The
        classic post-refresh incident.</li>
    <li><strong>Chunking splits the answer</strong> — a rule and its exception land in
        different chunks; only one is retrieved, and the grounded answer is half the truth.</li>
    <li><strong>Embedding vocabulary mismatch</strong> — users say "getting let go", documents
        say "involuntary termination"; general-purpose embeddings may not bridge domain
        jargon, so the right document never ranks.</li>
    <li><strong>top-k too small</strong> — the needed chunk exists and ranks 7th with k=5.
        Raising k trades precision (and tokens) for recall.</li>
    <li><strong>Reranking errors</strong> — first-stage retrieval finds the document; the
        reranker demotes it out of the window.</li>
</ul>

<div class="ev-callout ev-callout-warn">
    <div class="ev-callout-title">The tell: groundedness passes while answers fail</div>
    <p>An answer grounded in retrieved-but-wrong documents sails through every groundedness
    check — each claim is genuinely supported by the (wrong) context. This is why a RAG eval
    needs end-to-end correctness against ground truth <em>in addition to</em> groundedness.
    The score signature "groundedness high, end-to-end correctness low" points at retrieval
    with near-certainty; both low points at generation or both layers.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Given a RAG symptom, name the layer before naming the fix. "Wrong after a document
    refresh" → stale index. "Answers miss exceptions and caveats" → chunking. "Fails on user
    phrasing but works on document phrasing" → embedding mismatch. Distractors offer
    generation-side fixes (better prompts, stronger models, anti-hallucination instructions)
    for retrieval-side failures — recognisable because the answer was grounded all along.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'After the quarterly policy refresh, the policy knowledge assistant cites the old parental-leave duration — with accurate quotes and valid citations to the document it retrieved. Groundedness scores are unchanged. Where is the fault, and what confirms it?',
                'options' => [
                    'Generation — the model memorised the old policy; confirm by lowering temperature',
                    'The prompt — add an instruction to always prefer the newest policy version',
                    'The groundedness judge — it should have caught the wrong answer',
                    'Retrieval — the embedding index was likely not rebuilt after the refresh, so the old document is still being served; confirm by inspecting which chunks were retrieved for the failing queries',
                ],
                'answer' => 3,
                'explain' => 'Accurate quotes and valid citations mean generation did its job on the context it received — the wrong document was retrieved, and a stale index after a refresh is the textbook cause; the retrieval trace settles it. A misattributes cited retrieved content to memorisation. B asks the model to fix an infrastructure defect it cannot see. C misunderstands groundedness: the answer IS grounded — in stale context — which is exactly why end-to-end correctness must be measured separately.',
            ],
            [
                'q' => 'A RAG eval for the policy knowledge assistant reports groundedness 96% but end-to-end answer correctness 71%. Which investigation does this score signature most strongly support?',
                'options' => [
                    'Audit the retrieval layer: measure recall@k on the failing queries to see whether the answer-bearing chunks reached the context',
                    'Redesign the generation prompt with stricter anti-hallucination instructions',
                    'Recalibrate the groundedness judge against human labels',
                    'Expand the eval set — the gap is probably sampling noise',
                ],
                'answer' => 0,
                'explain' => 'High groundedness with low correctness is the signature of retrieval failure: the model faithfully used context that did not contain (or wrongly stated) the answer. B strengthens a layer already performing well. C questions the judge without evidence — the judge finding answers grounded is consistent with the retrieval hypothesis. D waves away a 25-point gap that has a specific, checkable explanation.',
            ],
            [
                'q' => 'The policy knowledge assistant answers correctly when users echo document phrasing ("involuntary termination benefits") but retrieves irrelevant chunks when they ask naturally ("what do I get if I\'m let go?"). Which failure and fix pair is best supported?',
                'options' => [
                    'Model mismatch — upgrade the generation model tier',
                    'Chunking failure — increase chunk overlap',
                    'Embedding vocabulary mismatch — the query and document phrasing don\'t meet in embedding space; fix with query expansion/rewriting toward document vocabulary, or domain-tuned embeddings, and add natural-phrasing queries to the retrieval eval set',
                    'top-k too small — retrieve more chunks per query',
                ],
                'answer' => 2,
                'explain' => 'Success tracking the phrasing of the query — not the difficulty of the question — localises the failure to how queries embed against documents. Query rewriting or better-suited embeddings bridge the vocabulary gap, and the eval set should have caught this by including realistic phrasings. A blames generation, which succeeds whenever retrieval succeeds. B and D adjust retrieval parameters that don\'t address ranking driven by vocabulary distance; with a mismatch this severe, the right chunk may not rank in any reasonable k.',
            ],
        ],
    ],

    // =====================================================================
    'ev-5-05' => [
        'body' => <<<'HTML'
<p>
Agentic systems add three failure surfaces beyond prompt, model, and retrieval: the
<strong>tools</strong> the model calls, the <strong>context</strong> that accumulates across
steps, and the <strong>loop</strong> that strings steps together. Diagnosing them shares one
prerequisite Anthropic's agent guidance insists on: read the <em>full trace</em>, step by
step. A final-output-only view shows you that the research report was wrong; only the trace
shows you it went wrong at step 3 and spent forty more steps elaborating the error.
</p>

<h3>Tool failures</h3>
<ul>
    <li><strong>Bad descriptions.</strong> Anthropic's tool-use guidance is emphatic: tool
        names, descriptions, and parameter docs are prompts too — the model chooses tools by
        reading them. Vague descriptions ("search: searches things") or undocumented
        parameters produce wrong-tool choices and malformed arguments. The fix is writing
        descriptions as carefully as system prompts: what it does, when to use it, what each
        parameter means, what it returns.</li>
    <li><strong>Silent errors.</strong> A tool that returns an empty list, an error string, or
        a timeout message can be treated by the model as a legitimate answer ("no relevant
        policies exist"). Error results must be explicit and distinguishable, and the model
        instructed how to react — retry, try another tool, or report failure.</li>
    <li><strong>Schema mismatch.</strong> The model emits arguments the tool rejects, or the
        tool returns a shape the downstream prompt doesn't expect. Mechanically checkable;
        should be caught by validation, not discovered in transcripts.</li>
</ul>

<h3>Context failures</h3>
<ul>
    <li><strong>Truncation</strong> — long-running sessions silently drop early instructions
        or facts; behaviour degrades exactly when history is trimmed.</li>
    <li><strong>Stale context</strong> — an early observation ("the config file is at /etc/app")
        stays in context after the world changed; the agent keeps acting on it.</li>
    <li><strong>Context poisoning</strong> — one wrong tool result or hallucinated
        intermediate conclusion enters the context and every later step builds on it. The
        error compounds instead of correcting.</li>
    <li><strong>Lost in the middle</strong> — with very long contexts, material buried mid-context
        gets less reliable attention than material at the ends (ties to ev-5-02's
        long-context degradation).</li>
</ul>

<h3>Loop failures — and the pass^k connection</h3>
<p>
The loop itself fails in two symmetric ways: <strong>runaway loops</strong> (the agent retries
a failing approach indefinitely, burning tokens — bound it with step budgets and repeated-action
detection) and <strong>premature stopping</strong> (the agent declares success with the task
half-done — caught by outcome verification, not by asking the agent if it finished). Beneath
both sits the arithmetic from ev-1-03: a 20-step trajectory at 98% per-step reliability
completes cleanly only ~67% of the time (0.98<sup>20</sup>). Agent reliability is a
compounding property, which is why per-step error handling and recovery matter more than any
single step's polish.
</p>

<div class="ev-callout ev-callout-tip">
    <div class="ev-callout-title">Step-level trace reading, operationalised</div>
    <p>For each failing trajectory, find the <em>first</em> step where the agent's state
    diverged from reality — wrong tool, misread result, poisoned conclusion — and diagnose
    that step. Everything after it is usually consequence, not cause. Fixing step 3 often
    clears twenty downstream "failures" at once; fixing step 23 first fixes nothing.</p>
</div>

<div class="ev-callout ev-callout-exam">
    <div class="ev-callout-title">Exam lens</div>
    <p>Map symptom to surface: wrong tool or malformed arguments → tool descriptions; "no data
    found" answers when data exists → silent tool errors; degradation late in long sessions →
    truncation or lost-in-the-middle; one early error elaborated confidently → context
    poisoning; token bills with no completion → runaway loop. The credited diagnostic act is
    reading the full trace to find the first divergent step — distractors evaluate only the
    final output or bolt on a stronger model.</p>
</div>
HTML,
        'questions' => [
            [
                'q' => 'The research report agent frequently reports "no recent studies were found" on topics with abundant literature. Traces show its search tool returned <code>{"error": "rate_limited"}</code> on those calls, and the agent proceeded to draft the report anyway. Which fix targets the root cause?',
                'options' => [
                    'Upgrade the agent to a stronger model that knows the literature',
                    'Make tool errors explicit and instruct the agent how to handle them — retry with backoff, and never present a failed search as an empty result; add these error cases to the eval suite',
                    'Increase the search tool\'s rate limit',
                    'Add a final review step where the agent double-checks its own report',
                ],
                'answer' => 1,
                'explain' => 'The agent treated a silent-ish tool failure as a legitimate "no results" answer — the classic silent-error pattern. The durable fix is unmistakable error signalling plus instructed recovery behaviour, verified by evals that inject tool failures. A asks the model to compensate from memory for a broken tool, undermining groundedness. C reduces the frequency of one error without fixing the handling of any error. D reviews with the same context that contains the poisoned "no studies" conclusion.',
            ],
            [
                'q' => 'In a 40-step code-migration trajectory, the agent misread a test-runner output at step 6 as "all tests passing", then spent 34 steps building on the broken port and opened a confident pull request. The team proposes prompt fixes for the PR-writing step, where the wrong claims appear. What should the architect say?',
                'options' => [
                    'Diagnose at the first divergent step: the step-6 misreading poisoned all later context; fix the test-result parsing/presentation there, because every downstream step was consequence, not cause',
                    'Agree — the PR text is where the falsehood surfaces, so fix it there',
                    'Cap the loop at 20 steps so errors have less room to compound',
                    'Run the agent twice and merge only when both runs agree',
                ],
                'answer' => 0,
                'explain' => 'This is context poisoning: one wrong intermediate fact entered the context and 34 steps elaborated it. The PR step faithfully reported the poisoned state — fixing it treats the symptom. C shortens trajectories without fixing why step 6 misread output, and a valid migration may need 40 steps. D doubles cost and only helps if the misreading is nondeterministic; the parsing defect could fail both runs identically.',
            ],
            [
                'q' => 'An agent\'s pipeline has 25 steps, each independently about 97% reliable, and the team is puzzled that only roughly half of full runs succeed despite "every component testing fine". What explains this, and what does it imply for diagnosis?',
                'options' => [
                    'The components must interact badly; rewrite the pipeline as a single prompt',
                    'Roughly half is expected: 0.97²⁵ ≈ 0.47 — per-step reliability compounds multiplicatively, so improving whole-run success means finding and hardening the weakest steps and adding recovery, not polishing the average step',
                    'The 97% figure must be wrong, since 25 steps at 97% should give ~97% overall',
                    'Success should be measured per step, not per run, so the pipeline is actually fine',
                ],
                'answer' => 1,
                'explain' => '0.97²⁵ ≈ 0.47 — the same compounding arithmetic behind pass^k. Nothing is mysteriously broken; multiplicative reliability means "every step fine on average" still halves whole-run success. The implication is to prioritise the least-reliable steps and per-step error recovery. A abandons a working architecture over arithmetic. C assumes reliabilities don\'t compound. D redefines the metric so the user-visible failure disappears from the report, which is measurement malpractice.',
            ],
        ],
    ],
];
