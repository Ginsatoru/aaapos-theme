<?php
/**
 * Checkout Form - WITH INTEGRATED PROGRESS INDICATOR
 * UPDATED: Removed "Ship to different address" functionality
 * CLEANED UP: Billing address only, shipping handled by WooCommerce default
 *
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if (!defined("ABSPATH")) {
    exit();
}

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (
    !$checkout->is_registration_enabled() &&
    $checkout->is_registration_required() &&
    !is_user_logged_in()
) {
    echo esc_html(
        apply_filters(
            "woocommerce_checkout_must_be_logged_in_message",
            __("You must be logged in to checkout.", "woocommerce"),
        ),
    );
    return;
}

// Check if cart needs shipping at all
$needs_shipping = WC()->cart->needs_shipping() && WC()->cart->show_shipping();
?>

<!-- CHECKOUT PROGRESS INDICATOR -->
<div class="woocommerce-checkout-progress">
	<div class="checkout-steps">
		
		<!-- Step 1: Shopping Cart (Completed & Clickable) -->
		<a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="checkout-step completed clickable">
			<span class="step-number">1</span>
			<span class="step-label"><?php esc_html_e("Shopping cart", "macedon-ranges"); ?></span>
		</a>
		
		<!-- Arrow -->
		<svg class="step-arrow" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
		
		<!-- Step 2: Checkout Details (Active) -->
		<div class="checkout-step active">
			<span class="step-number">2</span>
			<span class="step-label"><?php esc_html_e("Checkout details", "macedon-ranges"); ?></span>
		</div>
		
		<!-- Arrow -->
		<svg class="step-arrow" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
		
		<!-- Step 3: Order Complete -->
		<div class="checkout-step">
			<span class="step-number">3</span>
			<span class="step-label"><?php esc_html_e("Order complete", "macedon-ranges"); ?></span>
		</div>
		
	</div>
</div>

<?php do_action("woocommerce_before_checkout_form", $checkout); ?>

<div class="checkout-wrapper">
	
	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

		<div class="checkout-container">
			
			<!-- Left Column: Billing & Shipping Details -->
			<div class="checkout-main">
				
				<?php if ($checkout->get_checkout_fields()): ?>

					<?php do_action("woocommerce_checkout_before_customer_details"); ?>

					<div class="checkout-customer-details">
						
						<!-- Billing Details Section -->
						<div class="checkout-section billing-section">
							<h3 class="section-title">
								<span class="title-icon">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
										<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
										<circle cx="12" cy="7" r="4"></circle>
									</svg>
								</span>
								<?php esc_html_e("Billing details", "woocommerce"); ?>
							</h3>
							
							<div class="billing-fields">
								<?php do_action("woocommerce_checkout_billing"); ?>
							</div>
						</div>

						<!-- Shipping Method Section - Show if cart needs shipping -->
						<?php if ($needs_shipping): ?>
							
							<div class="checkout-section shipping-method-section">
								<h3 class="section-title">
									<span class="title-icon">
										<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<rect x="1" y="3" width="15" height="13"></rect>
											<polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
											<circle cx="5.5" cy="18.5" r="2.5"></circle>
											<circle cx="18.5" cy="18.5" r="2.5"></circle>
										</svg>
									</span>
									<?php esc_html_e("Shipping method", "macedon-ranges"); ?>
								</h3>
								
								<div class="shipping-method-options" id="shipping_method">
									<?php woocommerce_order_review_shipping(); ?>
								</div>
							</div>

						<?php endif; ?>

						<!-- Additional Information (Order Notes Only) -->
						<?php if (apply_filters("woocommerce_enable_order_notes_field", "yes" === get_option("woocommerce_enable_order_comments", "yes"))): ?>
							
							<div class="checkout-section additional-fields-section">
								<h3 class="section-title">
									<span class="title-icon">
										<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
											<polyline points="14 2 14 8 20 8"></polyline>
											<line x1="12" y1="18" x2="12" y2="12"></line>
											<line x1="9" y1="15" x2="15" y2="15"></line>
										</svg>
									</span>
									<?php esc_html_e("Additional information", "woocommerce"); ?>
								</h3>
								
								<div class="additional-fields">
									<?php do_action("woocommerce_before_order_notes", $checkout); ?>
									
									<div class="woocommerce-additional-fields__field-wrapper">
										<?php foreach ($checkout->get_checkout_fields("order") as $key => $field): ?>
											<?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
										<?php endforeach; ?>
									</div>

									<?php do_action("woocommerce_after_order_notes", $checkout); ?>
								</div>
							</div>

						<?php endif; ?>

					</div>

					<?php do_action("woocommerce_checkout_after_customer_details"); ?>

				<?php endif; ?>

			</div>

			<!-- Right Column: Order Review -->
			<div class="checkout-sidebar">
				
				<div class="order-review-wrapper">

					<h3 id="order_review_heading" class="order-review-title">
						<span class="title-icon">
							<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
								<circle cx="7.5" cy="17.5" r="0.833"></circle>
								<circle cx="16.667" cy="17.5" r="0.833"></circle>
								<path d="M0.833 0.833h3.334l2.233 11.158a1.667 1.667 0 0 0 1.667 1.342h8.1a1.667 1.667 0 0 0 1.666-1.342L19.167 5H5"></path>
							</svg>
						</span>
						<?php esc_html_e('Your order', 'woocommerce'); ?>
					</h3>

					<?php do_action('woocommerce_checkout_before_order_review'); ?>

					<div id="order_review" class="woocommerce-checkout-review-order">
						<?php do_action('woocommerce_checkout_order_review'); ?>
					</div>

				</div>

			</div>

		</div>

	</form>

</div>

<?php do_action("woocommerce_after_checkout_form", $checkout); ?>