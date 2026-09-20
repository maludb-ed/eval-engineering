<?php
/**
 * config.php — course metadata and structure for the Eval Engineering site.
 *
 * Self-contained on purpose: everything the site needs to render is defined
 * here, so the html/eval/ folder can be dropped into /var/www/html/eval on
 * the Ubuntu/Apache server without depending on files elsewhere in the repo.
 *
 * Source of record: course-outline.md, research/exam-blueprint.md, and the
 * topic files under content/topics/ (ev-M-NN-slug.md).
 */

$COURSE = [
    'title'      => 'Eval Engineering',
    'subtitle'   => 'for the Claude Certified Architect: Professional (CCAR-P)',
    'domain'     => 'Evaluation, Testing & Optimisation',
    'domain_no'  => 4,
    'weight'     => 16,           // % of the exam
    'exam_items' => 63,           // total items on the exam
    'scored'     => 10,           // approx scored items from this domain
    'hours'      => 14,           // total time budget
    'version'    => 'v0.1',
    'updated'    => '2026-09-15',
];

/**
 * Learning outcomes — verbatim from course-outline.md §1.
 */
$OUTCOMES = [
    'Turn a business requirement into measurable criteria across accuracy, latency, cost, safety, and security.',
    'Design an eval dataset and harness whose results actually predict production behaviour.',
    'Pick the cheapest grader that answers the question, and calibrate an LLM judge against humans.',
    'Design an A/B or canary rollout that can prove a change is an improvement.',
    'Trace a production symptom to the component that caused it, not the one that is easiest to blame.',
    'Choose among token, latency, and cost levers with the real numbers.',
    'Specify what to log and which signals to alert on for a Claude system.',
    'Recognise the domain\'s recurring distractor patterns under exam time pressure.',
];

/**
 * Modules and their topics. Each topic id maps to a content/topics/ file.
 */
