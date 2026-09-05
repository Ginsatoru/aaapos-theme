<?php
/**
 * Downloads
 *
 * Shows downloads on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/downloads.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 *
 * UPDATED (aaapos-prime): Empty-state now uses a real SVG icon + button
 * markup instead of wc_print_notice(), matching the Orders page pattern.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$downloads     = WC()->customer->get_downloadable_products();
$has_downloads = (bool) $downloads;

do_action( 'woocommerce_before_account_downloads', $has_downloads ); ?>

<?php if ( $has_downloads ) : ?>

	<?php do_action( 'woocommerce_before_available_downloads' ); ?>

	<?php do_action( 'woocommerce_available_downloads', $downloads ); ?>

	<?php do_action( 'woocommerce_after_available_downloads' ); ?>

<?php else : ?>

	<div class="aaapos-empty-downloads">
		<span class="aaapos-empty-downloads__icon">
			<svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
				<path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
			</svg>
		</span>
		<p class="aaapos-empty-downloads__text"><?php esc_html_e( 'No downloads available yet.', 'woocommerce' ); ?></p>
		<a class="aaapos-empty-downloads__btn" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php esc_html_e( 'Browse products', 'woocommerce' ); ?>
		</a>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_downloads', $has_downloads ); ?>