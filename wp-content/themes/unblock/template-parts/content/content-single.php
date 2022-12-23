<?php

/**
 * The template for displaying the full post content
 * @package unBlock
 * @since 1.0.1
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

$unblock_post_style = apply_filters('unblock_post_style', get_theme_mod('unblock_post_style', 'classic-right'));
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(esc_attr($unblock_post_style)); ?>>

	<header id="post-header" class="entry-header">
		<?php
		// Full post title
		the_title('<h1 class="entry-title">', '</h1>');

		// Post Entry meta
		unblock_entry_meta();
		?>
	</header>

	<div class="row column-wrapper">

		<?php if (esc_attr($unblock_post_style) === 'center') {
			echo '<div class="col">';
		} elseif (esc_attr($unblock_post_style) === 'classic-left') {
			echo '<div class="col-md-8 order-md-2">';
		} else {
			echo '<div class="col-md-8">';
		}
		?>

		<?php // Full post thumbnail
		unblock_post_thumbnail(); ?>

		<div class="entry-content">
			<?php  // Get our full post content
			the_content();
			unblock_multipage_pagination();
			?>
		</div><!-- .entry-content -->

		<?php // Get our post footer
		unblock_entry_footer();

		// Author bio.       
		unblock_bio_info();

		// Action hook for any content placed after author bio
		do_action('unblock_after_author_bio');

		// If comments are open or we have at least one comment, load up the comment template.
		if (comments_open() || get_comments_number()) :
			comments_template();
		endif;

		// load the post navigation
		echo '<div id="post-navigation-wrapper"><div id="post-navigation-inner">';
		unblock_post_pagination();
		echo '</div></div>';

		?>


		<?php if (esc_attr($unblock_post_style) === 'center') {
			echo '</div>';
		} elseif (esc_attr($unblock_post_style) === 'classic-left') {
			echo '</div><aside id="left-sidebar" class="col-md-4 order-2 order-md-1">';
			get_sidebar();
			echo '</aside>';
		} else {
			echo '</div><aside id="right-sidebar" class="col-md-4">';
			get_sidebar();
			echo '</aside>';
		}
		?>

	</div>





</article><!-- #post-## -->