$MODULES = [
    [
        'no'    => 1,
        'title' => 'Success Criteria and Metrics',
        'time'  => '1.5h',
        'task'  => '4.1 Define evaluation metrics (accuracy, latency, cost, safety, security)',
        'blurb' => 'Turn "the agent feels worse" into numbers: the four properties of good success criteria, the five measurable axes, and why single-axis targets are the trap.',
        'icon'  => 'bullseye',
        'topics' => [
            ['id' => 'ev-1-01', 'title' => 'Success Criteria That Hold Up',                 'file' => 'ev-1-01-success-criteria.md'],
            ['id' => 'ev-1-02', 'title' => 'Choosing an Accuracy Metric',                   'file' => 'ev-1-02-accuracy-metrics.md'],
            ['id' => 'ev-1-03', 'title' => 'Consistency Metrics — pass@k and pass^k',       'file' => 'ev-1-03-pass-at-k.md'],
            ['id' => 'ev-1-04', 'title' => 'Latency, Cost, Safety and Security as Axes',     'file' => 'ev-1-04-latency-cost-safety-security.md'],
        ],
    ],
    [
        'no'    => 2,
        'title' => 'Eval Datasets and Harness Design',
        'time'  => '2h',
        'task'  => '4.2 Design evaluation datasets and test frameworks using mixed methodologies',
        'blurb' => 'Start small with real failures. The vocabulary of an eval run, unambiguous solvable tasks, balanced sets, and harness parity with production.',
        'icon'  => 'database',
        'topics' => [
            ['id' => 'ev-2-01', 'title' => 'The Vocabulary of an Eval Run',                 'file' => 'ev-2-01-eval-vocabulary.md'],
            ['id' => 'ev-2-02', 'title' => 'Building the First Dataset',                     'file' => 'ev-2-02-first-dataset.md'],
            ['id' => 'ev-2-03', 'title' => 'Unambiguous, Solvable, Balanced Tasks',         'file' => 'ev-2-03-task-quality.md'],
            ['id' => 'ev-2-04', 'title' => 'Harness Parity and Clean State',                'file' => 'ev-2-04-harness-parity.md'],
            ['id' => 'ev-2-05', 'title' => 'Keeping the Suite Alive',                        'file' => 'ev-2-05-living-suite.md'],
        ],
    ],
    [
        'no'    => 3,
        'title' => 'Graders and Mixed Methodologies',
        'time'  => '2h',
        'task'  => '4.2 Design evaluation datasets and test frameworks using mixed methodologies',
        'blurb' => 'The cheapest grader that answers the question wins. Code, model, and human graders; LLM-judge design; and calibrating a judge against humans.',
        'icon'  => 'clipboard-check',
        'topics' => [
            ['id' => 'ev-3-01', 'title' => 'Choosing a Grader Family',                      'file' => 'ev-3-01-grader-families.md'],
            ['id' => 'ev-3-02', 'title' => 'Code-Based Graders and Outcome Verification',   'file' => 'ev-3-02-code-graders.md'],
            ['id' => 'ev-3-03', 'title' => 'Designing an LLM Judge',                         'file' => 'ev-3-03-llm-judge-design.md'],
            ['id' => 'ev-3-04', 'title' => 'Calibrating a Judge Against Humans',            'file' => 'ev-3-04-judge-calibration.md'],
            ['id' => 'ev-3-05', 'title' => 'When Grading Goes Wrong',                       'file' => 'ev-3-05-grading-failures.md'],
        ],
    ],
    [
        'no'    => 4,
        'title' => 'A/B Testing and Iterative Improvement',
        'time'  => '1.5h',
        'task'  => '4.3 Conduct A/B testing and iterative improvements',
        'blurb' => 'Offline evals gate; online experiments confirm. Regression gates, A/B design for LLM systems, guardrail metrics, and progressive rollout.',
        'icon'  => 'graph-up-arrow',
        'topics' => [
            ['id' => 'ev-4-01', 'title' => 'What Offline Evals and Online Experiments Prove', 'file' => 'ev-4-01-offline-and-online.md'],
            ['id' => 'ev-4-02', 'title' => 'Regression Gates and the Shipping Sequence',    'file' => 'ev-4-02-regression-gates.md'],
            ['id' => 'ev-4-03', 'title' => 'Designing an A/B Test for an LLM System',       'file' => 'ev-4-03-ab-design.md'],
            ['id' => 'ev-4-04', 'title' => 'Guardrails, Progressive Rollout, Online Signals', 'file' => 'ev-4-04-guardrails-and-rollout.md'],
        ],
    ],
    [
        'no'    => 5,
        'title' => 'Diagnosis and Root-Cause Analysis',
        'time'  => '2h',
        'task'  => '4.4 Diagnose system issues (prompt failure, hallucinations, model mismatch)',
        'blurb' => 'Trace the symptom to the component that changed. A failure taxonomy with distinguishing evidence, and the confident-but-wrong RAG scenario.',
        'icon'  => 'search',
        'topics' => [
            ['id' => 'ev-5-01', 'title' => 'The Diagnostic Method',                         'file' => 'ev-5-01-diagnostic-method.md'],
            ['id' => 'ev-5-02', 'title' => 'Prompt Failure or Model Mismatch',             'file' => 'ev-5-02-prompt-vs-model.md'],
            ['id' => 'ev-5-03', 'title' => 'Hallucination and Groundedness',               'file' => 'ev-5-03-hallucination.md'],
            ['id' => 'ev-5-04', 'title' => 'Diagnosing Retrieval and RAG Failures',        'file' => 'ev-5-04-rag-failures.md'],
            ['id' => 'ev-5-05', 'title' => 'Tool, Context, and Agent-Loop Failures',       'file' => 'ev-5-05-tool-context-loop-failures.md'],
        ],
    ],
    [
        'no'    => 6,
        'title' => 'Optimising Tokens, Latency, and Cost',
        'time'  => '2h',
        'task'  => '4.5 Optimise token usage, latency, and cost-performance trade-offs',
        'blurb' => 'Optimise only after quality is established. Model routing, prompt-caching economics with the real multipliers, token hygiene, and latency levers.',
        'icon'  => 'speedometer2',
        'topics' => [
            ['id' => 'ev-6-01', 'title' => 'The Order of Operations',                       'file' => 'ev-6-01-optimisation-order.md'],
            ['id' => 'ev-6-02', 'title' => 'Prompt Caching Economics and Mechanics',       'file' => 'ev-6-02-prompt-caching.md'],
            ['id' => 'ev-6-03', 'title' => 'Model Tier, Thinking Effort, and Batch',       'file' => 'ev-6-03-model-effort-batch.md'],
            ['id' => 'ev-6-04', 'title' => 'Input and Output Token Hygiene',               'file' => 'ev-6-04-token-hygiene.md'],
            ['id' => 'ev-6-05', 'title' => 'Latency Levers and Their Trades',              'file' => 'ev-6-05-latency.md'],
        ],
    ],
    [
        'no'    => 7,
        'title' => 'Monitoring and Observability',
        'time'  => '1.5h',
        'task'  => '4.6 Monitor system performance using logging and observability tools',
        'blurb' => 'The trace as the unit of logging. Metrics, dashboards and alerting; online evals and drift; log privacy, retention, and the review loop.',
        'icon'  => 'activity',
        'topics' => [
            ['id' => 'ev-7-01', 'title' => 'What to Log — the Trace as the Unit',          'file' => 'ev-7-01-what-to-log.md'],
            ['id' => 'ev-7-02', 'title' => 'Metrics, Dashboards, and Alerting',           'file' => 'ev-7-02-metrics-and-alerting.md'],
            ['id' => 'ev-7-03', 'title' => 'Online Evaluation and Drift Detection',       'file' => 'ev-7-03-online-evals-and-drift.md'],
            ['id' => 'ev-7-04', 'title' => 'Log Privacy, Retention, and the Review Loop',  'file' => 'ev-7-04-privacy-and-review.md'],
        ],
    ],
    [
        'no'    => 8,
        'title' => 'Where Eval Meets the Other Domains',
        'time'  => '1h',
        'task'  => 'Cross-domain reinforcement — Governance, Integration, Solution Design, Communication',
        'blurb' => 'Eval evidence as the connective tissue: governance and risk, evaluating the integration layers, and reporting results honestly to stakeholders.',
        'icon'  => 'diagram-3',
        'topics' => [
            ['id' => 'ev-8-01', 'title' => 'Eval Evidence in Governance and Risk',        'file' => 'ev-8-01-eval-evidence-in-governance.md'],
            ['id' => 'ev-8-02', 'title' => 'Evaluating the Integration and Architecture Layers', 'file' => 'ev-8-02-evaluating-the-architecture.md'],
            ['id' => 'ev-8-03', 'title' => 'Reporting Results and Owning the Practice',    'file' => 'ev-8-03-reporting-and-ownership.md'],
        ],
    ],
];

