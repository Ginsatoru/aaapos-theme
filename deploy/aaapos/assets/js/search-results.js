/**
 * Search Results - AJAX Sorting & Column View Toggle (2, 3, or 4 columns)
 * WITH RESPONSIVE MOBILE OVERRIDE
 * @package aaapos-prime
 */

(function($) {
    'use strict';

    class SearchResults {
        constructor() {
            this.isLoading = false;
            this.mobileBreakpoint = 767;
            this.init();
        }

        init() {
            this.initSorting();
            this.initColumnToggle();
            this.initResponsiveHandler();
        }

        initSorting() {
            const $orderby = $('.woocommerce-ordering select.orderby');
            
            if (!$orderby.length) return;

            const self = this;

            // Prevent form submission and handle via AJAX
            $orderby.closest('form').on('submit', function(e) {
                e.preventDefault();
                return false;
            });

            $orderby.on('change', function() {
                const orderby = $(this).val();
                self.updateResults(orderby);
            });
        }

        updateResults(orderby) {
            if (this.isLoading) return;

            const $productsGrid = $('.products.search-results-grid');
            const $toolbar = $('.search-toolbar');
            const searchQuery = $('input[name="s"]').val();

            if (!$productsGrid.length) return;

            this.isLoading = true;

            // Add loading state
            $productsGrid.addClass('loading').css('opacity', '0.5');
            $toolbar.css('pointer-events', 'none');

            // Build URL with parameters
            const url = new URL(window.location.href);
            url.searchParams.set('orderby', orderby);
            url.searchParams.set('s', searchQuery);
            url.searchParams.set('post_type', 'product');

            // Fetch sorted results
            $.ajax({
                url: url.toString(),
                type: 'GET',
                dataType: 'html',
                success: (response) => {
                    // Extract products from response
                    const $response = $(response);
                    const $newProducts = $response.find('.products.search-results-grid').html();
                    
                    if ($newProducts) {
                        // Get current column setting
                        const currentColumns = $productsGrid.attr('data-columns');
                        
                        // Update products
                        $productsGrid.html($newProducts);
                        
                        // Restore column setting
                        $productsGrid.attr('data-columns', currentColumns);
                        
                        // Update URL without reload
                        window.history.pushState({}, '', url.toString());
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Search sorting error:', error);
                },
                complete: () => {
                    this.isLoading = false;
                    $productsGrid.removeClass('loading').css('opacity', '1');
                    $toolbar.css('pointer-events', '');
                }
            });
        }

        initColumnToggle() {
            const $columnToggles = $('.column-toggle');
            const $productsGrid = $('.products.search-results-grid');
            
            if (!$columnToggles.length || !$productsGrid.length) return;

            // Get saved column preference (default: 4)
            const savedColumns = localStorage.getItem('searchResultsColumns') || '4';
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
                localStorage.setItem('searchResultsColumns', columns);
            });
        }

        initResponsiveHandler() {
            const $productsGrid = $('.products.search-results-grid');
            const $columnToggles = $('.column-toggle');
            
            if (!$productsGrid.length) return;

            const self = this;
            let resizeTimer;

            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const savedColumns = localStorage.getItem('searchResultsColumns') || '4';
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
        new SearchResults();
    });

})(jQuery);