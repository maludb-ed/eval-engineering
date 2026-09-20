const pptxgen = require('pptxgenjs');
const { C, HEAD, BODY, M, chip, heading, footer, card, statBlock, markerRow } = require('./theme');

const pres = new pptxgen();
pres.layout = 'LAYOUT_WIDE';            // 13.33 x 7.5
pres.author = 'CCAR-P eval engineering course';
pres.title = 'Module 1 — Success Criteria and Metrics';

const TOTAL = 18;
let n = 0;
const next = (bg) => {
  const s = pres.addSlide();
  s.background = { color: bg ?? C.white };
  n += 1;
  return s;
};

/* 1 — title */
{
  const s = next(C.ink);
  s.addText('M1', {
    x: 7.9, y: 1.55, w: 4.83, h: 2.4, isTextBox: true, margin: 0,
    align: 'right', valign: 'middle', fontFace: HEAD, fontSize: 150, bold: true, color: C.inkSoft,
  });
  s.addText('CLAUDE CERTIFIED ARCHITECT · PROFESSIONAL (CCAR-P)', {
    x: M.x, y: 1.5, w: 8.6, h: 0.3, isTextBox: true, margin: 0,
    charSpacing: 1.6, fontFace: BODY, fontSize: 11, bold: true, color: '7FB3C4',
  });
  s.addText('Success Criteria\nand Metrics', {
    x: M.x, y: 1.95, w: 8.6, h: 1.9, isTextBox: true, margin: 0,
    fontFace: HEAD, fontSize: 46, bold: true, color: C.white, lineSpacing: 52,
  });
  s.addText('Module 1 of 8 · Evaluation, Testing & Optimisation (16% of the exam)', {
    x: M.x, y: 3.95, w: 8.6, h: 0.4, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 16, color: 'B9CDD5',
  });
  const chips = [
    'Task statement 4.1',
    '4 topics · 1.5 hours',
    'ev-1-01 → ev-1-04',
  ];
  chips.forEach((t, i) => {
    const x = M.x + i * 2.95;
    s.addShape('roundRect', {
      x, y: 5.15, w: 2.75, h: 0.46, rectRadius: 0.08,
      fill: { color: C.inkSoft }, line: { color: C.inkLine, width: 1 },
    });
    s.addText(t, {
      x, y: 5.15, w: 2.75, h: 0.46, isTextBox: true, margin: 0,
      align: 'center', valign: 'middle', fontFace: BODY, fontSize: 12, color: 'CFE0E6',
    });
  });
  s.addNotes('Module 1 establishes the vocabulary every later module reuses. The exam point of view: an architect defending a measurement choice, not a researcher optimising a benchmark.');
}

/* 2 — where the module sits */
{
  const s = next();
  heading(s, 'Where this module sits');
  s.addText('The domain is worth about ten scored items. M1 covers the first of its six task statements — and supplies the vocabulary the other five reuse.', {
    x: M.x, y: 1.6, w: 11.2, h: 0.5, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 15, color: C.gray,
  });
  statBlock(s, { x: M.x, y: 2.35, w: 3.5, value: '16%', label: 'of the CCAR-P exam is Evaluation, Testing & Optimisation' });
  statBlock(s, { x: M.x, y: 3.8, w: 3.5, value: '≈10', label: 'scored items, of 63 across seven domains' });
  statBlock(s, { x: M.x, y: 5.25, w: 3.5, value: '114 s', label: 'average time per item — 63 items in 120 minutes' });

  const ts = [
    ['4.1', 'Define evaluation metrics — accuracy, latency, cost, safety, security', true],
    ['4.2', 'Design evaluation datasets and test frameworks', false],
    ['4.3', 'Conduct A/B testing and iterative improvements', false],
    ['4.4', 'Diagnose system issues', false],
    ['4.5', 'Optimise token usage, latency, cost trade-offs', false],
    ['4.6', 'Monitor performance with logging and observability', false],
  ];
  ts.forEach(([code, text, active], i) => {
    const y = 2.35 + i * 0.78;
    card(s, {
      x: 4.75, y, w: 7.98, h: 0.62,
      fill: active ? C.tealTint : C.light,
      line: active ? C.teal : 'E4EAEB',
    });
    s.addText(code, {
      x: 4.95, y, w: 0.6, h: 0.62, isTextBox: true, margin: 0, valign: 'middle',
      fontFace: BODY, fontSize: 13, bold: true, color: active ? C.tealDeep : C.grayLight,
    });
    s.addText(text, {
      x: 5.6, y, w: 6.95, h: 0.62, isTextBox: true, margin: 0, valign: 'middle',
      fontFace: BODY, fontSize: 13, bold: active, color: active ? C.ink : C.gray,
    });
  });
  footer(s, n, TOTAL);
}

