<?php
/**
 * Top bar component
 * Displays contact info, promo text, and social links (topbar.php)
 * 
 * @package AAAPOS
 */

if (!get_theme_mod('show_top_bar', true)) {
    return; // Don't render if disabled
}

$phone = get_theme_mod('topbar_phone', '+61 3 8400 3083');
$email = get_theme_mod('topbar_email', 'support@aaapos.com');
$promo_text = get_theme_mod('topbar_promo_text', 'Fresh local produce delivered daily!');
?>

<div class="top-bar">
    <div class="container">
        <div class="top-bar-inner">
            
            <!-- Left Side: Contact Info -->
            <div class="top-bar-left">
                <?php if ($phone) : ?>
                    <a href="tel:<?php echo esc_attr(str_replace(' ', '', $phone)); ?>" class="top-bar-item">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                        </svg>
                        <span><?php echo esc_html($phone); ?></span>
                    </a>
                <?php endif; ?>

                <?php if ($email) : ?>
                    <a href="mailto:<?php echo esc_attr($email); ?>" class="top-bar-item">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        <span><?php echo esc_html($email); ?></span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Right Side: Promo Text & Social Links -->
            <div class="top-bar-right">
                <?php if ($promo_text) : ?>
                    <div class="promo-text">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span><?php echo esc_html($promo_text); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Social Links -->
                <div class="social-links">
                    <?php
                    $social_links = array(
                        'facebook' => get_theme_mod('facebook_url', ''),
                        'instagram' => get_theme_mod('instagram_url', ''),
                        'twitter' => get_theme_mod('twitter_url', ''),
                    );

                    foreach ($social_links as $platform => $url) :
                        if (!$url) continue;
                        
                        $icons = array(
                            'facebook' => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>',
                            'instagram' => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>',
                            'twitter' => '<path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>',
                        );
                        ?>
                        <a href="<?php echo esc_url($url); ?>" 
                           class="social-link" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           aria-label="<?php echo esc_attr(ucfirst($platform)); ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <?php echo $icons[$platform]; ?>
                            </svg>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
        </div>
    </div>
</div>