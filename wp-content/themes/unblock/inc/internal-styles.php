<?php

/**
 * Dynamic Internal Styles
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

/* RESET CUSTOM STYLES CACHE
 @since 1.0.1
   ==================================================== */
if (!function_exists('unblock_reset_inline_style_cache')) :
  function unblock_reset_inline_style_cache()
  {
    delete_transient('unblock_inline_style');
  }
endif;
add_action('customize_save_after', 'unblock_reset_inline_style_cache');


/* INTERNAL STYLE
 @since 1.0.1
   ==================================================== */
if (!function_exists('unblock_internal_styles')) :
  function unblock_internal_styles()
  {
    if (is_customize_preview())
      return unblock_get_inline_style();

    $unblock_internal_styles = get_transient('unblock_inline_style');

    if ($unblock_internal_styles === false) {
      $unblock_internal_styles = unblock_get_inline_style();
      set_transient('unblock_inline_style', esc_attr($unblock_internal_styles));
    }

    if (is_singular() && comments_open()) {

      $unblock_internal_styles .= '
        .comment-list .bypostauthor .fn:after {
          content: "- ' . esc_attr('Author', 'unblock') . '";
        }';
    }

    return wp_kses_post($unblock_internal_styles);
  }
endif;


/* PRESET INTERNAL STYLES
 @since 1.0.0
   ====================================================   */
if (!function_exists('unblock_preset_css')) :
  function unblock_preset_css()
  {

    $unblock_presets = get_theme_mod('unblock_presets');
    if ($unblock_presets && $unblock_presets !== 'preset1') {
      echo '<style type="text/css" media="all">
		:root {', unblock_colour_presets(), '	}
		</style>';
    }
  }
endif;
add_action('wp_head', 'unblock_preset_css', 12);


/* CUSTOMIZER INTERNAL STYLES
 @since 1.0.0
   ==================================================== */
