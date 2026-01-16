<?php
/**
 * Cart page template
 * UPDATED: Single coupon implementation with toggle button
 * 
 * @package Macedon_Ranges
 */

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_cart'); ?>

<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
    
    <?php do_action('woocommerce_before_cart_table'); ?>

    <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
        <thead>
            <tr>
                <th class="product-remove"><span class="screen-reader-text"><?php esc_html_e('Remove item', 'macedon-ranges'); ?></span></th>
                <th class="product-thumbnail"><span class="screen-reader-text"><?php esc_html_e('Thumbnail image', 'macedon-ranges'); ?></span></th>
                <th class="product-name"><?php esc_html_e('Product', 'macedon-ranges'); ?></th>
                <th class="product-price"><?php esc_html_e('Price', 'macedon-ranges'); ?></th>
                <th class="product-quantity"><?php esc_html_e('Quantity', 'macedon-ranges'); ?></th>
                <th class="product-subtotal"><?php esc_html_e('Subtotal', 'macedon-ranges'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php do_action('woocommerce_before_cart_contents'); ?>

            <?php
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                    ?>
                    <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

                        <!-- Remove Button -->
                        <td class="product-remove">
                            <?php
                                echo apply_filters(
                                    'woocommerce_cart_item_remove_link',
                                    sprintf(
                                        '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                                        esc_attr(sprintf(__('Remove %s from cart', 'macedon-ranges'), wp_strip_all_tags($_product->get_name()))),
                                        esc_attr($product_id),
                                        esc_attr($_product->get_sku())
                                    ),
                                    $cart_item_key
                                );
                            ?>
                        </td>

                        <!-- Product Thumbnail -->
                        <td class="product-thumbnail">
                            <?php
                            $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

                            if (!$product_permalink) {
                                echo $thumbnail;
                            } else {
                                printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail);
                            }
                            ?>
                        </td>

                        <!-- Product Name -->
                        <td class="product-name" data-title="<?php esc_attr_e('Product', 'macedon-ranges'); ?>">
                            <?php
                            if (!$product_permalink) {
                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                            } else {
                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                            }

                            do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

                            echo wc_get_formatted_cart_item_data($cart_item);

                            if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'macedon-ranges') . '</p>', $product_id));
                            }
                            ?>
                        </td>

                        <!-- Product Price -->
                        <td class="product-price" data-title="<?php esc_attr_e('Price', 'macedon-ranges'); ?>">
                            <?php
                                echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                            ?>
                        </td>

                        <!-- Product Quantity -->
                        <td class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'macedon-ranges'); ?>">
                            <?php
                            if ($_product->is_sold_individually()) {
                                $min_quantity = 1;
                                $max_quantity = 1;
                            } else {
                                $min_quantity = 0;
                                $max_quantity = $_product->get_max_purchase_quantity();
                            }

                            $product_quantity = woocommerce_quantity_input(
                                array(
                                    'input_name'   => "cart[{$cart_item_key}][qty]",
                                    'input_value'  => $cart_item['quantity'],
                                    'max_value'    => $max_quantity,
                                    'min_value'    => $min_quantity,
                                    'product_name' => $_product->get_name(),
                                ),
                                $_product,
                                false
                            );

                            echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
                            ?>
                        </td>

                        <!-- Product Subtotal -->
                        <td class="product-subtotal" data-title="<?php esc_attr_e('Subtotal', 'macedon-ranges'); ?>">
                            <?php
                                echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>

            <?php do_action('woocommerce_cart_contents'); ?>

            <tr>
                <td colspan="6" class="actions">
                    <div class="cart-actions-wrapper">
                        <?php if (wc_coupons_enabled()) { 
                            // Check if a coupon is already applied
                            $applied_coupons = WC()->cart->get_applied_coupons();
                            $has_coupon = !empty($applied_coupons);
                            $coupon_code = $has_coupon ? reset($applied_coupons) : '';
                        ?>
                            <div class="coupon">
                                <label for="coupon_code" class="screen-reader-text"><?php esc_html_e('Coupon:', 'macedon-ranges'); ?></label>
                                <input 
                                    type="text" 
                                    name="coupon_code" 
                                    class="input-text" 
                                    id="coupon_code" 
                                    value="<?php echo esc_attr($coupon_code); ?>" 
                                    placeholder="<?php esc_attr_e('Coupon code', 'macedon-ranges'); ?>"
                                    <?php echo $has_coupon ? 'readonly' : ''; ?>
                                />
                                
                                <?php if ($has_coupon) : ?>
                                    <!-- Remove Coupon Button -->
                                    <button 
                                        type="submit" 
                                        class="button button-remove-coupon<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" 
                                        name="remove_coupon" 
                                        value="<?php echo esc_attr($coupon_code); ?>"
                                    >
                                        <?php esc_html_e('Remove Coupon', 'macedon-ranges'); ?>
                                    </button>
                                <?php else : ?>
                                    <!-- Apply Coupon Button -->
                                    <button 
                                        type="submit" 
                                        class="button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" 
                                        name="apply_coupon" 
                                        value="<?php esc_attr_e('Apply coupon', 'macedon-ranges'); ?>"
                                    >
                                        <?php esc_html_e('Apply coupon', 'macedon-ranges'); ?>
                                    </button>
                                <?php endif; ?>
                                
                                <?php do_action('woocommerce_cart_coupon'); ?>
                            </div>
                        <?php } ?>

                        <div class="cart-actions-right">
                            <button type="submit" class="button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="update_cart" value="<?php esc_attr_e('Update cart', 'macedon-ranges'); ?>"><?php esc_html_e('Update cart', 'macedon-ranges'); ?></button>

                            <a href="<?php echo esc_url(add_query_arg('clear-cart', 'true', wc_get_cart_url())); ?>" class="clear-cart-link clear-cart-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                                <?php esc_html_e('Clear Shopping Cart', 'macedon-ranges'); ?>
                            </a>
                        </div>
                    </div>

                    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                </td>
            </tr>

            <?php do_action('woocommerce_after_cart_contents'); ?>
        </tbody>
    </table>

    <?php do_action('woocommerce_after_cart_table'); ?>

</form>

<?php do_action('woocommerce_before_cart_collaterals'); ?>

<div class="cart-collaterals">
    <?php
        do_action('woocommerce_cart_collaterals');
    ?>
</div>

<?php do_action('woocommerce_after_cart'); ?>