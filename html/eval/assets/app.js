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

    initQuizzes();
  });

  // ---- Sample-question quizzes (module.php) -----------------------------
  function initQuizzes() {
    document.querySelectorAll('.ev-quiz').forEach(function (quiz) {
      var answer = parseInt(quiz.getAttribute('data-answer'), 10);
      var options = quiz.querySelectorAll('.ev-quiz-option');
      var checkBtn = quiz.querySelector('.ev-quiz-check');
      var result = quiz.querySelector('.ev-quiz-result');
      var explain = quiz.querySelector('.ev-quiz-explain');
      var selected = -1;
      var done = false;

      options.forEach(function (opt) {
        opt.addEventListener('click', function () {
          if (done) return;
          selected = parseInt(opt.getAttribute('data-idx'), 10);
          options.forEach(function (o) { o.classList.remove('selected'); });
          opt.classList.add('selected');
          checkBtn.disabled = false;
        });
      });

      checkBtn.addEventListener('click', function () {
        if (done || selected < 0) return;
        done = true;
        options.forEach(function (o) {
          var idx = parseInt(o.getAttribute('data-idx'), 10);
          o.classList.remove('selected');
          if (idx === answer) o.classList.add('correct');
          else if (idx === selected) o.classList.add('incorrect');
          o.disabled = true;
        });
        var ok = selected === answer;
        result.textContent = ok
          ? 'Correct.'
          : 'Not quite — the correct answer is ' + String.fromCharCode(65 + answer) + '.';
        result.classList.remove('d-none');
        result.classList.add(ok ? 'text-success' : 'text-danger');
        explain.classList.remove('d-none');
        checkBtn.classList.add('d-none');
      });
    });
  }
})();
