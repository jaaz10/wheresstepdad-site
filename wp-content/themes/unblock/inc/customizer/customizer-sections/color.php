<?php

/**
 * Customizer Color Setting
 * @package unBlock
 * @since 1.0.1
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

function unblock_customize_register_colour($wp_customize)
{

  // Colour Options Panel
  $wp_customize->add_panel('colors', array(
    'priority' => 40,
    'title'    => esc_html__('Colours', 'unblock'),
  ));

  // SECTION - PRESET COLOURS 
  $wp_customize->add_section('unblock_preset_colours', array(
    'title' => esc_html__('Preset Colours', 'unblock'),
    'priority' => 10,
    'panel' => 'colors'
  ));

  // SECTION - CONTENT COLOURS 
  $wp_customize->add_section('unblock_content_colours', array(
    'title' => esc_html__('Content Colors', 'unblock'),
    'priority' => 15,
    'panel' => 'colors'
  ));

  // SECTION - NAV COLOURS
  $wp_customize->add_section('unblock_nav_colours', array(
    'title' => esc_html__('Navigation Colors', 'unblock'),
    'priority' => 20,
    'panel' => 'colors'
  ));

  /* PRESET SETTINGS
@since 1.0.0
==================================================== */
  // Presets
  $wp_customize->add_setting(
    'unblock_presets',
    array(
      'default' => 'preset1',
      'sanitize_callback' => 'unblock_sanitize_select',
    )
  );

  $wp_customize->add_control(
    new unBlock_Radio_Image_Control(
      $wp_customize,
      'unblock_presets',
      array(
        'label' => esc_html__('Preset Groups', 'unblock'),
        'description' => esc_html__('Choose a preset colour palette for your theme. This will load a variety of accent colours for select elements in your page.', 'unblock'),
        'section' => 'unblock_preset_colours',
        'type' => 'radio-image',
        'choices' => unblock_colour_preset_options()
      )
    )
  );


  /** Enable Custom Accent Colours */
  $wp_customize->add_setting(
    'unblock_custom_accent_colours',
    array(
      'default'           => false,
      'sanitize_callback' => 'unblock_sanitize_checkbox',
    )
  );

  $wp_customize->add_control(
    new unBlock_Toggle_Control(
      $wp_customize,
      'unblock_custom_accent_colours',
      array(
        'section'     => 'unblock_preset_colours',
        'label'        => esc_html__('Custom Accent Colours', 'unblock'),
        'description' => esc_html__('Enable to change the theme accent colours to be your own.', 'unblock'),
      )
    )
  );


  // Custom Primary Colour
  $wp_customize->add_setting('unblock_custom_primary_colour', array(
    'default'           => '#d93a13',
    'transport' => 'postMessage',
    'sanitize_callback' => 'unblock_sanitize_hex_colour'
  ));

  $wp_customize->add_control(new WP_Customize_Color_Control(
    $wp_customize,
    'unblock_custom_primary_colour',
    array(
      'label'       => esc_html__('Primary Colour', 'unblock'),
      'description' => esc_html__('Sets a custom primary accent colour for your theme.', 'unblock'),
      'section'     => 'unblock_preset_colours',
      'active_callback' => 'unblock_custom_accent_colours_show',
    )
  ));

  // Custom Secondary Colour
  $wp_customize->add_setting('unblock_custom_secondary_colour', array(
    'default'           => '#9a4029',
    'transport' => 'postMessage',
    'sanitize_callback' => 'unblock_sanitize_hex_colour'
  ));

  $wp_customize->add_control(new WP_Customize_Color_Control(
    $wp_customize,
    'unblock_custom_secondary_colour',
    array(
      'label'       => esc_html__('Secondary Colour', 'unblock'),
      'description' => esc_html__('Sets a custom secondary accent colour for your theme.', 'unblock'),
      'section'     => 'unblock_preset_colours',
      'active_callback' => 'unblock_custom_accent_colours_show',
    )
  ));

  // Custom Tertiary Colour
  $wp_customize->add_setting('unblock_custom_tertiary_colour', array(
    'default'           => '#df7257',
    'transport' => 'postMessage',
    'sanitize_callback' => 'unblock_sanitize_hex_colour'
  ));

  $wp_customize->add_control(new WP_Customize_Color_Control(
    $wp_customize,
    'unblock_custom_tertiary_colour',
    array(
      'label'       => esc_html__('Tertiary Colour', 'unblock'),
      'description' => esc_html__('Sets a custom tertiary (third) accent colour for your theme.', 'unblock'),
      'section'     => 'unblock_preset_colours',
      'active_callback' => 'unblock_custom_accent_colours_show',
    )
  ));

  /* CONTENT COLOUR SETTINGS
==================================================== */
  $wp_customize->add_setting('unblock_content_bg_colour', array(
    'default'           => '#fff',
    'transport' => 'postMessage',
    'sanitize_callback' => 'unblock_sanitize_hex_colour'
  ));

  $wp_customize->add_control(new WP_Customize_Color_Control(
    $wp_customize,
    'unblock_content_bg_colour',
    array(
      'label'       => esc_html__('Body Content Background', 'unblock'),
      'description' => esc_html__('Unless a preset changes the colour, you can customize your body and content area background colour.', 'unblock'),
      'section'     => 'colors',
    )
  ));

  // Body text colour
  $wp_customize->add_setting(
    'unblock_content_area_text_colour',
    array(
      'default' => '#646464',
      'sanitize_callback' => 'unblock_sanitize_hex_colour',
      'transport' => 'postMessage'
    )
  );

  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'unblock_content_area_text_colour',
      array(
        'label' => esc_html__('Body Text Colour', 'unblock'),
        'section' => 'colors',
        'settings' => 'unblock_content_area_text_colour',
      )
    )
  );

  // Headings colour
  $wp_customize->add_setting(
    'unblock_headings_colour',
    array(
      'default' => '#333',
      'sanitize_callback' => 'unblock_sanitize_hex_colour',
      'transport' => 'postMessage'
    )
  );

  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'unblock_headings_colour',
      array(
        'label' => esc_html__('Headings Colour', 'unblock'),
        'section' => 'colors',
        'settings' => 'unblock_headings_colour',
      )
    )
  );


  // Content links
  $wp_customize->add_setting(
    'unblock_content_links',
    array(
      'default' => '#d93a13',
      'sanitize_callback' => 'unblock_sanitize_hex_colour',
      'transport' => 'postMessage'
    )
  );

  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'unblock_content_links',
      array(
        'label' => esc_html__('Content Links', 'unblock'),
        'section' => 'colors',
        'settings' => 'unblock_content_links',
      )
    )
  );

  // Banner caption background
  $wp_customize->add_setting(
    'unblock_banner_caption',
    array(
      'default' => '#d93a13',
      'sanitize_callback' => 'unblock_sanitize_hex_colour',
      'transport' => 'postMessage'
    )
  );

  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'unblock_banner_caption',
      array(
        'label' => esc_html__('Banner Caption Colour', 'unblock'),
        'section' => 'colors',
        'settings' => 'unblock_banner_caption',
      )
    )
  );

  // Banner caption text
  $wp_customize->add_setting(
    'unblock_banner_caption_text_color',
    array(
      'default' => '#fff',
      'sanitize_callback' => 'unblock_sanitize_hex_colour',
      'transport' => 'postMessage'
    )
  );

  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'unblock_banner_caption_text_color',
      array(
        'label' => esc_html__('Banner Caption Text Colour', 'unblock'),
        'section' => 'colors',
        'settings' => 'unblock_banner_caption_text_color',
      )
    )
  );

  // Bottom Sidebar background
  $wp_customize->add_setting(
    'unblock_bottom_sidebar_bg',
    array(
      'default' => '#df7257',
      'sanitize_callback' => 'unblock_sanitize_hex_colour',
      'transport' => 'postMessage'
    )
  );

  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'unblock_bottom_sidebar_bg',
      array(
        'label' => esc_html__('Bottom Sidebar Background', 'unblock'),
        'section' => 'colors',
        'settings' => 'unblock_bottom_sidebar_bg',
      )
    )
  );

  // Bottom sidebar text
  $wp_customize->add_setting(
    'unblock_bottom_sidebar_text',
    array(
      'default' => '#fff',
      'sanitize_callback' => 'unblock_sanitize_hex_colour',
      'transport' => 'postMessage'
    )
  );

  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'unblock_bottom_sidebar_text',
      array(
        'label' => esc_html__('Bottom Sidebar Text', 'unblock'),
        'section' => 'colors',
        'settings' => 'unblock_bottom_sidebar_text',
      )
    )
  );

  /* NAVIGATION COLOUR SETTINGS
==================================================== */
  $wp_customize->add_setting('unblock_nav_colour', array(
    'default'           => '#000000',
    'transport' => 'postMessage',
    'sanitize_callback' => 'unblock_sanitize_hex_colour'
  ));

  $wp_customize->add_control(new WP_Customize_Color_Control(
    $wp_customize,
    'unblock_nav_colour',
    array(
      'label'       => esc_html__('Primary Nav Link Colour', 'unblock'),
      'description' => esc_html__('This sets the colour for your primary menu links.', 'unblock'),
      'section'     => 'unblock_nav_colours',
    )
  ));

  // Mobile menu line separators
  $wp_customize->add_setting('unblock_mobile_line_separators', array(
    'default'           => '#ededed',
    'transport' => 'postMessage',
    'sanitize_callback' => 'unblock_sanitize_hex_colour'
  ));

  $wp_customize->add_control(new WP_Customize_Color_Control(
    $wp_customize,
    'unblock_mobile_line_separators',
    array(
      'label'       => esc_html__('Mobile Menu Line Separators', 'unblock'),
      'description' => esc_html__('This sets the colour for your mobile menu line separators.', 'unblock'),
      'section'     => 'unblock_nav_colours',
    )
  ));

  // Pagination background
  $wp_customize->add_setting('unblock_pagination_bg', array(
    'default'           => '#f2f2f2',
    'transport' => 'postMessage',
    'sanitize_callback' => 'unblock_sanitize_hex_colour'
  ));

  $wp_customize->add_control(new WP_Customize_Color_Control(
    $wp_customize,
    'unblock_pagination_bg',
    array(
      'label'       => esc_html__('Pagination Number Backgrounds', 'unblock'),
      'description' => esc_html__('This sets the colour for the non-active blog pagination background numbers.', 'unblock'),
      'section'     => 'unblock_nav_colours',
    )
  ));

  // Pagination numbers
  $wp_customize->add_setting('unblock_pagination_numbers', array(
    'default'           => '#333',
    'transport' => 'postMessage',
    'sanitize_callback' => 'unblock_sanitize_hex_colour'
  ));

  $wp_customize->add_control(new WP_Customize_Color_Control(
    $wp_customize,
    'unblock_pagination_numbers',
    array(
      'label'       => esc_html__('Pagination Numbers', 'unblock'),
      'description' => esc_html__('This sets the colour for the non-active blog pagination numbers numbers.', 'unblock'),
      'section'     => 'unblock_nav_colours',
    )
  ));
}
add_action('customize_register', 'unblock_customize_register_colour');


/* CALLBACKS
==================================================== */
// Show custom accent colour selectors callback
function unblock_custom_accent_colours_show($control)
{
  if ($control->manager->get_setting('unblock_custom_accent_colours')->value() == 'true') {
    return true;
  } else {
    return false;
  }
}
