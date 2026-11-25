/**
 * Main theme JavaScript
 * @package Macedon_Ranges
 */

class MacedonRangesTheme {
    constructor() {
        this.lastScroll = 0;
        this.init();
    }

    init() {
        this.initNavigation();
        this.initModals();
        this.initAnimations();
        this.initSliders();
        this.initCart();
        this.initMobileSubmenu();
        
        // WooCommerce specific
        if (typeof wc_add_to_cart_params !== 'undefined') {
            this.initWooCommerce();
        }
    }

    // ===================================
    // NAVIGATION FUNCTIONALITY
    // ===================================
    initNavigation() {
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');
        const header = document.querySelector('.site-header');

        // Mobile menu toggle
        if (mobileToggle && mobileMenu) {
            mobileToggle.addEventListener('click', () => {
                const isExpanded = mobileToggle.getAttribute('aria-expanded') === 'true';
                mobileToggle.setAttribute('aria-expanded', !isExpanded);
                mobileMenu.classList.toggle('active');
                mobileMenu.setAttribute('aria-hidden', isExpanded);
                document.body.classList.toggle('menu-open');
            });

            // Close mobile menu on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
                    mobileToggle.setAttribute('aria-expanded', 'false');
                    mobileMenu.classList.remove('active');
                    mobileMenu.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('menu-open');
                }
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.mobile-menu') && 
                    !e.target.closest('.mobile-menu-toggle') && 
                    mobileMenu.classList.contains('active')) {
                    mobileToggle.setAttribute('aria-expanded', 'false');
                    mobileMenu.classList.remove('active');
                    mobileMenu.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('menu-open');
                }
            });
        }

        // Sticky header with scroll behavior
        this.initStickyHeader(header);

        // Smooth scroll for anchor links
        this.initSmoothScroll();
    }

    initStickyHeader(header) {
        if (!header) return;

        let ticking = false;

        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    const currentScroll = window.pageYOffset;
                    
                    // Add scrolled class when scrolled more than 50px
                    if (currentScroll > 50) {
                        document.body.classList.add('scrolled');
                        header.classList.add('is-sticky');
                    } else {
                        document.body.classList.remove('scrolled');
                        header.classList.remove('is-sticky');
                    }
                    
                    this.lastScroll = currentScroll;
                    ticking = false;
                });

                ticking = true;
            }
        }, { passive: true });
    }

    initSmoothScroll() {
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // Ignore empty anchors and menu toggles
                if (href === '#' || href === '#main') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    const headerOffset = 140; // Account for fixed header
                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    // ===================================
    // MOBILE SUBMENU TOGGLE
    // ===================================
    initMobileSubmenu() {
        const mobileMenu = document.querySelector('.mobile-menu');
        if (!mobileMenu) return;

        const menuItems = mobileMenu.querySelectorAll('.menu-item-has-children');
        
        menuItems.forEach(item => {
            const link = item.querySelector('a');
            const submenu = item.querySelector('.sub-menu');
            
            if (link && submenu) {
                // Create toggle button
                const toggleBtn = document.createElement('button');
                toggleBtn.className = 'submenu-toggle';
                toggleBtn.setAttribute('aria-expanded', 'false');
                toggleBtn.setAttribute('aria-label', 'Toggle submenu');
                toggleBtn.innerHTML = '<span></span>';
                
                // Insert after the link
                link.after(toggleBtn);
                
                // Toggle submenu
                toggleBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const isExpanded = toggleBtn.getAttribute('aria-expanded') === 'true';
                    
                    // Close other open submenus at the same level
                    const siblings = item.parentElement.querySelectorAll(':scope > .menu-item-has-children');
                    siblings.forEach(sibling => {
                        if (sibling !== item) {
                            sibling.classList.remove('submenu-open');
                            const siblingToggle = sibling.querySelector('.submenu-toggle');
                            const siblingSubmenu = sibling.querySelector('.sub-menu');
                            if (siblingToggle) siblingToggle.setAttribute('aria-expanded', 'false');
                            if (siblingSubmenu) siblingSubmenu.classList.remove('active');
                        }
                    });
                    
                    // Toggle current submenu
                    toggleBtn.setAttribute('aria-expanded', !isExpanded);
                    item.classList.toggle('submenu-open');
                    submenu.classList.toggle('active');
                });
            }
        });
    }

    // ===================================
    // MODAL FUNCTIONALITY
    // ===================================
    initModals() {
        const searchToggle = document.querySelector('.search-toggle');
        const searchModal = document.querySelector('.search-modal');

        if (searchToggle && searchModal) {
            // Open search modal
            searchToggle.addEventListener('click', () => {
                searchModal.classList.add('active');
                searchModal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('modal-open');
                
                // Focus on search input
                const searchInput = searchModal.querySelector('input[type="search"]');
                if (searchInput) {
                    setTimeout(() => searchInput.focus(), 100);
                }
            });

            // Close modal on close button or backdrop click
            searchModal.addEventListener('click', (e) => {
                if (e.target === searchModal || e.target.closest('.search-modal-close')) {
                    this.closeSearchModal(searchModal);
                }
            });

            // Close on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && searchModal.classList.contains('active')) {
                    this.closeSearchModal(searchModal);
                }
            });
        }
    }

    closeSearchModal(modal) {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    }

    // ===================================
    // SCROLL ANIMATIONS
    // ===================================
    initAnimations() {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        // Unobserve after animation to improve performance
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            // Observe elements with animation classes
            document.querySelectorAll('.fade-in, .slide-up, .scale-in').forEach(el => {
                observer.observe(el);
            });
        }
    }

    // ===================================
    // TESTIMONIAL SLIDER
    // ===================================
    initSliders() {
        const testimonialSlider = document.querySelector('.testimonial-slider');
        if (testimonialSlider) {
            this.initTestimonialSlider(testimonialSlider);
        }
    }

    initTestimonialSlider(slider) {
        const slides = slider.querySelectorAll('.testimonial-slide');
        const dotsContainer = slider.querySelector('.slider-dots');
        
        if (!slides.length || !dotsContainer) return;
        
        let currentSlide = 0;
        let slideInterval;

        // Create dots
        slides.forEach((_, index) => {
            const dot = document.createElement('button');
            dot.className = `slider-dot ${index === 0 ? 'active' : ''}`;
            dot.setAttribute('aria-label', `Go to slide ${index + 1}`);
            dot.addEventListener('click', () => this.goToSlide(index, slides, dotsContainer));
            dotsContainer.appendChild(dot);
        });

        const dots = dotsContainer.querySelectorAll('.slider-dot');

        // Auto-rotate slides
        const startSlider = () => {
            slideInterval = setInterval(() => {
                const nextSlide = (currentSlide + 1) % slides.length;
                this.goToSlide(nextSlide, slides, dotsContainer);
            }, 5000);
        };

        this.goToSlide = (index, slideElements, dotsElement) => {
            const allSlides = slideElements || slides;
            const allDots = dotsElement.querySelectorAll('.slider-dot');
            
            allSlides[currentSlide].classList.remove('active');
            allDots[currentSlide].classList.remove('active');
            
            currentSlide = index;
            
            allSlides[currentSlide].classList.add('active');
            allDots[currentSlide].classList.add('active');
        };

        // Pause on hover
        slider.addEventListener('mouseenter', () => clearInterval(slideInterval));
        slider.addEventListener('mouseleave', () => startSlider());

        // Keyboard navigation
        slider.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                const prevSlide = currentSlide === 0 ? slides.length - 1 : currentSlide - 1;
                this.goToSlide(prevSlide, slides, dotsContainer);
            } else if (e.key === 'ArrowRight') {
                const nextSlide = (currentSlide + 1) % slides.length;
                this.goToSlide(nextSlide, slides, dotsContainer);
            }
        });

        startSlider();
    }

    // ===================================
    // CART FUNCTIONALITY
    // ===================================
    initCart() {
        // Listen for cart updates (WooCommerce)
        document.body.addEventListener('added_to_cart', () => {
            this.updateCartCount();
            this.showAddedToCartMessage();
        });

        // Initial cart count update
        this.updateCartCount();
    }

    updateCartCount() {
        if (typeof wc_cart_fragments_params === 'undefined') return;

        fetch(wc_cart_fragments_params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_refreshed_fragments'), {
            method: 'GET',
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.fragments) {
                // Update all cart counts on the page
                const cartCounts = document.querySelectorAll('.cart-count');
                if (cartCounts.length && data.fragments['.cart-count']) {
                    cartCounts.forEach(count => {
                        count.textContent = data.fragments['.cart-count'];
                    });
                }
            }
        })
        .catch(error => console.error('Error updating cart:', error));
    }

    showAddedToCartMessage() {
        // Create and show success message
        const message = document.createElement('div');
        message.className = 'added-to-cart-message';
        message.setAttribute('role', 'alert');
        message.setAttribute('aria-live', 'polite');
        message.innerHTML = `
            <div class="message-content">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span>Product added to cart successfully!</span>
            </div>
        `;
        
        document.body.appendChild(message);
        
        setTimeout(() => {
            message.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            message.classList.remove('show');
            setTimeout(() => {
                if (message.parentNode) {
                    message.parentNode.removeChild(message);
                }
            }, 300);
        }, 3000);
    }

    // ===================================
    // WOOCOMMERCE ENHANCEMENTS
    // ===================================
    initWooCommerce() {
        // Quick view functionality
        this.initQuickView();
        
        // Product image zoom on single product pages
        this.initProductGallery();
        
        // Quantity buttons
        this.initQuantityButtons();
    }

    initQuickView() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.quick-view-btn')) {
                e.preventDefault();
                const productId = e.target.closest('.quick-view-btn').dataset.productId;
                this.openQuickView(productId);
            }
        });
    }

    openQuickView(productId) {
        // Implement quick view modal with product details
        if (typeof mr_ajax === 'undefined') return;

        fetch(`${mr_ajax.url}?action=mr_quick_view&product_id=${productId}&nonce=${mr_ajax.nonce}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showQuickViewModal(data.data);
                }
            })
            .catch(error => console.error('Quick view error:', error));
    }

    showQuickViewModal(content) {
        // Create and show quick view modal
        const modal = document.createElement('div');
        modal.className = 'quick-view-modal';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.innerHTML = content;
        
        document.body.appendChild(modal);
        document.body.classList.add('modal-open');
        
        // Close functionality
        modal.addEventListener('click', (e) => {
            if (e.target === modal || e.target.closest('.quick-view-close')) {
                modal.remove();
                document.body.classList.remove('modal-open');
            }
        });

        // Close on escape
        const closeOnEscape = (e) => {
            if (e.key === 'Escape') {
                modal.remove();
                document.body.classList.remove('modal-open');
                document.removeEventListener('keydown', closeOnEscape);
            }
        };
        document.addEventListener('keydown', closeOnEscape);
        
        // Add to cart in quick view
        const addToCartBtn = modal.querySelector('.single_add_to_cart_button');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.addToCartFromQuickView(modal);
            });
        }
    }

    addToCartFromQuickView(modal) {
        const form = modal.querySelector('form.cart');
        if (!form) return;

        const formData = new FormData(form);
        
        if (typeof mr_ajax === 'undefined') return;

        fetch(`${mr_ajax.url}?action=wc_ajax_add_to_cart&nonce=${mr_ajax.nonce}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error && data.product_url) {
                window.location.href = data.product_url;
                return;
            }
            
            if (data.fragments) {
                // Update cart fragments
                this.updateCartFragments(data.fragments);
                this.showAddedToCartMessage();
                
                // Close quick view after short delay
                setTimeout(() => {
                    modal.remove();
                    document.body.classList.remove('modal-open');
                }, 1500);
            }
        })
        .catch(error => console.error('Add to cart error:', error));
    }

    updateCartFragments(fragments) {
        Object.keys(fragments).forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(element => {
                if (element) {
                    element.innerHTML = fragments[selector];
                }
            });
        });
    }

    initProductGallery() {
        const gallery = document.querySelector('.woocommerce-product-gallery');
        if (!gallery) return;

        const mainImage = gallery.querySelector('.wp-post-image');
        const thumbnails = gallery.querySelectorAll('.woocommerce-product-gallery__image');

        if (thumbnails.length > 1) {
            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', (e) => {
                    e.preventDefault();
                    const newSrc = thumb.querySelector('img').dataset.large || thumb.querySelector('img').src;
                    if (mainImage) {
                        mainImage.src = newSrc;
                    }
                    
                    // Update active thumbnail
                    thumbnails.forEach(t => t.classList.remove('active'));
                    thumb.classList.add('active');
                });
            });
        }
    }

    initQuantityButtons() {
        // Add plus/minus buttons to quantity inputs
        document.querySelectorAll('input[type="number"].qty').forEach(input => {
            if (input.closest('.quantity-wrapper')) return; // Already initialized
            
            const wrapper = document.createElement('div');
            wrapper.className = 'quantity-wrapper';
            
            const minusBtn = document.createElement('button');
            minusBtn.type = 'button';
            minusBtn.className = 'quantity-btn minus';
            minusBtn.innerHTML = '−';
            minusBtn.setAttribute('aria-label', 'Decrease quantity');
            
            const plusBtn = document.createElement('button');
            plusBtn.type = 'button';
            plusBtn.className = 'quantity-btn plus';
            plusBtn.innerHTML = '+';
            plusBtn.setAttribute('aria-label', 'Increase quantity');
            
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(minusBtn);
            wrapper.appendChild(input);
            wrapper.appendChild(plusBtn);
            
            // Button functionality
            minusBtn.addEventListener('click', () => {
                const min = parseInt(input.min) || 1;
                const current = parseInt(input.value) || 1;
                if (current > min) {
                    input.value = current - 1;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
            
            plusBtn.addEventListener('click', () => {
                const max = parseInt(input.max) || Infinity;
                const current = parseInt(input.value) || 0;
                if (current < max) {
                    input.value = current + 1;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });
    }
}

// Initialize theme when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new MacedonRangesTheme();
});

// Export for potential use in other scripts
window.MacedonRangesTheme = MacedonRangesTheme;

