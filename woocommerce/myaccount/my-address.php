<?php
/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 *
 * UPDATED (aaapos-prime): Restructured each address block to match the
 * dashboard's action-card pattern (icon circle + title + edit button in
 * a header row, address content below). The empty-state "not set up yet"
 * text no longer repeats the icon (icon already shown once in the header).
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing'  => __( 'Billing address', 'woocommerce' ),
			'shipping' => __( 'Shipping address', 'woocommerce' ),
		),
		$customer_id
	);
} else {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing' => __( 'Billing address', 'woocommerce' ),
		),
		$customer_id
	);
}

$oldcol = 1;
$col    = 1;

/**
 * Returns the icon markup for a given address type.
 * Billing = payment/card icon, Shipping = location pin icon.
 */
if ( ! function_exists( 'aaapos_my_address_icon' ) ) {
	function aaapos_my_address_icon( $name ) {
		if ( 'shipping' === $name ) {
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3" stroke-linecap="round" stroke-linejoin="round"/></svg>';
		}
		// Billing / default.
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>';
	}
}
?>

<p class="aaapos-addresses-intro">
	<?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'The following addresses will be used on the checkout page by default.', 'woocommerce' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</p>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
	<div class="u-columns woocommerce-Addresses col2-set addresses aaapos-addresses-grid">
<?php endif; ?>

<?php foreach ( $get_addresses as $name => $address_title ) : ?>
	<?php
		$address = wc_get_account_formatted_address( $name );
		$col     = $col * -1;
		$oldcol  = $oldcol * -1;
	?>

	<div class="u-column<?php echo $col < 0 ? 1 : 2; ?> col-<?php echo $oldcol < 0 ? 1 : 2; ?> woocommerce-Address aaapos-address-card">
		<header class="woocommerce-Address-title title aaapos-address-card__header">
			<span class="aaapos-address-card__icon"><?php echo aaapos_my_address_icon( $name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<h2 class="aaapos-address-card__title"><?php echo esc_html( $address_title ); ?></h2>
			<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="edit aaapos-address-card__edit">
				<?php
					printf(
						/* translators: %s: Address title */
						$address ? esc_html__( 'Edit', 'woocommerce' ) : esc_html__( 'Add', 'woocommerce' )
					);
				?>
			</a>
		</header>
		<address class="aaapos-address-card__body">
			<?php if ( $address ) : ?>
				<?php echo wp_kses_post( $address ); ?>
			<?php else : ?>
				<div class="aaapos-address-empty">
					<p class="aaapos-address-empty__text"><?php esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' ); ?></p>
				</div>
			<?php endif; ?>

			<?php
				/**
				 * Used to output content after core address fields.
				 *
				 * @param string $name Address type.
				 * @since 8.7.0
				 */
				do_action( 'woocommerce_my_account_after_my_address', $name );
			?>
		</address>
	</div>

<?php endforeach; ?>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
	</div>
	<?php
endif;