/**
 * Authentication Modal
 * Handles login, registration, and forgot password modal functionality
 *
 * @package AAAPOS
 * @since 1.0.4
 */
class AuthModal {
    constructor() {
        this.modal = null;
        this.backdrop = null;
        this.activeTab = 'login';
        this.activeView = 'login'; // 'login' | 'register' | 'forgot' | 'reset'
        this.hasOpenedBefore = false;

        this.init();
    }

    init() {
        this.createModal();
        this.bindEvents();
        this.initPasswordToggles();
        this.initPasswordStrength();
    }

    createModal() {
        if (document.querySelector('.auth-modal')) return;

        if (typeof mr_auth === 'undefined') {
            console.error('Auth Modal: mr_auth object is not defined');
            return;
        }

        const hasCustomImage   = mr_auth.has_custom_image && mr_auth.login_image && mr_auth.login_image.trim() !== '';
        const loginImage       = mr_auth.login_image || '';
        const loginSubtitle    = mr_auth.login_subtitle   || 'Welcome back! Please enter your details';
        const registerSubtitle = mr_auth.register_subtitle || 'Create your account to get started';

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

                            <!-- LOGIN VIEW -->
                            <div class="auth-tab-content active" data-content="login">
                                <div class="auth-modal-header">
                                    <h2 class="auth-modal-title" id="auth-modal-title">Log In</h2>
                                    <p class="auth-modal-subtitle" data-login-text="${loginSubtitle}">${loginSubtitle}</p>
                                </div>
                                <form class="auth-form" id="login-form" novalidate>
                                    <div class="auth-form-group">
                                        <label class="auth-form-label" for="login-username">Email or Username</label>
                                        <input type="text" id="login-username" name="username" class="auth-form-input" placeholder="Enter your email" required autocomplete="username" />
                                    </div>
                                    <div class="auth-form-group">
                                        <label class="auth-form-label" for="login-password">Password</label>
                                        <div class="auth-password-wrapper">
                                            <input type="password" id="login-password" name="password" class="auth-form-input" placeholder="Enter your password" required autocomplete="current-password" />
                                            <button type="button" class="auth-password-toggle" aria-label="Toggle password visibility">
                                                <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                <svg class="eye-closed" style="display:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" class="auth-forgot-link" id="open-forgot-password">forgot password?</button>
                                    <button type="submit" class="auth-submit-btn">Log in</button>
                                    <p class="auth-footer-text">Don't have an account? <a href="#" class="switch-to-register">Sign up</a></p>
                                </form>
                            </div>

                            <!-- REGISTER VIEW -->
                            <div class="auth-tab-content" data-content="register">
                                <div class="auth-modal-header">
                                    <h2 class="auth-modal-title">Sign Up</h2>
                                    <p class="auth-modal-subtitle" data-register-text="${registerSubtitle}">${registerSubtitle}</p>
                                </div>
                                <form class="auth-form" id="register-form" novalidate>
                                    <div class="auth-form-group">
                                        <label class="auth-form-label" for="register-username">Username</label>
                                        <input type="text" id="register-username" name="username" class="auth-form-input" placeholder="Choose a username" required autocomplete="username" />
                                    </div>
                                    <div class="auth-form-group">
                                        <label class="auth-form-label" for="register-email">Email</label>
                                        <input type="email" id="register-email" name="email" class="auth-form-input" placeholder="Enter your email" required autocomplete="email" />
                                    </div>
                                    <div class="auth-form-group">
                                        <label class="auth-form-label" for="register-password">Password</label>
                                        <div class="auth-password-wrapper">
                                            <input type="password" id="register-password" name="password" class="auth-form-input" placeholder="Create a password" required autocomplete="new-password" />
                                            <button type="button" class="auth-password-toggle" aria-label="Toggle password visibility">
                                                <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                <svg class="eye-closed" style="display:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                            </button>
                                        </div>
                                        <div class="auth-password-strength" id="register-password-strength">
                                            <div class="auth-strength-bars">
                                                <span class="auth-strength-bar"></span>
                                                <span class="auth-strength-bar"></span>
                                                <span class="auth-strength-bar"></span>
                                                <span class="auth-strength-bar"></span>
                                            </div>
                                            <span class="auth-strength-label"></span>
                                        </div>
                                        <ul class="auth-password-rules" id="register-password-rules">
                                            <li data-rule="length"><span class="rule-icon"><span class="rule-icon-default">○</span><span class="rule-icon-pass">✓</span><span class="rule-icon-fail">✕</span></span>At least 8 characters</li>
                                            <li data-rule="upper"><span class="rule-icon"><span class="rule-icon-default">○</span><span class="rule-icon-pass">✓</span><span class="rule-icon-fail">✕</span></span>One uppercase letter</li>
                                            <li data-rule="lower"><span class="rule-icon"><span class="rule-icon-default">○</span><span class="rule-icon-pass">✓</span><span class="rule-icon-fail">✕</span></span>One lowercase letter</li>
                                            <li data-rule="number"><span class="rule-icon"><span class="rule-icon-default">○</span><span class="rule-icon-pass">✓</span><span class="rule-icon-fail">✕</span></span>One number</li>
                                            <li data-rule="special"><span class="rule-icon"><span class="rule-icon-default">○</span><span class="rule-icon-pass">✓</span><span class="rule-icon-fail">✕</span></span>One special character (!@#$%^&*)</li>
                                        </ul>
                                    </div>
                                    <button type="submit" class="auth-submit-btn">Create Account</button>
                                    <p class="auth-footer-text">Already have an account? <a href="#" class="switch-to-login">Sign in</a></p>
                                </form>
                            </div>

                            <!-- FORGOT PASSWORD VIEW -->
                            <div class="auth-tab-content" data-content="forgot">
                                <div class="auth-modal-header">
                                    <h2 class="auth-modal-title">Reset Password</h2>
                                    <p class="auth-modal-subtitle">Enter your email and we'll send you a reset link</p>
                                </div>
                                <form class="auth-form" id="forgot-form" novalidate>
                                    <div class="auth-form-group">
                                        <label class="auth-form-label" for="forgot-email">Email or Username</label>
                                        <input type="text" id="forgot-email" name="email_or_username" class="auth-form-input" placeholder="Enter your email or username" required autocomplete="email" />
                                    </div>
                                    <button type="submit" class="auth-submit-btn">Send Reset Link</button>
                                    <p class="auth-footer-text">
                                        <a href="#" class="back-to-login">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" style="vertical-align:middle;margin-right:4px;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                                            Back to login
                                        </a>
                                    </p>
                                </form>
                            </div>

                            <!-- RESET PASSWORD VIEW (arrived via email link) -->
                            <div class="auth-tab-content" data-content="reset">
                                <div class="auth-modal-header">
                                    <h2 class="auth-modal-title">New Password</h2>
                                    <p class="auth-modal-subtitle">Enter a new password for your account</p>
                                </div>
                                <form class="auth-form" id="reset-form" novalidate>
                                    <input type="hidden" id="reset-key" name="key" value="" />
                                    <input type="hidden" id="reset-login" name="login" value="" />
                                    <div class="auth-form-group">
                                        <label class="auth-form-label" for="reset-password">New Password</label>
                                        <div class="auth-password-wrapper">
                                            <input type="password" id="reset-password" name="password" class="auth-form-input" placeholder="Create a strong password" required autocomplete="new-password" />
                                            <button type="button" class="auth-password-toggle" aria-label="Toggle password visibility">
                                                <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                <svg class="eye-closed" style="display:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                            </button>
                                        </div>
                                        <div class="auth-password-strength" id="reset-password-strength">
                                            <div class="auth-strength-bars">
                                                <span class="auth-strength-bar"></span>
                                                <span class="auth-strength-bar"></span>
                                                <span class="auth-strength-bar"></span>
                                                <span class="auth-strength-bar"></span>
                                            </div>
                                            <span class="auth-strength-label"></span>
                                        </div>
                                        <ul class="auth-password-rules" id="reset-password-rules">
                                            <li data-rule="length"><span class="rule-icon"><span class="rule-icon-default">○</span><span class="rule-icon-pass">✓</span><span class="rule-icon-fail">✕</span></span>At least 8 characters</li>
                                            <li data-rule="upper"><span class="rule-icon"><span class="rule-icon-default">○</span><span class="rule-icon-pass">✓</span><span class="rule-icon-fail">✕</span></span>One uppercase letter</li>
                                            <li data-rule="lower"><span class="rule-icon"><span class="rule-icon-default">○</span><span class="rule-icon-pass">✓</span><span class="rule-icon-fail">✕</span></span>One lowercase letter</li>
                                            <li data-rule="number"><span class="rule-icon"><span class="rule-icon-default">○</span><span class="rule-icon-pass">✓</span><span class="rule-icon-fail">✕</span></span>One number</li>
                                            <li data-rule="special"><span class="rule-icon"><span class="rule-icon-default">○</span><span class="rule-icon-pass">✓</span><span class="rule-icon-fail">✕</span></span>One special character (!@#$%^&*)</li>
                                        </ul>
                                    </div>
                                    <div class="auth-form-group">
                                        <label class="auth-form-label" for="reset-password2">Confirm New Password</label>
                                        <div class="auth-password-wrapper">
                                            <input type="password" id="reset-password2" name="password2" class="auth-form-input" placeholder="Confirm new password" required autocomplete="new-password" />
                                            <button type="button" class="auth-password-toggle" aria-label="Toggle password visibility">
                                                <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                <svg class="eye-closed" style="display:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="submit" class="auth-submit-btn">Save New Password</button>
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
        this.modal    = document.querySelector('.auth-modal');
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

        // Close modal
        this.backdrop?.addEventListener('click', () => this.close());
        document.querySelector('.auth-modal-close')?.addEventListener('click', () => this.close());

        // Tab switching (Login / Register tabs)
        document.querySelectorAll('.auth-tab').forEach(tab => {
            tab.addEventListener('click', () => this.switchTab(tab.dataset.tab));
        });

        // Switch links inside forms
        document.querySelector('.switch-to-register')?.addEventListener('click', (e) => {
            e.preventDefault();
            this.switchTab('register');
        });

        document.querySelector('.switch-to-login')?.addEventListener('click', (e) => {
            e.preventDefault();
            this.switchTab('login');
        });

        // Forgot password - open forgot view
        document.getElementById('open-forgot-password')?.addEventListener('click', (e) => {
            e.preventDefault();
            this.showForgotView();
        });

        // Back to login from forgot view
        document.querySelector('.back-to-login')?.addEventListener('click', (e) => {
            e.preventDefault();
            this.showLoginView();
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

        document.getElementById('forgot-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleForgotPassword(e.target);
        });

        document.getElementById('reset-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleResetPassword(e.target);
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
            toggle.addEventListener('click', function () {
                const input     = this.previousElementSibling;
                const eyeOpen   = this.querySelector('.eye-open');
                const eyeClosed = this.querySelector('.eye-closed');

                if (input.type === 'password') {
                    input.type = 'text';
                    eyeOpen.style.display   = 'none';
                    eyeClosed.style.display = 'block';
                } else {
                    input.type = 'password';
                    eyeOpen.style.display   = 'block';
                    eyeClosed.style.display = 'none';
                }
            });
        });
    }

    // -------------------------------------------------------------------------
    // Password Strength
    // -------------------------------------------------------------------------

    /**
     * Evaluate password strength.
     * Returns { score: 0-4, checks: { length, upper, lower, number, special } }
     */
    evaluatePassword(password) {
        const checks = {
            length:  password.length >= 8,
            upper:   /[A-Z]/.test(password),
            lower:   /[a-z]/.test(password),
            number:  /[0-9]/.test(password),
            special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password),
        };
        const score = Object.values(checks).filter(Boolean).length;
        return { score, checks };
    }

    /**
     * Returns true only when all rules pass (score === 5).
     */
    isPasswordStrong(password) {
        return this.evaluatePassword(password).score === 5;
    }

    initPasswordStrength() {
        // Register form password
        const registerInput   = document.getElementById('register-password');
        const registerStrength = document.getElementById('register-password-strength');
        const registerRules    = document.getElementById('register-password-rules');

        if (registerInput) {
            registerInput.addEventListener('input', () => {
                this.updateStrengthUI(registerInput.value, registerStrength, registerRules);
            });
        }

        // Reset form password
        const resetInput    = document.getElementById('reset-password');
        const resetStrength = document.getElementById('reset-password-strength');
        const resetRules    = document.getElementById('reset-password-rules');

        if (resetInput) {
            resetInput.addEventListener('input', () => {
                this.updateStrengthUI(resetInput.value, resetStrength, resetRules);
            });
        }
    }

    updateStrengthUI(password, strengthEl, rulesEl) {
        if (!strengthEl || !rulesEl) return;

        const { score, checks } = this.evaluatePassword(password);
        const hasInput = password.length > 0;

        // Fade the whole block in/out via CSS class (no display toggling)
        strengthEl.classList.toggle('visible', hasInput);
        rulesEl.classList.toggle('visible', hasInput);

        if (!hasInput) {
            // Reset bars and label when field is cleared
            strengthEl.querySelectorAll('.auth-strength-bar').forEach(bar => {
                bar.style.backgroundColor = '#e5e7eb';
            });
            const labelEl = strengthEl.querySelector('.auth-strength-label');
            if (labelEl) { labelEl.textContent = ''; labelEl.style.color = ''; }
            rulesEl.querySelectorAll('li[data-rule]').forEach(li => {
                li.classList.remove('passed', 'failed');
            });
            return;
        }

        // Strength levels — score range is 0–5, one entry per level
        const levels = [
            { label: 'Very weak', color: '#ef4444' },
            { label: 'Very weak', color: '#ef4444' },
            { label: 'Weak',      color: '#f97316' },
            { label: 'Fair',      color: '#eab308' },
            { label: 'Good',      color: '#3b82f6' },
            { label: 'Strong',    color: '#22c55e' },
        ];

        const level = levels[Math.min(score, levels.length - 1)];

        // Update bars — colour transition handled by CSS
        strengthEl.querySelectorAll('.auth-strength-bar').forEach((bar, i) => {
            bar.style.backgroundColor = i < score ? level.color : '#e5e7eb';
        });

        // Update label
        const labelEl = strengthEl.querySelector('.auth-strength-label');
        if (labelEl) {
            labelEl.textContent = level.label;
            labelEl.style.color = level.color;
        }

        // Mark rules — only show failed state once user has started typing
        rulesEl.querySelectorAll('li[data-rule]').forEach(li => {
            const passed = !!checks[li.dataset.rule];
            li.classList.toggle('passed', passed);
            li.classList.toggle('failed', !passed && password.length >= 1);
        });
    }

    // -------------------------------------------------------------------------
    // View switching
    // -------------------------------------------------------------------------

    switchTab(tab) {
        this.activeTab = tab;

        document.querySelectorAll('.auth-tab').forEach(t => {
            t.classList.toggle('active', t.dataset.tab === tab);
        });

        this.showView(tab);
    }

    showView(view) {
        this.activeView = view;

        document.querySelectorAll('.auth-tab-content').forEach(content => {
            content.classList.toggle('active', content.dataset.content === view);
        });

        // Hide tabs on forgot/reset views
        const tabsEl = document.querySelector('.auth-modal-tabs');
        if (tabsEl) {
            tabsEl.style.display = (view === 'forgot' || view === 'reset') ? 'none' : '';
        }

        this.clearMessages();
    }

    showForgotView() {
        this.showView('forgot');
        setTimeout(() => document.getElementById('forgot-email')?.focus(), 100);
    }

    showLoginView() {
        this.switchTab('login');
    }

    showResetView(key, login) {
        const keyField   = document.getElementById('reset-key');
        const loginField = document.getElementById('reset-login');
        if (keyField)   keyField.value   = key;
        if (loginField) loginField.value = login;
        this.showView('reset');
    }

    // -------------------------------------------------------------------------
    // AJAX Handlers
    // -------------------------------------------------------------------------

    async handleLogin(form) {
        const formData  = new FormData(form);
        const submitBtn = form.querySelector('.auth-submit-btn');

        this.setLoading(submitBtn, true);
        this.clearMessages();

        try {
            const response = await fetch(mr_auth.ajax_url, {
                method:  'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body:    new URLSearchParams({
                    action:     'mr_ajax_login',
                    username:   formData.get('username'),
                    password:   formData.get('password'),
                    rememberme: formData.get('rememberme') || '',
                    nonce:      mr_auth.nonce
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showMessage('success', data.data.message);
                setTimeout(() => window.location.reload(), 1000);
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
        const formData  = new FormData(form);
        const password  = formData.get('password');
        const submitBtn = form.querySelector('.auth-submit-btn');

        this.clearMessages();

        // Client-side strong password check before sending to server
        if (!this.isPasswordStrong(password)) {
            this.showMessage('error', 'Please choose a stronger password. It must be at least 8 characters and include uppercase, lowercase, a number, and a special character.');
            return;
        }

        this.setLoading(submitBtn, true);

        try {
            const response = await fetch(mr_auth.ajax_url, {
                method:  'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body:    new URLSearchParams({
                    action:   'mr_ajax_register',
                    username: formData.get('username'),
                    email:    formData.get('email'),
                    password: password,
                    nonce:    mr_auth.nonce
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showMessage('success', data.data.message);
                setTimeout(() => window.location.reload(), 1500);
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

    async handleResetPassword(form) {
        const formData  = new FormData(form);
        const password  = formData.get('password');
        const password2 = formData.get('password2');
        const submitBtn = form.querySelector('.auth-submit-btn');

        this.clearMessages();

        // Client-side strong password check
        if (!this.isPasswordStrong(password)) {
            this.showMessage('error', 'Please choose a stronger password. It must be at least 8 characters and include uppercase, lowercase, a number, and a special character.');
            return;
        }

        if (password !== password2) {
            this.showMessage('error', 'Passwords do not match. Please try again.');
            return;
        }

        this.setLoading(submitBtn, true);

        try {
            const response = await fetch(mr_auth.ajax_url, {
                method:  'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body:    new URLSearchParams({
                    action:    'mr_ajax_reset_password',
                    key:       formData.get('key'),
                    login:     formData.get('login'),
                    password:  password,
                    password2: password2,
                    nonce:     mr_auth.nonce
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showMessage('success', data.data.message);
                window.history.replaceState({}, document.title, window.location.pathname);
                setTimeout(() => this.showLoginView(), 2500);
            } else {
                this.showMessage('error', data.data.message);
            }
        } catch (error) {
            console.error('Reset password error:', error);
            this.showMessage('error', 'An error occurred. Please try again.');
        } finally {
            this.setLoading(submitBtn, false);
        }
    }

    async handleForgotPassword(form) {
        const formData  = new FormData(form);
        const submitBtn = form.querySelector('.auth-submit-btn');

        this.setLoading(submitBtn, true);
        this.clearMessages();

        try {
            const response = await fetch(mr_auth.ajax_url, {
                method:  'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body:    new URLSearchParams({
                    action:            'mr_ajax_forgot_password',
                    email_or_username: formData.get('email_or_username'),
                    nonce:             mr_auth.nonce
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showMessage('success', data.data.message);
                form.querySelector('#forgot-email').value = '';
            } else {
                this.showMessage('error', data.data.message);
            }
        } catch (error) {
            console.error('Forgot password error:', error);
            this.showMessage('error', 'An error occurred. Please try again.');
        } finally {
            this.setLoading(submitBtn, false);
        }
    }

    // -------------------------------------------------------------------------
    // UI Helpers
    // -------------------------------------------------------------------------

    showMessage(type, message) {
        const activeContent = document.querySelector('.auth-tab-content.active');
        const existing      = activeContent?.querySelector('.auth-error-message, .auth-success-message');
        if (existing) existing.remove();

        const msgHTML = `<div class="auth-${type}-message">${message}</div>`;
        const header  = activeContent?.querySelector('.auth-modal-header');
        header?.insertAdjacentHTML('afterend', msgHTML);
    }

    clearMessages() {
        document.querySelectorAll('.auth-error-message, .auth-success-message').forEach(m => m.remove());
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

        if (!this.hasOpenedBefore) {
            this.modal?.classList.add('first-open');
            this.hasOpenedBefore = true;
            setTimeout(() => this.modal?.classList.remove('first-open'), 1000);
        }

        this.modal?.classList.add('active');
        this.backdrop?.classList.add('active');
        document.body.style.overflow = 'hidden';

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

        setTimeout(() => {
            if (!this.modal?.classList.contains('active')) {
                this.showLoginView();
            }
        }, 400);
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    if (typeof mr_auth !== 'undefined') {
        window.authModal = new AuthModal();

        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.get('show_login') === '1') {
            window.authModal.open('login');
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        // Handle reset password link from email (?show_reset=1&key=...&login=...)
        if (urlParams.get('show_reset') === '1') {
            const key   = urlParams.get('key')   || '';
            const login = urlParams.get('login')  || '';
            if (key && login) {
                window.authModal.open('login');
                window.authModal.showResetView(key, login);
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }
    } else {
        console.error('Auth Modal: mr_auth object not found. Check enqueue.php localization.');
    }
});