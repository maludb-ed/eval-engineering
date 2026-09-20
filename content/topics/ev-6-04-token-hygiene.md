---
id: ev-6-04
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Input and Output Token Hygiene
task-statement: "4.5 Optimise token usage, latency, and cost-performance trade-offs"
scenarios: [3, 4, 6]
---

## Lesson

Token hygiene is where the free wins live after caching, and it splits by direction. On the **input** side the usual excesses are retrieval that returns more passages than the answer needs, tool results accumulating in context turn after turn, a tool catalogue where every definition is sent on every request, and instructions that have grown by accretion. Retrieval trimming is the highest-yield of these and interacts with quality in both directions: fewer, better-ranked passages usually improve faithfulness as well as cost, because the M5 "retrieved but not used" failure is partly a crowding problem. For large tool catalogues, deferred tool loading with a search tool keeps definitions out of the prompt until they are needed — with the constraint that not everything can be deferred; at least one tool, including the search tool itself, must remain loaded.

For long-running conversations there are two distinct mechanisms that candidates routinely conflate. **Context editing clears** — it removes old tool results or thinking blocks from the conversation before the model sees them. **Compaction summarises** — it replaces earlier history with a summary when the context approaches a threshold. Clearing is cheap and lossless for material that is genuinely spent, such as a tool result already acted on; summarising preserves the gist of a long history at the cost of detail, which is exactly the M5 failure mode where a constraint stated once gets dropped. Choose by what the agent still needs: clear what is finished, summarise what must persist in outline, and keep anything the task depends on verbatim.

On the **output** side, generated tokens cost several times input tokens at every tier, so output discipline pays disproportionately. Use `max_tokens` as a hard ceiling while knowing it is blunt — the response is simply cut off, possibly mid-word, so it suits short-answer and classification routes rather than prose. Ask for concision in the prompt, and express limits in sentences or paragraphs rather than word counts, because models count tokens, not words. Structured output reduces the prose wrapped around the answer and makes the result gradeable at the same time, which is the same decision M3 recommends for a different reason. And in agentic loops, output discipline compounds: every wasted turn writes tokens and then re-reads them as input on the next request.

## Key facts

- Input-side waste: over-retrieval, accumulated tool results, always-loaded tool catalogues, accreted instructions.
- Retrieval trimming often improves faithfulness as well as cost by reducing crowding in context.
- Deferred tool loading with a tool-search tool keeps unused definitions out of the prompt; at least one tool, including the search tool, must stay loaded.
- Context editing clears old tool results or thinking blocks; compaction summarises earlier history — different mechanisms, different losses.
- Clear what is finished; summarise what must persist in outline; keep task-critical details verbatim.
- Output tokens cost several times input tokens at every tier, so output discipline pays disproportionately.
- `max_tokens` is a hard ceiling that truncates mid-response; suited to short-answer routes, not prose.
- Express length limits in sentences or paragraphs — models count tokens, not words.
- Structured output reduces surrounding prose and makes results gradeable.
- In agent loops, tokens written this turn are re-read as input next turn, so wasted turns cost twice.

## Exam traps

- Compaction and context editing treated as the same mechanism, or summarisation chosen where the agent still needs the detail verbatim.
- `max_tokens` used to shorten long prose answers, producing truncated output instead of concise output.
- A word-count instruction used to control output length.
- Increasing retrieved passages to improve accuracy when the diagnosis shows crowding, not missing context.
- Every tool definition sent on every request in a system with a large tool catalogue.
- Trimming input tokens while an agent burns turns re-reading the same material each iteration.