/* 3 — objectives */
{
  const s = next();
  heading(s, 'What you will be able to do');
  const rows = [
    ['1', 'Write criteria that survive scrutiny', 'Specific, measurable, achievable, relevant — and fixed before the run, not after the result.'],
    ['2', 'Choose an accuracy metric that fits the output', 'Exact match, ROUGE-L, similarity, rubric — and recognise the aggregate that is lying to you.'],
    ['3', 'Pick the consistency metric the product needs', 'pass@k when one success is enough; pass^k when every run must be right.'],
    ['4', 'Measure latency, cost, safety and security as first-class axes', 'Each with a unit, a method, a threshold, and a named owner.'],
  ];
  rows.forEach(([mark, lead, body], i) => {
    markerRow(s, { x: M.x, y: 1.85 + i * 1.3, w: 11.4, mark, lead, body, h: 1.1, size: 15 });
  });
  footer(s, n, TOTAL);
}

/* 4 — the four properties */
{
  const s = next();
  chip(s, 'ev-1-01', 'Topic 1 — Success criteria that hold up');
  heading(s, 'Four properties of a criterion that works');
  const props = [
    ['Specific', 'Name the behaviour. Not "good performance" — "accurate sentiment classification".'],
    ['Measurable', 'A quantitative metric, or a defined qualitative scale applied consistently alongside one.'],
    ['Achievable', 'Benchmarked against industry results, prior experiments, or demonstrated model capability.'],
    ['Relevant', 'Earns its place here. Citation accuracy is critical for a medical assistant, overhead for a chatbot.'],
  ];
  props.forEach(([title, body], i) => {
    const x = M.x + (i % 2) * 6.15;
    const y = 1.75 + Math.floor(i / 2) * 1.95;
    card(s, { x, y, w: 5.98, h: 1.72 });
    s.addText(title, {
      x: x + 0.3, y: y + 0.22, w: 5.4, h: 0.4, isTextBox: true, margin: 0,
      fontFace: HEAD, fontSize: 20, bold: true, color: C.tealDeep,
    });
    s.addText(body, {
      x: x + 0.3, y: y + 0.72, w: 5.4, h: 0.85, isTextBox: true, margin: 0,
      fontFace: BODY, fontSize: 13.5, color: C.gray, lineSpacing: 18,
    });
  });
  card(s, { x: M.x, y: 5.75, w: 12.13, h: 0.92, fill: C.ochreTint, line: 'E8D6B8' });
  s.addText(
    [
      { text: 'Four properties, not five.  ', options: { bold: true, color: C.ink } },
      { text: 'Anthropic’s documentation never uses the acronym SMART, and there is no "time-bound" criterion. Distractors are written from the source’s wording — so borrow the source’s wording.', options: { color: C.gray } },
    ],
    { x: M.x + 0.3, y: 5.75, w: 11.5, h: 0.92, isTextBox: true, margin: 0, valign: 'middle',
      fontFace: BODY, fontSize: 13.5, lineSpacing: 18 }
  );
  footer(s, n, TOTAL);
  s.addNotes('Ask the room to state a criterion from their own work, then test it against all four properties. Most fail on measurable or achievable.');
}

/* 5 — targets come in sets */
{
  const s = next();
  chip(s, 'ev-1-01', 'Topic 1 — Success criteria that hold up');
  heading(s, 'Targets come in sets, not singles');
  s.addText('Most production use cases need several criteria evaluated at once. A realistic target set for a claims triage assistant:', {
    x: M.x, y: 1.62, w: 11.4, h: 0.4, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 15, color: C.gray,
  });
  const stats = [
    ['F1 ≥ 0.85', 'task fidelity on the labelled set'],
    ['99.5%', 'of outputs non-toxic'],
    ['p95 < 200 ms', 'response latency'],
    ['90%', 'of errors inconvenient, not critical'],
  ];
  stats.forEach(([v, l], i) => {
    const x = M.x + i * 3.05;
    card(s, { x, y: 2.25, w: 2.85, h: 1.65 });
    statBlock(s, { x: x + 0.28, y: 2.45, w: 2.3, value: v, label: l, size: 26, labelSize: 11.5 });
  });
  markerRow(s, {
    x: M.x, y: 4.35, w: 11.4, mark: '✓', lead: 'Error severity is itself a criterion.',
    body: 'Two systems with identical accuracy can carry completely different risk. "90% of errors cause inconvenience rather than critical failure" is a target, not commentary.',
    h: 0.95, size: 14.5,
  });
  markerRow(s, {
    x: M.x, y: 5.45, w: 11.4, mark: '✓', lead: 'Criterion-referenced, always.',
    body: 'Thresholds are set against a fixed standard before the run. A threshold chosen after seeing the result is not a criterion — it is a description.',
    h: 0.95, size: 14.5,
  });
  footer(s, n, TOTAL);
}

