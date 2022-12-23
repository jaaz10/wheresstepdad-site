<?php

/**
 * The header for our theme
 * This is the template that displays all of the <head> section and everything up until <div id="site-content">
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php if (function_exists('wp_body_open')) {
		wp_body_open();
	}
	?>

	<div id="page" class="site">

		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content', 'unblock'); ?></a>

		<?php // Get our header
		unblock_header_styles();

		// Banners
		get_template_part('template-parts/sidebars/sidebar', 'banner');

		// Pages with feature image
		if (is_page() && has_post_thumbnail()) {
			unblock_post_thumbnail();
		}
		?>

		<div id="content" class="site-content">
			<main id="main" class="site-main">
				<div class="container px-4">
					<div class="row gx-5">
						<div class="col">