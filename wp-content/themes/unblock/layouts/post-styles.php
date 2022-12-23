<?php

/**
 * Theme full post layouts
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/* POST LAYOUT
   ====================================================*/
if (!function_exists('unblock_post_styles')) :
	function unblock_post_styles()
	{

		if (have_posts()) :

			// Start the loop.
			while (have_posts()) : the_post();

				// Action hook for any content placed before the full post
				do_action('unblock_before_full_post');

				// Get the post content
				get_template_part('template-parts/content/content', 'single');

				// Action hook for any content placed after the full post
				do_action('unblock_after_full_post');

			// End the loop.
			endwhile;

		endif;
	}
endif;
