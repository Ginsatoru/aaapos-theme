/**
 * Variation Alert
 * Clean alert system for product variation selection
 * 
 * @package aaapos-prime
 * @since 1.0.0
 */

class VariationAlert {
    constructor() {
        this.alertContainer = null;
        this.init();
    }

    init() {
        this.createAlertHTML();
        this.bindEvents();
    }

    createAlertHTML() {
        // Check if alert already exists
        if (document.querySelector('.variation-alert')) {
            this.alertContainer = document.querySelector('.variation-alert');
            return;
        }

        const alertHTML = `
            <div class="variation-alert">
                <div class="variation-alert__backdrop"></div>
                <div class="variation-alert__container">
                    <div class="variation-alert__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="variation-alert__content">
                        <h3 class="variation-alert__title">Select Product Options</h3>
                        <p class="variation-alert__message">Please select some product options before adding this product to your cart.</p>
                        <div class="variation-alert__actions">
                            <button class="variation-alert__button variation-alert__button--primary">Got it</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', alertHTML);
        this.alertContainer = document.querySelector('.variation-alert');
    }

    bindEvents() {
        if (!this.alertContainer) return;

        // Primary button
        const primaryBtn = this.alertContainer.querySelector('.variation-alert__button--primary');
        if (primaryBtn) {
            primaryBtn.addEventListener('click', () => this.hide());
        }

        // Backdrop click
        const backdrop = this.alertContainer.querySelector('.variation-alert__backdrop');
        if (backdrop) {
            backdrop.addEventListener('click', () => this.hide());
        }

        // Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.alertContainer.classList.contains('active')) {
                this.hide();
            }
        });
    }

    show(options = {}) {
        const defaults = {
            title: 'Select Product Options',
            message: 'Please select some product options before adding this product to your cart.',
            buttonText: 'Got it',
            type: 'warning' // warning, success, error, info
        };

        const settings = { ...defaults, ...options };

        // Update content
        const title = this.alertContainer.querySelector('.variation-alert__title');
        const message = this.alertContainer.querySelector('.variation-alert__message');
        const button = this.alertContainer.querySelector('.variation-alert__button--primary');
        const icon = this.alertContainer.querySelector('.variation-alert__icon');

        if (title) title.textContent = settings.title;
        if (message) message.textContent = settings.message;
        if (button) button.textContent = settings.buttonText;

        // Update icon type
        if (icon) {
            icon.className = `variation-alert__icon variation-alert__icon--${settings.type}`;
        }

        // Show alert
        this.alertContainer.classList.add('active');
        document.body.classList.add('variation-alert-open');

        // Focus on primary button
        setTimeout(() => {
            button?.focus();
        }, 100);

        // Auto-hide after 5 seconds (optional)
        // setTimeout(() => this.hide(), 5000);
    }

    hide() {
        if (!this.alertContainer) return;

        this.alertContainer.classList.remove('active');
        document.body.classList.remove('variation-alert-open');
    }

    destroy() {
        if (this.alertContainer) {
            this.alertContainer.remove();
            this.alertContainer = null;
        }
    }
}

// Initialize variation alert
let variationAlert;

document.addEventListener('DOMContentLoaded', () => {
    variationAlert = new VariationAlert();

    // Override WooCommerce variation form validation
    const variationForms = document.querySelectorAll('.variations_form');
    
    variationForms.forEach(form => {
        const addToCartBtn = form.querySelector('.single_add_to_cart_button');
        
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', (e) => {
                // Check if product is variable and variations are not selected
                const variations = form.querySelectorAll('.variations select');
                let allSelected = true;

                variations.forEach(select => {
                    if (!select.value || select.value === '') {
                        allSelected = false;
                    }
                });

                // If not all variations are selected, show alert
                if (!allSelected && variations.length > 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Prevent default WooCommerce alert
                    setTimeout(() => {
                        variationAlert.show({
                            title: 'Select Product Options',
                            message: 'Please select some product options before adding this product to your cart.',
                            buttonText: 'Got it',
                            type: 'warning'
                        });
                    }, 10);

                    return false;
                }
            });
        }
    });
});

// Export for global use
window.VariationAlert = VariationAlert;
window.variationAlert = variationAlert;