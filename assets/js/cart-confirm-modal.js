/**
 * Cart Confirmation Modal
 * Handles confirmation dialog for clearing shopping cart
 */

class CartConfirmModal {
    constructor() {
        this.overlay = null;
        this.modal = null;
        this.callback = null;
        this.init();
    }

    init() {
        this.createModal();
        this.attachEvents();
        this.bindClearCartButton();
    }

    createModal() {
        // Create overlay
        this.overlay = document.createElement('div');
        this.overlay.className = 'cart-confirm-overlay';
        this.overlay.setAttribute('role', 'dialog');
        this.overlay.setAttribute('aria-modal', 'true');
        this.overlay.setAttribute('aria-labelledby', 'cart-confirm-title');

        // Create modal content
        this.overlay.innerHTML = `
            <div class="cart-confirm-modal">
                <div class="cart-confirm-header">
                    <div class="cart-confirm-icon">
                        <svg fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 id="cart-confirm-title" class="cart-confirm-title">Clear Shopping Cart?</h3>
                </div>
                <p class="cart-confirm-message">
                    Are you sure you want to remove all items from your cart? This action cannot be undone.
                </p>
                <div class="cart-confirm-actions">
                    <button type="button" class="cart-confirm-btn cart-confirm-btn-cancel" data-action="cancel">
                        Cancel
                    </button>
                    <button type="button" class="cart-confirm-btn cart-confirm-btn-confirm" data-action="confirm">
                        Clear Cart
                    </button>
                </div>
            </div>
        `;

        // Append to body
        document.body.appendChild(this.overlay);
        this.modal = this.overlay.querySelector('.cart-confirm-modal');
    }

    attachEvents() {
        // Close on overlay click
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.close();
            }
        });

        // Handle button clicks
        this.overlay.addEventListener('click', (e) => {
            const action = e.target.dataset.action;
            if (action === 'cancel') {
                this.close();
            } else if (action === 'confirm') {
                this.confirm();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.overlay.classList.contains('active')) {
                this.close();
            }
        });
    }

    bindClearCartButton() {
        // Wait for DOM to be ready
        const attachToButton = () => {
            // Multiple selectors to catch the button
            const clearCartBtn = document.querySelector(
                '.clear-cart-btn, .clear-cart-link, [name="clear_cart"], a[href*="clear-cart"]'
            );
            
            if (clearCartBtn) {
                console.log('Clear cart button found:', clearCartBtn);
                
                clearCartBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const clearUrl = clearCartBtn.href || clearCartBtn.dataset.url;
                    console.log('Clear cart URL:', clearUrl);
                    
                    this.show(() => {
                        // Proceed with clearing cart
                        if (clearUrl) {
                            window.location.href = clearUrl;
                        } else {
                            // If it's a form button
                            const form = clearCartBtn.closest('form');
                            if (form) {
                                form.submit();
                            }
                        }
                    });
                });
            } else {
                console.warn('Clear cart button not found');
            }
        };

        // Try immediately
        attachToButton();
        
        // Also try after a short delay in case the button loads later
        setTimeout(attachToButton, 500);
        setTimeout(attachToButton, 1000);
    }

    show(callback) {
        this.callback = callback;
        this.overlay.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Focus on cancel button for accessibility
        setTimeout(() => {
            const cancelBtn = this.overlay.querySelector('[data-action="cancel"]');
            if (cancelBtn) cancelBtn.focus();
        }, 100);
    }

    close() {
        this.overlay.classList.remove('active');
        document.body.style.overflow = '';
        this.callback = null;
    }

    confirm() {
        if (typeof this.callback === 'function') {
            this.callback();
        }
        this.close();
    }
}

// Initialize modal
let cartConfirmModal;

// Multiple initialization methods to ensure it loads
function initCartConfirmModal() {
    if (!cartConfirmModal) {
        cartConfirmModal = new CartConfirmModal();
        window.cartConfirmModal = cartConfirmModal;
        console.log('Cart Confirm Modal initialized');
    }
}

// Try different loading methods
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCartConfirmModal);
} else {
    initCartConfirmModal();
}

// Also try after window load
window.addEventListener('load', () => {
    if (!cartConfirmModal) {
        initCartConfirmModal();
    }
});

// Export for use in other scripts
window.CartConfirmModal = CartConfirmModal;