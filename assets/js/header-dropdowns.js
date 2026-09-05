/**
 * Header Dropdowns - JS-driven hover-intent controller
 *
 * Replaces pure-CSS :hover dropdowns with a smoother "hover intent"
 * interaction:
 *   - a short OPEN_DELAY so a quick mouse pass-by doesn't trigger it
 *   - a longer CLOSE_DELAY so moving the cursor from the trigger to the
 *     panel doesn't close it mid-move
 * On devices with no real hover capability (touch), falls back to
 * tap-to-toggle with outside-click-to-close.
 * Also wires up keyboard accessibility (focus/blur, Escape) and drives
 * aria-expanded on the account/cart triggers.
 *
 * Also drives the header search bar's loading state: adds .is-searching
 * to a .search-form the moment it's submitted (both the desktop
 * .header-search-bar and the .mobile-search-wrapper share this markup),
 * so the CSS spinner shows while the browser navigates to the search
 * results page.
 *
 * Pairs with: assets/css/components/header/_dropdowns.css (.is-open class)
 *             assets/css/components/header/_header-actions.css (.is-searching class)
 *
 * @package AAAPOS
 * Location: assets/js/header-dropdowns.js
 */
(function () {
    'use strict';

    var OPEN_DELAY = 30;   // ms
    var CLOSE_DELAY = 120; // ms

    var hasHover = window.matchMedia &&
        window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    /**
     * Wires up a single trigger + panel wrapper (account or cart dropdown).
     */
    function initDropdown(wrapper) {
        var trigger = wrapper.querySelector(':scope > a, :scope > .header-action');
        var openTimer = null;
        var closeTimer = null;

        function clearTimers() {
            if (openTimer) { clearTimeout(openTimer); openTimer = null; }
            if (closeTimer) { clearTimeout(closeTimer); closeTimer = null; }
        }

        function open() {
            clearTimers();
            wrapper.classList.add('is-open');
            if (trigger) trigger.setAttribute('aria-expanded', 'true');
        }

        function close() {
            clearTimers();
            wrapper.classList.remove('is-open');
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
        }

        function scheduleOpen() {
            clearTimers();
            openTimer = setTimeout(open, OPEN_DELAY);
        }

        function scheduleClose() {
            clearTimers();
            closeTimer = setTimeout(close, CLOSE_DELAY);
        }

        if (hasHover) {
            wrapper.addEventListener('mouseenter', scheduleOpen);
            wrapper.addEventListener('mouseleave', scheduleClose);
        } else if (trigger) {
            // Touch / no-hover fallback: tap to toggle
            trigger.addEventListener('click', function (e) {
                if (wrapper.classList.contains('is-open')) {
                    close();
                    return;
                }
                // Close any other open dropdown first
                document.querySelectorAll('.header-account-wrapper.is-open, .header-cart-wrapper.is-open')
                    .forEach(function (el) {
                        if (el !== wrapper) el.classList.remove('is-open');
                    });
                e.preventDefault();
                open();
            });
        }

        // Keyboard accessibility
        wrapper.addEventListener('focusin', open);
        wrapper.addEventListener('focusout', function (e) {
            if (!wrapper.contains(e.relatedTarget)) scheduleClose();
        });

        wrapper.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                close();
                if (trigger) trigger.focus();
            }
        });

        if (trigger) trigger.setAttribute('aria-expanded', 'false');
    }

    /**
     * Wires up all top-level nav menu items that have a sub-menu, plus
     * their nested sub-menus, using the same hover-intent timing.
     */
    function initNavSubmenus() {
        var items = document.querySelectorAll('.nav-menu .menu-item-has-children');

        items.forEach(function (item) {
            var panel = item.querySelector(':scope > .sub-menu');
            var trigger = item.querySelector(':scope > a');
            if (!panel) return;

            var openTimer = null;
            var closeTimer = null;

            function clearTimers() {
                if (openTimer) { clearTimeout(openTimer); openTimer = null; }
                if (closeTimer) { clearTimeout(closeTimer); closeTimer = null; }
            }

            function open() {
                clearTimers();
                item.classList.add('is-open');
            }

            function close() {
                clearTimers();
                item.classList.remove('is-open');
                // Close any nested submenus too
                item.querySelectorAll('.is-open').forEach(function (el) {
                    el.classList.remove('is-open');
                });
            }

            if (hasHover) {
                item.addEventListener('mouseenter', function () {
                    clearTimers();
                    openTimer = setTimeout(open, OPEN_DELAY);
                });
                item.addEventListener('mouseleave', function () {
                    clearTimers();
                    closeTimer = setTimeout(close, CLOSE_DELAY);
                });
            } else if (trigger) {
                trigger.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (item.classList.contains('is-open')) {
                        close();
                        return;
                    }
                    var siblings = item.parentElement
                        ? item.parentElement.querySelectorAll(':scope > .menu-item-has-children.is-open')
                        : [];
                    siblings.forEach(function (el) {
                        if (el !== item) el.classList.remove('is-open');
                    });
                    open();
                });
            }

            item.addEventListener('focusin', open);
            item.addEventListener('focusout', function (e) {
                if (!item.contains(e.relatedTarget)) close();
            });
        });
    }

    /**
     * Wires up the header search form(s) so submitting shows a loading
     * state (spinner + dimmed field) while the browser navigates to the
     * search results page. Plain GET form submit - no AJAX to hook into,
     * so this just needs to fire before the page unloads.
     */
    function initSearchLoadingState() {
        document.addEventListener('submit', function (e) {
            var form = e.target;

            if (!form.classList || !form.classList.contains('search-form')) {
                return;
            }

            var field = form.querySelector('.search-field');

            // Don't show a spinner for an empty search that the browser/theme
            // will just block or no-op on.
            if (field && !field.value.trim()) {
                return;
            }

            form.classList.add('is-searching');

            var submitBtn = form.querySelector('.search-submit');
            if (submitBtn) {
                submitBtn.setAttribute('aria-busy', 'true');
            }
        }, true);
    }

    function closeAllOnOutsideClick(e) {
        document.querySelectorAll(
            '.header-account-wrapper.is-open, .header-cart-wrapper.is-open, .nav-menu .menu-item-has-children.is-open'
        ).forEach(function (el) {
            if (!el.contains(e.target)) el.classList.remove('is-open');
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var accountWrapper = document.querySelector('.header-account-wrapper');
        var cartWrapper = document.querySelector('.header-cart-wrapper');

        if (accountWrapper) initDropdown(accountWrapper);
        if (cartWrapper) initDropdown(cartWrapper);

        initNavSubmenus();
        initSearchLoadingState();

        document.addEventListener('click', closeAllOnOutsideClick);
    });
})();