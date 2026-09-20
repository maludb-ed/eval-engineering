---
id: ev-6-02
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Prompt Caching Economics and Mechanics
task-statement: "4.5 Optimise token usage, latency, and cost-performance trade-offs"
scenarios: [2, 4, 6]
---

## Lesson

Prompt caching is the highest-leverage free win, and it is arithmetic. A **cache write costs 1.25× the base input price at the default five-minute TTL, or 2× at the one-hour TTL; a cache read costs 0.1×** — a tenth of the uncached price. Work the break-even: with a five-minute cache, the first call pays 1.25× and each later call pays 0.1×, so the cache pays for itself on the **second** use within the window. A one-hour cache, at 2× to write, needs roughly **three** uses. That is the whole decision procedure for whether to cache something, and it explains why caching a stable system prompt across a conversation is nearly always correct while caching a one-shot request is not.

The mechanics generate more items than the pricing does, because caching fails **silently**. Matching is by **prefix**: content is rendered in the order tools → system → messages, and any byte change anywhere in the prefix invalidates everything after it. So the breakpoint goes on the **last block that stays identical across requests**, never on a per-request block — a `cache_control` marker sitting on a timestamped or user-specific block produces a new hash every call and zero hits. A request may carry up to **four explicit breakpoints**, with a 20-block lookback when locating a prior write. The **minimum cacheable prefix is model-dependent**: 512 tokens on Opus 5 and the Fable family, 1,024 on Sonnet 5 and Opus 4.8, and 4,096 on Haiku 4.5 — and shorter prefixes simply do not cache, with no error raised. That last figure is a genuine trap: the cheapest model has the highest cache minimum, so a "use Haiku and cache everything" answer can fail on both halves.

Because the failure is silent, **verification is part of the design**: read `cache_read_input_tokens` in the response usage, and if it stays at zero across repeated requests, something in the prefix is changing. The usual culprits are a current timestamp in the system prompt, non-deterministically ordered JSON or tool definitions, a tool set that varies per request, and settings that invalidate at their level — changing tool definitions invalidates everything, while tool choice, images, and thinking parameters invalidate at their own level and below. Two refinements worth knowing: caches are **model-scoped**, so routing the same prefix across several models forfeits reuse; and for latency-critical paths the cache can be **pre-warmed** with a `max_tokens: 0` request before traffic arrives.

## Key facts

- Cache writes cost 1.25× base input at the 5-minute TTL, 2× at the 1-hour TTL; cache reads cost 0.1×.
- Break-even: a 5-minute cache pays off on the second use; a 1-hour cache on roughly the third.
- Matching is prefix-based; render order is tools → system → messages.
- Place the breakpoint on the last block that is identical across requests; never on a per-request block.
- Up to 4 explicit breakpoints per request, with a 20-block lookback for prior writes.
- Minimum cacheable prefix is model-dependent — 512 tokens (Opus 5, Fable family), 1,024 (Sonnet 5, Opus 4.8), 4,096 (Haiku 4.5).
- Prefixes below the minimum silently do not cache; no error is returned.
- Verify with `cache_read_input_tokens`; a persistent zero means a silent invalidator.
- Common invalidators: timestamps in the system prompt, unsorted JSON or tool definitions, a varying tool set, changed tool definitions, tool choice, images, thinking parameters.
- Caches are model-scoped; pre-warming is possible with a `max_tokens: 0` request.
- Prices quoted here are ratios to base input price — the ratios are the stable part; verify absolute prices against current documentation.

## Exam traps

- A `cache_control` breakpoint placed on the newest message or on a block containing a timestamp.
- Caching claimed as a win for a short prompt below the model's minimum cacheable length.
- A one-hour TTL chosen for traffic that recurs every few seconds, paying 2× to write for no additional benefit.
- Reporting cache savings with no check of `cache_read_input_tokens`.
- A model-routing cascade presented as pure savings, ignoring the forfeited cache reuse across models.
- Dynamic per-request content (user name, current date) placed in the system prompt ahead of the cached material.
