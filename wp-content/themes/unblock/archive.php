<?php

/**
 * The archive template file
 * Used to display blog archived pages like categories, tags, author posts, etc.
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

<header id="page-header">
	<?php
	echo '<h1 class="entry-title">' . esc_html('Blog', 'unblock') . '</h1>';
	the_archive_title('<h2 id="archive-title">', '</h2>');
	the_archive_description('<div id="archive-description">', '</div>');

	// action hook for any content after the archive title and description
	if (!is_category() && !is_author()) {
		do_action('unblock_after_archive_description');
	}
	?>

</header>

<?php
// Action hook for any content placed before posts
do_action('unblock_before_posts');

// Get our blog layout
unblock_blog_styles();

// Action hook for any content placed after posts
do_action('unblock_after_posts');
?>


<?php
get_footer();
