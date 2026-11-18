/**
 * Search modal: open/close, live product results, recent searches.
 *
 * @package aaapos-prime
 */
(function () {
    'use strict';

    var KEY = 'aaapos_recent_searches';
    var MAX = 6;
    var CLOCK = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>';

    function init() {
        var modal = document.getElementById('site-search-modal');
        if (!modal) return;

        var form = modal.querySelector('.ssm-form');
        var input = modal.querySelector('.ssm-input');
        var results = modal.querySelector('.ssm-results');
        var browse = modal.querySelector('.ssm-browse');
        var recent = modal.querySelector('.ssm-recent');
        var recentList = modal.querySelector('.ssm-recent-list');
        var timer, request, lastFocus;

        function getRecent() {
            try { return JSON.parse(localStorage.getItem(KEY)) || []; } catch (e) { return []; }
        }

        function saveRecent(term) {
            term = term.trim();
            if (!term) return;
            var list = getRecent().filter(function (t) { return t.toLowerCase() !== term.toLowerCase(); });
            list.unshift(term);
            try { localStorage.setItem(KEY, JSON.stringify(list.slice(0, MAX))); } catch (e) {}
        }

        function renderRecent() {
            var list = getRecent();
            recentList.innerHTML = '';
            list.forEach(function (term) {
                var chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'ssm-chip';
                chip.dataset.term = term;
                chip.innerHTML = CLOCK;
                chip.appendChild(document.createTextNode(term));
                recentList.appendChild(chip);
            });
            recent.hidden = !list.length;
        }

        function open() {
            if (!modal.hidden) return;
            lastFocus = document.activeElement;
            renderRecent();
            modal.hidden = false;
            document.documentElement.classList.add('ssm-open');
            requestAnimationFrame(function () {
                modal.classList.add('is-open');
                input.focus();
                input.select();
            });
        }

        function close() {
            modal.classList.remove('is-open');
            document.documentElement.classList.remove('ssm-open');
            setTimeout(function () { modal.hidden = true; }, 200);
            if (lastFocus) lastFocus.focus();
        }

        function showBrowse() {
            results.hidden = true;
            results.innerHTML = '';
            browse.hidden = false;
        }

        function search(query) {
            if (request) request.abort();
            if (query.length < 2) return showBrowse();

            request = new AbortController();
            results.hidden = false;
            browse.hidden = true;
            results.classList.add('is-loading');

            fetch(modal.dataset.ajax + '?action=mr_product_search&s=' + encodeURIComponent(query), { signal: request.signal })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    results.classList.remove('is-loading');
                    results.innerHTML = data && data.success ? data.data : '';
                })
                .catch(function (err) {
                    if (err.name !== 'AbortError') results.classList.remove('is-loading');
                });
        }

        document.addEventListener('click', function (e) {
            if (e.target.closest('[data-search-open]')) {
                e.preventDefault();
                open();
                return;
            }
            if (modal.hidden) return;

            if (e.target.closest('[data-search-close]')) return close();

            var chip = e.target.closest('.ssm-chip');
            if (chip && modal.contains(chip)) {
                saveRecent(chip.dataset.term);
                input.value = chip.dataset.term;
                form.submit();
                return;
            }

            if (e.target.closest('.ssm-clear')) {
                try { localStorage.removeItem(KEY); } catch (err) {}
                renderRecent();
                return;
            }

            if (e.target.closest('.ssm-results a')) saveRecent(input.value);
        });

        document.addEventListener('focusin', function (e) {
            if (modal.hidden && e.target.matches('[data-search-open] input[readonly]')) open();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.hidden) {
                close();
            } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                if (modal.hidden) open(); else close();
            }
        });

        input.addEventListener('input', function () {
            clearTimeout(timer);
            var query = input.value.trim();
            timer = setTimeout(function () { search(query); }, 250);
        });

        form.addEventListener('submit', function (e) {
            if (!input.value.trim()) {
                e.preventDefault();
                return;
            }
            saveRecent(input.value);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();