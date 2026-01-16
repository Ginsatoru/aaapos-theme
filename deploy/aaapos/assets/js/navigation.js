/**
 * Navigation Scripts - Mobile Menu Toggle & Dropdown Management
 * Handles opening/closing mobile navigation, submenu dropdowns, cart and account dropdowns
 */

(function () {
  "use strict";

  // Wait for DOM to be ready
  document.addEventListener("DOMContentLoaded", function () {
    // ==========================================================================
    // MOBILE MENU TOGGLE
    // ==========================================================================

    const mobileToggle = document.querySelector(".mobile-menu-toggle");
    const nav = document.querySelector(".main-navigation");
    const overlay = document.querySelector(".mobile-menu-overlay");
    const body = document.body;

    if (mobileToggle && nav) {
      mobileToggle.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        const isExpanded = this.getAttribute("aria-expanded") === "true";

        // Toggle aria-expanded
        this.setAttribute("aria-expanded", !isExpanded);

        // Toggle navigation active class
        nav.classList.toggle("active");

        // Toggle overlay
        if (overlay) {
          overlay.classList.toggle("active");
        }

        // Toggle body class to prevent scrolling when menu is open
        body.classList.toggle("mobile-menu-open");
      });
    }

    // ==========================================================================
    // CLOSE MENU ON OVERLAY CLICK (Mobile)
    // ==========================================================================

    if (overlay && mobileToggle && nav) {
      overlay.addEventListener("click", function () {
        mobileToggle.setAttribute("aria-expanded", "false");
        nav.classList.remove("active");
        overlay.classList.remove("active");
        body.classList.remove("mobile-menu-open");
      });
    }

    // ==========================================================================
    // CLOSE MENU ON ESC KEY
    // ==========================================================================

    document.addEventListener("keydown", function (e) {
      if (
        e.key === "Escape" &&
        mobileToggle &&
        mobileToggle.getAttribute("aria-expanded") === "true"
      ) {
        mobileToggle.setAttribute("aria-expanded", "false");
        nav.classList.remove("active");
        if (overlay) {
          overlay.classList.remove("active");
        }
        body.classList.remove("mobile-menu-open");
        mobileToggle.focus(); // Return focus to toggle button
      }
    });

    // ==========================================================================
    // REMOVE WORDPRESS DEFAULT SUBMENU ARROWS (Fix double icon issue)
    // ==========================================================================
    
    const submenuToggles = document.querySelectorAll('.main-navigation .submenu-toggle');
    submenuToggles.forEach(function(toggle) {
      toggle.remove(); // Remove WordPress default arrow spans
    });

    // ==========================================================================
    // MOBILE SUBMENU TOGGLE - Using CSS ::after arrows only
    // User clicks the link area to toggle submenu on mobile
    // ==========================================================================

    const menuItemsWithChildren = document.querySelectorAll(
      ".main-navigation .menu-item-has-children",
    );

    menuItemsWithChildren.forEach(function (item) {
      const link = item.querySelector("a");

      if (link) {
        link.addEventListener("click", function (e) {
          // Only prevent default and toggle on mobile
          if (window.innerWidth <= 1023) {
            // Check if click is on the arrow area (right 40px of the link)
            const clickX = e.offsetX;
            const linkWidth = this.offsetWidth;
            const isArrowClick = clickX > linkWidth - 40;

            // If clicking the arrow area OR if submenu is already open, toggle submenu
            // If clicking the text and submenu is closed, allow navigation
            if (isArrowClick || item.classList.contains("active")) {
              e.preventDefault();
              
              // Toggle active class
              item.classList.toggle("active");

              // Close other open submenus at the same level
              const parentItem = item.parentElement;
              const siblings = parentItem.querySelectorAll(':scope > .menu-item-has-children');
              
              siblings.forEach(function (sibling) {
                if (sibling !== item) {
                  sibling.classList.remove("active");
                }
              });
            }
            // else: let the link navigate normally
          }
        });
      }
    });

    // ==========================================================================
    // HANDLE WINDOW RESIZE
    // ==========================================================================

    let resizeTimer;
    window.addEventListener("resize", function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () {
        // Reset mobile menu state on desktop
        if (window.innerWidth > 1023) {
          if (mobileToggle) {
            mobileToggle.setAttribute("aria-expanded", "false");
          }
          if (nav) {
            nav.classList.remove("active");
          }
          if (overlay) {
            overlay.classList.remove("active");
          }
          body.classList.remove("mobile-menu-open");

          // Remove active classes from submenu items
          menuItemsWithChildren.forEach(function (item) {
            item.classList.remove("active");
          });
        }
      }, 250);
    });

    // ==========================================================================
    // CART ITEM REMOVAL (AJAX)
    // ==========================================================================

    const cartRemoveButtons = document.querySelectorAll(".cart-item-remove");

    cartRemoveButtons.forEach(function (button) {
      button.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        // Check if mr_ajax is defined
        if (typeof mr_ajax === "undefined") {
          console.error(
            "AJAX variables not loaded. Make sure inc/ajax.php is properly included.",
          );
          alert(
            "Error: Unable to remove item. Please refresh the page and try again.",
          );
          return;
        }

        const cartItemKey = this.getAttribute("data-cart-item-key");
        const cartItem = this.closest(".cart-dropdown-item");

        if (!cartItemKey) return;

        // Add loading state
        button.disabled = true;
        button.style.opacity = "0.5";

        // AJAX request to remove item
        fetch(mr_ajax.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({
            action: "remove_cart_item",
            cart_item_key: cartItemKey,
            nonce: mr_ajax.nonce,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Fade out and remove item
              cartItem.style.transition =
                "opacity 0.3s ease, transform 0.3s ease";
              cartItem.style.opacity = "0";
              cartItem.style.transform = "translateX(20px)";

              setTimeout(function () {
                cartItem.remove();

                // Update cart count
                updateCartCount(data.data.cart_count);

                // Update subtotal
                updateCartSubtotal(data.data.cart_subtotal);

                // If cart is empty, hide dropdown or show empty message
                if (data.data.cart_count === 0) {
                  const cartDropdown = document.querySelector(".cart-dropdown");
                  if (cartDropdown) {
                    cartDropdown.style.display = "none";
                  }

                  // Reload page to update cart icon
                  setTimeout(function () {
                    window.location.reload();
                  }, 500);
                }
              }, 300);
            } else {
              // Re-enable button on error
              button.disabled = false;
              button.style.opacity = "1";
              alert(data.data.message || "Failed to remove item");
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            button.disabled = false;
            button.style.opacity = "1";
            alert("An error occurred. Please try again.");
          });
      });
    });

    // ==========================================================================
    // HELPER FUNCTIONS
    // ==========================================================================

    function updateCartCount(count) {
      const cartCountElements = document.querySelectorAll(".cart-count");
      const cartItemCountElement = document.querySelector(".cart-item-count");

      cartCountElements.forEach(function (element) {
        if (count > 0) {
          element.textContent = count;
          element.style.display = "flex";
        } else {
          element.style.display = "none";
        }
      });

      if (cartItemCountElement) {
        const itemText = count === 1 ? "item" : "items";
        cartItemCountElement.textContent = count + " " + itemText;
      }
    }

    function updateCartSubtotal(subtotal) {
      const subtotalElement = document.querySelector(".cart-subtotal strong");
      if (subtotalElement && subtotal) {
        subtotalElement.innerHTML = subtotal;
      }
    }

    // ==========================================================================
    // KEYBOARD NAVIGATION FOR DROPDOWNS (Desktop)
    // ==========================================================================

    const navLinks = document.querySelectorAll(".main-navigation .nav-menu a");

    navLinks.forEach(function (link) {
      link.addEventListener("keydown", function (e) {
        const parent = this.closest("li");
        const submenu = parent.querySelector(".sub-menu");

        // Arrow Down - Open submenu or move to first item
        if (e.key === "ArrowDown") {
          e.preventDefault();

          if (submenu) {
            const firstLink = submenu.querySelector("a");
            if (firstLink) {
              firstLink.focus();
            }
          } else {
            // Move to next menu item
            const nextItem = parent.nextElementSibling;
            if (nextItem) {
              const nextLink = nextItem.querySelector("a");
              if (nextLink) nextLink.focus();
            }
          }
        }

        // Arrow Up - Move to previous item
        if (e.key === "ArrowUp") {
          e.preventDefault();

          const prevItem = parent.previousElementSibling;
          if (prevItem) {
            const prevLink = prevItem.querySelector("a");
            if (prevLink) prevLink.focus();
          }
        }

        // Arrow Right - Open submenu or move to next top-level item
        if (e.key === "ArrowRight") {
          e.preventDefault();

          if (submenu) {
            const firstLink = submenu.querySelector("a");
            if (firstLink) {
              firstLink.focus();
            }
          } else {
            const nextItem = parent.nextElementSibling;
            if (nextItem) {
              const nextLink = nextItem.querySelector("a");
              if (nextLink) nextLink.focus();
            }
          }
        }

        // Arrow Left - Move to parent or previous top-level item
        if (e.key === "ArrowLeft") {
          e.preventDefault();

          const parentMenu = parent.closest(".sub-menu");
          if (parentMenu) {
            const parentLink = parentMenu.previousElementSibling;
            if (parentLink) parentLink.focus();
          } else {
            const prevItem = parent.previousElementSibling;
            if (prevItem) {
              const prevLink = prevItem.querySelector("a");
              if (prevLink) prevLink.focus();
            }
          }
        }
      });
    });

    // ==========================================================================
    // PREVENT DROPDOWN CLOSE ON CLICK INSIDE
    // ==========================================================================

    const cartDropdown = document.querySelector(".cart-dropdown");
    const accountDropdown = document.querySelector(".account-dropdown");

    if (cartDropdown) {
      cartDropdown.addEventListener("click", function (e) {
        e.stopPropagation();
      });
    }

    if (accountDropdown) {
      accountDropdown.addEventListener("click", function (e) {
        e.stopPropagation();
      });
    }

    // ==========================================================================
    // MEGA MENU FUNCTIONALITY
    // ==========================================================================

    const megaMenuItems = document.querySelectorAll(".has-mega-menu");

    megaMenuItems.forEach(function (item) {
      const dropdown = item.querySelector(".mega-menu-dropdown");

      if (dropdown) {
        // Add hover effect class based on customizer setting
        const hoverEffect =
          document.body.getAttribute("data-menu-hover-effect") || "fade-slide";
        dropdown.parentElement.classList.add("menu-hover-" + hoverEffect);

        // Touch device support
        if ("ontouchstart" in window) {
          item.addEventListener("touchstart", function (e) {
            // Prevent default only if dropdown is closed
            if (!item.classList.contains("mega-menu-active")) {
              e.preventDefault();

              // Close other mega menus
              megaMenuItems.forEach(function (otherItem) {
                if (otherItem !== item) {
                  otherItem.classList.remove("mega-menu-active");
                }
              });

              item.classList.add("mega-menu-active");
            }
          });
        }
      }
    });

    // Close mega menu when clicking outside
    document.addEventListener("click", function (e) {
      if (!e.target.closest(".has-mega-menu")) {
        megaMenuItems.forEach(function (item) {
          item.classList.remove("mega-menu-active");
        });
      }
    });
  });
})();