/* 6 — traps: criteria */
{
  const s = next();
  chip(s, 'ev-1-01', 'Topic 1 — Success criteria that hold up');
  heading(s, 'Exam traps — criteria');
  const traps = [
    ['A single-axis target', 'when the stem states a cost, latency or safety constraint. The credited answer names the constraint the stem planted.'],
    ['"Time-bound", or the SMART framing', 'offered as one of the properties. The source lists four and never uses the label.'],
    ['100% accuracy, zero hallucinations', 'unachievable against demonstrated capability — so the criterion is broken, however important the use case.'],
    ['The threshold set after the first run', 'or moved when the system misses it. Sounds pragmatic; fails criterion-referenced scoring.'],
    ['Adjectives with no scale', '"professional", "helpful", "concise". Measurable needs a defined scale, not a better adjective.'],
  ];
  traps.forEach(([lead, body], i) => {
    markerRow(s, {
      x: M.x, y: 1.8 + i * 1.02, w: 11.4, mark: '!', markFill: C.ochre,
      lead, body, h: 0.9, size: 13.5,
    });
  });
  footer(s, n, TOTAL);
}

/* 7 — metric to output shape */
{
  const s = next();
  chip(s, 'ev-1-02', 'Topic 2 — Choosing an accuracy metric');
  heading(s, '"Accuracy" is a category. Match the metric to the output.');
  const rows = [
    ['Categorical label', 'Exact match', 'Normalise whitespace and case. An LLM judge here is over-engineering.'],
    ['Summary vs a reference', 'ROUGE-L (F1 of LCS)', 'Measures overlap with the reference — not truth.'],
    ['Consistency across paraphrases', 'Cosine similarity, sentence embeddings', 'Measures agreement between answers, not correctness.'],
    ['Subjective quality', 'LLM rubric, Likert 1–5', 'Fixed ordinal scale; grading model separate from the generating one.'],
    ['Yes/no property', 'LLM binary classification', 'Catches implicit and paraphrased cases a regex cannot.'],
  ];
  rows.forEach(([shape, metric, note], i) => {
    const y = 1.78 + i * 0.98;
    card(s, { x: M.x, y, w: 12.13, h: 0.82, fill: i % 2 ? C.white : C.tealPale, line: i % 2 ? 'E9EEEF' : C.tealTint });
    s.addText(shape, {
      x: M.x + 0.28, y, w: 3.15, h: 0.82, isTextBox: true, margin: 0, valign: 'middle',
      fontFace: BODY, fontSize: 13.5, bold: true, color: C.ink,
    });
    s.addText(metric, {
      x: M.x + 3.5, y, w: 3.3, h: 0.82, isTextBox: true, margin: 0, valign: 'middle',
      fontFace: BODY, fontSize: 13.5, color: C.tealDeep, bold: true,
    });
    s.addText(note, {
      x: M.x + 6.95, y, w: 5.0, h: 0.82, isTextBox: true, margin: 0, valign: 'middle',
      fontFace: BODY, fontSize: 12.5, color: C.gray,
    });
  });
  s.addText('Design principle: be task-specific, automate grading where possible, and prefer volume (50–1,000 cases) over per-item polish.', {
    x: M.x, y: 6.72, w: 12.13, h: 0.32, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 12, italic: true, color: C.grayLight,
  });
}

