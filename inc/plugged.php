<?php
/**
 * Plugged functions
 * Any functions declared here are overwriting counterparts from a plugin or Storefront core.
 *
 * @package homestore
 */

/**
 * Display the post header with a link to the single post
 * @since 1.0.0
 */
if ( ! function_exists( 'storefront_post_header' ) ) {
	function storefront_post_header() { ?>
		<header class="entry-header">
		<?php
		if ( is_single() ) {
			the_title( '<h1 class="entry-title" itemprop="name headline">', '</h1>' );
			storefront_posted_on();
		} else {

			the_title( sprintf( '<h1 class="entry-title" itemprop="name headline"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' );

			if ( 'post' == get_post_type() ) {
				storefront_posted_on();
			}
		}
		?>
		</header><!-- .entry-header -->
		<?php
	}
}
