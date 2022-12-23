<?php

/**
 * The main template file
 * This is the most generic template file in a WordPress theme and one of the two required files for a theme (the other being style.css).
 * We modified it for this theme without a loop; exception to getting our blog layout.
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

		<?php // Action hook for the blog home page heading area
		do_action('unblock_blog_home_heading');

		// Action hook for any content placed before posts
		do_action('unblock_before_posts');

		// Get our blog layout
		unblock_blog_styles();

		// Action hook for any content placed after posts
		do_action('unblock_after_posts');
		?>

<?php
get_footer();
