/**
 * Shopping cart functionality
 * UPDATED: Extended notification timing without fade effects
 * UPDATED: Fix empty cart display when removing items one by one
 * UPDATED: Coupon error messages show as notifications instead of inline
 * cart.js
 */
class CartManager {
  constructor() {
    this.ajaxUrl = this.getAjaxUrl();
    this.nonce = this.getNonce();
    this.isEmptyCart = this.checkIfEmptyCart();
    this.init();
  }

  /**
   * Get AJAX URL from available sources
   */
  getAjaxUrl() {
    // Try multiple sources for AJAX URL
    if (typeof mr_ajax !== "undefined" && mr_ajax.url) {
      return mr_ajax.url;
    }
    if (typeof mr_ajax !== "undefined" && mr_ajax.ajax_url) {
      return mr_ajax.ajax_url;
    }
    if (typeof mrTheme !== "undefined" && mrTheme.ajaxUrl) {
      return mrTheme.ajaxUrl;
    }
    if (
      typeof wc_add_to_cart_params !== "undefined" &&
      wc_add_to_cart_params.ajax_url
    ) {
      return wc_add_to_cart_params.ajax_url;
    }
    // Fallback to WordPress default
    return "/wp-admin/admin-ajax.php";
  }

  /**
   * Get nonce from available sources
   */
  getNonce() {
    if (typeof mr_ajax !== "undefined" && mr_ajax.nonce) {
      return mr_ajax.nonce;
    }
    if (typeof mrTheme !== "undefined" && mrTheme.nonce) {
      return mrTheme.nonce;
    }
    return "";
  }

  /**
   * Check if cart is currently empty on page load
   */
  checkIfEmptyCart() {
    if (!this.isCartPage()) return false;
    
    return document.querySelector('.cart-empty-wrapper') !== null ||
           document.querySelector('.wc-empty-cart-message') !== null ||
           document.querySelector('.cart-empty') !== null;
  }

  init() {
    this.bindEvents();
    this.bindWooCommerceEvents();
    this.handleCouponErrors();
    
    // If on empty cart, prevent fragment updates
    if (this.isEmptyCart && this.isCartPage()) {
      this.preventFragmentUpdates();
    }
  }

  /**
   * Handle coupon error messages - convert to notifications
   */
  handleCouponErrors() {
    // Watch for WooCommerce error messages in the coupon area
    if (typeof jQuery !== 'undefined') {
      // Intercept coupon form submissions
      jQuery(document).on('click', '.coupon button[name="apply_coupon"]', (e) => {
        const couponInput = jQuery('input[name="coupon_code"]');
        if (!couponInput.val().trim()) {
          e.preventDefault();
          this.showMessage('Please enter a coupon code.', 'error');
          return false;
        }
      });

      // Listen for WooCommerce notices after coupon application
      jQuery(document.body).on('updated_wc_div', () => {
        this.checkAndConvertCouponErrors();
      });

      // Also check on page load
      setTimeout(() => {
        this.checkAndConvertCouponErrors();
      }, 100);
    }
  }

  /**
   * Check for coupon errors and convert them to notifications
   */
  checkAndConvertCouponErrors() {
    // Find all WooCommerce error messages
    const errorMessages = document.querySelectorAll('.woocommerce-error, .woocommerce-message, .woocommerce-info');
    
    errorMessages.forEach(message => {
      // Check if it's a coupon-related message
      const messageText = message.textContent.trim();
      
      if (messageText.toLowerCase().includes('coupon') || 
          messageText.toLowerCase().includes('code') ||
          message.querySelector('a[href*="cart"]')) {
        
        // Extract the actual message (remove any links)
        const textOnly = messageText.replace(/×/g, '').trim();
        
        // Determine message type
        let messageType = 'info';
        if (message.classList.contains('woocommerce-error')) {
          messageType = 'error';
        } else if (message.classList.contains('woocommerce-message')) {
          messageType = 'success';
        }
        
        // Show as notification
        this.showMessage(textOnly, messageType);
        
        // Remove the original message
        message.style.display = 'none';
        message.remove();
      }
    });
  }