/* 8 — the aggregate that lies */
{
  const s = next();
  chip(s, 'ev-1-02', 'Topic 2 — Choosing an accuracy metric');
  heading(s, 'The aggregate that lies');
  card(s, { x: M.x, y: 1.8, w: 5.1, h: 2.45, fill: C.ochreTint, line: 'E8D6B8' });
  s.addText('95%', {
    x: M.x + 0.35, y: 2.0, w: 4.4, h: 1.0, isTextBox: true, margin: 0,
    fontFace: HEAD, fontSize: 58, bold: true, color: C.ochre,
  });
  s.addText('accurate — on a claims corpus where 5% are fraud, from a model that never once predicts fraud.', {
    x: M.x + 0.35, y: 3.0, w: 4.4, h: 1.0, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 14, color: C.ink, lineSpacing: 19,
  });
  s.addText('Read precision, recall and F1 per class. Then decide which of precision and recall the business is actually buying — they trade against each other, and the scenario decides.', {
    x: M.x, y: 4.45, w: 5.1, h: 1.3, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 13.5, color: C.gray, lineSpacing: 19,
  });
  const cols = [
    ['Precision failure', 'The clause extractor reports a non-compete that is not in the contract. Counsel wastes an hour proving a negative.', 'Costly when every finding is acted on.'],
    ['Recall failure', 'The extractor silently misses a real indemnity clause. Nobody discovers it until the dispute.', 'Costly when a miss is invisible downstream.'],
  ];
  cols.forEach(([title, body, tail], i) => {
    const x = 6.1 + i * 3.35;
    card(s, { x, y: 1.8, w: 3.15, h: 3.95 });
    s.addText(title, {
      x: x + 0.28, y: 2.0, w: 2.6, h: 0.45, isTextBox: true, margin: 0,
      fontFace: HEAD, fontSize: 17, bold: true, color: C.tealDeep,
    });
    s.addText(body, {
      x: x + 0.28, y: 2.5, w: 2.6, h: 1.9, isTextBox: true, margin: 0,
      fontFace: BODY, fontSize: 13, color: C.gray, lineSpacing: 18,
    });
    s.addText(tail, {
      x: x + 0.28, y: 4.5, w: 2.6, h: 1.1, isTextBox: true, margin: 0,
      fontFace: BODY, fontSize: 12.5, italic: true, color: C.ink, lineSpacing: 17,
    });
  });
  s.addText('The credited answer usually states the trade-off rather than promising both.', {
    x: M.x, y: 6.05, w: 12.13, h: 0.4, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 14, bold: true, color: C.ink,
  });
  footer(s, n, TOTAL);
}

/* 9 — traps: metrics */
{
  const s = next();
  chip(s, 'ev-1-02', 'Topic 2 — Choosing an accuracy metric');
  heading(s, 'Exam traps — metrics');
  const traps = [
    ['Overall accuracy on imbalanced data', 'or an option that "improves accuracy" without touching the minority class.'],
    ['ROUGE-L or embedding similarity as a truth check', 'both measure overlap with a reference; a fluent fabrication scores well.'],
    ['An LLM judge where exact match settles it', 'slower, dearer, less reproducible than the deterministic option.'],
    ['Grading with the generating model', 'the documented examples always use a separate instance.'],
    ['"Fewer, higher-quality hand-graded examples"', 'the documented preference is volume with automated grading.'],
    ['Precision and recall both improved', 'with no change to the system — name the trade instead.'],
  ];
  traps.forEach(([lead, body], i) => {
    const x = M.x + (i % 2) * 6.15;
    const y = 1.85 + Math.floor(i / 2) * 1.5;
    card(s, { x, y, w: 5.98, h: 1.26, fill: C.light, line: 'E4EAEB' });
    markerRow(s, {
      x: x + 0.26, y: y + 0.21, w: 5.5, mark: '!', markFill: C.ochre,
      lead, body, h: 0.95, size: 13, d: 0.3, markSize: 12,
    });
  });
  footer(s, n, TOTAL);
}

