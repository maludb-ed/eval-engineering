---
id: ev-5-04
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Diagnosing Retrieval and RAG Failures
task-statement: "4.4 Diagnose system issues (prompt failure, hallucinations, model mismatch)"
scenarios: [4, 2, 1]
---

## Lesson

This is the highest-probability scenario in the whole task statement, and the published study guidance states it almost as a rule: **when a RAG system returns confident-but-wrong answers after a document refresh, investigate the retrieval and indexing step first.** The logic is M5's method applied — the corpus is what changed, so the corpus and the pipeline that indexes it are the first hypothesis. The specific defects to have in mind are a refresh that partially failed or is still running, an index rebuilt with a different embedding model than the one used at query time, a chunking or preprocessing change that split passages differently, stale entries left behind so old and new versions both retrieve, and metadata or permission filters that silently exclude the new documents. Every one of those produces the same user-visible symptom: a fluent answer built from the wrong context.

The structural fix for diagnosability is to **measure retrieval and generation separately**. Retrieval is evaluated on whether the passage containing the answer was returned at all — recall at k, precision at k, and rank-sensitive measures where ordering matters. Generation is evaluated on faithfulness to whatever was retrieved, plus answer relevance. A single end-to-end accuracy number cannot separate "we never retrieved the right passage" from "we retrieved it and answered wrongly anyway," and those have opposite fixes: the first points at chunking, embeddings, k, or reranking; the second points at the prompt, the context budget, or the model. Any RAG system that has run into trouble twice should have these two metrics split permanently, not reconstructed during an incident.

Two further cases are worth recognising. **Retrieved but not used**: the right passage was in context and the answer still ignored it, which points at context assembly — buried in a long context, crowded out by lower-ranked chunks, or overridden by a confident system prompt — rather than at retrieval. And **freshness versus correctness**: a system answering correctly from a document that has since been superseded is not hallucinating, it is serving stale ground truth, and the fix lives in the ingestion pipeline and in surfacing document dates, not in the model. When a stem mentions quarterly policy updates or a document refresh, freshness is almost always the axis in play.

## Key facts

- Confident-but-wrong RAG answers after a document refresh: investigate retrieval and indexing first.
- Common indexing defects: partial or in-flight refresh, embedding-model mismatch between index and query time, changed chunking or preprocessing, stale duplicate entries, over-restrictive metadata or permission filters.
- Measure retrieval and generation separately: recall/precision at k and rank-sensitive measures for retrieval; faithfulness and answer relevance for generation.
- End-to-end accuracy cannot distinguish a retrieval miss from a generation failure, and the two have opposite fixes.
- Retrieval fixes: chunking strategy, embedding model, k, reranking, query rewriting.
- Generation fixes: prompt, context budget, ordering of retrieved material, model tier.
- "Retrieved but not used" is a context-assembly failure — position, crowding, or an overriding instruction.
- Serving a superseded document is a freshness failure in ingestion, not a model failure.

## Exam traps

- Prompt tuning or a model upgrade offered as the first response to degraded RAG quality after a corpus change.
- A single end-to-end accuracy metric proposed as sufficient monitoring for a RAG pipeline.
- Increasing k as a general remedy without establishing whether the answer passage is being retrieved at all.
- Treating stale-document answers as hallucination.
- Rebuilding the index with a new embedding model while leaving the query-side encoder unchanged.
- Adding a reranker when the diagnosis shows the right passage was already retrieved and ignored.