  /**
   * Prevent WooCommerce from updating fragments on empty cart page
   * This stops any broken layout from rendering
   */
  preventFragmentUpdates() {
    // Override the fragment refresh
    if (typeof jQuery !== 'undefined') {
      const originalTrigger = jQuery.fn.trigger;
      jQuery.fn.trigger = function(event) {
        // Block fragment-related events on empty cart
        if ((event === 'wc_fragment_refresh' || 
             event === 'wc_fragments_refreshed' || 
             event === 'wc_fragments_loaded') && 
            document.body.classList.contains('cart-is-empty')) {
          return this;
        }
        return originalTrigger.apply(this, arguments);
      };
    }
  }

  bindEvents() {
    // Add to cart buttons - Featured products & custom buttons
    document.addEventListener("click", (e) => {
      const button = e.target.closest(
        ".add-to-cart-button, .product-card__add-to-cart",
      );

      if (button && !button.classList.contains("ajax_add_to_cart")) {
        e.preventDefault();
        this.addToCart(button);
      }
    });

    // Remove item from cart (X button in cart table)
    document.addEventListener("click", (e) => {
      const removeLink = e.target.closest("a.remove");
      if (removeLink && this.isCartPage()) {
        e.preventDefault();
        e.stopPropagation();
        this.removeFromCartPage(removeLink);
      }
    }, true);

    // Remove item from cart dropdown - Using event delegation with capture phase
    document.addEventListener("click", (e) => {
      const removeBtn = e.target.closest(".cart-item-remove");
      if (removeBtn) {
        e.preventDefault();
        e.stopPropagation();
        this.removeFromCart(removeBtn);
      }
    }, true);

    // Quantity changes in cart page
    document.addEventListener("change", (e) => {
      if (e.target.classList.contains("qty")) {
        const cartItemKey = this.getCartItemKeyFromInput(e.target);
        if (cartItemKey) {
          this.updateQuantity(e.target, cartItemKey);
        }
      }
    });
  }

