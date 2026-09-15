<?php
/**
 * Shop breadcrumb
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/breadcrumb.php.
 *
 * UPDATED: WBR-style design - standalone circular home icon badge,
 * plain text crumbs, chevron separators. "Home" is now a normal text
 * crumb (previously the icon replaced it entirely).
 * Works on ALL WooCommerce pages EXCEPT cart page
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// HIDE BREADCRUMBS ON CART PAGE
if ( is_cart() ) {
	return;
}

if ( ! empty( $breadcrumb ) ) {

	echo $args['wrap_before'];

	// Standalone icon badge - decorative, always first, not tied to
	// any single breadcrumb item (all crumbs including "Home" render
	// as plain text below).
	echo '<span class="woocommerce-breadcrumb__icon" aria-hidden="true">';
	echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11.5L12 4l9 7.5"/><path d="M5.5 10v9a1 1 0 0 0 1 1H9a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h2.5a1 1 0 0 0 1-1v-9"/></svg>';
	echo '</span>';

	foreach ( $breadcrumb as $key => $crumb ) {

		echo $args['before'];

		if ( ! empty( $crumb[1] ) && sizeof( $breadcrumb ) !== $key + 1 ) {
			// Linked breadcrumb item - plain text link (Home included)
			echo '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>';
		} else {
			// Last item (current page) - truncate to first 2 words + ellipsis if longer
			$full_title = $crumb[0];
			$words      = explode( ' ', $full_title );
			$short      = count( $words ) > 2
				? implode( ' ', array_slice( $words, 0, 2 ) )
				: $full_title;

			echo '<span class="breadcrumb-current" title="' . esc_attr( $full_title ) . '">' . esc_html( $short ) . '</span>';
		}

		echo $args['after'];

		// Chevron separator between items (not after the last one)
		if ( sizeof( $breadcrumb ) !== $key + 1 ) {
			echo '<span class="woocommerce-breadcrumb__sep" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg></span>';
		}
	}

	echo $args['wrap_after'];
}