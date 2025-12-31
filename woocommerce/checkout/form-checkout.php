<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>

<div class="checkout-wrapper">
	
	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

		<div class="checkout-container">
			
			<!-- Left Column: Billing & Shipping Details -->
			<div class="checkout-main">
				
				<?php if ( $checkout->get_checkout_fields() ) : ?>

					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

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
								<?php esc_html_e( 'Billing details', 'woocommerce' ); ?>
							</h3>
							
							<div class="billing-fields">
								<?php do_action( 'woocommerce_checkout_billing' ); ?>
							</div>
						</div>

						<!-- Shipping Section -->
						<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
							
							<div class="checkout-section shipping-section">
								<div class="ship-to-different-address">
									<h3 class="section-title">
										<label class="ship-different-checkbox">
											<input id="ship-to-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" <?php checked( apply_filters( 'woocommerce_ship_to_different_address_checked', 'shipping' === get_option( 'woocommerce_ship_to_destination' ) ? 1 : 0 ), 1 ); ?> type="checkbox" name="ship_to_different_address" value="1" />
											<span class="title-icon">
												<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
													<rect x="1" y="3" width="15" height="13"></rect>
													<polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
													<circle cx="5.5" cy="18.5" r="2.5"></circle>
													<circle cx="18.5" cy="18.5" r="2.5"></circle>
												</svg>
											</span>
											<?php esc_html_e( 'Ship to a different address?', 'woocommerce' ); ?>
										</label>
									</h3>
								</div>

								<div class="shipping-fields">
									<?php do_action( 'woocommerce_checkout_shipping' ); ?>
								</div>
							</div>

						<?php endif; ?>

						<!-- Additional Information -->
						<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) : ?>
							
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
									<?php esc_html_e( 'Additional information', 'woocommerce' ); ?>
								</h3>
								
								<div class="additional-fields">
									<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>
									
									<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) : ?>
										<div class="woocommerce-additional-fields__field-wrapper">
											<?php foreach ( $checkout->get_checkout_fields( 'order' ) as $key => $field ) : ?>
												<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>

									<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
								</div>
							</div>

						<?php endif; ?>

					</div>

					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

				<?php endif; ?>

			</div>

			<!-- Right Column: Order Review -->
			<div class="checkout-sidebar">
				
				<div class="order-review-wrapper">
					<h3 id="order_review_heading" class="order-review-title">
						<span class="title-icon">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<circle cx="9" cy="21" r="1"></circle>
								<circle cx="20" cy="21" r="1"></circle>
								<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
							</svg>
						</span>
						<?php esc_html_e( 'Your order', 'woocommerce' ); ?>
					</h3>

					<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

					<div id="order_review" class="woocommerce-checkout-review-order">
						<?php do_action( 'woocommerce_checkout_order_review' ); ?>
					</div>

					<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
				</div>

			</div>

		</div>

	</form>

</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>