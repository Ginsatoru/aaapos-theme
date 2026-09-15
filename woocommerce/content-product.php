<?php
/**
 * The template for displaying product content within loops
 *
 * UPDATED: Redesigned to match the "wbr-card" design - image slideshow
 * with dots (main image + gallery images), short description, price
 * pill, and pill-shaped add-to-cart button. AJAX add-to-cart classes/
 * data attributes are preserved on the button so cart functionality
 * (aaapos-cart-notifications, wc-add-to-cart, etc.) keeps working.
 * Star rating dropped - no styled slot for it in the new design.
 *
 * @package AAAPOS_Prime
 * @version 2.0.0
 */

defined("ABSPATH") || exit();

global $product;

// Ensure visibility
if (empty($product) || !$product->is_visible()) {
    return;
}

$sale_badge_text = get_theme_mod("sale_badge_text", __("Sale", "aaapos-prime"));

/**
 * Image URLs for the card slideshow (main image + gallery, capped at 4)
 * come from aaapos_get_wbr_card_slide_urls() in inc/woocommerce.php,
 * shared with the homepage featured-products section.
 */
$wbr_slide_urls = aaapos_get_wbr_card_slide_urls($product);

// Short description for the card - trimmed product short description,
// falling back to the full description if no short one is set.
$wbr_desc_source = $product->get_short_description();
if ("" === trim(wp_strip_all_tags($wbr_desc_source))) {
    $wbr_desc_source = $product->get_description();
}
$wbr_card_desc = wp_trim_words(wp_strip_all_tags($wbr_desc_source), 16);
?>
<li <?php wc_product_class("", $product); ?>>
    <div class="wbr-card">

        <!-- Image slideshow -->
        <a href="<?php echo esc_url(get_permalink()); ?>" class="wbr-card__img-wrap">

            <?php if ($product->is_on_sale()): ?>
                <span class="wbr-card__badge wbr-card__badge--sale"><?php echo esc_html($sale_badge_text); ?></span>
            <?php endif; ?>

            <div class="wbr-card__slides">
                <?php foreach ($wbr_slide_urls as $i => $img_url) : ?>
                    <div class="wbr-card__slide<?php echo $i === 0 ? ' is-active' : ''; ?>">
                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" loading="lazy" />
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($wbr_slide_urls) > 1) : ?>
                <div class="wbr-card__dots">
                    <?php foreach ($wbr_slide_urls as $i => $img_url) : ?>
                        <span class="wbr-card__dot<?php echo $i === 0 ? ' is-active' : ''; ?>"></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </a>

        <!-- Body -->
        <div class="wbr-card__body">

            <h2 class="wbr-card__title">
                <a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html($product->get_name()); ?></a>
            </h2>

            <?php if (!empty($wbr_card_desc)) : ?>
                <p class="wbr-card__desc"><?php echo esc_html($wbr_card_desc); ?></p>
            <?php endif; ?>

            <div class="wbr-card__footer">

                <span class="wbr-card__price"><?php echo $product->get_price_html(); ?></span>

                <?php if ($product->is_type("variable")): ?>
                    <a href="<?php echo esc_url($product->get_permalink()); ?>"
                       class="wbr-card__btn button product_type_variable">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                        </svg>
                        <span><?php esc_html_e("Select options", "aaapos-prime"); ?></span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo esc_url("?add-to-cart=" . $product->get_id()); ?>"
                       data-quantity="1"
                       class="wbr-card__btn button product_type_simple add_to_cart_button ajax_add_to_cart"
                       data-product_id="<?php echo esc_attr($product->get_id()); ?>"
                       data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
                       aria-label="<?php echo esc_attr(sprintf(__('Add "%s" to your cart', "aaapos-prime"), $product->get_name())); ?>"
                       rel="nofollow">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                        </svg>
                        <span><?php echo esc_html($product->add_to_cart_text()); ?></span>
                    </a>
                <?php endif; ?>

            </div>

        </div>

    </div>
</li>

<?php
// Slideshow-cycling script - printed once per page load (not once per
// card), guarded with define() since this template runs once per product.
if (!defined("AAAPOS_WBR_CARD_SCRIPT_PRINTED")) :
    define("AAAPOS_WBR_CARD_SCRIPT_PRINTED", true);
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.wbr-card__img-wrap').forEach(function(card){
        var slides = card.querySelectorAll('.wbr-card__slide');
        var dots   = card.querySelectorAll('.wbr-card__dot');
        if ( slides.length <= 1 ) return;
        var current = 0;
        setInterval(function(){
            slides[current].classList.remove('is-active');
            dots[current] && dots[current].classList.remove('is-active');
            current = (current + 1) % slides.length;
            slides[current].classList.add('is-active');
            dots[current] && dots[current].classList.add('is-active');
        }, 2500);
    });
});
</script>
<?php endif; ?>