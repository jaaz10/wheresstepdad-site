<?php

/**
 * Upgrade Theme Info and offer
 * @package unBlock
 * @since 1.0.1
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// Load the premium upgrade info when using the free version
function unblock_customizer_unblock_upgrade($wp_customize)
{

	$wp_customize->add_section('unblock_upgrade', array(
		'title'       => esc_html__('unBlock Pro Version Features', 'unblock'),
		'priority'    => 5,
	));

	/** Important Links */
	$wp_customize->add_setting(
		'unblock_upgrade_theme',
		array(
			'default' => '',
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	$unblock_upgrade = '<div class="upgrade-pro">';
	$unblock_upgrade .= '<p class="rp-discount">';
	$unblock_upgrade .= esc_html__('Save $10 (Limited Time Offer!) if you decide to upgrade to the Pro (Plugin) version with this discount code on checkout: ', 'unblock');
	$unblock_upgrade .= '<span class="rp-discount-code">';
	$unblock_upgrade .= 'UNBLOCK10';
	$unblock_upgrade .= '</span></p>';
	$unblock_upgrade .= '<p class="rp-pro-title">';
	$unblock_upgrade .= esc_html__('Pro Features: ', 'unblock');
	$unblock_upgrade .= '</p><ul class="rp-pro-list">';
	$unblock_upgrade .= '<li>' . esc_html('&bull; 3 Header Styles', 'unblock') . '</li>';
	$unblock_upgrade .= '<li>' . esc_html('&bull; 6 Blog Styles', 'unblock') . '</li>';
	$unblock_upgrade .= '<li>' . esc_html('&bull; 4 Post Styles', 'unblock') . '</li>';
	$unblock_upgrade .= '<li>' . esc_html('&bull; 3 Footer Styles', 'unblock') . '</li>';
	$unblock_upgrade .= '<li>' . esc_html('&bull; Custom Blog Intro', 'unblock') . '</li>';
	$unblock_upgrade .= '<li>' . esc_html('&bull; Customizable Text Labels', 'unblock') . '</li>';
	$unblock_upgrade .= '<li>' . esc_html('&bull; Custom Excerpt Sizing', 'unblock') . '</li>';
	$unblock_upgrade .= '<li>' . esc_html('&bull; Auto Create Featured Image Thumbnails', 'unblock') . '</li>';
	$unblock_upgrade .= '<li>' . esc_html('&bull; Make All Images Black &amp; White', 'unblock') . '</li>';
	$unblock_upgrade .= '<li>' . esc_html('&bull; Customized Mailchimp Signup', 'unblock') . '</li>';
	$unblock_upgrade .= '<li>' . esc_html('&bull; Customized Contact 7 Form', 'unblock') . '</li>';
	$unblock_upgrade .= '<li>' . esc_html('&bull; Related Posts w/Thumbnails', 'unblock') . '</li>';
	$unblock_upgrade .= '<li>' . esc_html('&bull; Author Widget w/Thumbnail', 'unblock') . '</li>';
	$unblock_upgrade .= '<li>' . esc_html('&bull; Comments Widget w/Thumbnails', 'unblock') . '</li>';
	$unblock_upgrade .= '<li>' . esc_html('&bull; Show/Hide Blog &amp; Post Elements', 'unblock') . '</li>';
	$unblock_upgrade .= '<li>' . esc_html('&bull; Disable Gutenberg Theme Styles', 'unblock') . '</li>';
	$unblock_upgrade .= '<li>' . esc_html('...and much more!', 'unblock') . '</li></ul>';
	$unblock_upgrade .= esc_html__('Even though the FREE version of unBlock offers a lot, you may want to opt-in for more features.', 'unblock');
	$unblock_upgrade .= '<p>';
	$unblock_upgrade .= sprintf(__('Limited Time: %1$sSave $10 Now.%2$s', 'unblock'),  '<a class="rp-get-pro button" href="' . esc_url('https://www.roughpixels.com/blogging-themes/unblock/') . '" target="_blank">', '</a>');
	$unblock_upgrade .= '</p></div>';


	$wp_customize->add_control(
		new unBlock_Note_Control(
			$wp_customize,
			'unblock_upgrade_theme',
			array(
				'section'     => 'unblock_upgrade',
				'label'	      => esc_html__('Premium Offer', 'unblock'),
				'description' => $unblock_upgrade
			)
		)
	);
}

add_action('customize_register', 'unblock_customizer_unblock_upgrade', 10);
