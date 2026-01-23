<?php
/**
 * Theme Setup Wizard
 * File: inc/setup-wizard.php
 * 
 * @package AAAPOS
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AAAPOS_Setup_Wizard {
    
    private $step = '';
    private $steps = array();
    private $theme_slug = 'aaapos';
    
    public function __construct() {
        // Check if setup should run
        if (get_option('aaapos_setup_complete')) {
            return;
        }
        
        add_action('admin_menu', array($this, 'admin_menus'));
        add_action('admin_init', array($this, 'redirect_to_setup'), 30);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // AJAX handlers
        add_action('wp_ajax_aaapos_setup_install_demo', array($this, 'ajax_install_demo'));
        add_action('wp_ajax_aaapos_setup_configure_pages', array($this, 'ajax_configure_pages'));
        add_action('wp_ajax_aaapos_setup_save_branding', array($this, 'ajax_save_branding'));
        add_action('wp_ajax_aaapos_setup_complete', array($this, 'ajax_complete_setup'));
    }
    
    /**
     * Redirect to setup wizard on theme activation
     */
    public function redirect_to_setup() {
        // Only redirect if this is theme activation
        if (get_transient('_aaapos_activation_redirect')) {
            delete_transient('_aaapos_activation_redirect');
            
            if (!is_network_admin() && !isset($_GET['activate-multi'])) {
                wp_safe_redirect(admin_url('admin.php?page=aaapos-setup'));
                exit;
            }
        }
    }
    
    /**
     * Add admin menu
     */
    public function admin_menus() {
        add_dashboard_page(
            __('AAAPOS Theme Setup', 'aaapos'),
            __('Theme Setup', 'aaapos'),
            'manage_options',
            'aaapos-setup',
            array($this, 'setup_wizard_page')
        );
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts($hook) {
        if ('dashboard_page_aaapos-setup' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'aaapos-setup-wizard',
            get_template_directory_uri() . '/assets/css/setup-wizard.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'aaapos-setup-wizard',
            get_template_directory_uri() . '/assets/js/setup-wizard.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        wp_localize_script('aaapos-setup-wizard', 'aaaposSetup', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aaapos_setup_nonce'),
            'homeUrl' => home_url(),
            'adminUrl' => admin_url(),
        ));
    }
    
    /**
     * Setup Wizard Page
     */
    public function setup_wizard_page() {
        $this->steps = array(
            'welcome' => array(
                'name' => __('Welcome', 'aaapos'),
                'view' => array($this, 'step_welcome'),
            ),
            'pages' => array(
                'name' => __('Pages', 'aaapos'),
                'view' => array($this, 'step_pages'),
            ),
            'branding' => array(
                'name' => __('Branding', 'aaapos'),
                'view' => array($this, 'step_branding'),
            ),
            'ready' => array(
                'name' => __('Ready', 'aaapos'),
                'view' => array($this, 'step_ready'),
            ),
        );
        
        $this->step = isset($_GET['step']) ? sanitize_key($_GET['step']) : 'welcome';
        
        $this->setup_wizard_header();
        $this->setup_wizard_steps();
        $this->setup_wizard_content();
        $this->setup_wizard_footer();
    }
    
    /**
     * Header
     */
    private function setup_wizard_header() {
        set_current_screen();
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1" />
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title><?php esc_html_e('AAAPOS Theme Setup', 'aaapos'); ?></title>
            <?php do_action('admin_print_styles'); ?>
            <?php do_action('admin_print_scripts'); ?>
        </head>
        <body class="aaapos-setup-wizard">
            <div class="setup-container">
        <?php
    }
    
    /**
     * Steps Progress
     */
    private function setup_wizard_steps() {
        ?>
        <div class="setup-progress">
            <?php
            $step_index = 0;
            $current_index = array_search($this->step, array_keys($this->steps));
            
            foreach ($this->steps as $step_key => $step) :
                $is_active = $this->step === $step_key;
                $is_completed = $current_index > $step_index;
                ?>
                <div class="progress-step <?php echo $is_active ? 'active' : ''; ?> <?php echo $is_completed ? 'completed' : ''; ?>">
                    <div class="step-circle">
                        <?php if ($is_completed) : ?>
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M15 4.5L6.75 12.75L3 9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        <?php else : ?>
                            <span><?php echo $step_index + 1; ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="step-label"><?php echo esc_html($step['name']); ?></span>
                </div>
                <?php if ($step_index < count($this->steps) - 1) : ?>
                    <div class="progress-connector <?php echo $is_completed ? 'completed' : ''; ?>"></div>
                <?php endif; ?>
                <?php $step_index++; ?>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    /**
     * Content
     */
    private function setup_wizard_content() {
        echo '<div class="setup-content">';
        if (!empty($this->steps[$this->step]['view'])) {
            call_user_func($this->steps[$this->step]['view']);
        }
        echo '</div>';
    }
    
    /**
     * Footer
     */
    private function setup_wizard_footer() {
        ?>
            </div>
        </body>
        </html>
        <?php
    }
    
    /**
     * STEP: Welcome
     */
    private function step_welcome() {
        ?>
        <div class="setup-step step-welcome">
            <div class="step-hero">
                <div class="hero-icon">
                    <svg width="72" height="72" viewBox="0 0 72 72" fill="none">
                        <circle cx="36" cy="36" r="35" stroke="currentColor" stroke-width="2"/>
                        <path d="M24 36L32 44L48 28" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h1><?php esc_html_e('Welcome to AAAPOS', 'aaapos'); ?></h1>
                <p class="hero-subtitle">
                    <?php esc_html_e('Let\'s set up your store in just a few steps', 'aaapos'); ?>
                </p>
            </div>
            
            <div class="features-list">
                <div class="feature-item">
                    <div class="feature-icon-wrapper">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <line x1="9" y1="3" x2="9" y2="21"/>
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h3><?php esc_html_e('Essential Pages', 'aaapos'); ?></h3>
                        <p><?php esc_html_e('Automatically create all required WooCommerce pages', 'aaapos'); ?></p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon-wrapper">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <circle cx="12" cy="10" r="3"/>
                            <path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662"/>
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h3><?php esc_html_e('Brand Identity', 'aaapos'); ?></h3>
                        <p><?php esc_html_e('Customize your store name and brand colors', 'aaapos'); ?></p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon-wrapper">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h3><?php esc_html_e('Ready to Launch', 'aaapos'); ?></h3>
                        <p><?php esc_html_e('Your store will be ready to start selling', 'aaapos'); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="setup-actions">
                <a href="<?php echo esc_url(admin_url('admin.php?page=aaapos-setup&step=pages')); ?>" class="btn btn-primary">
                    <?php esc_html_e('Get Started', 'aaapos'); ?>
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <path d="M6.75 13.5L11.25 9L6.75 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <button class="btn btn-text" onclick="if(confirm('<?php esc_html_e('Are you sure? You can run this wizard again later from Appearance menu.', 'aaapos'); ?>')) { location.href='<?php echo esc_url(admin_url()); ?>'; }">
                    <?php esc_html_e('Skip Setup', 'aaapos'); ?>
                </button>
            </div>
        </div>
        <?php
    }
    
    /**
     * STEP: Pages
     */
    private function step_pages() {
        $pages_created = get_option('aaapos_pages_created', false);
        ?>
        <div class="setup-step step-pages">
            <div class="step-header">
                <div class="header-icon">
                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                        <rect x="10" y="8" width="28" height="32" rx="3" stroke="currentColor" stroke-width="2.5"/>
                        <line x1="16" y1="16" x2="32" y2="16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="16" y1="22" x2="32" y2="22" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="16" y1="28" x2="26" y2="28" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <h1><?php esc_html_e('Create Essential Pages', 'aaapos'); ?></h1>
                <p class="step-subtitle">
                    <?php esc_html_e('We\'ll create the pages your store needs to function', 'aaapos'); ?>
                </p>
            </div>
            
            <div class="pages-container" id="pages-list">
                <?php if (!$pages_created) : ?>
                    <div class="page-card">
                        <div class="page-icon">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <rect x="4" y="6" width="24" height="20" rx="2" stroke="currentColor" stroke-width="2"/>
                                <path d="M4 10h24M12 6v4M20 6v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="12" cy="16" r="1.5" fill="currentColor"/>
                                <circle cx="16" cy="16" r="1.5" fill="currentColor"/>
                                <circle cx="20" cy="16" r="1.5" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="page-details">
                            <h3><?php esc_html_e('Homepage', 'aaapos'); ?></h3>
                            <p><?php esc_html_e('Your store\'s front page', 'aaapos'); ?></p>
                        </div>
                        <span class="page-badge pending"><?php esc_html_e('Pending', 'aaapos'); ?></span>
                    </div>
                    <div class="page-card">
                        <div class="page-icon">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <path d="M6 10L4 28h24l-2-18H6z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                <path d="M12 14V8a4 4 0 0 1 8 0v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="18" r="1.5" fill="currentColor"/>
                                <circle cx="20" cy="18" r="1.5" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="page-details">
                            <h3><?php esc_html_e('Shop', 'aaapos'); ?></h3>
                            <p><?php esc_html_e('Product catalog', 'aaapos'); ?></p>
                        </div>
                        <span class="page-badge pending"><?php esc_html_e('Pending', 'aaapos'); ?></span>
                    </div>
                    <div class="page-card">
                        <div class="page-icon">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <circle cx="12" cy="26" r="2" stroke="currentColor" stroke-width="2"/>
                                <circle cx="24" cy="26" r="2" stroke="currentColor" stroke-width="2"/>
                                <path d="M2 4h4l3.68 13.39a2 2 0 0 0 1.92 1.44h12.54a2 2 0 0 0 1.92-1.44L28 8H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="page-details">
                            <h3><?php esc_html_e('Cart', 'aaapos'); ?></h3>
                            <p><?php esc_html_e('Shopping cart', 'aaapos'); ?></p>
                        </div>
                        <span class="page-badge pending"><?php esc_html_e('Pending', 'aaapos'); ?></span>
                    </div>
                    <div class="page-card">
                        <div class="page-icon">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <rect x="6" y="8" width="20" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
                                <path d="M6 12h20M10 8V6M22 8V6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M12 18h8M14 21h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div class="page-details">
                            <h3><?php esc_html_e('Checkout', 'aaapos'); ?></h3>
                            <p><?php esc_html_e('Payment page', 'aaapos'); ?></p>
                        </div>
                        <span class="page-badge pending"><?php esc_html_e('Pending', 'aaapos'); ?></span>
                    </div>
                    <div class="page-card">
                        <div class="page-icon">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <circle cx="16" cy="10" r="4" stroke="currentColor" stroke-width="2"/>
                                <path d="M8 28c0-4.418 3.582-8 8-8s8 3.582 8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M4 16h4M24 16h4M16 4v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div class="page-details">
                            <h3><?php esc_html_e('My Account', 'aaapos'); ?></h3>
                            <p><?php esc_html_e('Customer dashboard', 'aaapos'); ?></p>
                        </div>
                        <span class="page-badge pending"><?php esc_html_e('Pending', 'aaapos'); ?></span>
                    </div>
                <?php else : ?>
                    <div class="success-card">
                        <svg width="56" height="56" viewBox="0 0 56 56" fill="none">
                            <circle cx="28" cy="28" r="26" stroke="currentColor" stroke-width="2.5"/>
                            <path d="M16 28L24 36L40 20" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h3><?php esc_html_e('All pages created!', 'aaapos'); ?></h3>
                        <p><?php esc_html_e('Your essential pages are ready', 'aaapos'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="setup-actions">
                <?php if (!$pages_created) : ?>
                    <button type="button" class="btn btn-primary btn-large" id="create-pages-btn">
                        <span class="btn-text"><?php esc_html_e('Create Pages', 'aaapos'); ?></span>
                        <span class="btn-loader">
                            <svg class="spinner" width="20" height="20" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" opacity="0.25"/>
                                <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </button>
                <?php else : ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=aaapos-setup&step=branding')); ?>" class="btn btn-primary btn-large">
                        <?php esc_html_e('Continue', 'aaapos'); ?>
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                            <path d="M6.75 13.5L11.25 9L6.75 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                <?php endif; ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=aaapos-setup&step=branding')); ?>" class="btn btn-text">
                    <?php esc_html_e('Skip this step', 'aaapos'); ?>
                </a>
            </div>
        </div>
        <?php
    }
    
    /**
 * STEP: Branding
 * Replace the existing step_branding() method with this updated version
 */
private function step_branding() {
    ?>
    <div class="setup-step step-branding">
        <div class="step-header">
            <div class="header-icon">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                    <rect x="8" y="8" width="32" height="32" rx="4" stroke="currentColor" stroke-width="2.5"/>
                    <circle cx="24" cy="20" r="6" stroke="currentColor" stroke-width="2.5"/>
                    <path d="M14 36c0-5.5 4.5-8 10-8s10 2.5 10 8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
            </div>
            <h1><?php esc_html_e('Brand Settings', 'aaapos'); ?></h1>
            <p class="step-subtitle">
                <?php esc_html_e('Customize your store\'s identity', 'aaapos'); ?>
            </p>
        </div>
        
        <!-- Changed from form to div to prevent browser warning -->
        <div id="branding-form" class="branding-form">
            <div class="input-group">
                <label for="site_title"><?php esc_html_e('Store Name', 'aaapos'); ?></label>
                <input type="text" id="site_title" name="site_title" value="<?php echo esc_attr(get_bloginfo('name')); ?>" class="input-field" autocomplete="off">
                <span class="input-hint"><?php esc_html_e('Appears in your header and browser tab', 'aaapos'); ?></span>
            </div>
            
            <div class="input-group">
                <label for="brand_color"><?php esc_html_e('Brand Color', 'aaapos'); ?></label>
                <div class="color-picker-group">
                    <input type="color" id="brand_color" name="brand_color" value="#0f8abe" class="color-swatch" autocomplete="off">
                    <input type="text" id="brand_color_text" value="#0f8abe" class="input-field color-value" autocomplete="off">
                </div>
                <span class="input-hint"><?php esc_html_e('Used for buttons, links, and accents', 'aaapos'); ?></span>
            </div>
            
            <div class="preview-section">
                <h3><?php esc_html_e('Preview', 'aaapos'); ?></h3>
                <div class="preview-window">
                    <div class="preview-toolbar">
                        <div class="preview-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="preview-brand" id="preview-name"><?php echo esc_html(get_bloginfo('name')); ?></div>
                        <div class="preview-btn" id="preview-button">
                            <?php esc_html_e('Shop Now', 'aaapos'); ?>
                        </div>
                        <div class="preview-link" id="preview-link">
                            <?php esc_html_e('View Products', 'aaapos'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="setup-actions">
            <button type="button" class="btn btn-primary btn-large" id="save-branding-btn">
                <span class="btn-text"><?php esc_html_e('Save & Continue', 'aaapos'); ?></span>
                <span class="btn-loader">
                    <svg class="spinner" width="20" height="20" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" opacity="0.25"/>
                        <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round"/>
                    </svg>
                </span>
            </button>
            <a href="<?php echo esc_url(admin_url('admin.php?page=aaapos-setup&step=ready')); ?>" class="btn btn-text">
                <?php esc_html_e('Skip this step', 'aaapos'); ?>
            </a>
        </div>
    </div>
    
    <!-- JavaScript to prevent browser warning -->
    <script>
        // Prevent "Changes you made may not be saved" warning
        window.onbeforeunload = null;
        
        // Also prevent it when clicking any link
        document.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                window.onbeforeunload = null;
            });
        });
    </script>
    <?php
}
    
    /**
     * STEP: Ready
     */
    private function step_ready() {
        ?>
        <div class="setup-step step-ready">
            <div class="step-hero success-hero">
                <div class="hero-icon success">
                    <svg width="72" height="72" viewBox="0 0 72 72" fill="none">
                        <circle cx="36" cy="36" r="35" stroke="currentColor" stroke-width="2"/>
                        <path d="M24 36L32 44L48 28" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h1><?php esc_html_e('You\'re All Set!', 'aaapos'); ?></h1>
                <p class="hero-subtitle">
                    <?php esc_html_e('Your store is ready to launch', 'aaapos'); ?>
                </p>
            </div>
            
            <div class="next-steps-list">
                <div class="next-step-item">
                    <div class="step-num">1</div>
                    <div class="step-info">
                        <h3><?php esc_html_e('Add Products', 'aaapos'); ?></h3>
                        <p><?php esc_html_e('Start adding your products to the store', 'aaapos'); ?></p>
                        <a href="<?php echo esc_url(admin_url('post-new.php?post_type=product')); ?>" class="step-action">
                            <?php esc_html_e('Add Products', 'aaapos'); ?> →
                        </a>
                    </div>
                </div>
                
                <div class="next-step-item">
                    <div class="step-num">2</div>
                    <div class="step-info">
                        <h3><?php esc_html_e('Customize Design', 'aaapos'); ?></h3>
                        <p><?php esc_html_e('Fine-tune your store\'s appearance', 'aaapos'); ?></p>
                        <a href="<?php echo esc_url(admin_url('customize.php')); ?>" class="step-action">
                            <?php esc_html_e('Open Customizer', 'aaapos'); ?> →
                        </a>
                    </div>
                </div>
                
                <div class="next-step-item">
                    <div class="step-num">3</div>
                    <div class="step-info">
                        <h3><?php esc_html_e('Configure Payments', 'aaapos'); ?></h3>
                        <p><?php esc_html_e('Set up payment methods', 'aaapos'); ?></p>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=checkout')); ?>" class="step-action">
                            <?php esc_html_e('Payment Settings', 'aaapos'); ?> →
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="setup-actions">
                <button type="button" class="btn btn-primary btn-large" id="complete-setup-btn">
                    <?php esc_html_e('View My Store', 'aaapos'); ?>
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <path d="M6.75 13.5L11.25 9L6.75 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <a href="<?php echo esc_url(admin_url()); ?>" class="btn btn-text">
                    <?php esc_html_e('Go to Dashboard', 'aaapos'); ?>
                </a>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX: Configure Pages
     */
    public function ajax_configure_pages() {
        check_ajax_referer('aaapos_setup_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'aaapos')));
        }
        
        // Create homepage
        $homepage_id = wp_insert_post(array(
            'post_title' => __('Home', 'aaapos'),
            'post_type' => 'page',
            'post_status' => 'publish',
            'page_template' => 'page-templates/homepage.php',
        ));
        
        if ($homepage_id && !is_wp_error($homepage_id)) {
            update_option('page_on_front', $homepage_id);
            update_option('show_on_front', 'page');
        }
        
        // Let WooCommerce create its pages
        if (class_exists('WC_Install')) {
            WC_Install::create_pages();
        }
        
        update_option('aaapos_pages_created', true);
        
        wp_send_json_success(array(
            'message' => __('Pages created successfully!', 'aaapos'),
        ));
    }
    
    /**
     * AJAX: Save Branding
     */
    public function ajax_save_branding() {
        check_ajax_referer('aaapos_setup_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'aaapos')));
        }
        
        $site_title = isset($_POST['site_title']) ? sanitize_text_field($_POST['site_title']) : '';
        $brand_color = isset($_POST['brand_color']) ? sanitize_hex_color($_POST['brand_color']) : '#0f8abe';
        
        if ($site_title) {
            update_option('blogname', $site_title);
        }
        
        set_theme_mod('brand_color', $brand_color);
        
        wp_send_json_success(array(
            'message' => __('Branding saved successfully!', 'aaapos'),
        ));
    }
    
    /**
     * AJAX: Complete Setup
     */
    public function ajax_complete_setup() {
        check_ajax_referer('aaapos_setup_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'aaapos')));
        }
        
        update_option('aaapos_setup_complete', true);
        
        wp_send_json_success(array(
            'redirect' => home_url(),
        ));
    }
}

// Initialize
new AAAPOS_Setup_Wizard();