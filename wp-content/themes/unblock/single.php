<?php

/**
 * Single - Full Post template
 * The template for displaying all single posts and attachments.
 * We modified this template for this theme without the loop.
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

	<?php // Get our full post layout
	unblock_post_styles(); ?>

<?php
get_footer();
