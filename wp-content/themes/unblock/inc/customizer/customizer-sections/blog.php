<?php

/**
 * Blog Customizer Settings
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!function_exists('unblock_customize_register_blog')) :
	function unblock_customize_register_blog($wp_customize)
	{

		/** Blog Options */
		$wp_customize->add_section(
			'unblock_blog_options',
			array(
				'title'    => esc_html__('Blog Options', 'unblock'),
				'priority' => 22,
			)
		);

		// Heading - blog excerpts
		$wp_customize->add_control(new unBlock_Note_Control(
			$wp_customize,
			'unblock_blog_excerpts_note',
			array(
				'label' => esc_html__('Blog Excerpts', 'unblock'),
				'section' => 'unblock_blog_options',
				'priority' => 2,
				'settings' => array(),
			)
		));

		/** Blog Excerpt */
		$wp_customize->add_setting(
			'unblock_blog_excerpt',
			array(
				'default'           => 1,
				'sanitize_callback' => 'unblock_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			new unBlock_Toggle_Control(
				$wp_customize,
				'unblock_blog_excerpt',
				array(
					'section'     => 'unblock_blog_options',
					'priority' => 2,
					'label'	      => esc_html__('Enable Blog Excerpts', 'unblock'),
					'description' => esc_html__('Enable to show excerpts or disable to show full post content.', 'unblock'),
				)
			)
		);

		/** Excerpt Length */
		$wp_customize->add_setting(
			'unblock_excerpt_length',
			array(
				'default' => 35,
				'sanitize_callback' => 'unblock_sanitize_number_absint',
			)
		);

		$wp_customize->add_control(
			new unBlock_Slider_Control(
				$wp_customize,
				'unblock_excerpt_length',
				array(
					'section' => 'unblock_blog_options',
					'priority' => 3,
					'label' => esc_html__('Excerpt Length', 'unblock'),
					'description' => esc_html__('Automatically generated excerpt length (in words).', 'unblock'),
					'choices' => array(
						'min' => 10,
						'max' => 100,
						'step' => 5,
					)
				)
			)
		);


		// Add Partial for Blog Layout and Excerpt Length.
		$wp_customize->selective_refresh->add_partial('unblock_blog_content_partial', array(
			'selector' => '.site-main',
			'settings' => array(
				'unblock_blog_layout',
				'unblock_blog_excerpt',
				'unblock_excerpt_length',
			),
			'render_callback'  => 'unblock_customize_partial_blog_content',
			'fallback_refresh' => false,
		));
	}
endif;

add_action('customize_register', 'unblock_customize_register_blog');
