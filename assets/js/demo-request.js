/**
 * Request a Demo popup
 * Opens from the header custom button (or any link to #request-demo),
 * validates, and posts the form to admin-ajax (see inc/demo-request.php).
 *
 * @package aaapos-prime
 */
(function () {
    'use strict';

    var overlay = document.getElementById('demo-modal-overlay');
    if (!overlay) {
        return;
    }

    var modal = document.getElementById('demo-modal');
    var form = document.getElementById('demo-modal-form');
    var closeBtn = document.getElementById('demo-modal-close');
    var statusEl = document.getElementById('demo-modal-status');
    var successEl = document.getElementById('demo-modal-success');
    var submitBtn = form.querySelector('.demo-modal-submit');
    var submitLabel = submitBtn.querySelector('.demo-modal-submit-label');
    var defaultLabel = submitLabel.textContent;
    var lastFocused = null;
    var hasOpenedBefore = false;

    var config = window.aaaposDemo || {};

    // Custom dropdowns (checkbox / radio list in a panel).
    // "Product of interest" allows several choices, "How did you hear" just one.
    var dropdowns = [];

    function initDropdown(wrapId, toggleId, inputSelector, single) {
        var wrap = document.getElementById(wrapId);
        if (!wrap) {
            return null;
        }

        var toggle = document.getElementById(toggleId);
        var panel = wrap.querySelector('.demo-modal-multi-panel');
        var text = wrap.querySelector('.demo-modal-multi-text');

        var api = {
            selected: function () {
                return Array.prototype.map.call(
                    form.querySelectorAll(inputSelector + ':checked'),
                    function (el) {
                        return el.getAttribute('data-label') || el.value;
                    }
                );
            },
            updateText: function () {
                var chosen = api.selected();
                if (!chosen.length) {
                    text.textContent = text.getAttribute('data-placeholder');
                } else {
                    text.textContent = chosen[0] + (chosen.length > 1 ? ' +' + (chosen.length - 1) : '');
                    toggle.classList.remove('is-invalid');
                }
            },
            setOpen: function (open) {
                if (open) {
                    dropdowns.forEach(function (d) {
                        if (d !== api) {
                            d.setOpen(false);
                        }
                    });
                }
                panel.hidden = !open;
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                wrap.classList.toggle('is-open', open);
            },
            markInvalid: function (flag) {
                toggle.classList.toggle('is-invalid', flag);
            }
        };

        toggle.addEventListener('click', function () {
            api.setOpen(panel.hidden);
        });

        panel.addEventListener('change', function () {
            api.updateText();
            if (single) {
                api.setOpen(false);
            }
        });

        document.addEventListener('click', function (e) {
            if (!panel.hidden && !wrap.contains(e.target)) {
                api.setOpen(false);
            }
        });

        dropdowns.push(api);
        return api;
    }

    var productsDropdown = initDropdown('demo-product', 'demo-product-toggle', 'input[name="product[]"]', false);
    var sourceDropdown = initDropdown('demo-source', 'demo-source-toggle', 'input[name="source"]', true);

    function closeDropdowns() {
        dropdowns.forEach(function (d) {
            d.setOpen(false);
        });
    }

    function updateDropdownTexts() {
        dropdowns.forEach(function (d) {
            d.updateText();
        });
    }

    // reCAPTCHA (only when enabled in the Customizer). Rendered the first
    // time the popup opens. If another script (login popup) already loaded
    // Google's api.js we reuse it, otherwise we load it ourselves.
    var recaptchaBox = document.getElementById('demo-recaptcha');
    var recaptchaKey = config.recaptcha_site_key || '';
    var recaptchaId = null;

    function loadRecaptchaScript() {
        if (!document.querySelector('script[src*="recaptcha/api.js"]')) {
            var tag = document.createElement('script');
            tag.src = 'https://www.google.com/recaptcha/api.js?render=explicit';
            tag.async = true;
            tag.defer = true;
            document.head.appendChild(tag);
        }
    }

    function renderRecaptcha(tries) {
        if (!recaptchaBox || !recaptchaKey || recaptchaId !== null) {
            return;
        }

        if (window.grecaptcha && typeof window.grecaptcha.render === 'function') {
            recaptchaId = window.grecaptcha.render(recaptchaBox, { sitekey: recaptchaKey });
            return;
        }

        loadRecaptchaScript();

        if ((tries || 0) < 50) {
            setTimeout(function () {
                renderRecaptcha((tries || 0) + 1);
            }, 200);
        }
    }

    function recaptchaToken() {
        if (recaptchaId === null || !window.grecaptcha) {
            return '';
        }
        return window.grecaptcha.getResponse(recaptchaId);
    }

    function resetRecaptcha() {
        if (recaptchaId !== null && window.grecaptcha && typeof window.grecaptcha.reset === 'function') {
            window.grecaptcha.reset(recaptchaId);
        }
    }

    function showError(message) {
        statusEl.textContent = message;
        statusEl.classList.add('show');
    }

    function clearError() {
        statusEl.textContent = '';
        statusEl.classList.remove('show');
    }

    function openModal() {
        lastFocused = document.activeElement;

        // Fresh state each time it opens
        closeDropdowns();
        form.hidden = false;
        successEl.hidden = true;
        clearError();

        // Entrance animation plays on the first open only (same as the login popup)
        if (!hasOpenedBefore) {
            modal.classList.add('first-open');
            hasOpenedBefore = true;
            setTimeout(function () {
                modal.classList.remove('first-open');
            }, 1000);
        }

        // If opened from the mobile menu, close the menu first
        var openMenuToggle = document.querySelector('.mobile-menu-toggle[aria-expanded="true"]');
        if (openMenuToggle) {
            openMenuToggle.click();
        }

        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';

        renderRecaptcha();

        setTimeout(function () {
            var first = document.getElementById('demo-full-name');
            if (first) {
                first.focus();
            }
        }, 300);
    }

    function closeModal() {
        closeDropdowns();
        overlay.classList.remove('active');
        document.body.style.overflow = '';
        clearError();

        if (lastFocused && typeof lastFocused.focus === 'function') {
            lastFocused.focus();
        }

        if (window.location.hash === '#request-demo') {
            history.replaceState(null, document.title, window.location.pathname + window.location.search);
        }
    }

    // Open: header button or any link pointing at #request-demo
    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('[data-demo-form-open], a[href="#request-demo"]');
        if (trigger) {
            e.preventDefault();
            openModal();
        }
    });

    // Close: only the X button (no backdrop click, no Escape key)
    closeBtn.addEventListener('click', closeModal);

    // Keep Tab focus inside the popup
    document.addEventListener('keydown', function (e) {
        if (!overlay.classList.contains('active')) {
            return;
        }

        if (e.key === 'Tab') {
            var focusable = Array.prototype.filter.call(
                overlay.querySelectorAll('button, input, select, textarea, [href], [tabindex]:not([tabindex="-1"])'),
                function (el) {
                    return !el.disabled && el.offsetParent !== null;
                }
            );
            if (!focusable.length) {
                return;
            }
            var first = focusable[0];
            var last = focusable[focusable.length - 1];

            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        }
    });

    // Submit
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearError();

        var hasProducts = !productsDropdown || productsDropdown.selected().length > 0;
        var hasSource = !sourceDropdown || sourceDropdown.selected().length > 0;
        var fieldsOk = form.checkValidity();

        if (!fieldsOk || !hasProducts || !hasSource) {
            form.classList.add('was-validated');

            if (!hasProducts) {
                productsDropdown.markInvalid(true);
            }
            if (!hasSource) {
                sourceDropdown.markInvalid(true);
            }

            if (!fieldsOk) {
                form.reportValidity();
            } else if (!hasProducts) {
                productsDropdown.setOpen(true);
                showError(config.product_required || 'Please select at least one product.');
            } else {
                sourceDropdown.setOpen(true);
                showError(config.source_required || 'Please tell us how you heard about us.');
            }
            return;
        }

        if (recaptchaKey && !recaptchaToken()) {
            showError(config.recaptcha_required || 'Please tick the reCAPTCHA box.');
            return;
        }

        submitBtn.disabled = true;
        submitLabel.textContent = config.sending || 'Sending…';

        var body = new URLSearchParams(new FormData(form));
        body.append('action', 'aaapos_demo_request');

        fetch(config.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                // Tokens are single-use, so always get a fresh one
                resetRecaptcha();

                if (data && data.success) {
                    // Fade the form out, then swap to the success message.
                    // The popup keeps its height so it doesn't jump.
                    var formHeight = form.offsetHeight;

                    form.reset();
                    updateDropdownTexts();
                    form.classList.remove('was-validated');
                    form.classList.add('is-leaving');

                    setTimeout(function () {
                        successEl.style.minHeight = formHeight + 'px';
                        form.hidden = true;
                        form.classList.remove('is-leaving');
                        successEl.hidden = false;
                    }, 250);
                } else {
                    showError((data && data.data && data.data.message) || config.error);
                }
            })
            .catch(function () {
                resetRecaptcha();
                showError(config.error || 'Something went wrong. Please try again.');
            })
            .then(function () {
                submitBtn.disabled = false;
                submitLabel.textContent = defaultLabel;
            });
    });

    // Also open if someone lands on a page with #request-demo in the URL
    if (window.location.hash === '#request-demo') {
        openModal();
    }
})();