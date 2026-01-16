/**
 * Authentication Modal
 * Handles login and registration modal functionality
 * 
 * @package AAAPOS
 * @since 1.0.0
 */
class AuthModal {
    constructor() {
        this.modal = null;
        this.backdrop = null;
        this.activeTab = 'login';
        this.hasOpenedBefore = false;
        
        this.init();
    }

    init() {
        this.createModal();
        this.bindEvents();
        this.initPasswordToggles();
    }

    createModal() {
        // Prevent duplicate modals
        if (document.querySelector('.auth-modal')) {
            return;
        }

        // Verify mr_auth object exists
        if (typeof mr_auth === 'undefined') {
            console.error('Auth Modal: mr_auth object is not defined');
            return;
        }

        // Get configuration values
        const hasCustomImage = mr_auth.has_custom_image && 
                              mr_auth.login_image && 
                              mr_auth.login_image.trim() !== '';
        const loginImage = mr_auth.login_image || '';
        const loginSubtitle = mr_auth.login_subtitle || 'Welcome back! Please enter your details';
        const registerSubtitle = mr_auth.register_subtitle || 'Create your account to get started';
        const lostPasswordUrl = mr_auth.lost_password_url || '/wp-login.php?action=lostpassword';

        // Build right panel styles
        const rightPanelClass = hasCustomImage ? 'auth-modal-right' : 'auth-modal-right no-image';
        const rightPanelStyle = hasCustomImage 
            ? `background-image: url('${loginImage}'); background-size: cover; background-position: center; background-repeat: no-repeat;`
            : '';

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
                                    <p class="auth-modal-subtitle" data-login-text="${loginSubtitle}">${loginSubtitle}</p>
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

                                    <a href="${lostPasswordUrl}" class="auth-forgot-link">forgot password ?</a>

                                    <button type="submit" class="auth-submit-btn">Log in</button>

                                    <p class="auth-footer-text">
                                        Don't have account? <a href="#" class="switch-to-register">Sign up</a>
                                    </p>
                                </form>
                            </div>

                            <!-- Register Tab -->
                            <div class="auth-tab-content" data-content="register">
                                <div class="auth-modal-header">
                                    <h2 class="auth-modal-title">Sign Up</h2>
                                    <p class="auth-modal-subtitle" data-register-text="${registerSubtitle}">${registerSubtitle}</p>
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

                                    <p class="auth-footer-text">
                                        Already have an account? <a href="#" class="switch-to-login">Sign in</a>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Image or Gradient -->
                    <div class="${rightPanelClass}" style="${rightPanelStyle}"></div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = document.querySelector('.auth-modal');
        this.backdrop = document.querySelector('.auth-modal-backdrop');
    }

    bindEvents() {
        // Open modal on login/register button clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-login') || e.target.closest('.mobile-account-link')) {
                if (!document.body.classList.contains('logged-in')) {
                    e.preventDefault();
                    this.open();
                }
            }
        });

        // Close modal handlers
        this.backdrop?.addEventListener('click', () => this.close());
        document.querySelector('.auth-modal-close')?.addEventListener('click', () => this.close());

        // Tab switching
        document.querySelectorAll('.auth-tab').forEach(tab => {
            tab.addEventListener('click', () => this.switchTab(tab.dataset.tab));
        });

        // Switch between login/register forms
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

        // Keyboard shortcuts
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

        // Update tab buttons
        document.querySelectorAll('.auth-tab').forEach(t => {
            t.classList.toggle('active', t.dataset.tab === tab);
        });

        // Update tab content
        document.querySelectorAll('.auth-tab-content').forEach(content => {
            content.classList.toggle('active', content.dataset.content === tab);
        });

        // Update title and subtitle
        const activeContent = document.querySelector('.auth-tab-content.active');
        const title = activeContent.querySelector('.auth-modal-title');
        const subtitle = activeContent.querySelector('.auth-modal-subtitle');
        
        if (tab === 'login') {
            title.textContent = 'Log In';
            const loginText = subtitle.getAttribute('data-login-text') || 'Welcome back! Please enter your details';
            subtitle.textContent = loginText;
        } else {
            title.textContent = 'Sign Up';
            const registerText = subtitle.getAttribute('data-register-text') || 'Create your account to get started';
            subtitle.textContent = registerText;
        }

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
            console.error('Login error:', error);
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
            console.error('Registration error:', error);
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
            button.setAttribute('aria-busy', 'true');
        } else {
            button.classList.remove('loading');
            button.disabled = false;
            button.setAttribute('aria-busy', 'false');
        }
    }

    open(tab = 'login') {
        this.switchTab(tab);
        
        // Add first-open animation class
        if (!this.hasOpenedBefore) {
            this.modal?.classList.add('first-open');
            this.hasOpenedBefore = true;
            
            setTimeout(() => {
                this.modal?.classList.remove('first-open');
            }, 1000);
        }
        
        // Show modal
        this.modal?.classList.add('active');
        this.backdrop?.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Focus first input for accessibility
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

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    if (typeof mr_auth !== 'undefined') {
        window.authModal = new AuthModal();
        
        // Handle URL parameter for showing login modal
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('show_login') === '1') {
            window.authModal.open('login');
            // Clean URL without page reload
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    } else {
        console.error('Auth Modal: mr_auth object not found. Check enqueue.php localization.');
    }
});