/**
 * Header Search Collapse
 *
 * Watches the header for actual overflow (not a fixed breakpoint) and
 * collapses the inline search bar down to an icon-only toggle when
 * there isn't enough room for it alongside the nav menu. Clicking the
 * toggle opens a full-width overlay search panel instead.
 *
 * Two steps, only applied when the header genuinely overflows:
 *   1. .is-compact   - search bar shrinks to a narrower width
 *   2. .is-collapsed - search bar becomes an icon-only toggle
 * Headers that already fit are left completely untouched.
 *
 * Self-contained - only touches .header-search-bar / .header-search-overlay,
 * so it can't conflict with other header scripts (navigation.js, etc.).
 *
 * @package aaapos-prime
 */
(function () {
    'use strict';

    function debounce(fn, wait) {
        var timeout;
        return function () {
            var context = this;
            var args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                fn.apply(context, args);
            }, wait);
        };
    }

    function init() {
        var headerInner = document.querySelector('.site-header .header-inner');
        var searchBar = document.querySelector('.header-search-bar');
        var toggleBtn = searchBar ? searchBar.querySelector('.header-search-toggle') : null;
        var dropdown = document.getElementById('header-search-dropdown');
        var dropdownInput = dropdown ? dropdown.querySelector('.search-field') : null;

        if (!headerInner || !searchBar) {
            return;
        }

        // Move the dropdown to be a direct child of <body> and switch it
        // to position:fixed, positioned via JS from the toggle button's
        // real on-screen coordinates. This guarantees it can never be
        // clipped by an ancestor's overflow:hidden, regardless of what
        // wrapper elements exist between it and the header.
        if (dropdown && dropdown.parentElement !== document.body) {
            document.body.appendChild(dropdown);
        }

        function positionDropdown() {
            if (!dropdown || !toggleBtn) {
                return;
            }
            var rect = toggleBtn.getBoundingClientRect();
            var dropdownWidth = dropdown.offsetWidth || 260;
            var gap = 30;
            var left = rect.right - dropdownWidth;
            // Keep it on-screen if the icon sits near the left edge.
            left = Math.max(8, Math.min(left, window.innerWidth - dropdownWidth - 8));

            dropdown.style.top = (rect.bottom + gap) + 'px';
            dropdown.style.left = left + 'px';
        }

        /**
         * Element width including its horizontal margins.
         * Uses offsetWidth (layout width) so load-in animation
         * transforms (scale/translate) don't skew the measurement.
         */
        function outerWidth(el) {
            if (!el) {
                return 0;
            }
            var cs = getComputedStyle(el);
            return el.offsetWidth +
                (parseFloat(cs.marginLeft) || 0) +
                (parseFloat(cs.marginRight) || 0);
        }

        /**
         * Width the header actions actually NEED (sum of their visible
         * children + gaps). .header-actions is allowed to flex-shrink,
         * so its own box can be narrower than its contents when the
         * header is overflowing - measuring the box alone hides the
         * overflow.
         */
        function actionsContentWidth(actions) {
            if (!actions) {
                return 0;
            }
            var total = 0;
            var visible = 0;
            Array.prototype.forEach.call(actions.children, function (child) {
                if (child.offsetWidth > 0) {
                    total += outerWidth(child);
                    visible++;
                }
            });
            var cs = getComputedStyle(actions);
            var gap = parseFloat(cs.columnGap || cs.gap || 0) || 0;
            if (visible > 1) {
                total += gap * (visible - 1);
            }
            return total;
        }

        /**
         * True if branding + nav + actions don't fit inside the header's
         * content area. We sum the three flex children's own widths
         * rather than using headerInner.scrollWidth, because scrollWidth
         * also counts hidden dropdown mega-menu panels (if they're
         * hidden via opacity/visibility rather than display:none),
         * which falsely inflates it regardless of real available space.
         */
        function isHeaderOverflowing() {
            var branding = document.querySelector('.site-branding');
            var mainNav = document.querySelector('.main-navigation');
            var actions = document.querySelector('.header-actions');

            var innerStyle = getComputedStyle(headerInner);

            // clientWidth includes padding - subtract it to get the
            // real space available for content.
            var available = headerInner.clientWidth -
                (parseFloat(innerStyle.paddingLeft) || 0) -
                (parseFloat(innerStyle.paddingRight) || 0);

            var gapValue = parseFloat(innerStyle.columnGap || innerStyle.gap || 0) || 0;

            var totalWidth = outerWidth(branding) +
                outerWidth(mainNav) +
                actionsContentWidth(actions) +
                gapValue * 2; // 2 gaps between the 3 children

            return totalWidth > available + 1;
        }

        /**
         * Decide whether the header is actually overflowing and toggle
         * the compact / collapsed state accordingly. Only applies at
         * desktop widths (matches the >=1024px breakpoint in
         * _header-actions.css).
         */
        function checkOverflow() {
            if (window.innerWidth < 1024) {
                searchBar.classList.remove('is-collapsed', 'is-compact');
                if (toggleBtn) {
                    toggleBtn.setAttribute('aria-expanded', 'false');
                }
                closeDropdown();
                return;
            }

            // Measure with the search bar at full size first, so we get
            // an accurate read on whether it actually fits.
            var wasCollapsed = searchBar.classList.contains('is-collapsed');
            searchBar.classList.remove('is-collapsed', 'is-compact');

            if (isHeaderOverflowing()) {
                // Step 1: try a narrower search bar
                searchBar.classList.add('is-compact');

                if (isHeaderOverflowing()) {
                    // Step 2: still no room - collapse to icon
                    searchBar.classList.remove('is-compact');
                    searchBar.classList.add('is-collapsed');
                }
            }

            if (wasCollapsed && !searchBar.classList.contains('is-collapsed')) {
                closeDropdown();
            }
        }

        function openDropdown() {
            if (!dropdown) {
                return;
            }
            positionDropdown();
            dropdown.classList.add('is-open');
            if (toggleBtn) {
                toggleBtn.setAttribute('aria-expanded', 'true');
            }
            document.addEventListener('keydown', onKeydown);
            document.addEventListener('click', onDocumentClick, true);
            window.addEventListener('scroll', onScrollOrResize, true);
            window.addEventListener('resize', onScrollOrResize);
            if (dropdownInput) {
                window.setTimeout(function () {
                    dropdownInput.focus();
                }, 0);
            }
        }

        function closeDropdown() {
            if (!dropdown || !dropdown.classList.contains('is-open')) {
                return;
            }
            dropdown.classList.remove('is-open');
            if (toggleBtn) {
                toggleBtn.setAttribute('aria-expanded', 'false');
            }
            document.removeEventListener('keydown', onKeydown);
            document.removeEventListener('click', onDocumentClick, true);
            window.removeEventListener('scroll', onScrollOrResize, true);
            window.removeEventListener('resize', onScrollOrResize);
        }

        function onScrollOrResize() {
            positionDropdown();
        }

        function onKeydown(e) {
            if (e.key === 'Escape') {
                closeDropdown();
                if (toggleBtn) {
                    toggleBtn.focus();
                }
            }
        }

        function onDocumentClick(e) {
            if (!dropdown || !dropdown.classList.contains('is-open')) {
                return;
            }
            if (dropdown.contains(e.target) || (toggleBtn && toggleBtn.contains(e.target))) {
                return;
            }
            closeDropdown();
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                if (dropdown && !dropdown.classList.contains('is-open')) {
                    openDropdown();
                } else {
                    closeDropdown();
                }
            });
        }

        // Re-check whenever the header's actual content width could
        // have changed (viewport resize covers zoom, orientation, and
        // window resizing).
        window.addEventListener('resize', debounce(checkOverflow, 150));

        // Also re-check if the header's own content changes size for
        // reasons other than a window resize (e.g. a menu item's text
        // wrapping differently after webfonts finish loading).
        if (window.ResizeObserver) {
            var observer = new ResizeObserver(debounce(checkOverflow, 150));
            observer.observe(headerInner);
        }

        // Webfonts can change menu text widths after first paint.
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(checkOverflow);
        }

        checkOverflow();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();