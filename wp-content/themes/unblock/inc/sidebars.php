<?php

/**
 * Register theme sidebars
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

function unblock_widgets_init()
{

	register_sidebar(array(
		'name' => esc_html__('Blog Sidebar', 'unblock'),
		'id' => 'blog-sidebar',
		'description' => esc_html__('Sidebar for your blog and archives.', 'unblock'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));
	register_sidebar(array(
		'name' => esc_html__('Page Right Sidebar', 'unblock'),
		'id' => 'right-sidebar',
		'description' => esc_html__('Right sidebar for your pages.', 'unblock'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));
	register_sidebar(array(
		'name' => esc_html__('Page Left Sidebar', 'unblock'),
		'id' => 'left-sidebar',
		'description' => esc_html__('Left sidebar for your pages.', 'unblock'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => esc_html__('Banner', 'unblock'),
		'id' => 'banner',
		'description' => esc_html__('Banner sidebar for images and sliders.', 'unblock'),
		'before_widget' => '<div id="%1$s" class="banner widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2>',
	));

	register_sidebar(array(
		'name'          => esc_html__('Bottom 1', 'unblock'),
		'id'            => 'bottom1',
		'description'   => esc_html__('First sidebar of the bottom group located above the footer area.', 'unblock'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => esc_html__('Bottom 2', 'unblock'),
		'id'            => 'bottom2',
		'description'   => esc_html__('Second sidebar of the bottom group located above the footer area.', 'unblock'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => esc_html__('Bottom 3', 'unblock'),
		'id'            => 'bottom3',
		'description'   => esc_html__('Third  sidebar of the bottom group located above the footer area.', 'unblock'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => esc_html__('Bottom 4', 'unblock'),
		'id'            => 'bottom4',
		'description'   => esc_html__('Fourth sidebar of the bottom group located above the footer area.', 'unblock'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));
}
add_action('widgets_init', 'unblock_widgets_init');


/*----------------------------------------------------------------------------------------------- 
	Grouped Sidebars - Bottom
	This will add classes based on how many sidebars are active.
	@since 1.0.1
--------------------------------------------------------------------------------------------------- */
function unblock_bottom_group()
{
	$count = 0;
	if (is_active_sidebar('bottom1'))
		$count++;
	if (is_active_sidebar('bottom2'))
		$count++;
	if (is_active_sidebar('bottom3'))
		$count++;
	if (is_active_sidebar('bottom4'))
		$count++;
	$class = '';
	switch ($count) {
		case '1':
			$class = 'col-lg-12 widget-area';
			break;
		case '2':
			$class = 'col-lg-6 widget-area';
			break;
		case '3':
			$class = 'col-lg-4 widget-area';
			break;
		case '4':
			$class = 'col-sm-12 col-md-6 col-lg-3 widget-area';
			break;
	}
	if ($class)
		echo 'class="' . esc_attr($class) . '"';
}