/* 10 — pass@k vs pass^k + chart */
{
  const s = next();
  chip(s, 'ev-1-03', 'Topic 3 — Consistency metrics');
  heading(s, 'One eval run, two opposite verdicts');
  card(s, { x: M.x, y: 1.78, w: 5.25, h: 1.95 });
  s.addText('pass@k', {
    x: M.x + 0.3, y: 1.95, w: 4.6, h: 0.45, isTextBox: true, margin: 0,
    fontFace: HEAD, fontSize: 22, bold: true, color: C.tealDeep,
  });
  s.addText('At least one of k attempts succeeds.\nThe metric for "one success is enough" — retries are cheap and safe.', {
    x: M.x + 0.3, y: 2.45, w: 4.6, h: 1.15, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 13.5, color: C.gray, lineSpacing: 19,
  });
  card(s, { x: M.x, y: 3.95, w: 5.25, h: 1.95, fill: C.ochreTint, line: 'E8D6B8' });
  s.addText('pass^k', {
    x: M.x + 0.3, y: 4.12, w: 4.6, h: 0.45, isTextBox: true, margin: 0,
    fontFace: HEAD, fontSize: 22, bold: true, color: C.ochre,
  });
  s.addText('All k attempts succeed.\nThe metric for "this has to work every time" — unattended, irreversible, customer-facing.', {
    x: M.x + 0.3, y: 4.62, w: 4.6, h: 1.15, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 13.5, color: C.gray, lineSpacing: 19,
  });
  s.addText('Identical at k = 1. By k = 10 they are telling opposite stories about the same system.', {
    x: M.x, y: 6.05, w: 5.25, h: 0.6, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 13, bold: true, color: C.ink, lineSpacing: 18,
  });

  const ks = [1,2,3,4,5,6,7,8,9,10];
  const atK = ks.map(k => +( (1 - Math.pow(0.3, k)) * 100 ).toFixed(1));
  const hatK = ks.map(k => +( Math.pow(0.7, k) * 100 ).toFixed(1));
  s.addChart('line', [
    { name: 'pass@k', labels: ks.map(String), values: atK },
    { name: 'pass^k', labels: ks.map(String), values: hatK },
  ], {
    x: 6.3, y: 1.78, w: 6.45, h: 4.35,
    showTitle: true, title: 'Same system, 70% success per trial',
    titleFontSize: 13, titleColor: C.ink, titleFontFace: BODY,
    chartColors: [C.teal, C.ochre],
    lineSize: 3, lineSmooth: false,
    showLegend: true, legendPos: 'b', legendFontSize: 11, legendColor: C.gray,
    catAxisTitle: 'k (trials)', showCatAxisTitle: true, catAxisTitleFontSize: 11, catAxisTitleColor: C.gray,
    catAxisLabelColor: C.gray, catAxisLabelFontSize: 11,
    valAxisLabelColor: C.gray, valAxisLabelFontSize: 11,
    valAxisMaxVal: 100, valAxisMinVal: 0, valAxisLabelFormatCode: '0"%"',
    valGridLine: { color: 'E4EAEB', size: 1 },
    catGridLine: { style: 'none' },
  });
  s.addText('Illustrative: a task the system passes 70% of the time.', {
    x: 6.3, y: 6.2, w: 6.45, h: 0.3, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 11, italic: true, color: C.grayLight,
  });
}

/* 11 — mapping to the product */
{
  const s = next();
  chip(s, 'ev-1-03', 'Topic 3 — Consistency metrics');
  heading(s, 'Which one does the product actually need?');
  const cols = [
    [C.teal, C.tealPale, C.tealTint, 'pass@k systems', [
      'The migration agent opens a pull request a human reviews',
      'A draft the user edits before sending',
      'Any step where a retry costs cents and harms nobody',
    ], 'Retries raise pass@k — so "retry on failure" is a real fix here.'],
    [C.ochre, C.ochreTint, 'E8D6B8', 'pass^k systems', [
      'An unattended refund or payment',
      'An email that reaches the customer with no review',
      'A merge to main, or anything irreversible',
    ], 'Retries do not move pass^k — the ninth run that hallucinated already happened.'],
  ];
  cols.forEach(([accent, fill, line, title, items, tail], i) => {
    const x = M.x + i * 6.15;
    card(s, { x, y: 1.78, w: 5.98, h: 3.5, fill, line });
    s.addText(title, {
      x: x + 0.3, y: 1.98, w: 5.4, h: 0.45, isTextBox: true, margin: 0,
      fontFace: HEAD, fontSize: 21, bold: true, color: accent,
    });
    s.addText(items.map((t, j) => ({ text: t, options: { bullet: true, breakLine: j < items.length - 1 } })), {
      x: x + 0.3, y: 2.55, w: 5.4, h: 1.6, isTextBox: true, margin: 0,
      fontFace: BODY, fontSize: 13.5, color: C.gray, paraSpaceAfter: 8,
    });
    s.addText(tail, {
      x: x + 0.3, y: 4.35, w: 5.4, h: 0.8, isTextBox: true, margin: 0,
      fontFace: BODY, fontSize: 13, bold: true, color: C.ink, lineSpacing: 18,
    });
  });
  const rules = [
    ['Always state k.', 'A pass rate with no trial count is uninterpretable, and rates measured at different k are not comparable.'],
    ['One run each proves nothing.', 'A single-trial comparison between two prompts is indistinguishable from run-to-run variance.'],
  ];
  rules.forEach(([lead, body], i) => {
    markerRow(s, { x: M.x + i * 6.15, y: 5.55, w: 5.9, mark: '✓', lead, body, h: 1.0, size: 13, d: 0.3, markSize: 12 });
  });
  footer(s, n, TOTAL);
}

/* 12 — traps: consistency */
{
  const s = next();
  chip(s, 'ev-1-03', 'Topic 3 — Consistency metrics');
  heading(s, 'Exam traps — consistency');
  const traps = [
    ['pass@k quoted for an unattended workflow', 'the classic wrong-metric item — an irreversible action demands pass^k.'],
    ['k raised, then the better pass@k reported', 'as though the system improved.'],
    ['A pass rate with no k', 'or two results compared that were measured at different k.'],
    ['Victory declared from one run of each', 'the defect is the experimental design, not the prompt.'],
    ['"Add automatic retries"', 'offered for an agent that must be right the first time — it moves the metric that is not the requirement.'],
  ];
  traps.forEach(([lead, body], i) => {
    markerRow(s, { x: M.x, y: 1.85 + i * 1.02, w: 11.4, mark: '!', markFill: C.ochre, lead, body, h: 0.9, size: 13.5 });
  });
  footer(s, n, TOTAL);
}

