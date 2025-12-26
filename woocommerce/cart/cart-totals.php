<?php
/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * UPDATED: Removed estimated text from Tax, made tax amount bold
 *
 * @package Macedon_Ranges
 */

defined('ABSPATH') || exit;

?>
<div class="cart_totals <?php echo (WC()->customer->has_calculated_shipping()) ? 'calculated_shipping' : ''; ?>">

	<?php do_action('woocommerce_before_cart_totals'); ?>

	<h2><?php esc_html_e('Cart totals', 'macedon-ranges'); ?></h2>

	<table cellspacing="0" class="shop_table shop_table_responsive">

		<tr class="cart-items-count">
			<th><?php esc_html_e('Items', 'macedon-ranges'); ?></th>
			<td data-title="<?php esc_attr_e('Items', 'macedon-ranges'); ?>">
				<?php echo esc_html(WC()->cart->get_cart_contents_count()); ?>
			</td>
		</tr>

		<tr class="cart-subtotal">
			<th><?php esc_html_e('Subtotal', 'macedon-ranges'); ?></th>
			<td data-title="<?php esc_attr_e('Subtotal', 'macedon-ranges'); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>

			<?php do_action('woocommerce_cart_totals_before_shipping'); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action('woocommerce_cart_totals_after_shipping'); ?>

		<?php elseif (WC()->cart->needs_shipping()) : ?>

			<tr class="shipping">
				<th><?php esc_html_e('Shipping', 'macedon-ranges'); ?></th>
				<td data-title="<?php esc_attr_e('Shipping', 'macedon-ranges'); ?>">
					<?php
					/* translators: %s: shipping destination. */
					echo wp_kses_post(sprintf(esc_html__('Shipping options will be updated during checkout.', 'macedon-ranges')));
					?>
				</td>
			</tr>

		<?php endif; ?>

		<?php
		// ALWAYS DISPLAY TAX (even if $0) - WITH BOLD STYLING AND NO ESTIMATED TEXT
		if (wc_tax_enabled()) {
			$tax_total = WC()->cart->get_total_tax();

			if (!WC()->cart->display_prices_including_tax()) {
				if ('itemized' === get_option('woocommerce_tax_total_display')) {
					// Show itemized taxes
					$tax_totals = WC()->cart->get_tax_totals();
					if (!empty($tax_totals)) {
						foreach ($tax_totals as $code => $tax) {
							?>
							<tr class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
								<th><?php esc_html_e('Tax', 'macedon-ranges'); ?></th>
								<td data-title="<?php esc_attr_e('Tax', 'macedon-ranges'); ?>"><strong><?php echo wp_kses_post($tax->formatted_amount); ?></strong></td>
							</tr>
							<?php
						}
					} else {
						// No taxes, show $0.00
						?>
						<tr class="tax-total">
							<th><?php esc_html_e('Tax', 'macedon-ranges'); ?></th>
							<td data-title="<?php esc_attr_e('Tax', 'macedon-ranges'); ?>"><strong><?php echo wc_price(0); ?></strong></td>
						</tr>
						<?php
					}
				} else {
					// Show total tax (or $0.00 if no tax)
					?>
					<tr class="tax-total">
						<th><?php esc_html_e('Tax', 'macedon-ranges'); ?></th>
						<td data-title="<?php esc_attr_e('Tax', 'macedon-ranges'); ?>">
							<?php if ($tax_total > 0) : ?>
								<strong><?php wc_cart_totals_taxes_total_html(); ?></strong>
							<?php else : ?>
								<strong><?php echo wc_price(0); ?></strong>
							<?php endif; ?>
						</td>
					</tr>
					<?php
				}
			}
		}
		?>

		<?php
		// ALWAYS DISPLAY COUPON DISCOUNT (even if $0 or no coupon applied)
		$coupons = WC()->cart->get_coupons();
		$discount_total = WC()->cart->get_discount_total();
		
		if (!empty($coupons)) {
			// Show actual coupons - simple display like other rows
			foreach ($coupons as $code => $coupon) : 
				// Get the discount amount
				$discount_amount = WC()->cart->get_coupon_discount_amount($code);
				?>
				<tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
					<th><?php esc_html_e('Coupon Discount', 'macedon-ranges'); ?></th>
					<td data-title="<?php esc_attr_e('Coupon Discount', 'macedon-ranges'); ?>">
						<strong>-<?php echo wc_price($discount_amount); ?></strong>
					</td>
				</tr>
			<?php endforeach;
		} else {
			// No coupon applied, show $0.00
			?>
			<tr class="cart-discount no-coupon">
				<th><?php esc_html_e('Coupon Discount', 'macedon-ranges'); ?></th>
				<td data-title="<?php esc_attr_e('Coupon Discount', 'macedon-ranges'); ?>">
					<strong><?php echo wc_price(0); ?></strong>
				</td>
			</tr>
			<?php
		}
		?>

		<?php foreach (WC()->cart->get_fees() as $fee) : ?>
			<tr class="fee">
				<th><?php echo esc_html($fee->name); ?></th>
				<td data-title="<?php echo esc_attr($fee->name); ?>"><?php wc_cart_totals_fee_html($fee); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php do_action('woocommerce_cart_totals_before_order_total'); ?>

		<tr class="order-total">
			<th><strong><?php esc_html_e('Total', 'macedon-ranges'); ?></strong></th>
			<td data-title="<?php esc_attr_e('Total', 'macedon-ranges'); ?>"><strong><?php wc_cart_totals_order_total_html(); ?></strong></td>
		</tr>

		<?php do_action('woocommerce_cart_totals_after_order_total'); ?>

	</table>

	<div class="wc-proceed-to-checkout">
		<?php do_action('woocommerce_proceed_to_checkout'); ?>
	</div>

	<?php do_action('woocommerce_after_cart_totals'); ?>

</div>