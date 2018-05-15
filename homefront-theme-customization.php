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