/* 13 — four axes */
{
  const s = next();
  chip(s, 'ev-1-04', 'Topic 4 — The non-accuracy axes');
  heading(s, 'Four axes, four ways to measure them wrongly');
  const axes = [
    ['Latency', 'Percentiles — p50, p95, p99', 'The mean, which hides the tail'],
    ['Cost', 'Per resolved task or processed document', 'Per token, or per API call'],
    ['Safety', 'Unsafe-output rate, refusal correctness both ways, error severity', 'A one-time pre-launch review'],
    ['Security', 'Injection resistance, leakage rate, tool-permission violations', 'Keyword and regex filters alone'],
  ];
  axes.forEach(([title, right, wrong], i) => {
    const x = M.x + (i % 2) * 6.15;
    const y = 1.75 + Math.floor(i / 2) * 2.5;
    card(s, { x, y, w: 5.98, h: 2.25 });
    s.addText(title, {
      x: x + 0.3, y: y + 0.2, w: 5.4, h: 0.45, isTextBox: true, margin: 0,
      fontFace: HEAD, fontSize: 20, bold: true, color: C.tealDeep,
    });
    s.addText([
      { text: 'Measure  ', options: { bold: true, color: C.teal } },
      { text: right, options: { color: C.ink, breakLine: true } },
    ], {
      x: x + 0.3, y: y + 0.72, w: 5.4, h: 0.72, isTextBox: true, margin: 0,
      fontFace: BODY, fontSize: 13, lineSpacing: 18,
    });
    s.addText([
      { text: 'Not  ', options: { bold: true, color: C.ochre } },
      { text: wrong, options: { color: C.gray } },
    ], {
      x: x + 0.3, y: y + 1.5, w: 5.4, h: 0.6, isTextBox: true, margin: 0,
      fontFace: BODY, fontSize: 13, lineSpacing: 18,
    });
  });
  footer(s, n, TOTAL);
}

/* 14 — latency */
{
  const s = next();
  chip(s, 'ev-1-04', 'Topic 4 — The non-accuracy axes');
  heading(s, 'Two latencies, and why the mean is useless');
  card(s, { x: M.x, y: 1.78, w: 5.25, h: 1.8 });
  s.addText('Time to first token', {
    x: M.x + 0.3, y: 1.95, w: 4.6, h: 0.4, isTextBox: true, margin: 0,
    fontFace: HEAD, fontSize: 18, bold: true, color: C.tealDeep,
  });
  s.addText('Governs whether the interface feels responsive. Streaming is a real fix here.', {
    x: M.x + 0.3, y: 2.4, w: 4.6, h: 1.0, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 13.5, color: C.gray, lineSpacing: 19,
  });
  card(s, { x: M.x, y: 3.78, w: 5.25, h: 1.8 });
  s.addText('Baseline latency', {
    x: M.x + 0.3, y: 3.95, w: 4.6, h: 0.4, isTextBox: true, margin: 0,
    fontFace: HEAD, fontSize: 18, bold: true, color: C.tealDeep,
  });
  s.addText('Total time to a finished response. Streaming changes it not at all — a batch pipeline cares only about this.', {
    x: M.x + 0.3, y: 4.4, w: 4.6, h: 1.05, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 13.5, color: C.gray, lineSpacing: 19,
  });
  s.addText('State the target as a percentile: "95% of responses under 200 ms".', {
    x: M.x, y: 5.75, w: 5.25, h: 0.7, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 13.5, bold: true, color: C.ink, lineSpacing: 18,
  });
  s.addChart('bar', [
    { name: 'Response time (ms)', labels: ['p50', 'mean', 'p95', 'p99'], values: [400, 780, 2100, 9000] },
  ], {
    x: 6.3, y: 1.78, w: 6.45, h: 4.0,
    barDir: 'col',
    showTitle: true, title: 'The mean sits below every number that hurts',
    titleFontSize: 13, titleColor: C.ink, titleFontFace: BODY,
    chartColors: [C.teal, C.grayLight, C.teal, C.ochre],
    varyColors: true,
    showValue: true, dataLabelPosition: 'outEnd', dataLabelColor: C.gray, dataLabelFontSize: 11,
    catAxisLabelColor: C.gray, catAxisLabelFontSize: 12,
    valAxisLabelColor: C.gray, valAxisLabelFontSize: 11,
    valGridLine: { color: 'E4EAEB', size: 1 },
    catGridLine: { style: 'none' },
    showLegend: false,
    barGapWidthPct: 60,
  });
  s.addText('Illustrative distribution for a support assistant. A mean of 780 ms reports a healthy system; one call in a hundred takes nine seconds.', {
    x: 6.3, y: 5.85, w: 6.45, h: 0.55, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 11, italic: true, color: C.grayLight, lineSpacing: 15,
  });
}

