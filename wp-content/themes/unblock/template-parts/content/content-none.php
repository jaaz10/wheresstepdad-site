<?php

/**
 * No Content Template
 * Template part for displaying a message that posts cannot be found
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>

<header id="page-header">
	<h1 class="entry-title"><?php esc_html_e('Nothing Found', 'unblock'); ?></h1>

	<?php if (has_excerpt() && !is_archive() && !is_search() && !is_404()) : ?>
		<div id="page-excerpt">
			<?php the_excerpt();  ?>
		</div>
	<?php endif; ?>
</header>

<div class="row column-wrapper">
	<div class="col">
		<div class="entry-content">
			<?php if (is_home() && current_user_can('publish_posts')) : ?>

				<p>
					<?php esc_html_e('Ready to publish your first post?', 'unblock'); ?> <a class="nav-link" href="<?php echo esc_url(admin_url('post-new.php')); ?>"><?php esc_html_e('Get started here', 'unblock'); ?></a>.
				</p>

			<?php elseif (is_search()) : ?>

				<p><?php esc_html_e('Unfortunately, nothing matched your search terms. Please try again with different keywords.', 'unblock'); ?></p>
				<?php get_search_form(); ?>

			<?php else : ?>

				<p><?php esc_html_e('It seems we cannot find what you were wanting. Perhaps searching can help find what you are looking for?', 'unblock'); ?></p>
				<?php get_search_form(); ?>

			<?php endif; ?>
		</div>
	</div>
</div>