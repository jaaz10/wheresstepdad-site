<?php

/**
 * Functions which enhance the theme by hooking into WordPress
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}


/* ADD BODY CLASSES
@since 1.0.0
   ==================================================== */
if (!function_exists('unblock_body_classes')) :
  function unblock_body_classes($classes)
  {

    $blog_style = apply_filters('unblock_blog_style', esc_attr(get_theme_mod('unblock_blog_style', 'classic-right')));
    $post_style = apply_filters('unblock_post_style', esc_attr(get_theme_mod('unblock_post_style', 'classic-right')));
    $footer_style = apply_filters('unblock_footer_style', esc_attr(get_theme_mod('unblock_footer_style', 'footer1')));

    // Page excerpt class
    if (is_page() && has_excerpt()) {
      $classes[] = 'has-excerpt';
    }

    // Adds a class of hfeed to non-singular pages.
    if (!is_singular()) {
      $classes[] = 'hfeed';
    }

    if (esc_attr(get_theme_mod('unblock_sticky_nav', 0))) {
      $classes[] = 'sticky-nav';
    }

    // Blog style classes
    if ($blog_style !== 'classic-right' && !is_singular()) {
      $classes[] = 'blog-' . esc_attr($blog_style);
    }

    // Post style classes
    if ($post_style !== 'classic-right' && is_singular()) {
      $classes[] = 'single-' . esc_attr($post_style);
    }

    // Check whether the current page is the left column template
    if (is_page_template(array('templates/template-left.php'))) {
      $classes[] = 'template-left';
    }

    // Check whether the current page is the full width template
    if (is_page_template(array('templates/template-full-width.php'))) {
      $classes[] = 'template-full';
    }

    // Check whether the current page is the full width template
    if (is_page_template(array('templates/template-wide.php'))) {
      $classes[] = 'template-wide';
    }

    // Footer style
    if ($footer_style !== 'footer1') {
      $classes[] = esc_attr($footer_style);
    }

    // Check for post thumbnail
    if (
      is_single() && has_post_thumbnail()
      || is_page() && has_post_thumbnail()
    ) {
      $classes[] = 'has-post-thumbnail';
    } else {
      $classes[] = 'no-post-thumbnail';
    }

    // Check whether we're in the customizer preview
    if (is_customize_preview()) {
      $classes[] = 'customizer-preview';
    }

    return $classes;
  }
endif;
add_filter('body_class', 'unblock_body_classes');


/* CONVERT HEX to RGBA
@since 1.0.0
   ==================================================== */
if (!function_exists('hex2rgba')) :
  function hex2rgba($color, $opacity = 1, $css = false)
  {
    if (empty($color))
      return;

    $color = str_replace('#', '', $color);

    if (strlen($color) == 6) {
      $r = hexdec($color[0] . $color[1]);
      $g = hexdec($color[2] . $color[3]);
      $b = hexdec($color[4] . $color[5]);
    } elseif (strlen($color) == 3) {
      $r = hexdec($color[0] . $color[0]);
      $g = hexdec($color[1] . $color[1]);
      $b = hexdec($color[2] . $color[2]);
    } else {
      return false;
    }

    $opacity = floatval($opacity);

    if ($css)
      return 'rgba( ' . esc_attr($r) . ', ' . esc_attr($g) . ', ' . esc_attr($b) . ', ' . esc_attr($opacity) . ' )';
    else
      return compact(esc_attr($r), esc_attr($g), esc_attr($b), esc_attr($opacity));
  }
endif;


/* ADD WELCOME TO THE BLOG HOMEPAGE
   ==================================================== */
