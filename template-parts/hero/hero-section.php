<?php
/**
 * Hero Section - Driven by Customizer settings (inc/customizer/hero.php)
 * Falls back to the original default values/images if a setting is left
 * empty OR if the stored attachment ID no longer resolves to a real file
 * (e.g. media was deleted) - see aaapos_resolve_media_url() below.
 */

// Resolves an attachment ID to a URL, falling back safely if the ID is
// empty OR if wp_get_attachment_image_url() fails (e.g. deleted media).
if (!function_exists('aaapos_resolve_media_url')) {
    function aaapos_resolve_media_url($attachment_id, $fallback_url) {
        if ($attachment_id) {
            $url = wp_get_attachment_image_url($attachment_id, 'full');
            if ($url) {
                return $url;
            }
        }
        return $fallback_url;
    }
}

// ==========================================================================
// Background image
// ==========================================================================
$hero_bg_image = aaapos_resolve_media_url(
    get_theme_mod('hero_bg_image', ''),
    get_template_directory_uri() . '/images/herobg.png'
);

// ==========================================================================
// Eyebrow / Title / Subtitle
// ==========================================================================
$hero_eyebrow      = get_theme_mod('hero_eyebrow', 'TRUSTED FOR 25+ YEARS');
$hero_title_line1  = get_theme_mod('hero_title_line1', 'AAAPOS RetailManager');
$hero_title_line2  = get_theme_mod('hero_title_line2', 'POS Software');
$hero_subtitle     = get_theme_mod('hero_subtitle', 'Formerly MYOB RetailManager, trusted by retailers across Australia, New Zealand, Asia and the Pacific Islands for over 25 years.');

// ==========================================================================
// Feature bullets (small icon + label row under subtitle)
// Icon uses the uploaded image if set and valid; otherwise falls back to
// the bundled /images/sync.gif and /images/support.gif icons.
// ==========================================================================
$hero_features = array(
    array(
        'icon_url' => aaapos_resolve_media_url(
            get_theme_mod('hero_feature_1_icon', ''),
            get_template_directory_uri() . '/images/sync.gif'
        ),
        'label' => get_theme_mod('hero_feature_1_label', 'Multi-Store Sync'),
    ),
    array(
        'icon_url' => aaapos_resolve_media_url(
            get_theme_mod('hero_feature_2_icon', ''),
            get_template_directory_uri() . '/images/support.gif'
        ),
        'label' => get_theme_mod('hero_feature_2_label', '7 Days Support'),
    ),
);

// ==========================================================================
// Buttons
// ==========================================================================
$primary_btn_text   = get_theme_mod('hero_primary_btn_text', 'RetailManager Update');
$primary_btn_link   = get_theme_mod('hero_primary_btn_link', '/shop');
$secondary_btn_text = get_theme_mod('hero_secondary_btn_text', 'Support');
$secondary_btn_link = get_theme_mod('hero_secondary_btn_link', '/support');

// ==========================================================================
// Product image panel (sliding images)
// ==========================================================================
$show_product_carousel = get_theme_mod('hero_show_product_carousel', true);

$default_carousel_images = array(
    get_template_directory_uri() . '/images/1.png',
    get_template_directory_uri() . '/images/2.png',
    get_template_directory_uri() . '/images/3.png',
    get_template_directory_uri() . '/images/4.png',
);

$hardcoded_product_images = array();
for ($i = 1; $i <= 4; $i++) {
    $hardcoded_product_images[] = aaapos_resolve_media_url(
        get_theme_mod("hero_carousel_image_{$i}", ''),
        $default_carousel_images[$i - 1]
    );
}

// Optional per-image link URL - clicking the slide navigates here if set.
$hardcoded_product_links = array();
for ($i = 1; $i <= 4; $i++) {
    $hardcoded_product_links[] = get_theme_mod("hero_carousel_link_{$i}", '');
}

// ==========================================================================
// "Other goods" cards below the hero
// ==========================================================================
$show_other_goods = get_theme_mod('hero_show_other_goods', true);

$default_other_goods = array(
    1 => array(
        'title'  => 'RM-MultiStore',
        'desc'   => 'Head office data aggregation tool to manage multiple stores from one dashboard, with real-time sync.',
        'image'  => get_template_directory_uri() . '/images/2.png',
        'ribbon' => '',
    ),
    2 => array(
        'title'  => 'Webstore Manager',
        'desc'   => 'Sync stock and orders automatically with Shopify, WooCommerce, eBay and BigCommerce.',
        'image'  => get_template_directory_uri() . '/images/3.png',
        'ribbon' => '',
    ),
    3 => array(
        'title'  => 'RM Mobile App',
        'desc'   => 'Access AAAPOS RetailManager on the go with the free companion app for Android and iOS.',
        'image'  => get_template_directory_uri() . '/images/4.png',
        'ribbon' => 'Coming soon',
    ),
);

$other_goods = array();
foreach ($default_other_goods as $i => $defaults) {
    $other_goods[] = array(
        'title'  => get_theme_mod("hero_goods_{$i}_title", $defaults['title']),
        'desc'   => get_theme_mod("hero_goods_{$i}_desc", $defaults['desc']),
        'image'  => aaapos_resolve_media_url(get_theme_mod("hero_goods_{$i}_image", ''), $defaults['image']),
        'ribbon' => get_theme_mod("hero_goods_{$i}_ribbon", $defaults['ribbon']),
        'link'   => get_theme_mod("hero_goods_{$i}_link", ''),
    );
}

