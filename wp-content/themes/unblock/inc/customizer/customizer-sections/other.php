<?php

/**
 * Customizer Other options
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

function unblock_customize_register_other($wp_customize)
{

	$wp_customize->add_section('unblock_other_settings', array(
		'title'      => esc_html__('Other Options', 'unblock'),
		'priority'   => 38,
		'capability' => 'edit_theme_options',
	));

	// Hide page header border
	$wp_customize->add_setting('unblock_hide_header_border', array(
		'default'           => 0,
		'sanitize_callback' => 'unblock_sanitize_checkbox',
	));

	$wp_customize->add_control(new unBlock_Toggle_Control($wp_customize, 'unblock_hide_header_border', array(
		'label'    => esc_html__('Hide the Page Header Border', 'unblock'),
		'description' => esc_html__('Show or hide the page header border line.', 'unblock'),
		'section'  => 'unblock_other_settings',
	)));

	// Hide back to top link
	$wp_customize->add_setting('unblock_hide_backtotop', array(
		'default'           => 0,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'unblock_sanitize_checkbox',
	));

	$wp_customize->add_control(new unBlock_Toggle_Control($wp_customize, 'unblock_hide_backtotop', array(
		'label'    => esc_html__('Hide the Back to Top Link', 'unblock'),
		'description' => esc_html__('Show or hide the footer Back To Top navigation link.', 'unblock'),
		'section'  => 'unblock_other_settings',
	)));

	// Hide banner captions
	$wp_customize->add_setting('unblock_hide_banner_captions', array(
		'default'           => 0,
		'sanitize_callback' => 'unblock_sanitize_checkbox',
	));

	$wp_customize->add_control(new unBlock_Toggle_Control($wp_customize, 'unblock_hide_banner_captions', array(
		'label'    => esc_html__('Hide Banner Captions', 'unblock'),
		'description' => esc_html__('Show or hide the banner sidebar image captions when using the image widget.', 'unblock'),
		'section'  => 'unblock_other_settings',
	)));

	// Hide edit link
	$wp_customize->add_setting('unblock_hide_edit', array(
		'default'           => 0,
		'transport'         => 'postMessage',
		'sanitize_callback' => 'unblock_sanitize_checkbox',
	));

	$wp_customize->add_control(new unBlock_Toggle_Control($wp_customize, 'unblock_hide_edit', array(
		'label'    => esc_html__('Hide Edit Link', 'unblock'),
		'description' => esc_html__('Show or hide the edit link from posts and pages. This will not show in the Customizer.', 'unblock'),
		'section'  => 'unblock_other_settings',
	)));
}
add_action('customize_register', 'unblock_customize_register_other');