if (!function_exists('unblock_welcome_intro')) :
  function unblock_welcome_intro($before = '', $after = '')
  {

    // Prints HTML with welcome text.
    $welcome_intro_page_id = absint(get_theme_mod('welcome_intro_page_id'));
    $output = '';

    if (!$welcome_intro_page_id)
      return;


    if (has_post_thumbnail($welcome_intro_page_id)) {
      $output .= sprintf('<div class="unblock-welcome-intro-thumbnail">%s</div>', get_the_post_thumbnail($welcome_intro_page_id, 'thumbnail'));
    }

    $welcome_intro_page = get_post($welcome_intro_page_id);
    $welcome_intro_title = apply_filters('the_title', $welcome_intro_page->post_title, $welcome_intro_page_id);

    if ($welcome_intro_title)
      $output .= sprintf('<h1 class="entry-title">%s</h1>', $welcome_intro_title);

    $welcome_intro_content = apply_filters('the_content', $welcome_intro_page->post_content);
    $welcome_intro_content = str_replace(']]>', ']]&gt;', $welcome_intro_content);

    if ($welcome_intro_content)
      $output .= sprintf('<div id="page-intro">%s</div>', $welcome_intro_content);

    if ($output)
      printf('%s%s%s', '<header id="page-header">', $output, '</header>');
  }

endif;


// Welcome Intro Content
if (!function_exists('unblock_welcome_intro_content')) :
  function unblock_welcome_intro_content()
  {

    if (is_front_page() && get_theme_mod('display_welcome_intro_only_on_homepage', 0)) {
      unblock_welcome_intro('<div class="unblock-welcome-intro">', '</div>');
    } elseif (is_home()) {
      unblock_welcome_intro('<div class="unblock-welcome-intro">', '</div>');
    }
  }
endif;
add_action('unblock_blog_home_heading', 'unblock_welcome_intro_content');



/* MODIFY SEARCH FORM
 @since 1.0.0
   ==================================================== */
if (!function_exists('unblock_search_form')) :
  function unblock_search_form($form)
  {
    $form = '
      <form  method="get" class="search-form" action="' . esc_url(home_url('/')) . '">
        <div class="search-wrap input-group">
            <input type="search" class="search-field" placeholder="' . esc_attr_x('Type keywords...', 'placeholder', 'unblock') . '" value="' . get_search_query() . '" name="s" title="' . esc_attr_x('Search for:', '', 'unblock') . '" />
          <button type="submit" class="button"><i class="bi bi-search"></i></button>
        </div>
			</form>';
    return $form;
  }
endif;
add_filter('get_search_form', 'unblock_search_form');



/* INSERT FORMATS TO DROP DOWN - CLASSIC EDITOR
 @since 1.0.0
   ==================================================== */
if (!function_exists('insert_formats_to_editor')) :
  function insert_formats_to_editor($init_array)
  {
    // Define the style_formats array
    $style_formats = array(
      // Each array child is a format with it's own settings

      array(
        'title' => esc_html__('Extra Small', 'unblock'),
        'inline' => 'span',
        'classes' => 'extra-small-text',
        'wrapper' => true
      ),

      array(
        'title' => esc_html__('Small', 'unblock'),
        'inline' => 'span',
        'classes' => 'small-text',
        'wrapper' => true
      ),
      array(
        'title' => esc_html__('Medium', 'unblock'),
        'inline' => 'span',
        'classes' => 'medium-text',
        'wrapper' => true
      ),

      array(
        'title' => esc_html__('Large', 'unblock'),
        'inline' => 'span',
        'classes' => 'large-text',
        'wrapper' => true
      ),
      array(
        'title' => esc_html__('Extra Large', 'unblock'),
        'inline' => 'span',
        'classes' => 'extra-large-text',
        'wrapper' => true
      ),
      array(
        'title' => esc_html__('Huge', 'unblock'),
        'inline' => 'span',
        'classes' => 'huge-text',
        'wrapper' => true
      ),
      array(
        'title' => esc_html__('Gigantic', 'unblock'),
        'block' => 'div',
        'classes' => 'gigantic-text',
        'wrapper' => true
      )
    );
    // Insert the array, JSON ENCODED, into 'style_formats'
    $init_array['style_formats'] = json_encode($style_formats);

    return $init_array;
  }
endif;
add_filter('tiny_mce_before_init', 'insert_formats_to_editor');


/* ADD STYLE DROP DOWN TO CLASSIC EDITOR
 @since 1.0.0
   ==================================================== */
if (!function_exists('unblock_mce_buttons_2')) :
  function unblock_mce_buttons_2($buttons)
  {
    array_unshift($buttons, 'styleselect');
    return $buttons;
  }
endif;
add_filter('mce_buttons_2', 'unblock_mce_buttons_2');


