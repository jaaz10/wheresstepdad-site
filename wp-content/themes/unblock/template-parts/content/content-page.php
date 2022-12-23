<?php

/**
 * The page content template
 * This displays content for all page templates.
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header id="page-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>

		<?php if (has_excerpt() && !is_archive() && !is_search() && !is_404()) : ?>
			<div id="page-excerpt">
				<?php the_excerpt();  ?>
			</div>
		<?php endif; ?>
	</header>

	<div class="row column-wrapper">

		<?php // What page template is being used
		if (is_page_template(array('templates/template-full-width.php', 'templates/template-wide.php'))) {
			echo '<div class="col">';
		} elseif (is_page_template(array('templates/template-left.php'))) {
			echo '<div class="col-md-8 order-md-2">';
		} else {
			echo '<div class="col-md-8">';
		} ?>

		<?php //unblock_post_thumbnail(); 
		?>

		<div class="entry-content">
			<?php
			the_content();
			unblock_multipage_pagination();
			?>
		</div><!-- .entry-content -->

		<?php
		if (!esc_attr(get_theme_mod('unblock_hide_edit', 0))) {
			edit_post_link(esc_html('Edit This Page', 'unblock'), '<p class="post-edit_post_link"><i class="bi bi-pencil-square"></i>', '</p>');
		}
		?>

		<?php // What page template is being used
		if (is_page_template(array('templates/template-full-width.php', 'templates/template-wide.php'))) {
			echo '</div>';
		} elseif (is_page_template(array('templates/template-left.php'))) {
			echo '</div><aside id="left-sidebar" class="col-md-4 order-2 order-md-1">';
			get_sidebar();
			echo '</aside>';
		} elseif (basename(get_page_template()) === 'page.php') {
			echo '</div><aside id="right-sidebar" class="col-md-4">';
			get_sidebar();
			echo '</aside>';
		} ?>

	</div>

</article><!-- #post-## -->