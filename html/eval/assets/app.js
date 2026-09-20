// app.js — light/dark theme toggle with localStorage persistence.
(function () {
  'use strict';

  var STORAGE_KEY = 'ev-theme';
  var root = document.documentElement;

  function currentTheme() {
    try {
      var saved = localStorage.getItem(STORAGE_KEY);
      if (saved === 'light' || saved === 'dark') return saved;
    } catch (e) { /* storage may be unavailable */ }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function applyTheme(theme) {
    root.setAttribute('data-bs-theme', theme);
    document.querySelectorAll('.ev-theme-toggle i').forEach(function (icon) {
      icon.className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
    });
  }

  // Apply as early as possible.
  applyTheme(currentTheme());

  document.addEventListener('DOMContentLoaded', function () {
    applyTheme(currentTheme());
    document.querySelectorAll('.ev-theme-toggle').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        try { localStorage.setItem(STORAGE_KEY, next); } catch (e) { /* ignore */ }
        applyTheme(next);
      });
    });
  });
})();
