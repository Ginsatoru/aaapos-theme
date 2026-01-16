/**
 * Shop Page - Column View Toggle (2, 3, or 4 columns)
 * WITH RESPONSIVE MOBILE OVERRIDE
 * @package aaapos-prime
 */

(function($) {
    'use strict';

    class ShopColumnToggle {
        constructor() {
            this.mobileBreakpoint = 767;
            this.init();
        }

        init() {
            this.initColumnToggle();
            this.initResponsiveHandler();
        }

        initColumnToggle() {
            const $columnToggles = $('.column-toggle');
            const $productsGrid = $('.woocommerce ul.products, .woocommerce-page ul.products');
            
            if (!$columnToggles.length || !$productsGrid.length) return;

            // Get saved column preference (default: 4)
            const savedColumns = localStorage.getItem('shopColumnsView') || '4';
            this.applyResponsiveColumns(savedColumns, $productsGrid, $columnToggles);

            const self = this;

            $columnToggles.on('click', function(e) {
                e.preventDefault();
                
                const $btn = $(this);
                const columns = $btn.attr('data-columns');
                
                if (!columns) return;
                
                // Update active state
                $columnToggles.removeClass('active');
                $btn.addClass('active');
                
                // Apply column layout
                self.setColumns(columns, $productsGrid, $columnToggles);
                
                // Save preference
                localStorage.setItem('shopColumnsView', columns);
            });
        }

        initResponsiveHandler() {
            const $productsGrid = $('.woocommerce ul.products, .woocommerce-page ul.products');
            const $columnToggles = $('.column-toggle');
            
            if (!$productsGrid.length) return;

            const self = this;
            let resizeTimer;

            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const savedColumns = localStorage.getItem('shopColumnsView') || '4';
                    self.applyResponsiveColumns(savedColumns, $productsGrid, $columnToggles);
                }, 250);
            });
        }

        applyResponsiveColumns(columns, $grid, $toggles) {
            const isMobile = window.innerWidth <= this.mobileBreakpoint;
            
            if (isMobile) {
                // Force 2 columns on mobile
                this.setColumns('2', $grid, $toggles, true);
            } else {
                // Use saved preference on desktop/tablet
                this.setColumns(columns, $grid, $toggles, false);
            }
        }

        setColumns(columns, $grid, $toggles, isMobileOverride = false) {
            // Validate columns value
            if (!columns || !['2', '3', '4'].includes(columns)) {
                columns = '4';
            }
            
            // Add transition class
            $grid.addClass('view-transitioning');
            
            // Set data attribute for CSS targeting
            $grid.attr('data-columns', columns);
            
            // Update active toggle button (only if not mobile override)
            if (!isMobileOverride && $toggles && $toggles.length) {
                $toggles.removeClass('active');
                $toggles.filter(`[data-columns="${columns}"]`).addClass('active');
            }
            
            // Force reflow for transition
            if ($grid[0]) {
                $grid[0].offsetHeight;
            }
            
            // Remove transition class after animation
            setTimeout(() => {
                $grid.removeClass('view-transitioning');
            }, 300);
        }
    }

    $(function() {
        new ShopColumnToggle();
    });

})(jQuery);