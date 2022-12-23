<?php

/**
 * Theme Customizer main file
 * @package unblock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}


/* CUSTOMIZER MODIFICATIONS
 Customizer additions and adjustments.
@since 1.0.0
==================================================== */
function unblock_customize_register($wp_customize)
{

	// Modify Transport
	$wp_customize->get_setting('blogname')->transport = 'postMessage';
	$wp_customize->get_setting('blogdescription')->transport = 'postMessage';
	$wp_customize->get_setting('background_color')->transport = 'postMessage';
	$wp_customize->get_setting('background_image')->transport = 'postMessage';

	// Modify Controls
	$wp_customize->get_control('blogdescription')->label = esc_html__('Site Description', 'unblock');
	$wp_customize->get_control('blogdescription')->description = esc_html__('Short description of what your website is about.', 'unblock');
	$wp_customize->get_control('blogdescription')->priority = 10;
	$wp_customize->get_control('blogname')->priority = 0;

	// Modify Sections
	$wp_customize->get_section('colors')->panel = 'colors';
	$wp_customize->get_section('colors')->priority = 15;
	$wp_customize->get_section('colors')->title = esc_html__('Content Area', 'unblock');
}
add_action('customize_register', 'unblock_customize_register');


/* CUSTOMIZER INCLUDES
 Customizer additions
@since 1.0.0
==================================================== */

// Get the theme customizer sections
$unblock_panels = array('upgrade', 'site', 'layout', 'blog', 'color', 'other', 'labels', 'typography');
foreach ($unblock_panels as $panel) {
	require get_template_directory() . '/inc/customizer/customizer-sections/' . esc_attr($panel) . '.php';
}

// Add customizer stylesheet to the admin
function unblock_customizer_styles()
{
	wp_enqueue_style('admin-style', get_stylesheet_directory_uri() . '/assets/css/customizer.css');
}
add_action('admin_enqueue_scripts', 'unblock_customizer_styles');


// Theme Customizer preview reload changes asynchronously.
function unblock_customizer_preview_js()
{
	wp_enqueue_script('unblock-customizer', get_template_directory_uri() . '/assets/js/customizer-preview.js', array('customize-preview'), null, true);
}
add_action('customize_preview_init', 'unblock_customizer_preview_js');


/* CUSTOMIZER PRESET CHOICES
Set our colour preset options in the dropdown field
@since 1.0.0
==================================================== */
if (!function_exists('unblock_colour_preset_options')) :
	function unblock_colour_preset_options()
	{
		return apply_filters('unblock_colour_preset_options', array(
			'preset1' => get_template_directory_uri() . '/assets/images/preset1.png',
			'preset2' => get_template_directory_uri() . '/assets/images/preset2.png',
			'preset3'  => get_template_directory_uri() . '/assets/images/preset3.png',
			'preset4'  => get_template_directory_uri() . '/assets/images/preset4.png',
			'preset5'  => get_template_directory_uri() . '/assets/images/preset5.png',
			'preset6'  => get_template_directory_uri() . '/assets/images/preset6.png',
			'preset7'  => get_template_directory_uri() . '/assets/images/preset7.png',
			'preset8'  => get_template_directory_uri() . '/assets/images/preset8.png',
			'preset9'  => get_template_directory_uri() . '/assets/images/preset9.png',
			'preset10'  => get_template_directory_uri() . '/assets/images/preset10.png',
		));
	}
endif;

/* CUSTOMIZER HEADER CHOICES
Set our header layout options in the dropdown field
@since 1.0.0
==================================================== */
if (!function_exists('unblock_header_style_choices')) :
	function unblock_header_style_choices()
	{
		return apply_filters('unblock_header_style_choices', array(
			'header1'  => esc_html__('Header 1', 'unblock'),
			'header2'  => esc_html__('Header 2', 'unblock'),
		));
	}
endif;


/* CUSTOMIZER BLOG CHOICES
Set our blog layout options in the dropdown field
@since 1.0.0
==================================================== */
if (!function_exists('unblock_blog_style_choices')) :
	function unblock_blog_style_choices()
	{
		return apply_filters('unblock_blog_style_choices', array(
			'classic-right' => esc_html__('Classic Right Sidebar', 'unblock'),
			'classic-left' => esc_html__('Classic Left Sidebar', 'unblock'),
			'list' => esc_html__('List No Sidebars', 'unblock'),
		));
	}