/**
 * Fixed business scenarios reused across the course (content/scenarios.md).
 */
$SCENARIOS = [
    ['no' => 1, 'name' => 'Claims triage assistant',   'tags' => 'insurance · high volume · regulated',    'desc' => 'Classifies and routes 40k claims/day; a wrong route delays a payout.'],
    ['no' => 2, 'name' => 'Contract clause extractor',  'tags' => 'legal · batch · precision-critical',      'desc' => 'Pulls named clause types from long agreements; a missed clause reaches counsel.'],
    ['no' => 3, 'name' => 'Support deflection agent',   'tags' => 'multi-turn · latency-sensitive · CSAT',   'desc' => 'Answers customer questions and escalates when it cannot.'],
    ['no' => 4, 'name' => 'Policy knowledge assistant', 'tags' => 'internal RAG · quarterly refresh',         'desc' => 'RAG over HR and compliance docs; answers must be grounded and current.'],
    ['no' => 5, 'name' => 'Research report agent',      'tags' => 'long-horizon · open-ended · expensive',   'desc' => 'Gathers sources and drafts a briefing; judged on coverage and groundedness.'],
    ['no' => 6, 'name' => 'Code migration agent in CI', 'tags' => 'verifiable · test-gated',                 'desc' => 'Ports services to a new framework and opens pull requests.'],
];

/**
 * Assessment plan (course-outline.md §4).
 */
$ASSESSMENT = [
    ['instrument' => 'Module quizzes',    'when' => 'End of each module', 'items' => '70 total',    'purpose' => 'Recall + application'],
    ['instrument' => 'Checkpoint A',      'when' => 'After M3',           'items' => '15 in 28 min', 'purpose' => 'Dataset/grader judgment under pacing'],
    ['instrument' => 'Checkpoint B',      'when' => 'After M6',           'items' => '15 in 28 min', 'purpose' => 'Diagnosis + optimisation under pacing'],
    ['instrument' => 'Domain mock',       'when' => 'After M8',           'items' => '30 in 57 min', 'purpose' => 'Exam-condition simulation'],
    ['instrument' => 'Weak-area retest',  'when' => 'Post-mock',          'items' => 'Adaptive',     'purpose' => 'Close gaps found by the mock'],
];

/** Total topic count across all modules. */
$TOPIC_COUNT = array_sum(array_map(fn($m) => count($m['topics']), $MODULES));
