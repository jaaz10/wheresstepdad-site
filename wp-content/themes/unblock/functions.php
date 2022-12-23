<?php

/**
 * Functions main file
 * @package unblock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// This theme requires WordPress 4.7 or later.
if (version_compare($GLOBALS['wp_version'], '4.7', '<')) {
	require get_template_directory() . '/inc/back-compat.php';
}


/* THEME SETUP
@since unBlock 1.0.0
   ==================================================== */
// Sets up theme defaults and registers support for various WordPress features.
if (!function_exists('unblock_setup')) {
	function unblock_setup()
	{

		// Theme text domain
		load_theme_textdomain('unblock', get_template_directory() . '/languages');

		// Add RSS feed links to <head> for posts and comments.
		add_theme_support('automatic-feed-links');

		// Add document title tag to HTML <head>.
		add_theme_support('title-tag');

		// Add Page Excerpt support
		add_post_type_support('page', 'excerpt');

		// Add theme support for Secondary Logo.
		add_theme_support('custom-logo', array(
			'flex-width' => true,
		));

		// Add support for Block Styles.
		add_theme_support('wp-block-styles');
		add_theme_support('align-wide');

		// Block Editor color palette.
		$black      = '#000000';
		$grey     = '#777';
		$white     = '#ffffff';
		$primary = esc_attr(get_theme_mod('unblock_custom_primary_colour', '#d93a13'));
		$secondary    = esc_attr(get_theme_mod('unblock_custom_secondary_colour', '#9a4029'));
		$tertiary    = esc_attr(get_theme_mod('unblock_custom_tertiary_colour', '#df7257'));

		add_theme_support(
			'editor-color-palette',
			array(
				array(
					'name'  => esc_html__('Black', 'unblock'),
					'slug'  => 'black',
					'color' => $black,
				),

				array(
					'name'  => esc_html__('Grey', 'unblock'),
					'slug'  => 'grey',
					'color' => $grey,
				),

				array(
					'name'  => esc_html__('White', 'unblock'),
					'slug'  => 'white',
					'color' => $white,
				),

				// Default accent colours - Primary is yellow, Secondary is green, Tertiary is brown
				array(
					'name'  => esc_html__('Primary', 'unblock'),
					'slug'  => 'primary',
					'color' => $primary,
				),
				array(
					'name'  => esc_html__('Secondary', 'unblock'),
					'slug'  => 'secondary',
					'color' => $secondary,
				),
				array(
					'name'  => esc_html__('Tertiary', 'unblock'),
					'slug'  => 'tertiary',
					'color' => $tertiary,
				),
			)
		);

		// Add custom editor font sizes.
		add_theme_support(
			'editor-font-sizes',
			array(
				array(
					'name'      => esc_html__('Extra small', 'unblock'),
					'shortName' => esc_html_x('XS', 'Font size', 'unblock'),
					'size'      => 14,
					'slug'      => 'extra-small',
				),
				array(
					'name'      => esc_html__('Small', 'unblock'),
					'shortName' => esc_html_x('S', 'Font size', 'unblock'),
					'size'      => 16,
					'slug'      => 'small',
				),
				array(
					'name'      => esc_html__('Medium', 'unblock'),
					'shortName' => esc_html_x('M', 'Font size', 'unblock'),
					'size'      => 18,
					'slug'      => 'medium',
				),
				array(
					'name'      => esc_html__('Large', 'unblock'),
					'shortName' => esc_html_x('L', 'Font size', 'unblock'),
					'size'      => 24,
					'slug'      => 'large',
				),
				array(
					'name'      => esc_html__('Extra large', 'unblock'),
					'shortName' => esc_html_x('XL', 'Font size', 'unblock'),
					'size'      => 34,
					'slug'      => 'extra-large',
				),
				array(
					'name'      => esc_html__('Huge', 'unblock'),
					'shortName' => esc_html_x('XXL', 'Font size', 'unblock'),
					'size'      => 48,
					'slug'      => 'huge',
				),
				array(
					'name'      => esc_html__('Gigantic', 'unblock'),
					'shortName' => esc_html_x('XXXL', 'Font size', 'unblock'),
					'size'      => 60,
					'slug'      => 'gigantic',
				),
			)
		);

		// Enable support for Post Thumbnails, and declare two sizes.
		add_theme_support('post-thumbnails');
		set_post_thumbnail_size(1600, 9999, false);

		// Switch default core markup for search form, comment form, and comments to output valid HTML5
		add_theme_support('html5', array(
			'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
		));

		// Register navigation menus.
		register_nav_menus(
			array(
				'primary' 			=> esc_html__('Top Menu', 'unblock'),
				'footer' 			=> esc_html__('Footer Menu', 'unblock'),
				'social' 			=> esc_html__('Social Menu', 'unblock')
			)
		);

		// Additional support
		add_theme_support('customize-selective-refresh-widgets');
		add_theme_support('responsive-embeds');
		add_theme_support('custom-line-height');
		add_theme_support('experimental-link-color');
		add_theme_support('custom-spacing');
		add_theme_support('custom-units');
	}
}
add_action('after_setup_theme', 'unblock_setup');