/* 15 — cost per completed task */
{
  const s = next();
  chip(s, 'ev-1-04', 'Topic 4 — The non-accuracy axes');
  heading(s, 'Cost per token is not cost');
  s.addText('Support deflection, illustrative: a human-handled ticket costs $6.00. Both models are measured on the same 1,000 tickets.', {
    x: M.x, y: 1.6, w: 11.6, h: 0.4, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 14.5, color: C.gray,
  });
  const models = [
    ['Smaller model', '$0.004', 'per call', '60%', 'resolved without a human', '$2.40', 'per ticket, all in', C.ochre, C.ochreTint, 'E8D6B8'],
    ['Larger model', '$0.020', 'per call', '85%', 'resolved without a human', '$0.92', 'per ticket, all in', C.tealDeep, C.tealPale, C.tealTint],
  ];
  models.forEach(([title, unit, unitLabel, rate, rateLabel, total, totalLabel, accent, fill, line], i) => {
    const x = M.x + i * 6.15;
    card(s, { x, y: 2.2, w: 5.98, h: 3.5, fill, line });
    s.addText(title, {
      x: x + 0.35, y: 2.4, w: 5.3, h: 0.45, isTextBox: true, margin: 0,
      fontFace: HEAD, fontSize: 20, bold: true, color: accent,
    });
    statBlock(s, { x: x + 0.35, y: 2.95, w: 2.4, value: unit, label: unitLabel, size: 28, labelSize: 12, vh: 0.6 });
    statBlock(s, { x: x + 3.1, y: 2.95, w: 2.5, value: rate, label: rateLabel, size: 28, labelSize: 12, vh: 0.6 });
    statBlock(s, { x: x + 0.35, y: 4.35, w: 5.3, value: total, label: totalLabel, size: 40, labelSize: 13, vh: 0.78, color: accent });
  });
  card(s, { x: M.x, y: 5.95, w: 12.13, h: 0.85, fill: C.ochreTint, line: 'E8D6B8' });
  s.addText(
    [
      { text: 'The smaller model: five times cheaper per call, two and a half times dearer per outcome.  ', options: { bold: true, color: C.ink } },
      { text: 'Escalations, retries, judge calls and cache writes all belong in the figure.', options: { color: C.gray } },
    ],
    { x: M.x + 0.3, y: 5.95, w: 11.5, h: 0.85, isTextBox: true, margin: 0, valign: 'middle',
      fontFace: BODY, fontSize: 13.5, lineSpacing: 18 }
  );
}

/* 16 — worked criteria table */
{
  const s = next();
  chip(s, 'ev-1-04', 'Topic 4 — The non-accuracy axes');
  heading(s, 'What a complete criteria table looks like');
  s.addText('Claims triage assistant — the artefact M1 asks you to produce. Every row has a unit, a method, and a name against it.', {
    x: M.x, y: 1.6, w: 11.6, h: 0.35, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 14, color: C.gray,
  });
  const head = ['Axis', 'Metric', 'Target', 'Measured by', 'Owner'];
  const widths = [1.9, 3.5, 2.3, 2.85, 1.58];
  let hx = M.x;
  head.forEach((h, i) => {
    s.addText(h.toUpperCase(), {
      x: hx, y: 2.1, w: widths[i], h: 0.32, isTextBox: true, margin: 0,
      fontFace: BODY, fontSize: 10, bold: true, charSpacing: 1.2, color: C.grayLight,
    });
    hx += widths[i];
  });
  const rows = [
    ['Accuracy', 'Macro-F1, per class', '≥ 0.85', '300-case labelled suite', 'ML lead'],
    ['Latency', 'p95, end to end', '< 3 s', 'Production traces', 'Platform'],
    ['Cost', 'Per processed claim', '≤ $0.05', 'Usage telemetry', 'Architect'],
    ['Safety', 'Critical mis-route rate', '< 1%', 'Severity-labelled eval', 'Risk'],
    ['Security', 'PII in stored logs', '0, monitored', 'LLM binary classifier', 'Security'],
  ];
  rows.forEach((r, i) => {
    const y = 2.5 + i * 0.78;
    card(s, { x: M.x, y, w: 12.13, h: 0.68, fill: i % 2 ? C.white : C.tealPale, line: i % 2 ? 'E9EEEF' : C.tealTint });
    let x = M.x + 0.08;
    r.forEach((cell, j) => {
      s.addText(cell, {
        x, y, w: widths[j] - 0.1, h: 0.68, isTextBox: true, margin: 0, valign: 'middle',
        fontFace: BODY, fontSize: 13, bold: j === 0 || j === 2,
        color: j === 0 ? C.ink : (j === 2 ? C.tealDeep : C.gray),
      });
      x += widths[j];
    });
  });
  s.addText('Thresholds are fixed here — before the first run — and every one of them is checkable from a system that already exists.', {
    x: M.x, y: 6.52, w: 12.13, h: 0.35, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 12.5, italic: true, color: C.grayLight,
  });
}

