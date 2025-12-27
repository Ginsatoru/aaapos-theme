/**
 * AAAPOS – WooCommerce Animated Cart Notification
 * Tick → Expand → Collapse → Slide
 * FULL ROW LAYOUT (COMPACT + GROUPED)
 * NOW WITH COUPON SUPPORT
 * @version 2.3.0
 */

(function ($) {
  'use strict';

  let autoCloseTimer = null;

  // SVG Checkmark Icon
  const checkmarkSVG = `
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
      <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
    </svg>
  `;

  // SVG Tag/Coupon Icon
  const couponSVG = `
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
      <line x1="7" y1="7" x2="7.01" y2="7"/>
    </svg>
  `;

  function isMyAccountPage() {
    return $('body').hasClass('woocommerce-account') ||
           $('.woocommerce-MyAccount-navigation').length > 0;
  }

  /**
   * Create notification for product added to cart
   */
  function createNotification(productName) {
    removeNotification();

    const $el = $(`
      <div class="aaapos-cart-notification">
        <div class="aaapos-cart-tick">${checkmarkSVG}</div>

        <div class="aaapos-cart-content aaapos-cart-row">
          <div class="aaapos-cart-icon">${checkmarkSVG}</div>

          <div class="aaapos-cart-text">
            <div class="aaapos-cart-title">
              <strong>${escapeHtml(productName)}</strong>
            </div>
            <div class="aaapos-cart-desc">
              added to cart
            </div>
          </div>

          <div class="aaapos-cart-actions">
            <a href="${getCartUrl()}" class="aaapos-cart-view">
              View Cart
            </a>
          </div>

          <div class="aaapos-cart-close">&times;</div>
        </div>
      </div>
    `);

    $('body').append($el);
    animateNotification($el);
  }

  /**
   * Create notification for coupon applied
   */
  function createCouponNotification(couponCode, discountAmount) {
    removeNotification();

    const $el = $(`
      <div class="aaapos-cart-notification">
        <div class="aaapos-cart-tick">${checkmarkSVG}</div>

        <div class="aaapos-cart-content aaapos-cart-row">
          <div class="aaapos-cart-icon aaapos-cart-icon--coupon">${couponSVG}</div>

          <div class="aaapos-cart-text">
            <div class="aaapos-cart-title">
              <strong>${escapeHtml(couponCode.toUpperCase())}</strong>
            </div>
            <div class="aaapos-cart-desc">
              ${discountAmount ? 'Discount: ' + discountAmount : 'coupon applied'}
            </div>
          </div>

          <div class="aaapos-cart-actions">
            <a href="${getCartUrl()}" class="aaapos-cart-view">
              View Cart
            </a>
          </div>

          <div class="aaapos-cart-close">&times;</div>
        </div>
      </div>
    `);

    $('body').append($el);
    animateNotification($el);
  }

  /**
   * Animate notification (shared for both types)
   */
  function animateNotification($el) {
    requestAnimationFrame(() => {
      $el.addClass('is-active');

      setTimeout(() => {
        $el.addClass('is-center');
      }, 50);

      setTimeout(() => {
        $el.addClass('is-expanded');
      }, 1000);

      autoCloseTimer = setTimeout(closeNotification, 4000);
    });

    $el.on('click', '.aaapos-cart-close', closeNotification);
  }

  function closeNotification() {
    const $el = $('.aaapos-cart-notification');
    if (!$el.length) return;

    clearTimeout(autoCloseTimer);

    $el.removeClass('is-expanded');

    setTimeout(() => {
      $el.removeClass('is-center');
    }, 450);

    setTimeout(() => {
      $el.removeClass('is-active');
      setTimeout(() => $el.remove(), 300);
    }, 900);
  }

  function removeNotification() {
    $('.aaapos-cart-notification').remove();
  }

  function getProductName(button) {
    const $card = button.closest('.product, li.product');
    const $title = $card.find('.woocommerce-loop-product__title, h2, h3');
    return $title.length ? $title.text().trim() : 'Product';
  }

  function getCartUrl() {
    return (typeof wc_add_to_cart_params !== 'undefined')
      ? wc_add_to_cart_params.cart_url
      : '/cart';
  }

  function escapeHtml(text) {
    return text.replace(/[&<>"']/g, function (m) {
      return {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      }[m];
    });
  }

  /**
   * Extract coupon info from WooCommerce message
   */
  function extractCouponInfo(message) {
    // Try to extract coupon code from message
    // Example: "Coupon code applied successfully."
    const couponInput = $('input[name="coupon_code"]').val();
    
    // Try to find discount amount in cart totals
    let discountAmount = '';
    
    // Look for the coupon discount in the updated cart
    setTimeout(() => {
      const $couponRow = $('.cart-discount');
      if ($couponRow.length) {
        discountAmount = $couponRow.find('.amount').text();
      }
    }, 100);

    return {
      code: couponInput || 'Coupon',
      amount: discountAmount
    };
  }

  // ==========================================================================
  // EVENT LISTENERS
  // ==========================================================================

  /**
   * Listen for "Add to Cart" events
   */
  $(document.body).on('added_to_cart', function (e, fragments, hash, button) {
    if (isMyAccountPage()) return;
    createNotification(getProductName($(button)));
  });

  /**
   * Listen for Coupon Apply Events (Method 1: Cart Update)
   */
  $(document.body).on('updated_cart_totals', function() {
    // Check if there's a success message for coupon
    const $successMessage = $('.woocommerce-message');
    
    if ($successMessage.length && $successMessage.text().toLowerCase().includes('coupon')) {
      const couponInfo = extractCouponInfo($successMessage.text());
      
      // Small delay to get the discount amount
      setTimeout(() => {
        const $couponDiscount = $('.cart-discount .amount').first();
        const discountAmount = $couponDiscount.length ? $couponDiscount.text() : '';
        
        createCouponNotification(couponInfo.code, discountAmount);
      }, 200);
    }
  });

  /**
   * Listen for Coupon Apply Events (Method 2: Form Submission)
   */
  $(document).on('submit', 'form.woocommerce-cart-form', function(e) {
    // Check if apply coupon button was clicked
    if ($(document.activeElement).attr('name') === 'apply_coupon') {
      const couponCode = $('input[name="coupon_code"]').val();
      
      if (couponCode) {
        // Store the coupon code for later use
        sessionStorage.setItem('pending_coupon', couponCode);
      }
    }
  });

  /**
   * Listen for AJAX complete to catch coupon application
   */
  $(document).ajaxComplete(function(event, xhr, settings) {
    // Check if this is a coupon-related AJAX call
    if (settings.url && settings.url.includes('apply_coupon')) {
      const pendingCoupon = sessionStorage.getItem('pending_coupon');
      
      if (pendingCoupon) {
        sessionStorage.removeItem('pending_coupon');
        
        // Wait for cart to update
        setTimeout(() => {
          const $couponDiscount = $('.cart-discount .amount').first();
          const discountAmount = $couponDiscount.length ? $couponDiscount.text() : '';
          
          // Check if coupon was successful (no error messages)
          if ($('.woocommerce-error').length === 0) {
            createCouponNotification(pendingCoupon, discountAmount);
          }
        }, 500);
      }
    }
  });

  /**
   * Direct coupon button click handler (fallback)
   */
  $(document).on('click', 'button[name="apply_coupon"]', function() {
    const couponCode = $(this).closest('form').find('input[name="coupon_code"]').val();
    
    if (couponCode) {
      // Monitor for success
      const checkInterval = setInterval(function() {
        const $successMessage = $('.woocommerce-message');
        
        if ($successMessage.length && $successMessage.text().toLowerCase().includes('coupon')) {
          clearInterval(checkInterval);
          
          setTimeout(() => {
            const $couponDiscount = $('.cart-discount .amount').first();
            const discountAmount = $couponDiscount.length ? $couponDiscount.text() : '';
            
            createCouponNotification(couponCode, discountAmount);
          }, 300);
        }
      }, 100);
      
      // Stop checking after 3 seconds
      setTimeout(() => clearInterval(checkInterval), 3000);
    }
  });

})(jQuery);