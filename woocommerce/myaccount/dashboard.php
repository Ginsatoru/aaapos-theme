<?php
/**
 * My Account Dashboard
 *
 * Custom AAAPOS Prime dashboard: welcome header, two action cards
 * (My Account / Billing Address), and a 4-item stat grid
 * (Downloads / Orders / Addresses / Payment Methods) with real
 * badge counts pulled from the current user.
 *
 * This template overrides WooCommerce's default
 * woocommerce/myaccount/dashboard.php. Previously this markup lived
 * in inc/myaccount-dashboard.php and was injected via the
 * woocommerce_account_dashboard hook (which replaced WooCommerce's
 * own dashboard text callback). Moved here directly so it lives
 * alongside the rest of the myaccount template overrides.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package AAAPOS_Prime
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$user_id      = get_current_user_id();
$display_name = ! empty( $current_user->first_name )
	? $current_user->first_name
	: $current_user->display_name;

// Profile icon (custom image instead of Gravatar)
$avatar = sprintf(
	'<img src="%s" alt="%s" class="dashboard-welcome-avatar-img">',
	esc_url( get_template_directory_uri() . '/images/icons/profile-icon.png' ),
	esc_attr( $current_user->display_name )
);

// Real stats - no fake/placeholder numbers
$downloads_count = count( wc_get_customer_available_downloads( $user_id ) );
$orders_count    = wc_get_customer_order_count( $user_id );

// Addresses: count how many of billing/shipping are actually filled in
$addresses_count = 0;
if ( $current_user->billing_address_1 || $current_user->billing_city ) {
	$addresses_count++;
}
if ( $current_user->shipping_address_1 || $current_user->shipping_city ) {
	$addresses_count++;
}

// Saved payment methods (only counts if a gateway supports tokenization)
$payment_methods_count = 0;
if ( class_exists( 'WC_Payment_Tokens' ) ) {
	$payment_methods_count = count( WC_Payment_Tokens::get_customer_tokens( $user_id ) );
}

$logout_url          = wc_logout_url();
$edit_account_url     = wc_get_account_endpoint_url( 'edit-account' );
$edit_address_url     = wc_get_account_endpoint_url( 'edit-address' );
$downloads_url        = wc_get_account_endpoint_url( 'downloads' );
$orders_url           = wc_get_account_endpoint_url( 'orders' );
$payment_methods_url  = wc_get_account_endpoint_url( 'payment-methods' );
?>

<!-- Welcome Header -->
<div class="dashboard-welcome-card">
	<div class="dashboard-welcome-avatar"><?php echo $avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	<div class="dashboard-welcome-text">
		<h2 class="dashboard-welcome-title">
			<?php esc_html_e( 'Hello,', 'aaapos-prime' ); ?>
			<span class="greeting-name"><?php echo esc_html( $display_name ); ?></span>
		</h2>
		<a href="<?php echo esc_url( $logout_url ); ?>" class="dashboard-welcome-logout">
			<?php esc_html_e( 'Not you?', 'aaapos-prime' ); ?>
			<span><?php esc_html_e( 'Logout', 'aaapos-prime' ); ?></span>
		</a>
	</div>
</div>

<!-- Two Action Cards -->
<div class="dashboard-action-grid">

	<div class="dashboard-action-card">
		<div class="dashboard-action-card__icon">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
				<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
			</svg>
		</div>
		<div class="dashboard-action-card__body">
			<h3><?php esc_html_e( 'My Account', 'aaapos-prime' ); ?></h3>
			<p><?php esc_html_e( 'Edit your name or change your password.', 'aaapos-prime' ); ?></p>
			<a href="<?php echo esc_url( $edit_account_url ); ?>" class="dashboard-action-card__btn">
				<?php esc_html_e( 'Change Your Password', 'aaapos-prime' ); ?>
			</a>
		</div>
	</div>

	<div class="dashboard-action-card">
		<div class="dashboard-action-card__icon dashboard-action-card__icon--filled">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
				<path stroke-linecap="round" stroke-linejoin="round" d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
				<circle cx="12" cy="10" r="3" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</div>
		<div class="dashboard-action-card__body">
			<h3><?php esc_html_e( 'Billing Address', 'aaapos-prime' ); ?></h3>
			<p><?php esc_html_e( 'Shipping and billing addresses and edit your account details.', 'aaapos-prime' ); ?></p>
			<a href="<?php echo esc_url( $edit_address_url ); ?>" class="dashboard-action-card__btn">
				<?php esc_html_e( 'Edit Your Account', 'aaapos-prime' ); ?>
			</a>
		</div>
	</div>

</div>

<!-- 4-Item Stat Grid -->
<div class="dashboard-stats-grid">

	<a href="<?php echo esc_url( $downloads_url ); ?>" class="dashboard-stat-card">
		<?php if ( $downloads_count > 0 ) : ?>
			<span class="dashboard-stat-badge"><?php echo esc_html( $downloads_count ); ?></span>
		<?php endif; ?>
		<span class="dashboard-stat-icon">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
				<path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
			</svg>
		</span>
		<span class="dashboard-stat-label"><?php esc_html_e( 'Downloads', 'aaapos-prime' ); ?></span>
	</a>

	<a href="<?php echo esc_url( $orders_url ); ?>" class="dashboard-stat-card">
		<?php if ( $orders_count > 0 ) : ?>
			<span class="dashboard-stat-badge"><?php echo esc_html( $orders_count ); ?></span>
		<?php endif; ?>
		<span class="dashboard-stat-icon">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
				<path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
			</svg>
		</span>
		<span class="dashboard-stat-label"><?php esc_html_e( 'Orders', 'aaapos-prime' ); ?></span>
	</a>

	<a href="<?php echo esc_url( $edit_address_url ); ?>" class="dashboard-stat-card">
		<?php if ( $addresses_count > 0 ) : ?>
			<span class="dashboard-stat-badge"><?php echo esc_html( $addresses_count ); ?></span>
		<?php endif; ?>
		<span class="dashboard-stat-icon">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
				<path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
				<path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
			</svg>
		</span>
		<span class="dashboard-stat-label"><?php esc_html_e( 'Addresses', 'aaapos-prime' ); ?></span>
	</a>

	<a href="<?php echo esc_url( $payment_methods_url ); ?>" class="dashboard-stat-card">
		<?php if ( $payment_methods_count > 0 ) : ?>
			<span class="dashboard-stat-badge"><?php echo esc_html( $payment_methods_count ); ?></span>
		<?php endif; ?>
		<span class="dashboard-stat-icon">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
				<path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
			</svg>
		</span>
		<span class="dashboard-stat-label"><?php esc_html_e( 'Payment Methods', 'aaapos-prime' ); ?></span>
	</a>

</div>

<?php
/**
 * My Account dashboard.
 * Kept for compatibility with plugins that hook into it
 * (e.g. subscriptions/memberships extensions).
 *
 * @since 2.6.0
 */
do_action( 'woocommerce_account_dashboard' );