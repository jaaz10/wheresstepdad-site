<?php
/* 
* Template Name: Wide
* Template Post Type: page
* @package unBlock
* @since 1.0.0
*/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

	<?php
	// Start the loop.
	if (have_posts()) {
		while (have_posts()) : the_post();

			// Include the page content template.
			get_template_part('template-parts/content/content', 'page');

			// If comments are open or we have at least one comment, load up the comment template.
			if (comments_open() || get_comments_number()) :
				comments_template();
			endif;

		// End the loop.
		endwhile;
	}
	?>

<?php
get_footer();
