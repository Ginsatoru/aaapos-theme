<?php
/**
 * Order Received (Thank You) Page - Modern Clean Design
 *
 * @package WooCommerce\Templates
 * @version 8.0.0
 */

defined('ABSPATH') || exit;
?>

<div class="order-received-wrapper">

	<?php
	if ($order):
		do_action('woocommerce_before_thankyou', $order->get_id());
	?>

		<?php if ($order->has_status('failed')): ?>

			<!-- ORDER FAILED -->
			<div class="order-failed-notice">
				<div class="notice-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<circle cx="12" cy="12" r="10"></circle>
						<line x1="12" y1="8" x2="12" y2="12"></line>
						<line x1="12" y1="16" x2="12.01" y2="16"></line>
					</svg>
				</div>
				<div class="notice-content">
					<h3><?php esc_html_e('Payment failed', 'woocommerce'); ?></h3>
					<p><?php esc_html_e('Unfortunately your order cannot be processed as the payment was not successful. Please try again.', 'woocommerce'); ?></p>
				</div>
			</div>

		<?php else: ?>

			<!-- SUCCESS MESSAGE -->
			<div class="order-success-banner">
				<div class="success-icon">
					<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
						<polyline points="22 4 12 14.01 9 11.01"></polyline>
					</svg>
				</div>
				<div class="success-content">
					<h2><?php esc_html_e('Thank you. Your order has been received.', 'woocommerce'); ?></h2>
					<p><?php esc_html_e('We\'ll send you an email confirmation shortly.', 'woocommerce'); ?></p>
				</div>
			</div>

		<?php endif; ?>

		<!-- ORDER OVERVIEW CARDS -->
		<div class="order-overview-cards">
			
			<div class="overview-card">
				<div class="card-icon">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
						<polyline points="14 2 14 8 20 8"></polyline>
					</svg>
				</div>
				<div class="card-content">
					<span class="card-label"><?php esc_html_e('Order number', 'woocommerce'); ?></span>
					<span class="card-value"><?php echo $order->get_order_number(); ?></span>
				</div>
			</div>

			<div class="overview-card">
				<div class="card-icon">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
						<line x1="16" y1="2" x2="16" y2="6"></line>
						<line x1="8" y1="2" x2="8" y2="6"></line>
						<line x1="3" y1="10" x2="21" y2="10"></line>
					</svg>
				</div>
				<div class="card-content">
					<span class="card-label"><?php esc_html_e('Date', 'woocommerce'); ?></span>
					<span class="card-value"><?php echo wc_format_datetime($order->get_date_created()); ?></span>
				</div>
			</div>

			<div class="overview-card">
				<div class="card-icon">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
						<polyline points="22,6 12,13 2,6"></polyline>
					</svg>
				</div>
				<div class="card-content">
					<span class="card-label"><?php esc_html_e('Email', 'woocommerce'); ?></span>
					<span class="card-value"><?php echo $order->get_billing_email(); ?></span>
				</div>
			</div>

			<div class="overview-card">
				<div class="card-icon">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<line x1="12" y1="1" x2="12" y2="23"></line>
						<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
					</svg>
				</div>
				<div class="card-content">
					<span class="card-label"><?php esc_html_e('Total', 'woocommerce'); ?></span>
					<span class="card-value"><?php echo $order->get_formatted_order_total(); ?></span>
				</div>
			</div>

			<div class="overview-card">
				<div class="card-icon">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
						<line x1="1" y1="10" x2="23" y2="10"></line>
					</svg>
				</div>
				<div class="card-content">
					<span class="card-label"><?php esc_html_e('Payment method', 'woocommerce'); ?></span>
					<span class="card-value"><?php echo wp_kses_post($order->get_payment_method_title()); ?></span>
				</div>
			</div>

		</div>

		<!-- PAYMENT INSTRUCTIONS (if any) -->
		<?php if ($order->get_payment_method()): ?>
			<div class="order-payment-section">
				<?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
			</div>
		<?php endif; ?>

		<!-- ORDER DETAILS SECTION -->
		<div class="order-details-section">
			
			<h3 class="section-title">
				<span class="title-icon">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<circle cx="9" cy="21" r="1"></circle>
						<circle cx="20" cy="21" r="1"></circle>
						<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
					</svg>
				</span>
				<?php esc_html_e('Order details', 'woocommerce'); ?>
			</h3>

			<?php do_action('woocommerce_thankyou_order_received_text', $order); ?>

			<?php do_action('woocommerce_order_details_before_order_table', $order); ?>

			<!-- ORDER TABLE -->
			<div class="order-table-wrapper">
				<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
					<thead>
						<tr>
							<th class="woocommerce-table__product-name product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
							<th class="woocommerce-table__product-table product-total"><?php esc_html_e('Total', 'woocommerce'); ?></th>
						</tr>
					</thead>

					<tbody>
						<?php
						do_action('woocommerce_order_details_before_order_table_items', $order);

						foreach ($order->get_items() as $item_id => $item):
							$product = $item->get_product();
						?>
							<tr class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order)); ?>">

								<td class="woocommerce-table__product-name product-name">
									<div class="product-info">
										<?php if ($product): ?>
											<div class="product-thumbnail">
												<?php echo $product->get_image('thumbnail'); ?>
											</div>
										<?php endif; ?>
										<div class="product-details">
											<span class="product-name-text">
												<?php
												$is_visible = $product && $product->is_visible();
												$product_permalink = apply_filters('woocommerce_order_item_permalink', $is_visible ? $product->get_permalink($item) : '', $item, $order);

												echo $product_permalink ? sprintf('<a href="%s">%s</a>', esc_url($product_permalink), wp_kses_post($item->get_name())) : wp_kses_post($item->get_name());
												?>
											</span>
											<span class="product-quantity">
												<?php
												$qty = $item->get_quantity();
												$refunded_qty = $order->get_qty_refunded_for_item($item_id);

												if ($refunded_qty) {
													$qty_display = '<del>' . esc_html($qty) . '</del> <ins>' . esc_html($qty - ($refunded_qty * -1)) . '</ins>';
												} else {
													$qty_display = esc_html($qty);
												}
												echo apply_filters('woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf('&times;&nbsp;%s', $qty_display) . '</strong>', $item);
												?>
											</span>
											<?php
											do_action('woocommerce_order_item_meta_start', $item_id, $item, $order, false);
											wc_display_item_meta($item);
											do_action('woocommerce_order_item_meta_end', $item_id, $item, $order, false);
											?>
										</div>
									</div>
								</td>

								<td class="woocommerce-table__product-total product-total">
									<?php echo $order->get_formatted_line_subtotal($item); ?>
								</td>

							</tr>
						<?php endforeach; ?>

						<?php
						do_action('woocommerce_order_details_after_order_table_items', $order);
						?>
					</tbody>

					<tfoot>
						<?php
						foreach ($order->get_order_item_totals() as $key => $total):
						?>
							<tr>
								<th scope="row"><?php echo esc_html($total['label']); ?></th>
								<td><?php echo ('payment_method' === $key) ? esc_html($total['value']) : wp_kses_post($total['value']); ?></td>
							</tr>
						<?php endforeach; ?>

						<?php if ($order->get_customer_note()): ?>
							<tr>
								<th><?php esc_html_e('Note:', 'woocommerce'); ?></th>
								<td><?php echo wp_kses_post(nl2br(wptexturize($order->get_customer_note()))); ?></td>
							</tr>
						<?php endif; ?>
					</tfoot>
				</table>
			</div>

			<?php do_action('woocommerce_order_details_after_order_table', $order); ?>

		</div>

		<!-- CUSTOMER DETAILS SECTION -->
		<div class="order-customer-details">
			<?php do_action('woocommerce_order_details_after_customer_details', $order); ?>
		</div>

	<?php else: ?>

		<!-- NO ORDER FOUND -->
		<div class="order-not-found">
			<div class="notice-icon">
				<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<circle cx="12" cy="12" r="10"></circle>
					<line x1="12" y1="8" x2="12" y2="12"></line>
					<line x1="12" y1="16" x2="12.01" y2="16"></line>
				</svg>
			</div>
			<h3><?php esc_html_e('Order not found', 'woocommerce'); ?></h3>
			<p><?php esc_html_e('Sorry, this order cannot be found.', 'woocommerce'); ?></p>
			<a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="button">
				<?php esc_html_e('Continue shopping', 'woocommerce'); ?>
			</a>
		</div>

	<?php endif; ?>

</div>

<?php do_action('woocommerce_after_thankyou', $order ? $order->get_id() : 0); ?>