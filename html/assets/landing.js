/*
 * landing.js — behaviour for the Orbit View landing page (html/index.php).
 *
 * The page is fully usable without this file (every node is a link). With it:
 * clicking a node opens a detail panel instead of navigating, the search box
 * and module chips dim everything that doesn't match, and the zoom and
 * motion controls work.
 */
(function () {
    'use strict';

    var data = JSON.parse(document.getElementById('lp-data').textContent);
    var root = document.querySelector('.lp-root');
    var svg = document.querySelector('.lp-network');
    var stage = document.getElementById('lp-stage');
    var panel = document.getElementById('lp-panel');
    var results = document.getElementById('lp-results');
    var searchInput = document.getElementById('lp-search');
    var statusText = document.getElementById('lp-status');
    var chips = Array.prototype.slice.call(document.querySelectorAll('.lp-chip'));
    var nodes = Array.prototype.slice.call(document.querySelectorAll('.lp-node'));
    var edges = Array.prototype.slice.call(document.querySelectorAll('.lp-edge'));

    var defaultStatus = statusText.textContent;
    var selected = null;      // 'course', a module number as a string, or null
    var matched = null;       // Set of module numbers matching the search, or null when no query
    var lastFocus = null;

    function moduleByNo(no) {
        return data.modules.filter(function (m) { return String(m.no) === String(no); })[0];
    }

    /** Tiny DOM builder: el('a', {class: 'x', href: '…'}, 'text' | [children]). */
    function el(tag, attrs, children) {
        var node = document.createElement(tag);
        Object.keys(attrs || {}).forEach(function (k) { node.setAttribute(k, attrs[k]); });
        [].concat(children || []).forEach(function (c) {
            node.appendChild(typeof c === 'string' ? document.createTextNode(c) : c);
        });
        return node;
    }

    /* ---------- Dimming: selection wins over search ---------- */
    function applyDim() {
        function isLit(key) {
            if (selected && selected !== 'course') { return key === selected; }
            if (matched) { return key !== 'course' && matched.has(key); }
            return true;
        }
        nodes.forEach(function (n) {
            var key = n.getAttribute('data-module');
            n.classList.toggle('dimmed', key !== 'course' && !isLit(key));
            n.classList.toggle('selected', key === selected);
        });
        edges.forEach(function (e) {
            e.classList.toggle('dimmed', !isLit(e.getAttribute('data-module')));
        });
        chips.forEach(function (c) {
            var key = c.getAttribute('data-module');
            var on = key ? key === selected : !selected || selected === 'course';
            c.classList.toggle('active', on);
            if (key) { c.setAttribute('aria-pressed', String(on)); }
        });
    }

    /* ---------- Detail panel ---------- */
    function panelShell(kind, badge, title, colour) {
        panel.textContent = '';
        var close = el('button', { type: 'button', class: 'lp-panel-close', 'aria-label': 'Close panel' }, '✕');
        close.addEventListener('click', function () { select(null); });
        var badgeEl = el('div', { class: 'lp-panel-badge' }, badge);
        badgeEl.style.color = colour;
        var kindEl = el('p', { class: 'lp-panel-kind' }, kind);
        kindEl.style.color = colour;
        panel.appendChild(close);
        panel.appendChild(el('div', { class: 'lp-panel-head' }, [
            badgeEl,
            el('div', {}, [kindEl, el('h2', { id: 'lp-panel-title' }, title)])
        ]));
        return close;
    }

    function stat(value, label) {
        return el('div', {}, [el('span', {}, String(value)), el('label', {}, label)]);
    }

    function renderModule(m) {
        var colour = m.group === 'gold' ? 'var(--gold)' : 'var(--cyan)';
        var url = data.base + 'module.php?m=' + m.no;
        var close = panelShell('Module ' + m.no + ' · ' + m.time, 'M' + m.no, m.title, colour);
        panel.appendChild(el('p', { class: 'lp-panel-task' }, m.blurb));
        panel.appendChild(el('div', { class: 'lp-panel-stats' }, [stat(m.topics.length, 'Topics'), stat(m.time, 'Time budget')]));
        panel.appendChild(el('div', {}, [
            el('h3', {}, 'Exam task'),
            el('p', { class: 'lp-panel-task' }, m.task)
        ]));
        panel.appendChild(el('div', {}, [
            el('h3', {}, 'Topics'),
            el('ul', { class: 'lp-panel-list' }, m.topics.map(function (t) {
                return el('li', {}, el('a', { class: 'lp-link', href: url + '#' + t.id }, t.title));
            }))
        ]));
        panel.appendChild(el('a', { class: 'lp-cta', href: url }, 'Open module ' + m.no + ' →'));
        return close;
    }

    function renderCourse() {
        var c = data.course;
        var close = panelShell('Domain ' + c.domainNo + ' · ' + c.weight + '% of the exam', c.badge, c.title, 'var(--gold)');
        panel.appendChild(el('p', { class: 'lp-panel-task' }, c.domain + ' — ' + c.subtitle + '.'));
        panel.appendChild(el('div', { class: 'lp-panel-stats' }, [
            stat(data.modules.length, 'Modules'), stat(c.topics, 'Topics'), stat('~' + c.hours + 'h', 'Time budget')
        ]));
        panel.appendChild(el('div', {}, [
            el('h3', {}, 'By the end you can'),
            el('ul', { class: 'lp-panel-list' }, c.outcomes.map(function (o) { return el('li', {}, o); }))
        ]));
        panel.appendChild(el('a', { class: 'lp-cta', href: data.base }, 'Enter the course →'));
        return close;
    }

    function select(key) {
        var wasOpen = !panel.hidden;
        selected = key === selected ? null : key;
        applyDim();

        if (!selected) {
            panel.hidden = true;
            statusText.textContent = defaultStatus;
            if (lastFocus && document.contains(lastFocus)) { lastFocus.focus(); }
            lastFocus = null;
            return;
        }
        if (!wasOpen) { lastFocus = document.activeElement; }
        var close;
        if (selected === 'course') {
            close = renderCourse();
            statusText.textContent = 'Course overview';
        } else {
            var m = moduleByNo(selected);
            close = renderModule(m);
            statusText.textContent = 'Module ' + m.no + ' · ' + m.short;
        }
        panel.hidden = false;
        panel.scrollTop = 0;
        close.focus();
    }

    nodes.forEach(function (n) {
        n.addEventListener('click', function (ev) {
            // Let modified clicks (new tab etc.) behave like the plain link they are.
            if (ev.metaKey || ev.ctrlKey || ev.shiftKey || ev.button) { return; }
            ev.preventDefault();
            select(n.getAttribute('data-module'));
        });
    });
    chips.forEach(function (c) {
        c.addEventListener('click', function () {
            var key = c.getAttribute('data-module');
            if (key) { select(key); } else if (selected) { select(selected); }
        });
    });
    document.addEventListener('keydown', function (ev) {
        if (ev.key === 'Escape' && selected) { select(selected); }
    });

    /* ---------- Search ---------- */
    function runSearch() {
        var q = searchInput.value.trim().toLowerCase();
        results.textContent = '';
        if (!q) {
            matched = null;
            results.hidden = true;
            applyDim();
            return;
        }
        matched = new Set();
        var items = [];
        data.modules.forEach(function (m) {
            var url = data.base + 'module.php?m=' + m.no;
            if ([m.title, m.short, m.task, m.blurb].join(' ').toLowerCase().indexOf(q) !== -1) {
                matched.add(String(m.no));
                var btn = el('button', { type: 'button', class: 'lp-link' }, m.title);
                btn.addEventListener('click', function () { if (selected !== String(m.no)) { select(String(m.no)); } });
                items.push(el('li', {}, [btn, el('small', {}, 'MODULE ' + m.no)]));
            }
            m.topics.forEach(function (t) {
                if (t.title.toLowerCase().indexOf(q) !== -1) {
                    matched.add(String(m.no));
                    items.push(el('li', {}, [el('a', { class: 'lp-link', href: url + '#' + t.id }, t.title), el('small', {}, 'M' + m.no + ' TOPIC')]));
                }
            });
        });
        results.appendChild(el('p', { class: 'lp-match-count' }, items.length + (items.length === 1 ? ' match' : ' matches')));
        if (items.length) { results.appendChild(el('ul', {}, items)); }
        results.hidden = false;
        applyDim();
    }
    searchInput.addEventListener('input', runSearch);

    /* ---------- Zoom ---------- */
    var zoom = 1, ZOOM_MIN = 0.6, ZOOM_MAX = 1.6;
    var zoomValue = document.getElementById('lp-zoom-value');
    var zoomButtons = {};
    Array.prototype.forEach.call(document.querySelectorAll('[data-zoom]'), function (b) {
        zoomButtons[b.getAttribute('data-zoom')] = b;
        b.addEventListener('click', function () {
            var dir = b.getAttribute('data-zoom');
            zoom = dir === 'reset' ? 1 : zoom + (dir === 'in' ? 0.1 : -0.1);
            zoom = Math.round(Math.min(ZOOM_MAX, Math.max(ZOOM_MIN, zoom)) * 10) / 10;
            stage.style.transform = 'scale(' + zoom + ')';
            zoomValue.textContent = Math.round(zoom * 100) + '%';
            zoomButtons.out.disabled = zoom <= ZOOM_MIN;
            zoomButtons.in.disabled = zoom >= ZOOM_MAX;
        });
    });

    /* ---------- Motion toggle (CSS spins + SVG particles) ---------- */
    var motionBtn = document.querySelector('.lp-motion');
    function setMotion(on) {
        root.classList.toggle('lp-motion-paused', !on);
        if (on) { svg.unpauseAnimations(); } else { svg.pauseAnimations(); }
        motionBtn.setAttribute('aria-pressed', String(on));
        motionBtn.title = on ? 'Pause motion' : 'Play motion';
        motionBtn.querySelector('.lp-motion-on').hidden = !on;
        motionBtn.querySelector('.lp-motion-off').hidden = on;
    }
    motionBtn.addEventListener('click', function () {
        setMotion(motionBtn.getAttribute('aria-pressed') !== 'true');
    });
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        setMotion(false);
    }
})();
