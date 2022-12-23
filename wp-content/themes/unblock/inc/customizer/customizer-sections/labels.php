<?php

/**
 * Customizer Label Options
 * Register the label options section, settings and controls for Theme Customizer
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// Label Options
if (!function_exists('unblock_customize_register_label_options')) :
	function unblock_customize_register_label_options($wp_customize)
	{

		// Add Sections for label options.
		$wp_customize->add_section('unblock_label_options', array(
			'title'    => esc_html__('Label Options', 'unblock'),
			'priority' => 27,
		));

		/** Prefix Archive Page */
		$wp_customize->add_setting(
			'unblock_hide_prefix_archive',
			array(
				'default'           => false,
				'transport' => 'postMessage',
				'sanitize_callback' => 'unblock_sanitize_checkbox'
			)
		);

		$wp_customize->add_control(
			new unBlock_Toggle_Control(
				$wp_customize,
				'unblock_hide_prefix_archive',
				array(
					'section'     => 'unblock_label_options',
					'priority' => 1,
					'label'	      => esc_html__('Hide Prefix in Archive Pages', 'unblock'),
					'description' => esc_html__('Enable to hide the archive prefix labels from archive titles.', 'unblock'),
				)
			)
		);

		// Back to Top text
		$wp_customize->add_setting(
			'unblock_back_to_top_text',
			array(
				'default'           => esc_html__('Back to the Top', 'unblock'),
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage'
			)
		);

		$wp_customize->add_control(
			'unblock_back_to_top_text',
			array(
				'type'    => 'text',
				'section' => 'unblock_label_options',
				'label'   => esc_html__('Back To Top Text', 'unblock'),
			)
		);

		// Footer Copyright
		$wp_customize->add_setting('unblock_copyright', array(
			'sanitize_callback' => 'wp_kses_post',
			'transport' => 'postMessage'
		));

		$wp_customize->add_control('unblock_copyright', array(
			'type'    => 'text',
			'label'   => esc_html__('Copyright Name', 'unblock'),
			'description' => esc_html__('Enter your name, website name, or company name [ no html ].', 'unblock'),
			'priority' => 8,
			'section' => 'unblock_label_options',
		));
	}
endif;

add_action('customize_register', 'unblock_customize_register_label_options', 12);
