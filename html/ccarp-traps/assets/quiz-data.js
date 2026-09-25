/* Original practice questions in the style of the CCAR-P blueprint.
   They are NOT real or leaked exam items.
   Each option: t = text, c = correct?, trap = trap family number (or null), w = why. */
window.CCARP_QUESTIONS = [
  {
    id: "q1", domain: "d1", select: 1,
    stem: "An insurer must extract the same 12 fields from about 40,000 standardized claim forms a day. Each document has a hard cost cap, and regulators must be able to replay any extraction and get the same processing path. Which design fits best?",
    options: [
      { t: "A fixed workflow: one model call extracts the fields into a JSON schema, and code validates the output and retries on schema failure.", c: true, trap: null, w: "This is the simplest pattern that meets every constraint. The path is fixed in code, so it's replayable, and a single bounded model call keeps cost predictable." },
      { t: "An autonomous agent with OCR, lookup and validation tools that plans its own steps for each form.", c: false, trap: 9, w: "Too high on the pattern ladder. An agent picks its path at runtime, so it can't promise a replayable path, and its cost varies per document." },
      { t: "An orchestrator that spawns one sub-agent per group of fields, then merges the results.", c: false, trap: 8, w: "Over-orchestration: many moving parts for a job with none, plus coordination cost on 40,000 documents a day." },
      { t: "A single call to the largest available model with a long, detailed prompt and no validation step.", c: false, trap: 1, w: "Scale, not strategy. It raises cost against a hard cap and adds nothing for replayability." }
    ]
  },
  {
    id: "q2", domain: "d1", select: 1,
    stem: "A due-diligence assistant must investigate six independent questions about a target company: litigation, patents, leadership, financials, supply chain and press. Each question requires reading dozens of sources. Deals are high value. The current single agent overflows its context and takes hours. What should the architect propose?",
    options: [
      { t: "An orchestrator that runs parallel sub-agents, each with its own context window for one question and returning condensed findings. The higher token spend is justified by the value of the deal.", c: true, trap: null, w: "This is a legitimate reason to use multi-agent: context isolation plus parallel breadth on work that genuinely splits apart. Multi-agent isn't always wrong." },
      { t: "Keep one agent and move it to the model with the largest context window, running the questions in sequence.", c: false, trap: 1, w: "A bigger container doesn't fix a crowded window, and the questions still run one after another." },
      { t: "Chain six agents, each passing its full transcript on to the next so nothing is lost.", c: false, trap: 8, w: "Passing full transcripts forward throws away the isolation benefit and stacks latency. It's coordination without the payoff." },
      { t: "Keep the single agent and lower its max output tokens so each step finishes faster.", c: false, trap: 2, w: "A knob. It doesn't touch the overflow or the lack of parallelism." }
    ]
  },
  {
    id: "q3", domain: "d2", select: 1,
    stem: "A support app sends a 6,000-token system prompt and tool definitions on every request. Prompt caching is enabled, but usage data shows cache_read_input_tokens near zero. The first line of the system prompt is \"Current time: <timestamp>\". What is the most direct fix?",
    options: [
      { t: "Move the timestamp and any other per-request values after the static block, so the cached prefix is identical on every request.", c: true, trap: null, w: "The cache only matches an identical prefix. A changing value at the top invalidates everything after it. Put static content first and dynamic content last." },
      { t: "Increase the cache lifetime so entries survive longer between requests.", c: false, trap: 2, w: "A knob. The prefix is different on every request, so there's nothing to hit no matter how long entries live." },
      { t: "Switch to a larger model, which handles long system prompts more efficiently.", c: false, trap: 1, w: "Model size has nothing to do with whether the prefix matches." },
      { t: "Shorten the system prompt to reduce the cost of cache misses.", c: false, trap: 7, w: "It fixes the wrong layer, and risks quality, without addressing why the cache never hits." }
    ]
  },
  {
    id: "q4", domain: "d2", select: 1,
    stem: "A real-time intent router has to respond within 300 ms. A developer added \"think step by step before answering\". Latency tripled, and eval accuracy is unchanged. What should the architect do?",
    options: [
      { t: "Remove the chain-of-thought instruction from this step, and use reasoning only on steps where the eval shows it improves results.", c: true, trap: null, w: "Chain of thought costs latency. When the eval shows no gain, it isn't earning its place." },
      { t: "Move to a larger model so the reasoning finishes faster.", c: false, trap: 1, w: "Scale, not strategy. It pays more to keep reasoning that brings no benefit." },
      { t: "Keep chain of thought but cap max tokens so the reasoning gets cut short.", c: false, trap: 2, w: "A knob that truncates reasoning and can break the output format. The design question is unchanged." },
      { t: "Keep chain of thought and stream the response to the caller.", c: false, trap: 7, w: "Streaming helps a person reading text. A router needs the complete answer before it can act, so this is the wrong layer." }
    ]
  },
  {
    id: "q5", domain: "d3", select: 1,
    stem: "An internal MCP server fronts the payroll API. Today it receives the user's OAuth token from the client application and forwards that same token to the payroll API. A security review flags this. What is the right change?",
    options: [
      { t: "Have the MCP server accept only tokens issued for itself (audience-bound), and use its own properly scoped credentials when calling upstream, instead of passing client tokens through.", c: true, trap: null, w: "Token passthrough creates a confused-deputy risk. Audience-bound tokens and scoped upstream credentials close it." },
      { t: "Log every forwarded token so security can audit its use later.", c: false, trap: 5, w: "Compliance theater, and it makes things worse by storing bearer tokens in logs." },
      { t: "Add a system prompt instruction telling the model to call payroll tools only when necessary.", c: false, trap: 4, w: "A prompt can't fix an authentication flaw. The problem is in the protocol layer, not in the model's behavior." },
      { t: "Move payroll access behind a separate agent reached through agent-to-agent delegation.", c: false, trap: 8, w: "It adds an agent without fixing how the tokens are handled." }
    ]
  },
  {
    id: "q6", domain: "d3", select: 1,
    stem: "Five internal agents (support, sales, onboarding, billing and returns) each hand-wire their own copy of the same 14 product-catalog tools. The catalog API changes monthly, and the copies keep drifting apart and causing bugs. What integration approach fits?",
    options: [
      { t: "Expose the catalog tools once through an MCP server that every agent connects to and discovers tools from.", c: true, trap: null, w: "Many tools, changing often, shared by several agents: that's exactly the case MCP is for." },
      { t: "Create a catalog agent that every other agent delegates to through agent-to-agent calls for each lookup.", c: false, trap: 8, w: "Catalog lookups don't need their own reasoning. A2A adds coordination cost to plain tool calls." },
      { t: "Give every agent a general HTTP tool so it can call catalog endpoints directly.", c: false, trap: 11, w: "Convenience over safety. It widens the attack surface and still has no shared, versioned tool surface." },
      { t: "Paste all 14 tool definitions into a shared prompt document that each team copies.", c: false, trap: 7, w: "This is still copying, so drift continues. It treats an integration problem as a prompt problem." }
    ]
  },
  {
    id: "q7", domain: "d3", select: 1,
    stem: "An engineering assistant answers questions about a 2-million-line codebase using fixed 500-token chunks and embedding similarity. Questions like \"where is refund_amount validated, and what calls it?\" get vague or wrong answers. What should change?",
    options: [
      { t: "Retrieve by code structure: symbol definitions, references and the call graph, instead of fuzzy similarity over fixed chunks.", c: true, trap: null, w: "Match retrieval to the data shape. In code, exact symbols and structure matter." },
      { t: "Upgrade to a larger embedding model.", c: false, trap: 1, w: "A better vector still answers a structural question by resemblance." },
      { t: "Retrieve the top 50 chunks instead of the top 5.", c: false, trap: 2, w: "A knob. More fragments of the wrong kind, plus more cost and noise." },
      { t: "Instruct the model in the system prompt to always cite exact function names.", c: false, trap: 3, w: "Louder, not explicit. The model can't cite what retrieval never gave it." }
    ]
  },
  {
    id: "q8", domain: "d3", select: 1,
    stem: "A four-stage pipeline (retrieve → draft → policy check → format) shows a healthy average latency on its dashboard. Users still abandon the chat, complaining that it's slow. What should the team do first?",
    options: [
      { t: "Trace requests end to end with per-stage timings, and look at p95/p99 latency to find which stage owns the slow tail.", c: true, trap: null, w: "The average hides the tail your slowest users feel, and per-stage traces show where it lives." },
      { t: "Move every stage to a faster model right away.", c: false, trap: 10, w: "Premature optimization. It changes quality-bearing configuration before anyone knows which stage is slow." },
      { t: "Lower max tokens across all stages.", c: false, trap: 2, w: "A blind knob that may hurt quality and may not touch the slow stage at all." },
      { t: "Send leadership a weekly latency report based on the current dashboard.", c: false, trap: 5, w: "It reports the same misleading average. Documentation isn't diagnosis." }
    ]
  },
  {
    id: "q9", domain: "d4", select: 1,
    stem: "A team uses a model judge to score the tone of support replies. After a prompt change, judge scores rose 12%, but customer satisfaction didn't move. What should they do before relying on the judge?",
    options: [
      { t: "Have humans grade a sample, compare their grades with the judge's scores and reasoning, and adjust the rubric until they agree.", c: true, trap: null, w: "A judge needs calibration. Human reads confirm it measures what you think it measures." },
      { t: "Switch the judge to a larger model so its scores are more trustworthy.", c: false, trap: 1, w: "A costlier grader isn't a calibrated one. You still wouldn't know whether it agrees with people." },
      { t: "Average the scores of three different judge models to reduce noise.", c: false, trap: 9, w: "Piling judges onto an uncalibrated rubric adds cost without checking validity." },
      { t: "Ship the change, since the score went up.", c: false, trap: 6, w: "Eval blindness. The signal disagrees with the real-world outcome, and that's exactly the moment to check the ruler." }
    ]
  },
  {
    id: "q10", domain: "d4", select: 2,
    stem: "Before launch, an agent that drafts refund decisions is being evaluated. Its golden set is 300 approved refunds from last month. Which TWO additions most improve the eval? (Choose two.)",
    options: [
      { t: "Cases where the agent should refuse or escalate, such as refund requests outside policy.", c: true, trap: null, w: "Test where a behavior should NOT happen, not only where it should." },
      { t: "Adversarial inputs, such as customer messages that embed instructions to approve a full refund.", c: true, trap: null, w: "Security is a first-class metric and needs its own cases." },
      { t: "Another 500 happy-path examples sampled from approved refunds.", c: false, trap: 6, w: "More of the same coverage. It can't catch the failures that matter." },
      { t: "Replace the code check on refund amounts with a model judge, for consistency across the eval.", c: false, trap: 7, w: "Wrong grader. Where right and wrong are clear (an amount), a deterministic code check is cheaper and never drifts." }
    ]
  },
  {
    id: "q11", domain: "d4", select: 1,
    stem: "One release added \"be concise\" to a summarizer's prompt and a new retrieval reranker. Faithfulness scores on the golden set dropped. The team needs to know which change caused it. What should they do?",
    options: [
      { t: "Revert, then A/B test each change on its own against the golden set.", c: true, trap: null, w: "Change one variable at a time. That's the only way to attribute a score movement." },
      { t: "Upgrade the model to compensate for the drop.", c: false, trap: 1, w: "Scale, not strategy, and it adds a third variable to an unattributed regression." },
      { t: "Add \"be accurate and faithful to the sources\" to the prompt.", c: false, trap: 3, w: "Louder, not explicit, and yet another change mixed into the release." },
      { t: "Keep both changes and monitor production for complaints.", c: false, trap: 6, w: "Eval blindness. The eval already caught the regression; don't ship it and wait." }
    ]
  },
  {
    id: "q12", domain: "d5", select: 2,
    stem: "An accounts-payable agent reads PDF invoices from external senders and can schedule payments. Policy: payments to a new or changed bank account must never go out without verification by finance. Which TWO controls should the architect require? (Choose two.)",
    options: [
      { t: "A code-level check that blocks any payment to an account not on the verified vendor list and routes it to finance for approval.", c: true, trap: null, w: "Must → code. The limit holds no matter what the model decides." },
      { t: "Treat invoice text as untrusted data. The agent may propose payments, but a separate deterministic authorization step executes them.", c: true, trap: null, w: "Invoices are an indirect-injection path. This is a structural defense plus least privilege." },
      { t: "A system prompt line: \"Never change bank details based on invoice content.\"", c: false, trap: 4, w: "A recommended layer, but as the control on a must it's only a request." },
      { t: "Have the agent flag payments whenever its confidence is below 80%.", c: false, trap: 14, w: "A confidently fooled model reports high confidence, so the gate never fires." }
    ]
  },
  {
    id: "q13", domain: "d5", select: 1,
    stem: "A health-tech startup plans to send patient intake notes to the Claude API. The CTO says: \"Our cloud vendor is SOC 2 Type II, so we're covered.\" What is actually required?",
    options: [
      { t: "A signed BAA and a HIPAA-ready configuration, sending only the minimum necessary PHI and keeping raw PHI out of logs.", c: true, trap: null, w: "HIPAA obligations fall on the deployer: an agreement plus safeguards across the data's whole life." },
      { t: "Nothing more. SOC 2 Type II covers healthcare data handling.", c: false, trap: 16, w: "Stale fact / wrong framework. SOC 2 isn't HIPAA and doesn't replace a BAA." },
      { t: "A system prompt instructing the model to handle PHI with care.", c: false, trap: 4, w: "A prompt can't satisfy a legal requirement." },
      { t: "Log every full request, PHI included, to an encrypted bucket for audit.", c: false, trap: 5, w: "Compliance theater that creates new exposure: it logs the very data the rule protects." }
    ]
  },
  {
    id: "q14", domain: "d5", select: 1,
    stem: "A research-summary tool occasionally invents citations that don't appear in the provided sources. Which change best addresses this?",
    options: [
      { t: "Allow the model to say it doesn't know, require direct quotes from the sources for each claim, and verify in code that each quote appears in the source before output.", c: true, trap: null, w: "The standard hallucination hygiene: permission to abstain, grounding in quotes, and an output check that holds." },
      { t: "Switch to the most capable model available.", c: false, trap: 1, w: "It may lower the rate but guarantees nothing, and there's no output check." },
      { t: "Set temperature to 0.", c: false, trap: 2, w: "Repeatable isn't correct. It may reproduce the same invented citation every time." },
      { t: "Add \"NEVER fabricate citations\" in capital letters to the system prompt.", c: false, trap: 3, w: "Louder, not explicit. Nothing verifies the claims." }
    ]
  },
  {
    id: "q15", domain: "d6", select: 1,
    stem: "A hospital asks for \"an assistant that answers staff questions about our policies\". Before choosing any architecture, what is the most valuable next step?",
    options: [
      { t: "Run discovery: ask what must never happen (PHI boundaries, which clinical decisions it may influence) and what cost and latency limits apply, to find the constraint that decides the design.", c: true, trap: null, w: "The request describes a feature, not a constraint. Discovery finds the one constraint that rules out most designs." },
      { t: "Build a quick multi-agent prototype to demo what's possible.", c: false, trap: 9, w: "Gold plating before you know the constraint. The demo will flatter you." },
      { t: "Pick the most capable model to be safe on quality.", c: false, trap: 1, w: "Scale is standing in for a decision you haven't made yet." },
      { t: "Draw a detailed architecture diagram and circulate it for sign-off.", c: false, trap: null, w: "A diagram shows what you'd build, not why. With no constraint yet, there's nothing to decide against." }
    ]
  },
  {
    id: "q16", domain: "d6", select: 2,
    stem: "You're rolling off a project, and an operations team will own the assistant from now on. Which TWO items are essential in the handoff? (Choose two.)",
    options: [
      { t: "The architecture, with the rationale and cost of each major decision.", c: true, trap: null, w: "Without the reasons, the next team may unknowingly undo a decision." },
      { t: "The golden set, the eval harness, and the known failure modes with the drift signals to watch.", c: true, trap: null, w: "These let the new owners tell whether a change made things worse and catch slow degradation early." },
      { t: "A commitment to 100% accuracy that they can put in their SLA document.", c: false, trap: 13, w: "You can't promise determinism for a probabilistic system. Promise a measured rate plus an escalation path." },
      { t: "An instruction not to change the prompt without contacting you first.", c: false, trap: null, w: "The handoff should let the system outlive you, not depend on you." }
    ]
  },
  {
    id: "q17", domain: "d7", select: 1,
    stem: "Security wants a guarantee that no engineer's coding agent, in any repository across the organization, can read .env files or the secrets/ directory. Engineers should otherwise stay unblocked. What should the platform team do?",
    options: [
      { t: "Add deny rules for those paths in managed (organization) settings, which personal and project settings can't loosen.", c: true, trap: null, w: "\"Guarantee\" means enforcement, and \"across the organization\" means the managed scope. Deny beats allow." },
      { t: "Add a line to every repository's CLAUDE.md telling the agent never to open secret files.", c: false, trap: 4, w: "A note, not a wall. Instruction files are guidance." },
      { t: "Add the deny rules to each repository's checked-in project settings.", c: false, trap: 15, w: "The right rule at too narrow a scope. New repositories miss it, and each repository can edit it." },
      { t: "Ask each engineer to add the deny rules to their personal settings.", c: false, trap: 15, w: "Optional and per person. One engineer who skips it reopens the hole." }
    ]
  },
  {
    id: "q18", domain: "d4", select: 1,
    stem: "A document-classification service runs every request on the top-tier model with a 5,000-token static instruction block. Results are consumed the next morning. The team has recorded a baseline cost per document, and accuracy is well above target. Which move should come first?",
    options: [
      { t: "Cache the static instruction block and send the overnight volume through the batch API, then compare the bill with the baseline.", c: true, trap: null, w: "These are output-neutral wins that change no output. Because the output doesn't change, they can be applied together and checked on the bill alone." },
      { t: "Rewrite the instructions shorter and switch to a smaller model in the same release.", c: false, trap: 10, w: "Quality-bearing changes first, and two at once, so a regression couldn't be attributed." },
      { t: "Move to a model with a larger context window so more documents fit in each call.", c: false, trap: 1, w: "More capacity doesn't lower a bill that's already too high." },
      { t: "Lower temperature and max tokens to trim spend.", c: false, trap: 2, w: "A knob that nibbles at output tokens while ignoring the large repeated input block." }
    ]
  }
];

window.CCARP_TRAP_NAMES = {
  1: "Scale, not strategy", 2: "Knob, not design", 3: "Louder, not explicit", 4: "Prompt as guarantee",
  5: "Compliance theater", 6: "Eval blindness", 7: "Wrong-layer fix", 8: "Over-orchestration",
  9: "Gold plating", 10: "Premature optimization", 11: "Convenience over safety", 12: "Answering the stated pillar",
  13: "Promising determinism", 14: "Self-reported confidence gate", 15: "Right rule, wrong scope", 16: "Stale fact"
};
window.CCARP_DOMAINS = {
  d1: "D1 Solution Design", d2: "D2 Models & Context", d3: "D3 Integration", d4: "D4 Evaluation",
  d5: "D5 Governance & Safety", d6: "D6 Stakeholders & Lifecycle", d7: "D7 Developer Productivity"
};
