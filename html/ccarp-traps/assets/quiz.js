/* CCAR-P practice quiz engine. Renders into #quiz. */
(function () {
  "use strict";
  var Q = window.CCARP_QUESTIONS || [];
  var TRAPS = window.CCARP_TRAP_NAMES || {};
  var DOMS = window.CCARP_DOMAINS || {};
  var store = window.ccarpStore || { get: function (k, f) { return f; }, set: function () {} };
  var GROUP = { 1: "found", 2: "found", 3: "found", 4: "found", 11: "found", 5: "pro", 6: "pro", 7: "pro", 8: "pro", 9: "pro", 10: "pro", 16: "pro", 12: "course", 13: "course", 14: "course", 15: "course" };
  var LETTERS = "ABCDEFGH";
  var root = document.getElementById("quiz");
  if (!root) return;

  var session = null;

  function el(tag, attrs, children) {
    var n = document.createElement(tag);
    if (attrs) Object.keys(attrs).forEach(function (k) {
      if (k === "text") n.textContent = attrs[k];
      else if (k === "class") n.className = attrs[k];
      else if (k.slice(0, 2) === "on") n.addEventListener(k.slice(2), attrs[k]);
      else n.setAttribute(k, attrs[k]);
    });
    (children || []).forEach(function (c) { if (c) n.appendChild(typeof c === "string" ? document.createTextNode(c) : c); });
    return n;
  }
  function shuffle(a) {
    a = a.slice();
    for (var i = a.length - 1; i > 0; i--) { var j = Math.floor(Math.random() * (i + 1)); var t = a[i]; a[i] = a[j]; a[j] = t; }
    return a;
  }
  function trapChip(n) {
    return el("a", { class: "chip " + (GROUP[n] || ""), href: "traps.html#t" + n, text: "T" + n + " " + TRAPS[n] });
  }

  // ---------- Setup ----------
  function renderSetup() {
    root.innerHTML = "";
    var last = store.get("ccarp-quiz-last", null);
    var domSel = el("select", { id: "quiz-domain", "aria-label": "Domain" }, [el("option", { value: "all", text: "All domains (" + Q.length + " questions)" })]);
    Object.keys(DOMS).forEach(function (d) {
      var n = Q.filter(function (q) { return q.domain === d; }).length;
      if (n) domSel.appendChild(el("option", { value: d, text: DOMS[d] + " (" + n + ")" }));
    });
    var shuf = el("input", { type: "checkbox", id: "quiz-shuffle", checked: "checked" });
    var card = el("div", { class: "card" }, [
      el("h2", { text: "Set up a session", style: "margin-top:0" }),
      el("div", { class: "toolbar", style: "margin:0 0 1rem" }, [
        domSel,
        el("label", { style: "display:flex;gap:8px;align-items:center" }, [shuf, "Shuffle questions and options"])
      ]),
      el("div", { class: "btn-row" }, [
        el("button", { class: "btn", type: "button", text: "Start practice", onclick: function () { start(domSel.value, shuf.checked); } })
      ]),
      last ? el("p", { class: "src", style: "margin-top:1rem", text: "Last session: " + last.correct + " / " + last.total + " (" + last.pct + "%) on " + last.date + "." }) : null
    ]);
    root.appendChild(card);
  }

  function start(domain, doShuffle, onlyIds) {
    var pool = Q.filter(function (q) { return (domain === "all" || q.domain === domain) && (!onlyIds || onlyIds.indexOf(q.id) !== -1); });
    if (doShuffle) pool = shuffle(pool);
    session = {
      domain: domain, shuffle: doShuffle,
      items: pool.map(function (q) {
        var idx = q.options.map(function (_, i) { return i; });
        return { q: q, order: doShuffle ? shuffle(idx) : idx, picked: [], done: false, right: false };
      }),
      i: 0
    };
    renderQuestion();
  }

  // ---------- Question ----------
  function renderQuestion() {
    var item = session.items[session.i];
    var q = item.q;
    var multi = q.select > 1;
    root.innerHTML = "";

    var list = el("ul", { class: "options", role: multi ? "group" : "radiogroup" });
    item.order.forEach(function (oi, pos) {
      var o = q.options[oi];
      var input = el("input", { type: multi ? "checkbox" : "radio", name: "opt", value: String(oi), id: "opt-" + oi });
      var why = el("div", { class: "why" }, [
        el("span", { class: "verdict " + (o.c ? "ok" : "bad"), text: o.c ? "Correct. " : "Incorrect. " }),
        o.w,
        o.trap ? el("div", { style: "margin-top:6px" }, [trapChip(o.trap)]) : null
      ]);
      var label = el("label", { for: "opt-" + oi }, [
        input,
        el("span", null, [el("span", { class: "letter", text: LETTERS[pos] + "." }), o.t])
      ]);
      label.appendChild(why);
      list.appendChild(el("li", { class: "option", "data-oi": String(oi) }, [label]));
    });

    var submit = el("button", { class: "btn", type: "button", text: "Check answer", disabled: "disabled" });
    var next = el("button", { class: "btn", type: "button", hidden: "hidden", text: session.i === session.items.length - 1 ? "See results" : "Next question →" });
    var result = el("p", { class: "verdict", "aria-live": "polite" });

    list.addEventListener("change", function (e) {
      var picked = Array.prototype.map.call(list.querySelectorAll("input:checked"), function (x) { return +x.value; });
      if (multi && picked.length > q.select && e.target && e.target.checked) {
        // never allow more than N selections: undo the box just ticked
        e.target.checked = false;
        picked = picked.filter(function (p) { return p !== +e.target.value; });
      }
      item.picked = picked;
      if (picked.length === q.select) submit.removeAttribute("disabled"); else submit.setAttribute("disabled", "disabled");
    });

    submit.addEventListener("click", function () {
      var correctSet = q.options.map(function (o, i) { return o.c ? i : -1; }).filter(function (i) { return i >= 0; });
      item.right = item.picked.length === correctSet.length && item.picked.every(function (p) { return correctSet.indexOf(p) !== -1; });
      item.done = true;
      card.classList.add("answered");
      list.querySelectorAll("input").forEach(function (x) { x.disabled = true; });
      list.querySelectorAll(".option").forEach(function (li) {
        var oi = +li.dataset.oi, o = q.options[oi];
        if (o.c) li.classList.add("is-correct");
        else if (item.picked.indexOf(oi) !== -1) li.classList.add("is-wrong");
      });
      result.className = "verdict " + (item.right ? "ok" : "bad");
      result.textContent = item.right ? "✓ Correct. Read why each wrong option is wrong; that's where the principle lives." : "✗ Not quite. Check which trap family caught you.";
      submit.hidden = true;
      next.hidden = false;
      next.focus();
    });
    next.addEventListener("click", function () {
      if (session.i < session.items.length - 1) { session.i++; renderQuestion(); window.scrollTo({ top: root.offsetTop - 80 }); }
      else renderResults();
    });

    var progress = el("div", { class: "progress-line", "aria-hidden": "true" }, [el("span", { style: "width:" + Math.round((session.i / session.items.length) * 100) + "%" })]);
    var card = el("div", { class: "card question" }, [
      el("div", { class: "quiz-meta" }, [
        el("span", { text: "Question " + (session.i + 1) + " of " + session.items.length }),
        el("span", { class: "chip dom", text: DOMS[q.domain] }),
        el("span", { class: "select-n", text: multi ? "Select " + q.select : "Select 1" })
      ]),
      progress,
      el("h2", { text: q.stem }),
      list,
      el("div", { class: "btn-row" }, [submit, next, el("button", { class: "btn secondary", type: "button", text: "Quit", onclick: renderResults })]),
      result
    ]);
    root.appendChild(card);
  }

  // ---------- Results ----------
  function renderResults() {
    var done = session.items.filter(function (it) { return it.done; });
    var correct = done.filter(function (it) { return it.right; }).length;
    var pct = done.length ? Math.round((correct / done.length) * 100) : 0;
    store.set("ccarp-quiz-last", { correct: correct, total: done.length, pct: pct, date: new Date().toISOString().slice(0, 10) });

    var byDom = {};
    done.forEach(function (it) {
      var d = it.q.domain; byDom[d] = byDom[d] || { c: 0, n: 0 };
      byDom[d].n++; if (it.right) byDom[d].c++;
    });
    var tally = {};
    done.forEach(function (it) {
      it.picked.forEach(function (oi) { var o = it.q.options[oi]; if (!o.c && o.trap) tally[o.trap] = (tally[o.trap] || 0) + 1; });
    });
    var missedIds = done.filter(function (it) { return !it.right; }).map(function (it) { return it.q.id; });

    root.innerHTML = "";
    var domList = el("ul", { class: "trap-tally" });
    Object.keys(byDom).sort().forEach(function (d) {
      domList.appendChild(el("li", null, [el("span", { text: DOMS[d] }), el("strong", { text: byDom[d].c + " / " + byDom[d].n })]));
    });
    var trapList = el("ul", { class: "trap-tally" });
    var trapKeys = Object.keys(tally).sort(function (a, b) { return tally[b] - tally[a]; });
    trapKeys.forEach(function (t) {
      trapList.appendChild(el("li", null, [trapChip(+t), el("strong", { text: "× " + tally[t] })]));
    });

    root.appendChild(el("div", { class: "card" }, [
      el("h2", { text: "Session results", style: "margin-top:0" }),
      el("div", { class: "score-big", text: correct + " / " + done.length }),
      el("p", { class: "src", text: pct + "% correct. Aim well above the real exam's bar; practice should feel harder than the exam." }),
      el("div", { class: "grid grid-2", style: "margin-top:1rem" }, [
        el("div", null, [el("h3", { text: "By domain" }), domList]),
        el("div", null, [
          el("h3", { text: "Traps that caught you" }),
          trapKeys.length ? trapList : el("p", { class: "src", text: "None this session. Nice work." }),
          trapKeys.length ? el("p", { class: "src", text: "Reread these trap cards before your next session." }) : null
        ])
      ]),
      el("div", { class: "btn-row", style: "margin-top:1.2rem" }, [
        missedIds.length ? el("button", { class: "btn", type: "button", text: "Retry the " + missedIds.length + " missed", onclick: function () { start("all", session.shuffle, missedIds); } }) : null,
        el("button", { class: "btn secondary", type: "button", text: "New session", onclick: renderSetup })
      ])
    ]));
  }

  renderSetup();
})();
