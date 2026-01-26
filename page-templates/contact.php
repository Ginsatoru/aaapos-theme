<?php
/**
 * Template Name: Contact Page
 * Template Post Type: page
 * 
 * @package aaapos-prime
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="contact-page-redesign">
    
    <!-- Hero Section -->
    <?php if (get_theme_mod('contact_show_hero', true)) : ?>
    <section class="contact-hero">
        <div class="container">
            <div class="contact-hero__content" data-animate="fade-up">
                <span class="contact-hero__badge"><?php echo esc_html(get_theme_mod('contact_hero_badge', 'Get in Touch')); ?></span>
                <h1 class="contact-hero__title">
                    <?php 
                    $hero_title = get_theme_mod('contact_hero_title', 'Let\'s Start a Conversation');
                    $title_parts = explode(' ', $hero_title);
                    $last_word = array_pop($title_parts);
                    echo esc_html(implode(' ', $title_parts));
                    if (!empty($last_word)) {
                        echo '<br><span class="contact-hero__title-accent">' . esc_html($last_word) . '</span>';
                    }
                    ?>
                </h1>
                <p class="contact-hero__subtitle">
                    <?php echo esc_html(get_theme_mod('contact_hero_subtitle', 'Whether you have a question about products, pricing, or anything else, our team is ready to answer all your questions.')); ?>
                </p>
            </div>
            
            <!-- Quick Stats -->
            <?php if (get_theme_mod('contact_show_quick_stats', true)) : ?>
            <div class="contact-hero__stats">
                <?php if (get_theme_mod('contact_show_stat1', true)) : ?>
                <div class="quick-stat" data-animate="fade-up" data-animate-delay="100">
                    <svg class="quick-stat__icon" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    <div class="quick-stat__text">
                        <strong><?php echo esc_html(get_theme_mod('contact_stat1_title', 'Call Anytime')); ?></strong>
                        <span><?php echo esc_html(get_theme_mod('contact_stat1_text', 'Mon-Sat, 9AM-6PM')); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (get_theme_mod('contact_show_stat2', true)) : ?>
                <div class="quick-stat" data-animate="fade-up" data-animate-delay="200">
                    <svg class="quick-stat__icon" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <div class="quick-stat__text">
                        <strong><?php echo esc_html(get_theme_mod('contact_stat2_title', 'Quick Response')); ?></strong>
                        <span><?php echo esc_html(get_theme_mod('contact_stat2_text', 'Within 24 hours')); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (get_theme_mod('contact_show_stat3', true)) : ?>
                <div class="quick-stat" data-animate="fade-up" data-animate-delay="300">
                    <svg class="quick-stat__icon" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <div class="quick-stat__text">
                        <strong><?php echo esc_html(get_theme_mod('contact_stat3_title', 'Visit Us')); ?></strong>
                        <span><?php echo esc_html(get_theme_mod('contact_stat3_text', 'AAAPOS')); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Main Content Section -->
    <section class="contact-main">
        <div class="container">
            <div class="contact-main__grid">
                
                <!-- Contact Form -->
                <?php if (get_theme_mod('contact_show_form', true)) : ?>
                <div class="contact-form-wrapper" data-animate="fade-right">
                    <div class="contact-form-header">
                        <h2 class="contact-form__title"><?php echo esc_html(get_theme_mod('contact_form_title', 'Send Us a Message')); ?></h2>
                        <p class="contact-form__intro">
                            <?php echo esc_html(get_theme_mod('contact_form_intro', 'Fill out the form below and we\'ll get back to you as soon as possible.')); ?>
                        </p>
                    </div>
                    
                    <?php
                    if (function_exists('aaapos_display_contact_form_messages')) {
                        aaapos_display_contact_form_messages();
                    }
                    ?>
                    
                    <form class="contact-form-modern" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="submit_contact_form">
                        <?php wp_nonce_field('contact_form_submit', 'contact_nonce'); ?>
                        
                        <div class="form-grid">
                            <div class="form-field">
                                <label for="contact-name" class="form-label">
                                    <span>Full Name</span>
                                    <span class="form-required">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="contact-name" 
                                    name="contact_name" 
                                    class="form-input"
                                    placeholder="Your Name"
                                    required>
                            </div>
                            
                            <div class="form-field">
                                <label for="contact-email" class="form-label">
                                    <span>Email Address</span>
                                    <span class="form-required">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    id="contact-email" 
                                    name="contact_email" 
                                    class="form-input"
                                    placeholder="Your Email Address"
                                    required>
                            </div>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-field">
                                <label for="contact-phone" class="form-label">
                                    <span>Phone Number</span>
                                </label>
                                <input 
                                    type="tel" 
                                    id="contact-phone" 
                                    name="contact_phone" 
                                    class="form-input"
                                    placeholder="Your Phone Number">
                            </div>
                            
                            <div class="form-field">
                                <label for="contact-subject" class="form-label">
                                    <span>Subject</span>
                                    <span class="form-required">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="contact-subject" 
                                    name="contact_subject" 
                                    class="form-input"
                                    placeholder="How can we help?"
                                    required>
                            </div>
                        </div>
                        
                        <div class="form-field">
                            <label for="contact-message" class="form-label">
                                <span>Your Message</span>
                                <span class="form-required">*</span>
                            </label>
                            <textarea 
                                id="contact-message" 
                                name="contact_message" 
                                class="form-textarea"
                                rows="6"
                                placeholder="Tell us what's on your mind..."
                                required></textarea>
                        </div>
                        
                        <button type="submit" class="btn-contact btn-contact--primary">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                            <span>Send Message</span>
                        </button>
                    </form>
                </div>
                <?php endif; ?>
                
                <!-- Contact Information Sidebar -->
                <div class="contact-sidebar" data-animate="fade-left" data-animate-delay="200">
                    
                    <!-- Contact Info Card -->
                    <?php if (get_theme_mod('contact_show_info_block', true)) : ?>
                    <div class="contact-info-block">
                        <h3 class="contact-info-block__title"><?php echo esc_html(get_theme_mod('contact_info_title', 'Contact Information')); ?></h3>
                        <p class="contact-info-block__text">
                            <?php echo esc_html(get_theme_mod('contact_info_text', 'Reach out through any of these channels—we\'re here to help!')); ?>
                        </p>
                        
                        <div class="contact-info-list">
                            
                            <!-- Phone -->
                            <?php 
                            $phone = get_theme_mod('contact_phone', '1300 555 115');
                            if (!empty($phone)) : 
                            ?>
                            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>" 
                               class="contact-info-link">
                                <div class="contact-info-link__icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                </div>
                                <div class="contact-info-link__content">
                                    <span class="contact-info-link__label">Phone</span>
                                    <span class="contact-info-link__value"><?php echo esc_html($phone); ?></span>
                                </div>
                            </a>
                            <?php endif; ?>
                            
                            <!-- Email -->
                            <?php 
                            $email = get_theme_mod('contact_email', 'support@aaapos.com');
                            if (!empty($email)) : 
                            ?>
                            <a href="mailto:<?php echo esc_attr(antispambot($email)); ?>" 
                               class="contact-info-link">
                                <div class="contact-info-link__icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                </div>
                                <div class="contact-info-link__content">
                                    <span class="contact-info-link__label">Email</span>
                                    <span class="contact-info-link__value"><?php echo esc_html(antispambot($email)); ?></span>
                                </div>
                            </a>
                            <?php endif; ?>
                            
                            <!-- Address -->
                            <?php 
                            $address = get_theme_mod('contact_address', '123 Farm Road, AAAPOS VIC 3440');
                            if (!empty($address)) : 
                            ?>
                            <div class="contact-info-link">
                                <div class="contact-info-link__icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                </div>
                                <div class="contact-info-link__content">
                                    <span class="contact-info-link__label">Address</span>
                                    <span class="contact-info-link__value"><?php echo esc_html($address); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Business Hours Card -->
                    <?php if (get_theme_mod('contact_show_hours', true)) : ?>
                    <div class="business-hours-block">
                        <h3 class="business-hours-block__title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <?php echo esc_html(get_theme_mod('contact_hours_title', 'Business Hours')); ?>
                        </h3>
                        
                        <div class="hours-list">
                            <div class="hours-item">
                                <span class="hours-day">Monday - Friday</span>
                                <span class="hours-time"><?php echo esc_html(get_theme_mod('contact_hours_weekday', '7:00 AM - 7:00 PM')); ?></span>
                            </div>
                            <div class="hours-item">
                                <span class="hours-day">Saturday</span>
                                <span class="hours-time"><?php echo esc_html(get_theme_mod('contact_hours_saturday', '9:00 AM - 5:00 PM')); ?></span>
                            </div>
                            <div class="hours-item <?php echo (strtolower(get_theme_mod('contact_hours_sunday', 'Closed')) === 'closed') ? 'hours-item--closed' : ''; ?>">
                                <span class="hours-day">Sunday</span>
                                <span class="hours-time"><?php echo esc_html(get_theme_mod('contact_hours_sunday', 'Closed')); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Social Media Card -->
                    <?php if (get_theme_mod('contact_show_social', true)) : ?>
                    <div class="social-block-white">
                        <h3 class="social-block__title"><?php echo esc_html(get_theme_mod('contact_social_title', 'Connect With Us')); ?></h3>
                        <p class="social-block__text"><?php echo esc_html(get_theme_mod('contact_social_text', 'Follow us for updates, tips, and special offers')); ?></p>
                        <div class="social-block__links">
                            <?php
                            // Display social media icons - inline implementation with fallback URLs
                            $social_icons = array(
                                'facebook' => array(
                                    'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>',
                                    'fallback' => 'https://www.facebook.com/aaapos.retailmanager'
                                ),
                                'instagram' => array(
                                    'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01"/><path d="M6.5 3h11A3.5 3.5 0 0121 6.5v11a3.5 3.5 0 01-3.5 3.5h-11A3.5 3.5 0 013 17.5v-11A3.5 3.5 0 016.5 3z"/></svg>',
                                    'fallback' => ''
                                ),
                                'twitter' => array(
                                    'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>',
                                    'fallback' => 'https://x.com/'
                                ),
                                'youtube' => array(
                                    'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 00-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 00-1.94 2A29 29 0 001 11.75a29 29 0 00.46 5.33A2.78 2.78 0 003.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 001.94-2 29 29 0 00.46-5.25 29 29 0 00-.46-5.33z M9.75 15.02l.01-6.27 5.77 3.14-5.78 3.13z"/></svg>',
                                    'fallback' => 'https://www.youtube.com/@aaapos'
                                ),
                                'linkedin' => array(
                                    'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z M4 6a2 2 0 110-4 2 2 0 010 4z"/></svg>',
                                    'fallback' => ''
                                ),
                                'pinterest' => array(
                                    'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M8 2.1c-3.3 0-6 2.7-6 6 0 2.5 1.5 4.7 3.7 5.7.1 0 .2 0 .2-.1 0-.1.1-.4.1-.5 0-.1 0-.2-.1-.3-.4-.5-.7-1.1-.7-1.8 0-2.3 1.7-4.4 4.4-4.4 2.4 0 3.7 1.5 3.7 3.4 0 2.6-1.1 4.7-2.8 4.7-.9 0-1.6-.7-1.4-1.6.2-1.1.7-2.2.7-3 0-.7-.4-1.3-1.1-1.3-1 0-1.7 1-1.7 2.3 0 .8.3 1.4.3 1.4s-.9 3.8-1 4.5c-.3 1.3-.1 2.9 0 3.1 0 .1.1.1.2.1.1 0 1.4-1.8 1.8-3 .1-.4.6-2.3.6-2.3.3.6 1.2 1.1 2.2 1.1 2.9 0 4.9-2.7 4.9-6.2 0-2.7-2.2-5.2-5.6-5.2z"/></svg>',
                                    'fallback' => ''
                                ),
                                'tiktok' => array(
                                    'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12a4 4 0 104 4V4a5 5 0 005 5"/></svg>',
                                    'fallback' => ''
                                ),
                                'whatsapp' => array(
                                    'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>',
                                    'fallback' => ''
                                ),
                            );
                            
                            $has_social = false;
                            foreach ($social_icons as $network => $data) {
                                $url = get_theme_mod("footer_social_{$network}", $data['fallback']);
                                if (!empty($url)) {
                                    $has_social = true;
                                    echo '<a href="' . esc_url($url) . '" class="social-link-contact" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr(ucfirst($network)) . '">' . $data['icon'] . '</a>';
                                }
                            }
                            
                            if (!$has_social) {
                                echo '<p class="no-social-message">Add your social media links in Appearance → Customize → Footer Settings</p>';
                            }
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                </div>
                
            </div>
        </div>
    </section>
    
    <!-- Map Section -->
    <?php 
    $map_url = get_theme_mod('contact_map_embed');
    if (!empty($map_url) && get_theme_mod('contact_show_map', true)) : 
    ?>
    <section class="contact-map" data-animate="fade-up">
        <div class="container">
            <div class="contact-map__header">
                <h2 class="contact-map__title"><?php echo esc_html(get_theme_mod('contact_map_title', 'Find Us Here')); ?></h2>
                <p class="contact-map__subtitle"><?php echo esc_html(get_theme_mod('contact_map_subtitle', 'Visit our store and experience our products firsthand')); ?></p>
            </div>
            
            <div class="map-embed">
                <iframe 
                    src="<?php echo esc_url($map_url); ?>"
                    width="100%" 
                    height="500" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    title="<?php echo esc_attr(get_theme_mod('contact_map_title', 'Find Us Here')); ?>">
                </iframe>
            </div>
        </div>
    </section>
    <?php endif; ?>

</div>

<?php 
get_footer();
?>