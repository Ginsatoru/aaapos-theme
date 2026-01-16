<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;
?>

<table class="shop_table woocommerce-checkout-review-order-table">
	<thead>
		<tr>
			<th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-total"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
					<td class="product-name">
						<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?>
						<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', $cart_item['quantity'] ) . '</strong>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</td>
					<td class="product-total">
						<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</td>
				</tr>
				<?php
			}
		}

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</tbody>
	<tfoot>

		<tr class="cart-subtotal">
			<th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?> <?php echo esc_html( $code ); ?></th>
				<td data-title="<?php esc_attr_e( 'Coupon', 'woocommerce' ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

			<tr class="woocommerce-shipping-totals shipping">
				<th><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></th>
				<td data-title="<?php esc_attr_e( 'Shipping', 'woocommerce' ); ?>">
					<?php
					// Get the chosen shipping method
					$packages = WC()->shipping()->get_packages();
					$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
					
					if ( ! empty( $packages ) && ! empty( $chosen_methods ) ) {
						$package = reset( $packages );
						$chosen_method = isset( $chosen_methods[0] ) ? $chosen_methods[0] : '';
						
						// Find the chosen shipping rate
						if ( isset( $package['rates'][$chosen_method] ) ) {
							$rate = $package['rates'][$chosen_method];
							echo wc_price( $rate->cost );
							
							// Add tax if applicable
							if ( $rate->get_shipping_tax() > 0 && ! wc_prices_include_tax() ) {
								echo ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
							}
						} else {
							// No shipping method selected yet
							esc_html_e( 'Select a shipping method', 'woocommerce' );
						}
					} else {
						esc_html_e( 'Shipping will be calculated during checkout', 'woocommerce' );
					}
					?>
				</td>
			</tr>

			<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo esc_html( $fee->name ); ?></th>
				<td data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
					<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<th><?php echo esc_html( $tax->label ); ?></th>
						<td data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
					<td data-title="<?php echo esc_attr( WC()->countries->tax_or_vat() ); ?>"><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<tr class="order-total">
			<th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>"><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</tfoot>
</table>

<!-- Coupon Field with Toggle Button (Apply/Remove) -->
<?php if ( wc_coupons_enabled() ) : 
	$applied_coupons = WC()->cart->get_applied_coupons();
	$has_coupon = !empty($applied_coupons);
	$coupon_code = $has_coupon ? $applied_coupons[0] : '';
?>
	<div class="checkout-coupon-bottom">
		<form class="coupon-form-bottom" method="post">
			<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
			<input type="hidden" name="redirect" value="<?php echo esc_url( wc_get_checkout_url() ); ?>" />
			<div class="coupon-input-wrapper">
				<input 
					type="text" 
					name="coupon_code" 
					class="input-text" 
					placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" 
					id="coupon_code_bottom" 
					value="<?php echo esc_attr($coupon_code); ?>"
					<?php echo $has_coupon ? 'readonly' : ''; ?>
				/>
				<button 
					type="submit" 
					class="button coupon-apply-btn <?php echo $has_coupon ? 'is-applied' : ''; ?>" 
					name="<?php echo $has_coupon ? 'remove_coupon' : 'apply_coupon'; ?>" 
					value="<?php echo $has_coupon ? esc_attr($coupon_code) : esc_attr__('Apply', 'woocommerce'); ?>"
					data-action="<?php echo $has_coupon ? 'remove' : 'apply'; ?>"
					data-coupon-code="<?php echo esc_attr($coupon_code); ?>"
				>
					<?php echo $has_coupon ? esc_html__('Remove', 'woocommerce') : esc_html__('Apply', 'woocommerce'); ?>
				</button>
			</div>
		</form>
	</div>
<?php endif; ?>