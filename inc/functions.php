<?php
/**
 * Functions.php
 *
 * @package  Theme_Customisations
 * @author   WooThemes
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Removing header top bar
add_action( 'wp', 'mhshop_removing_header_topbar', 99 );

function mhshop_removing_header_topbar() {
	if ( class_exists( 'Storefront_WooCommerce_Customiser' ) ) {
		$search = get_theme_mod( 'swc_header_search', true );
	} else {
		$search = false;
	}

	if ( true != $search ) {
		remove_action( 'storefront_header', 'storefront_product_search', 40 );
		remove_action( 'storefront_header', 'storefront_product_search', 10 );
	}
}