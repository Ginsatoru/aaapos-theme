/**
 * WooCommerce Enhanced Functionality
 * woocommerce.js - FIXED VERSION - NO TAB SCROLLING
 * 
 * @package Macedon_Ranges
 */

(function($) {
    'use strict';

    /**
     * WooCommerce Enhancements
     */
    class MRWooCommerce {
        constructor() { 
            this.init();
        }

        init() {
            this.initQuickView();
            this.initProductFilters();
            this.initQuantityButtons();
            this.initWishlist();
            this.initCompare();
            this.initAjaxAddToCart();
            this.initCartDrawer();
            this.initProductTabs();
            this.initColorSwatches();
        }

        /**
         * Quick View Modal
         */
        initQuickView() {
            $(document).on('click', '.quick-view-btn', function(e) {
                e.preventDefault();
                const productId = $(this).data('product-id');
                
                if (!productId) return;

                const $btn = $(this);
                const originalText = $btn.text();
                $btn.text('Loading...').prop('disabled', true);

                $.ajax({
                    url: mr_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'mr_quick_view',
                        product_id: productId,
                        nonce: mr_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            const modal = $('<div class="quick-view-modal">' + response.data.html + '</div>');
                            $('body').append(modal);
                            modal.find('.woocommerce-product-gallery').wc_product_gallery();
                            setTimeout(() => modal.addClass('visible'), 10);
                            modal.on('click', '.quick-view-close, .quick-view-overlay', function() {
                                modal.removeClass('visible');
                                setTimeout(() => modal.remove(), 300);
                            });
                        }
                    },
                    error: function() {
                        if (window.MRAnimations) {
                            window.MRAnimations.showNotification('Failed to load product', 'error');
                        }
                    },
                    complete: function() {
                        $btn.text(originalText).prop('disabled', false);
                    }
                });
            });
        }

        /**
         * Product Filters
         */
        initProductFilters() {
            const $filters = $('.product-filters');
            if (!$filters.length) return;

            const $priceSlider = $filters.find('.price-slider');
            if ($priceSlider.length && $.fn.slider) {
                const min = parseInt($priceSlider.data('min')) || 0;
                const max = parseInt($priceSlider.data('max')) || 1000;

                $priceSlider.slider({
                    range: true,
                    min: min,
                    max: max,
                    values: [min, max],
                    slide: function(event, ui) {
                        $('.price-range-display').text(`$${ui.values[0]} - $${ui.values[1]}`);
                    }
                });
            }

            $filters.on('submit', 'form', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $('.products').addClass('loading');
                
                $.ajax({
                    url: mr_ajax.ajax_url,
                    type: 'POST',
                    data: formData + '&action=mr_filter_products',
                    success: function(response) {
                        if (response.success) {
                            $('.products').html(response.data.html);
                            if (history.pushState) {
                                history.pushState(null, null, '?' + formData);
                            }
                        }
                    },
                    complete: function() {
                        $('.products').removeClass('loading');
                    }
                });
            });

            $filters.on('click', '.clear-filters', function(e) {
                e.preventDefault();
                $filters.find('form')[0].reset();
                $filters.find('form').submit();
            });
        }

        /**
         * Quantity Increment/Decrement Buttons
         */
        initQuantityButtons() {
            $(document).on('click', '.quantity-btn', function(e) {
                e.preventDefault();
                
                const $btn = $(this);
                const $input = $btn.siblings('.qty');
                const currentVal = parseInt($input.val()) || 0;
                const min = parseInt($input.attr('min')) || 1;
                const max = parseInt($input.attr('max')) || 999;
                
                let newVal = currentVal;
                
                if ($btn.hasClass('quantity-plus')) {
                    newVal = currentVal < max ? currentVal + 1 : max;
                } else if ($btn.hasClass('quantity-minus')) {
                    newVal = currentVal > min ? currentVal - 1 : min;
                }
                
                $input.val(newVal).trigger('change');
            });
        }

        /**
         * Wishlist Functionality
         */
        initWishlist() {
            $(document).on('click', '.add-to-wishlist', function(e) {
                e.preventDefault();
                
                const $btn = $(this);
                const productId = $btn.data('product-id');
                
                $btn.addClass('loading');

                $.ajax({
                    url: mr_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'mr_toggle_wishlist',
                        product_id: productId,
                        nonce: mr_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $btn.toggleClass('in-wishlist');
                            
                            if (window.MRAnimations) {
                                window.MRAnimations.showNotification(response.data.message, 'success');
                            }
                            
                            $('.wishlist-count').text(response.data.count);
                        }
                    },
                    complete: function() {
                        $btn.removeClass('loading');
                    }
                });
            });
        }

        /**
         * Product Compare
         */
        initCompare() {
            $(document).on('click', '.add-to-compare', function(e) {
                e.preventDefault();
                
                const $btn = $(this);
                const productId = $btn.data('product-id');
                
                $.ajax({
                    url: mr_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'mr_toggle_compare',
                        product_id: productId,
                        nonce: mr_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $btn.toggleClass('in-compare');
                            
                            if (window.MRAnimations) {
                                window.MRAnimations.showNotification(response.data.message, 'success');
                            }
                            
                            $('.compare-count').text(response.data.count);
                        }
                    }
                });
            });
        }

        /**
         * AJAX Add to Cart
         */
        initAjaxAddToCart() {
            $(document).on('click', '.ajax-add-to-cart', function(e) {
                e.preventDefault();
                
                const $btn = $(this);
                const productId = $btn.data('product-id');
                const quantity = $btn.data('quantity') || 1;
                
                $btn.addClass('loading');

                $.ajax({
                    url: mr_ajax.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart'),
                    type: 'POST',
                    data: {
                        product_id: productId,
                        quantity: quantity
                    },
                    success: function(response) {
                        if (response.error) {
                            if (window.MRAnimations) {
                                window.MRAnimations.showNotification(response.error_message, 'error');
                            }
                        } else {
                            $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $btn]);
                            
                            if (window.MRAnimations) {
                                window.MRAnimations.showNotification('Product added to cart!', 'success');
                            }
                        }
                    },
                    complete: function() {
                        $btn.removeClass('loading');
                    }
                });
            });
        }

        /**
         * Cart Drawer/Sidebar
         */
        initCartDrawer() {
            $(document).on('click', '.cart-trigger', function(e) {
                e.preventDefault();
                $('.cart-drawer').addClass('open');
                $('body').addClass('cart-drawer-open');
            });

            $(document).on('click', '.cart-drawer__close, .cart-drawer__overlay', function() {
                $('.cart-drawer').removeClass('open');
                $('body').removeClass('cart-drawer-open');
            });
        }

        /**
         * Product Tabs Enhancement - FIXED: NO AUTO-SCROLL
         * Prevents the annoying upward scroll when clicking tabs
         */
        initProductTabs() {
            const $tabs = $('.woocommerce-tabs');
            if (!$tabs.length) return;

            // Store original scroll position
            let scrollBeforeClick = 0;

            $tabs.find('.tabs li a').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Save current scroll position BEFORE any changes
                scrollBeforeClick = $(window).scrollTop();
                
                const $this = $(this);
                const target = $this.attr('href');
                
                // Update active states
                $this.closest('li').addClass('active').siblings().removeClass('active');
                $(target).show().addClass('active').siblings('.woocommerce-Tabs-panel').hide().removeClass('active');
                
                // Update ARIA attributes for accessibility
                $this.attr('aria-selected', 'true').attr('tabindex', '0');
                $this.closest('li').siblings().find('a').attr('aria-selected', 'false').attr('tabindex', '-1');
                
                // CRITICAL FIX: Restore scroll position immediately
                setTimeout(function() {
                    $(window).scrollTop(scrollBeforeClick);
                }, 0);
                
                return false;
            });

            // Prevent hash changes from scrolling
            if (window.location.hash && window.location.hash.indexOf('tab-') !== -1) {
                const savedScroll = $(window).scrollTop();
                setTimeout(function() {
                    $(window).scrollTop(savedScroll);
                }, 1);
            }

            // Handle hashchange events (prevent auto-scroll)
            $(window).on('hashchange', function(e) {
                const hash = window.location.hash;
                if (hash && hash.indexOf('tab-') !== -1) {
                    const currentScroll = $(window).scrollTop();
                    const $targetTab = $('.tabs li a[href="' + hash + '"]');
                    if ($targetTab.length) {
                        $targetTab.trigger('click');
                        $(window).scrollTop(currentScroll);
                    }
                    return false;
                }
            });
        }

        /**
         * Color/Attribute Swatches
         */
        initColorSwatches() {
            $(document).on('click', '.variation-swatch', function(e) {
                e.preventDefault();
                
                const $swatch = $(this);
                const value = $swatch.data('value');
                const $select = $swatch.closest('.variations').find('select');
                
                $select.val(value).trigger('change');
                $swatch.addClass('selected').siblings().removeClass('selected');
            });
        }
    }

    // Initialize WooCommerce enhancements
    $(function() {
        new MRWooCommerce();
    });

})(jQuery);