/* ARCHIVE TITLE PREFIX
	Styles the archive title prefix with a span
	@since 1.0.0
   ==================================================== */
function unblock_prefix_archive_title($title)
{

  $regex = apply_filters(
    'unblock_prefix_the_archive_title_regex',
    array(
      'pattern'     => '/(\A[^\:]+\:)/',
      'replacement' => '<span class="archive-prefix colour">$1</span>',
    )
  );

  if (empty($regex)) {
    return $title;
  }
  return preg_replace($regex['pattern'], $regex['replacement'], $title);
}

add_filter('get_the_archive_title', 'unblock_prefix_archive_title');

/* ARCHIVE TITLES
	Change how archive titles are displayed
	@since 1.0.0
   ==================================================== */
if (!function_exists('unblock_archive_title')) :
  function unblock_archive_title($title)
  {

    $archive_prefix = esc_attr(get_theme_mod('unblock_hide_prefix_archive', 0));

    // If enabled - the prefix archive label is hidden
    if ($archive_prefix) {
      if (is_category()) {
        return single_cat_title('', false);
      } elseif (is_author()) {
        return get_the_author();
      }
    }
    return $title;
  }
endif;
add_filter('get_the_archive_title', 'unblock_archive_title', 10, 1);



/* MODIFY the COMMENT FORM
 @since 1.0.1
   ==================================================== */
if (!function_exists('unblock_comment_form_default_fields')) :
  function unblock_comment_form_default_fields($fields)
  {
    $commenter = wp_get_current_commenter();

    $req      = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');
    $html_req = ($req ? " required='required'" : '');
    $html5    = 'html5';

    $fields['author'] = '
      <div class="row"><div class="col-md-4"><p class="comment-form-author">
        <input id="author" name="author" type="text" placeholder="' . esc_attr__('Name', 'unblock') . (esc_attr($req) ? ' *' : '') . '" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . esc_attr($aria_req) . esc_attr($html_req) . ' />
      </p></div>';

    $fields['email'] = '
      <div class="col-md-4"><p class="comment-form-email">
        <input id="email" name="email" ' . (esc_attr($html5) ? 'type="email"' : 'type="text"') . ' placeholder="' . esc_attr__('Email', 'unblock') . (esc_attr($req) ? ' *' : '') . '" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" aria-describedby="email-notes"' . esc_attr($aria_req) . esc_attr($html_req)  . ' />
      </p></div>';

    $fields['url'] = '
      <div class="col-md-4"><p class="comment-form-url">
        <input id="url" name="url" ' . (esc_attr($html5) ? 'type="url"' : 'type="text"') . ' placeholder="' . esc_attr__('Website', 'unblock') . '" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" />
      </p></div></div>';

    return $fields;
  }
endif;
add_filter('comment_form_default_fields', 'unblock_comment_form_default_fields', 10, 1);



/* ADD A TITLE TO POSTS MISSING TITLES
	When a post is missing a title, a default title will be used.
	@since 1.0.0
   ==================================================== */
if (!function_exists('unblock_post_title')) {
  function unblock_post_title($title)
  {
    return '' === $title ? esc_html_x('Untitled', 'Added to posts and pages that are missing titles', 'unblock') : $title;
  }
}

add_filter('the_title', 'unblock_post_title');



/* FILTER THE EXCERPT LENGTH
	Customizable excerpt length
	@since 1.0.0
   ==================================================== */
if (!function_exists('unblock_excerpt_length')) {
  function unblock_excerpt_length($length)
  {

    if (is_admin()) {
      return $length;
    }

    $excerpt_length = esc_attr(get_theme_mod('unblock_excerpt_length', '25'));
    return $excerpt_length;
  }
}
add_filter('excerpt_length', 'unblock_excerpt_length', 99);



/* FILTER THE EXCERPT SUFFIX
	Replaces the default [...] with a &hellip; (three dots)
	@since 1.0.0
   ==================================================== */
if (!function_exists('unblock_excerpt_more')) :
  function unblock_excerpt_more()
  {
    return '&hellip;';
  }
  add_filter('excerpt_more', 'unblock_excerpt_more');
endif;


/* CREATE A CONTINUE READING LINK FOR EXCERPTS
@since 1.0.0
   ==================================================== */
