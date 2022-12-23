<?php

/**
 * The main sidebar template file
 * The left and right sidebars are determined by the template being used
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// Check if Sidebar has widgets.
if (
	!is_active_sidebar('left-sidebar')
	&& !is_active_sidebar('right-sidebar')
	&& !is_active_sidebar('blog-sidebar')
)
	return;


// Use the sidebar that relates to the page type being viewed
if (is_page_template(array('templates/template-left.php'))) {

	dynamic_sidebar('left-sidebar');
} elseif (is_home() || is_archive() || is_single()) {

	dynamic_sidebar('blog-sidebar');
} elseif (basename(get_page_template()) === 'page.php') {

	dynamic_sidebar('right-sidebar');

	// Skip to the blog sidebar for everything else.
} else {

	dynamic_sidebar('blog-sidebar');
}
