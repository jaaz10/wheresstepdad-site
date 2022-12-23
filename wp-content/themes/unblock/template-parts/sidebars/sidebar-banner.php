<?php

/**
 * The template for displaying the banner sidebar
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!is_active_sidebar('banner'))
	return;
// If we get this far, we have widgets. Let do this.
?>

<aside id="banner-sidebar" class="widget-area">
	<?php dynamic_sidebar('banner'); ?>
</aside>