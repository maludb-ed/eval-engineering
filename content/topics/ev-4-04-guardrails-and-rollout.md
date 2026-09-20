---
id: ev-4-04
domain: 4
domain-name: Evaluation, Testing & Optimisation
weight: 16
title: Guardrails, Progressive Rollout, and Online Signals
task-statement: "4.3 Conduct A/B testing and iterative improvements"
scenarios: [1, 3, 4]
---

## Lesson

Every experiment needs metrics it is not trying to improve but must not damage. **Guardrail metrics** are the multi-axis target set from M1 pressed into service during a rollout: cost per resolved task, p95 latency, refusal rate, escalation rate, unsafe-output rate. They carry explicit thresholds and an automatic stop, so a variant that lifts resolution rate by 4% while raising cost per ticket by 60% is caught by the design rather than by an invoice three weeks later. A rollout plan with a primary metric and no guardrails is the single most reliable distractor in this topic.

Exposure is then bought gradually, and the mechanisms differ in what they can prove. **Shadow mode** runs the candidate on real traffic without serving its output: it validates parity, cost, latency, and error rates against the real distribution at zero user risk, and it cannot measure user response, because no user saw anything. A **canary** serves a small slice of real traffic under close monitoring, which is the first point at which user-visible harm is possible and therefore the first point guardrail stops must be armed. **Staged rollout** ramps the slice on a schedule with the same stops at each step. **Champion/challenger** keeps a challenger running continuously against the incumbent, which suits systems where the input distribution keeps moving. Choosing between them is a risk question: the more irreversible the action the system takes, the more of the sequence you run.

Online signals are what the monitoring watches, and their biases are exam material. **Explicit feedback** — thumbs up and down — is sparse and skewed toward extremes, so it is directional evidence, not a quality metric. **Implicit signals** are usually stronger: escalation to a human, conversation abandonment, retry rate, and edit distance between the system's draft and what the user actually sent. Business outcomes — resolution rate, time to close, claim cycle time — are the ones stakeholders act on and the slowest to move. The productive use of all of these is not to score the system but to *route attention*: a spike in escalations selects the transcripts worth reading, and those transcripts become the next eval tasks.

## Key facts

- Guardrail metrics are named before launch with thresholds and an automatic stop: cost per task, p95 latency, refusal rate, escalation rate, unsafe-output rate.
- Shadow mode validates parity, cost, latency, and errors on real traffic with no user exposure — and cannot measure user response.
- Canary exposes a small slice of real traffic; guardrail stops must be armed before it begins.
- Staged rollout ramps exposure on a schedule, re-checking guardrails at each step.
- Champion/challenger runs a continuous comparison, suited to shifting input distributions.
- The more irreversible the system's actions, the more of the shadow → canary → staged sequence is warranted.
- Thumbs up/down is sparse and extreme-biased — directional evidence, not a quality metric.
- Implicit signals — escalation, abandonment, retries, edit distance — are usually stronger than explicit ratings.
- Online signals are best used to route attention to transcripts, which become new eval tasks.

## Exam traps

- A rollout plan with a primary metric and no guardrails, or guardrails with no stop condition.
- Shadow mode credited with measuring user satisfaction or engagement.
- Canary traffic served before monitoring and stop criteria exist.
- Thumbs-down rate adopted as the primary quality metric for a system with sparse feedback.
- A full rollout for an agent taking irreversible actions, justified by strong offline results.
- Collecting user feedback with no path from a feedback spike to transcript review and new eval tasks.