if (!function_exists('unblock_read_more_link')) :
  function unblock_read_more_link()
  {
    $unblock_readmore_text = esc_html(get_theme_mod('unblock_readmore_text', esc_html__('Read More', 'unblock')));
    echo '<p class="more-link-wrapper"><a class="more-link" href="' . esc_url(get_permalink()) . '">' . wp_kses_post($unblock_readmore_text) . '</a></p>';
  }
endif;


/* MOVE READ MORE LINK OUTSIDE OF PARAGRAPHS
	Move the 'continue reading' link outside of paragraph.
	@since 1.0.0
   ==================================================== */
if (!function_exists('unblock_move_more_link')) :
  function unblock_move_more_link()
  {
    $unblock_readmore_text = esc_html(get_theme_mod('unblock_readmore_text', esc_html__('Read More', 'unblock')));
    return '<p><a class="more-link" href="' . esc_url(get_permalink()) . '">' . wp_kses_post($unblock_readmore_text) . '</a></p>';
  }
  add_filter('the_content_more_link', 'unblock_move_more_link');
endif;


/* RETURN SVG CODE FOR ICON
	Detects the social network from a URL and returns the SVG code for its icon.
   ==================================================== */
function unblock_get_social_link_svg($uri, $size = 24)
{
  return unBlock_SVG_Icons::get_social_link_svg($uri, $size);
}

/* ADDS SVG ICON FOR SOCIAL MENUS
	Displays SVG icons in the social navigation.
   ==================================================== */
function unblock_nav_menu_social_icons($item_output, $item, $depth, $args)
{
  // Change SVG icon inside social links menu if there is supported URL.
  if ('social' === $args->theme_location) {
    $svg = unblock_get_social_link_svg($item->url, 24);
    if (!empty($svg)) {
      $item_output = str_replace($args->link_before, $svg, $item_output);
    }
  }

  return $item_output;
}

add_filter('walker_nav_menu_start_el', 'unblock_nav_menu_social_icons', 10, 4);


/* INLINE COLOUR PRESETS
   ====================================================*/
if (!function_exists('unblock_colour_presets')) :
  function unblock_colour_presets()
  {

    $unblock_presets = get_theme_mod('unblock_presets', 'preset1');
    switch (esc_attr($unblock_presets)) {

      case "preset10":
        echo '--unblock-colour-primary: #af5078;
						--unblock-colour-secondary: #7d3956;
						--unblock-colour-tertiary: #fc74ad;';
        break;

      case "preset9":
        echo '--unblock-colour-primary: #d4826e;
						--unblock-colour-secondary: #54342c;
						--unblock-colour-tertiary: #dbbbb3;';
        break;

      case "preset8":
        echo '--unblock-colour-primary: #9dbd21;
						--unblock-colour-secondary: #333d0a;
						--unblock-colour-tertiary: #b3c85e;';
        break;

      case "preset7":
        echo '--unblock-colour-primary: #d1bc36;
						--unblock-colour-secondary: #524a15;
						--unblock-colour-tertiary: #d9cc79;';
        break;

      case "preset6":
        echo '--unblock-colour-primary: #70cf8c;
						--unblock-colour-secondary: #2b4f36;
						--unblock-colour-tertiary: #b4d7bf;';
        break;

      case "preset5":
        echo '--unblock-colour-primary: #815db0;
						--unblock-colour-secondary: #5c427d;
						--unblock-colour-tertiary: #e4d2fc;';
        break;

      case "preset4":
        echo '--unblock-colour-primary: #447ec9;
						--unblock-colour-secondary: #192e4a;
						--unblock-colour-tertiary: #87a7d2;';
        break;

      case "preset3":
        echo '--unblock-colour-primary: #e1a726;
						--unblock-colour-secondary: #61512e;
						--unblock-colour-tertiary: #e6bf6c;';
        break;

      case "preset2":
        echo '--unblock-colour-primary: #3ea4b5;
						--unblock-colour-secondary: #223336;
						--unblock-colour-tertiary: #7cb7c1;';
        break;

      default:
        echo '--unblock-colour-primary: #d93a13;
						--unblock-colour-secondary: #592e23;
						--unblock-colour-tertiary: #df7257;';
    }
  }
endif;