endif;

/* CUSTOMIZER POST CHOICES
Set our post layout options in the dropdown field
@since 1.0.0
==================================================== */
if (!function_exists('unblock_post_style_choices')) :
	function unblock_post_style_choices()
	{
		return apply_filters('unblock_post_style_choices', array(
			'classic-right' => esc_html__('Classic Right Sidebar', 'unblock'),
			'classic-left' => esc_html__('Classic Left Sidebar', 'unblock'),
			'center' => esc_html__('Centered No Sidebars', 'unblock'),
		));
	}
endif;


/* CUSTOMIZER FOOTER CHOICES
Set our footer layout options in the dropdown field
@since 1.0.0
==================================================== */
if (!function_exists('unblock_footer_style_choices')) :
	function unblock_footer_style_choices()
	{
		return apply_filters('unblock_footer_style_choices', array(
			'footer1' => esc_html__('Footer 1', 'unblock'),
		));
	}
endif;

/* CUSTOMIZER TEXT TRANSFORM CHOICES
Set our text transformation dropdown field
@since 1.0.0
==================================================== */
if (!function_exists('unblock_text_transform_choices')) :
	function unblock_text_transform_choices()
	{
		return apply_filters('unblock_text_transform_choices', array(
			'uppercase'  => esc_html__('Uppercase', 'unblock'),
			'capitalize'  => esc_html__('Capitalize', 'unblock'),
			'lowercase'  => esc_html__('Lowercase', 'unblock'),
		));
	}
endif;

/* CUSTOMIZER SANITIZATION
 Sanitization of customizer functions
@since 1.0.0
==================================================== */

// Text area
if (!function_exists('unblock_textarea_sanitize')) :
	function unblock_textarea_sanitize($value)
	{
		if ($value) {
			$value = wp_unslash(
				wp_kses($value, array(
					'a' => array(
						'href' => array(),
						'title' => array(),
						'target' => array()
					),
					'b'      => array(),
					'strong' => array(),
					'em'     => array(),
					'i'      => array(),
					'img' => array(
						'src' => array(),
						'alt' => array(),
						'title' => array()
					),
					'span' => array(),
					'br' => array(),
					'p'  => array()
				))
			);
		}

		return $value;
	}
endif;

// Sanitization callback for checkbox  
if (!function_exists('unblock_sanitize_checkbox')) :
	function unblock_sanitize_checkbox($checked)
	{
		return ((isset($checked) && true == $checked) ? true : false);
	}
endif;


// Sanitization callback for radio  
if (!function_exists('unblock_sanitize_radio')) :
	function unblock_sanitize_radio($input, $setting)
	{
		$input = sanitize_key($input);
		$choices = $setting->manager->get_control($setting->id)->choices;
		return (array_key_exists($input, $choices) ? $input : $setting->default);
	}
endif;

// Sanitization callback for select  
if (!function_exists('unblock_sanitize_select')) :
	function unblock_sanitize_select($value)
	{
		if (is_array($value)) {
			foreach ($value as $key => $subvalue) {
				$value[$key] = esc_attr($subvalue);
			}
			return $value;
		}
		return esc_attr($value);
	}
endif;

// Sanitization callback for decimal numbers  
if (!function_exists('unblock_sanitize_number_decimal')) :
	function unblock_sanitize_number_decimal($number, $setting)
	{
		// Ensure $number is an absolute integer (whole number, zero or decimal).
		filter_var($number, FILTER_FLAG_ALLOW_FRACTION);
		// If the input is an absolute integer, return it; otherwise, return the default
		return ($number ? $number : $setting->default);
	}
endif;

// Sanitization callback for absolute numbers  
if (!function_exists('unblock_sanitize_number_absint')) :
	function unblock_sanitize_number_absint($number, $setting)
	{
		// Ensure $number is an absolute integer (whole number, zero or greater).
		$number = absint($number);
		// If the input is an absolute integer, return it; otherwise, return the default
		return ($number ? $number : $setting->default);
	}
endif;

// Sanitization callback for HEX colours  
if (!function_exists('unblock_sanitize_hex_colour')) :
	function unblock_sanitize_hex_colour($color)
	{
		if ('' === $color)
			return '';
		// 3 or 6 hex digits, or the empty string.
		if (preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color))
			return $color;
	}
endif;