if (!function_exists('unblock_get_inline_style')) :
  function unblock_get_inline_style()
  {
    $css = '';

    // Site title colour
    $unblock_site_title_colour = get_theme_mod('unblock_site_title_colour');

    if ($unblock_site_title_colour && $unblock_site_title_colour !== '#000') {
      $css .= '
        #site-title a,
		#site-title a:visited {
          color: ' . esc_attr($unblock_site_title_colour) . ';
        }';
    }

    // Site tagline colour
    $unblock_tagline_colour = get_theme_mod('unblock_tagline_colour');

    if ($unblock_tagline_colour && $unblock_tagline_colour !== '#000') {
      $css .= '
        #site-description {
          color: ' . esc_attr($unblock_tagline_colour) . ';
        }';
    }

    // Logo width
    $unblock_logo_width = get_theme_mod('unblock_logo_width');

    if ($unblock_logo_width && $unblock_logo_width !== '2') {
      $css .= '
        :root {
          --unblock-logo-size:' . floatval(esc_attr($unblock_logo_width)) . 'rem;
        }';
    }

    // Site title font size
    $site_title_font_size = get_theme_mod('site_title_font_size');

    if ($site_title_font_size && $site_title_font_size !== '2') {
      $css .= '
        :root {
          --unblock-site-title-size: ' . floatval(esc_attr($site_title_font_size)) . 'rem;
        }';
    }

    // Headings border
    $unblock_hide_header_border = get_theme_mod('unblock_hide_header_border');

    if ($unblock_hide_header_border && $unblock_hide_header_border !== false) {
      $css .= '
        .entry-title::after {
          content: none;
		  padding: 0;
        }';
    }

    // Headings transform text style
    $unblock_heading_transform_style = get_theme_mod('unblock_heading_transform_style');

    if ($unblock_heading_transform_style && $unblock_heading_transform_style !== 'uppercase') {
      $css .= '
        #page-header .entry-title, 
		#post-header .entry-title {
          text-transform: ' . esc_attr($unblock_heading_transform_style) . ';
        }';
    }

    // Heading excerpt transform text style
    $unblock_heading_excerpt_transform_style = get_theme_mod('unblock_heading_excerpt_transform_style');

    if ($unblock_heading_excerpt_transform_style && $unblock_heading_excerpt_transform_style !== 'uppercase') {
      $css .= '
        #page-excerpt, 
		#page-intro, 
		#archive-description {
          text-transform: ' . esc_attr($unblock_heading_excerpt_transform_style) . ';
        }';
    }

    // Headings font size
    $unblock_heading_font_size = get_theme_mod('unblock_heading_font_size');

    if ($unblock_heading_font_size && $unblock_heading_font_size !== '5.25') {
      $css .= '
        #page-header .entry-title, 
		#post-header .entry-title {
          font-size: ' . floatval(esc_attr($unblock_heading_font_size)) . 'rem;
        }';
    }

    // Primary colour
    $unblock_custom_primary_colour = get_theme_mod('unblock_custom_primary_colour');

    if ($unblock_custom_primary_colour && $unblock_custom_primary_colour !== '#d93a13') {
      $css .= '
	  :root {
		  --unblock-colour-primary: ' . esc_attr($unblock_custom_primary_colour) . ' !important;
        }';
    }

    // Secondary colour
    $unblock_custom_secondary_colour = get_theme_mod('unblock_custom_secondary_colour');

    if ($unblock_custom_secondary_colour && $unblock_custom_secondary_colour !== '#592e23') {
      $css .= '
	  :root {
		  --unblock-colour-secondary:' . esc_attr($unblock_custom_secondary_colour) . ' !important;
        }';
    }

    // Content area colors
    $unblock_content_bg_colour = get_theme_mod('unblock_content_bg_colour');

    if ($unblock_content_bg_colour && $unblock_content_bg_colour !== '#ffffff') {
      $css .= '
        body, .site-content, 
        .site-footer, 
        .offcanvas, 
        .topnav,
        .offcanvas .nav-menu .sub-menu, 
        .offcanvas .nav-menu .children, 
        .topnav .nav-menu .sub-menu, 
        .topnav .nav-menu .children {
          background-color: ' . esc_attr($unblock_content_bg_colour) . ';
        }
		
		@media (max-width: 991px) {
				.offcanvas .nav-menu, .topnav .nav-menu,
				.offcanvas .nav-menu .sub-menu, .topnav .nav-menu .sub-menu {
					background-color: ' . esc_attr($unblock_content_bg_colour) . ';
				}
			}';
    }

    // Body text colour
    $unblock_content_area_text_colour = get_theme_mod('unblock_content_area_text_colour');

    if ($unblock_content_area_text_colour && $unblock_content_area_text_colour !== '#646464') {
      $css .= '
        body {
          color: ' . esc_attr($unblock_content_area_text_colour) . ';
        }';
    }

    // Secondary text colour
    $unblock_secondary_text_colour = get_theme_mod('unblock_secondary_text_colour');

    if ($unblock_secondary_text_colour && $unblock_secondary_text_colour !== '#7c7c7c') {
      $css .= '
        .tags-list a, 
		.tagcloud a, 
		.post-cats a, 
		.post-meta, 
		.post-date, 
		.comment-meta,
		.post-navigation a > .nav-meta, 
		.wp-caption-text, 
		.entry-caption,
		.entry-meta,
		.entry-meta a,
		.entry-meta a:visited,
		.form-text {
          color: ' . esc_attr($unblock_secondary_text_colour) . ';
        }';
    }

    // Primary menu link colour
    $unblock_nav_colour = get_theme_mod('unblock_nav_colour');

    if ($unblock_nav_colour && $unblock_nav_colour !== '#000000') {
      $css .= '
        #mainmenu a {
          color: ' . hex2rgba(esc_attr($unblock_nav_colour), 0.7, true) . ';
        }
		#mainmenu a:focus,
        #mainmenu a:hover	{
          color: ' . hex2rgba(esc_attr($unblock_nav_colour), 1, true) . ';
        }';
    }

    // Mobile menu  line separators
    $unblock_mobile_line_separators = get_theme_mod('unblock_mobile_line_separators');

    if ($unblock_mobile_line_separators && $unblock_mobile_line_separators !== '#ededed') {
      $css .= '
        @media (max-width: 991px) {
			#mainmenu li,
			#mainmenu .sub-menu {
			  border-color: ' . hex2rgba(esc_attr($unblock_mobile_line_separators), 0.08, true) . '
			}
						
			#mainmenu a>span::before {
				background-color: ' . hex2rgba(esc_attr($unblock_mobile_line_separators), 0.15, true) . '
			}
			
		}';
    }

    // Headings colour
    $unblock_headings_colour = get_theme_mod('unblock_headings_colour');

    if ($unblock_headings_colour && $unblock_headings_colour !== '#222222') {
      $css .= '
        h1, h2, h3, h4, h5, h6, h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {
          color: ' . esc_attr($unblock_headings_colour) . ';
        }
        h1 a:focus,  h2 a:focus,  h3 a:focus,  h4 a:focus,  h5 a:focus,  h6 a:focus, h1 a:hover,  h2 a:hover,  h3 a:hover,  h4 a:hover,  h5 a:hover, h6 a:hover {
          color: ' . hex2rgba(esc_attr($unblock_headings_colour), 0.7, true) . '
        }';
    }


    // Content links
    $unblock_content_links = get_theme_mod('unblock_content_links');

    if ($unblock_content_links && $unblock_content_links !== '#d93a13') {
      $css .= '
      .page-content a, 
      .entry-summary a, 
      .entry-content a:not(.wp-block-button__link), 
      .author-content a, .comment-content a, 
      .textwidget a, 
      .comment-navigation a, 
      .pingback .comment-body>a, 
      .comment-meta a, 
      .logged-in-as a, 
      .widget_calendar a, 
      .entry-content .wp-block-calendar tfoot a {
            color: ' . esc_attr($unblock_content_links) . ' !important;
      }';
    }

    // Banner caption background
    $unblock_banner_caption = get_theme_mod('unblock_banner_caption');

    if ($unblock_banner_caption && $unblock_banner_caption !== '#d93a13') {
      $css .= '
        .widget_media_image figcaption, #banner-sidebar figcaption {
          background-color: ' . hex2rgba(esc_attr($unblock_banner_caption), 0.8, true) . ' !important;
        }';
    }

    // Banner caption text
    $unblock_banner_caption_text_color = get_theme_mod('unblock_banner_caption_text_color');

    if ($unblock_banner_caption_text_color && $unblock_banner_caption_text_color !== '#ffffff') {
      $css .= '
        .widget_media_image figcaption, #banner-sidebar figcaption {
          color: ' . esc_attr($unblock_banner_caption_text_color) . ';
        }';
    }


    // Bottom sidebar bg
    $unblock_bottom_sidebar_bg = get_theme_mod('unblock_bottom_sidebar_bg');

    if ($unblock_bottom_sidebar_bg && $unblock_bottom_sidebar_bg !== '#df7257') {
      $css .= '
        #bottom-sidebar {
          background: ' . esc_attr($unblock_bottom_sidebar_bg) . ' !important;
        }';
    }

    // Bottom sidebar text
    $unblock_bottom_sidebar_text = get_theme_mod('unblock_bottom_sidebar_text');

    if ($unblock_bottom_sidebar_text && $unblock_bottom_sidebar_text !== '#ffffff') {
      $css .= '
        #bottom-sidebar, 
		#bottom-sidebar .widget-title, 
		#bottom-sidebar a {
          color: ' . hex2rgba(esc_attr($unblock_bottom_sidebar_text), 1, true) . ';
        }';
    }




    // Pagination background
    $unblock_pagination_bg = get_theme_mod('unblock_pagination_bg');

    if ($unblock_pagination_bg && $unblock_pagination_bg !== '#f2f2f2') {
      $css .= '
        :root {
          --unblock-pagination-bg: ' . esc_attr($unblock_pagination_bg) . ';
        }';
    }

    // Pagination numbers
    $unblock_pagination_numbers = get_theme_mod('unblock_pagination_numbers');

    if ($unblock_pagination_numbers && $unblock_pagination_numbers !== '#333333') {
      $css .= '
        :root {
          --unblock-pagination-text: ' . esc_attr($unblock_pagination_numbers) . ';
        }';
    }

    // Hide banner captions
    $unblock_hide_banner_captions = get_theme_mod('unblock_hide_banner_captions');

    if ($unblock_hide_banner_captions && $unblock_hide_banner_captions !== false) {
      $css .= "
        #banner-sidebar .wp-caption-text {
          display: none;
        }";
    }

    $custom_css = get_theme_mod('unblock_custom_css');

    if ($custom_css) {
      $css .= wp_filter_nohtml_kses($custom_css);
    }

    return $css;
  }
endif;
