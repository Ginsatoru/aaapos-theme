/**
 * Header Search Collapse
 *
 * Watches the header for actual overflow (not a fixed breakpoint) and
 * collapses the inline search bar down to an icon-only toggle when
 * there isn't enough room for it alongside the nav menu. Clicking the
 * toggle opens a full-width overlay search panel instead.
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
         * Decide whether the header is actually overflowing and toggle
         * the collapsed state accordingly. Only applies at desktop
         * widths (matches the >=1024px breakpoint in _header-actions.css).
         */
        function checkOverflow() {
            if (window.innerWidth < 1024) {
                searchBar.classList.remove('is-collapsed');
                if (toggleBtn) {
                    toggleBtn.setAttribute('aria-expanded', 'false');
                }
                closeDropdown();
                return;
            }

            // Measure with the search bar expanded first, so we get an
            // accurate read on whether it actually fits. We sum the
            // three flex children's own rendered widths rather than
            // using headerInner.scrollWidth, because scrollWidth also
            // counts hidden dropdown mega-menu panels (if they're
            // hidden via opacity/visibility rather than display:none),
            // which falsely inflates it regardless of real available space.
            var wasCollapsed = searchBar.classList.contains('is-collapsed');
            searchBar.classList.remove('is-collapsed');

            var branding = document.querySelector('.site-branding');
            var mainNav = document.querySelector('.main-navigation');
            var actions = document.querySelector('.header-actions');

            var totalWidth = 0;
            [branding, mainNav, actions].forEach(function (el) {
                if (el) {
                    totalWidth += el.getBoundingClientRect().width;
                }
            });

            var gapValue = parseFloat(
                getComputedStyle(headerInner).columnGap ||
                getComputedStyle(headerInner).gap ||
                0
            ) || 0;
            totalWidth += gapValue * 2; // 2 gaps between the 3 children

            var isOverflowing = totalWidth > headerInner.clientWidth + 1;

            if (isOverflowing) {
                searchBar.classList.add('is-collapsed');
            } else if (wasCollapsed) {
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

        checkOverflow();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();