/* SET CONTENT WIDTH
@since unBlock 1.0.0
   ==================================================== */
$GLOBALS['content_width'] = 1240;

if (!function_exists('unblock_content_width')) :
	function unblock_content_width()
	{
		$content_width = $GLOBALS['content_width'];
		// Check if the page has a sidebar.
		if (is_active_sidebar('left-sidebar') || is_active_sidebar('right-sidebar') || is_active_sidebar('blog-sidebar')) {
			$content_width = 760;
		}
		$GLOBALS['content_width'] = apply_filters('unblock_content_width', $content_width);
	}
endif;
add_action('template_redirect', 'unblock_content_width', 0);


/* ENQUEUE THEME SCRIPTS
@since unBlock 1.0.0
   ==================================================== */
if (!function_exists('unblock_enqueue_scripts')) :
	function unblock_enqueue_scripts()
	{

		$theme_version = wp_get_theme()->get('Version');

		// Check Comments
		if ((!is_admin()) && is_singular() && comments_open() && get_option('thread_comments')) {
			wp_enqueue_script('comment-reply');
		}

		// Skip to link
		wp_enqueue_script('unblock-skip-link-focus-fix', get_template_directory_uri() . '/assets/js/skip-link-focus-fix.js', array(), null, true);

		// Theme scripts
		wp_enqueue_script('unblock-scripts', get_template_directory_uri() . '/assets/js/theme-scripts.js', array('jquery'), $theme_version, true);
		wp_enqueue_script('bootstrap-js', get_template_directory_uri() . '/assets/js/bootstrap.js', array('jquery'), $theme_version, true);
	}
endif;
add_action('wp_enqueue_scripts', 'unblock_enqueue_scripts');


/* ENQUEUE THEME STYLES
@since unBlock 1.0.0
   ==================================================== */
if (!function_exists('unblock_enqueue_styles')) :
	function unblock_enqueue_styles()
	{

		$theme_version = wp_get_theme()->get('Version');

		// Block styles - Front-end
		wp_enqueue_style('block-styles-css', get_template_directory_uri() . '/assets/css/block-styles.css', array(), null);

		// Bootstrap Icons
		wp_enqueue_style('bootstrap-icons', get_template_directory_uri() . '/assets/css/bootstrap-icons.css', array(), null);

		// Bootstrap styles
		wp_enqueue_style('bootstrap-reboot', get_template_directory_uri() . '/assets/css/bootstrap-reboot.css', array(), null);
		wp_enqueue_style('bootstrap-grid', get_template_directory_uri() . '/assets/css/bootstrap-grid.css', array(), null);

		// Load our primary stylesheet.
		wp_enqueue_style('unblock-style', get_stylesheet_uri(), array(), $theme_version);

		// Add customizer css.
		wp_add_inline_style('unblock-style', unblock_internal_styles());
	}
endif;
add_action('wp_enqueue_scripts', 'unblock_enqueue_styles', 10);


/* EDITOR STYLES FOR THE CLASSIC EDITOR
@since unBlock 1.0.0
   ==================================================== */
if (!function_exists('unblock_classic_editor_styles')) :
	function unblock_classic_editor_styles()
	{
		$classic_editor_styles = array(
			'/assets/css/editor-classic.css',
		);
		add_editor_style($classic_editor_styles);
	}
endif;
add_action('init', 'unblock_classic_editor_styles');


/* ENQUEUE BLOCK EDITOR STYLES
 @since unBlock 1.0.0
   ==================================================== */
if (!function_exists('unblock_block_editor_styles')) :
	function unblock_block_editor_styles()
	{

		$theme_version = wp_get_theme()->get('Version');

		// Block styles.
		wp_enqueue_style('unblock-block-editor-style', get_template_directory_uri() . '/assets/css/editor-blocks.css', array(), $theme_version);
	}
endif;
add_action('enqueue_block_editor_assets', 'unblock_block_editor_styles', 10);


/* INCLUDES - ADDITIONAL FUNCTIONS & CLASSES
@since unBlock 1.0.0
   ==================================================== */
// Function files
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/internal-styles.php';
require get_template_directory() . '/inc/sidebars.php';

// Classes
require get_template_directory() . '/classes/class-unblock-comment-walker.php';
require get_template_directory() . '/classes/class-unblock-svg-icons.php';

// Customizer
require get_template_directory() . '/inc/customizer/custom-controls/custom-control.php';
require get_template_directory() . '/inc/customizer/customizer.php';

// Load theme structure
require get_template_directory() . '/layouts/header-styles.php';
require get_template_directory() . '/layouts/blog-styles.php';
require get_template_directory() . '/layouts/post-styles.php';
require get_template_directory() . '/layouts/footer-styles.php';
