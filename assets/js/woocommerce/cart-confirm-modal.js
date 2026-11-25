/**
 * Cart Confirmation Modal
 * Handles confirmation dialog for clearing shopping cart
 */

class CartConfirmModal {
    constructor() {
        this.overlay = null;
        this.modal = null;
        this.callback = null;
        this.isInitialized = false;
        this.init();
    }

    init() {
        if (this.isInitialized) return;
        
        this.createModal();
        this.attachEvents();
        this.bindClearCartButton();
        this.isInitialized = true;
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

        document.body.appendChild(this.overlay);
        this.modal = this.overlay.querySelector('.cart-confirm-modal');
    }

    attachEvents() {
        this.overlay.addEventListener('click', this.handleOverlayClick.bind(this));
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen()) {
                this.close();
            }
        });
    }

    handleOverlayClick(e) {
        // Close on overlay click
        if (e.target === this.overlay) {
            this.close();
            return;
        }

        // Handle button clicks
        const action = e.target.dataset?.action;
        if (action === 'cancel') {
            this.close();
        } else if (action === 'confirm') {
            this.confirm();
        }
    }

    bindClearCartButton() {
        const attachHandler = () => {
            const clearCartBtn = document.querySelector(
                '.clear-cart-btn, .clear-cart-link, [name="clear_cart"], a[href*="clear-cart"]'
            );
            
            if (!clearCartBtn) return;

            clearCartBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const clearUrl = clearCartBtn.href || clearCartBtn.dataset?.url;
                
                this.show(() => {
                    if (clearUrl) {
                        window.location.href = clearUrl;
                    } else {
                        const form = clearCartBtn.closest('form');
                        form?.submit();
                    }
                });
            });
        };

        // Attempt to attach handler
        attachHandler();
        
        // Retry if button loads dynamically
        [500, 1000].forEach(delay => {
            setTimeout(attachHandler, delay);
        });
    }

    show(callback) {
        this.callback = callback;
        this.overlay.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Focus management for accessibility
        requestAnimationFrame(() => {
            const cancelBtn = this.overlay.querySelector('[data-action="cancel"]');
            cancelBtn?.focus();
        });
    }

    close() {
        this.overlay.classList.remove('active');
        document.body.style.overflow = '';
        this.callback = null;
    }

    confirm() {
        this.callback?.();
        this.close();
    }

    isOpen() {
        return this.overlay.classList.contains('active');
    }

    destroy() {
        this.overlay?.remove();
        this.overlay = null;
        this.modal = null;
        this.callback = null;
        this.isInitialized = false;
    }
}

// Singleton instance management
const CartConfirmModalManager = (() => {
    let instance = null;

    return {
        getInstance: () => {
            if (!instance) {
                instance = new CartConfirmModal();
            }
            return instance;
        },
        
        init: () => {
            return CartConfirmModalManager.getInstance();
        },
        
        destroy: () => {
            if (instance) {
                instance.destroy();
                instance = null;
            }
        }
    };
})();

// Initialize when DOM is ready
const initCartConfirmModal = () => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', CartConfirmModalManager.init);
    } else {
        CartConfirmModalManager.init();
    }
};

// Initialize immediately
initCartConfirmModal();

// Export for module systems if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { CartConfirmModal, CartConfirmModalManager };
}

// Global access for browser
window.CartConfirmModal = CartConfirmModal;
window.CartConfirmModalManager = CartConfirmModalManager;