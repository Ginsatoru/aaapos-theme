/**
 * AAAPOS – WooCommerce Animated Cart Notification
 * Tick → Expand → Collapse → Slide
 * @version 2.0.0
 */

(function($) {
  'use strict';

  let autoCloseTimer = null;

  function isMyAccountPage() {
    return $('body').hasClass('woocommerce-account') ||
           $('.woocommerce-MyAccount-navigation').length > 0;
  }

  function createNotification(productName) {
    removeNotification();

    const $el = $(`
      <div class="aaapos-cart-notification">
        <div class="aaapos-cart-tick">✓</div>
        <div class="aaapos-cart-content">
          <div class="aaapos-cart-close">&times;</div>
          <div class="aaapos-cart-icon">✓</div>
          <div class="aaapos-cart-title">
            <strong>${escapeHtml(productName)}</strong><br>
            has been added to your cart successfully
          </div>
          <div class="aaapos-cart-actions">
            <a href="${getCartUrl()}" class="aaapos-cart-view">View Cart</a>
          </div>
        </div>
      </div>
    `);

    $('body').append($el);

    requestAnimationFrame(() => {
      $el.addClass('is-active');

      setTimeout(() => {
        $el.addClass('is-center');
      }, 50);

      setTimeout(() => {
        $el.addClass('is-expanded');
      }, 550);

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
    return text.replace(/[&<>"']/g, m => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
    }[m]));
  }

  /* Listen to WooCommerce */
  $(document.body).on('added_to_cart', function(e, fragments, hash, button) {
    if (isMyAccountPage()) return;
    createNotification(getProductName($(button)));
  });

})(jQuery);
