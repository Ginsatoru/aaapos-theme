/**
 * Field Tick
 *
 * Shows the checkout-style green tick in text fields of any form that has
 * the data-field-tick attribute:
 *  - when you leave a field that's filled in and valid, the tick appears
 *  - as soon as the field becomes empty or invalid while typing, it goes
 *  - form.reset() clears all ticks
 *
 * Uses event delegation on document, so it also works for forms that are
 * added to the page later (modals built by JavaScript).
 *
 * Styles: assets/css/components/field-tick.css
 *
 * @package aaapos-prime
 */
(function () {
  'use strict';

  if (window.aaaposFieldTick) return;
  window.aaaposFieldTick = true;

  var TEXT_TYPES = ['text', 'email', 'tel', 'url', 'number', 'search'];

  function isTickField(el) {
    if (!el || el.tagName !== 'INPUT') return false;
    if (TEXT_TYPES.indexOf((el.type || 'text').toLowerCase()) === -1) return false;
    if (el.disabled || el.readOnly) return false;
    if (el.getAttribute('tabindex') === '-1') return false; // hidden honeypot fields
    // Password fields - also while "show password" has turned them into
    // type="text" (the eye icon sits where the tick would go)
    var pw = ((el.name || '') + ' ' + (el.getAttribute('autocomplete') || '') + ' ' + (el.id || '')).toLowerCase();
    if (pw.indexOf('password') !== -1) return false;
    return !!(el.closest && el.closest('form[data-field-tick]'));
  }

  // name@domain.tld - the browser's own email check also accepts "bo@gmail"
  var EMAIL_RE = /^[^\s@]+@[^\s@.]+(\.[^\s@.]+)*\.[A-Za-z]{2,}$/;

  function fieldHint(el) {
    return [el.name, el.placeholder, el.getAttribute('autocomplete')].join(' ').toLowerCase();
  }

  // Must the value be a full email address?
  //  - type="email", or a field that mentions "email" but not "username":
  //    always
  //  - "email or username" fields (login, forgot password): only when the
  //    value contains "@" - otherwise it's a username, e.g. "bo"
  function mustBeEmail(el, value) {
    if ((el.type || '').toLowerCase() === 'email') return true;
    var hint = fieldHint(el);
    if (hint.indexOf('username') !== -1) return value.indexOf('@') !== -1;
    return hint.indexOf('email') !== -1;
  }

  function isValid(el) {
    var value = el.value.trim();
    if (value === '' || !el.checkValidity()) return false;
    if (mustBeEmail(el, value) && !EMAIL_RE.test(value)) return false;
    return true;
  }

  // Leaving a field: show or hide the tick
  document.addEventListener('focusout', function (e) {
    var el = e.target;
    if (!isTickField(el)) return;
    el.classList.toggle('is-field-valid', isValid(el));
  }, true);

  // While typing: drop the tick as soon as the value stops being valid
  document.addEventListener('input', function (e) {
    var el = e.target;
    if (!isTickField(el)) return;
    if (!isValid(el)) el.classList.remove('is-field-valid');
  }, true);

  // Form reset: clear every tick in that form
  document.addEventListener('reset', function (e) {
    var form = e.target;
    if (!form || !form.matches || !form.matches('form[data-field-tick]')) return;
    var ticked = form.querySelectorAll('.is-field-valid');
    for (var i = 0; i < ticked.length; i++) {
      ticked[i].classList.remove('is-field-valid');
    }
  }, true);
})();
