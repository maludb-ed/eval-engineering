---
id: ev-6-05
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Latency Levers and Their Trades
task-statement: "4.5 Optimise token usage, latency, and cost-performance trade-offs"
scenarios: [1, 3, 4]
---

## Lesson

Latency work begins by naming which latency is the problem, because the levers differ. **Time to first token** governs whether an interface feels responsive; **baseline latency** — total time to a finished response — governs whether a pipeline stage meets its budget. Streaming improves the first and does nothing for the second, which is the cleanest distinction in this topic: for a chat interface, streaming is a real fix; for a batch extraction job, it is irrelevant. The documented lever order is **model choice first** (Haiku 4.5 is positioned for speed-critical paths), then **prompt and output token reduction**, then **streaming** for perceived responsiveness — with the standing caution that quality comes first and premature latency work hides what good looks like.

Output length is the dominant term in total latency because tokens are generated serially: halving generated tokens roughly halves generation time, while halving input tokens helps much less. So concision instructions, structured output, and appropriate `max_tokens` are latency levers as much as cost levers. Thinking effort belongs here too — lower effort means less reasoning to generate, so it buys latency and cost together on routes that do not need the depth. Beyond the single call, pipeline latency is the sum of its stages plus the serial dependencies between them: parallelising independent tool calls, overlapping retrieval with other work, and removing a verification pass that fires on every request rather than on the uncertain ones all attack wall-clock time that no per-call tuning will reach.

Two further options are worth knowing as the trade they represent. **Fast mode** — a research preview on Opus 5 and Opus 4.8 — runs the same model at up to 2.5× higher output tokens per second at premium pricing, and it is the explicit "pay more for speed" direction; it is not available with batch, and switching the speed setting invalidates the prompt cache. **Batch** is the opposite trade: half price for asynchronous turnaround. Caching, unusually, helps both axes at once — a cache read is cheaper *and* faster than reprocessing the prefix, which is why pre-warming the cache is a latency technique on latency-critical paths. Whatever you change, state the target as a percentile against the M1 criteria, and check the cost guardrail: the fastest configuration is frequently the most expensive one.

## Key facts

- TTFT governs perceived responsiveness; baseline latency governs total time to a finished response.
- Streaming improves TTFT and perceived latency; it does not reduce total latency.
- Documented lever order: choose the right model, reduce prompt and output tokens, then stream.
- Haiku 4.5 is positioned for speed-critical paths.
- Output length dominates total latency because generation is serial; reducing generated tokens is the strongest single-call lever.
- Lower thinking effort reduces latency and cost together on routes that do not need the depth.
- Pipeline latency is attacked by parallelising independent calls and removing always-on verification passes, not by per-call tuning alone.
- Fast mode (research preview, Opus 5 and Opus 4.8) trades premium pricing for up to 2.5× output tokens per second; unavailable with batch, and switching speed invalidates the prompt cache.
- Batch is the opposite trade: 50% cost for asynchronous turnaround.
- Cache reads are both cheaper and faster than reprocessing; pre-warming helps latency-critical paths.
- Express latency targets as percentiles and pair every latency change with a cost guardrail.

## Exam traps

- Streaming offered as a fix for total latency, or for a batch or non-interactive workload.
- Latency optimisation started before the system meets its quality bar.
- Input-token trimming pursued for a latency problem dominated by long generated output.
- A latency target stated as an average.
- Fast mode or a premium speed setting adopted with no cost guardrail.
- Per-call tuning applied to a pipeline whose wall-clock is dominated by serial stages or an always-on verification step.
