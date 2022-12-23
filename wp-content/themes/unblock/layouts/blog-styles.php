<?php

/**
 * Theme blog layouts: default, list
 * @package unBlock
 * @since 1.0.1
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}


/* BLOG LAYOUT
   ====================================================*/
if (!function_exists('unblock_blog_styles')) :
	function unblock_blog_styles()
	{

		$unblock_blog_style = apply_filters('unblock_blog_style', get_theme_mod('unblock_blog_style', 'classic-right'));

		// Start
		if (have_posts()) :

			switch (esc_attr($unblock_blog_style)) {

				case "list":
					echo '<div class="column-wrapper">';
					while (have_posts()) : the_post();
						// Get the post summary
						get_template_part('template-parts/content/content', $unblock_blog_style !== 'classic-right' ? $unblock_blog_style : '');
					endwhile;
					echo '</div>';
					// Blog navigation
					unblock_paging_nav();
					break;

				case "classic-left":
					echo '<div class="row column-wrapper"><div class="col-md-8 order-md-2">';
					while (have_posts()) : the_post();
						// Get the post summary							
						get_template_part('template-parts/content/content', $unblock_blog_style !== 'classic-right' ? $unblock_blog_style : '');
					endwhile;
					echo '</div><aside id="left-sidebar" class="col-md-4 order-2 order-md-1">';
					get_sidebar();
					echo '</aside></div>';

					// Blog navigation
					unblock_paging_nav();
					break;

				default:
					echo '<div class="row column-wrapper"><div class="col-md-8">';
					while (have_posts()) : the_post();
						// Get the post summary							
						get_template_part('template-parts/content/content', $unblock_blog_style !== 'classic-right' ? $unblock_blog_style : '');
					endwhile;
					echo '</div><aside id="right-sidebar" class="col-md-4">';
					get_sidebar();
					echo '</aside></div>';

					// Blog navigation
					unblock_paging_nav();
			}


		else :
			get_template_part('template-parts/content/content', 'none');
		endif;
		// End

	}
endif;