/* 17 — traps: axes */
{
  const s = next();
  chip(s, 'ev-1-04', 'Topic 4 — The non-accuracy axes');
  heading(s, 'Exam traps — latency, cost, safety, security');
  const traps = [
    ['A mean or average latency target', 'the percentile option is credited whenever a tail is described.'],
    ['"Enable streaming to reduce latency"', 'for a batch workload, where perceived responsiveness is irrelevant.'],
    ['Cost stated per token or per call', 'or a downgrade justified on unit price while the resolution rate falls.'],
    ['A cost model without retries or judge calls', 'claiming a saving the invoice will not show.'],
    ['Regex or keyword filtering as PII measurement', 'implicit and paraphrased disclosures go straight past it.'],
    ['Unsafe output driven to zero by refusing broadly', 'over-refusal is a safety failure too, and users notice it faster.'],
  ];
  traps.forEach(([lead, body], i) => {
    const x = M.x + (i % 2) * 6.15;
    const y = 1.85 + Math.floor(i / 2) * 1.5;
    card(s, { x, y, w: 5.98, h: 1.26, fill: C.light, line: 'E4EAEB' });
    markerRow(s, {
      x: x + 0.26, y: y + 0.21, w: 5.5, mark: '!', markFill: C.ochre,
      lead, body, h: 0.95, size: 13, d: 0.3, markSize: 12,
    });
  });
  footer(s, n, TOTAL);
}

/* 18 — wrap */
{
  const s = next(C.ink);
  s.addText('BEFORE YOU MOVE ON', {
    x: M.x, y: 0.95, w: 11.6, h: 0.3, isTextBox: true, margin: 0,
    charSpacing: 1.6, fontFace: BODY, fontSize: 11, bold: true, color: '7FB3C4',
  });
  s.addText('Four things you should now be able to defend', {
    x: M.x, y: 1.35, w: 11.6, h: 0.8, isTextBox: true, margin: 0,
    fontFace: HEAD, fontSize: 34, bold: true, color: C.white,
  });
  const items = [
    ['Why your criterion is measurable and achievable', 'and what evidence set the threshold before the run.'],
    ['Why that accuracy metric fits this output', 'and what the aggregate would hide if the classes were imbalanced.'],
    ['Whether the product needs pass@k or pass^k', 'and what k you measured at.'],
    ['What the cost and latency figures include', 'per resolved task, at a percentile, with an owner against each axis.'],
  ];
  items.forEach(([lead, body], i) => {
    const y = 2.45 + i * 0.92;
    s.addShape('ellipse', { x: M.x, y: y + 0.04, w: 0.32, h: 0.32, fill: { color: C.teal }, line: { color: C.teal } });
    s.addText(String(i + 1), {
      x: M.x, y: y + 0.04, w: 0.32, h: 0.32, isTextBox: true, margin: 0,
      align: 'center', valign: 'middle', fontFace: BODY, fontSize: 12, bold: true, color: C.white,
    });
    s.addText([
      { text: lead + '  ', options: { bold: true, color: C.white } },
      { text: body, options: { color: 'A9C2CB' } },
    ], {
      x: M.x + 0.54, y, w: 11.0, h: 0.8, isTextBox: true, margin: 0, valign: 'top',
      fontFace: BODY, fontSize: 15, lineSpacing: 20,
    });
  });
  s.addText('Next — M2: Eval datasets and harness design', {
    x: M.x, y: 6.35, w: 11.6, h: 0.4, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: 14, bold: true, color: '7FB3C4',
  });
}

pres.writeFile({ fileName: '/Users/user/eval/decks/M1-success-criteria-and-metrics.pptx' })
  .then(f => console.log('wrote', f, 'slides:', n));
