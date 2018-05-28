<?php
/**
 * Plugin Name:       Homefront Theme Customizations
 * Description:       A handy little plugin to contain all theme customisation snippets. We do this in order to allow the theme to be updated without overwriting our customizations.
 * Plugin URI:        http://www.codersclan.com
 * Version:           1.0.0
 * Author:            Bishoy A.
 * Author URI:        https://bishoy.me/
 * Requires at least: 3.0.0
 * Tested up to:      4.4.2
 *
 * @package Theme_Customisations
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Theme_Customisations Class
 *
 * @class Theme_Customisations
 * @version	1.0.0
 * @since 1.0.0
 * @package	Theme_Customisations
 */
final class Theme_Customisations {

	/**
	 * Set up the plugin
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'theme_customisations_setup' ), -1 );
		require_once( 'inc/functions.php' );
	}

	/**
	 * Setup all the things
	 */
	public function theme_customisations_setup() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_gulp_hashed_styles' ), 999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_gulp_hashed_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'vendor_assets' ) );
		add_filter( 'template_include',   array( $this, 'theme_customisations_template' ), 11 );
		add_filter( 'wc_get_template',    array( $this, 'theme_customisations_wc_get_template' ), 11, 5 );
		add_action( 'homepage', array( $this, 'dna_product_categories'),    80 );
		add_action( 'homepage', array( $this, 'accessories_products'),    90 );
	}

	/**
	 * Enqueue here vendor css/js files
	 * @return void
	 */
	public function vendor_assets() {
		// wp_enqueue_script( 'vendor-script', plugins_url( '/assets/dist/vendor/js/scripts.js', __FILE__ ), array(), null, true );
	}

	/**
	 * Look in this plugin for template files first.
	 * This works for the top level templates (IE single.php, page.php etc). However, it doesn't work for
	 * template parts yet (content.php, header.php etc).
	 *
	 * Relevant trac ticket; https://core.trac.wordpress.org/ticket/13239
	 *
	 * @param  string $template template string.
	 * @return string $template new template string.
	 */
	public function theme_customisations_template( $template ) {
		if ( file_exists( untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' . basename( $template ) ) ) {
			$template = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' . basename( $template );
		}

		return $template;
	}

	/**
	 * Look in this plugin for WooCommerce template overrides.
	 *
	 * For example, if you want to override woocommerce/templates/cart/cart.php, you
	 * can place the modified template in <plugindir>/templates/woocommerce/cart/cart.php
	 *
	 * @param string $located is the currently located template, if any was found so far.
	 * @param string $template_name is the name of the template (ex: cart/cart.php).
	 * @return string $located is the newly located template if one was found, otherwise
	 *                         it is the previously found template.
	 */
	public function theme_customisations_wc_get_template( $located, $template_name, $args, $template_path, $default_path ) {
		$plugin_template_path = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/woocommerce/' . $template_name;

		if ( file_exists( $plugin_template_path ) ) {
			$located = $plugin_template_path;
		}

		return $located;
	}

	/**
	 * Enqueue hashed generated JS files
	 * @return void
	 */
	public function enqueue_gulp_hashed_scripts() {
		$dirJS  = new DirectoryIterator( untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/assets/dist/js');

		foreach ($dirJS as $file) {

			if (pathinfo($file, PATHINFO_EXTENSION) === 'js') {
			    $fullName = basename($file);
			    $name_arr = explode("-", $fullName, 2);
			    $name = $name_arr[0];
			    
			    switch($name) {

			        case 'app':
			            $deps = array('jquery');
			            break;

			        default:
			            $deps = null;               
			            break;

			    }

			    wp_enqueue_script( $name, plugins_url( '/assets/dist/js/' . $fullName, __FILE__ ), $deps, null, true );
				wp_localize_script( $name, 'mhshop_' . $name, array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

			}

		}
	}

	/**
	 * Enqueue hashed generated css files
	 * @return void
	 */
	public function enqueue_gulp_hashed_styles() {
		$dirCSS  = new DirectoryIterator( untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/assets/dist/css');

		foreach ($dirCSS as $file) {

			if (pathinfo($file, PATHINFO_EXTENSION) === 'css') {
			    $fullName = basename($file);
			    $name_arr = explode("-", $fullName, 2);
			    $name = $name_arr[0];

			    wp_enqueue_style( $name, plugins_url( '/assets/dist/css/' . $fullName, __FILE__ ), array(), null );
			}
		}
	}
	
	/**
	 * Display DNA Sub Categories
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @since  1.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function dna_product_categories( $args ) {
			$args = apply_filters( 'storefront_product_categories_args', array(
				'limit' 			=> 9,
				'columns' 			=> 3,
				'child_categories' 	=> 16,
				'orderby' 			=> 'name',
				'title'				=> __( 'DNA', 'storefront' ),
			) );

			$shortcode_content = storefront_do_shortcode( 'product_categories', apply_filters( 'storefront_product_categories_shortcode_args', array(
				'number'  => intval( $args['limit'] ),
				'columns' => intval( $args['columns'] ),
				'orderby' => esc_attr( $args['orderby'] ),
				'parent'  => esc_attr( $args['child_categories'] ),
			) ) );

			/**
			 * Only display the section if the shortcode returns product categories
			 */
			if ( false !== strpos( $shortcode_content, 'product-category' ) ) {

				echo '<section class="storefront-product-section storefront-product-categories" aria-label="' . esc_attr__( 'Product Categories', 'storefront' ) . '">';

				do_action( 'storefront_homepage_before_product_categories' );

				echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

				do_action( 'storefront_homepage_after_product_categories_title' );

				echo $shortcode_content;

				do_action( 'storefront_homepage_after_product_categories' );

				echo '</section>';

			}
		}
		
    /**
	 * Display All products in the Accessories category
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @since  1.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function accessories_products( $args ) {
			$args = apply_filters( 'storefront_product_category_args', array(
				'limit' 			=> 12,
				'columns' 			=> 4,
				'orderby' 			=> 'name',
				'category'          => 'accessories',
				'title'				=> __( 'Accessories', 'storefront' ),
			) );

			$shortcode_content = storefront_do_shortcode( 'product_category', apply_filters( 'storefront_product_category_shortcode_args', array(
				'number'  => intval( $args['limit'] ),
				'columns' => intval( $args['columns'] ),
				'orderby' => esc_attr( $args['orderby'] ),
				'category'  => esc_attr( $args['category'] ),
			) ) );

			/**
			 * Only display the section if the shortcode returns products
			 */
			 error_log(print_r($shortcode_content,true));
			if ( false !== strpos( $shortcode_content, 'product' ) ) {

				echo '<section class="storefront-product-section" aria-label="' . esc_attr__( 'Accessories Products', 'storefront' ) . '">';

				do_action( 'storefront_homepage_before_product_category' );

				echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

				do_action( 'storefront_homepage_after_product_category_title' );

				echo $shortcode_content;

				do_action( 'storefront_homepage_after_product_category' );

				echo '</section>';

		}
	}
} // End Class

/**
 * The 'main' function
 *
 * @return void
 */
function theme_customisations_main() {
	new Theme_Customisations();
}

/**
 * Initialise the plugin
 */
add_action( 'plugins_loaded', 'theme_customisations_main' );
