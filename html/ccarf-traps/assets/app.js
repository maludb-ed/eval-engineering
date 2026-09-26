/* CCAR-P Exam Traps — shared behaviour. Every storage call is guarded so the site works without it. */
(function () {
  "use strict";

  var store = {
    get: function (k, fallback) {
      try { var v = localStorage.getItem(k); return v === null ? fallback : JSON.parse(v); } catch (e) { return fallback; }
    },
    set: function (k, v) {
      try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) { /* ignore */ }
    }
  };
  window.ccarpStore = store;

  // ---------- Theme toggle ----------
  var root = document.documentElement;
  function effectiveTheme() {
    if (root.dataset.theme) return root.dataset.theme;
    return window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
  }
  var themeBtn = document.querySelector("[data-theme-toggle]");
  function paintThemeBtn() {
    if (!themeBtn) return;
    var dark = effectiveTheme() === "dark";
    themeBtn.textContent = dark ? "☀" : "☾";
    themeBtn.setAttribute("aria-label", dark ? "Switch to light theme" : "Switch to dark theme");
  }
  if (themeBtn) {
    themeBtn.addEventListener("click", function () {
      var next = effectiveTheme() === "dark" ? "light" : "dark";
      root.dataset.theme = next;
      store.set("ccarp-theme", next);
      paintThemeBtn();
    });
    paintThemeBtn();
  }

  // ---------- Mobile nav ----------
  var navBtn = document.querySelector(".nav-toggle");
  var nav = document.getElementById("site-nav");
  if (navBtn && nav) {
    navBtn.addEventListener("click", function () {
      var open = nav.classList.toggle("open");
      navBtn.setAttribute("aria-expanded", open ? "true" : "false");
    });
  }

  // ---------- Study-path checklist (index) ----------
  var checklist = document.querySelector("[data-checklist]");
  if (checklist) {
    var key = checklist.getAttribute("data-checklist") || "ccarp-progress";
    var state = store.get(key, {});
    var boxes = checklist.querySelectorAll("input[type=checkbox]");
    var bar = document.querySelector("[data-progress-bar]");
    var label = document.querySelector("[data-progress-label]");
    function paint() {
      var done = 0;
      boxes.forEach(function (b) {
        b.checked = !!state[b.value];
        b.closest("label").querySelector("span").classList.toggle("done", b.checked);
        if (b.checked) done++;
      });
      var pct = Math.round((done / boxes.length) * 100);
      if (bar) bar.style.width = pct + "%";
      if (label) label.textContent = done + " of " + boxes.length + " steps done";
    }
    boxes.forEach(function (b) {
      b.addEventListener("change", function () { state[b.value] = b.checked; store.set(key, state); paint(); });
    });
    paint();
  }

  // ---------- Trap cards: group filter + flashcard mode ----------
  var trapGrid = document.querySelector("[data-trap-grid]");
  if (trapGrid) {
    var cards = trapGrid.querySelectorAll(".trap-card");
    document.querySelectorAll("[data-group-filter] button").forEach(function (btn) {
      btn.addEventListener("click", function () {
        btn.parentNode.querySelectorAll("button").forEach(function (b) { b.setAttribute("aria-pressed", "false"); });
        btn.setAttribute("aria-pressed", "true");
        var g = btn.dataset.group;
        cards.forEach(function (c) { c.hidden = g !== "all" && c.dataset.group !== g; });
      });
    });
    var flashBtn = document.querySelector("[data-flash-toggle]");
    if (flashBtn) {
      flashBtn.addEventListener("click", function () {
        var on = trapGrid.classList.toggle("flash");
        flashBtn.setAttribute("aria-pressed", on ? "true" : "false");
        flashBtn.textContent = on ? "Flashcard mode: on" : "Flashcard mode: off";
        cards.forEach(function (c) { c.classList.remove("revealed"); });
      });
    }
    trapGrid.addEventListener("click", function (e) {
      var r = e.target.closest(".reveal-btn");
      if (r) r.closest(".trap-card").classList.add("revealed");
    });
    // Deep link to #t4 etc. should reveal that card even in flash mode
    if (location.hash) {
      var target = document.querySelector(location.hash);
      if (target && target.classList.contains("trap-card")) target.classList.add("revealed");
    }
  }

  // ---------- Constraint map filter ----------
  var mapTable = document.querySelector("[data-map-table]");
  if (mapTable) {
    var rows = mapTable.querySelectorAll("tbody tr");
    var q = document.getElementById("map-search");
    var dom = document.getElementById("map-domain");
    var count = document.querySelector("[data-map-count]");
    var empty = document.querySelector("[data-map-empty]");
    function filter() {
      var text = (q.value || "").trim().toLowerCase();
      var d = dom.value;
      var shown = 0;
      rows.forEach(function (r) {
        var ok = (!text || r.textContent.toLowerCase().indexOf(text) !== -1) && (d === "all" || r.dataset.domain === d);
        r.hidden = !ok;
        if (ok) shown++;
      });
      if (count) count.textContent = shown + " of " + rows.length + " clues";
      if (empty) empty.hidden = shown !== 0;
    }
    q.addEventListener("input", filter);
    dom.addEventListener("change", filter);
    filter();
  }

  // ---------- Domains TOC highlight ----------
  var tocLinks = document.querySelectorAll(".toc a[href^='#']");
  if (tocLinks.length && "IntersectionObserver" in window) {
    var map = {};
    tocLinks.forEach(function (a) { map[a.getAttribute("href").slice(1)] = a; });
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) {
          tocLinks.forEach(function (a) { a.classList.remove("active"); });
          var a = map[en.target.id];
          if (a) a.classList.add("active");
        }
      });
    }, { rootMargin: "-20% 0px -70% 0px" });
    Object.keys(map).forEach(function (id) { var el = document.getElementById(id); if (el) io.observe(el); });
  }
})();
