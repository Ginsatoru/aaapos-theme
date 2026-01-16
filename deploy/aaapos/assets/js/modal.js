/**
 * Modal functionality
 */
class Modal {
    constructor() {
        this.modals = new Map();
        this.init();
    }

    init() {
        this.initSearchModal();
        this.initQuickViewModal();
        this.bindGlobalEvents();
    }

    initSearchModal() {
        const searchToggle = document.querySelector('.search-toggle');
        const searchModal = document.querySelector('.search-modal');

        if (!searchToggle || !searchModal) return;

        this.modals.set('search', {
            element: searchModal,
            openers: [searchToggle]
        });

        // Search functionality
        const searchInput = searchModal.querySelector('input[type="search"]');
        const searchResults = searchModal.querySelector('.search-results-container');

        if (searchInput && searchResults) {
            this.initSearch(searchInput, searchResults);
        }
    }

    initSearch(input, resultsContainer) {
        let searchTimeout;

        input.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();

            if (query.length < 2) {
                resultsContainer.innerHTML = '';
                return;
            }

            searchTimeout = setTimeout(() => {
                this.performSearch(query, resultsContainer);
            }, 300);
        });

        // Handle form submission
        input.closest('form').addEventListener('submit', (e) => {
            if (input.value.trim().length < 2) {
                e.preventDefault();
            }
        });
    }

    async performSearch(query, container) {
        try {
            const response = await fetch(`${mr_ajax.url}?action=mr_product_search&s=${encodeURIComponent(query)}&nonce=${mr_ajax.nonce}`);
            const data = await response.json();

            if (data.success) {
                container.innerHTML = data.data;
            }
        } catch (error) {
            console.error('Search error:', error);
            container.innerHTML = '<p class="search-error">Search failed. Please try again.</p>';
        }
    }

    initQuickViewModal() {
        const quickViewModal = document.querySelector('.quick-view-modal');
        if (quickViewModal) {
            this.modals.set('quick-view', {
                element: quickViewModal,
                openers: []
            });
        }
    }

    bindGlobalEvents() {
        // Close modals on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });

        // Close modals on backdrop click
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-backdrop') || 
                e.target.closest('.modal-close')) {
                this.closeAllModals();
            }
        });
    }

    openModal(name) {
        const modal = this.modals.get(name);
        if (modal) {
            modal.element.classList.add('active');
            document.body.classList.add('modal-open');

            // Focus trap
            this.trapFocus(modal.element);

            // Dispatch event
            document.dispatchEvent(new CustomEvent('modalOpened', { detail: { name } }));
        }
    }

    closeModal(name) {
        const modal = this.modals.get(name);
        if (modal) {
            modal.element.classList.remove('active');
            document.body.classList.remove('modal-open');

            // Dispatch event
            document.dispatchEvent(new CustomEvent('modalClosed', { detail: { name } }));
        }
    }

    closeAllModals() {
        this.modals.forEach((modal, name) => {
            this.closeModal(name);
        });
    }

    trapFocus(element) {
        const focusableElements = element.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        element.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if (document.activeElement === firstElement) {
                        e.preventDefault();
                        lastElement.focus();
                    }
                } else {
                    if (document.activeElement === lastElement) {
                        e.preventDefault();
                        firstElement.focus();
                    }
                }
            }
        });

        firstElement.focus();
    }
}

// Initialize modal system
document.addEventListener('DOMContentLoaded', () => {
    window.modal = new Modal();
});