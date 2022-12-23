<?php

/**
 * Theme Customizer - Site Identity tab
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

function unblock_customize_register_site($wp_customize)
{

	/** Add postMessage support for site title and description */
	$wp_customize->get_setting('blogname')->transport         = 'postMessage';
	$wp_customize->get_setting('blogdescription')->transport  = 'postMessage';
	$wp_customize->get_setting('background_color')->transport = 'refresh';
	$wp_customize->get_setting('background_image')->transport = 'refresh';


	// Show site title
	$wp_customize->add_setting('unblock_show_site_title',	array(
		'default' => true,
		'transport'  => 'postMessage',
		'sanitize_callback' => 'unblock_sanitize_checkbox',
	));

	$wp_customize->add_control(new unBlock_Toggle_Control(
		$wp_customize,
		'unblock_show_site_title',
		array(
			'label'    => esc_html__('Show the Site Title', 'unblock'),
			'description' => esc_html__('Show or hide the site title.', 'unblock'),
			'section'  => 'title_tagline',
		)
	));

	// Show site description
	$wp_customize->add_setting('unblock_show_site_desc', array(
		'default' => true,
		'transport'  => 'postMessage',
		'sanitize_callback' => 'unblock_sanitize_checkbox',
	));

	$wp_customize->add_control(new unBlock_Toggle_Control(
		$wp_customize,
		'unblock_show_site_desc',
		array(
			'label'    => esc_html__('Show the Site Description', 'unblock'),
			'description' => esc_html__('Show or hide the site tagline [description].', 'unblock'),
			'section'  => 'title_tagline',
		)
	));

	// Site title colour
	$wp_customize->add_setting('unblock_site_title_colour', array(
		'default'           => '#000',
		'transport' => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color'
	));

	$wp_customize->add_control(new WP_Customize_Color_Control(
		$wp_customize,
		'unblock_site_title_colour',
		array(
			'label'       => esc_html__('Site Title Colour', 'unblock'),
			'description' => esc_html__('Sets the site title colour in the sidebar area.', 'unblock'),
			'section'     => 'title_tagline',
		)
	));

	// Site tagline colour
	$wp_customize->add_setting('unblock_tagline_colour', array(
		'default'           => '#000',
		'transport' => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color'
	));

	$wp_customize->add_control(new WP_Customize_Color_Control(
		$wp_customize,
		'unblock_tagline_colour',
		array(
			'label'       => esc_html__('Site Tagline Colour', 'unblock'),
			'description' => esc_html__('Sets the site tagline [ description ] colour in the header area.', 'unblock'),
			'section'     => 'title_tagline',
		)
	));

	// Site Logo size
	$wp_customize->add_setting('unblock_logo_width', array(
		'default'           => 2,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'unblock_sanitize_number_decimal',
	));

	$wp_customize->add_control(
		new unBlock_Slider_Control($wp_customize, 'unblock_logo_width', array(
			'section'	  => 'title_tagline',
			'label'		  => esc_html__('Site Logo Size', 'unblock'),
			'description' => esc_html__('Change the site logo size for your sidebar column.', 'unblock'),
			'choices'	  => array(
				'min' 	=> 1,
				'max' 	=> 5,
				'step'	=> 0.25,
			)
		))
	);


	// Site Title Font Size for the custom header image
	$wp_customize->add_setting('site_title_font_size', array(
		'default'           => 2,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'unblock_sanitize_number_decimal',
	));

	$wp_customize->add_control(
		new unBlock_Slider_Control($wp_customize, 'site_title_font_size', array(
			'section'	  => 'title_tagline',
			'label'		  => esc_html__('Site Title Font Size', 'unblock'),
			'description' => esc_html__('Change the font size of your site title.', 'unblock'),
			'priority'    => 65,
			'choices'	  => array(
				'min' 	=> 1,
				'max' 	=> 5,
				'step'	=> 0.25,
			)
		))
	);
}
add_action('customize_register', 'unblock_customize_register_site');
