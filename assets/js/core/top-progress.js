/**
 * Top Progress Bar
 *
 * Shows a thin bar at the very top of the page:
 *  - on every page load / refresh (starts as soon as <head> is parsed,
 *    finishes on window "load")
 *  - when leaving the page via a normal link click or form submit, so the
 *    shopper sees progress while the next page loads.
 *
 * Inlined in <head> by inc/features/top-progress-bar.php. No dependencies.
 *
 * @package aaapos-prime
 */
(function () {
  'use strict';

  if (window.aaaposTopProgress) return;

  var bar = document.createElement('div');
  bar.id = 'aaapos-top-progress';
  bar.setAttribute('aria-hidden', 'true');
  // <body> doesn't exist yet in <head>; a fixed element on <html> works fine.
  document.documentElement.appendChild(bar);

  var value = 0;
  var trickleTimer = null;
  var safetyTimer = null;
  var resetTimer = null;
  var running = false;

  function set(v) {
    value = Math.max(0, Math.min(1, v));
    bar.style.transform = 'scaleX(' + value + ')';
  }

  function start() {
    clearTimeout(resetTimer);
    clearInterval(trickleTimer);
    clearTimeout(safetyTimer);
    running = true;
    bar.classList.remove('is-done', 'is-reset');
    bar.classList.add('is-active');
    if (value > 0.3) set(0.08);
    set(Math.max(value, 0.08));

    // Ease towards 90% - fast at first, slower as it gets closer
    trickleTimer = setInterval(function () {
      if (value < 0.9) set(value + (0.9 - value) * 0.1);
    }, 200);

    // Never leave the bar hanging (e.g. a navigation that was cancelled)
    safetyTimer = setTimeout(finish, 10000);
  }

  function finish() {
    if (!running) return;
    running = false;
    clearInterval(trickleTimer);
    clearTimeout(safetyTimer);
    set(1);
    bar.classList.add('is-done');

    resetTimer = setTimeout(function () {
      bar.classList.add('is-reset');
      bar.classList.remove('is-active', 'is-done');
      set(0);
    }, 700);
  }

  // ---- Page load / refresh ------------------------------------------------
  start();
  if (document.readyState === 'complete') {
    finish();
  } else {
    window.addEventListener('load', finish);
  }

  // Coming back via the back/forward cache: make sure nothing is left showing
  window.addEventListener('pageshow', function (e) {
    if (e.persisted) {
      running = true;
      finish();
    }
  });

  // ---- Leaving the page ---------------------------------------------------
  function isNavigatingLink(a, e) {
    if (!a || !a.href) return false;
    if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return false;
    if (a.target && a.target !== '_self') return false;
    if (a.hasAttribute('download')) return false;

    var url;
    try { url = new URL(a.href, window.location.href); } catch (err) { return false; }
    if (url.protocol !== 'http:' && url.protocol !== 'https:') return false; // mailto:, tel:, javascript:
    if (url.origin !== window.location.origin) return false;

    // Same page, only the #hash differs (including bare "#" links used as
    // menu toggles - URL.hash is empty for those, so check the raw href)
    if (a.href.indexOf('#') !== -1 &&
        url.pathname === window.location.pathname &&
        url.search === window.location.search) return false;
    return true;
  }

  document.addEventListener('click', function (e) {
    var a = e.target && e.target.closest ? e.target.closest('a') : null;
    if (!isNavigatingLink(a, e)) return;
    // Let other handlers run first: skip links handled by JS (AJAX add to
    // cart, modals, dropdown toggles...) which call preventDefault().
    setTimeout(function () {
      if (!e.defaultPrevented) start();
    }, 0);
  });

  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form || (form.target && form.target !== '_self')) return;
    setTimeout(function () {
      if (!e.defaultPrevented) start();
    }, 0);
  });

  window.aaaposTopProgress = { start: start, finish: finish };
})();
