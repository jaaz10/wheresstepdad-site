<?php

/**
 * Customizer Typography options
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

function unblock_customize_register_typography($wp_customize)
{

	$wp_customize->add_section('unblock_typography_settings', array(
		'title'      => esc_html__('Typography Options', 'unblock'),
		'priority'   => 36,
		'capability' => 'edit_theme_options',
	));

	// Headings Transform
	$wp_customize->add_setting(
		'unblock_heading_transform_style',
		array(
			'default' => 'uppercase',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'unblock_sanitize_select',
		)
	);

	$wp_customize->add_control(
		new unBlock_Select_Control(
			$wp_customize,
			'unblock_heading_transform_style',
			array(

				'label'   => esc_html__('Headings Transform', 'unblock'),
				'section' => 'unblock_typography_settings',
				'choices' => unblock_text_transform_choices()
			)
		)
	);

	// Heading Excerpt Transform
	$wp_customize->add_setting(
		'unblock_heading_excerpt_transform_style',
		array(
			'default' => 'uppercase',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'unblock_sanitize_select',
		)
	);

	$wp_customize->add_control(
		new unBlock_Select_Control(
			$wp_customize,
			'unblock_heading_excerpt_transform_style',
			array(

				'label'   => esc_html__('Heading Excerpt Transform', 'unblock'),
				'section' => 'unblock_typography_settings',
				'choices' => unblock_text_transform_choices()
			)
		)
	);

	// Headings Font Size
	$wp_customize->add_setting('unblock_heading_font_size', array(
		'default'           => 5.25,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'unblock_sanitize_number_decimal',
	));

	$wp_customize->add_control(
		new unBlock_Slider_Control($wp_customize, 'unblock_heading_font_size', array(
			'section'	  => 'unblock_typography_settings',
			'label'		  => esc_html__('Headings Font Size', 'unblock'),
			'description' => esc_html__('Change the font size for page headings and titles.', 'unblock'),
			'choices'	  => array(
				'min' 	=> 1,
				'max' 	=> 6,
				'step'	=> 0.25,
			)
		))
	);
}
add_action('customize_register', 'unblock_customize_register_typography');
