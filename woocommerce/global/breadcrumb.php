<?php
/**
 * Shop breadcrumb
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/breadcrumb.php.
 * 
 * CUSTOM: Arrow-style breadcrumbs with home icon
 * Works on ALL WooCommerce pages EXCEPT cart page
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// HIDE BREADCRUMBS ON CART PAGE
if ( is_cart() ) {
	return;
}

if ( ! empty( $breadcrumb ) ) {
	
	// Empty separator - we're using CSS arrows instead
	$separator = '';

	echo $args['wrap_before'];

	foreach ( $breadcrumb as $key => $crumb ) {

		echo $args['before'];

		if ( ! empty( $crumb[1] ) && sizeof( $breadcrumb ) !== $key + 1 ) {
			// It's a linked breadcrumb item
			
			if ( $key === 0 ) {
				// First item (Home) - use SVG icon
				echo '<a href="' . esc_url( $crumb[1] ) . '">';
				echo '<svg class="breadcrumb-home-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-label="Home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>';
				echo '</a>';
			} else {
				// All other links - show the text
				echo '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>';
			}
			
		} else {
			// Last item (current page) - WRAP IN SPAN with special class
			echo '<span class="breadcrumb-current">' . esc_html( $crumb[0] ) . '</span>';
		}

		echo $args['after'];

		// No separator needed - CSS handles the arrows
		if ( sizeof( $breadcrumb ) !== $key + 1 ) {
			echo $separator;
		}
	}

	echo $args['wrap_after'];
}