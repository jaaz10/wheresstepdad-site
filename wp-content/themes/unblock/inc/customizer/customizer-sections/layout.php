<?php

/**
 * Customizer Layout options
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

function unblock_customize_register_layout($wp_customize)
{

	$wp_customize->add_section('unblock_layout_settings', array(
		'title'      => esc_html__('Layout Options', 'unblock'),
		'priority'   => 21,
		'capability' => 'edit_theme_options',
	));

	// Header Layout
	$wp_customize->add_setting(
		'unblock_header_style',
		array(
			'default' => 'header1',
			'sanitize_callback' => 'unblock_sanitize_select',
		)
	);

	$wp_customize->add_control(
		new unBlock_Select_Control(
			$wp_customize,
			'unblock_header_style',
			array(

				'label'   => esc_html__('Header Style', 'unblock'),
				'section' => 'unblock_layout_settings',
				'choices' => unblock_header_style_choices()
			)
		)
	);

	// Blog Layout
	$wp_customize->add_setting(
		'unblock_blog_style',
		array(
			'default' => 'classic-right',
			'sanitize_callback' => 'unblock_sanitize_select',
		)
	);

	$wp_customize->add_control(
		new unBlock_Select_Control(
			$wp_customize,
			'unblock_blog_style',
			array(

				'label'   => esc_html__('Blog Style', 'unblock'),
				'section' => 'unblock_layout_settings',
				'choices' => unblock_blog_style_choices()
			)
		)
	);


	// Post Layout
	$wp_customize->add_setting(
		'unblock_post_style',
		array(
			'default' => 'classic-right',
			'sanitize_callback' => 'unblock_sanitize_select',
		)
	);

	$wp_customize->add_control(
		new unBlock_Select_Control(
			$wp_customize,
			'unblock_post_style',
			array(
				'label'   => esc_html__('Post Style', 'unblock'),
				'section' => 'unblock_layout_settings',
				'choices' => unblock_post_style_choices()
			)
		)
	);

	// Footer Layout
	$wp_customize->add_setting(
		'unblock_footer_style',
		array(
			'default' => 'footer1',
			'sanitize_callback' => 'unblock_sanitize_select',
		)
	);

	$wp_customize->add_control(
		new unBlock_Select_Control(
			$wp_customize,
			'unblock_footer_style',
			array(

				'label'   => esc_html__('Footer Style', 'unblock'),
				'section' => 'unblock_layout_settings',
				'choices' => unblock_footer_style_choices()
			)
		)
	);


	// Add Partial for single Layout
	$wp_customize->selective_refresh->add_partial('unblock_customize_partial_single_post', array(
		'selector'         => '.site-content',
		'settings'         => array(
			'unblock_single_layout',
		),
		'render_callback'  => 'unblock_customize_partial_single_content',
		'fallback_refresh' => false,
	));
}
add_action('customize_register', 'unblock_customize_register_layout');


// Render the blog content for the selective refresh partial.
function unblock_customize_partial_blog_content()
{
	while (have_posts()) {
		the_post();
		get_template_part('template-parts/content', get_post_format());
	}
}

// Render single posts partial
function unblock_customize_partial_single_post()
{
	while (have_posts()) {
		the_post();
		get_template_part('template-parts/content', 'single');
	}
}
