/* CCAR-F (formerly CCA-F) practice bank: 150 original questions from the learn-claude study plugin
   (github.com/maludb/learn-claude, MIT). NOT real or leaked exam items.
   Explanations reference option letters as originally written, so options are not shuffled. */
window.CCAF_QUESTIONS = [
{
"id": "d1-01-q01",
"topic": "d1-01",
"domain": "d1",
"scenario": "Customer Support Resolution Agent",
"stem": "Your support agent requests `lookup_order`, your application executes it and receives the order data, but on the next API call the model requests the identical `lookup_order` call again — and keeps doing so until the process is killed. What is the most likely defect in the agentic loop?",
"options": [
"The system prompt lacks an instruction telling the model not to repeat tool calls it has already made",
"The loop is missing an iteration cap, which would have detected the repetition and terminated cleanly",
"The tool result is never appended to the conversation history, so the model never sees that the call succeeded",
"The `lookup_order` description is too vague, causing the model to select it repeatedly over other tools"
],
"answer": 2,
"explanation": "The model has no memory between API calls; if an executed tool's result never enters the conversation history, the model cannot learn what happened and typically re-issues the same call — this is the documented symptom of a skipped append step. A is the wrong layer: no prompt wording can compensate for a result the model literally never sees. B is the named anti-pattern of treating a cap as a control mechanism — it would mask the bug, not fix it. D misdiagnoses a loop-plumbing defect as a tool-selection problem; the model is choosing the right tool, it just never receives the result."
},
{
"id": "d1-01-q02",
"topic": "d1-01",
"domain": "d1",
"scenario": "Multi-Agent Research System",
"stem": "In your research system, an engineer proposes ending the coordinator's agentic loop whenever the model's response text contains the phrase \"research complete.\" During review you're asked to recommend the correct termination mechanism. What should drive loop exit?",
"options": [
"Exit the loop when the API response's `stop_reason` field is `\"end_turn\"` rather than `\"tool_use\"`",
"Keep the text check but expand it to a list of completion phrases so paraphrases are also caught",
"Cap the loop at fifteen iterations, the maximum any research task has needed in testing",
"Instruct the model to emit a dedicated `DONE` token as its final word and check for that token each turn"
],
"answer": 0,
"explanation": "The `stop_reason` field is the single signal that drives loop control flow: `\"end_turn\"` means the model is finished, `\"tool_use\"` means it wants another tool executed. B and D are both variants of the named anti-pattern of parsing natural-language output for termination — the model can say \"research complete for phase one\" mid-task, and a magic token is still probabilistic text the model may forget or emit early. C is the other named anti-pattern: an iteration cap is only a runaway fail-safe, never the primary stop mechanism."
},
{
"id": "d1-01-q03",
"topic": "d1-01",
"domain": "d1",
"scenario": "Developer Productivity with Claude",
"stem": "Your Agent SDK productivity assistant is asked to find all callers of a deprecated function. The app sends the request, prints the assistant's opening text (\"I'll search the codebase...\"), and then exits — the model had requested a Grep call that was never run, and the task never completes. What fix repairs the loop?",
"options": [
"Raise `max_tokens` so the model can complete the entire search analysis within a single response",
"Add a system-prompt instruction telling the model to keep working until the task is fully done",
"Replace the loop with a decision tree that maps \"find callers\" queries to a fixed Grep-then-Read tool sequence",
"Check `stop_reason` after each response and, on `\"tool_use\"`, run the tool, append the result, and re-call the API"
],
"answer": 3,
"explanation": "The app is skipping the inspect step — it treats every response as final, so the loop goes silent after the model's first tool request; checking `stop_reason` and continuing the cycle (execute, append, re-call) is exactly the missing lifecycle step. A misunderstands the architecture: the model cannot execute Grep itself no matter how many tokens it has. B is a prompt-layer fix for a code-layer defect — the model already wants to continue; the app hangs up on it. C abandons model-driven decision-making for a hardcoded route, which is the documented anti-pattern, not a repair."
},
{
"id": "d1-01-q04",
"topic": "d1-01",
"domain": "d1",
"scenario": "Customer Support Resolution Agent",
"stem": "To hit the 80% first-contact-resolution target more predictably, a teammate proposes classifying each incoming ticket in application code, then programmatically executing a fixed `get_customer` → `lookup_order` → `process_refund` sequence, calling Claude only to word the final reply. What is the key architectural cost of this design?",
"options": [
"Nothing significant is lost, provided an iteration cap is added as a safety net around the fixed sequence",
"The system is no longer agentic — hardcoded routing, not model reasoning over accumulated results, chooses each next action",
"It violates the SDK requirement that every tool invocation must originate from a `stop_reason` of `\"tool_use\"` in a model response",
"Tool results can no longer be appended to the conversation history once tools are called from application code"
],
"answer": 1,
"explanation": "Model-driven decision-making over the updated conversation is the defining property of an agentic loop; a pre-configured decision tree handles only the cases its author anticipated, which is precisely what caps first-contact resolution on messy real tickets. A is irrelevant reassurance — a cap is a runaway fail-safe, not a substitute for adaptive reasoning. C invents a nonexistent rule; applications are free to call their own functions, it just isn't agentic. D is false mechanically — nothing stops you from appending results to history; the problem is the model no longer decides what happens next."
},
{
"id": "d1-01-q05",
"topic": "d1-01",
"domain": "d1",
"scenario": "Multi-Agent Research System",
"stem": "A bug once caused your research coordinator to loop indefinitely, and the postmortem asks what role a maximum-iteration limit should play going forward. The team is split between \"make it the stop mechanism\" and \"delete it entirely.\" What is the correct position?",
"options": [
"Keep `stop_reason: \"end_turn\"` as the loop's control mechanism, with a generous iteration cap purely as a fail-safe",
"Tune the cap to the typical number of iterations a research task needs, so it doubles as the primary termination signal",
"Keep the cap but add a backup check that scans the model's text for completion phrases before the cap is reached",
"Remove the cap entirely, since a correctly implemented loop always receives `\"end_turn\"` eventually"
],
"answer": 0,
"explanation": "The documented position is exactly this split role: `end_turn` drives control flow, while a hard cap is acceptable only as a fail-safe against runaway loops — never the primary stop. B converts the fail-safe into the control mechanism, the named anti-pattern, and would truncate legitimately long investigations. C layers on a second anti-pattern, parsing natural-language text for termination. D is too absolute: bugs, adversarial inputs, and pathological states are why the fail-safe exists, as this team's own postmortem demonstrates."
},
{
"id": "d1-02-q01",
"topic": "d1-02",
"domain": "d1",
"scenario": "Multi-Agent Research System",
"stem": "Your coordinator fanned a market-research request out to four subagents. Every subagent reported success and returned well-formed findings, yet the synthesized report omits two competitor segments the client explicitly asked about. Where is the root cause?",
"options": [
"The subagents underperformed on their assigned slices and should each be upgraded to a more capable model with stronger reasoning",
"The coordinator decomposed the task too narrowly, so no subtask covered those segments",
"The synthesis prompt was too brief for the model to merge four result sets correctly",
"The subagents could not fill each other's gaps because they lack a peer-to-peer channel"
],
"answer": 1,
"explanation": "This is the signature failure mode: overly narrow task decomposition lets every subagent succeed on its slice while the aggregate has coverage holes — the defect is upstream in how the coordinator scoped the subtasks. A blames execution when every subtask verifiably succeeded at what it was asked. C points at synthesis, but you cannot synthesize findings that no subtask was assigned to gather. D proposes peer-to-peer communication, which the tested hub-and-spoke pattern explicitly forbids — and gap-filling is the coordinator's job via its refinement loop, not the subagents'."
},
{
"id": "d1-02-q02",
"topic": "d1-02",
"domain": "d1",
"scenario": "Customer Support Resolution Agent",
"stem": "Your support coordinator verified a customer's identity with `get_customer` early in the conversation, then spawned a billing-investigation subagent. The subagent immediately tries to re-verify the customer because it has no idea who the customer is. Why?",
"options": [
"A bug in the Task tool is preventing the SDK's shared-memory layer from replicating to the subagent",
"The subagent's system prompt is overriding and masking the conversation history it automatically inherited from the coordinator at spawn",
"The session was not resumed with `--resume`, so verification state was lost between turns",
"Subagents run with isolated context and never inherit the coordinator's conversation history"
],
"answer": 3,
"explanation": "Context isolation is a core structural property, not a malfunction: a spawned subagent starts from its own system prompt plus whatever the coordinator explicitly writes into the Task prompt, so unpassed facts simply do not exist for it. A imagines a shared-memory layer that the architecture deliberately does not have. B presumes history was inherited and then suppressed, but no inheritance ever happens. C drags in session-resumption machinery that is irrelevant to a live spawn within one workflow."
},
{
"id": "d1-02-q03",
"topic": "d1-02",
"domain": "d1",
"scenario": "Multi-Agent Research System",
"stem": "Your coordinator launched four parallel search subagents; three completed and wrote their findings into the shared results store, while the fourth failed on a network timeout. What is the correct recovery strategy?",
"options": [
"Retry only the failed subagent — its successful siblings have already committed results that must not be duplicated",
"Re-run all four subagents so the result set is regenerated under identical conditions",
"Have one of the successful subagents pick up the failed subagent's query and message it the results directly",
"Restart the entire workflow from task decomposition to guarantee a clean state"
],
"answer": 0,
"explanation": "The tested recovery pattern is a targeted retry: the failure was isolated to one subagent, and the completed siblings have already produced side effects (writes to the results store) that a blanket re-run would repeat. B is the documented blanket-retry mistake — it duplicates committed work and side effects for no reliability gain. C violates hub-and-spoke by having subagents communicate peer-to-peer instead of routing through the coordinator. D throws away three valid results and re-executes their side effects, an even broader version of B's error."
},
{
"id": "d1-02-q04",
"topic": "d1-02",
"domain": "d1",
"scenario": "Customer Support Resolution Agent",
"stem": "To shave latency off multi-step support cases, an engineer proposes letting the order-lookup subagent forward its findings straight to the refund subagent instead of returning them to the coordinator first. Why does the tested coordinator-subagent architecture reject this?",
"options": [
"The change is acceptable as long as every direct hand-off is also written to an audit log for later review",
"Direct hand-offs are safe for read-only data like order lookups, and only tools with side effects must route through the hub",
"All inter-subagent communication must route through the coordinator — peer-to-peer hand-offs bypass the hub",
"Subagents already share the coordinator's memory, so forwarding results explicitly is redundant"
],
"answer": 2,
"explanation": "Hub-and-spoke centralizes every hand-off at the coordinator precisely so each transfer is observable and errors are handled in one place; a peer-to-peer shortcut creates an invisible, unmanaged edge in the workflow. A concedes the violation and patches it with logging — an audit trail records the bypass but restores neither centralized error handling nor routing control. B invents a read-only exemption that the pattern does not have; observability applies to all hand-offs. D is factually wrong: subagents have isolated context and no shared memory at all."
},
{
"id": "d1-02-q05",
"topic": "d1-02",
"domain": "d1",
"scenario": "Multi-Agent Research System",
"stem": "Your coordinator invokes its full roster of five subagents for every query — even one-line factual lookups — driving costs up, and separately, finished reports sometimes ship with coverage gaps nobody catches. Which pair of coordinator changes matches the tested orchestration pattern?",
"options": [
"Cap the roster at a flat three subagents for every query regardless of its complexity, and route every finished report to a human reviewer for gap detection",
"Select subagents dynamically by query complexity, and add a refinement loop that re-delegates subtasks when synthesis shows gaps",
"Let the synthesis subagent query the search subagents directly whenever it notices missing material",
"Keep the fixed five-subagent pipeline for predictability but add an iteration cap so refinement cannot run away"
],
"answer": 1,
"explanation": "These are the two named coordinator responsibilities that this system is missing: dynamic subagent selection by query complexity (stop paying for the full roster on trivial queries) and the iterative evaluate-gaps/re-delegate/re-synthesize loop (catch incomplete coverage before shipping). A substitutes an arbitrary cap for complexity-based selection and outsources gap-catching to humans instead of the coordinator's own refinement loop. C fixes gaps by breaking hub-and-spoke with peer-to-peer subagent traffic. D keeps the wasteful fixed fan-out and adds a cap that addresses neither cost nor coverage."
},
{
"id": "d1-03-q01",
"topic": "d1-03",
"domain": "d1",
"scenario": "Multi-Agent Research System",
"stem": "You've built a research coordinator whose system prompt clearly instructs it to delegate searching, analysis, and synthesis to its defined subagents. In every test run it attempts the entire job itself and never spawns a single subagent. What should you check first?",
"options": [
"Whether the delegation instruction is emphatic enough — it may need to be repeated at the end of the system prompt",
"Whether the subagents' `AgentDefinition` descriptions are too short for the coordinator to match tasks against",
"Whether the coordinator's model is capable enough to handle orchestration, or needs upgrading",
"Whether \"Task\" is present in the coordinator's `allowedTools` — without it, the coordinator cannot spawn subagents no matter what its prompt says"
],
"answer": 3,
"explanation": "Subagents are spawned via the Task tool, so a coordinator whose `allowedTools` omits \"Task\" is mechanically incapable of delegating — the classic first check when a coordinator never spawns anything. A is the wrong layer: no prompt emphasis can invoke a tool the coordinator isn't allowed to call. B would explain choosing the *wrong* subagent, not a total absence of spawning. C reaches for a model upgrade when the symptom pattern (zero delegation despite clear instructions) points squarely at tool configuration."
},
{
"id": "d1-03-q02",
"topic": "d1-03",
"domain": "d1",
"scenario": "Developer Productivity with Claude",
"stem": "Your productivity coordinator spent several turns establishing the team's testing conventions, then spawned a subagent with the prompt: \"Write tests for the parser module following the conventions we discussed.\" The subagent produced tests in a completely different style. What went wrong?",
"options": [
"The subagent's tool restrictions prevented it from reading the coordinator's conversation history",
"The coordinator should have used `fork_session` instead of the Task tool, so the subagent branched off a session that already contained the conventions",
"\"The conventions we discussed\" refers to context the subagent cannot see — the conventions must be written into the Task prompt",
"The subagent's context window was too small to hold both the conventions and the parser module"
],
"answer": 2,
"explanation": "There is no automatic inheritance of coordinator history and no shared memory; a delegation prompt that points at \"what we discussed\" points at nothing, so the actual conventions must be spelled out in the invocation prompt. A implies history access is a permission that tool restrictions gate — no such access exists to restrict. B misapplies forking, which is a session-management mechanism for exploring divergent approaches from a baseline, not the standard way to hand context to a Task-spawned subagent. D invents a capacity problem, but the conventions never reached the subagent at any size."
},
{
"id": "d1-03-q03",
"topic": "d1-03",
"domain": "d1",
"scenario": "Multi-Agent Research System",
"stem": "Your search subagents return their findings to the coordinator as flowing prose summaries. The final reports keep shipping with missing or mismatched source URLs, and the report subagent sometimes attributes claims to the wrong paper. What is the correct fix?",
"options": [
"Strengthen the synthesis subagent's prompt with instructions to be more careful and double-check every citation",
"Have subagents return findings in a structured format with separate fields for content and source metadata",
"Give the report subagent web access so it can re-locate the original source for each claim it cites",
"Have the coordinator re-fetch every referenced page and rebuild the citation list itself before synthesis"
],
"answer": 1,
"explanation": "Dumping findings as raw prose destroys provenance; the tested pattern is a structured data format that keeps content and source metadata in separate fields, so citations travel intact through every agent hand-off. A asks a downstream model to be careful with metadata that the prose already lost — no diligence recovers information that wasn't passed. C bolts on re-discovery of sources the system already had, an expensive and error-prone patch. D turns the coordinator into a redundant crawler, re-doing the search subagents' work instead of fixing the hand-off format."
},
{
"id": "d1-03-q04",
"topic": "d1-03",
"domain": "d1",
"scenario": "Developer Productivity with Claude",
"stem": "Your coordinator must audit four independent microservices for outdated dependencies. It currently spawns one audit subagent, waits for it to finish, then spawns the next, taking nearly an hour end to end. How should the coordinator cut the latency?",
"options": [
"Emit all four Task tool calls in a single response, so the four audit subagents execute concurrently",
"Merge the work into one subagent that audits all four services in sequence, eliminating spawn overhead",
"Use `fork_session` to branch four copies of the coordinator's session, one per service",
"Run four separate coordinator processes, each owning one service's audit pipeline"
],
"answer": 0,
"explanation": "Parallel subagents are spawned by issuing multiple Task tool calls in one coordinator response — the SDK executes them concurrently, which is exactly the latency fix for independent subtasks. B removes what little overhead spawning has but keeps everything serial, so the hour barely shrinks. C misuses fork-based session management, which exists for exploring divergent approaches from a shared baseline, not for dispatching parallel work. D achieves parallelism by multiplying infrastructure — four coordinators to operate and aggregate across — when a one-line change to how Tasks are emitted does the same job."
},
{
"id": "d1-03-q05",
"topic": "d1-03",
"domain": "d1",
"scenario": "Multi-Agent Research System",
"stem": "Your coordinator's delegation prompts are meticulous twenty-step procedures for each research subagent. The subagents follow them faithfully — and consistently miss relevant findings that fall outside the scripted steps. How should the delegation prompts be rewritten?",
"options": [
"Expand the procedures with more steps covering the categories of findings that were missed last time",
"Split each subtask across more subagents so each one's procedure is shorter and easier to follow",
"Add a compliance-checking subagent that verifies each procedure was executed step by step",
"Specify the goal and the quality criteria for a complete result, and let each subagent reason its own way there"
],
"answer": 3,
"explanation": "The tested guidance is that delegation prompts specify goals plus quality criteria, not step-by-step procedures — a subagent is a reasoning agent, and prescriptive checklists reproduce the coordinator's blind spots, which is exactly the observed failure. A is an arms race: every expansion covers last time's misses while creating next time's, because the script's author still can't anticipate everything. B shortens each script without removing the fundamental problem that scripts bound what subagents will look at. C verifies compliance with the procedure — the subagents already comply; the procedure itself is the defect."
},
{
"id": "d1-04-q01",
"topic": "d1-04",
"domain": "d1",
"scenario": "Customer Support Resolution Agent",
"stem": "Compliance requires that no refund is ever processed without prior identity verification. Your system prompt states this emphatically, and the rule holds in 99.5% of test runs — but an audit flags the failures as unacceptable. What is the correct remediation?",
"options": [
"Add a prerequisite gate that programmatically blocks `process_refund` from executing until `get_customer` has run and identity is verified",
"Rewrite the rule in capital letters and add few-shot examples of correct verify-then-refund sequences",
"Lower the temperature so the model follows its instructions more consistently",
"Add a nightly reconciliation job that audits refunds and flags any processed without verification"
],
"answer": 0,
"explanation": "Deterministic compliance requires programmatic enforcement: a prerequisite gate physically prevents the downstream call until verification completes, converting a strong suggestion into a guarantee. B strengthens the prompt, but prompt compliance is probabilistic — the failure rate shrinks and never reaches zero, which is what the audit rejects. C adjusts sampling, another probabilistic knob that cannot produce a guarantee. D detects violations after money has already moved; the compliance requirement is prevention, not after-the-fact discovery."
},
{
"id": "d1-04-q02",
"topic": "d1-04",
"domain": "d1",
"scenario": "Customer Support Resolution Agent",
"stem": "Human agents receiving escalations from your support agent rate them \"unactionable\" — they have no access to the agent's transcript and can't reconstruct what happened. What should the `escalate_to_human` payload contain?",
"options": [
"The complete conversation transcript, so the human has every detail the agent had",
"A structured summary with the customer ID, the diagnosed root cause, the relevant refund amount, and a recommended action",
"The session identifier, so the human can pull up the conversation in the admin console when needed",
"A prioritization score and a templated apology the human can send while investigating from scratch"
],
"answer": 1,
"explanation": "The tested handoff protocol is a structured summary carrying concrete fields — customer ID, root cause, refund amount, recommendation — so the human can act immediately without transcript access, picking up exactly where the agent stopped. A dumps raw material and forces the human to redo the agent's diagnosis; a transcript is not a handoff. C hands over a pointer to a transcript the scenario says humans can't or won't dig through, which is a cold start with extra steps. D prioritizes and placates but transfers none of the diagnostic substance the human needs."
},
{
"id": "d1-04-q03",
"topic": "d1-04",
"domain": "d1",
"scenario": "Customer Support Resolution Agent",
"stem": "A customer writes one message containing three issues: a duplicate charge, a damaged item from a recent order, and a request to pause their subscription. Your agent resolves the duplicate charge thoroughly and never mentions the other two. What workflow pattern should the agent follow instead?",
"options": [
"Reply to the duplicate charge first and ask the customer to resubmit the other issues as separate tickets",
"Handle the three issues strictly in sequence, sending the customer a separate reply as each one closes",
"Decompose the message into its three concerns, investigate them in parallel where independent, and synthesize a single unified resolution covering all three",
"Route any message containing multiple issues directly to a human agent, since multi-concern requests exceed single-agent scope"
],
"answer": 2,
"explanation": "The tested pattern for multi-concern requests is decompose, investigate in parallel where the concerns are independent, then synthesize one coherent resolution — answering only the first issue is a named failure mode. A pushes the decomposition work onto the customer, hurting first-contact resolution. B fixes the coverage problem but produces the other named failure: disjointed per-issue replies instead of a unified response. D escalates work the agent is fully capable of, abandoning the resolution target instead of applying the correct decomposition pattern."
},
{
"id": "d1-04-q04",
"topic": "d1-04",
"domain": "d1",
"scenario": "Customer Support Resolution Agent",
"stem": "Your team is deciding which support-agent behaviors need programmatic enforcement (hooks or prerequisite gates) versus system-prompt guidance. Which of the following belongs in code rather than the prompt?",
"options": [
"Opening each reply with a warm, personalized greeting",
"Formatting all monetary amounts with the currency symbol and two decimal places",
"Apologizing whenever a shipment has been delayed",
"Never executing a refund before the customer's identity has been verified"
],
"answer": 3,
"explanation": "Verification-before-refund is a financial compliance rule that must hold on every execution — prompts carry a non-zero failure rate, so deterministic ordering belongs in a prerequisite gate in code. A and C are tone and courtesy preferences where an occasional lapse causes no real harm, exactly the category prompts are appropriate for. B is a formatting convention — mildly annoying when violated, never dangerous — so it also stays in the prompt rather than justifying enforcement machinery."
},
{
"id": "d1-04-q05",
"topic": "d1-04",
"domain": "d1",
"scenario": "Customer Support Resolution Agent",
"stem": "After prerequisite gates fixed your refund-verification failures, an engineer proposes migrating every behavioral rule — greeting tone, apology phrasing, list formatting — into hooks as well, \"so everything is equally reliable.\" How should you respond?",
"options": [
"Agree — moving all rules into hooks maximizes reliability, and reliability should be uniform across the agent",
"Decline, because hooks are themselves probabilistic and therefore no more reliable than well-written prompt instructions",
"Decline — reserve hooks for rules that must never break, and keep stylistic preferences in the prompt",
"Agree, but only after the next model upgrade, when the gates themselves can be safely replaced by stronger prompt wording"
],
"answer": 2,
"explanation": "The tested design judgment is matching the mechanism to the stakes: deterministic enforcement is reserved for compliance-critical invariants, while style, tone, and formatting are exactly the cases where probabilistic prompt guidance is appropriate — hard-coding every stylistic preference is over-engineering that buys nothing. A treats uniformity as a virtue when the framework explicitly distinguishes rule categories by consequence — enforcement strength should match the consequence of a violation. B declines for a false reason: hooks are deterministic code that runs on every execution, and that guarantee is exactly what distinguishes them from probabilistic prompt compliance. D has it backwards: no model upgrade makes prompts deterministic, so gates on financial rules should never be traded back for prompt wording."
},
{
"id": "d1-05-q01",
"topic": "d1-05",
"domain": "d1",
"scenario": "Customer Support Resolution Agent",
"stem": "A new compliance policy states that any refund over $500 must be handled by a human — no exceptions, ever. Your agent has `process_refund` and `escalate_to_human` tools. How do you implement the policy?",
"options": [
"Add a prominent system-prompt rule with worked examples showing refunds over $500 being escalated",
"Add an interception hook that blocks any `process_refund` call over $500 before execution and redirects to `escalate_to_human`",
"Add a `PostToolUse` hook that detects refunds over $500 after `process_refund` returns and automatically issues a compensating reversal transaction",
"Prompt the model to rate its confidence before large refunds and escalate whenever confidence falls below a threshold"
],
"answer": 1,
"explanation": "\"No exceptions, ever\" demands a deterministic guarantee, and only code standing between the model's request and the tool's execution provides one — the interception hook blocks the over-limit call every single time regardless of what the model decides. A is probabilistic; prompt rules hold most of the time, and \"most of the time\" fails this policy. C has the timing wrong: `PostToolUse` runs after the tool executes, so the prohibited refund has already been paid out. D wraps a probabilistic mechanism in another probabilistic mechanism — self-assessed confidence guarantees nothing."
},
{
"id": "d1-05-q02",
"topic": "d1-05",
"domain": "d1",
"scenario": "Customer Support Resolution Agent",
"stem": "Your support agent queries three backend systems that return timestamps in three conventions — Unix epochs, ISO 8601 strings, and a legacy MM/DD/YYYY format. The agent occasionally miscompares dates when reasoning across systems, producing wrong \"order shipped before payment\" conclusions. What is the recommended fix?",
"options": [
"Add a system-prompt instruction: \"Always convert all timestamps to ISO 8601 before comparing them\"",
"Add an interception hook that rewrites each tool's input arguments so the backends return consistent formats",
"Provide few-shot examples in the prompt demonstrating correct conversions between the three formats",
"Add a `PostToolUse` hook that normalizes every timestamp to one canonical format before the model ever sees the results"
],
"answer": 3,
"explanation": "This is the canonical `PostToolUse` use case: transform tool results in cheap deterministic code after the tool returns, so the model receives clean, consistent data and spends its reasoning on the actual task instead of format reconciliation. A pushes the conversion into probabilistic model reasoning on every turn — token-wasteful and the source of exactly these subtle errors. B has the timing and target wrong: interception operates on the call before execution, and rewriting inputs cannot change what format a backend replies in. C is another probabilistic prompt-layer variant of A; examples reduce errors but cannot eliminate them."
},
{
"id": "d1-05-q03",
"topic": "d1-05",
"domain": "d1",
"scenario": "Customer Support Resolution Agent",
"stem": "In load testing, your prompt rule \"never reveal internal case notes to the customer\" failed in 3 out of 10,000 conversations. The team lead proposes iterating on the prompt wording until the failure count reaches zero. What should you tell them?",
"options": [
"Prompt compliance cannot be tuned to zero — move the rule into a hook that strips internal notes before they reach the customer",
"Add few-shot examples of correct refusals to the prompt, then keep re-running the load test until a complete run shows zero failures anywhere",
"Accept the 0.03% rate, since it is far below the error tolerance of a typical human support team",
"Set the temperature to 0 so the model's behavior becomes deterministic and the rule always holds"
],
"answer": 0,
"explanation": "The exam's shorthand is hooks give deterministic guarantees, prompts give probabilistic compliance — any non-zero failure rate on a confidentiality rule means the mechanism is wrong, not the wording, so the rule belongs in code that runs on every execution. B chases zero observed failures, but a clean test run only means the failures didn't sample this time. C accepts leakage on a \"must never happen\" rule; the framework treats non-zero rates as unacceptable for compliance boundaries. D misunderstands temperature: greedy sampling still varies with context and provides no behavioral guarantee."
},
{
"id": "d1-05-q04",
"topic": "d1-05",
"domain": "d1",
"scenario": "Customer Support Resolution Agent",
"stem": "You have two requirements: (1) refund attempts against closed accounts must be stopped before any money moves, and (2) the numeric status codes returned by `lookup_order` should reach the model as human-readable labels. Which hook types implement each?",
"options": [
"`PostToolUse` hooks for both — one validates the refund after it runs, the other relabels statuses",
"Interception hooks for both — one blocks the refund, the other rewrites `lookup_order`'s arguments",
"An interception hook for (1), which blocks the call pre-execution; a `PostToolUse` hook for (2), which transforms the results",
"A `PostToolUse` hook for (1) that validates each refund after it runs, and an interception hook for (2) that rewrites the arguments of every `lookup_order` call"
],
"answer": 2,
"explanation": "The two hook roles divide exactly along the execution timeline: interception hooks run before a tool executes and can block or redirect it (right for stopping the refund pre-execution), while `PostToolUse` hooks transform results after the tool returns and before the model sees them (right for relabeling status codes). A fails requirement (1): validating after execution means the refund already went out. B fails requirement (2): rewriting the call's arguments cannot change the format of the response that comes back. D reverses both mappings, blocking nothing and transforming nothing at the right moment."
},
{
"id": "d1-05-q05",
"topic": "d1-05",
"domain": "d1",
"scenario": "Customer Support Resolution Agent",
"stem": "Reviewing your `PostToolUse` normalization hook, a senior engineer argues for deleting it: \"The model is smart enough to reconcile the formats itself, and the hook is one more piece of code to maintain.\" What is the strongest counterargument?",
"options": [
"Normalization is a mechanical invariant — deterministic code handles it reliably, while in-model reconciliation costs tokens every turn",
"Current models cannot reliably perform any date or numeric format conversion whatsoever, so a deterministic hook is the only mechanism that could possibly work",
"Prompt-driven format conversion is unsupported by the Agent SDK and may break on future releases",
"The model could handle it, but only if temperature is pinned to 0 for every request in the session"
],
"answer": 0,
"explanation": "The design principle is put judgment in the model and invariants in hooks: normalization is a mechanical invariant, so doing it in deterministic code saves tokens on every single turn and eliminates the class of subtle conversion mistakes that probabilistic reasoning occasionally makes. B overclaims — models usually convert formats correctly, which is exactly why \"usually\" is the problem for an invariant. C invents an SDK restriction that does not exist; the argument is economic and reliability-based, not one of support. D is false: temperature 0 does not make model behavior a guarantee, so it rescues neither position."
},
{
"id": "d1-06-q01",
"topic": "d1-06",
"domain": "d1",
"scenario": "Developer Productivity with Claude",
"stem": "You're designing a nightly job in which your agent reviews every changed file for security, style, and performance issues, then checks how the changed pieces fit together. A teammate suggests giving the agent an adaptive planner that decides its own review steps each night. What architecture should you choose?",
"options": [
"The adaptive planner — flexibility means it can do everything a fixed sequence can and more",
"A single comprehensive prompt asking the agent to review all changes for all aspects in one pass",
"A fixed sequential pipeline: analyze each changed file individually, then run a separate cross-file integration pass",
"One subagent per review aspect — security, style, and performance — with the subagents coordinating among themselves on which files each covers"
],
"answer": 2,
"explanation": "This is the canonical prompt-chaining case: a predictable multi-aspect review whose steps can be listed in advance, where a hard-coded sequence provides the guarantee that every file and every pass provably runs. A is the named over-engineering trap — bolting an adaptive planner onto a fully knowable workflow sacrifices the fixed sequence's guarantees, and some night the planner will decide to skip something. B is the \"do the whole review at once\" distractor; the tested pattern separates per-file passes from a distinct integration pass. D adds peer-to-peer coordination between subagents, which the orchestration model routes through a coordinator, and still provides no sequence guarantee."
},
{
"id": "d1-06-q02",
"topic": "d1-06",
"domain": "d1",
"scenario": "Multi-Agent Research System",
"stem": "Your research system is assigned: \"Identify every undocumented integration and hidden dependency of this fifteen-year-old inventory platform.\" Nobody can enumerate the investigation steps in advance — each finding determines where to look next. Which decomposition strategy fits?",
"options": [
"A fixed pipeline enumerating every integration point the team already knows about as a scripted, repeatable sequence of checks",
"Dynamic adaptive decomposition: generate each next round of subtasks from what the completed steps discover",
"Prompt chaining: analyze each source file individually, then run a cross-file integration pass",
"A single deep-dive prompt asking the model to list all hidden dependencies in one comprehensive response"
],
"answer": 1,
"explanation": "Open-ended investigation is the canonical case for dynamic adaptive decomposition — the plan cannot be scripted because the step list only emerges from discoveries, so the agent must generate new subtasks from each finding. A can only check what its author anticipated, and the task is defined precisely by the dependencies nobody anticipated. C applies the right pattern to the wrong problem: per-file-then-integration chaining suits predictable multi-aspect reviews, not exploration whose targets are unknown. D asks for the entire investigation in one shot, with no mechanism for findings to direct further digging."
},
{
"id": "d1-06-q03",
"topic": "d1-06",
"domain": "d1",
"scenario": "Developer Productivity with Claude",
"stem": "A new compliance mandate requires demonstrable proof that every file in each release received a security review. Your current adaptive review agent occasionally judges some files \"low risk, no review needed\" and skips them. How do you meet the mandate?",
"options": [
"Add a prompt instruction telling the adaptive agent that all files must be reviewed without exception",
"Add a `PostToolUse` hook that logs which files the agent reviewed so skips are visible in the audit trail",
"Raise the adaptive planner's subtask budget so it has capacity to cover every file",
"Move to a fixed sequential pipeline whose orchestration code runs the security pass on every file"
],
"answer": 3,
"explanation": "When every step must provably run, the guarantee comes from a fixed pipeline: the orchestration code owns the sequence, so coverage is a structural property rather than a model decision. A leaves the skip decision with the model — an instruction reduces skips but the mandate requires proof, and prompt compliance is probabilistic. B produces evidence of skips, not prevention of them; the audit would document the violations rather than eliminate them. C misreads the failure: the agent skips files by judgment, not because it ran out of budget, so more capacity changes nothing."
},
{
"id": "d1-06-q04",
"topic": "d1-06",
"domain": "d1",
"scenario": "Multi-Agent Research System",
"stem": "Your research coordinator is midway through a scripted five-step pipeline analyzing a partner company's public filings when step two surfaces something the script never anticipated: most of the company's revenue flows through three subsidiaries the plan doesn't mention, and none of the remaining scripted steps examine subsidiaries at all. How should the coordinator proceed?",
"options": [
"Re-decompose mid-run: switch to dynamic adaptive decomposition, letting the subsidiary discovery generate the next round of subtasks",
"Finish the remaining scripted steps exactly as written, then note the subsidiaries in an appendix as a candidate for a future follow-up engagement",
"Restart the entire pipeline from step one with the same script so the run stays internally consistent",
"Ask the synthesis subagent to infer the subsidiaries' role from the findings already collected during the final write-up"
],
"answer": 0,
"explanation": "The discovery is the signal that this workflow is no longer predictable: the pre-scripted step list failed to anticipate where the substance actually lies, which is exactly when the plan must be generated from findings — dynamic adaptive decomposition, with the discovery spawning targeted new subtasks. B knowingly ships a report whose scripted scope misses most of the revenue picture; no later step ever looks at the subsidiaries. C re-runs the same blind script — repetition cannot add steps its author never wrote. D asks synthesis to conjure findings no subtask gathered; synthesis can only merge what was actually collected."
},
{
"id": "d1-06-q05",
"topic": "d1-06",
"domain": "d1",
"scenario": "Developer Productivity with Claude",
"stem": "Your platform team runs two agent workflows: (a) a weekly dependency-upgrade check whose steps are identical every week, and (b) an investigation into a flaky integration test whose cause is completely unknown. To simplify the codebase, the team wants to standardize both on a single decomposition strategy. What do you advise?",
"options": [
"Standardize both workflows on dynamic adaptive decomposition — as the more flexible strategy, it can do everything a fixed pipeline does and more",
"Standardize on a fixed pipeline — predictability and auditability matter more than flexibility",
"Use adaptive decomposition for the weekly check and a fixed pipeline for the flaky-test investigation",
"Don't standardize: prompt chaining for the predictable weekly check, dynamic adaptive decomposition for the open-ended investigation"
],
"answer": 3,
"explanation": "The selection rule is directly tested: prompt chaining for predictable work whose steps are known, dynamic decomposition for open-ended investigation whose steps emerge from discoveries — these two workflows sit on opposite sides of the line, so a single strategy is the wrong goal. A trades away the fixed pipeline's skip-proof guarantees on the weekly check for flexibility it doesn't need. B blinds the flaky-test investigation to whatever the script's author didn't anticipate — which is the entire problem, since the cause is unknown. C assigns each strategy to exactly the wrong workflow."
},
{
"id": "d1-07-q01",
"topic": "d1-07",
"domain": "d1",
"scenario": "Developer Productivity with Claude",
"stem": "Last week your agent session built a rich understanding of the payments codebase — dozens of file reads, schema dumps, and architectural conclusions. Over the weekend a major refactor restructured most of those modules. You now need the agent to continue feature work in that area. What do you do?",
"options": [
"Resume the old session with `--resume` — the accumulated context is too valuable to discard",
"Start a fresh session seeded with a structured summary of the prior session's durable conclusions",
"Fork the old session with `fork_session` so the original stays pristine while the branch absorbs the refactor",
"Resume the old session and rely on the agent to notice discrepancies between its history and the current code"
],
"answer": 1,
"explanation": "When prior tool results are largely stale, the tested judgment is fresh-with-summary: the structured summary carries forward the durable findings while the new session reads reality as it is now, instead of reasoning from file contents that no longer exist. A resumes a session whose history is full of pre-refactor reads — the agent will confidently hallucinate line numbers and edit vanished code. C forks the staleness into a new branch; forking shares a baseline, and this baseline is exactly what went bad. D assumes stale results announce themselves — a resumed agent trusts its history unless explicitly redirected."
},
{
"id": "d1-07-q02",
"topic": "d1-07",
"domain": "d1",
"scenario": "Multi-Agent Research System",
"stem": "Your research agent spent four hours building a comprehensive analysis of a 2,000-document corpus. You now want to evaluate three fundamentally different report-synthesis strategies, each starting from that same completed analysis. How do you set this up?",
"options": [
"Launch three fresh sessions, each instructed to redo the corpus analysis before applying its assigned strategy",
"Resume the original session and try the three strategies one after another in the same conversation",
"Use `fork_session` to branch three sessions off the completed analysis, one per synthesis strategy",
"Spawn three synthesis subagents that read the analysis directly out of the coordinator's memory"
],
"answer": 2,
"explanation": "This is the canonical fork case: an expensive shared baseline computed once, then divergent approaches branched from it — each fork inherits the analysis and explores independently without polluting its siblings. A is the named wasteful distractor, re-computing four hours of analysis three times over. B runs the strategies in one timeline where each attempt contaminates the context the next one sees, making the comparison unfair. D relies on subagents reading coordinator memory, which does not exist — subagent context is isolated and explicitly passed, and this is a session-management problem besides."
},
{
"id": "d1-07-q03",
"topic": "d1-07",
"domain": "d1",
"scenario": "Developer Productivity with Claude",
"stem": "Yesterday's agent session mapped your service's API layer in detail. Overnight, exactly one thing changed: the `billing` module was renamed to `invoicing`, with paths updated accordingly. The prior context is otherwise fully valid, and you want to continue today. What is the right approach?",
"options": [
"Resume with `--resume`, explicitly telling the agent about the rename so it re-analyzes the affected paths",
"Resume with `--resume` and nothing more — the agent will notice the rename on its own the first time a file read fails at runtime",
"Start a fresh session, since any change to the codebase invalidates a session's accumulated context",
"Fork the session so the pre-rename and post-rename views of the codebase can coexist in separate branches"
],
"answer": 0,
"explanation": "Resumption is right when prior context is mostly valid, and the tested discipline is to explicitly identify what changed so the agent performs targeted re-analysis of just those paths rather than trusting everything wholesale. B leans on stale references failing loudly, but a resumed agent trusts its history — it may reason from the old paths without touching the filesystem, and the trap is silent. C over-corrects: discarding a fully valid map over one rename wastes the accumulated context that resumption exists to preserve. D misuses forking, which serves divergent exploration from a shared baseline — there is nothing to explore divergently here."
},
{
"id": "d1-07-q04",
"topic": "d1-07",
"domain": "d1",
"scenario": "Multi-Agent Research System",
"stem": "Your team faces three session-management decisions: (i) continue yesterday's literature review, with all sources unchanged; (ii) compare two competing report structures built from the same completed research; (iii) continue an investigation after the data sources were re-crawled and roughly half the cached results are now outdated. Which mechanism fits each situation?",
"options": [
"(i) fork, (ii) resume, (iii) fresh session with a structured summary",
"(i) resume, (ii) fresh session per structure, (iii) fork",
"(i) fresh session with a structured summary, (ii) fork, (iii) resume with a note about the re-crawl",
"(i) resume, (ii) fork, (iii) fresh session seeded with a structured summary of prior findings"
],
"answer": 3,
"explanation": "The mapping follows the validity of the old context: unchanged sources mean prior context is still valid, so resume (i); divergent alternatives from one shared baseline is exactly what `fork_session` exists for (ii); and when cached results are largely stale, the tested preference is a fresh session seeded with a structured summary rather than resuming clutter (iii). A misuses fork for simple continuation and resume for divergent comparison. B pays to rebuild context for the comparison while forking off a half-stale session. C throws away valid context in (i) and resumes half-outdated results in (iii), where a note cannot patch that much staleness."
},
{
"id": "d1-07-q05",
"topic": "d1-07",
"domain": "d1",
"scenario": "Developer Productivity with Claude",
"stem": "Against advice, a teammate resumed a three-week-old session after a large refactor. The agent proceeded to \"fix\" a bug by confidently editing a function at line numbers that no longer exist, in a file that has since been split in two. What best explains this behavior?",
"options": [
"The `--resume` operation corrupted the transcript encoding, garbling the file contents stored in the session",
"The resumed history is full of stale tool results, which the model treats as current reality",
"The model's knowledge cutoff predates the refactor, so it cannot be aware of the new file layout",
"The teammate should have used `fork_session`, since forked branches automatically refresh their tool results on creation"
],
"answer": 1,
"explanation": "Stale tool results are the key hazard of resumption: session history preserves file reads and schemas exactly as captured, and a resumed agent trusts them as current unless explicitly directed to re-analyze — producing confident edits against a vanished reality. A invents a corruption failure; the transcript is perfectly intact, which is precisely the problem. C confuses training data with session context — the agent's picture of this codebase comes from tool results in its history, not the pretraining cutoff. D attributes a refresh behavior to forking that it does not have; a fork inherits the same stale baseline."
},
{
"id": "d2-01-q01",
"topic": "d2-01",
"domain": "d2",
"scenario": "Customer Support Resolution Agent",
"stem": "Your support agent exposes `get_customer` and `lookup_order`, each carrying a one-line description (\"Retrieves customer information\" / \"Retrieves order information\"). In production, about a third of order-status questions trigger `get_customer` first, wasting a turn before the agent recovers. What is the best first step to fix the misrouting?",
"options": [
"Add few-shot examples of correct tool calls to the system prompt so the model learns the routing pattern",
"Rewrite both descriptions to add input formats, example queries, and a boundary statement naming the sibling tool",
"Merge the two tools into a single `customer_data` tool so there is no routing decision to get wrong",
"Insert a lightweight classifier step that inspects the user question and dispatches to the correct tool programmatically"
],
"answer": 1,
"explanation": "Tool selection is driven entirely by name, description, and schema, so enriching the descriptions — including a boundary line like \"for a specific purchase, use lookup_order; this tool holds no order data\" — is the low-effort, root-cause fix. A is over-engineering that patches symptoms in the prompt instead of fixing the interface. C replaces two focused tools with a generic multi-purpose one, the opposite of good tool design. D adds routing machinery the model does not need once the descriptions disambiguate the tools."
},
{
"id": "d2-01-q02",
"topic": "d2-01",
"domain": "d2",
"scenario": "Multi-Agent Research System",
"stem": "In your research system, the analysis subagent holds `analyze_content` and `analyze_document`. You have already expanded both descriptions with input formats, examples, and boundary statements, yet the subagent still picks between them close to randomly. What should you do next?",
"options": [
"Add another paragraph of edge cases to each description until the overlap disappears",
"Set temperature to 0 for the analysis subagent so selection becomes deterministic",
"Instruct the coordinator to double-check which analysis tool the subagent chose and re-delegate on a miss",
"Rename the tools to functionally distinct names, e.g. `summarize_webpage` and `extract_pdf_findings`"
],
"answer": 3,
"explanation": "Renaming is the next rung on the remediation ladder when enriched descriptions cannot overcome near-identical names — names like analyze_content vs analyze_document stay a coin flip no matter how the descriptions read. A has been exhausted; more prose cannot separate names that sit this close. B changes sampling, not the semantic overlap causing the confusion. C adds a supervision loop that burns turns instead of fixing the interface defect."
},
{
"id": "d2-01-q03",
"topic": "d2-01",
"domain": "d2",
"scenario": "Developer Productivity with Claude",
"stem": "A teammate building an Agent SDK productivity assistant asks how Claude actually decides which of the registered MCP tools to call, wondering whether the SDK maintains a hidden routing index. What information does the model use to select a tool?",
"options": [
"Only each tool's name, description, and input schema",
"The name and description, plus an embedding-similarity index the SDK builds over past successful calls",
"The description plus telemetry about which tools succeeded most often in earlier turns of the session",
"A server-side router that pre-filters the tool list to the top candidates before the model sees them"
],
"answer": 0,
"explanation": "There is no hidden routing layer; the tool definition (name, description, input schema) is the entire interface the model selects from, which is why the exam treats misrouting as a definition defect. B invents an embedding index that does not exist in the selection path. C describes telemetry the model never receives as a selection input. D describes pre-filtering machinery that is not part of how tools are presented to the model."
},
{
"id": "d2-01-q04",
"topic": "d2-01",
"domain": "d2",
"scenario": "Customer Support Resolution Agent",
"stem": "Your support agent has a well-described internal `search_kb` tool and a `web_search` tool with a clear boundary statement, yet it keeps calling `web_search` for questions the knowledge base answers. You notice the system prompt says \"always verify answers against external documentation before responding.\" What is the right fix?",
"options": [
"Expand the `search_kb` description further, adding more example queries about internal policies",
"Remove the `web_search` tool entirely so the agent has no wrong option to choose",
"Revise the system prompt's keyword-sensitive instruction so it no longer steers the agent toward the web tool",
"Force `search_kb` via tool_choice on every single turn so the `web_search` tool can never be selected ahead of it"
],
"answer": 2,
"explanation": "Keyword-sensitive prompt instructions can override well-written descriptions — \"external documentation\" pushes the model toward web search — so prompts and tool descriptions must be audited as a pair. A polishes descriptions that are already good; the collision lives in the prompt. B removes a tool the agent legitimately needs for genuinely external questions. D is a blunt structural override that breaks the turns where web search is actually correct."
},
{
"id": "d2-01-q05",
"topic": "d2-01",
"domain": "d2",
"scenario": "Multi-Agent Research System",
"stem": "Your research system's search subagent uses one `fetch_data` tool that accepts a free-form \"request\" string and internally hits web search, an academic index, or an internal archive depending on keywords. Even after you enriched its description substantially, the subagent frequently gets the wrong backend and malformed results. Which change best addresses the root cause?",
"options": [
"Document the keyword-to-backend mapping exhaustively in the description so the model can compose the right request strings",
"Split `fetch_data` into three purpose-specific tools (web, academic, archive), each with a rigid input/output contract",
"Have the coordinator pre-classify every research task and inject the intended backend name into the subagent's prompt",
"Add a `backend` enum parameter to `fetch_data` and keep the free-form request string for everything else"
],
"answer": 1,
"explanation": "When description enrichment has been tried and a generic do-everything tool still misfires, the remediation ladder says to split it into purpose-specific tools with defined contracts. A doubles down on the failed strategy of describing away an inherently ambiguous interface. C moves the routing problem into prompt plumbing instead of fixing the tool boundary. D narrows one dimension but keeps the free-form contract that produces malformed requests, so it treats the symptom rather than the structure."
},
{
"id": "d2-02-q01",
"topic": "d2-02",
"domain": "d2",
"scenario": "Customer Support Resolution Agent",
"stem": "A customer asks for a refund on an order that is 45 days old, past the 30-day return window. `process_refund` returns the string \"operation failed\", and the agent retries the call five times with slightly different arguments before giving up. How should the tool report this failure?",
"options": [
"Return a JSON-RPC protocol error so the agent's SDK surfaces the failure at the transport layer before the model wastes turns on it",
"Return the same error but add exponential backoff guidance so the retries at least spread out",
"Return a tool result with `isError`, a business-error category, `retriable: false`, and a policy explanation",
"Return a successful result containing an empty refund object so the agent moves on without looping"
],
"answer": 2,
"explanation": "A business-rule violation can never succeed on retry, so the tool must say so structurally — retriable false plus an explanation lets the agent stop retrying and explain the policy to the customer. A is the wrong layer: the tool executed successfully and hit a business rule, which is a tool result, not a protocol error. B optimizes retries that should never happen at all. D disguises a real policy denial as success, hiding the failure and producing a wrong answer to the customer."
},
{
"id": "d2-02-q02",
"topic": "d2-02",
"domain": "d2",
"scenario": "Multi-Agent Research System",
"stem": "In your research system, two failures occur in the same run: the analysis subagent calls `extract_citations` without the required `document_id` parameter, and the search subagent's `web_search` call reaches its upstream API but gets a 503. How should each be reported under MCP?",
"options": [
"The missing parameter is a JSON-RPC protocol error because the tool never ran; the 503 is a tool result with `is_error: true` because it happened after successful invocation",
"Both are JSON-RPC protocol errors, since in both cases the tool failed to produce usable output",
"Both are `is_error` tool results, so the model can see and react to every failure uniformly",
"The missing parameter is an `is_error` tool result the model can fix; the 503 is a protocol error because the failure came from infrastructure"
],
"answer": 0,
"explanation": "The boundary is whether the tool actually executed: a malformed request never runs and is a protocol-level JSON-RPC error, while a post-invocation external failure like a 503 comes back as a tool result flagged is_error so the model can react. B wrongly pushes an executed-then-failed call down to the protocol layer. C hides a malformed request from the protocol layer where it belongs. D has the two cases exactly inverted."
},
{
"id": "d2-02-q03",
"topic": "d2-02",
"domain": "d2",
"scenario": "Customer Support Resolution Agent",
"stem": "The support agent runs `lookup_order` with a valid customer ID and a correct date range, and the query executes cleanly but matches zero orders. The current implementation returns `isError: true` with \"no orders found\", and you observe the agent retrying the search with widened date ranges for several turns. What should the tool return instead?",
"options": [
"`isError: true` with `retriable: false` so the agent stops widening the search",
"A validation-category error telling the agent to correct its date-range input and retry with adjusted parameters",
"A permission-category error so the agent escalates the empty result to a human",
"A successful result containing an empty list, clearly distinguishable from any access failure"
],
"answer": 3,
"explanation": "A query that runs and finds nothing is a valid empty result, not an error; reporting it as success with an empty list stops the pointless retry loop while keeping real access failures distinguishable. A still mislabels a valid outcome as a failure, just a non-retryable one. B invents an input problem when the parameters were correct. C escalates a perfectly normal outcome, wasting human attention on non-failures."
},
{
"id": "d2-02-q04",
"topic": "d2-02",
"domain": "d2",
"scenario": "Multi-Agent Research System",
"stem": "Your search subagent's `fetch_page` tool intermittently returns network timeouts. Currently every timeout is escalated to the coordinator, which re-delegates the whole search task, and traces show most timeouts would have succeeded on a second attempt. What is the correct recovery design?",
"options": [
"Keep escalating: the coordinator owns error handling in hub-and-spoke, so all failures must route through it",
"Have the subagent recognize the transient error category and retry locally, escalating only failures it cannot recover from",
"Mark timeouts `retriable: false` so the subagent reports partial results immediately instead of stalling",
"Move `fetch_page` into the coordinator's own tool set so timeouts happen where they can be managed centrally"
],
"answer": 1,
"explanation": "Transient errors like timeouts and 503s are retryable, and subagents should recover from them locally rather than escalating every hiccup to the coordinator. A misapplies hub-and-spoke: routing communication through the coordinator does not mean forwarding recoverable infrastructure noise. C mislabels a retryable failure as permanent and sacrifices data that a retry would have fetched. D restructures the whole tool distribution to dodge an error-handling category the subagent can handle itself."
},
{
"id": "d2-02-q05",
"topic": "d2-02",
"domain": "d2",
"scenario": "Customer Support Resolution Agent",
"stem": "You are defining the error contract for all four support-agent tools. Beyond flagging failure via `isError`, which combination of metadata gives the agent what it needs to choose between retrying, fixing its input, explaining a policy, or escalating?",
"options": [
"An HTTP status code and the raw stack trace, so the agent has full diagnostic detail",
"A severity level (info/warn/fatal) plus a correlation ID for the operations team",
"A single boolean distinguishing user-caused from system-caused failures",
"An error category, an `isRetryable` boolean, and a human-readable description"
],
"answer": 3,
"explanation": "Category plus isRetryable plus a readable description maps directly onto the four decision paths — transient errors retry, validation errors mean fix the input, business errors get explained, permission errors escalate. A dumps low-level diagnostics that don't tell the agent which recovery action applies. B is operations telemetry, useful to humans but not a recovery signal for the agent. C collapses four distinct error classes into two, losing the retry-versus-explain-versus-escalate distinction."
},
{
"id": "d2-03-q01",
"topic": "d2-03",
"domain": "d2",
"scenario": "Multi-Agent Research System",
"stem": "To \"keep every subagent unblocked,\" a teammate configured all four research subagents — search, analyze, synthesize, report — with the system's full set of 18 tools. Tool-selection accuracy has dropped noticeably, and the report-generation agent has begun calling `analyze_document` mid-report instead of formatting the findings it was handed. What is the correct redesign?",
"options": [
"Restrict each subagent to the 4–5 tools its role requires, delegating out-of-scope needs to the coordinator",
"Keep all 18 tools but reorder them so each subagent's role-relevant tools appear first in the list",
"Add a system-prompt rule to each subagent listing which of the 18 tools it is allowed to use",
"Keep the shared tool set but raise the coordinator's oversight so it rejects out-of-role tool calls after the fact"
],
"answer": 0,
"explanation": "Large tool sets measurably degrade selection (18 vs a focused 4–5), and agents holding out-of-role tools drift into misusing them — least privilege for tools plus coordinator delegation is the graded pattern. B leaves the decision-complexity problem intact; ordering is not a selection contract. C enforces a structural rule through prompt wording, which cannot guarantee compliance. D lets the misuse happen and cleans up afterward instead of preventing it."
},
{
"id": "d2-03-q02",
"topic": "d2-03",
"domain": "d2",
"scenario": "Customer Support Resolution Agent",
"stem": "Your support pipeline post-processes every agent turn by parsing the arguments of a `classify_ticket` tool call into a database; `classify_ticket` is the only tool the agent has. In about 5% of runs under `tool_choice: \"auto\"`, the model answers in plain text and the pipeline crashes on the missing tool call. How do you guarantee the model never replies in plain text?",
"options": [
"Add \"you must always call a tool\" to the system prompt so the model stops replying in text",
"Wrap the parser in a retry loop that re-sends the request whenever no tool call is present",
"Set `tool_choice: \"any\"` so the model must call some tool from its list on every single request",
"Keep `\"auto\"` but lower temperature so the plain-text completions become less likely"
],
"answer": 2,
"explanation": "\"any\" is the mode that guarantees the model calls some tool from the list, which is exactly how you get structured output reliably in an automated pipeline. A relies on prompt wording to enforce a rule that must always hold — a structural guarantee belongs in configuration, not instructions. B tolerates the failure and pays for it with retries instead of eliminating it. D reduces the frequency of plain-text replies but cannot guarantee they never occur."
},
{
"id": "d2-03-q03",
"topic": "d2-03",
"domain": "d2",
"scenario": "Multi-Agent Research System",
"stem": "Your synthesis subagent needs to check small facts constantly — dozens of times per report, and most checks are simple lookups. Today every check is delegated back through the coordinator, which adds latency, but you are wary of handing the synthesis agent the full-powered `web_search` tool after past misuse. What is the right design?",
"options": [
"Accept the latency: cross-role needs must always route through the coordinator in hub-and-spoke",
"Give the synthesis agent a narrow `verify_fact` tool for the simple checks, routing complex ones through the coordinator",
"Give it the full `web_search` tool but constrain usage with a strict system-prompt instruction limiting it to verification queries",
"Spawn a dedicated verification subagent that the synthesis agent calls directly for every check"
],
"answer": 1,
"explanation": "A genuine high-frequency, mostly-simple cross-role need is the flagship case for a narrow scoped tool — verify_fact handles the routine checks while complex verifications still go through the coordinator. A ignores that scoped access exists precisely to relieve this bottleneck. C hands over the generic tool and leans on prompt wording, which cannot enforce the constraint. D breaks hub-and-spoke by adding direct subagent-to-subagent communication and heavy machinery for a need a scoped tool solves."
},
{
"id": "d2-03-q04",
"topic": "d2-03",
"domain": "d2",
"scenario": "Customer Support Resolution Agent",
"stem": "Compliance requires that the support agent verify identity by calling `get_customer` before any other tool runs in a session. The current build states this ordering in the system prompt, but audits show roughly 2% of sessions start with `lookup_order` instead. How do you make the ordering guaranteed?",
"options": [
"Move the ordering rule to the top of the system prompt and repeat it in each tool description",
"Set `tool_choice: \"any\"` on the first turn so the model is forced to pick a tool rather than reply in text",
"Have `lookup_order` return an error whenever it is called before `get_customer` in the same session",
"Force `get_customer` by name via tool_choice on the first request, then switch to `\"auto\"`"
],
"answer": 3,
"explanation": "Forced tool selection is the mechanism for guaranteeing a specific prerequisite step runs first; switching back to auto afterward restores normal behavior. A strengthens prompt wording, which by definition cannot guarantee sequencing. B guarantees a tool call but not which tool, so lookup_order can still run first. C only guards lookup_order — every other tool can still run before get_customer, so it fails the actual compliance rule, and even on the one path it covers it burns a failed turn per violation instead of preventing it."
},
{
"id": "d2-03-q05",
"topic": "d2-03",
"domain": "d2",
"scenario": "Multi-Agent Research System",
"stem": "After you added `web_search` to the synthesis subagent \"so it could double-check sources,\" its reports started arriving late and half-synthesized: traces show it spending most of its turns running new searches instead of synthesizing the material it was handed. What does this failure mode illustrate, and what is the fix?",
"options": [
"Attention drift from an out-of-role tool; remove it and delegate search needs to the coordinator",
"The web_search description lacked a boundary statement; enrich it to say synthesis agents should use it sparingly",
"The model needs an explicit turn budget; cap synthesis at a fixed number of tool calls per report",
"The synthesis prompt is under-specified; add instructions ranking synthesis above verification in priority"
],
"answer": 0,
"explanation": "This is the canonical attention-drift failure — a synthesis agent handed a search tool starts searching instead of synthesizing — and the remedy is least privilege: withdraw the out-of-role tool and route search through the coordinator. B treats a role-scoping problem as a description problem; no boundary sentence makes the tool belong in this agent's kit. C caps the symptom while leaving the misused tool in place. D leans on prompt priority language to counter a structural mis-distribution, which prompts cannot reliably do."
},
{
"id": "d2-04-q01",
"topic": "d2-04",
"domain": "d2",
"scenario": "Developer Productivity with Claude",
"stem": "A developer on your team set up the staging-database MCP server and it works perfectly in her Claude Code sessions, but when three teammates clone the repository the server does not appear for any of them. What went wrong and where should the configuration live?",
"options": [
"The teammates have not restarted Claude Code since cloning; tools are only discovered at connection time",
"The server binary was not committed alongside the config; MCP servers must be vendored into the repo",
"The server was configured at user scope in `~/.claude.json` rather than a project-scoped `.mcp.json` committed to the repo",
"The teammates lack the server's credentials, so Claude Code silently hides the server from their tool list until valid credentials appear"
],
"answer": 2,
"explanation": "A server that works for one developer and nobody else is the classic user-scope symptom — ~/.claude.json never reaches version control, while project-scoped .mcp.json ships with the repo to every teammate. A misuses a true fact (connection-time discovery) as a diagnosis; no restart reveals config that was never shared. B is wrong because .mcp.json references how to run a server, not a vendored binary. D invents silent credential-based hiding; missing credentials produce errors, not an absent configuration."
},
{
"id": "d2-04-q02",
"topic": "d2-04",
"domain": "d2",
"scenario": "Developer Productivity with Claude",
"stem": "You are moving the team's GitHub MCP server into the project's `.mcp.json` so everyone gets it on clone, but the server needs a personal access token, and committing a token to the repository is out of the question. How should the committed configuration handle the credential?",
"options": [
"Commit `.mcp.json` with the token field blank and have each developer fill it in locally, git-ignoring their changes",
"Reference the token as `${GITHUB_TOKEN}` in `.mcp.json` and let each developer set it in their local environment",
"Keep the GitHub server at user scope, since any server requiring credentials cannot be project-scoped",
"Encrypt the token in `.mcp.json` and distribute the decryption key through the team password manager"
],
"answer": 1,
"explanation": ".mcp.json supports environment variable expansion precisely for this case — the committed file stays safe while each developer's real token lives in their local environment. A creates a permanently-dirty tracked file and a footgun where someone eventually commits their token. C is a false constraint; credentialed servers are exactly what variable expansion enables at project scope. D adds key-distribution machinery for a problem the config format already solves natively."
},
{
"id": "d2-04-q03",
"topic": "d2-04",
"domain": "d2",
"scenario": "Customer Support Resolution Agent",
"stem": "Your support agent's order-database MCP server runs as a process on the same machine as the agent. A teammate proposes configuring it with SSE transport \"so we're ready if we ever move it to its own host later.\" Which transport should you configure today, and why?",
"options": [
"SSE, because standardizing on one network transport across all servers simplifies operations",
"SSE, because database-backed servers need persistent connection streaming — a capability stdio's pipe model cannot provide",
"Either transport behaves identically for a local process, so the choice is purely a matter of team style",
"stdio, because a same-machine server needs no network hop or authentication design"
],
"answer": 3,
"explanation": "stdio is the default for a server that can live on the same machine: it runs over stdin/stdout with no network latency and no auth to design, while SSE earns its complexity only for remote hosts. A pays a real, ongoing cost for hypothetical future portability. B invents a streaming requirement; stdio serves database-backed servers fine locally. C is false — SSE adds latency and an authentication surface that stdio avoids entirely."
},
{
"id": "d2-04-q04",
"topic": "d2-04",
"domain": "d2",
"scenario": "Developer Productivity with Claude",
"stem": "Your team wants Claude Code to work with your issue tracker, a mainstream product used by thousands of companies. An engineer has sketched a two-week plan to write a custom MCP server for it, arguing the team will want control over the implementation. What should you recommend?",
"options": [
"Adopt an existing community MCP server for the tracker, reserving custom server development for genuinely team-specific workflows",
"Build the custom server: owning the integration code is worth two weeks for a tool the team uses daily",
"Skip MCP and have the agent drive the tracker's REST API directly through Bash and curl",
"Build a thin custom wrapper around the community server so the team controls the interface without owning the whole implementation"
],
"answer": 0,
"explanation": "For standard integrations the graded answer is buy before build — prefer existing community MCP servers and write custom ones only for workflows unique to your team. B spends two weeks re-implementing a solved problem and takes on permanent maintenance. C abandons the standard that exists precisely so integrations plug into any compliant client. D adds a wrapper layer with no identified team-specific need to justify it."
},
{
"id": "d2-04-q05",
"topic": "d2-04",
"domain": "d2",
"scenario": "Customer Support Resolution Agent",
"stem": "Traces show your support agent starting each session by firing exploratory tool calls — listing tables, sampling rows, probing endpoints — just to learn what the order database contains before it can address the customer's question. Which MCP capability best eliminates this warm-up cost?",
"options": [
"A longer system prompt embedding a hand-written summary of the database layout",
"Increasing the server's tool count so each table gets a dedicated query tool the agent can recognize",
"MCP resources that expose the database schema as a readable content catalog",
"Caching the exploratory calls from the agent's first session and replaying the results into every subsequent session"
],
"answer": 2,
"explanation": "MCP resources exist for exactly this: exposing catalogs like schemas, directory trees, or issue lists so the agent starts with a structured map rather than burning turns exploring. A hand-maintains in a prompt what the server can publish authoritatively and keep current. B inflates the tool count, which degrades selection reliability without conveying structure. D bolts caching machinery onto the exploratory anti-pattern instead of replacing it."
},
{
"id": "d2-05-q01",
"topic": "d2-05",
"domain": "d2",
"scenario": "Developer Productivity with Claude",
"stem": "Working in an unfamiliar legacy codebase, your agent must find every call site of `calculateTax()`, including calls buried inside wrapper modules that re-export it under the same name. Which built-in tool does this job?",
"options": [
"Glob, matching `**/*tax*` to collect the files most likely to contain the calls",
"Read, loading the module that defines `calculateTax()` to see what references it",
"Bash, walking the directory tree and collecting candidate files for inspection",
"Grep, searching file contents across the codebase for the function name"
],
"answer": 3,
"explanation": "Finding call sites is a content search — Grep scans file contents for a pattern, which is exactly how you trace a function's usage across wrapper modules. A matches file names, and call sites live in files whose names say nothing about tax. B shows the definition, not the callers scattered across the codebase. C reinvents file discovery without searching contents, leaving the actual call sites unfound."
},
{
"id": "d2-05-q02",
"topic": "d2-05",
"domain": "d2",
"scenario": "Developer Productivity with Claude",
"stem": "A developer new to Claude Code asks why the Edit tool requires a unique snippet of existing text to anchor a change, rather than accepting a line number like every diff tool they have used. What is the reason for this design?",
"options": [
"Models miscount lines across large files, so a line-number edit can land a few lines off and corrupt code",
"Line numbers change whenever any earlier edit is applied, so anchoring by text avoids recomputing offsets between edits",
"Unique-string matching lets Edit apply the same change to every occurrence in one call, which line numbers cannot express",
"The tool never reads the file before editing, so text anchors are the only addressing scheme available to it"
],
"answer": 0,
"explanation": "The design guards against the model's weakness: language models miscount lines over large contexts, so a \"line 412\" edit can silently land in the wrong place, whereas a unique text anchor cannot drift. B describes a real property of line numbers but is not the model-reliability rationale behind this tool's design. C is backwards — Edit's unique-match requirement means it targets one occurrence, not all of them. D is false; the constraint comes from model reliability, not from the tool being unable to read files."
},
{
"id": "d2-05-q03",
"topic": "d2-05",
"domain": "d2",
"scenario": "Developer Productivity with Claude",
"stem": "Your agent tries to Edit a legacy validation module, anchoring on `return true`, and the call fails because that exact string appears dozens of times in the file. Attempts to grow the anchor keep colliding with the file's heavily copy-pasted structure. What is the correct fallback?",
"options": [
"Switch to a line-number-based edit, since text anchoring has been exhausted in this file",
"Read the full file, then Write the complete modified version back",
"Use Bash with sed to modify the target line in place, bypassing the uniqueness check",
"Split the file into smaller modules first so each fragment contains the anchor only once"
],
"answer": 1,
"explanation": "When no unique anchor exists, the documented fallback is Read the whole file and Write the complete modified contents. A reaches for line-number addressing, which Edit deliberately avoids because models miscount lines. C bypasses the safety property rather than working within the toolset, and a sed pattern hits the same non-uniqueness problem. D restructures production code purely to satisfy an editing tool — massive collateral change for a one-line fix."
},
{
"id": "d2-05-q04",
"topic": "d2-05",
"domain": "d2",
"scenario": "Developer Productivity with Claude",
"stem": "Before planning a Python 3 migration, your agent needs the full inventory of `.py` files across a large monorepo. Context budget is tight, so the inventory step must not pull any file contents into the window. Which tool fits?",
"options": [
"Grep for `import` statements, since every Python file contains at least one",
"Read the repository root directory listing and recurse manually into each package",
"Glob with a `**/*.py` pattern, which matches file paths without reading any contents",
"Bash running `cat` over candidate files to confirm each one is really Python"
],
"answer": 2,
"explanation": "Matching files by extension is a name/path job — Glob does it without touching file contents, making it fast and token-cheap, exactly what the constraint demands. A searches contents for a pattern many Python files could lack and spends tokens the task forbids. B turns one pattern match into a slow manual crawl of directory listings. D reads file contents wholesale, the very cost the question rules out."
},
{
"id": "d2-05-q05",
"topic": "d2-05",
"domain": "d2",
"scenario": "Developer Productivity with Claude",
"stem": "Your agent must diagnose a payment bug somewhere in a sprawling legacy service nobody on the team understands. Its first move is to Read every file in the payments module \"to build complete context,\" and it exhausts the context window before forming a hypothesis. What exploration strategy should it use instead?",
"options": [
"Read only the module's largest files first, since core logic usually concentrates there",
"Glob the module for all source files and read them in dependency order",
"Ask Bash to dump the module's directory tree, then read files alphabetically until the bug appears",
"Grep for the error message and entry points, then Read files selectively, following imports"
],
"answer": 3,
"explanation": "The graded pattern is incremental understanding: Grep to locate entry points and relevant symbols, then selective Reads that trace imports and execution flow — comprehension grows without flooding the window. A still bulk-reads on a size heuristic that has no connection to where the bug lives. B fixes the ordering but keeps the read-everything cost that exhausted the context. C is bulk reading with an arbitrary ordering, the same anti-pattern wearing a different sort key."
},
{
"id": "d3-01-q01",
"topic": "d3-01",
"domain": "d3",
"scenario": "Code Generation with Claude Code",
"stem": "A senior engineer added a rule — \"all data access must go through the repository layer, never raw SQL in controllers\" — and Claude Code follows it flawlessly on her laptop. Every other developer on the team reports that Claude generates raw SQL in controllers anyway. What should she do?",
"options": [
"Reword the rule with stronger, more explicit language so Claude weighs it more heavily",
"Move the rule from her `~/.claude/CLAUDE.md` into the project's root CLAUDE.md and commit it",
"Ask each teammate to run `/memory` at the start of every session to reload instructions",
"Convert the rule into a slash command that teammates invoke before generating code"
],
"answer": 1,
"explanation": "The symptom — works for one developer, ignored everywhere else — means the rule lives at user level, which never enters version control; committing it at project level ships it with every clone. A is wrong because the root cause is placement, not phrasing. C is wrong because `/memory` only shows what is loaded; it cannot load a file that was never in the repo. D is wrong because a slash command is an on-demand prompt, not a standing convention, and still would not exist in teammates' clones unless committed anyway."
},
{
"id": "d3-01-q02",
"topic": "d3-01",
"domain": "d3",
"scenario": "Code Generation with Claude Code",
"stem": "A developer wants Claude Code to always answer tersely and follow his personal commit-message style across every repository he works in — but he must not impose these quirks on the twelve other engineers sharing those repositories. Where do these instructions belong?",
"options": [
"In each repository's `.claude/CLAUDE.md`, since that directory is for configuration",
"In a `.claude/rules/` file with a glob pattern matching all files",
"In each repository's root CLAUDE.md, with the file added to `.gitignore`",
"In `~/.claude/CLAUDE.md` in his home directory"
],
"answer": 3,
"explanation": "User-level `~/.claude/CLAUDE.md` applies to every project on his machine and is never committed, so teammates are untouched — exactly the scoping he needs. A is wrong because `.claude/CLAUDE.md` is project-level and version-controlled, pushing his quirks onto the team. B is wrong because path rules scope by file pattern within a project, not by person, and would also be committed. C is wrong because gitignoring the canonical project memory file would fight teammates who need a real shared CLAUDE.md at that path."
},
{
"id": "d3-01-q03",
"topic": "d3-01",
"domain": "d3",
"scenario": "Claude Code for Continuous Integration",
"stem": "A platform team runs Claude Code in CI for review and test generation. Their project CLAUDE.md has grown to 1,400 lines covering database conventions, API security, frontend styling, and release procedures, and every automated run pays the token cost of the entire file. How should they restructure it?",
"options": [
"Keep the root CLAUDE.md lean and split topics into files under `.claude/rules/`",
"Move the entire contents into a skill so it loads on demand instead of at session start",
"Copy the relevant sections into a directory-level CLAUDE.md inside every folder they apply to",
"Delete everything except the ten most important rules and rely on PR review to catch the rest"
],
"answer": 0,
"explanation": "The graded remedy for a monolithic CLAUDE.md is a lean root file plus `@import` and focused `.claude/rules/` files, so each run carries only what applies. B is wrong because team-wide standards must be reliably active, and burying all of them in one on-demand skill means they may never load. C is wrong because duplicating sections across directories multiplies maintenance without reducing what loads. D is wrong because it discards working conventions instead of reorganizing them — the problem is structure, not the rules' existence."
},
{
"id": "d3-01-q04",
"topic": "d3-01",
"domain": "d3",
"scenario": "Code Generation with Claude Code",
"stem": "After a repo reorganization, Claude Code starts generating code that violates conventions the team is certain they wrote down somewhere. Before editing any configuration, a developer wants to see exactly which memory files are active in the current session. What should she run?",
"options": [
"`/config`, which lists all active configuration sources",
"A grep across the repository for every file named CLAUDE.md",
"The `/memory` command, which shows which memory files are loaded",
"A fresh session with a verbose flag to print instructions as they load"
],
"answer": 2,
"explanation": "`/memory` is the diagnostic for configuration drift — it shows precisely which memory files the current session loaded, replacing guesswork. A is wrong because `/config` deals with settings, not which CLAUDE.md content is active. B is wrong because grepping finds files on disk, not which ones the session actually loaded (a user-level file would not even be in the repo). D is wrong because restarting with extra flags is speculation when a purpose-built inspection command exists."
},
{
"id": "d3-01-q05",
"topic": "d3-01",
"domain": "d3",
"scenario": "Claude Code for Continuous Integration",
"stem": "A team adopts a new standard — every SQL migration must include a tested rollback — that must be active in every session for every engineer and every CI run. Their root CLAUDE.md is already large, and the tech lead is wary of growing it further. What is the best placement?",
"options": [
"Each engineer's `~/.claude/CLAUDE.md`, with the standard documented in the onboarding wiki so new hires copy it",
"A focused database-conventions file under `.claude/rules/`, committed to the repo and referenced from the project CLAUDE.md",
"A `.claude/skills/` skill that engineers invoke whenever they write a migration",
"Appended to the root CLAUDE.md, since splitting files risks the rule being missed"
],
"answer": 1,
"explanation": "A committed, focused rules file keeps the root lean while the convention still ships to every clone and CI run — modular organization is exactly for this. A is wrong because user-level files never reach version control and depend on humans remembering to copy them. C is wrong because a skill loads only when invoked, and a standard that must always hold cannot depend on someone remembering to invoke it. D works but trades away the modular structure the team needs as the file keeps growing — the always-loaded guarantee does not require a monolith."
},
{
"id": "d3-02-q06",
"topic": "d3-02",
"domain": "d3",
"scenario": "Code Generation with Claude Code",
"stem": "A team lead writes a `/scaffold-endpoint` prompt that generates API endpoints following the team's controller, validation, and test layout. She wants every developer to have it automatically the moment they clone the repository. Where should the command live?",
"options": [
"A markdown file in the project's `.claude/commands/` directory, committed to version control",
"A markdown file in `~/.claude/commands/`, with setup docs telling teammates to copy it",
"A section in the project CLAUDE.md describing the scaffolding steps for Claude to follow",
"A folder in her home directory's skills path so it loads on demand for everyone"
],
"answer": 0,
"explanation": "Project-scoped commands in `.claude/commands/` travel through version control, so every clone gets `/scaffold-endpoint` with zero setup. B is wrong because the home directory is user-scoped — teammates only get it if they manually copy it, which is exactly what she wants to avoid. C is wrong because CLAUDE.md prose loads every session and is not an invocable command. D is wrong because anything under her home directory stays on her machine regardless of how it loads."
},
{
"id": "d3-02-q07",
"topic": "d3-02",
"domain": "d3",
"scenario": "Developer Productivity with Claude",
"stem": "A team ships a `/security-audit` skill that inspects code and reports vulnerabilities. During a demo it \"helpfully\" rewrote a vulnerable function while auditing it, alarming the security team, who require that audits can never modify the codebase. What is the correct fix?",
"options": [
"Add \"you are strictly read-only; never modify files\" to the top of SKILL.md",
"Require developers to run the skill only inside plan mode",
"Set `allowed-tools` in the SKILL.md frontmatter to permit only read operations",
"Set `context: fork` so the skill's changes happen in an isolated context"
],
"answer": 2,
"explanation": "`allowed-tools` is a structural restriction — a skill without write tools cannot modify files no matter how it reasons, which is the guarantee the security team asked for. A is wrong because prompt instructions are probabilistic; the demo already showed instructions being overridden by helpfulness. B is wrong because it depends on every developer remembering a manual step rather than enforcing the constraint in the skill itself. D is wrong because `context: fork` isolates verbose output from the main conversation; it does not remove the ability to write files."
},
{
"id": "d3-02-q08",
"topic": "d3-02",
"domain": "d3",
"scenario": "Developer Productivity with Claude",
"stem": "A `/deps-report` skill crawls lockfiles, reads dozens of manifests, and greps usage sites before producing a one-page summary. Developers complain that after running it, the main conversation is so flooded with file dumps and search output that Claude loses track of the task they were working on. What configuration change fixes this?",
"options": [
"Add \"summarize your findings concisely\" instructions to the skill's markdown body",
"Tell developers to run `/compact` immediately after the skill finishes",
"Move the skill's instructions into CLAUDE.md so they are loaded once at session start",
"Set `context: fork` in the skill's SKILL.md frontmatter"
],
"answer": 3,
"explanation": "`context: fork` is the structural fix — the messy reads and greps happen in a background context and only the final report enters the main conversation, so the primary context is never polluted. A is wrong because the final summary was already concise; the problem is the intermediate exploration output, which instructions do not remove from context. B is wrong because compacting after the fact recovers space but has already degraded the session and adds a manual step. C is wrong because where the instructions load has nothing to do with where the skill's runtime output lands."
},
{
"id": "d3-02-q09",
"topic": "d3-02",
"domain": "d3",
"scenario": "Code Generation with Claude Code",
"stem": "A team's `/migrate-schema` skill needs a target database name and a migration direction, but developers keep invoking it bare, forcing a round of clarifying questions before any work starts. Which frontmatter option addresses this directly?",
"options": [
"`context: fork`, so the clarification happens outside the main conversation",
"`argument-hint`, which documents the expected parameters and prompts for missing ones",
"`allowed-tools`, restricting the skill until arguments are supplied",
"A CLAUDE.md rule stating the default database and direction"
],
"answer": 1,
"explanation": "`argument-hint` exists precisely for this — it documents what the skill expects and prompts the developer for required arguments they forgot to supply. A is wrong because forking the context changes where output goes, not whether arguments arrive. C is wrong because `allowed-tools` governs which tools the skill may use, not its invocation parameters. D is wrong because silently assuming a default database for schema migrations papers over missing input on a destructive operation rather than eliciting it."
},
{
"id": "d3-02-q10",
"topic": "d3-02",
"domain": "d3",
"scenario": "Developer Productivity with Claude",
"stem": "A team's CLAUDE.md contains a 300-line procedure for their quarterly license-compliance sweep. It is executed four times a year, yet every session all year pays its token cost, and developers say unrelated sessions have started referencing license rules. What is the right restructuring?",
"options": [
"Move the procedure into a skill under `.claude/skills/`",
"Keep it in CLAUDE.md but compress the wording to reduce its token footprint",
"Move it to a `.claude/rules/` file with a `paths` glob so it loads conditionally",
"Move it to the team lead's `~/.claude/commands/` since only they run the sweep"
],
"answer": 0,
"explanation": "The decision rule is CLAUDE.md for always-loaded universal standards, skills for occasional workflows — a quarterly procedure is the textbook on-demand case. B is wrong because compressing a rarely-relevant procedure still charges every session for content that almost never applies. C is wrong because path rules key on file types being edited, and a compliance sweep is a workflow, not a file-type convention. D is wrong because burying a team procedure in one person's home directory makes it invisible to the team and lost when they leave."
},
{
"id": "d3-03-q11",
"topic": "d3-03",
"domain": "d3",
"scenario": "Code Generation with Claude Code",
"stem": "A product team maintains distinct conventions for React components, API handlers, and database models. Test files sit alongside the code they cover in nearly every directory of the repository. How should the team wire up these conventions so each applies only to the right files?",
"options": [
"Place a directory-level CLAUDE.md in each folder describing that folder's conventions",
"Put all four convention sets in the root CLAUDE.md so nothing is ever missed",
"Create one skill per convention set for developers to invoke as needed",
"Create rule files in `.claude/rules/`, each with YAML frontmatter glob patterns matching its file type"
],
"answer": 3,
"explanation": "Conventions here are keyed to file type, not location — glob-pattern `paths` frontmatter loads each rule file exactly when a matching file is edited, and one test rule covers colocated tests everywhere. A is wrong because tests living in nearly every directory would force the same CLAUDE.md to be duplicated dozens of times. B is wrong because always loading all four sets wastes tokens and invites conventions bleeding into the wrong file types. C is wrong because conventions must apply automatically during edits, not wait for a developer to remember an invocation."
},
{
"id": "d3-03-q12",
"topic": "d3-03",
"domain": "d3",
"scenario": "Developer Productivity with Claude",
"stem": "A developer is writing a rule file in `.claude/rules/` that should activate only when Claude Code is editing Terraform files, and stay out of context the rest of the time. What mechanism controls this conditional activation?",
"options": [
"An `allowed-tools` entry in the rule file restricting it to Terraform operations",
"An `@import` statement in CLAUDE.md wrapped in a conditional expression",
"A `paths` field in the rule file's YAML frontmatter containing glob patterns like `**/*.tf`",
"Placing the rule file inside the infrastructure directory so proximity scopes it"
],
"answer": 2,
"explanation": "Rule files support a frontmatter `paths` field of glob patterns, and the file loads only when edited files match — that is the conditional-loading mechanism. A is wrong because `allowed-tools` is skill frontmatter governing tool access, not rule activation. B is wrong because `@import` includes files unconditionally; there is no conditional expression syntax around it. D is wrong because rule files belong in `.claude/rules/` and scope by pattern — moving the file into a code directory confuses pattern scoping with location scoping."
},
{
"id": "d3-03-q13",
"topic": "d3-03",
"domain": "d3",
"scenario": "Developer Productivity with Claude",
"stem": "Two engineers are debating configuration scoping. One claims a directory-level CLAUDE.md and a `.claude/rules/` file with `paths` frontmatter are interchangeable — both just \"limit where conventions apply\" — so the team should pick whichever feels tidier. What is the actual distinction that decides which mechanism fits a given convention?",
"options": [
"Rule files are evaluated before the session starts, so they load faster than directory files",
"Directory CLAUDE.md files stay on one developer's machine, while rule files are committed and shared with the whole team",
"A rule file may carry only a single glob pattern, so any convention spanning several file types requires a directory CLAUDE.md instead",
"A directory CLAUDE.md scopes by location — its folder — while `paths` globs scope by file pattern wherever matching files live"
],
"answer": 3,
"explanation": "The two mechanisms scope on different dimensions: a directory-level CLAUDE.md covers everything under its folder, while frontmatter `paths` globs activate a rule for matching file types anywhere in the repository — so globs win when a file type is spread across directories, and a directory file fits when conventions are genuinely location-bound. A is wrong because no pre-session glob evaluation exists; load speed is an invented differentiator. B is wrong because both mechanisms live in the repository and travel through version control — neither is user-scoped. C is wrong because the `paths` field holds a list of glob patterns; a one-glob limit is a made-up restriction."
},
{
"id": "d3-03-q14",
"topic": "d3-03",
"domain": "d3",
"scenario": "Code Generation with Claude Code",
"stem": "Developers notice Claude Code applying the team's React styling conventions — component naming, CSS module patterns — while editing SQL migration scripts, occasionally producing bizarre suggestions. All conventions currently live in one always-loaded file. What is the graded fix?",
"options": [
"Add \"these rules apply only to React files\" preambles to each convention section",
"Split the conventions into `.claude/rules/` files scoped by frontmatter glob patterns",
"Move frontend and database code into separate repositories with separate CLAUDE.md files",
"Have developers enter plan mode before editing migrations so conventions are reviewed first"
],
"answer": 1,
"explanation": "Cross-contamination is the known failure of always-loaded conventions; path-scoped rules make styling rules simply absent while editing SQL, which no amount of instruction weighting matches. A is wrong because the irrelevant content still occupies context and prose disclaimers are followed only probabilistically. C is wrong because splitting repositories is a massive structural cost for a problem conditional loading solves in-place. D is wrong because plan mode governs when edits happen, not which conventions load into context."
},
{
"id": "d3-03-q15",
"topic": "d3-03",
"domain": "d3",
"scenario": "Developer Productivity with Claude",
"stem": "In a monorepo, the `packages/mobile/` package has conventions that apply to everything inside that one folder, while `*.test.ts` files scattered across all packages share a separate set of testing conventions. Which combination scopes both correctly?",
"options": [
"Two `.claude/skills/` entries, one per convention set, invoked when relevant",
"Directory-level CLAUDE.md files copied into every package, each containing both the mobile and testing convention sets",
"A CLAUDE.md inside `packages/mobile/` plus a `.claude/rules/` file with a `**/*.test.ts` glob",
"Both sets in the root CLAUDE.md, since the root reaches every file anyway"
],
"answer": 2,
"explanation": "The two convention sets scope on different dimensions — location and file type — so each gets the matching mechanism: a directory CLAUDE.md scopes by folder, a `paths` glob scopes by pattern. A is wrong because standing conventions should load automatically, not wait to be invoked as workflows. B is wrong because copying mobile conventions into non-mobile packages misapplies them and duplicates the test rules everywhere. D is wrong because always loading both sets spends tokens on rules irrelevant to most edits and invites cross-contamination."
},
{
"id": "d3-04-q16",
"topic": "d3-04",
"domain": "d3",
"scenario": "Code Generation with Claude Code",
"stem": "An architect asks Claude Code to break a monolithic order-processing service into three microservices. The work spans dozens of files, the service boundaries are debatable, and hidden coupling through shared database tables is suspected. How should the session begin?",
"options": [
"Direct execution, extracting one service at a time and fixing coupling as it surfaces",
"Direct execution first, switching into plan mode if the work turns out to be complicated",
"Spawning parallel sub-agents immediately, one per candidate microservice",
"Plan mode, mapping dependencies before presenting a design for approval"
],
"answer": 3,
"explanation": "Every stated signal — dozens of files, debatable boundaries, suspected hidden coupling — is a plan-mode signal: explore safely, understand dependencies, and commit to a design before edits land. A is wrong because editing before mapping the coupling is how shared-table surprises break extracted services midway. B is wrong because the complexity is already stated in the scenario, not hypothetical — waiting to discover it is the classic trap. C is wrong because parallelizing implementation before service boundaries are even decided multiplies rework rather than reducing it."
},
{
"id": "d3-04-q17",
"topic": "d3-04",
"domain": "d3",
"scenario": "Developer Productivity with Claude",
"stem": "A stack trace pinpoints a crash to a missing null check on one parameter of one function. The fix is a single guard clause with an early return, and the developer wants it done between meetings. A teammate insists all changes should start in plan mode \"as a best practice.\" Who is right, and why?",
"options": [
"The teammate — planning first is always safer regardless of task size",
"The developer — a well-scoped single-file fix should be executed directly",
"Neither — the Explore sub-agent should first survey the codebase for similar missing checks",
"The teammate — but a lightweight written design doc can substitute for plan mode"
],
"answer": 1,
"explanation": "Adding a single validation check is the official example of a direct-execution task; the blast radius is minimal, the diagnosis is already in hand, and over-planning trivial work is its own mistake. A is wrong because forcing plan mode onto contained fixes wastes effort — mode selection follows stated scale, not blanket policy. C is wrong because a codebase-wide survey expands a two-line fix into an open-ended project nobody asked for. D is wrong because a design document for a guard clause is over-planning under a different name."
},
{
"id": "d3-04-q18",
"topic": "d3-04",
"domain": "d3",
"scenario": "Developer Productivity with Claude",
"stem": "While investigating a payment-flow redesign in plan mode, Claude runs waves of greps and reads dozens of files, and the developer notices the session is now dominated by raw search output and dead-end file dumps — the architectural discussion is getting crowded out. What is the right mechanism to fix this?",
"options": [
"Delegate the discovery work to the Explore sub-agent",
"Run `/compact` after each wave of searches to reclaim context space",
"Abandon plan mode and start implementing, since exploration is clearly too expensive",
"Restart the session and paste in only the files known to matter"
],
"answer": 0,
"explanation": "The Explore sub-agent exists for exactly this — noisy discovery runs in an isolated context and the main session receives only distilled findings, preserving room for the architectural decisions. B is wrong because repeatedly compacting mid-investigation risks losing nuance and treats the flood after it arrives instead of preventing it. C is wrong because the redesign still needs the investigation; skipping it trades context pressure for uninformed edits. D is wrong because \"files known to matter\" is precisely what the investigation is supposed to determine."
},
{
"id": "d3-04-q19",
"topic": "d3-04",
"domain": "d3",
"scenario": "Code Generation with Claude Code",
"stem": "A team used plan mode to investigate a cross-cutting logging overhaul, and the resulting design was reviewed and approved. One engineer argues the implementation must also happen in plan mode \"to stay consistent\"; another says plan mode was wasted since they will execute directly anyway. What is the graded workflow?",
"options": [
"Remain in plan mode through implementation so every edit gets pre-approval",
"The second engineer is right — the investigation should have been direct execution from the start",
"Use plan mode for investigation and design approval, then switch to direct execution to implement the approved plan",
"Re-enter plan mode before each file is modified to re-validate the design"
],
"answer": 2,
"explanation": "The modes combine: plan mode buys safe exploration and an approved design, and direct execution then implements it — they are phases of one workflow, not rivals. A is wrong because plan mode's purpose is deciding what to change; once the design is approved, gating every edit adds friction without new information. B is wrong because a cross-cutting overhaul with review-worthy design decisions is exactly what plan mode is for. D is wrong because re-planning per file discards the approved design's value and turns implementation into ceremony."
},
{
"id": "d3-04-q20",
"topic": "d3-04",
"domain": "d3",
"scenario": "Code Generation with Claude Code",
"stem": "Four tasks land in a team's queue. Based on the signals stated in each, which one most clearly warrants starting in plan mode?",
"options": [
"Correct a misspelled word in a user-facing error message flagged during this morning's localization review",
"Add a bounds check to the single function named in yesterday's crash report stack trace",
"Rename a poorly named local variable inside a single utility function to something more descriptive",
"Migrate auth from server-side sessions to JWTs across middleware, API routes, and two services"
],
"answer": 3,
"explanation": "The migration carries every plan-mode signal — architectural implications, many files, cross-service impact, and multiple valid approaches to token handling — so exploration and an approved design come first. A is wrong because a copy fix is the definition of a contained direct-execution change. B is wrong because a diagnosed one-function guard is the official style of example for direct execution. C is wrong because a local rename has effectively zero blast radius and gains nothing from a planning phase."
},
{
"id": "d3-05-q21",
"topic": "d3-05",
"domain": "d3",
"scenario": "Code Generation with Claude Code",
"stem": "A developer needs Claude Code to convert legacy log lines into a structured event format. She has written three paragraphs describing the field mappings, yet each generation run interprets the rules slightly differently — timestamps formatted one way on Monday, another on Tuesday. What change most reliably fixes the inconsistency?",
"options": [
"Expand the prose into a longer, more precise specification covering every field mapping and timestamp variant individually",
"Repeat the formatting rules in CLAUDE.md so they load on every session",
"Replace most of the prose with a few concrete input/output example pairs of real log lines",
"Ask Claude to interview her about the requirements before converting anything"
],
"answer": 2,
"explanation": "Worked example pairs are the most effective way to communicate an expected transformation — an example pins the pattern down where prose gets reinterpreted on every run. A is wrong because longer descriptions of formatting rules are exactly the input that keeps drifting; more prose amplifies the failure mode. B is wrong because relocating ambiguous prose changes where it loads, not how it is interpreted. D is wrong because the interview surfaces unknown requirements — here the requirement is known and merely being communicated poorly."
},
{
"id": "d3-05-q22",
"topic": "d3-05",
"domain": "d3",
"scenario": "Developer Productivity with Claude",
"stem": "A developer has spent an hour in a loop: Claude produces a rate limiter, the developer replies \"still not working right,\" Claude adjusts something, and the cycle repeats without converging. What restructuring of the iteration gives the fastest convergence?",
"options": [
"Write a test suite first, then iterate by sharing the specific failing assertions each cycle",
"Start a fresh session for each attempt so earlier failed reasoning stops contaminating the context",
"Collect every complaint from the hour into one comprehensive message describing all the ways it is wrong",
"Switch to plan mode so the limiter design is approved before another implementation attempt"
],
"answer": 0,
"explanation": "\"Still not working right\" is unactionable; test-driven iteration turns each cycle into a concrete failing assertion Claude can target, and the failure count measurably narrows the gap. B is wrong because a fresh session with the same vague feedback reproduces the same loop from scratch. C is wrong because a batch of general complaints is still general — batching helps interacting issues, not vague ones. D is wrong because the problem stated is feedback quality during iteration, not a missing design phase."
},
{
"id": "d3-05-q23",
"topic": "d3-05",
"domain": "d3",
"scenario": "Developer Productivity with Claude",
"stem": "Every time Claude finishes generating a caching layer, the developer realizes something he never specified — first invalidation timing, then behavior under concurrent writes, then memory ceilings — and each omission costs a rebuild. Which technique attacks this failure mode directly?",
"options": [
"Write a much longer up-front specification attempting to cover every contingency",
"Instruct Claude to interview him about the task before building anything",
"Provide input/output example pairs demonstrating correct cache behavior",
"Write the test suite first and let its failures drive the implementation"
],
"answer": 1,
"explanation": "The recurring failure is unstated requirements discovered after the build; the interview pattern flips the dynamic so Claude surfaces those design considerations — exactly things like cache invalidation — before code exists. A is wrong because the developer has already proven he cannot enumerate the contingencies alone; hoping a longer spec is complete repeats the failure. C is wrong because examples communicate a known transformation, and here the requirements themselves are unknown. D is wrong because tests can only encode requirements someone has already thought of — the gap is upstream of testing."
},
{
"id": "d3-05-q24",
"topic": "d3-05",
"domain": "d3",
"scenario": "Code Generation with Claude Code",
"stem": "A developer is iterating on a report generator with three open bugs: the pagination fix changes how rows are grouped, the grouping logic drives the subtotal math, and the subtotals feed the page-break positions. Twice already, fixing one bug has silently regressed another. How should the next round of feedback be delivered?",
"options": [
"One bug per message, ordered by severity, verifying each fix before reporting the next",
"Each bug in its own fresh session so the fixes cannot interfere",
"Describe the three bugs briefly and let Claude choose which to address first",
"One detailed message covering all three bugs and how they interact, asking for a holistic fix"
],
"answer": 3,
"explanation": "These issues interact — each fix perturbs the others — so they belong in a single detailed message that lets Claude resolve the pagination-grouping-subtotal system holistically instead of playing whack-a-mole. A is wrong because sequential fixes to coupled behavior are precisely what has been causing the regressions. B is wrong because separate sessions maximize isolation when the bugs need to be reasoned about together. C is wrong because ordering is not the problem; any one-at-a-time treatment of interacting issues reproduces the regression cycle."
},
{
"id": "d3-05-q25",
"topic": "d3-05",
"domain": "d3",
"scenario": "Developer Productivity with Claude",
"stem": "After reviewing generated code, a developer has five findings: a query-batching change and a cache-key change that clearly affect each other, plus three unrelated cosmetic issues (a typo, an unused import, a misnamed constant). What is the most effective way to feed these back?",
"options": [
"Send all five findings in a single exhaustive message with no indication of which issues are related, and ask for one combined fix",
"All five one at a time, smallest first, to keep each diff reviewable",
"The two interacting findings together in one detailed message; the cosmetic issues sequentially afterward",
"A fresh session per finding to guarantee clean context for each fix"
],
"answer": 2,
"explanation": "The batching rule follows the interaction structure: coupled issues go together so the fix is holistic, while fully independent issues can safely be handled sequentially. A is wrong because dumping independent trivia into the same undifferentiated message as the coupled changes dilutes the holistic reasoning the interacting pair requires — batching follows interaction structure, and hiding which findings are related invites a scattered fix that regresses the coupled pair. B is wrong because splitting the two interacting changes is the whack-a-mole pattern where each fix regresses the other. D is wrong because five sessions add heavy overhead and still separate the two findings that must be reasoned about jointly."
},
{
"id": "d3-06-q26",
"topic": "d3-06",
"domain": "d3",
"scenario": "Claude Code for Continuous Integration",
"stem": "A team adds a Claude Code review step to their merge pipeline. In local terminals the same invocation works perfectly, but in CI the job sits idle until the 60-minute runner timeout kills it, producing no output at all. What is the fix?",
"options": [
"Export a headless environment variable in the job so Claude detects the CI context",
"Invoke it as `claude -p \"<review prompt>\"` so it runs non-interactively: process, print, exit",
"Redirect a file of pre-written confirmations into the process via stdin",
"Add the `--batch` flag so the run executes without prompting"
],
"answer": 1,
"explanation": "Claude Code is interactive by default and a CI runner has no keyboard, so it hangs awaiting input; the `-p`/`--print` flag is the non-interactive mode built for pipelines. A is wrong because no headless environment variable exists — it is an invented mechanism. C is wrong because stdin redirection is not the supported headless path and cannot anticipate an interactive session's prompts. D is wrong because there is no `--batch` flag; spotting the made-up option eliminates it."
},
{
"id": "d3-06-q27",
"topic": "d3-06",
"domain": "d3",
"scenario": "Claude Code for Continuous Integration",
"stem": "A pipeline runs Claude Code review and must do two things programmatically: post each finding as a PR comment with file, line, and severity, and fail the build only when a finding's category is \"security.\" The current prose output defeats both. What should the invocation add?",
"options": [
"A parsing script with regular expressions tuned to Claude's typical prose layout",
"A prompt instruction: \"Respond only with a JSON array of findings, no other text\"",
"A second Claude call that reads the prose and re-emits it as JSON",
"`--output-format json` plus `--json-schema` to enforce a findings structure"
],
"answer": 3,
"explanation": "The supported mechanism for machine-parseable CI output is `--output-format json` plus `--json-schema` to enforce the structure, letting the pipeline post comments and gate on the severity field reliably. A is wrong because regex over free prose breaks the first time phrasing shifts — the pipeline cannot parse prose dependably. B is wrong because a prompt request for JSON is probabilistic; one chatty preamble breaks the build step. C is wrong because a second model call adds cost and another unreliable prose-to-structure hop when a flag does it deterministically."
},
{
"id": "d3-06-q28",
"topic": "d3-06",
"domain": "d3",
"scenario": "Claude Code for Continuous Integration",
"stem": "A pipeline stage has Claude Code generate a feature implementation and then, in the same session, immediately review its own diff. Reviews come back glowing, yet human reviewers keep finding real defects the automated pass approved. What is the structural cause and fix?",
"options": [
"The session is biased toward validating its own code; review in a fresh, independent instance",
"The review prompt is too polite; instruct the same session to adopt a hostile senior-reviewer persona",
"The diff lacks context; re-send the full file contents to the same session before it reviews",
"The review runs too soon; add a delay stage between generation and review"
],
"answer": 0,
"explanation": "This is self-review bias — a session that just wrote the code is grading its own homework and defends its own reasoning; context isolation via a fresh independent instance is the graded fix. B is wrong because persona instructions do not remove the underlying reasoning that biases the judgment. C is wrong because the session already has maximal context — that is the problem, not the deficiency. D is wrong because elapsed pipeline time changes nothing about what remains in the session's context."
},
{
"id": "d3-06-q29",
"topic": "d3-06",
"domain": "d3",
"scenario": "Claude Code for Continuous Integration",
"stem": "An automated reviewer runs on every push to a PR. Developers complain that after they fix the flagged issues and push, the next run re-posts the same findings alongside any new ones, and PRs now carry three copies of every resolved comment. What should the re-run include?",
"options": [
"Nothing extra — each run should be a completely clean-slate review for maximum independence",
"A post-processing script that string-matches new comments against old ones and drops duplicates",
"The prior review's findings in the run's context, so Claude reports only issues not already raised",
"Only the lines changed in the newest commit, ignoring the rest of the diff"
],
"answer": 2,
"explanation": "The graded pattern for re-reviews is to include prior findings in context so the run reports only new issues instead of repeating resolved ones. A is wrong because independence matters relative to the code-writing session, not relative to earlier review findings — clean-slate re-runs are the cause of the flood. B works superficially but string-matching rephrased findings is brittle and treats the symptom downstream of the fix. D is wrong because narrowing to the last commit misses issues introduced by the interaction of the new commit with earlier changes."
},
{
"id": "d3-06-q30",
"topic": "d3-06",
"domain": "d3",
"scenario": "Claude Code for Continuous Integration",
"stem": "A CI stage generates unit tests for changed modules, but the output ignores the team's fixture and naming standards and keeps re-testing paths the existing suite already covers. The team wants better tests without hand-tuning the pipeline prompt before every run. What is the durable fix?",
"options": [
"Expand the `-p` prompt in the pipeline YAML to restate the testing standards on each invocation",
"Document the standards in the committed CLAUDE.md and provide the existing test files in context",
"Set a headless environment variable that points Claude at the team's testing guide",
"Have a developer run the generation interactively each release so standards can be enforced by review"
],
"answer": 1,
"explanation": "A committed CLAUDE.md carries project standards into headless runs automatically, and supplying existing tests in context is the stated remedy for duplicated coverage — both fixes live in the repo, not the prompt. A works today but embeds a second, drifting copy of the standards in pipeline YAML instead of the shared file every session already reads. C is wrong because no such environment variable exists — an invented mechanism. D is wrong because reintroducing an interactive human step abandons the automation the CI stage exists to provide."
},
{
"id": "d4-01-q01",
"topic": "d4-01",
"domain": "d4",
"scenario": "Claude Code for Continuous Integration",
"stem": "Your CI review bot posts eight findings per pull request, and developers report that most are stylistic opinions rather than real defects. The current prompt says \"review the diff carefully and report only important, high-confidence issues.\" What change is most likely to actually raise precision?",
"options": [
"Strengthen the prompt wording to \"be extremely conservative and only ever report issues that you are completely certain about\"",
"Replace the qualifiers with categorical rules that enumerate exactly which issue types to report and which to skip",
"Ask the model to attach a confidence score to each finding and drop everything below 0.8",
"Run the review twice and post only the findings that appear in both runs"
],
"answer": 1,
"explanation": "Vague qualifiers like \"important\" and \"high-confidence\" are re-interpreted differently on every run; explicit, yes-or-no criteria for what to report versus skip are the fix the exam credits. A just swaps one subjective adjective for another and barely moves precision. C relies on self-reported confidence, which is poorly calibrated and does not address the root cause. D adds cost and can still agree on the same stylistic noise twice, since both runs share the same vague prompt."
},
{
"id": "d4-01-q02",
"topic": "d4-01",
"domain": "d4",
"scenario": "Claude Code for Continuous Integration",
"stem": "One category of your automated review — \"misleading variable names\" — has generated false positives on nearly every PR for two weeks, and developers have started ignoring the bot entirely, including its accurate security findings. You need to rebuild trust while you rework the prompt. What should you do with the noisy category right now?",
"options": [
"Keep it running so you can collect more failure examples while you tune the wording",
"Downgrade its findings from \"warning\" to \"info\" so they look less alarming",
"Add \"only flag names that are truly misleading\" to the category's instructions",
"Temporarily disable the category entirely until you have rewritten it with explicit criteria"
],
"answer": 3,
"explanation": "One noisy category undermines confidence in every accurate one, and the recognized remediation is to pull it offline while its criteria are rewritten — a quieter reviewer that is right rebuilds trust faster than a chatty one that is sometimes right. A keeps eroding trust on every PR while you tune, which the exam treats as the wrong tradeoff. B still shows developers noise, just with a smaller label, so the skimming habit persists. C is the vague-adjective trap; \"truly misleading\" is no more testable than the original wording."
},
{
"id": "d4-01-q03",
"topic": "d4-01",
"domain": "d4",
"scenario": "Claude Code for Continuous Integration",
"stem": "You are drafting the criteria section of a code-review prompt for your pipeline. Which of the following is the best example of a well-formed review criterion?",
"options": [
"\"Flag a code comment only when the behavior it claims contradicts what the code actually does\"",
"\"Flag comments that seem out of date or potentially confusing to future maintainers\"",
"\"Use professional judgment to decide whether each comment still adds value\"",
"\"Flag comments unless you have low confidence that they are a problem\""
],
"answer": 0,
"explanation": "A is a categorical rule with a yes-or-no answer — the claimed behavior either contradicts the code or it does not — which makes it mechanically checkable, the defining property of good criteria. B rests on \"seem\" and \"potentially,\" subjective terms the model interprets differently on each run. C delegates the decision back to unstated judgment, which is exactly what explicit criteria exist to replace. D routes the decision through self-assessed confidence, which is poorly calibrated and not testable."
},
{
"id": "d4-01-q04",
"topic": "d4-01",
"domain": "d4",
"scenario": "Claude Code for Continuous Integration",
"stem": "Your team wants the review step to block merges when it finds serious problems. The bot's output schema includes named categories like `security_violation` and `breaking_api_change`, plus a free-form `general_concerns` text field. How should the pipeline decide when to fail the build?",
"options": [
"Fail whenever any finding is present, since every finding passed the review prompt's filters",
"Fail when the `general_concerns` field is non-empty, because prose concerns capture issues the categories miss",
"Fail only when findings appear in explicitly named categories such as `security_violation`, never on free-form concerns",
"Fail when the total finding count exceeds a threshold tuned from the last month of PRs"
],
"answer": 2,
"explanation": "CI gating should key off explicitly named finding categories in the schema, never vague free-form concerns — blocking on noise teaches developers to ignore the gate, and an ignored review is worse than no review. A blocks merges on stylistic and informational findings, guaranteeing false-positive build failures. B gates on the least precise output the bot produces, inverting the principle. D treats finding volume as severity, so three nitpicks can block a merge while one real vulnerability under the threshold passes."
},
{
"id": "d4-01-q05",
"topic": "d4-01",
"domain": "d4",
"scenario": "Claude Code for Continuous Integration",
"stem": "Developers complain that your reviewer labels unused imports \"critical\" while a genuine injection risk last week came through as \"minor.\" The severity field is currently populated by the instruction \"assign an appropriate severity to each finding.\" What is the strongest fix?",
"options": [
"Instruct the model to \"reserve critical for issues that truly endanger production\"",
"Define each severity level with explicit criteria and include a concrete code example for every level",
"Add a second reviewing instance that votes on each finding's severity and take the higher of the two",
"Have the pipeline re-sort findings by the model's self-reported confidence before posting"
],
"answer": 1,
"explanation": "Severity mislabeling is an explicit-criteria problem: define each level categorically and anchor it with a concrete code example so the model pattern-matches against demonstrations instead of guessing at intent. A replaces one subjective phrase with another; \"truly endanger\" is untestable. C is over-engineering that adds a second run without giving either instance criteria to judge by. D reshuffles the display order but leaves the mislabeled severities — and the trust damage — in place."
},
{
"id": "d4-02-q01",
"topic": "d4-02",
"domain": "d4",
"scenario": "Structured Data Extraction",
"stem": "Your invoice-extraction pipeline returns dates as \"March 3, 2026\" in one run, \"2026-03-03\" in the next, and \"03/03/26\" in a third — despite a paragraph of formatting instructions in the prompt. The schema accepts all three as strings. What is the most effective fix?",
"options": [
"Add two to four worked example pairs showing invoices mapped to the exact desired date format",
"Lower the temperature to 0 so the model stops varying its formatting choices",
"Expand the formatting paragraph into a longer, more precise, and more emphatic specification of the required date format",
"Switch to a larger model that follows detailed instructions more faithfully"
],
"answer": 0,
"explanation": "Inconsistent formatting despite detailed prose is the classic few-shot cue: a small set of worked examples demonstrating the exact output beats a page of instructions, because the model learns the pattern instead of re-interpreting text each run. B treats a pattern problem as a sampling problem; temperature does not teach a format. C adds more prose, which is exactly what is already failing — instructions get re-read differently on every run. D swaps models without addressing the missing demonstrations, so the drift follows."
},
{
"id": "d4-02-q02",
"topic": "d4-02",
"domain": "d4",
"scenario": "Structured Data Extraction",
"stem": "A colleague setting up a contract-extraction prompt asks how many worked examples to include, reasoning that if examples help, thirty must help more. What guidance matches best practice?",
"options": [
"Include at least ten so every clause type appears at least once",
"One canonical example is optimal; more than that causes the model to copy verbatim",
"Roughly two to four targeted examples; returns diminish and can turn negative beyond about six",
"As many as fit in the context window, since unused context is wasted capacity"
],
"answer": 2,
"explanation": "The recommended count is small — about 2–4 targeted examples — with diminishing and eventually negative returns past roughly six, so piling on thirty over-prompts and can degrade output. A sets a floor well past the point of diminishing returns. B understates it; a single example cannot demonstrate edge-case handling or structural variety. D treats context capacity as a reason to add examples, but more examples is not strictly better and double-digit counts hurt."
},
{
"id": "d4-02-q03",
"topic": "d4-02",
"domain": "d4",
"scenario": "Claude Code for Continuous Integration",
"stem": "Your review bot keeps flagging a decade-old callback-heavy module as a defect on every PR that touches it, even though the team has declared that pattern accepted and load-bearing. Meanwhile it correctly flags genuinely dangerous code elsewhere. How do you teach it the difference?",
"options": [
"Add an instruction such as \"do not flag legacy patterns or idioms the team has already reviewed and accepted as load-bearing\"",
"Exclude the legacy module from review entirely so the noise disappears",
"Maintain a list of file paths whose findings the pipeline silently discards",
"Add a contrast pair: the accepted legacy pattern left unflagged beside a genuine issue flagged, with reasoning for each"
],
"answer": 3,
"explanation": "Contrast examples — an alarming-looking accepted pattern left unflagged beside a real issue that is flagged, with reasoning spelled out — teach judgment the model can generalize to novel patterns, which is exactly what this failure needs. A is prose the model must re-interpret every run, and \"accepted legacy patterns\" is undefined without a demonstration. B removes real review coverage from a module that still receives changes. C suppresses symptoms downstream while the model keeps wasting output on findings nobody sees."
},
{
"id": "d4-02-q04",
"topic": "d4-02",
"domain": "d4",
"scenario": "Structured Data Extraction",
"stem": "Your pipeline extracts a `payment_terms` field from purchase orders. About a fifth of the documents never mention payment terms, and for those the model confidently outputs \"Net 30\" — a plausible invention. The prompt already says \"never fabricate values.\" What is the most effective addition?",
"options": [
"Repeat the anti-fabrication instruction at both the start and end of the prompt for emphasis",
"Add a worked example of a document with no payment terms whose output shows the field as null",
"Post-process the output by cross-checking \"Net 30\" values against a list of known suppliers",
"Raise the stakes in the prompt by stating that fabricated values will corrupt downstream accounting"
],
"answer": 1,
"explanation": "One demonstrated null case outperforms any paragraph instructing the model not to make things up — showing what to do when information is genuinely absent is the single most valuable behavior an example can teach. A duplicates prose that is already failing. C only catches one specific fabricated value and fires after the bad data exists rather than preventing it. D is still an instruction, and dramatic phrasing does not change how instructions get re-interpreted run to run."
},
{
"id": "d4-02-q05",
"topic": "d4-02",
"domain": "d4",
"scenario": "Structured Data Extraction",
"stem": "You have room in the prompt for three worked examples for a form-extraction task. Your test corpus contains clean single-page forms, scanned forms with fields relocated by different vendors, and forms where two fields are easily confused. A teammate proposes using three clean, obviously correct forms so the examples are \"unambiguous.\" What is the better selection, and why?",
"options": [
"Include the vendor-variant and confusable-field cases with their reasoning, since easy cases teach the model little",
"Use the three clean forms as proposed, because ambiguous or tricky examples risk teaching the model to produce ambiguous output",
"Use one clean form and duplicate it three times so the target format is reinforced",
"Skip examples entirely and describe the edge cases in prose to save tokens for the document"
],
"answer": 0,
"explanation": "The value of examples is concentrated in ambiguous and edge cases resolved with visible reasoning — showing varied document structures mapping to the same output stops structural variation from causing drift, and easy cases add almost nothing. B trains on cases the model already gets right, leaving the actual failure modes undemonstrated. C repeats one data point three times, teaching nothing about variation. D falls back to prose descriptions, which are precisely what worked examples outperform."
},
{
"id": "d4-03-q01",
"topic": "d4-03",
"domain": "d4",
"scenario": "Structured Data Extraction",
"stem": "Your extraction service prompts Claude with \"Respond only with valid JSON matching this structure\" and parses the reply. It works in testing, but in production roughly one response in forty arrives wrapped in markdown fences or prefixed with \"Here is the extracted data:\", crashing the parser. What is the most reliable fix?",
"options": [
"Add a post-processing step that strips markdown fences and any leading conversational prose with regexes before parsing",
"Add \"do not include any text before or after the JSON\" to the prompt",
"Define a tool whose input schema is the desired output shape and require the model to call it",
"Retry any unparseable response until a clean JSON reply comes back"
],
"answer": 2,
"explanation": "Tool use is the reliable mechanism: the tool's input schema is the output contract, the API enforces conformance, and Claude's training against tool schemas gives far higher compliance than any free-text instruction. A patches known wrapper variants but new ones keep appearing; it treats symptoms. B is still a prose format request, the naive approach that intermittently fails in exactly this way. D burns tokens re-rolling the dice on an approach that is structurally unreliable."
},
{
"id": "d4-03-q02",
"topic": "d4-03",
"domain": "d4",
"scenario": "Structured Data Extraction",
"stem": "Your pipeline receives a mixed stream of invoices, receipts, and shipping manifests, with a separate extraction tool defined for each type. Every response must be structured — plain text is never acceptable — but the correct schema depends on which document type arrives. Which `tool_choice` setting fits?",
"options": [
"`\"auto\"`, so the model can pick the right tool for each document",
"`\"any\"`, so the model must call some tool but chooses which schema fits the input",
"A forced choice naming the invoice tool, since invoices are the most common type",
"`\"auto\"` with a system-prompt instruction that a tool must always be used"
],
"answer": 1,
"explanation": "`\"any\"` is designed for exactly this case — it guarantees the model calls some tool from the list, ensuring structured output, while leaving schema selection to the model based on the document. A permits plain-text answers, so structure is not guaranteed. C forces the invoice schema onto receipts and manifests, producing garbage for two-thirds of the stream. D still relies on a prose instruction to close the gap `\"auto\"` leaves open, which is not a guarantee."
},
{
"id": "d4-03-q03",
"topic": "d4-03",
"domain": "d4",
"scenario": "Structured Data Extraction",
"stem": "After migrating to forced tool use with a strict JSON schema, your team lead declares the extraction pipeline \"fully validated\" and proposes deleting the downstream reconciliation checks. Which error class does schema enforcement actually eliminate, and which survives?",
"options": [
"It eliminates both syntax and semantic errors, so the checks are safely removable",
"It eliminates semantic errors such as misfiled values, but syntax errors like trailing commas and unbalanced brackets can still occur",
"It eliminates neither; tool use only affects how the response is transported",
"It eliminates syntax errors, but semantic errors like a total in the wrong field survive"
],
"answer": 3,
"explanation": "Strict schemas via tool use remove syntax failures (conversational preambles, fences, malformed JSON) but cannot stop a valid-shaped document from carrying wrong values — totals in the wrong field or line items that do not sum still pass, so the reconciliation checks must stay. A overstates the guarantee; value correctness is out of schema scope. B inverts the two error classes. C understates it — schema conformance is genuinely enforced, which is precisely why syntax errors disappear."
},
{
"id": "d4-03-q04",
"topic": "d4-03",
"domain": "d4",
"scenario": "Structured Data Extraction",
"stem": "Your purchase-order schema marks every field required \"for completeness,\" and category is a closed enum of five values. In production, orders missing a delivery date come back with invented dates, and unusual product categories get shoehorned into the nearest enum value. What schema redesign addresses both problems?",
"options": [
"Make legitimately absent fields nullable, and add \"unclear\" and \"other\" values to the enum",
"Keep all fields required but add a prompt instruction to leave unknown fields blank",
"Replace the enum with a free-text category field so the model is never forced into a wrong value",
"Add a second extraction pass that scans the first pass's output and overwrites suspicious dates and categories with null"
],
"answer": 0,
"explanation": "Required fields force the model to write something even when the source contains nothing — fabrication to satisfy the schema — so absent-capable fields must be optional or nullable, and extensible enums need \"unclear\" for ambiguity plus \"other\" with a detail field for unanticipated categories. B leaves the structural pressure in place and papers over it with prose; the schema still demands a value. C discards the categorical constraint entirely, trading fabrication for uncontrolled vocabulary. D detects damage after the fact instead of removing the schema design that causes it."
},
{
"id": "d4-03-q05",
"topic": "d4-03",
"domain": "d4",
"scenario": "Claude Code for Continuous Integration",
"stem": "Your CI review job gives Claude several tools — `post_findings`, `fetch_diff_context`, and `summarize_pr` — but the pipeline requires that a `post_findings` call happen first so downstream gating always has structured findings, after which the model may use the other tools freely. How do you guarantee that first call?",
"options": [
"Set `tool_choice: \"any\"` on the first request so a tool call is guaranteed",
"List `post_findings` first in the tools array so the model prefers it",
"Add a firm system-prompt instruction, repeated at the end of the prompt, that the model must always begin by calling `post_findings`",
"Set `tool_choice: {\"type\": \"tool\", \"name\": \"post_findings\"}` on the first request, then loosen to `\"auto\"` for subsequent turns"
],
"answer": 3,
"explanation": "Forcing the named tool is the only mode that guarantees that exact tool runs — the documented pattern is to force the specific call first, then relax to `\"auto\"` once the required structured step has happened. A guarantees some tool is called but not which; the model could call `summarize_pr` first. B relies on ordering as a soft preference the API does not treat as a constraint. C is a prose instruction the model usually follows but the pipeline cannot rely on it as a guarantee."
},
{
"id": "d4-04-q01",
"topic": "d4-04",
"domain": "d4",
"scenario": "Structured Data Extraction",
"stem": "Your invoice pipeline validates parsed output in application code and finds a failure: the extraction reports revenue of $0 while the document plainly states $4.2M. The JSON itself is schema-perfect. What should the retry request contain?",
"options": [
"The original prompt again, unchanged, since a fresh sample will likely land differently",
"The original document, the failed extraction, and the validation error naming the broken field",
"The original prompt plus an emphatic instruction to \"double-check every numeric field very carefully this time before responding\"",
"Only the failed JSON, with an instruction to find and fix whatever is wrong with it"
],
"answer": 1,
"explanation": "Retry-with-error-feedback is the credited pattern: with its own failed output and the exact discrepancy in context — including the value the document actually states — the model's second-attempt correction rate exceeds 90%. A is a blind retry — rerunning the identical prompt just rolls the dice again. C adds a vague carefulness instruction without telling the model what actually failed. D withholds the source document, so the model has nothing authoritative to correct against."
},
{
"id": "d4-04-q02",
"topic": "d4-04",
"domain": "d4",
"scenario": "Structured Data Extraction",
"stem": "A teammate argues that retrying failed extractions is pointless: \"If the model got it wrong once, it'll get it wrong again.\" Under the feedback-loop pattern, why is a well-constructed second attempt actually very likely to succeed?",
"options": [
"Seeing its own failed output alongside the specific validation error concentrates the model on the exact broken field",
"The API automatically detects the repeated document and raises its reasoning effort on any request that resubmits a previously failed task",
"The second request is routed to a fresh model instance whose weights have not seen the failure",
"Validation errors reset the context window, removing the noise that caused the mistake"
],
"answer": 0,
"explanation": "The mechanism is contextual, not mystical: the follow-up carries the original document, the failed extraction, and the precise error, so the model corrects a named defect rather than re-attempting the whole task — better than 90% of such retries succeed. B invents an automatic effort escalation the API does not perform. C misunderstands inference; there is no per-instance weight state to escape. D describes a context-reset behavior that does not exist and would discard the very feedback that makes the retry work."
},
{
"id": "d4-04-q03",
"topic": "d4-04",
"domain": "d4",
"scenario": "Structured Data Extraction",
"stem": "Your contract pipeline requires a `termination_notice_days` field. One batch of older contracts simply never specifies a notice period. Logs show the retry loop hammering these documents five times each; by the fourth attempt the model starts returning \"30\", which appears nowhere in the text. What should the pipeline do instead?",
"options": [
"Increase the retry limit to ten so the model has more chances to locate the clause",
"Rephrase the retry prompt more forcefully each attempt until a value is produced",
"Recognize that retries cannot recover information absent from the source, and fail gracefully or escalate after one or two attempts",
"Accept the \"30\" once it appears in two consecutive attempts, treating agreement as evidence"
],
"answer": 2,
"explanation": "Retries fix format, structure, and arithmetic — they can never conjure data the source does not contain, and persistent pressure is exactly what pushes the model into hallucinating a value to end the loop. A doubles down on a futile loop and multiplies both cost and hallucination pressure. B increases the pressure that manufactured the fake \"30\" in the first place. D launders a hallucination into the dataset; repeated fabrication under identical pressure is not corroboration."
},
{
"id": "d4-04-q04",
"topic": "d4-04",
"domain": "d4",
"scenario": "Structured Data Extraction",
"stem": "You want arithmetic discrepancies in expense-report extraction to surface during generation rather than only in downstream checks. Which schema design accomplishes this?",
"options": [
"Add a required `is_accurate` boolean the model sets after reviewing its own output",
"Require the model to output every numeric field twice in two separate schema sections and compare the copies for agreement",
"Add a post-generation prompt asking the model whether its previous answer was correct",
"Require both a `calculated_total` from the line items and the document's `stated_total`, plus a conflict flag"
],
"answer": 3,
"explanation": "The self-correction schema pattern makes discrepancies flag themselves: requiring both the stated total and a total calculated from the line items, with a conflict flag, forces the reconciliation to happen during generation. A collects an unanchored self-assessment with nothing concrete to check against. B duplicates fields within one generation pass, so both copies inherit the same error. C is self-review after the fact, which lacks the structural comparison that makes the pattern work."
},
{
"id": "d4-04-q05",
"topic": "d4-04",
"domain": "d4",
"scenario": "Claude Code for Continuous Integration",
"stem": "Developers dismiss about a third of your review bot's findings, but nobody can say which kinds of findings are the problem — dismissals are just clicks in the PR UI. You want to turn those dismissals into systematic prompt improvements. What should you add?",
"options": [
"A weekly meeting where developers recall which findings annoyed them most",
"A dismissal-rate alert that disables the bot when the rate crosses 40%",
"A `detected_pattern` field on every finding recording which code construct triggered it",
"A free-text `reasoning` paragraph on each finding so an engineer can read through the dismissals one by one, case by case"
],
"answer": 2,
"explanation": "Recording the triggering construct in a structured `detected_pattern` field turns each dismissal into a data point — you can systematically identify which patterns generate false positives and refine the criteria or examples for exactly those. A substitutes memory and anecdote for data. B reacts to aggregate noise without identifying what is noisy, and turning the bot off discards the accurate categories too. D produces prose that must be read individually and cannot be aggregated by pattern."
},
{
"id": "d4-05-q01",
"topic": "d4-05",
"domain": "d4",
"scenario": "Structured Data Extraction",
"stem": "You are costing out a job to extract structured metadata from a 200,000-document archive with no interactive consumers. A colleague asks what the Message Batches API actually promises before you commit. Which characterization is accurate?",
"options": [
"50% cost savings with results guaranteed within one hour for most batches",
"Same cost as real-time calls, but with higher rate limits for bulk submission",
"50% cost savings with a guaranteed four-hour turnaround SLA",
"50% cost savings, results within a window of up to 24 hours, and no guaranteed latency SLA"
],
"answer": 3,
"explanation": "The three numbers to memorize: half the cost of real-time calls, a processing window of up to 24 hours, and no latency SLA — a profile built for latency-tolerant work like this archive. A invents a one-hour guarantee; batches are often faster than 24 hours but nothing is promised. B misses the entire economic point, which is the 50% discount. C invents an SLA that does not exist, and designing around it would break the first time a batch runs long."
},
{
"id": "d4-05-q02",
"topic": "d4-05",
"domain": "d4",
"scenario": "Claude Code for Continuous Integration",
"stem": "Finance wants your team's Claude spend cut, and someone proposes moving both workloads to the Batch API: the nightly technical-debt report, and the pre-merge review check that developers wait on before merging. Batch jobs in your testing usually finish within 20 minutes. What is the right split?",
"options": [
"Move both — 20-minute typical turnaround is well within a developer's working session",
"Batch the nightly report; keep the pre-merge check on the synchronous API because a human is blocked on it and batch has no latency SLA",
"Keep both real-time, since review outputs are too important to delay",
"Batch both, but poll aggressively and fall back to a synchronous call if results take over an hour"
],
"answer": 1,
"explanation": "Route by urgency: the overnight report is non-blocking and pockets the 50% savings, while the pre-merge check has a developer waiting at the keyboard — \"usually fast\" is worthless without an SLA, and a 24-hour worst case is disqualifying. A bets a human-blocking workflow on typical-case behavior the API does not guarantee. C forfeits the easy, risk-free savings on the report. D pays for batch and a synchronous fallback while still leaving developers waiting up to an hour before the fallback fires."
},
{
"id": "d4-05-q03",
"topic": "d4-05",
"domain": "d4",
"scenario": "Structured Data Extraction",
"stem": "Your overnight extraction design has each batch request call a `lookup_vendor` tool mid-processing to resolve vendor IDs against your database before producing the final JSON. During implementation you discover this cannot work. Why, and what is the fix?",
"options": [
"Batch requests are single-turn with no multi-turn tool calling, so vendor data must be pre-fetched into each prompt",
"Batch requests allow at most one tool call each, so the lookup must be merged into the extraction tool",
"Tools are unsupported in batch requests entirely, so extraction must return free-form text parsed downstream",
"The batch runner caches tool results across all requests in the batch, so mid-processing lookups would return stale vendor data"
],
"answer": 0,
"explanation": "A batch request is one turn — prompt in, final answer out — with no way for the model to pause and call back into your environment, so anything it needs (schemas, reference data, dependencies) must be pre-fetched into the prompt. B invents a one-call allowance; the constraint is no callback round-trips at all. C overcorrects — the lesson's constraint is specifically no multi-turn tool-calling round-trips, not a blanket tool ban. D describes a caching behavior that is not the actual limitation."
},
{
"id": "d4-05-q04",
"topic": "d4-05",
"domain": "d4",
"scenario": "Structured Data Extraction",
"stem": "Your 10,000-document overnight batch completes with 240 failures, most of them oversized filings that blew past the context limit. The rest of the batch extracted cleanly. What is the correct recovery procedure?",
"options": [
"Resubmit the full 10,000-request batch with a larger context configuration",
"Immediately rerun all 240 failed documents, unchanged, one at a time through the real-time API to get results back as fast as possible",
"Identify the failed items by `custom_id`, chunk the oversized filings, and resubmit only those items as a new batch",
"Log the 240 failures and accept 97.6% coverage, since partial failure is expected at this scale"
],
"answer": 2,
"explanation": "The `custom_id` on each request exists to correlate asynchronous request/response pairs, and the credited failure-handling pattern is to isolate failed items by `custom_id`, repair the cause (chunk the oversized documents), and resubmit only those — never the whole batch. A reprocesses 9,760 already-successful documents, paying double for no benefit. B's decisive flaw is that the failures were context-limit blowouts — rerunning the documents unrepaired hits the same limit on any endpoint, so C's chunking is what actually makes recovery possible; paying the 2x real-time rate for work nothing is blocking on overnight is a secondary objection. D abandons recoverable documents when a programmatic fix exists."
},
{
"id": "d4-05-q05",
"topic": "d4-05",
"domain": "d4",
"scenario": "Structured Data Extraction",
"stem": "Your contract with a data provider requires every submitted document to be processed within 30 hours of arrival. Documents arrive continuously and you submit them to the Batch API in periodic batches, where processing can take up to the full 24-hour window. What is the longest safe interval between batch submissions?",
"options": [
"Every 6 hours",
"Every 24 hours, matching the processing window",
"Every 30 hours, matching the contractual deadline",
"Every 12 hours, submitting twice per day for safety margin"
],
"answer": 0,
"explanation": "The worst-case document arrives immediately after a submission, waits the full interval, then takes the full 24-hour window: interval + 24 must stay within 30 hours, so the longest safe interval is the 6-hour slack. B leaves a worst case of 24 + 24 = 48 hours, blowing the deadline. C ignores processing time entirely, allowing up to 54 hours. D also breaches the deadline: 12 + 24 = 36 hours, which exceeds the 30-hour contract, so a 12-hour interval is not safe in the worst case."
},
{
"id": "d4-06-q01",
"topic": "d4-06",
"domain": "d4",
"scenario": "Claude Code for Continuous Integration",
"stem": "Your CI job has Claude generate a bug fix and then, in the same session, evaluates its own patch with the instruction \"critically review the code above for defects before approving.\" The step confidently approves patches that later fail in staging. Why does this happen, and what fixes it?",
"options": [
"The review instruction is too vague; specifying defect categories to check will restore rigor",
"The session's context is too full after generation; summarizing before the review step will fix it",
"The session's own reasoning context biases it toward validating its logic — route the patch to a separate instance without that context",
"The model needs an explicit adversarial persona; instructing it to \"act as a hostile reviewer\" during the self-check will restore the missing rigor"
],
"answer": 2,
"explanation": "Self-review is structurally weak because the same reasoning that produced the logic tends to validate it — no prompt wording overcomes that, and the architectural fix is a fresh instance with no stake in the prior decisions. A tunes the instruction while leaving the biased reviewer in place. B misdiagnoses a reasoning-context bias as a capacity problem. D is still the same session grading its own homework, persona or not."
},
{
"id": "d4-06-q02",
"topic": "d4-06",
"domain": "d4",
"scenario": "Claude Code for Continuous Integration",
"stem": "A fourteen-file PR reviewed in a single pass yields findings that contradict each other — one finding says a variable is unused while another discusses its downstream effect — and results vary wildly between runs. What review architecture addresses the root cause?",
"options": [
"Rerun the single-pass review three times and merge the outputs into one report",
"Sort the files by size and review the largest ones first while attention is freshest",
"Ask the model to build a full dependency graph of the PR first, then review everything in the same pass with the graph as context",
"Run focused per-file passes, then a separate integration pass scoped to cross-file data flow and interfaces"
],
"answer": 3,
"explanation": "The contradictions come from attention dilution — fourteen files competing for attention at once lose variables and hallucinate cross-file connections — and the credited design gives each pass a narrow job: per-file local analysis plus a dedicated cross-file integration pass. A merges several diluted reviews, combining their inconsistencies rather than fixing them. B still puts all fourteen files into one attention-diluted pass. C adds a preparatory step but the review itself still processes everything at once."
},
{
"id": "d4-06-q03",
"topic": "d4-06",
"domain": "d4",
"scenario": "Claude Code for Continuous Integration",
"stem": "After the contradictory-findings incident, your platform team proposes upgrading the review job to a model tier with a much larger context window, reasoning that the PR will then \"fit comfortably.\" Will this fix the inconsistent multi-file findings?",
"options": [
"Yes — the findings were inconsistent because the PR was truncated to fit the window",
"No — attention dilution is about attention quality, not capacity; more room does not improve how well text is attended to",
"Yes, provided the fourteen files are concatenated in strict dependency order within the larger window so related code stays adjacent",
"No — larger context windows disable tool use, which the review job requires"
],
"answer": 1,
"explanation": "A bigger window increases how much fits, not how well the model attends to it — attention dilution persists at any capacity, which is why the fix is splitting the review into narrow passes rather than buying headroom. A assumes truncation was the cause, but the failure occurs even when everything fits. C reorders the same overloaded single pass. D is a fabricated limitation; context size has no such interaction with tool use."
},
{
"id": "d4-06-q04",
"topic": "d4-06",
"domain": "d4",
"scenario": "Claude Code for Continuous Integration",
"stem": "Your review pipeline runs each PR five times, and findings differ between runs. An engineer proposes keeping only findings that appear in at least three of the five runs, arguing this filters hallucinations. A serious race condition last month was flagged in exactly one run of five. What is wrong with the proposal?",
"options": [
"Consensus voting suppresses rare-but-real catches; route low-frequency findings for triage with self-reported confidence instead",
"Nothing — a finding that appears once in five runs is by definition unreliable and should be dropped",
"The threshold is simply too high; requiring two of five runs would keep the race condition",
"Voting is the right mechanism but should use seven or nine runs, since five is too small a sample for a statistically stable majority"
],
"answer": 0,
"explanation": "Consensus averaging is specifically called out as wrong because subtle, real issues often surface in only some runs — majority filtering silently deletes exactly the findings that matter most, while confidence self-reporting gives downstream tooling a routing signal without discarding them. B equates infrequency with unreliability, which the race-condition example directly refutes. C keeps the flawed mechanism and would still have dropped this one-in-five catch. D adds runs without fixing the structural problem that voting suppresses intermittent true positives."
},
{
"id": "d4-06-q05",
"topic": "d4-06",
"domain": "d4",
"scenario": "Claude Code for Continuous Integration",
"stem": "You have implemented the per-file-plus-integration review split, but the integration pass's findings are mostly duplicates of the per-file passes — long-function warnings and naming issues restated — while an interface mismatch between two modules went unmentioned. How should the integration pass be fixed?",
"options": [
"Give the integration pass the complete list of per-file findings so it knows which issues are already covered and can skip them",
"Scope the integration pass to cross-file concerns only — data flow and interface boundaries — excluding single-file issues",
"Drop the integration pass and deduplicate the per-file findings programmatically instead",
"Run the integration pass first so the per-file passes become the ones that deduplicate"
],
"answer": 1,
"explanation": "Each pass must have a narrow job: the integration pass exists for cross-file data flow and interface boundaries, and without that explicit scope it defaults to re-reviewing everything, diluting attention away from exactly the mismatch it missed. A treats the symptom — duplication — while leaving the pass's attention spread over single-file concerns. C removes the only pass capable of catching cross-file defects like the interface mismatch. D reorders the passes without scoping either, so the duplication and the missed mismatch both persist."
},
{
"id": "d5-01-q01",
"topic": "d5-01",
"domain": "d5",
"scenario": "Customer Support Resolution Agent",
"stem": "A developer building the support agent notices that at turn 25 the agent no longer knows the order number the customer gave at turn 2, even though the previous API call \"went fine.\" They ask why the API \"forgot.\" What is the accurate explanation?",
"options": [
"The API retains conversation state server-side for about five minutes, and this conversation exceeded that retention window",
"The system prompt persists between calls but user turns do not, so early user-provided details are dropped automatically",
"The Messages API is stateless, so the application must be sending a truncated history",
"Conversation state is stored per API key, and a key rotation during the session cleared the stored history"
],
"answer": 2,
"explanation": "The API is stateless: each request carries its own complete history, so a missing fact means the application stopped sending it. A confuses prompt caching's ~5-minute prefix reuse with server-side memory, which does not exist. B is wrong because nothing persists automatically between calls, not even the system prompt. D invents per-key session storage that the API does not provide."
},
{
"id": "d5-01-q02",
"topic": "d5-01",
"domain": "d5",
"scenario": "Customer Support Resolution Agent",
"stem": "To control token growth, a support agent summarizes older turns every ten exchanges. After three summarization passes, the agent tells a customer \"a refund was discussed\" but can no longer state the exact refund amount or the promised delivery date. What change best prevents this class of loss?",
"options": [
"Extract durable transactional facts into a structured case-facts block kept outside the summarized history and injected into every request",
"Increase the target length of each summary so that more of the original detail survives every summarization pass",
"Add an instruction to the summarization prompt telling the model to be extra careful with numbers, then re-summarize from the original transcript",
"Reduce summarization frequency to every twenty exchanges so fewer lossy passes occur over the conversation's lifetime"
],
"answer": 0,
"explanation": "A case-facts block keeps hard facts out of the lossy summarization path entirely and pins them at an attention-reliable position. B still passes exact values through repeated lossy compression, only more slowly. C relies on prompt-level care to fix a structural loss mechanism, and precision still erodes across passes. D merely delays the erosion; the numbers and dates still eventually blur."
},
{
"id": "d5-01-q03",
"topic": "d5-01",
"domain": "d5",
"scenario": "Multi-Agent Research System",
"stem": "A coordinator concatenates ten subagents' verbose outputs into one long synthesis prompt. Reviewers notice the final report consistently omits findings from subagents whose output landed in the middle of that prompt, while findings near the start and end are covered well. What is the best structural fix?",
"options": [
"Randomize the order of subagent outputs on every synthesis call so no single subagent is systematically disadvantaged",
"Add an instruction to the synthesis prompt telling the model to give equal weight to every section of the input",
"Switch to a model with a larger advertised context window so the middle of the input falls under less pressure",
"Put a key-findings summary at the start of the aggregated input and require trimmed, structured subagent output"
],
"answer": 3,
"explanation": "Lost-in-the-middle is positional: putting critical findings at an attention-reliable position and shrinking the noise volume addresses the mechanism directly. A just rotates which findings get lost rather than preventing loss. B asks the model to override an attention characteristic that instructions do not reliably fix. C misunderstands the effect — attention quality does not scale with window size, so a bigger window keeps the same middle-zone weakness."
},
{
"id": "d5-01-q04",
"topic": "d5-01",
"domain": "d5",
"scenario": "Customer Support Resolution Agent",
"stem": "The support agent's `lookup_order` tool returns roughly 40 fields per call, and multi-issue conversations involve several lookups. By turn 30, tool results dominate the context and the agent starts missing customer-stated details. What is the best remedy?",
"options": [
"Delete all tool results from history immediately after each turn so they never accumulate",
"Trim each tool result to the handful of fields relevant to the current issue before it enters the conversation history",
"Set a `cache_control` breakpoint over the tool results so their token cost is reduced on subsequent calls",
"Summarize the entire conversation, including the tool results, more frequently as the token count grows"
],
"answer": 1,
"explanation": "Tool results consume tokens out of proportion to their usefulness; trimming to relevant fields keeps needed facts while removing the bulk that buries them. A discards data the agent may still need for follow-up questions in the same case. C is the wrong layer — caching reduces price on a stable prefix, not the attention burden or token footprint inside the window. D applies lossy summarization to exactly the transactional details that summaries blur first."
},
{
"id": "d5-01-q05",
"topic": "d5-01",
"domain": "d5",
"scenario": "Multi-Agent Research System",
"stem": "A research coordinator sends the same 8,000-token system prompt and few-shot examples on every subagent call, followed by a task instruction that differs per call. The team wants to cut input costs with prompt caching. Which approach uses the feature correctly?",
"options": [
"Place a `cache_control` breakpoint after the stable system prompt and few-shot examples",
"Cache the entire prompt including the final task instruction so every token in every call is discounted",
"Warm the cache once each morning, since cached prefixes persist for the rest of the day once written",
"Enable caching to expand the effective context window, allowing longer subagent outputs at the same attention quality"
],
"answer": 0,
"explanation": "Caching pays off on a byte-identical stable prefix reused within the ~5-minute window — exactly the system prompt plus few-shot block — cutting the cached portion to roughly one tenth of the input price. B fails because the changing final instruction breaks prefix identity, so those tokens never hit cache. C is wrong on lifetime — the cache lasts roughly five minutes, not a day. D confuses cost reduction with capacity: caching changes price, not window size or attention behavior."
},
{
"id": "d5-02-q01",
"topic": "d5-02",
"domain": "d5",
"scenario": "Customer Support Resolution Agent",
"stem": "A team is writing the escalation section of their support agent's system prompt and wants to list only conditions that genuinely warrant a handoff to a human. Which of the following is a valid escalation trigger?",
"options": [
"The customer's messages show strongly negative sentiment across two consecutive turns",
"The model reports its own confidence in the proposed resolution as below 60%",
"The conversation has exceeded fifteen turns without the customer confirming resolution",
"The documented policy is silent on the customer's situation, so resolving it would require the agent to invent policy"
],
"answer": 3,
"explanation": "A policy gap is one of the three canonical triggers — an agent must never fill a policy silence with its own judgment. A fails because sentiment does not track case complexity; a furious customer with a simple in-policy issue should get an acknowledgment and a resolution. B fails because self-reported confidence is poorly calibrated, and worst on exactly the hard cases. C is an arbitrary length proxy — long conversations can be making steady progress, and short ones can be stuck."
},
{
"id": "d5-02-q02",
"topic": "d5-02",
"domain": "d5",
"scenario": "Customer Support Resolution Agent",
"stem": "Three turns in, a customer writes \"just get me a real person.\" The agent has already identified the issue as a duplicate charge that its `process_refund` tool can fix within policy in under a minute. What should the agent do?",
"options": [
"Explain that the issue is simple, process the refund, and then offer a human follow-up if the customer is still unsatisfied",
"Transfer immediately, passing the human a structured handoff with the customer ID, root cause, and recommended action",
"Ask the customer why they want a human, in case the underlying concern is something the agent can address directly",
"Attempt the refund first and transfer only if the tool call fails, since first-contact resolution is the team's primary metric"
],
"answer": 1,
"explanation": "An explicit request for a human is honored immediately — investigating or fixing first disrespects the request — and the handoff should carry structured context so the human does not cold-start. A and D both make the customer wait through an attempted resolution they explicitly declined. C interrogates the request instead of honoring it, which delays the transfer and adds friction."
},
{
"id": "d5-02-q03",
"topic": "d5-02",
"domain": "d5",
"scenario": "Customer Support Resolution Agent",
"stem": "A deployed support agent achieves 55% first-contact resolution against an 80% target. Transcript review shows it transfers routine password resets to humans while attempting to resolve complex policy-exception refunds on its own. What is the best fix?",
"options": [
"Add a self-reported confidence score to each response and escalate whenever the score falls below a tuned threshold",
"Train a lightweight sentiment classifier on past transcripts and escalate conversations the classifier flags as difficult",
"Add explicit escalation criteria to the system prompt, with few-shot examples of cases to escalate and cases to resolve",
"Route every refund-related case to humans and keep only informational queries with the agent until resolution rates improve"
],
"answer": 2,
"explanation": "The failure is miscalibrated escalation judgment, and the graded fix is explicit criteria plus contrasting examples in the system prompt. A relies on self-reported confidence, which is poorly calibrated and most overconfident on hard cases. B uses sentiment as a proxy for difficulty, and sentiment does not correlate with case complexity. D abandons a large in-policy workload the agent can handle, moving the system further from the 80% target."
},
{
"id": "d5-02-q04",
"topic": "d5-02",
"domain": "d5",
"scenario": "Customer Support Resolution Agent",
"stem": "During identity verification, `get_customer` returns two records matching the caller's name and postal code. The agent needs to look up an order and possibly issue a refund. How should it proceed?",
"options": [
"Ask the customer for an additional identifier, such as the email address or order number on the account, and re-query",
"Select the record with the most recent account activity, since the caller is most likely the active account holder",
"Escalate to a human immediately, because ambiguous identity resolution is outside the agent's competence",
"Continue with both records in parallel and let the order lookup disambiguate which account the caller means"
],
"answer": 0,
"explanation": "With multiple matches the rule is never guess — request another identifier and resolve the ambiguity before acting. B is heuristic selection, which risks operating on (and refunding to) the wrong person's account. C over-escalates a situation the agent can resolve with one clarifying question. D proceeds on unverified identity and could expose one customer's order details to another."
},
{
"id": "d5-02-q05",
"topic": "d5-02",
"domain": "d5",
"scenario": "Customer Support Resolution Agent",
"stem": "A customer opens with an angry message about a late package. The order lookup shows a straightforward in-policy remedy: reship at no charge. The agent's escalation instructions list the standard triggers. Which response best balances customer experience against escalation discipline?",
"options": [
"Escalate to a human, since visible anger this early predicts a difficult interaction the agent should not attempt",
"Offer a goodwill credit above the documented remedy to defuse the anger before processing the reshipment",
"Process the reshipment silently and keep the reply brief, since engaging with the emotion may escalate it",
"Acknowledge the frustration explicitly, then resolve the issue with the in-policy reshipment in the same reply"
],
"answer": 3,
"explanation": "Sentiment is not an escalation trigger; the correct pattern for a frustrated customer with a simple in-policy issue is acknowledge-and-resolve. A escalates on sentiment, an unreliable proxy that wastes human capacity on an easy case. B invents compensation beyond documented policy, which is exactly the policy-gap behavior an agent must avoid. C resolves the mechanics but ignores the customer's stated frustration, degrading the experience the acknowledgment step exists to protect."
},
{
"id": "d5-03-q01",
"topic": "d5-03",
"domain": "d5",
"scenario": "Multi-Agent Research System",
"stem": "A web-search subagent in a research pipeline hits a provider timeout partway through its assignment after collecting some results. What should it return to the coordinator?",
"options": [
"An empty result set with a success status, so the pipeline continues without interruption",
"A structured error report with the failure type, query attempted, partial results, and suggested alternatives",
"A concise status string such as \"search unavailable\" so the coordinator's parsing logic stays simple",
"Nothing — it should raise the exception so the whole run halts and an operator can inspect the failure state directly"
],
"answer": 1,
"explanation": "The four-part structured error context is what lets the coordinator retry differently, switch sources, or proceed with partials while annotating the gap. A silently suppresses the failure, shipping a report with invisible holes. C hides everything the coordinator needs to choose a recovery path behind a generic status. D turns one recoverable failure into loss of the entire run."
},
{
"id": "d5-03-q02",
"topic": "d5-03",
"domain": "d5",
"scenario": "Multi-Agent Research System",
"stem": "A search subagent completes normally but finds zero documents matching its assigned query about a niche regulation. The coordinator's error handler is about to classify this as a failure and schedule a retry with modified parameters. Why is that classification wrong?",
"options": [
"Retries should always be handled inside the subagent, so the coordinator classifying anything is a layering violation",
"Zero results should be escalated to a human researcher rather than retried, since automation cannot judge relevance",
"A search that succeeds and finds zero matches is a legitimate result, not an error",
"The subagent should have broadened its own query until it found at least one match before reporting back"
],
"answer": 2,
"explanation": "The access-failure-versus-empty-result distinction is central: zero matches from a successful search is a valid answer, and mislabeling it as an error causes endless retries. A overstates the layering rule — subagents retry transient failures locally, but the coordinator legitimately makes recovery decisions for propagated errors. B escalates a normal outcome that needs no human. D pressures the subagent to manufacture matches, distorting the finding that nothing relevant exists."
},
{
"id": "d5-03-q03",
"topic": "d5-03",
"domain": "d5",
"scenario": "Customer Support Resolution Agent",
"stem": "A support coordinator decomposes a multi-issue complaint and spawns three parallel subagents: one processes a refund, one updates the shipping address, and one drafts a goodwill message. The address-update subagent fails on an unresolvable API error; the refund subagent has already completed its `process_refund` call. What is the correct recovery?",
"options": [
"Rerun all three subagents from the top so the workflow state is guaranteed consistent",
"Abort the whole workflow and hand the customer to a human with the raw transcript",
"Skip the address update and finish the case, since two of three tasks succeeded",
"Retry only the failed address-update subagent, leaving the completed siblings untouched"
],
"answer": 3,
"explanation": "Targeted retry of only the failed subagent is required because successful siblings have committed side effects — rerunning them would issue a second refund. A repeats the refund, a concrete duplicated side effect. B discards recoverable work and violates structured-handoff practice by passing a raw transcript. C silently drops a task the customer asked for, leaving the address wrong without anyone knowing."
},
{
"id": "d5-03-q04",
"topic": "d5-03",
"domain": "d5",
"scenario": "Multi-Agent Research System",
"stem": "An architect is deciding where retry logic for transient network timeouts should live in a coordinator/subagent research system. Which placement follows the correct error-handling layering?",
"options": [
"Subagents retry transient failures like timeouts locally and propagate upward only the errors they cannot resolve themselves",
"All errors, including transient timeouts, propagate to the coordinator so recovery decisions are centralized and observable in one place",
"A dedicated error-handling subagent receives every failure from its siblings and decides the recovery strategy for each",
"Subagents absorb all errors silently and return whatever partial data they have, keeping the coordinator's logic simple"
],
"answer": 0,
"explanation": "The layering rule is local recovery for transient failures, propagation only for unresolvable ones — the coordinator should not be interrupted for a retryable blip. B floods the coordinator with noise it cannot act on better than the subagent that hit the timeout. C adds a peer-to-peer error channel, breaking hub-and-spoke routing through the coordinator. D is silent suppression, which converts failures into invisible data gaps."
},
{
"id": "d5-03-q05",
"topic": "d5-03",
"domain": "d5",
"scenario": "Multi-Agent Research System",
"stem": "Two of five research subagents have failed permanently after local retries, and the stakeholder deadline for the synthesized report is in an hour. The other three returned solid findings. What should the coordinator do?",
"options": [
"Rerun the full five-subagent pipeline once more, since a clean run may still complete before the deadline",
"Deliver the report built from the three successful streams, presented as complete so stakeholders are not distracted by pipeline internals",
"Synthesize the three successful streams now, with coverage annotations marking where evidence is missing",
"Hold the report until the two failed streams can be repaired, since an incomplete analysis is worse than a late one"
],
"answer": 2,
"explanation": "Honest partial delivery — proceed with what succeeded and annotate coverage gaps explicitly — beats both false completeness and total delay. A repeats work in the three healthy streams and gambles the deadline on an unchanged failure cause. B ships silent holes; readers will treat missing areas as investigated and empty. D turns a recoverable partial outcome into a missed deadline when annotated partials would serve the decision."
},
{
"id": "d5-04-q01",
"topic": "d5-04",
"domain": "d5",
"scenario": "Code Generation with Claude Code",
"stem": "Three hours into a refactoring session across a large monorepo, Claude Code starts describing how \"repository classes typically handle caching\" instead of citing the actual `CacheRepository` implementation it read earlier, and its answers about the same module contradict each other. What does this symptom indicate, and what is the right response?",
"options": [
"The model is hallucinating due to sampling randomness — set temperature to zero and re-ask the questions",
"The context has degraded under accumulated file reads and dead ends — the fix is architectural: delegate, compact, or reseed a fresh session",
"The codebase exceeds what the model can reason about — read every relevant file again at the start of each new question",
"The session needs a larger context window — switch to a model with a bigger window and continue in place, since extra capacity restores recall of earlier reads"
],
"answer": 1,
"explanation": "Generic-pattern answers plus inconsistency is the signature of context degradation, and the graded fixes are structural (subagent delegation, scratchpads, /compact, or a summary-seeded fresh session). A misattributes a context problem to sampling; determinism does not restore blurred specifics. C re-floods the window with reads, accelerating the same degradation. D assumes window size fixes attention quality, which it does not."
},
{
"id": "d5-04-q02",
"topic": "d5-04",
"domain": "d5",
"scenario": "Code Generation with Claude Code",
"stem": "On Friday an engineer ended a long Claude Code session full of file reads and greps mid-migration. Over the weekend, teammates merged forty commits touching the same modules. Monday morning, how should the engineer continue the work?",
"options": [
"Resume the Friday session with `--resume` so none of the accumulated understanding is lost",
"Run `/compact` on the resumed session first, then continue, since compaction removes the outdated portions",
"Resume the session but instruct the agent to re-read any file before editing it, keeping the old context as background",
"Start a fresh session seeded with a dense structured summary of Friday's findings, letting current file reads rebuild the details"
],
"answer": 3,
"explanation": "Most of the accumulated context is now stale, and resumed stale tool results are how agents confidently edit line numbers that no longer exist — a fresh session seeded with a structured summary is the reliable move. A carries forty commits' worth of wrong file contents forward as trusted context. B misunderstands /compact, which strips verbosity but cannot tell current reads from outdated ones. C keeps contradictory old reads in context, inviting the model to mix Friday's code with Monday's."
},
{
"id": "d5-04-q03",
"topic": "d5-04",
"domain": "d5",
"scenario": "Multi-Agent Research System",
"stem": "A coordinator has finished phase one of a codebase investigation and holds a rich picture of the module boundaries. For phase two it will spawn several analysis subagents. How should the phase-one knowledge reach them?",
"options": [
"Summarize the key phase-one findings and inject that summary into each subagent's initial prompt",
"No transfer step is needed — subagents spawned by the coordinator inherit its conversation history automatically",
"Let each subagent rediscover the codebase independently so its conclusions are not biased by the coordinator's framing",
"Paste the coordinator's full phase-one transcript, including every file read, into each subagent's prompt for maximum fidelity"
],
"answer": 0,
"explanation": "Subagents start blank — context must be explicitly passed — and a deliberate findings summary is the efficient seed. B is false: there is no automatic inheritance or shared memory between coordinator and subagents. C burns each subagent's window on redundant rediscovery the coordinator already paid for. D transfers the noise along with the signal, front-loading each subagent with the verbose reads that degrade context in the first place."
},
{
"id": "d5-04-q04",
"topic": "d5-04",
"domain": "d5",
"scenario": "Code Generation with Claude Code",
"stem": "A team runs a multi-agent overnight job that explores and annotates a large legacy codebase. Twice this month the container crashed at around 3 a.m. and the entire night's exploration was lost. What practice prevents that loss?",
"options": [
"Commit the working tree to git after each agent action so the repository always reflects the latest exploration",
"Keep each agent's findings in its own context window and extend the job timeout so crashes become less likely",
"Have each agent periodically export structured state to a manifest that the coordinator loads on resume",
"Rely on session resumption after restart, since the harness can reopen each agent's previous conversation"
],
"answer": 2,
"explanation": "Periodic structured-state manifests turn a crash into a minutes-long resume instead of a lost run. A captures code changes but not the exploration findings — signatures, mappings, notes — which live outside the working tree. B leaves all state in volatile context, which is exactly what a crash destroys. D depends on the crashed process's sessions surviving intact and still leaves no coordinator-readable record of overall progress."
},
{
"id": "d5-04-q05",
"topic": "d5-04",
"domain": "d5",
"scenario": "Code Generation with Claude Code",
"stem": "Mid-session, a Claude Code context is nearly full — mostly verbose grep output and file listings from earlier discovery — but every finding still reflects the current state of the repository and the agent is actively mid-task. What is the best next step?",
"options": [
"Start a fresh session immediately, since a nearly full context is unreliable regardless of freshness",
"Run `/compact` to strip the verbose discovery logs while keeping the live thread and its current findings",
"Manually delete the oldest half of the conversation, since age is the best proxy for irrelevance",
"Continue without intervention — the window has not actually overflowed yet, so nothing is lost"
],
"answer": 1,
"explanation": "When accumulated context is bulky but still current, /compact is the fitted tool: it reduces usage by stripping verbosity without abandoning the in-flight thread. A applies the fresh-session remedy meant for stale context, paying an unnecessary reseeding cost mid-task. C deletes by age rather than relevance, risking the early findings the task still depends on. D lets the session run into the degradation zone near the limit, where attention quality falls before the hard cutoff."
},
{
"id": "d5-05-q01",
"topic": "d5-05",
"domain": "d5",
"scenario": "Structured Data Extraction",
"stem": "An invoice-extraction pipeline reports 97% overall accuracy on the validation run, and the product owner wants to auto-approve everything above a confidence cutoff starting Monday. What must the team verify first?",
"options": [
"Accuracy broken down by document type and by field, since an aggregate figure can hide a segment performing far worse than the average",
"That the few-shot examples in the extraction prompt cover every field, since better prompting will lift the 97% before launch",
"That the confidence cutoff is at least 99%, since a stricter threshold guarantees the automated lane is safe",
"That the model version is pinned, since accuracy figures are only meaningful against a frozen model"
],
"answer": 0,
"explanation": "The aggregate-metric hazard is the tested point: 97% overall can coexist with 60% on a rare, business-critical document type, so segment-level validation must precede automation. B improves the pipeline but does not answer whether any segment is failing behind the average. C assumes uncalibrated raw scores map to safety, which they do not. D is sound hygiene but irrelevant to whether the current figure conceals a weak segment."
},
{
"id": "d5-05-q02",
"topic": "d5-05",
"domain": "d5",
"scenario": "Structured Data Extraction",
"stem": "Six months after launch, only low-confidence extractions get human review; the high-confidence lane flows straight to the database. An auditor asks how the team would notice if the model developed a new pattern of confident but wrong extractions. What is the correct mechanism?",
"options": [
"It is unnecessary — high confidence means the calibration already performed at launch continues to guarantee low error",
"Compare the outputs of two different models and flag disagreements, since independent agreement implies correctness",
"Tighten the review threshold so more borderline extractions receive human eyes",
"Continuously pull a stratified random sample from the high-confidence stream and measure its real error rate against human review"
],
"answer": 3,
"explanation": "Errors in an unreviewed lane are invisible by construction, and ongoing stratified random sampling is the only mechanism that detects a new class of confident hallucination after it appears. A assumes launch-time calibration holds forever, which drift defeats. B adds cost and still misses correlated errors both models share, and it is not the graded audit mechanism. C reviews more borderline cases but never touches the high-confidence lane where the hypothesized failure lives."
},
{
"id": "d5-05-q03",
"topic": "d5-05",
"domain": "d5",
"scenario": "Structured Data Extraction",
"stem": "A contract-extraction system emits one confidence score per document. A twenty-field contract scores 0.96 and skips review, yet its single hardest field — the liability cap — was extracted wrong. The nineteen trivial fields dominated the score. What is the structural fix?",
"options": [
"Lower the document-level review threshold to 0.98 so documents like this one fall into the review queue",
"Run the extraction twice and only auto-approve documents where both passes agree on every field",
"Have the model output field-level confidence scores and route individual low-confidence fields to review",
"Send every contract to human review, since legal documents are too high-stakes for confidence-based routing"
],
"answer": 2,
"explanation": "The granularity is the flaw: a document score averages away the one field that matters, and field-level confidence lets routing catch it directly. A still averages, so easy fields keep masking the hard one at any document-level threshold. B doubles cost and self-consistent errors pass both times, so agreement is not correctness. D abandons routing entirely, spending scarce reviewer attention uniformly instead of where risk concentrates."
},
{
"id": "d5-05-q04",
"topic": "d5-05",
"domain": "d5",
"scenario": "Structured Data Extraction",
"stem": "A team is choosing the confidence threshold below which extractions go to human review. One engineer proposes 0.8 \"because that is what everyone uses.\" What makes a threshold actually meaningful?",
"options": [
"Setting it so the resulting review volume exactly matches the reviewer headcount available each day",
"Calibrating it against a labeled validation set, so each score band maps to a measured error rate on data with known answers",
"Asking the model to justify each score in prose, then trusting scores whose justifications read as sound",
"Adopting the industry-standard 0.8 as proposed, since consistency with common practice eases audits"
],
"answer": 1,
"explanation": "A raw score is meaningless until you have measured what error rate each band corresponds to on labeled data — calibration is what turns a number into a routing decision. A sizes the queue to staffing rather than to accuracy, silently accepting whatever error rate falls outside capacity. C substitutes persuasive-sounding text for measurement; fluent justifications are not calibration. D imports a number with no relationship to this model, this document mix, or this error tolerance."
},
{
"id": "d5-05-q05",
"topic": "d5-05",
"domain": "d5",
"scenario": "Structured Data Extraction",
"stem": "After calibration, a document pipeline has two reviewers for thousands of daily extractions. The team debates how to spend that scarce attention. Which allocation gives the most reliability per reviewer-hour?",
"options": [
"Spread reviews uniformly at random across all extractions so that every document type and every confidence band has an equal chance of inspection",
"Assign reviewers exclusively to the newest document types, since mature types have already proven themselves",
"Review every low-confidence extraction and nothing else, since that is where the model itself signals doubt",
"Focus on weak fields and document types, contradictions, and low-confidence cases, plus a small random audit of the high-confidence lane"
],
"answer": 3,
"explanation": "Reviewer attention should be prioritized where errors historically concentrate — weak fields, weak document types, contradictions — while a random audit slice keeps the automated lane honest. A spends most hours on documents that are almost always right, buying little error detection. B ignores that mature types can regress and that known-weak segments still need coverage. C leaves the high-confidence lane entirely unaudited, so confident errors remain invisible."
},
{
"id": "d5-06-q01",
"topic": "d5-06",
"domain": "d5",
"scenario": "Multi-Agent Research System",
"stem": "In a research pipeline, search subagents cite sources in their prose, but after two summarization hops the final report contains claims nobody can trace back to a document. What is the structural fix?",
"options": [
"Require subagents to emit structured claim-source mappings and require downstream agents to preserve and merge them",
"Instruct the synthesis agent to append a bibliography of every source any subagent consulted at the end of the report",
"Add a post-processing pass in which a citation agent rereads the finished report and attaches the most likely source to each claim",
"Keep every subagent's full transcript in the coordinator's context so the original citations are always available for reference"
],
"answer": 0,
"explanation": "Provenance survives only if the hand-off format forces it: structured claim-source mappings — each claim paired with its source URL or document name, a relevant excerpt, and the publication date — preserved at every hop keep each claim attached to its origin. B lists sources without binding any claim to any of them, so \"says who?\" still has no answer. C guesses attribution after the link has already been destroyed, which invents rather than preserves provenance. D bloats the coordinator's context with raw transcripts and still leaves the summarization hops free to drop citations."
},
{
"id": "d5-06-q02",
"topic": "d5-06",
"domain": "d5",
"scenario": "Multi-Agent Research System",
"stem": "An analysis subagent finds that two credible industry reports state materially different market-share figures for the same company in the same year. Its output feeds the synthesis agent. What should the subagent do?",
"options": [
"Report the figure from the report with the stronger reputation, noting internally that the other was considered",
"Include both figures with their attributions, annotate the conflict, and leave reconciliation downstream",
"Report the midpoint of the two figures so the synthesis is not skewed toward either source",
"Omit both figures and mark market share as unavailable, since conflicting data cannot support a reliable claim"
],
"answer": 1,
"explanation": "The graded behavior for credible-source disagreement is both values, both attributions, an explicit conflict annotation, and deferred reconciliation. A arbitrarily selects a winner, hiding a real disagreement behind one agent's credibility heuristic. C invents a number neither source published, which is fabrication dressed as neutrality. D discards usable evidence entirely when an annotated conflict would inform the decision better than a gap."
},
{
"id": "d5-06-q03",
"topic": "d5-06",
"domain": "d5",
"scenario": "Structured Data Extraction",
"stem": "A synthesis stage flags a \"contradiction\": one extracted document says a compliance fine cap is $2M, another says $10M. A human check shows the first document is from 2021 and the second from 2024 — the cap was raised. The dates had been stripped during an earlier summarization step. What prevents this failure class?",
"options": [
"Instruct the synthesis agent to prefer whichever figure comes from the document with the larger sample or scope",
"Escalate every detected numeric conflict to a human reviewer so hallucinated inconsistencies are caught manually",
"Make publication or data-collection dates a required field in the structured outputs passed between stages",
"Re-run the extraction on both documents whenever a conflict is detected, since a second pass usually resolves the discrepancy"
],
"answer": 2,
"explanation": "Figures from different years are a trend, not a contradiction — but only if dates survive the pipeline, which is why they are required metadata, not optional. A applies a credibility heuristic to what is not actually a conflict. B catches the symptom at human expense on every numeric difference instead of removing the cause. D re-extracts the same date-stripped values and rediscovers the same false conflict."
},
{
"id": "d5-06-q04",
"topic": "d5-06",
"domain": "d5",
"scenario": "Multi-Agent Research System",
"stem": "A coordinator is designing the final report format for a research system whose findings vary widely in evidential support — some claims are corroborated by many sources, others rest on a single thin one. How should the report communicate this?",
"options": [
"With explicit sections that structurally separate well-established findings from contested or thinly-supported ones",
"In a single uniform narrative with a consistent confident tone, since hedging language undermines reader trust in the whole report",
"By excluding thinly-supported findings entirely, so everything in the report meets the same evidentiary bar",
"By appending a numeric confidence percentage to every individual sentence in the report"
],
"answer": 0,
"explanation": "Report-level honesty is structural: separating well-supported from contested findings makes the certainty distinction visible to readers. B presents weak and strong claims with equal confidence, exactly the undifferentiated-section trap. C hides potentially decision-relevant leads instead of presenting them with an honest support label. D drowns readers in per-sentence pseudo-precision that the underlying evidence cannot justify and readers cannot act on."
},
{
"id": "d5-06-q05",
"topic": "d5-06",
"domain": "d5",
"scenario": "Structured Data Extraction",
"stem": "A pipeline extracts financial tables, narrative risk discussion, and technical configuration details from filings, then a synthesis agent merges everything into flowing paragraphs \"for a consistent voice.\" Downstream analysts complain the numbers are now hard to verify and the configurations hard to apply. What should change?",
"options": [
"Keep the uniform prose but add an appendix that restates the key numbers, preserving the consistent voice for the main body",
"Convert the entire synthesis output to JSON, since a single machine-readable format serves every consumer",
"Render everything as tables, since tables are the most verifiable format for extracted data",
"Render each content type natively — tables for financial data, prose for narrative, lists for technical details"
],
"answer": 3,
"explanation": "Format is part of fidelity: each content type keeps its usable structure only when rendered natively, and uniform flattening is the named anti-pattern. A leaves the primary output degraded and forks the numbers into two places that can drift apart. B forces narrative discussion into a structure that strips its meaning for human readers. C does the same in the other direction, mangling prose findings into cells that misrepresent them."
}
];
window.CCAF_TOPICS = {
"d1-01": {
"title": "Agentic Loops",
"ts": "1.1"
},
"d1-02": {
"title": "Multi-Agent Orchestration",
"ts": "1.2"
},
"d1-03": {
"title": "Subagent Invocation & Context Passing",
"ts": "1.3"
},
"d1-04": {
"title": "Workflow Enforcement & Handoffs",
"ts": "1.4"
},
"d1-05": {
"title": "Agent SDK Hooks",
"ts": "1.5"
},
"d1-06": {
"title": "Task Decomposition Strategies",
"ts": "1.6"
},
"d1-07": {
"title": "Session State, Resumption & Forking",
"ts": "1.7"
},
"d2-01": {
"title": "Tool Descriptions & Interface Boundaries",
"ts": "2.1"
},
"d2-02": {
"title": "Structured Error Responses for MCP Tools",
"ts": "2.2"
},
"d2-03": {
"title": "Tool Distribution & tool_choice",
"ts": "2.3"
},
"d2-04": {
"title": "MCP Server Integration & Configuration",
"ts": "2.4"
},
"d2-05": {
"title": "Built-in Tools (Read, Write, Edit, Bash, Grep, Glob)",
"ts": "2.5"
},
"d3-01": {
"title": "CLAUDE.md Hierarchy",
"ts": "3.1"
},
"d3-02": {
"title": "Slash Commands & Skills",
"ts": "3.2"
},
"d3-03": {
"title": "Path-Specific Rules",
"ts": "3.3"
},
"d3-04": {
"title": "Plan Mode vs Direct Execution",
"ts": "3.4"
},
"d3-05": {
"title": "Iterative Refinement",
"ts": "3.5"
},
"d3-06": {
"title": "CI/CD Integration",
"ts": "3.6"
},
"d4-01": {
"title": "Explicit Review Criteria",
"ts": "4.1"
},
"d4-02": {
"title": "Few-Shot Prompting",
"ts": "4.2"
},
"d4-03": {
"title": "Structured Output via Tool Use & JSON Schemas",
"ts": "4.3"
},
"d4-04": {
"title": "Validation, Retry & Feedback Loops",
"ts": "4.4"
},
"d4-05": {
"title": "Batch Processing Strategies",
"ts": "4.5"
},
"d4-06": {
"title": "Multi-Instance & Multi-Pass Review",
"ts": "4.6"
},
"d5-01": {
"title": "Conversation Context in Long Interactions",
"ts": "5.1"
},
"d5-02": {
"title": "Escalation & Ambiguity Resolution",
"ts": "5.2"
},
"d5-03": {
"title": "Error Propagation in Multi-Agent Systems",
"ts": "5.3"
},
"d5-04": {
"title": "Context in Large Codebase Exploration",
"ts": "5.4"
},
"d5-05": {
"title": "Human Review & Confidence Calibration",
"ts": "5.5"
},
"d5-06": {
"title": "Provenance & Uncertainty in Multi-Source Synthesis",
"ts": "5.6"
}
};
