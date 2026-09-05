<?php
/**
 * Payment methods
 *
 * Shows customer payment methods on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/payment-methods.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.9.0
 *
 * UPDATED (aaapos-prime): Restructured to match the address-card pattern -
 * icon circle + title + "Add" button in a header row, table (or empty-state
 * text) below. Wrapped in the same flat white rounded-card used on the
 * Addresses page.
 */

defined( 'ABSPATH' ) || exit;

$saved_methods = wc_get_customer_saved_methods_list( get_current_user_id() );
$has_methods   = (bool) $saved_methods;
$types         = wc_get_account_payment_methods_types();
$has_gateways  = (bool) WC()->payment_gateways->get_available_payment_gateways();

if ( isset( $_GET['debug_payment_tokens'] ) && current_user_can( 'manage_options' ) ) {
	echo '<pre style="background:#fff;border:1px solid #ccc;padding:15px;margin:15px 0;font-size:12px;overflow:auto;">';
	echo 'get_current_user_id(): ' . get_current_user_id() . "\n\n";
	echo 'has_gateways: ' . var_export( $has_gateways, true ) . "\n\n";
	echo 'raw tokens from WC_Payment_Tokens::get_customer_tokens():' . "\n";
	print_r( WC_Payment_Tokens::get_customer_tokens( get_current_user_id() ) );
	echo "\n\nsaved_methods list:\n";
	print_r( $saved_methods );
	echo '</pre>';
}

/**
 * Returns the icon markup for the payment methods card header.
 */
if ( ! function_exists( 'aaapos_payment_methods_icon' ) ) {
	function aaapos_payment_methods_icon() {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>';
	}
}

do_action( 'woocommerce_before_account_payment_methods', $has_methods ); ?>

<div class="aaapos-payment-card">
	<header class="aaapos-payment-card__header">
		<span class="aaapos-payment-card__icon"><?php echo aaapos_payment_methods_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<h2 class="aaapos-payment-card__title"><?php esc_html_e( 'Payment methods', 'woocommerce' ); ?></h2>
		<?php if ( $has_gateways ) : ?>
			<a href="<?php echo esc_url( wc_get_endpoint_url( 'add-payment-method' ) ); ?>" class="aaapos-payment-card__add">
				<?php esc_html_e( 'Add', 'woocommerce' ); ?>
			</a>
		<?php endif; ?>
	</header>

	<div class="aaapos-payment-card__body">
		<?php if ( $has_methods ) : ?>

			<table class="woocommerce-MyAccount-paymentMethods shop_table shop_table_responsive account-payment-methods-table">
				<thead>
					<tr>
						<?php foreach ( wc_get_account_payment_methods_columns() as $column_id => $column_name ) : ?>
							<th class="woocommerce-PaymentMethod woocommerce-PaymentMethod--<?php echo esc_attr( $column_id ); ?> payment-method-<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<?php foreach ( $saved_methods as $type => $methods ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
					<?php foreach ( $methods as $method ) : ?>
						<tr class="payment-method<?php echo ! empty( $method['is_default'] ) ? ' default-payment-method' : ''; ?>">
							<?php foreach ( wc_get_account_payment_methods_columns() as $column_id => $column_name ) : ?>
								<td class="woocommerce-PaymentMethod woocommerce-PaymentMethod--<?php echo esc_attr( $column_id ); ?> payment-method-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
									<?php
									if ( has_action( 'woocommerce_account_payment_methods_column_' . $column_id ) ) {
										do_action( 'woocommerce_account_payment_methods_column_' . $column_id, $method );
									} elseif ( 'method' === $column_id ) {
										if ( ! empty( $method['method']['last4'] ) ) {
											/* translators: 1: credit card type 2: last 4 digits */
											echo sprintf( esc_html__( '%1$s ending in %2$s', 'woocommerce' ), esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) ), esc_html( $method['method']['last4'] ) );
										} else {
											echo esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) );
										}
									} elseif ( 'expires' === $column_id ) {
										echo esc_html( $method['expires'] );
									} elseif ( 'actions' === $column_id ) {
										foreach ( $method['actions'] as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
											echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>&nbsp;';
										}
									}
									?>
								</td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</table>

		<?php else : ?>

			<div class="aaapos-payment-empty">
				<p class="aaapos-payment-empty__text"><?php esc_html_e( 'No saved methods found.', 'woocommerce' ); ?></p>
			</div>

		<?php endif; ?>
	</div>
</div>

<?php do_action( 'woocommerce_after_account_payment_methods', $has_methods ); ?>