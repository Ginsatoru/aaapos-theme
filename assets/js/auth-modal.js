/**
 * Authentication Modal - Modern Split-Screen Login/Register
 */
class AuthModal {
    constructor() {
        this.modal = null;
        this.backdrop = null;
        this.activeTab = 'login';
        this.hasOpenedBefore = false; // Track if modal has been opened before
        this.init();
    }

    init() {
        this.createModal();
        this.bindEvents();
        this.initPasswordToggles();
    }

    createModal() {
        // Check if modal already exists
        if (document.querySelector('.auth-modal')) return;

        const modalHTML = `
            <div class="auth-modal-backdrop"></div>
            <div class="auth-modal" role="dialog" aria-modal="true" aria-labelledby="auth-modal-title">
                <div class="auth-modal-split">
                    <!-- Left Side - Form -->
                    <div class="auth-modal-left">
                        <button class="auth-modal-close" aria-label="Close modal">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>

                        <div class="auth-modal-tabs">
                            <button class="auth-tab active" data-tab="login">Login</button>
                            <button class="auth-tab" data-tab="register">Register</button>
                        </div>

                        <div class="auth-modal-body">
                            <!-- Login Tab -->
                            <div class="auth-tab-content active" data-content="login">
                                <div class="auth-modal-header">
                                    <h2 class="auth-modal-title" id="auth-modal-title">Log In</h2>
                                    <p class="auth-modal-subtitle">Welcome back! Please enter your details</p>
                                </div>

                                <form class="auth-form" id="login-form">
                                    <div class="auth-form-group">
                                        <label class="auth-form-label" for="login-username">Email</label>
                                        <input 
                                            type="text" 
                                            id="login-username" 
                                            name="username"
                                            class="auth-form-input" 
                                            placeholder="Enter your email"
                                            required
                                            autocomplete="username"
                                        />
                                    </div>

                                    <div class="auth-form-group">
                                        <label class="auth-form-label" for="login-password">Password</label>
                                        <div class="auth-password-wrapper">
                                            <input 
                                                type="password" 
                                                id="login-password" 
                                                name="password"
                                                class="auth-form-input" 
                                                placeholder="Enter your password"
                                                required
                                                autocomplete="current-password"
                                            />
                                            <button type="button" class="auth-password-toggle" aria-label="Toggle password visibility">
                                                <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                <svg class="eye-closed" style="display:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <a href="${mr_auth.lost_password_url}" class="auth-forgot-link">forgot password ?</a>

                                    <button type="submit" class="auth-submit-btn">Log in</button>

                                    <div class="auth-divider">Or Continue With</div>

                                    <div class="auth-social-buttons">
                                        <button type="button" class="auth-social-btn">
                                            <svg viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                            </svg>
                                            Google
                                        </button>
                                        <button type="button" class="auth-social-btn">
                                            <svg viewBox="0 0 24 24" fill="#1877F2">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                            Facebook
                                        </button>
                                    </div>

                                    <p class="auth-footer-text">
                                        Don't have account? <a href="#" class="switch-to-register">Sign up</a>
                                    </p>
                                </form>
                            </div>

                            <!-- Register Tab -->
                            <div class="auth-tab-content" data-content="register">
                                <div class="auth-modal-header">
                                    <h2 class="auth-modal-title">Sign Up</h2>
                                    <p class="auth-modal-subtitle">Create your account to get started</p>
                                </div>

                                <form class="auth-form" id="register-form">
                                    <div class="auth-form-group">
                                        <label class="auth-form-label" for="register-username">Username</label>
                                        <input 
                                            type="text" 
                                            id="register-username" 
                                            name="username"
                                            class="auth-form-input" 
                                            placeholder="Choose a username"
                                            required
                                            autocomplete="username"
                                        />
                                    </div>

                                    <div class="auth-form-group">
                                        <label class="auth-form-label" for="register-email">Email</label>
                                        <input 
                                            type="email" 
                                            id="register-email" 
                                            name="email"
                                            class="auth-form-input" 
                                            placeholder="Enter your email"
                                            required
                                            autocomplete="email"
                                        />
                                    </div>

                                    <div class="auth-form-group">
                                        <label class="auth-form-label" for="register-password">Password</label>
                                        <div class="auth-password-wrapper">
                                            <input 
                                                type="password" 
                                                id="register-password" 
                                                name="password"
                                                class="auth-form-input" 
                                                placeholder="Create a password"
                                                required
                                                autocomplete="new-password"
                                            />
                                            <button type="button" class="auth-password-toggle" aria-label="Toggle password visibility">
                                                <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                <svg class="eye-closed" style="display:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <button type="submit" class="auth-submit-btn">Create Account</button>

                                    <div class="auth-divider">Or Continue With</div>

                                    <div class="auth-social-buttons">
                                        <button type="button" class="auth-social-btn">
                                            <svg viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                            </svg>
                                            Google
                                        </button>
                                        <button type="button" class="auth-social-btn">
                                            <svg viewBox="0 0 24 24" fill="#1877F2">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                            Facebook
                                        </button>
                                    </div>

                                    <p class="auth-footer-text">
                                        Already have an account? <a href="#" class="switch-to-login">Sign in</a>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Image -->
                    <div class="auth-modal-right" style="background-image: url('${mr_auth.login_image}');"></div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = document.querySelector('.auth-modal');
        this.backdrop = document.querySelector('.auth-modal-backdrop');
    }

    bindEvents() {
        // Open modal when clicking login button
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-login') || e.target.closest('.mobile-account-link')) {
                if (!document.body.classList.contains('logged-in')) {
                    e.preventDefault();
                    this.open();
                }
            }
        });

        // Close modal
        this.backdrop?.addEventListener('click', () => this.close());
        document.querySelector('.auth-modal-close')?.addEventListener('click', () => this.close());

        // Tab switching
        document.querySelectorAll('.auth-tab').forEach(tab => {
            tab.addEventListener('click', () => this.switchTab(tab.dataset.tab));
        });

        // Switch links
        document.querySelector('.switch-to-register')?.addEventListener('click', (e) => {
            e.preventDefault();
            this.switchTab('register');
        });

        document.querySelector('.switch-to-login')?.addEventListener('click', (e) => {
            e.preventDefault();
            this.switchTab('login');
        });

        // Form submissions
        document.getElementById('login-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleLogin(e.target);
        });

        document.getElementById('register-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleRegister(e.target);
        });

        // Escape key to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal?.classList.contains('active')) {
                this.close();
            }
        });
    }

    initPasswordToggles() {
        document.querySelectorAll('.auth-password-toggle').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const eyeOpen = this.querySelector('.eye-open');
                const eyeClosed = this.querySelector('.eye-closed');

                if (input.type === 'password') {
                    input.type = 'text';
                    eyeOpen.style.display = 'none';
                    eyeClosed.style.display = 'block';
                } else {
                    input.type = 'password';
                    eyeOpen.style.display = 'block';
                    eyeClosed.style.display = 'none';
                }
            });
        });
    }

    switchTab(tab) {
        this.activeTab = tab;

        // Update tabs
        document.querySelectorAll('.auth-tab').forEach(t => {
            t.classList.toggle('active', t.dataset.tab === tab);
        });

        // Update content
        document.querySelectorAll('.auth-tab-content').forEach(content => {
            content.classList.toggle('active', content.dataset.content === tab);
        });

        // Update title
        const title = this.modal.querySelector('.auth-modal-title');
        const subtitle = this.modal.querySelector('.auth-modal-subtitle');
        
        if (tab === 'login') {
            title.textContent = 'Log In';
            subtitle.textContent = 'Welcome back! Please enter your details';
        } else {
            title.textContent = 'Sign Up';
            subtitle.textContent = 'Create your account to get started';
        }

        // Clear any error messages
        this.clearMessages();
    }

    async handleLogin(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('.auth-submit-btn');
        
        this.setLoading(submitBtn, true);
        this.clearMessages();

        try {
            const response = await fetch(mr_auth.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'mr_ajax_login',
                    username: formData.get('username'),
                    password: formData.get('password'),
                    rememberme: formData.get('rememberme') || '',
                    nonce: mr_auth.nonce
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showMessage('success', data.data.message);
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                this.showMessage('error', data.data.message);
            }
        } catch (error) {
            this.showMessage('error', 'An error occurred. Please try again.');
        } finally {
            this.setLoading(submitBtn, false);
        }
    }

    async handleRegister(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('.auth-submit-btn');
        
        this.setLoading(submitBtn, true);
        this.clearMessages();

        try {
            const response = await fetch(mr_auth.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'mr_ajax_register',
                    username: formData.get('username'),
                    email: formData.get('email'),
                    password: formData.get('password'),
                    nonce: mr_auth.nonce
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showMessage('success', data.data.message);
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                this.showMessage('error', data.data.message);
            }
        } catch (error) {
            this.showMessage('error', 'An error occurred. Please try again.');
        } finally {
            this.setLoading(submitBtn, false);
        }
    }

    showMessage(type, message) {
        const activeContent = document.querySelector('.auth-tab-content.active');
        const existingMessage = activeContent.querySelector('.auth-error-message, .auth-success-message');
        
        if (existingMessage) {
            existingMessage.remove();
        }

        const messageHTML = `
            <div class="auth-${type}-message">
                ${message}
            </div>
        `;

        const header = activeContent.querySelector('.auth-modal-header');
        header.insertAdjacentHTML('afterend', messageHTML);
    }

    clearMessages() {
        document.querySelectorAll('.auth-error-message, .auth-success-message').forEach(msg => {
            msg.remove();
        });
    }

    setLoading(button, isLoading) {
        if (isLoading) {
            button.classList.add('loading');
            button.disabled = true;
        } else {
            button.classList.remove('loading');
            button.disabled = false;
        }
    }

    open(tab = 'login') {
        this.switchTab(tab);
        
        // Add 'first-open' class ONLY if this is the first time opening
        if (!this.hasOpenedBefore) {
            this.modal?.classList.add('first-open');
            this.hasOpenedBefore = true; // Mark that modal has been opened
            
            // Remove 'first-open' class after animations complete (1 second)
            setTimeout(() => {
                this.modal?.classList.remove('first-open');
            }, 1000);
        }
        
        this.modal?.classList.add('active');
        this.backdrop?.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Focus first input
        setTimeout(() => {
            const firstInput = this.modal?.querySelector('.auth-tab-content.active input');
            firstInput?.focus();
        }, 300);
    }

    close() {
        this.modal?.classList.remove('active');
        this.backdrop?.classList.remove('active');
        document.body.style.overflow = '';
        this.clearMessages();
    }
}

// Initialize authentication modal
document.addEventListener('DOMContentLoaded', () => {
    window.authModal = new AuthModal();
    
    // Check if we should open the modal automatically (from redirect)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('show_login') === '1') {
        window.authModal.open('login');
        // Clean up URL without page reload
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});