  /**
   * Extract cart item key from input name attribute
   * Input name format: cart[CART_ITEM_KEY][qty]
   */
  getCartItemKeyFromInput(input) {
    const name = input.getAttribute('name');
    if (!name) return null;
    
    // Match pattern: cart[CART_ITEM_KEY][qty]
    const match = name.match(/cart\[([^\]]+)\]\[qty\]/);
    return match ? match[1] : null;
  }

  bindWooCommerceEvents() {
    // Check if jQuery is available
    if (typeof jQuery === "undefined") {
      console.warn("jQuery not available for WooCommerce events");
      return;
    }

    // Listen for WooCommerce's native add to cart event
    jQuery(document.body).on(
      "added_to_cart",
      (event, fragments, cart_hash, $button) => {
        // If adding to empty cart, wait for notification to complete
        if (this.isEmptyCart && this.isCartPage()) {
          // Prevent any fragment updates
          event.stopImmediatePropagation();
          
          // Wait for notification to complete its full display cycle
          // This gives time for: appear -> display -> auto-hide animation
          setTimeout(() => {
            window.location.reload();
          }, 5500);
          
          return false;
        }
        
        // Normal flow for non-empty cart
        this.updateFragments(fragments);
        this.showMessage("Product added to cart!", "success");
      },
    );

    // Listen for WooCommerce's native remove from cart event
    jQuery(document.body).on(
      "removed_from_cart",
      (event, fragments, cart_hash) => {
        if (fragments) {
          this.updateFragments(fragments);
        }
      },
    );

    // Listen for cart update
    jQuery(document.body).on("wc_fragments_refreshed", () => {
      // Only update if not on empty cart
      if (!this.isEmptyCart || !this.isCartPage()) {
        this.updateCartCountVisibility();
      }
    });
  }

  /**
   * Check if current page is the cart page
   */
  isCartPage() {
    return document.body.classList.contains('woocommerce-cart');
  }

  /**
   * Handle remove item from cart page (X button)
   * This prevents the broken layout when cart becomes empty
   */
  async removeFromCartPage(link) {
    const url = link.getAttribute('href');
    
    if (!url) {
      console.error("No remove URL found");
      return;
    }

    // Show loading state
    const row = link.closest('tr');
    if (row) {
      row.style.opacity = '0.5';
      row.style.pointerEvents = 'none';
    }

    try {
      // Make the remove request
      const response = await fetch(url, {
        method: 'GET',
        credentials: 'same-origin',
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      // Check how many items are left in the cart
      const remainingRows = document.querySelectorAll('.woocommerce-cart-form__cart-item').length;
      
      // If this is the last item, reload to show empty cart properly
      if (remainingRows <= 1) {
        this.showMessage("Item removed from cart.", "info");
        setTimeout(() => {
          window.location.reload();
        }, 500);
        return;
      }

      // If there are still items, just reload normally
      window.location.reload();

    } catch (error) {
      console.error("Remove from cart error:", error);
      this.showMessage("Failed to remove item. Please try again.", "error");

      // Restore row state
      if (row) {
        row.style.opacity = '1';
        row.style.pointerEvents = '';
      }
    }
  }

  async addToCart(button) {
    const productId =
      button.dataset.product_id || button.dataset.productId || button.value;
    const quantity = button.dataset.quantity || 1;

    if (!productId) {
      console.error("No product ID found");
      return;
    }

    // Show loading state
    button.classList.add("loading");
    const originalText = button.innerHTML;
    button.disabled = true;

    try {
      // Check if WooCommerce AJAX params are available
      if (typeof wc_add_to_cart_params === "undefined") {
        throw new Error("WooCommerce not properly initialized");
      }

      const formData = new FormData();
      formData.append("product_id", productId);
      formData.append("quantity", quantity);

      const response = await fetch(
        wc_add_to_cart_params.wc_ajax_url
          .toString()
          .replace("%%endpoint%%", "add_to_cart"),
        {
          method: "POST",
          body: formData,
        },
      );

      const data = await response.json();

      if (data.error && data.product_url) {
        // Variable product or other - redirect to product page
        window.location.href = data.product_url;
        return;
      }

      if (data.fragments) {
        // Show success message
        this.showMessage("Product added to cart!", "success");
        
        // If we're on empty cart, wait for notification to complete
        if (this.isEmptyCart && this.isCartPage()) {
          setTimeout(() => {
            window.location.reload();
          }, 2500);
          
          return;
        }
        
        // Normal flow for non-empty cart
        this.updateFragments(data.fragments);

        // Trigger WooCommerce event for compatibility
        if (typeof jQuery !== "undefined") {
          jQuery(document.body).trigger("added_to_cart", [
            data.fragments,
            data.cart_hash,
            jQuery(button),
          ]);
        }
      }
    } catch (error) {
      console.error("Add to cart error:", error);
      this.showMessage(
        "Failed to add product to cart. Please try again.",
        "error",
      );
    } finally {
      // Only update button if not reloading
      if (!this.isEmptyCart || !this.isCartPage()) {
        button.classList.remove("loading");
        button.disabled = false;
        button.innerHTML = originalText;
      }
    }
  }

  async removeFromCart(button) {
    const cartItemKey = button.dataset.cartItemKey;

    if (!cartItemKey) {
      console.error("No cart item key found");
      return;
    }

    // Show loading state
    button.classList.add("loading");
    const listItem = button.closest(".cart-dropdown-item");
    if (listItem) {
      listItem.style.opacity = "0.5";
      listItem.style.pointerEvents = "none";
    }

    try {
      const formData = new FormData();
      formData.append("action", "remove_cart_item");
      formData.append("cart_item_key", cartItemKey);
      formData.append("nonce", this.nonce);

      const response = await fetch(this.ajaxUrl, {
        method: "POST",
        body: formData,
        credentials: "same-origin",
      });

      // Check if response is OK
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const contentType = response.headers.get("content-type");
      if (!contentType || !contentType.includes("application/json")) {
        throw new Error("Response is not JSON");
      }

      const data = await response.json();

      if (data.success) {
        // Update fragments if provided
        if (data.data.fragments) {
          this.updateFragments(data.data.fragments);
        }
        
        // Update cart count
        this.updateCartCount(data.data.cart_count);

        // Remove the item from dropdown with animation (slide to the RIGHT)
        if (listItem) {
          listItem.style.transition = "all 0.3s ease";
          listItem.style.transform = "translateX(5%)";
          listItem.style.opacity = "0";

          setTimeout(() => {
            listItem.remove();

            // If cart is empty, update dropdown
            if (data.data.cart_count === 0) {
              const itemsList = document.querySelector(".cart-dropdown-items");
              if (itemsList) {
                itemsList.innerHTML =
                  '<li class="cart-dropdown-empty"><p>Your cart is empty.</p></li>';
              }
            }
          }, 300);
        }

        // Update subtotal
        const subtotalEl = document.querySelector(".cart-subtotal-amount");
        if (subtotalEl && data.data.cart_subtotal) {
          subtotalEl.innerHTML = data.data.cart_subtotal;
        }

        // Update item count text
        const itemCountEl = document.querySelector(".cart-item-count");
        if (itemCountEl) {
          const count = data.data.cart_count;
          itemCountEl.textContent = `${count} ${count === 1 ? "item" : "items"}`;
        }

        this.showMessage("Item removed from cart.", "info");

        // Trigger WooCommerce event for compatibility
        if (typeof jQuery !== "undefined") {
          jQuery(document.body).trigger("removed_from_cart", [
            data.data.fragments || null,
            null,
          ]);
        }
      } else {
        throw new Error(data.data?.message || "Failed to remove item");
      }
    } catch (error) {
      console.error("Remove from cart error:", error);
      this.showMessage("Failed to remove item. Please try again.", "error");

      // Restore item state
      if (listItem) {
        listItem.style.opacity = "1";
        listItem.style.pointerEvents = "";
      }
    } finally {
      button.classList.remove("loading");
    }
  }

  async updateQuantity(input, cartItemKey) {
    const quantity = input.value;

    if (!cartItemKey || quantity < 0) return;

    // Show loading state on the row
    const row = input.closest('tr');
    if (row) {
      row.style.opacity = '0.6';
      row.style.pointerEvents = 'none';
    }

    try {
      const formData = new FormData();
      formData.append("action", "update_cart_quantity");
      formData.append("cart_item_key", cartItemKey);
      formData.append("quantity", quantity);
      formData.append("nonce", this.nonce);

      const response = await fetch(this.ajaxUrl, {
        method: "POST",
        body: formData,
        credentials: "same-origin",
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();

      if (data.success) {
        // Update the product subtotal in the same row
        if (data.data.line_subtotal && row) {
          const subtotalCell = row.querySelector('.product-subtotal');
          if (subtotalCell) {
            subtotalCell.innerHTML = data.data.line_subtotal;
          }
        }

        // Update cart totals section
        if (data.data.cart_totals_html) {
          const cartTotals = document.querySelector('.cart_totals');
          if (cartTotals) {
            cartTotals.outerHTML = data.data.cart_totals_html;
          }
        }

        // Update fragments if provided
        if (data.data.fragments) {
          this.updateFragments(data.data.fragments);
        }
        
        this.updateCartCount(data.data.cart_count);

        // Update subtotal in header/dropdown
        const subtotalEl = document.querySelector(".cart-subtotal-amount");
        if (subtotalEl && data.data.cart_subtotal) {
          subtotalEl.innerHTML = data.data.cart_subtotal;
        }

        // Trigger WooCommerce event
        if (typeof jQuery !== "undefined") {
          jQuery(document.body).trigger('updated_cart_totals');
        }

        this.showMessage("Cart updated successfully!", "success");
      } else {
        throw new Error(data.data?.message || "Failed to update cart");
      }
    } catch (error) {
      console.error("Update cart error:", error);
      this.showMessage("Failed to update cart. Please try again.", "error");
    } finally {
      // Remove loading state
      if (row) {
        row.style.opacity = '1';
        row.style.pointerEvents = '';
      }
    }
  }

  updateFragments(fragments) {
    if (!fragments) return;

    // Don't update fragments if on empty cart page
    if (this.isEmptyCart && this.isCartPage()) {
      return;
    }

    Object.keys(fragments).forEach((selector) => {
      const elements = document.querySelectorAll(selector);
      
      elements.forEach((element) => {
        const fragmentHTML = fragments[selector];
        
        // Special handling for cart dropdown items
        if (selector === '.cart-dropdown-items') {
          // Create temporary container
          const temp = document.createElement('ul');
          temp.className = 'cart-dropdown-items';
          temp.innerHTML = fragmentHTML;
          
          // Replace the entire element instead of just innerHTML
          element.parentNode.replaceChild(temp, element);
        } else {
          // For other fragments, just update innerHTML
          element.innerHTML = fragmentHTML;
        }
      });
    });

    this.updateCartCountVisibility();
  }

  updateCartCount(count) {
    const cartCountElements = document.querySelectorAll(".cart-count");
    cartCountElements.forEach((el) => {
      el.textContent = count;
      el.style.display = count > 0 ? "" : "none";
    });
  }

  updateCartCountVisibility() {
    const cartCountElements = document.querySelectorAll(".cart-count");
    cartCountElements.forEach((el) => {
      const count = parseInt(el.textContent) || 0;
      el.style.display = count > 0 ? "" : "none";
    });
  }

  showMessage(text, type = "info") {
    // Remove existing messages
    const existing = document.querySelector(".cart-message");
    if (existing) {
      existing.remove();
    }

    const message = document.createElement("div");
    message.className = `cart-message cart-message--${type}`;
    message.innerHTML = `
            <div class="cart-message__content">
                <span class="cart-message__icon">
                    ${type === "success" ? "✓" : type === "error" ? "✕" : "ℹ"}
                </span>
                <span class="cart-message__text">${text}</span>
                <button class="cart-message__close" aria-label="Close message">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path fill-rule="evenodd" d="M13.854 2.146a.5.5 0 010 .708l-11 11a.5.5 0 01-.708-.708l11-11a.5.5 0 01.708 0z" clip-rule="evenodd"/>
                        <path fill-rule="evenodd" d="M2.146 2.146a.5.5 0 000 .708l11 11a.5.5 0 00.708-.708l-11-11a.5.5 0 00-.708 0z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        `;

    document.body.appendChild(message);

    // Animate in
    requestAnimationFrame(() => {
      message.classList.add("cart-message--visible");
    });

    // Auto remove after 4 seconds
    const autoRemove = setTimeout(() => {
      this.removeMessage(message);
    }, 4000);

    // Close button
    message
      .querySelector(".cart-message__close")
      .addEventListener("click", () => {
        clearTimeout(autoRemove);
        this.removeMessage(message);
      });
  }

  removeMessage(message) {
    message.classList.remove("cart-message--visible");
    setTimeout(() => {
      if (message.parentNode) {
        message.parentNode.removeChild(message);
      }
    }, 300);
  }
}

// Initialize cart manager when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  window.cartManager = new CartManager();
});

// Also initialize if DOM is already loaded
if (document.readyState !== 'loading') {
  window.cartManager = new CartManager();
}