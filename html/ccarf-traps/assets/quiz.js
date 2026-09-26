/* CCAR-F practice + mock exam engine. Renders into #quiz. */
(function () {
  "use strict";
  var Q = window.CCAF_QUESTIONS || [];
  var TOPICS = window.CCAF_TOPICS || {};
  var DOMS = { d1: "D1 Agentic Architecture", d2: "D2 Tool Design & MCP", d3: "D3 Claude Code Config", d4: "D4 Prompting & Output", d5: "D5 Context & Reliability" };
  var WEIGHTS = { d1: 27, d2: 18, d3: 20, d4: 20, d5: 15 };
  var SCENARIOS = ["Customer Support Resolution Agent", "Code Generation with Claude Code", "Multi-Agent Research System", "Developer Productivity with Claude", "Claude Code for Continuous Integration", "Structured Data Extraction"];
  var store = window.ccarpStore || { get: function (k, f) { return f; }, set: function () {} };
  var root = document.getElementById("quiz");
  if (!root) return;
  var S = null, timerId = null;
  function stopTimer() { if (timerId) clearInterval(timerId); timerId = null; }

  function el(tag, attrs, kids) {
    var n = document.createElement(tag);
    if (attrs) Object.keys(attrs).forEach(function (k) {
      var v = attrs[k];
      if (v === null || v === undefined || v === false) return;
      if (k === "text") n.textContent = v;
      else if (k === "class") n.className = v;
      else if (k.slice(0, 2) === "on") n.addEventListener(k.slice(2), v);
      else n.setAttribute(k, v === true ? "" : v);
    });
    (kids || []).forEach(function (c) { if (c) n.appendChild(typeof c === "string" ? document.createTextNode(c) : c); });
    return n;
  }
  function shuffle(a) { a = a.slice(); for (var i = a.length - 1; i > 0; i--) { var j = Math.floor(Math.random() * (i + 1)); var t = a[i]; a[i] = a[j]; a[j] = t; } return a; }
  function topicLink(tid) {
    var t = TOPICS[tid] || { title: tid, ts: "" };
    return el("a", { href: "domains.html#ts-" + t.ts.replace(".", "-"), text: t.ts + " " + t.title });
  }
  function fmtTime(sec) { var m = Math.floor(sec / 60), s = sec % 60; return m + ":" + (s < 10 ? "0" : "") + s; }
  function top() { window.scrollTo({ top: Math.max(0, root.offsetTop - 80) }); }

  // ---------------- Setup ----------------
  function renderSetup() {
    stopTimer();
    root.innerHTML = "";
    var last = store.get("ccarf-quiz-last", null);
    var dom = el("select", { "aria-label": "Domain" }, [el("option", { value: "all", text: "All domains" })]);
    Object.keys(DOMS).forEach(function (d) { dom.appendChild(el("option", { value: d, text: DOMS[d] + " (" + Q.filter(function (q) { return q.domain === d; }).length + ")" })); });
    var scen = el("select", { "aria-label": "Scenario" }, [el("option", { value: "all", text: "All scenarios" })]);
    SCENARIOS.forEach(function (s) { scen.appendChild(el("option", { value: s, text: s })); });
    var params = new URLSearchParams(location.search);
    var pd = params.get("domain"), ps = params.get("scenario");
    if (pd && DOMS[pd]) dom.value = pd;
    if (ps && SCENARIOS[+ps - 1]) scen.value = SCENARIOS[+ps - 1];
    var len = el("select", { "aria-label": "Session length" }, [
      el("option", { value: "10", text: "10 questions" }), el("option", { value: "20", text: "20 questions" }), el("option", { value: "0", text: "All matching" })
    ]);

    root.appendChild(el("div", { class: "grid grid-2" }, [
      el("div", { class: "card" }, [
        el("h2", { text: "Practice mode", style: "margin-top:0" }),
        el("p", { class: "src", text: "Instant feedback and an explanation after every question. Filter by domain or scenario to drill a weak spot." }),
        el("div", { class: "toolbar", style: "margin:0 0 1rem" }, [dom, scen, len]),
        el("button", { class: "btn", type: "button", text: "Start practice", onclick: function () {
          var pool = Q.filter(function (q) { return (dom.value === "all" || q.domain === dom.value) && (scen.value === "all" || q.scenario === scen.value); });
          pool = shuffle(pool); var n = +len.value; if (n) pool = pool.slice(0, n);
          if (!pool.length) { alert("No questions match that filter."); return; }
          startPractice(pool);
        } })
      ]),
      el("div", { class: "card" }, [
        el("h2", { text: "Mock exam", style: "margin-top:0" }),
        el("p", { class: "src", text: "Mirrors the real format: 60 questions from 4 randomly chosen scenarios, weighted by domain, a 120-minute clock, flagging, and no feedback until you submit." }),
        el("p", { class: "src", text: "Practise the two-pass plan: guess, flag, finish, then return." }),
        el("button", { class: "btn", type: "button", text: "Start 60-question mock", onclick: startMock })
      ])
    ]));
    if (last) root.appendChild(el("p", { class: "src", style: "margin-top:1rem", text: "Last session (" + last.mode + "): " + last.correct + " / " + last.total + " (" + last.pct + "%) on " + last.date + "." }));
  }

  // ---------------- Practice mode ----------------
  function startPractice(pool) { S = { mode: "practice", items: pool.map(function (q) { return { q: q, pick: null }; }), i: 0 }; renderPractice(); }

  function questionBlock(item, locked, onPick) {
    var q = item.q;
    var list = el("ul", { class: "options", role: "radiogroup" });
    q.options.forEach(function (t, i) {
      var id = "o-" + q.id + "-" + i;
      var input = el("input", { type: "radio", name: "opt-" + q.id, id: id, value: String(i), checked: item.pick === i, disabled: locked });
      input.addEventListener("change", function () { onPick(i); });
      var li = el("li", { class: "option" }, [el("label", { for: id }, [input, el("span", null, [el("span", { class: "letter", text: "ABCD"[i] + "." }), t])])]);
      if (locked) {
        if (i === q.answer) li.classList.add("is-correct");
        else if (item.pick === i) li.classList.add("is-wrong");
      }
      list.appendChild(li);
    });
    return list;
  }

  function renderPractice() {
    var item = S.items[S.i], q = item.q, answered = item.checked;
    root.innerHTML = "";
    var check = el("button", { class: "btn", type: "button", text: "Check answer", disabled: item.pick === null || answered });
    var next = el("button", { class: "btn", type: "button", hidden: !answered, text: S.i === S.items.length - 1 ? "See results" : "Next question →" });
    var list = questionBlock(item, answered, function (i) { item.pick = i; check.removeAttribute("disabled"); });
    check.addEventListener("click", function () { item.checked = true; renderPractice(); });
    next.addEventListener("click", function () { if (S.i < S.items.length - 1) { S.i++; renderPractice(); top(); } else renderResults(); });
    var fb = null;
    if (answered) {
      var ok = item.pick === q.answer;
      fb = el("div", { class: "callout " + (ok ? "ok" : "warn") }, [
        el("p", { class: "verdict " + (ok ? "ok" : "bad"), text: ok ? "✓ Correct. Now read why each distractor is wrong." : "✗ Not quite. The answer is " + "ABCD"[q.answer] + "." }),
        el("p", { text: q.explanation }),
        el("p", { class: "src" }, ["Topic: ", topicLink(q.topic)])
      ]);
    }
    root.appendChild(el("div", { class: "card question" + (answered ? " answered" : "") }, [
      el("div", { class: "quiz-meta" }, [
        el("span", { text: "Question " + (S.i + 1) + " of " + S.items.length }),
        el("span", { class: "chip dom", text: DOMS[q.domain] }),
        el("span", { class: "chip", text: q.scenario })
      ]),
      el("div", { class: "progress-line", "aria-hidden": "true" }, [el("span", { style: "width:" + Math.round(S.i / S.items.length * 100) + "%" })]),
      el("h2", { text: q.stem }),
      list,
      fb,
      el("div", { class: "btn-row" }, [check, next, el("button", { class: "btn secondary", type: "button", text: "End session", onclick: function () { renderResults(false); } })])
    ]));
  }

  // ---------------- Mock mode ----------------
  function startMock() {
    var scen = shuffle(SCENARIOS).slice(0, 4);
    var pool = Q.filter(function (q) { return scen.indexOf(q.scenario) !== -1; });
    var target = 60, picked = [];
    // Domain quotas follow the official weights (16/11/12/12/9). Each quota is filled from the drawn
    // scenarios first; if the bank has too few there, it borrows that domain's questions from the others.
    var others = Q.filter(function (q) { return scen.indexOf(q.scenario) === -1; });
    var byDom = {};
    Object.keys(WEIGHTS).forEach(function (d) {
      byDom[d] = shuffle(pool.filter(function (q) { return q.domain === d; }))
        .concat(shuffle(others.filter(function (q) { return q.domain === d; })));
    });
    Object.keys(WEIGHTS).forEach(function (d) { picked = picked.concat(byDom[d].splice(0, Math.round(target * WEIGHTS[d] / 100))); });
    var rest = shuffle([].concat.apply([], Object.keys(byDom).map(function (d) { return byDom[d]; })));
    while (picked.length < target && rest.length) picked.push(rest.pop());
    picked = shuffle(picked.slice(0, target));
    S = { mode: "mock", scenarios: scen, items: picked.map(function (q) { return { q: q, pick: null, flag: false }; }), i: 0, end: Date.now() + 120 * 60 * 1000 };
    stopTimer();
    timerId = setInterval(tick, 1000);
    renderMock();
  }
  function tick() {
    var left = Math.max(0, Math.round((S.end - Date.now()) / 1000));
    var t = document.querySelector("[data-timer]");
    if (t) { t.textContent = fmtTime(left); t.style.color = left < 600 ? "var(--bad)" : ""; }
    if (left === 0) { stopTimer(); renderResults(true); }
  }
  function renderMock() {
    var item = S.items[S.i], q = item.q;
    root.innerHTML = "";
    var palette = el("div", { style: "display:flex;flex-wrap:wrap;gap:4px;margin:10px 0" });
    S.items.forEach(function (it, idx) {
      var st = idx === S.i ? "var(--accent)" : it.flag ? "var(--warn)" : it.pick !== null ? "var(--ok)" : "var(--border)";
      palette.appendChild(el("button", {
        type: "button", "aria-label": "Question " + (idx + 1) + (it.flag ? ", flagged" : "") + (it.pick !== null ? ", answered" : ""),
        style: "width:30px;height:30px;border-radius:6px;font:inherit;font-size:.75rem;cursor:pointer;border:2px solid " + st + ";background:" + (it.pick !== null ? "var(--surface-2)" : "var(--surface)") + ";color:var(--text)",
        text: String(idx + 1), onclick: function () { S.i = idx; renderMock(); }
      }));
    });
    var answeredN = S.items.filter(function (it) { return it.pick !== null; }).length;
    var flagged = S.items.filter(function (it) { return it.flag; }).length;
    root.appendChild(el("div", { class: "card question" }, [
      el("div", { class: "quiz-meta" }, [
        el("strong", { text: "Mock exam" }),
        el("span", { text: "Question " + (S.i + 1) + " of " + S.items.length }),
        el("span", { class: "chip", text: q.scenario }),
        el("span", { style: "margin-left:auto;font-variant-numeric:tabular-nums;font-weight:800", "data-timer": true, text: fmtTime(Math.max(0, Math.round((S.end - Date.now()) / 1000))) })
      ]),
      el("details", null, [el("summary", { class: "src", text: answeredN + " answered · " + flagged + " flagged · " + (S.items.length - answeredN) + " blank. Show question grid" }), palette]),
      el("h2", { text: q.stem }),
      questionBlock(item, false, function (i) { item.pick = i; }),
      el("div", { class: "btn-row" }, [
        el("button", { class: "btn secondary", type: "button", text: "← Prev", disabled: S.i === 0, onclick: function () { S.i--; renderMock(); } }),
        el("button", { class: "btn secondary", type: "button", text: item.flag ? "⚑ Unflag" : "⚐ Flag for review", onclick: function () { item.flag = !item.flag; renderMock(); } }),
        el("button", { class: "btn", type: "button", text: S.i === S.items.length - 1 ? "Review & submit" : "Next →", onclick: function () {
          if (S.i < S.items.length - 1) { S.i++; renderMock(); top(); }
          else confirmSubmit();
        } }),
        el("button", { class: "btn secondary", type: "button", text: "Submit exam", onclick: confirmSubmit })
      ])
    ]));
  }
  function confirmSubmit() {
    var blank = S.items.filter(function (it) { return it.pick === null; }).length;
    var flagged = S.items.filter(function (it) { return it.flag; }).length;
    var msg = "Submit the mock exam?";
    if (blank) msg += "\n\n" + blank + " question(s) are blank. A blank can never be marked correct, so guess before submitting.";
    if (flagged) msg += "\n" + flagged + " question(s) are still flagged.";
    if (window.confirm(msg)) { stopTimer(); renderResults(); }
  }

  // ---------------- Results ----------------
  function renderResults(timedOut) {
    stopTimer();
    var items = S.mode === "practice" ? S.items.filter(function (it) { return it.checked; }) : S.items;
    var correct = items.filter(function (it) { return it.pick === it.q.answer; }).length;
    var pct = items.length ? Math.round(correct / items.length * 100) : 0;
    store.set("ccarf-quiz-last", { mode: S.mode, correct: correct, total: items.length, pct: pct, date: new Date().toISOString().slice(0, 10) });

    var agg = function (key) {
      var m = {};
      items.forEach(function (it) { var k = key(it.q); m[k] = m[k] || { c: 0, n: 0 }; m[k].n++; if (it.pick === it.q.answer) m[k].c++; });
      return m;
    };
    var byDom = agg(function (q) { return q.domain; });
    var byTopic = agg(function (q) { return q.topic; });
    var domList = el("ul", { class: "trap-tally" });
    Object.keys(byDom).sort().forEach(function (d) { domList.appendChild(el("li", null, [el("span", { text: DOMS[d] }), el("strong", { text: byDom[d].c + " / " + byDom[d].n })])); });
    var weak = Object.keys(byTopic).filter(function (t) { return byTopic[t].c < byTopic[t].n; }).sort(function (a, b) { return (byTopic[a].c / byTopic[a].n) - (byTopic[b].c / byTopic[b].n); });
    var weakList = el("ul", { class: "trap-tally" });
    weak.slice(0, 8).forEach(function (t) { weakList.appendChild(el("li", null, [topicLink(t), el("strong", { text: byTopic[t].c + " / " + byTopic[t].n })])); });

    var missed = items.filter(function (it) { return it.pick !== it.q.answer; });
    var review = null;
    if (S.mode === "mock" && missed.length) {
      review = el("div", { style: "margin-top:1.5rem" }, [el("h3", { text: "Review what you missed" })]);
      missed.forEach(function (it, n) {
        var q = it.q;
        review.appendChild(el("details", { class: "card", style: "margin-bottom:10px" }, [
          el("summary", { style: "cursor:pointer;font-weight:600", text: (n + 1) + ". " + q.stem.slice(0, 140) + (q.stem.length > 140 ? "…" : "") }),
          el("div", { class: "answered", style: "margin-top:10px" }, [
            el("p", { text: q.stem }),
            questionBlock(it, true, function () {}),
            el("p", { class: "verdict " + (it.pick === null ? "bad" : "bad"), text: it.pick === null ? "Left blank. Answer: " + "ABCD"[q.answer] + "." : "You chose " + "ABCD"[it.pick] + ". Answer: " + "ABCD"[q.answer] + "." }),
            el("p", { text: q.explanation }),
            el("p", { class: "src" }, ["Topic: ", topicLink(q.topic)])
          ])
        ]));
      });
    }

    root.innerHTML = "";
    root.appendChild(el("div", { class: "card" }, [
      el("h2", { text: S.mode === "mock" ? "Mock exam results" : "Session results", style: "margin-top:0" }),
      timedOut ? el("p", { class: "verdict bad", text: "Time's up. Unanswered questions count as wrong, just like the real exam." }) : null,
      el("div", { class: "score-big", text: correct + " / " + items.length }),
      el("p", { class: "src", text: pct + "% correct." + (S.mode === "mock" ? " Scenarios emphasized: " + S.scenarios.join(", ") + ". The real exam reports a scaled score (pass = 720); a raw percentage here doesn't convert to that scale, so treat it as a trend line and aim well above 72%." : "") }),
      el("div", { class: "grid grid-2", style: "margin-top:1rem" }, [
        el("div", null, [el("h3", { text: "By domain" }), domList]),
        el("div", null, [el("h3", { text: "Weakest topics" }), weak.length ? weakList : el("p", { class: "src", text: "No misses this session." })])
      ]),
      review,
      el("div", { class: "btn-row", style: "margin-top:1.2rem" }, [
        S.mode === "practice" && missed.length ? el("button", { class: "btn", type: "button", text: "Retry the " + missed.length + " missed", onclick: function () {
          startPractice(missed.map(function (it) { return it.q; }));
        } }) : null,
        el("button", { class: "btn secondary", type: "button", text: "Back to setup", onclick: renderSetup })
      ])
    ]));
    top();
  }

  window.addEventListener("beforeunload", function (e) {
    if (S && S.mode === "mock" && timerId) { e.preventDefault(); e.returnValue = ""; }
  });
  renderSetup();
})();