// Word-by-word ascend reveal helper
if (!function_exists('aaapos_ascend_words')) {
    function aaapos_ascend_words($text, $base_delay = 0, $step = 0.045) {
        $words = preg_split('/\s+/', trim($text));
        $out = array();
        foreach ($words as $i => $word) {
            $delay = $base_delay + ($i * $step);
            $out[] = '<span class="ascend-word" style="--ascend-delay: ' . esc_attr(number_format($delay, 3)) . 's;"><span class="ascend-inner">' . esc_html($word) . '</span></span>';
        }
        return implode(' ', $out);
    }
}
?>

<section class="hero-section hero-simple hero-light" style="background-image: url('<?php echo esc_url($hero_bg_image); ?>') !important; background-size: cover !important; background-position: center !important; background-repeat: no-repeat !important;">

    <!-- Hero Content Container -->
    <div class="hero-content-wrapper">
        <div class="hero-content-container">

            <!-- Left Side: Content -->
            <div class="hero-content">
                <?php if ($hero_eyebrow): ?>
                <p class="hero-eyebrow"><?php echo aaapos_ascend_words($hero_eyebrow, 0.05); ?></p>
                <?php endif; ?>

                <h1 class="hero-title">
                    <span class="hero-title-line"><?php echo aaapos_ascend_words($hero_title_line1, 0.15); ?></span>
                    <span class="hero-title-line"><?php echo aaapos_ascend_words($hero_title_line2, 0.28); ?></span>
                </h1>

                <p class="hero-subtitle"><?php echo aaapos_ascend_words($hero_subtitle, 0.4, 0.025); ?></p>

                <?php if (!empty($hero_features)): ?>
                <div class="hero-features">
                    <?php foreach ($hero_features as $f_index => $feature): ?>
                        <div class="hero-feature">
                            <span class="hero-feature-icon">
                                <img src="<?php echo esc_url($feature['icon_url']); ?>" alt="<?php echo esc_attr($feature['label']); ?>">
                            </span>
                            <span class="hero-feature-label"><?php echo aaapos_ascend_words($feature['label'], 0.55 + ($f_index * 0.1)); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="hero-buttons">
                    <?php if ($primary_btn_text): ?>
                        <a href="<?php echo esc_url($primary_btn_link); ?>" class="hero-btn hero-btn-primary">
                            <span class="hero-btn-label"><?php echo aaapos_ascend_words($primary_btn_text, 0.6); ?></span>
                            <svg class="hero-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 17L17 7M17 7H8M17 7V16"></path>
                            </svg>
                        </a>
                    <?php endif; ?>

                    <?php if ($secondary_btn_text): ?>
                        <a href="<?php echo esc_url($secondary_btn_link); ?>" class="hero-btn hero-btn-secondary">
                            <svg class="hero-btn-icon-left" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h7"></path>
                            </svg>
                            <span class="hero-btn-label"><?php echo aaapos_ascend_words($secondary_btn_text, 0.68); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Side: Product Image + Vertical Dots -->
            <?php if ($show_product_carousel): ?>
            <div class="hero-product-carousel hero-product-side hero-product-desktop-only">
                <div class="product-carousel-track">
                    <?php foreach ($hardcoded_product_images as $index => $img_url): ?>
                        <div class="product-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                            <?php $slide_link = $hardcoded_product_links[$index]; ?>
                            <?php if (!empty($slide_link)): ?>
                                <a href="<?php echo esc_url($slide_link); ?>" class="product-slide-link">
                                    <img src="<?php echo esc_url($img_url); ?>"
                                         alt="<?php echo esc_attr($hero_title_line1 . ' - image ' . ($index + 1)); ?>"
                                         class="product-slide-img"
                                         loading="lazy">
                                </a>
                            <?php else: ?>
                                <img src="<?php echo esc_url($img_url); ?>"
                                     alt="<?php echo esc_attr($hero_title_line1 . ' - image ' . ($index + 1)); ?>"
                                     class="product-slide-img"
                                     loading="lazy">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Vertical Dot Pill -->
                <div class="carousel-indicators">
                    <?php foreach ($hardcoded_product_images as $index => $img_url): ?>
                        <button class="indicator-dot <?php echo $index === 0 ? 'active' : ''; ?>"
                                data-slide="<?php echo $index; ?>"
                                aria-label="Go to image <?php echo $index + 1; ?>">
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <div>Testing</div>

        <!-- Other Goods Row -->
        <?php if ($show_other_goods && !empty($other_goods)): ?>
        <div class="hero-other-goods">
            <div class="other-goods-grid">
                <?php foreach ($other_goods as $item): ?>
                    <?php $goods_link = $item['link']; ?>
                    <?php if (!empty($goods_link)): ?><a href="<?php echo esc_url($goods_link); ?>" class="other-goods-card-link"><?php endif; ?>
                    <div class="other-goods-card">
                        <?php if (!empty($item['ribbon'])): ?>
                            <span class="other-goods-ribbon"><?php echo aaapos_ascend_words($item['ribbon'], 0.9); ?></span>
                        <?php endif; ?>
                        <div class="other-goods-card-body">
                            <div class="other-goods-text">
                                <h3 class="other-goods-title"><?php echo aaapos_ascend_words($item['title'], 0.75); ?></h3>
                                <p class="other-goods-desc"><?php echo aaapos_ascend_words($item['desc'], 0.85, 0.02); ?></p>
                            </div>
                            <div class="other-goods-thumb">
                                <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>" loading="lazy">
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($goods_link)): ?></a><?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

</section>