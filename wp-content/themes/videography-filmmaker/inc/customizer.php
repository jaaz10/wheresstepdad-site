<?php
/**
 * Videography Filmmaker Theme Customizer
 *
 * @package Videography Filmmaker
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function videography_filmmaker_customize_register( $wp_customize ) {

	load_template( trailingslashit( get_template_directory() ) . '/inc/icon-changer.php' );

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';

	$wp_customize->selective_refresh->add_partial(
		'blogname',
		array(
			'selector'        => '.site-title a',
			'render_callback' => 'videography_filmmaker_customize_partial_blogname',
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'blogdescription',
		array(
			'selector'        => '.site-description',
			'render_callback' => 'videography_filmmaker_customize_partial_blogdescription',
		)
	);

	//add home page setting pannel
	$wp_customize->add_panel( 'videography_filmmaker_panel_id', array(
	    'priority' => 10,
	    'capability' => 'edit_theme_options',
	    'theme_supports' => '',
	    'title' => __( 'Theme Settings', 'videography-filmmaker' ),
	) );

    $videography_filmmaker_font_array= array(
        '' =>'No Fonts',
        'Abril Fatface' => 'Abril Fatface',
        'Acme' =>'Acme', 
        'Anton' => 'Anton', 
        'Architects Daughter' =>'Architects Daughter',
        'Arimo' => 'Arimo', 
        'Arsenal' =>'Arsenal',
        'Arvo' =>'Arvo',
        'Alegreya' =>'Alegreya',
        'Alfa Slab One' =>'Alfa Slab One',
        'Averia Serif Libre' =>'Averia Serif Libre', 
        'Bangers' =>'Bangers', 
        'Boogaloo' =>'Boogaloo', 
        'Bad Script' =>'Bad Script',
        'Bitter' =>'Bitter', 
        'Bree Serif' =>'Bree Serif', 
        'BenchNine' =>'BenchNine',
        'Cabin' =>'Cabin',
        'Cardo' =>'Cardo', 
        'Courgette' =>'Courgette', 
        'Cherry Swash' =>'Cherry Swash',
        'Cormorant Garamond' =>'Cormorant Garamond', 
        'Crimson Text' =>'Crimson Text',
        'Cuprum' =>'Cuprum', 
        'Cookie' =>'Cookie',
        'Chewy' =>'Chewy',
        'Days One' =>'Days One',
        'Dosis' =>'Dosis',
        'Droid Sans' =>'Droid Sans', 
        'Economica' =>'Economica', 
        'Fredoka One' =>'Fredoka One',
        'Fjalla One' =>'Fjalla One',
        'Francois One' =>'Francois One', 
        'Frank Ruhl Libre' => 'Frank Ruhl Libre', 
        'Gloria Hallelujah' =>'Gloria Hallelujah',
        'Great Vibes' =>'Great Vibes', 
        'Handlee' =>'Handlee', 
        'Hammersmith One' =>'Hammersmith One',
        'Inconsolata' =>'Inconsolata',
        'Indie Flower' =>'Indie Flower', 
        'IM Fell English SC' =>'IM Fell English SC',
        'Julius Sans One' =>'Julius Sans One',
        'Josefin Slab' =>'Josefin Slab',
        'Josefin Sans' =>'Josefin Sans',
        'Kanit' =>'Kanit',
        'Lobster' =>'Lobster',
        'Lato' => 'Lato',
        'Lora' =>'Lora', 
        'Libre Baskerville' =>'Libre Baskerville',
        'Lobster Two' => 'Lobster Two',
        'Merriweather' =>'Merriweather',
        'Monda' =>'Monda',
        'Montserrat' =>'Montserrat',
        'Muli' =>'Muli',
        'Marck Script' =>'Marck Script',
        'Noto Serif' =>'Noto Serif',
        'Open Sans' =>'Open Sans',
        'Overpass' => 'Overpass', 
        'Overpass Mono' =>'Overpass Mono',
        'Oxygen' =>'Oxygen',
        'Orbitron' =>'Orbitron',
        'Patua One' =>'Patua One',
        'Pacifico' =>'Pacifico',
        'Padauk' =>'Padauk',
        'Playball' =>'Playball',
        'Playfair Display' =>'Playfair Display',
        'PT Sans' =>'PT Sans',
        'Philosopher' =>'Philosopher',
        'Permanent Marker' =>'Permanent Marker',
        'Poiret One' =>'Poiret One',
        'Quicksand' =>'Quicksand',
        'Quattrocento Sans' =>'Quattrocento Sans',
        'Raleway' =>'Raleway',
        'Rubik' =>'Rubik',
        'Rokkitt' =>'Rokkitt',
        'Russo One' => 'Russo One', 
        'Righteous' =>'Righteous', 
        'Slabo' =>'Slabo', 
        'Source Sans Pro' =>'Source Sans Pro',
        'Shadows Into Light Two' =>'Shadows Into Light Two',
        'Shadows Into Light' =>  'Shadows Into Light',
        'Sacramento' =>'Sacramento',
        'Shrikhand' =>'Shrikhand',
        'Tangerine' => 'Tangerine',
        'Ubuntu' =>'Ubuntu',
        'VT323' =>'VT323',
        'Varela Round' =>'Varela Round',
        'Vampiro One' =>'Vampiro One',
        'Vollkorn' => 'Vollkorn',
        'Volkhov' =>'Volkhov',
        'Kavoon' =>'Kavoon',
        'Yanone Kaffeesatz' =>'Yanone Kaffeesatz'
    );

	//Color / Font Pallete
	$wp_customize->add_section( 'videography_filmmaker_typography', array(
    	'title'      => __( 'Color / Font Pallete', 'videography-filmmaker' ),
		'priority'   => 30,
		'panel' => 'videography_filmmaker_panel_id'
	) );

	// This is Body Color setting
	$wp_customize->add_setting( 'videography_filmmaker_body_color', array(
		'default' => '',
		'sanitize_callback'	=> 'sanitize_hex_color'
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'videography_filmmaker_body_color', array(
		'label' => __('Body Color', 'videography-filmmaker'),
		'section' => 'videography_filmmaker_typography',
		'settings' => 'videography_filmmaker_body_color',
	)));

	//This is Body FontFamily  setting
	$wp_customize->add_setting('videography_filmmaker_body_font_family',array(
	  'default' => '',
	  'capability' => 'edit_theme_options',
	  'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control(
		'videography_filmmaker_body_font_family', array(
		'section'  => 'videography_filmmaker_typography',
		'label'    => __( 'Body Fonts','videography-filmmaker'),
		'type'     => 'select',
		'choices'  => $videography_filmmaker_font_array,
	));

    //This is Body Fontsize setting
	$wp_customize->add_setting('videography_filmmaker_body_font_size',array(
		'default'	=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('videography_filmmaker_body_font_size',array(
		'label'	=> __('Body Font Size','videography-filmmaker'),
		'section'	=> 'videography_filmmaker_typography',
		'setting'	=> 'videography_filmmaker_body_font_size',
		'type'	=> 'text'
	));
	
	// This is Paragraph Color picker setting
	$wp_customize->add_setting( 'videography_filmmaker_paragraph_color', array(
		'default' => '',
		'sanitize_callback'	=> 'sanitize_hex_color'
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'videography_filmmaker_paragraph_color', array(
		'label' => __('Paragraph Color', 'videography-filmmaker'),
		'section' => 'videography_filmmaker_typography',
		'settings' => 'videography_filmmaker_paragraph_color',
	)));

	//This is Paragraph FontFamily picker setting
	$wp_customize->add_setting('videography_filmmaker_paragraph_font_family',array(
	  'default' => '',
	  'capability' => 'edit_theme_options',
	  'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control(
	    'videography_filmmaker_paragraph_font_family', array(
	    'section'  => 'videography_filmmaker_typography',
	    'label'    => __( 'Paragraph Fonts','videography-filmmaker'),
	    'type'     => 'select',
	    'choices'  => $videography_filmmaker_font_array,
	));

	$wp_customize->add_setting('videography_filmmaker_paragraph_font_size',array(
		'default'	=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('videography_filmmaker_paragraph_font_size',array(
		'label'	=> __('Paragraph Font Size','videography-filmmaker'),
		'section'	=> 'videography_filmmaker_typography',
		'setting'	=> 'videography_filmmaker_paragraph_font_size',
		'type'	=> 'text'
	));

	// This is "a" Tag Color picker setting
	$wp_customize->add_setting( 'videography_filmmaker_atag_color', array(
		'default' => '',
		'sanitize_callback'	=> 'sanitize_hex_color'
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'videography_filmmaker_atag_color', array(
		'label' => __('"a" Tag Color', 'videography-filmmaker'),
		'section' => 'videography_filmmaker_typography',
		'settings' => 'videography_filmmaker_atag_color',
	)));

	//This is "a" Tag FontFamily picker setting
	$wp_customize->add_setting('videography_filmmaker_atag_font_family',array(
	  'default' => '',
	  'capability' => 'edit_theme_options',
	  'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control(
	    'videography_filmmaker_atag_font_family', array(
	    'section'  => 'videography_filmmaker_typography',
	    'label'    => __( '"a" Tag Fonts','videography-filmmaker'),
	    'type'     => 'select',
	    'choices'  => $videography_filmmaker_font_array,
	));

	// This is "a" Tag Color picker setting
	$wp_customize->add_setting( 'videography_filmmaker_li_color', array(
		'default' => '',
		'sanitize_callback'	=> 'sanitize_hex_color'
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'videography_filmmaker_li_color', array(
		'label' => __('"li" Tag Color', 'videography-filmmaker'),
		'section' => 'videography_filmmaker_typography',
		'settings' => 'videography_filmmaker_li_color',
	)));

	//This is "li" Tag FontFamily picker setting
	$wp_customize->add_setting('videography_filmmaker_li_font_family',array(
	  'default' => '',
	  'capability' => 'edit_theme_options',
	  'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control(
	    'videography_filmmaker_li_font_family', array(
	    'section'  => 'videography_filmmaker_typography',
	    'label'    => __( '"li" Tag Fonts','videography-filmmaker'),
	    'type'     => 'select',
	    'choices'  => $videography_filmmaker_font_array,
	));

	// This is H1 Color picker setting
	$wp_customize->add_setting( 'videography_filmmaker_h1_color', array(
		'default' => '',
		'sanitize_callback'	=> 'sanitize_hex_color'
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'videography_filmmaker_h1_color', array(
		'label' => __('h1 Color', 'videography-filmmaker'),
		'section' => 'videography_filmmaker_typography',
		'settings' => 'videography_filmmaker_h1_color',
	)));

	//This is H1 FontFamily picker setting
	$wp_customize->add_setting('videography_filmmaker_h1_font_family',array(
	  'default' => '',
	  'capability' => 'edit_theme_options',
	  'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control(
	    'videography_filmmaker_h1_font_family', array(
	    'section'  => 'videography_filmmaker_typography',
	    'label'    => __( 'h1 Fonts','videography-filmmaker'),
	    'type'     => 'select',
	    'choices'  => $videography_filmmaker_font_array,
	));

	//This is H1 FontSize setting
	$wp_customize->add_setting('videography_filmmaker_h1_font_size',array(
		'default'	=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('videography_filmmaker_h1_font_size',array(
		'label'	=> __('h1 Font Size','videography-filmmaker'),
		'section'	=> 'videography_filmmaker_typography',
		'setting'	=> 'videography_filmmaker_h1_font_size',
		'type'	=> 'text'
	));

	// This is H2 Color picker setting
	$wp_customize->add_setting( 'videography_filmmaker_h2_color', array(
		'default' => '',
		'sanitize_callback'	=> 'sanitize_hex_color'
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'videography_filmmaker_h2_color', array(
		'label' => __('h2 Color', 'videography-filmmaker'),
		'section' => 'videography_filmmaker_typography',
		'settings' => 'videography_filmmaker_h2_color',
	)));

	//This is H2 FontFamily picker setting
	$wp_customize->add_setting('videography_filmmaker_h2_font_family',array(
	  'default' => '',
	  'capability' => 'edit_theme_options',
	  'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control(
	    'videography_filmmaker_h2_font_family', array(
	    'section'  => 'videography_filmmaker_typography',
	    'label'    => __( 'h2 Fonts','videography-filmmaker'),
	    'type'     => 'select',
	    'choices'  => $videography_filmmaker_font_array,
	));

	//This is H2 FontSize setting
	$wp_customize->add_setting('videography_filmmaker_h2_font_size',array(
		'default'	=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('videography_filmmaker_h2_font_size',array(
		'label'	=> __('h2 Font Size','videography-filmmaker'),
		'section'	=> 'videography_filmmaker_typography',
		'setting'	=> 'videography_filmmaker_h2_font_size',
		'type'	=> 'text'
	));

	// This is H3 Color picker setting
	$wp_customize->add_setting( 'videography_filmmaker_h3_color', array(
		'default' => '',
		'sanitize_callback'	=> 'sanitize_hex_color'
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'videography_filmmaker_h3_color', array(
		'label' => __('h3 Color', 'videography-filmmaker'),
		'section' => 'videography_filmmaker_typography',
		'settings' => 'videography_filmmaker_h3_color',
	)));

	//This is H3 FontFamily picker setting
	$wp_customize->add_setting('videography_filmmaker_h3_font_family',array(
	  'default' => '',
	  'capability' => 'edit_theme_options',
	  'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control(
	    'videography_filmmaker_h3_font_family', array(
	    'section'  => 'videography_filmmaker_typography',
	    'label'    => __( 'h3 Fonts','videography-filmmaker'),
	    'type'     => 'select',
	    'choices'  => $videography_filmmaker_font_array,
	));

	//This is H3 FontSize setting
	$wp_customize->add_setting('videography_filmmaker_h3_font_size',array(
		'default'	=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('videography_filmmaker_h3_font_size',array(
		'label'	=> __('h3 Font Size','videography-filmmaker'),
		'section'	=> 'videography_filmmaker_typography',
		'setting'	=> 'videography_filmmaker_h3_font_size',
		'type'	=> 'text'
	));

	// This is H4 Color picker setting
	$wp_customize->add_setting( 'videography_filmmaker_h4_color', array(
		'default' => '',
		'sanitize_callback'	=> 'sanitize_hex_color'
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'videography_filmmaker_h4_color', array(
		'label' => __('h4 Color', 'videography-filmmaker'),
		'section' => 'videography_filmmaker_typography',
		'settings' => 'videography_filmmaker_h4_color',
	)));

	//This is H4 FontFamily picker setting
	$wp_customize->add_setting('videography_filmmaker_h4_font_family',array(
	  'default' => '',
	  'capability' => 'edit_theme_options',
	  'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control(
	    'videography_filmmaker_h4_font_family', array(
	    'section'  => 'videography_filmmaker_typography',
	    'label'    => __( 'h4 Fonts','videography-filmmaker'),
	    'type'     => 'select',
	    'choices'  => $videography_filmmaker_font_array,
	));

	//This is H4 FontSize setting
	$wp_customize->add_setting('videography_filmmaker_h4_font_size',array(
		'default'	=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('videography_filmmaker_h4_font_size',array(
		'label'	=> __('h4 Font Size','videography-filmmaker'),
		'section'	=> 'videography_filmmaker_typography',
		'setting'	=> 'videography_filmmaker_h4_font_size',
		'type'	=> 'text'
	));

	// This is H5 Color picker setting
	$wp_customize->add_setting( 'videography_filmmaker_h5_color', array(
		'default' => '',
		'sanitize_callback'	=> 'sanitize_hex_color'
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'videography_filmmaker_h5_color', array(
		'label' => __('h5 Color', 'videography-filmmaker'),
		'section' => 'videography_filmmaker_typography',
		'settings' => 'videography_filmmaker_h5_color',
	)));

	//This is H5 FontFamily picker setting
	$wp_customize->add_setting('videography_filmmaker_h5_font_family',array(
	  'default' => '',
	  'capability' => 'edit_theme_options',
	  'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control(
	    'videography_filmmaker_h5_font_family', array(
	    'section'  => 'videography_filmmaker_typography',
	    'label'    => __( 'h5 Fonts','videography-filmmaker'),
	    'type'     => 'select',
	    'choices'  => $videography_filmmaker_font_array,
	));

	//This is H5 FontSize setting
	$wp_customize->add_setting('videography_filmmaker_h5_font_size',array(
		'default'	=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('videography_filmmaker_h5_font_size',array(
		'label'	=> __('h5 Font Size','videography-filmmaker'),
		'section'	=> 'videography_filmmaker_typography',
		'setting'	=> 'videography_filmmaker_h5_font_size',
		'type'	=> 'text'
	));

	// This is H6 Color picker setting
	$wp_customize->add_setting( 'videography_filmmaker_h6_color', array(
		'default' => '',
		'sanitize_callback'	=> 'sanitize_hex_color'
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'videography_filmmaker_h6_color', array(
		'label' => __('h6 Color', 'videography-filmmaker'),
		'section' => 'videography_filmmaker_typography',
		'settings' => 'videography_filmmaker_h6_color',
	)));

	//This is H6 FontFamily picker setting
	$wp_customize->add_setting('videography_filmmaker_h6_font_family',array(
	  'default' => '',
	  'capability' => 'edit_theme_options',
	  'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control(
	    'videography_filmmaker_h6_font_family', array(
	    'section'  => 'videography_filmmaker_typography',
	    'label'    => __( 'h6 Fonts','videography-filmmaker'),
	    'type'     => 'select',
	    'choices'  => $videography_filmmaker_font_array,
	));

	//This is H6 FontSize setting
	$wp_customize->add_setting('videography_filmmaker_h6_font_size',array(
		'default'	=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('videography_filmmaker_h6_font_size',array(
		'label'	=> __('h6 Font Size','videography-filmmaker'),
		'section'	=> 'videography_filmmaker_typography',
		'setting'	=> 'videography_filmmaker_h6_font_size',
		'type'	=> 'text'
	));

	//Layouts
	$wp_customize->add_section( 'videography_filmmaker_left_right', array(
    	'title'      => __( 'Theme Layout Settings', 'videography-filmmaker' ),
		'priority'   => 30,
		'panel' => 'videography_filmmaker_panel_id'
	) );

	$wp_customize->add_setting('videography_filmmaker_width_options',array(
        'default' => 'Full Layout',
        'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control('videography_filmmaker_width_options',array(
        'type' => 'select',
        'label' => __('Select Site Layout','videography-filmmaker'),
        'section' => 'videography_filmmaker_left_right',
        'choices' => array(
            'Full Layout' => __('Full Layout','videography-filmmaker'),
            'Contained Layout' => __('Contained Layout','videography-filmmaker'),
            'Boxed Layout' => __('Boxed Layout','videography-filmmaker'),
        ),
	) );

	// Add Settings and Controls for Layout
	$wp_customize->add_setting('videography_filmmaker_theme_options',array(
        'default' => 'Right Sidebar',
        'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	) );
	$wp_customize->add_control('videography_filmmaker_theme_options', array(
        'type' => 'radio',
        'label' => __('Sidebar Layout','videography-filmmaker'),
        'section' => 'videography_filmmaker_left_right',
        'choices' => array(
            'Left Sidebar' => __('Left Sidebar','videography-filmmaker'),
            'Right Sidebar' => __('Right Sidebar','videography-filmmaker'),
            'One Column' => __('One Column','videography-filmmaker'),
            'Three Columns' => __('Three Columns','videography-filmmaker'),
            'Four Columns' => __('Four Columns','videography-filmmaker'),
            'Grid Layout' => __('Grid Layout','videography-filmmaker')
        ),
    ));

	// Add Settings and Controls for Layout
	$wp_customize->add_setting('videography_filmmaker_single_post_sidebar',array(
        'default' => 'Right Sidebar',
        'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	) );
	$wp_customize->add_control('videography_filmmaker_single_post_sidebar', array(
        'type' => 'radio',
        'label' => __('Single Post Sidebar Layout','videography-filmmaker'),
        'section' => 'videography_filmmaker_left_right',
        'choices' => array(
            'Left Sidebar' => __('Left Sidebar','videography-filmmaker'),
            'Right Sidebar' => __('Right Sidebar','videography-filmmaker'),
            'One Column' => __('One Column','videography-filmmaker'),
        ),
    ));

	// Preloader
	$wp_customize->add_setting( 'videography_filmmaker_preloader_hide',array(
		'default' => false,
      	'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ) );
    $wp_customize->add_control('videography_filmmaker_preloader_hide',array(
    	'type' => 'checkbox',
        'label' => __( 'Show / Hide Preloader','videography-filmmaker' ),
        'section' => 'videography_filmmaker_left_right'
    ));

    $wp_customize->add_setting('videography_filmmaker_preloader_type',array(
        'default'   => 'center-square',
        'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control( 'videography_filmmaker_preloader_type', array(
		'label' => __( 'Preloader Type','videography-filmmaker' ),
		'section' => 'videography_filmmaker_left_right',
		'type'  => 'select',
		'settings' => 'videography_filmmaker_preloader_type',
		'choices' => array(
		    'center-square' => __('Center Square','videography-filmmaker'),
		    'chasing-square' => __('Chasing Square','videography-filmmaker'),
	    ),
	));

	$wp_customize->add_setting( 'videography_filmmaker_preloader_color', array(
	    'default' => '#333333',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'videography_filmmaker_preloader_color', array(
  		'label' => 'Preloader Color',
	    'section' => 'videography_filmmaker_left_right',
	    'settings' => 'videography_filmmaker_preloader_color',
  	)));

  	$wp_customize->add_setting( 'videography_filmmaker_preloader_bg_color', array(
	    'default' => '#fff',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'videography_filmmaker_preloader_bg_color', array(
  		'label' => 'Preloader Background Color',
	    'section' => 'videography_filmmaker_left_right',
	    'settings' => 'videography_filmmaker_preloader_bg_color',
  	)));

	//Header
	$wp_customize->add_section('videography_filmmaker_header',array(
		'title'	=> __('Header','videography-filmmaker'),
		'priority'	=> null,
		'panel' => 'videography_filmmaker_panel_id',
	));

	//Sticky Header
	$wp_customize->add_setting( 'videography_filmmaker_sticky_header',array(
      	'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ) );
    $wp_customize->add_control('videography_filmmaker_sticky_header',array(
    	'type' => 'checkbox',
        'label' => __( 'Sticky Header','videography-filmmaker' ),
        'section' => 'videography_filmmaker_header'
    ));

    $wp_customize->add_setting('videography_filmmaker_sticky_header_padding', array(
		'default'=> '',
		'sanitize_callback'	=> 'videography_filmmaker_sanitize_float'
	));
	$wp_customize->add_control('videography_filmmaker_sticky_header_padding', array(
		'label'	=> __('Sticky Header Padding','videography-filmmaker'),
		'input_attrs' => array(
            'step' => 1,
			'min'  => 0,
			'max'  => 50,
        ),
		'section'=> 'videography_filmmaker_header',
		'type'=> 'number',
	));

	$wp_customize->add_setting('videography_filmmaker_navigation_case',array(
        'default' => 'capitalize',
        'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control('videography_filmmaker_navigation_case',array(
        'type' => 'select',
        'label' => __('Navigation Case','videography-filmmaker'),
        'section' => 'videography_filmmaker_header',
        'choices' => array(
            'uppercase' => __('Uppercase','videography-filmmaker'),
            'capitalize' => __('Capitalize','videography-filmmaker'),
        ),
	) );

	$wp_customize->add_setting( 'videography_filmmaker_nav_font_size', array(
		'default'           => 15,
		'sanitize_callback' => 'videography_filmmaker_sanitize_float',
	) );
	$wp_customize->add_control( 'videography_filmmaker_nav_font_size', array(
		'label' => __( 'Navigation Font Size','videography-filmmaker' ),
		'section'     => 'videography_filmmaker_header',
		'type'        => 'number',
		'input_attrs' => array(
			'step' => 1,
			'min' => 0,
			'max' => 50,
		),
	) );

	$wp_customize->add_setting('videography_filmmaker_font_weight_menu_option',array(
        'default' => '500',
        'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
    ));
    $wp_customize->add_control('videography_filmmaker_font_weight_menu_option',array(
        'type' => 'select',
        'label' => __('Navigation Font Weight','videography-filmmaker'),
        'section' => 'videography_filmmaker_header',
        'choices' => array(
            '100' => __('100','videography-filmmaker'),
            '200' => __('200','videography-filmmaker'),
            '300' => __('300','videography-filmmaker'),
            '400' => __('400','videography-filmmaker'),
            '500' => __('500','videography-filmmaker'),
            '600' => __('600','videography-filmmaker'),
            '700' => __('700','videography-filmmaker'),
            '800' => __('800','videography-filmmaker'),
            '900' => __('900','videography-filmmaker'),
        ),
	) );

	//home page slider
	$wp_customize->add_section( 'videography_filmmaker_slidersettings' , array(
    	'title'      => __( 'Slider Settings', 'videography-filmmaker' ),
		'priority'   => null,
		'panel' => 'videography_filmmaker_panel_id'
	) );

	$wp_customize->selective_refresh->add_partial(
		'videography_filmmaker_slider_hide_show',
		array(
			'selector'        => '#slider .inner_carousel h1',
			'render_callback' => 'videography_filmmaker_customize_partial_videography_filmmaker_slider_hide_show',
		)
	);

	$wp_customize->add_setting('videography_filmmaker_slider_hide_show',array(
       'default' => false,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
	));
	$wp_customize->add_control('videography_filmmaker_slider_hide_show',array(
	   'type' => 'checkbox',
	   'label' => __('Show / Hide slider','videography-filmmaker'),
	   'section' => 'videography_filmmaker_slidersettings',
	));

	$wp_customize->add_setting('videography_filmmaker_slider_background_img',array(
		'default'	=> get_template_directory_uri().'/images/slider-bg.png',
		'sanitize_callback'	=> 'esc_url_raw',
	));
	$wp_customize->add_control( new WP_Customize_Image_Control($wp_customize,'videography_filmmaker_slider_background_img',array(
        'label' => __('Slider Background Image','videography-filmmaker'),
        'description' => __('Image size (1400px x 750px)','videography-filmmaker'),
        'section' => 'videography_filmmaker_slidersettings'
	)));

	$wp_customize->add_setting('videography_filmmaker_slider_section_title',array(
       'default' => '',
       'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('videography_filmmaker_slider_section_title',array(
	   'type' => 'text',
	   'label' => __('Section Title','videography-filmmaker'),
	   'section' => 'videography_filmmaker_slidersettings',
	));

	$wp_customize->add_setting('videography_filmmaker_slider_section_text',array(
       'default' => '',
       'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('videography_filmmaker_slider_section_text',array(
	   'type' => 'text',
	   'label' => __('Section Text','videography-filmmaker'),
	   'section' => 'videography_filmmaker_slidersettings',
	));

	$wp_customize->add_setting('videography_filmmaker_slider_section_btn_text',array(
       'default' => '',
       'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('videography_filmmaker_slider_section_btn_text',array(
	   'type' => 'text',
	   'label' => __('Add Button Text','videography-filmmaker'),
	   'section' => 'videography_filmmaker_slidersettings',
	));

	$wp_customize->add_setting('videography_filmmaker_slider_section_btn_url',array(
       'default' => '',
       'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('videography_filmmaker_slider_section_btn_url',array(
	   'type' => 'text',
	   'label' => __('Add Button URL','videography-filmmaker'),
	   'section' => 'videography_filmmaker_slidersettings',
	));

	$categories = get_categories();
	$cat_posts = array();
	$i = 0;
	$cat_posts[]='Select';
	foreach($categories as $category){
		if($i==0){
			$default = $category->slug;
			$i++;
		}
		$cat_posts[$category->slug] = $category->name;
	}

	$wp_customize->add_setting('videography_filmmaker_slider_category',array(
		'default'	=> 'select',
		'sanitize_callback' => 'videography_filmmaker_sanitize_choices',
	));
	$wp_customize->add_control('videography_filmmaker_slider_category',array(
		'type'    => 'select',
		'choices' => $cat_posts,
		'label' => __('Select Slider Category','videography-filmmaker'),
		'section' => 'videography_filmmaker_slidersettings',
	));

	//Services Section
	$wp_customize->add_section('videography_filmmaker_services_section',array(
		'title'	=> __('Services Section','videography-filmmaker'),
		'panel' => 'videography_filmmaker_panel_id',
	));

	$wp_customize->add_setting('videography_filmmaker_section_title',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control('videography_filmmaker_section_title',array(
		'label'	=> esc_html__('Section Title','videography-filmmaker'),
		'section'=> 'videography_filmmaker_services_section',
		'type'=> 'text'
	));

	$categories = get_categories();
	$cats = array();
	$i = 0;
	$cat_post[]= 'select';
	foreach($categories as $category){
		if($i==0){
			$default = $category->slug;
			$i++;
		}
		$cat_post[$category->slug] = $category->name;
	}

	$wp_customize->add_setting('videography_filmmaker_service_category',array(
		'default'	=> 'select',
		'sanitize_callback' => 'videography_filmmaker_sanitize_choices',
	));	
	$wp_customize->add_control('videography_filmmaker_service_category',array(
		'type'    => 'select',
		'choices' => $cat_post,		
		'label' => __('Select Category to display posts','videography-filmmaker'),
		'section' => 'videography_filmmaker_services_section',
	));

	//Blog Post
	$wp_customize->add_section('videography_filmmaker_blog_post',array(
		'title'	=> __('Post Settings','videography-filmmaker'),
		'panel' => 'videography_filmmaker_panel_id',
	));	

	$wp_customize->add_setting('videography_filmmaker_date_hide',array(
       'default' => true,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ));
    $wp_customize->add_control('videography_filmmaker_date_hide',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Post Date','videography-filmmaker'),
       'section' => 'videography_filmmaker_blog_post'
    ));

    $wp_customize->add_setting('videography_filmmaker_author_hide',array(
       'default' => true,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ));
    $wp_customize->add_control('videography_filmmaker_author_hide',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Post Author','videography-filmmaker'),
       'section' => 'videography_filmmaker_blog_post'
    ));

    $wp_customize->add_setting('videography_filmmaker_comment_hide',array(
       'default' => true,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ));
    $wp_customize->add_control('videography_filmmaker_comment_hide',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Post Comments','videography-filmmaker'),
       'section' => 'videography_filmmaker_blog_post'
    ));

    $wp_customize->add_setting('videography_filmmaker_time_hide',array(
       'default' => false,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ));
    $wp_customize->add_control('videography_filmmaker_time_hide',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Post Time','videography-filmmaker'),
       'section' => 'videography_filmmaker_blog_post'
    ));

    $wp_customize->add_setting('videography_filmmaker_feature_image_hide',array(
       'default' => true,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ));
    $wp_customize->add_control('videography_filmmaker_feature_image_hide',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Featured Image','videography-filmmaker'),
       'section' => 'videography_filmmaker_blog_post'
    ));

    $wp_customize->add_setting( 'videography_filmmaker_featured_image_border_radius', array(
		'default' => 0,
		'sanitize_callback'	=> 'videography_filmmaker_sanitize_float'
	) );
	$wp_customize->add_control( 'videography_filmmaker_featured_image_border_radius', array(
		'label' => __( 'Featured image border radius','videography-filmmaker' ),
		'section' => 'videography_filmmaker_blog_post',
		'type'  => 'number',
		'input_attrs' => array(
			'step' => 1,
			'min'  => 0,
			'max'  => 50,
		),
	) );

    $wp_customize->add_setting( 'videography_filmmaker_featured_image_box_shadow', array(
		'default' => 0,
		'sanitize_callback'	=> 'videography_filmmaker_sanitize_float'
	) );
	$wp_customize->add_control( 'videography_filmmaker_featured_image_box_shadow', array(
		'label' => __( 'Featured image box shadow','videography-filmmaker' ),
		'section' => 'videography_filmmaker_blog_post',
		'type'  => 'number',
		'input_attrs' => array(
			'step' => 1,
			'min'  => 0,
			'max'  => 50,
		),
	) );

    $wp_customize->add_setting('videography_filmmaker_post_content',array(
    	'default' => 'Excerpt Content',
        'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control('videography_filmmaker_post_content',array(
        'type' => 'radio',
        'label' => __('Post Content Type','videography-filmmaker'),
        'section' => 'videography_filmmaker_blog_post',
        'choices' => array(
            'No Content' => __('No Content','videography-filmmaker'),
            'Full Content' => __('Full Content','videography-filmmaker'),
            'Excerpt Content' => __('Excerpt Content','videography-filmmaker'),
        ),
	) );

    $wp_customize->add_setting( 'videography_filmmaker_post_excerpt_length', array(
		'default'              => 20,
		'sanitize_callback'	=> 'videography_filmmaker_sanitize_float'
	) );
	$wp_customize->add_control( 'videography_filmmaker_post_excerpt_length', array(
		'label' => esc_html__( 'Post Excerpt Length','videography-filmmaker' ),
		'section'  => 'videography_filmmaker_blog_post',
		'type'  => 'number',
		'settings' => 'videography_filmmaker_post_excerpt_length',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 0,
			'max'              => 50,
		),
	) );

	$wp_customize->add_setting( 'videography_filmmaker_button_excerpt_suffix', array(
		'default'   => __('[...]','videography-filmmaker' ),
		'sanitize_callback'	=> 'sanitize_text_field'
	) );
	$wp_customize->add_control( 'videography_filmmaker_button_excerpt_suffix', array(
		'label'       => esc_html__( 'Excerpt Suffix','videography-filmmaker' ),
		'section'     => 'videography_filmmaker_blog_post',
		'type'        => 'text',
		'settings' => 'videography_filmmaker_button_excerpt_suffix'
	) );

	$wp_customize->add_setting( 'videography_filmmaker_post_button_text', array(
		'default'   => esc_html__('Read More','videography-filmmaker' ),
		'sanitize_callback'	=> 'sanitize_text_field'
	) );
	$wp_customize->add_control( 'videography_filmmaker_post_button_text', array(
		'label' => esc_html__('Post Button Text','videography-filmmaker' ),
		'section'     => 'videography_filmmaker_blog_post',
		'type'        => 'text',
		'settings'    => 'videography_filmmaker_post_button_text'
	) );

	$wp_customize->add_setting('videography_filmmaker_top_button_padding',array(
		'default'=> '',
		'sanitize_callback'	=> 'videography_filmmaker_sanitize_float'
	));
	$wp_customize->add_control('videography_filmmaker_top_button_padding',array(
		'label'	=> __('Top Bottom Button Padding','videography-filmmaker'),
		'input_attrs' => array(
            'step' => 1,
			'min'  => 0,
			'max'  => 50,
        ),
		'section'=> 'videography_filmmaker_blog_post',
		'type'=> 'number',
	));

	$wp_customize->add_setting('videography_filmmaker_left_button_padding',array(
		'default'=> '',
		'sanitize_callback'	=> 'videography_filmmaker_sanitize_float'
	));
	$wp_customize->add_control('videography_filmmaker_left_button_padding',array(
		'label'	=> __('Left Right Button Padding','videography-filmmaker'),
		'input_attrs' => array(
            'step'             => 1,
			'min'              => 0,
			'max'              => 50,
        ),
		'section'=> 'videography_filmmaker_blog_post',
		'type'=> 'number',
	));

	$wp_customize->add_setting( 'videography_filmmaker_button_border_radius', array(
		'default'=> '0',
		'sanitize_callback'	=> 'videography_filmmaker_sanitize_float'
	) );
	$wp_customize->add_control('videography_filmmaker_button_border_radius', array(
        'label'  => __('Button Border Radius','videography-filmmaker'),
        'type'=> 'number',
        'section'  => 'videography_filmmaker_blog_post',
        'input_attrs' => array(
        	'step' => 1,
            'min' => 0,
            'max' => 50,
        ),
    ));

    $wp_customize->add_setting( 'videography_filmmaker_post_blocks', array(
        'default'			=> 'Without box',
        'sanitize_callback'	=> 'videography_filmmaker_sanitize_choices'
    ));
    $wp_customize->add_control( 'videography_filmmaker_post_blocks', array(
        'section' => 'videography_filmmaker_blog_post',
        'type' => 'select',
        'label' => __( 'Post blocks', 'videography-filmmaker' ),
        'choices' => array(
            'Within box'  => __( 'Within box', 'videography-filmmaker' ),
            'Without box' => __( 'Without box', 'videography-filmmaker' ),
    )));

    $wp_customize->add_setting('videography_filmmaker_navigation_hide',array(
       'default' => true,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ));
    $wp_customize->add_control('videography_filmmaker_navigation_hide',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Post Navigation','videography-filmmaker'),
       'section' => 'videography_filmmaker_blog_post'
    ));

    $wp_customize->add_setting( 'videography_filmmaker_post_navigation_type', array(
        'default'			=> 'numbers',
        'sanitize_callback'	=> 'videography_filmmaker_sanitize_choices'
    ));
    $wp_customize->add_control( 'videography_filmmaker_post_navigation_type', array(
        'section' => 'videography_filmmaker_blog_post',
        'type' => 'select',
        'label' => __( 'Post Navigation Type', 'videography-filmmaker' ),
        'choices' => array(
            'numbers'  => __( 'Number', 'videography-filmmaker' ),
            'next-prev' => __( 'Next/Prev Button', 'videography-filmmaker' ),
    )));

    $wp_customize->add_setting( 'videography_filmmaker_post_navigation_position', array(
        'default'			=> 'bottom',
        'sanitize_callback'	=> 'videography_filmmaker_sanitize_choices'
    ));
    $wp_customize->add_control( 'videography_filmmaker_post_navigation_position', array(
        'section' => 'videography_filmmaker_blog_post',
        'type' => 'select',
        'label' => __( 'Post Navigation Position', 'videography-filmmaker' ),
        'choices' => array(
            'top'  => __( 'Top', 'videography-filmmaker' ),
            'bottom' => __( 'Bottom', 'videography-filmmaker' ),
            'both' => __( 'Both', 'videography-filmmaker' ),
    )));

    //Single Post Settings
	$wp_customize->add_section('videography_filmmaker_single_post',array(
		'title'	=> __('Single Post Settings','videography-filmmaker'),
		'panel' => 'videography_filmmaker_panel_id',
	));	

	$wp_customize->add_setting('videography_filmmaker_feature_image',array(
       'default' => true,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ));
    $wp_customize->add_control('videography_filmmaker_feature_image',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Feature Image','videography-filmmaker'),
       'section' => 'videography_filmmaker_single_post'
    ));

    $wp_customize->add_setting('videography_filmmaker_tags',array(
       'default' => true,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ));
    $wp_customize->add_control('videography_filmmaker_tags',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Tags','videography-filmmaker'),
       'section' => 'videography_filmmaker_single_post'
    ));

    $wp_customize->add_setting('videography_filmmaker_comment',array(
       'default' => true,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ));
    $wp_customize->add_control('videography_filmmaker_comment',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Comment','videography-filmmaker'),
       'section' => 'videography_filmmaker_single_post'
    ));

     $wp_customize->add_setting('videography_filmmaker_show_hide_single_post_categories',array(
		'default' => true,
		'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
 	));
 	$wp_customize->add_control('videography_filmmaker_show_hide_single_post_categories',array(
		'type' => 'checkbox',
		'label' => __('Single Post Categories','videography-filmmaker'),
		'section' => 'videography_filmmaker_single_post'
	));

    $wp_customize->add_setting( 'videography_filmmaker_comment_width', array(
		'default' => 100,
		'sanitize_callback'	=> 'videography_filmmaker_sanitize_float'
	) );
	$wp_customize->add_control( 'videography_filmmaker_comment_width', array(
		'label' => __( 'Comment Textarea Width', 'videography-filmmaker'),
		'section' => 'videography_filmmaker_single_post',
		'type' => 'number',
		'settings' => 'videography_filmmaker_comment_width',
		'input_attrs' => array(
			'step' => 1,
			'min' => 0,
			'max' => 100,
		),
	) );

    $wp_customize->add_setting('videography_filmmaker_comment_title',array(
       'default' => __('Leave a Reply','videography-filmmaker'),
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('videography_filmmaker_comment_title',array(
       'type' => 'text',
       'label' => __('Comment form Title','videography-filmmaker'),
       'section' => 'videography_filmmaker_single_post'
    ));

    $wp_customize->add_setting('videography_filmmaker_comment_submit_text',array(
       'default' => __('Post Comment','videography-filmmaker'),
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('videography_filmmaker_comment_submit_text',array(
       'type' => 'text',
       'label' => __('Comment Button Text','videography-filmmaker'),
       'section' => 'videography_filmmaker_single_post'
    ));

    $wp_customize->add_setting('videography_filmmaker_nav_links',array(
       'default' => true,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ));
    $wp_customize->add_control('videography_filmmaker_nav_links',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Nav Links','videography-filmmaker'),
       'section' => 'videography_filmmaker_single_post'
    ));

    $wp_customize->add_setting('videography_filmmaker_prev_text',array(
       'default' => '',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('videography_filmmaker_prev_text',array(
       'type' => 'text',
       'label' => __('Previous Navigation Text','videography-filmmaker'),
       'section' => 'videography_filmmaker_single_post'
    ));

    $wp_customize->add_setting('videography_filmmaker_next_text',array(
       'default' => '',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('videography_filmmaker_next_text',array(
       'type' => 'text',
       'label' => __('Next Navigation Text','videography-filmmaker'),
       'section' => 'videography_filmmaker_single_post'
    ));

    $wp_customize->add_setting('videography_filmmaker_related_posts',array(
       'default' => true,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ));
    $wp_customize->add_control('videography_filmmaker_related_posts',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Related Posts','videography-filmmaker'),
       'section' => 'videography_filmmaker_single_post'
    ));

    $wp_customize->add_setting('videography_filmmaker_related_posts_title',array(
       'default' => '',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('videography_filmmaker_related_posts_title',array(
       'type' => 'text',
       'label' => __('Related Posts Title','videography-filmmaker'),
       'section' => 'videography_filmmaker_single_post'
    ));

    $wp_customize->add_setting( 'videography_filmmaker_related_post_count', array(
		'default' => 3,
		'sanitize_callback'	=> 'videography_filmmaker_sanitize_float'
	) );
	$wp_customize->add_control( 'videography_filmmaker_related_post_count', array(
		'label' => esc_html__( 'Related Posts Count','videography-filmmaker' ),
		'section' => 'videography_filmmaker_single_post',
		'type' => 'number',
		'settings' => 'videography_filmmaker_related_post_count',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 0,
			'max'              => 6,
		),
	) );

    $wp_customize->add_setting( 'videography_filmmaker_post_order', array(
        'default' => 'categories',
        'sanitize_callback'	=> 'videography_filmmaker_sanitize_choices'
    ));
    $wp_customize->add_control( 'videography_filmmaker_post_order', array(
        'section' => 'videography_filmmaker_single_post',
        'type' => 'radio',
        'label' => __( 'Related Posts Order By', 'videography-filmmaker' ),
        'choices' => array(
            'categories' => __('Categories', 'videography-filmmaker'),
            'tags' => __( 'Tags', 'videography-filmmaker' ),
    )));

    //404 page settings
	$wp_customize->add_section('videography_filmmaker_404_page',array(
		'title'	=> __('404 & No Result Page Settings','videography-filmmaker'),
		'priority'	=> null,
		'panel' => 'videography_filmmaker_panel_id',
	));

	$wp_customize->add_setting('videography_filmmaker_404_title',array(
       'default' => '',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('videography_filmmaker_404_title',array(
       'type' => 'text',
       'label' => __('404 Page Title','videography-filmmaker'),
       'section' => 'videography_filmmaker_404_page'
    ));

    $wp_customize->add_setting('videography_filmmaker_404_text',array(
       'default' => '',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('videography_filmmaker_404_text',array(
       'type' => 'text',
       'label' => __('404 Page Text','videography-filmmaker'),
       'section' => 'videography_filmmaker_404_page'
    ));

    $wp_customize->add_setting('videography_filmmaker_404_button_text',array(
       'default' => '',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('videography_filmmaker_404_button_text',array(
       'type' => 'text',
       'label' => __('404 Page Button Text','videography-filmmaker'),
       'section' => 'videography_filmmaker_404_page'
    ));

    $wp_customize->add_setting('videography_filmmaker_no_result_title',array(
       'default' => '',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('videography_filmmaker_no_result_title',array(
       'type' => 'text',
       'label' => __('No Result Page Title','videography-filmmaker'),
       'section' => 'videography_filmmaker_404_page'
    ));

    $wp_customize->add_setting('videography_filmmaker_no_result_text',array(
       'default' => '',
       'sanitize_callback'	=> 'sanitize_text_field'
    ));
    $wp_customize->add_control('videography_filmmaker_no_result_text',array(
       'type' => 'text',
       'label' => __('No Result Page Text','videography-filmmaker'),
       'section' => 'videography_filmmaker_404_page'
    ));

    $wp_customize->add_setting('videography_filmmaker_show_search_form',array(
        'default' => true,
        'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
	));
	$wp_customize->add_control('videography_filmmaker_show_search_form',array(
     	'type' => 'checkbox',
      	'label' => __('Show/Hide Search Form','videography-filmmaker'),
      	'section' => 'videography_filmmaker_404_page',
	));

	//Footer
	$wp_customize->add_section('videography_filmmaker_footer_section',array(
		'title'	=> __('Footer Section','videography-filmmaker'),
		'priority'	=> null,
		'panel' => 'videography_filmmaker_panel_id',
	));

	$wp_customize->selective_refresh->add_partial(
		'videography_filmmaker_show_back_to_top',
		array(
			'selector'        => '.scrollup',
			'render_callback' => 'videography_filmmaker_customize_partial_videography_filmmaker_show_back_to_top',
		)
	);

	$wp_customize->add_setting('videography_filmmaker_show_back_to_top',array(
        'default' => 'true',
        'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
	));
	$wp_customize->add_control('videography_filmmaker_show_back_to_top',array(
     	'type' => 'checkbox',
      	'label' => __('Show/Hide Back to Top Button','videography-filmmaker'),
      	'section' => 'videography_filmmaker_footer_section',
	));

	$wp_customize->add_setting('videography_filmmaker_back_to_top_icon',array(
		'default'	=> 'fas fa-arrow-up',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Videography_Filmmaker_Icon_Changer(
        $wp_customize, 'videography_filmmaker_back_to_top_icon',array(
		'label'	=> __('Back to Top Icon','videography-filmmaker'),
		'section'	=> 'videography_filmmaker_footer_section',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('videography_filmmaker_back_to_top_text',array(
		'default'	=> __('Back to Top','videography-filmmaker'),
		'sanitize_callback'	=> 'sanitize_text_field',
	));	
	$wp_customize->add_control('videography_filmmaker_back_to_top_text',array(
		'label'	=> __('Back to Top Button Text','videography-filmmaker'),
		'section'	=> 'videography_filmmaker_footer_section',
		'type'		=> 'text'
	));

	$wp_customize->add_setting('videography_filmmaker_back_to_top_alignment',array(
        'default' => 'Right',
        'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control('videography_filmmaker_back_to_top_alignment',array(
        'type' => 'select',
        'label' => __('Back to Top Button Alignment','videography-filmmaker'),
        'section' => 'videography_filmmaker_footer_section',
        'choices' => array(
            'Left' => __('Left','videography-filmmaker'),
            'Right' => __('Right','videography-filmmaker'),
            'Center' => __('Center','videography-filmmaker'),
        ),
	) );

	$wp_customize->add_setting('videography_filmmaker_footer_background_color', array(
		'default'           => '#000',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'videography_filmmaker_footer_background_color', array(
		'label'    => __('Footer Background Color', 'videography-filmmaker'),
		'section'  => 'videography_filmmaker_footer_section',
	)));

	$wp_customize->add_setting('videography_filmmaker_footer_background_img',array(
		'default'	=> '',
		'sanitize_callback'	=> 'esc_url_raw',
	));
	$wp_customize->add_control( new WP_Customize_Image_Control($wp_customize,'videography_filmmaker_footer_background_img',array(
        'label' => __('Footer Background Image','videography-filmmaker'),
        'section' => 'videography_filmmaker_footer_section'
	)));

	$wp_customize->add_setting('videography_filmmaker_footer_widget_layout',array(
        'default'           => '4',
        'sanitize_callback' => 'videography_filmmaker_sanitize_choices',
    ));
    $wp_customize->add_control('videography_filmmaker_footer_widget_layout',array(
        'type' => 'radio',
        'label'  => __('Footer widget layout', 'videography-filmmaker'),
        'section'     => 'videography_filmmaker_footer_section',
        'description' => __('Select the number of widget areas you want in the footer. After that, go to Appearance > Widgets and add your widgets.', 'videography-filmmaker'),
        'choices' => array(
            '1'     => __('One', 'videography-filmmaker'),
            '2'     => __('Two', 'videography-filmmaker'),
            '3'     => __('Three', 'videography-filmmaker'),
            '4'     => __('Four', 'videography-filmmaker')
        ),
    ));

    $wp_customize->add_setting('videography_filmmaker_copyright_alignment',array(
        'default' => 'Center',
        'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control('videography_filmmaker_copyright_alignment',array(
        'type' => 'select',
        'label' => __('Copyright Alignment','videography-filmmaker'),
        'section' => 'videography_filmmaker_footer_section',
        'choices' => array(
            'Left' => __('Left','videography-filmmaker'),
            'Right' => __('Right','videography-filmmaker'),
            'Center' => __('Center','videography-filmmaker'),
        ),
	) );

	$wp_customize->add_setting('videography_filmmaker_copyright_fontsize',array(
		'default'	=> 16,
		'sanitize_callback'	=> 'videography_filmmaker_sanitize_float',
	));	
	$wp_customize->add_control('videography_filmmaker_copyright_fontsize',array(
		'label'	=> __('Copyright Font Size','videography-filmmaker'),
		'section'	=> 'videography_filmmaker_footer_section',
		'type'		=> 'number'
	));

	$wp_customize->add_setting('videography_filmmaker_copyright_top_bottom_padding',array(
		'default'	=> 15,
		'sanitize_callback'	=> 'videography_filmmaker_sanitize_float',
	));	
	$wp_customize->add_control('videography_filmmaker_copyright_top_bottom_padding',array(
		'label'	=> __('Copyright Top Bottom Padding','videography-filmmaker'),
		'section'	=> 'videography_filmmaker_footer_section',
		'type'		=> 'number'
	));

    $wp_customize->selective_refresh->add_partial(
		'videography_filmmaker_footer_copy',
		array(
			'selector'        => '#footer p',
			'render_callback' => 'videography_filmmaker_customize_partial_videography_filmmaker_footer_copy',
		)
	);
	
	$wp_customize->add_setting('videography_filmmaker_footer_copy',array(
		'default'	=> '',
		'sanitize_callback'	=> 'sanitize_text_field',
	));	
	$wp_customize->add_control('videography_filmmaker_footer_copy',array(
		'label'	=> __('Copyright Text','videography-filmmaker'),
		'section'	=> 'videography_filmmaker_footer_section',
		'type'		=> 'text'
	));

	//Mobile Media Section
	$wp_customize->add_section( 'videography_filmmaker_mobile_media_options' , array(
    	'title'      => __( 'Mobile Media Options', 'videography-filmmaker' ),
		'priority'   => null,
		'panel' => 'videography_filmmaker_panel_id'
	) );

	$wp_customize->add_setting('videography_filmmaker_responsive_open_menu_icon',array(
		'default'	=> 'fas fa-bars',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Videography_Filmmaker_Icon_Changer(
        $wp_customize, 'videography_filmmaker_responsive_open_menu_icon',array(
		'label'	=> __('Open Menu Icon','videography-filmmaker'),
		'section'	=> 'videography_filmmaker_mobile_media_options',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('videography_filmmaker_responsive_close_menu_icon',array(
		'default'	=> 'fas fa-times',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new Videography_Filmmaker_Icon_Changer(
        $wp_customize, 'videography_filmmaker_responsive_close_menu_icon',array(
		'label'	=> __('Close Menu Icon','videography-filmmaker'),
		'section'	=> 'videography_filmmaker_mobile_media_options',
		'type'		=> 'icon'
	)));

    $wp_customize->add_setting('videography_filmmaker_responsive_show_back_to_top',array(
        'default' => true,
        'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
	));
	$wp_customize->add_control('videography_filmmaker_responsive_show_back_to_top',array(
     	'type' => 'checkbox',
      	'label' => __('Show / Hide Back to Top Button','videography-filmmaker'),
      	'section' => 'videography_filmmaker_mobile_media_options',
	));

	$wp_customize->add_setting( 'videography_filmmaker_responsive_preloader_hide',array(
		'default' => false,
      	'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ) );
    $wp_customize->add_control('videography_filmmaker_responsive_preloader_hide',array(
    	'type' => 'checkbox',
        'label' => __( 'Show / Hide Preloader','videography-filmmaker' ),
        'section' => 'videography_filmmaker_mobile_media_options'
    ));

	//Woocommerce Section
	$wp_customize->add_section( 'videography_filmmaker_woocommerce_options' , array(
    	'title'      => __( 'Additional WooCommerce Options', 'videography-filmmaker' ),
		'priority'   => null,
		'panel' => 'videography_filmmaker_panel_id'
	) );

	// Product Columns
	$wp_customize->add_setting( 'videography_filmmaker_products_per_row' , array(
		'default'           => '3',
		'sanitize_callback' => 'videography_filmmaker_sanitize_choices',
	) );

	$wp_customize->add_control('videography_filmmaker_products_per_row', array(
		'label' => __( 'Product per row', 'videography-filmmaker' ),
		'section'  => 'videography_filmmaker_woocommerce_options',
		'type'     => 'select',
		'choices'  => array(
			'2' => '2',
			'3' => '3',
			'4' => '4',
		),
	) );

	$wp_customize->add_setting('videography_filmmaker_product_per_page',array(
		'default'	=> '9',
		'sanitize_callback'	=> 'videography_filmmaker_sanitize_float'
	));	
	$wp_customize->add_control('videography_filmmaker_product_per_page',array(
		'label'	=> __('Product per page','videography-filmmaker'),
		'section'	=> 'videography_filmmaker_woocommerce_options',
		'type'		=> 'number'
	));

	$wp_customize->add_setting('videography_filmmaker_shop_sidebar',array(
       'default' => true,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ));
    $wp_customize->add_control('videography_filmmaker_shop_sidebar',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Shop page sidebar','videography-filmmaker'),
       'section' => 'videography_filmmaker_woocommerce_options',
    ));

	$wp_customize->add_setting('videography_filmmaker_shop_page_pagination',array(
       'default' => true,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ));
    $wp_customize->add_control('videography_filmmaker_shop_page_pagination',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Shop page pagination','videography-filmmaker'),
       'section' => 'videography_filmmaker_woocommerce_options',
    ));

    $wp_customize->add_setting('videography_filmmaker_product_page_sidebar',array(
       'default' => true,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ));
    $wp_customize->add_control('videography_filmmaker_product_page_sidebar',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Product page sidebar','videography-filmmaker'),
       'section' => 'videography_filmmaker_woocommerce_options',
    ));

    $wp_customize->add_setting('videography_filmmaker_related_product',array(
       'default' => true,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ));
    $wp_customize->add_control('videography_filmmaker_related_product',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable Related product','videography-filmmaker'),
       'section' => 'videography_filmmaker_woocommerce_options',
    ));

	$wp_customize->add_setting( 'videography_filmmaker_woocommerce_button_padding_top',array(
		'default' => 10,
		'sanitize_callback' => 'videography_filmmaker_sanitize_float'
	));
	$wp_customize->add_control( 'videography_filmmaker_woocommerce_button_padding_top',	array(
		'label' => esc_html__( 'Button Top Bottom Padding','videography-filmmaker' ),
		'type' => 'number',
		'section' => 'videography_filmmaker_woocommerce_options',
		'input_attrs' => array(
			'min' => 0,
			'max' => 50,
			'step' => 1,
		),
	));

	$wp_customize->add_setting( 'videography_filmmaker_woocommerce_button_padding_right',array(
	 	'default' => 20,
	 	'sanitize_callback' => 'videography_filmmaker_sanitize_float'
	));
	$wp_customize->add_control('videography_filmmaker_woocommerce_button_padding_right',	array(
	 	'label' => esc_html__( 'Button Right Left Padding','videography-filmmaker' ),
		'type' => 'number',
		'section' => 'videography_filmmaker_woocommerce_options',
	 	'input_attrs' => array(
			'min' => 0,
			'max' => 50,
	 		'step' => 1,
		),
	));

	$wp_customize->add_setting( 'videography_filmmaker_woocommerce_button_border_radius',array(
		'default' => 0,
		'sanitize_callback' => 'videography_filmmaker_sanitize_float'
	));
	$wp_customize->add_control('videography_filmmaker_woocommerce_button_border_radius',array(
		'label' => esc_html__( 'Button Border Radius','videography-filmmaker' ),
		'type' => 'number',
		'section' => 'videography_filmmaker_woocommerce_options',
		'input_attrs' => array(
			'min' => 0,
			'max' => 50,
			'step' => 1,
		),
	));

    $wp_customize->add_setting('videography_filmmaker_woocommerce_product_border',array(
       'default' => false,
       'sanitize_callback'	=> 'videography_filmmaker_sanitize_checkbox'
    ));
    $wp_customize->add_control('videography_filmmaker_woocommerce_product_border',array(
       'type' => 'checkbox',
       'label' => __('Enable / Disable product border','videography-filmmaker'),
       'section' => 'videography_filmmaker_woocommerce_options',
    ));

	$wp_customize->add_setting( 'videography_filmmaker_woocommerce_product_padding_top',array(
		'default' => 0,
		'sanitize_callback' => 'videography_filmmaker_sanitize_float'
	));
	$wp_customize->add_control('videography_filmmaker_woocommerce_product_padding_top', array(
		'label' => esc_html__( 'Product Top Bottom Padding','videography-filmmaker' ),
		'type' => 'number',
		'section' => 'videography_filmmaker_woocommerce_options',
		'input_attrs' => array(
			'min' => 0,
			'max' => 50,
			'step' => 1,
		),
	));

	$wp_customize->add_setting( 'videography_filmmaker_woocommerce_product_padding_right',array(
		'default' => 0,
		'sanitize_callback' => 'videography_filmmaker_sanitize_float'
	));
	$wp_customize->add_control('videography_filmmaker_woocommerce_product_padding_right', array(
		'label' => esc_html__( 'Product Right Left Padding','videography-filmmaker' ),
		'type' => 'number',
		'section' => 'videography_filmmaker_woocommerce_options',
		'input_attrs' => array(
			'min' => 0,
			'max' => 50,
			'step' => 1,
		),
	));

	$wp_customize->add_setting( 'videography_filmmaker_woocommerce_product_border_radius',array(
		'default' => 0,
		'sanitize_callback' => 'videography_filmmaker_sanitize_float'
	));
	$wp_customize->add_control('videography_filmmaker_woocommerce_product_border_radius',array(
		'label' => esc_html__( 'Product Border Radius','videography-filmmaker' ),
		'type' => 'number',
		'section' => 'videography_filmmaker_woocommerce_options',
		'input_attrs' => array(
			'min' => 0,
			'max' => 50,
			'step' => 1,
		),
	));

	$wp_customize->add_setting( 'videography_filmmaker_woocommerce_product_box_shadow',array(
		'default' => 0,
		'sanitize_callback' => 'videography_filmmaker_sanitize_float'
	));
	$wp_customize->add_control( 'videography_filmmaker_woocommerce_product_box_shadow',array(
		'label' => esc_html__( 'Product Box Shadow','videography-filmmaker' ),
		'type' => 'number',
		'section' => 'videography_filmmaker_woocommerce_options',
		'input_attrs' => array(
			'min' => 0,
			'max' => 50,
			'step' => 1,
		),
	));

	$wp_customize->add_setting('videography_filmmaker_sale_position',array(
        'default' => 'left',
        'sanitize_callback' => 'videography_filmmaker_sanitize_choices'
	));
	$wp_customize->add_control('videography_filmmaker_sale_position',array(
        'type' => 'select',
        'label' => __('Sale badge Position','videography-filmmaker'),
        'section' => 'videography_filmmaker_woocommerce_options',
        'choices' => array(
            'left' => __('Left','videography-filmmaker'),
            'right' => __('Right','videography-filmmaker'),
        ),
	) );

	$wp_customize->add_setting( 'videography_filmmaker_woocommerce_sale_top_padding',array(
		'default' => 0,
		'sanitize_callback' => 'videography_filmmaker_sanitize_float'
	));
	$wp_customize->add_control( 'videography_filmmaker_woocommerce_sale_top_padding',	array(
		'label' => esc_html__( 'Sale Top Bottom Padding','videography-filmmaker' ),
		'type' => 'number',
		'section' => 'videography_filmmaker_woocommerce_options',
		'input_attrs' => array(
			'min' => 0,
			'max' => 50,
			'step' => 1,
		),
	));

	$wp_customize->add_setting( 'videography_filmmaker_woocommerce_sale_left_padding',array(
	 	'default' => 0,
	 	'sanitize_callback' => 'videography_filmmaker_sanitize_float'
	));
	$wp_customize->add_control('videography_filmmaker_woocommerce_sale_left_padding',	array(
	 	'label' => esc_html__( 'Sale Right Left Padding','videography-filmmaker' ),
		'type' => 'number',
		'section' => 'videography_filmmaker_woocommerce_options',
	 	'input_attrs' => array(
			'min' => 0,
			'max' => 50,
	 		'step' => 1,
		),
	));

	$wp_customize->add_setting( 'videography_filmmaker_woocommerce_sale_border_radius',array(
		'default' => 0,
		'sanitize_callback' => 'videography_filmmaker_sanitize_float'
	));
	$wp_customize->add_control('videography_filmmaker_woocommerce_sale_border_radius',array(
		'label' => esc_html__( 'Sale Border Radius','videography-filmmaker' ),
		'type' => 'number',
		'section' => 'videography_filmmaker_woocommerce_options',
		'input_attrs' => array(
			'min' => 0,
			'max' => 50,
			'step' => 1,
		),
	));

	$wp_customize->add_setting( 'videography_filmmaker_product_sale_font_size',array(
		'default' => '',
		'sanitize_callback' => 'videography_filmmaker_sanitize_float'
	));
	$wp_customize->add_control('videography_filmmaker_product_sale_font_size',array(
		'label' => esc_html__( 'Sale Font Size','videography-filmmaker' ),
		'type' => 'number',
		'section' => 'videography_filmmaker_woocommerce_options',
		'input_attrs' => array(
			'min' => 0,
			'max' => 50,
			'step' => 1,
		),
	));
}
add_action( 'customize_register', 'videography_filmmaker_customize_register' );

// logo resize
load_template( trailingslashit( get_template_directory() ) . '/inc/logo/logo-width.php' );


/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class Videography_Filmmaker_Customize {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	 */
	public function sections( $manager ) {

		// Load custom sections.
		load_template( trailingslashit( get_template_directory() ) . '/inc/section-pro.php' );
		
		// Register custom section types.
		$manager->register_section_type( 'Videography_Filmmaker_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new Videography_Filmmaker_Customize_Section_Pro(
				$manager,
				'videography_filmmaker_example_1',
				array(
					'priority' => 9,
					'title'    => esc_html__( 'Videography Filmmaker Pro', 'videography-filmmaker' ),
					'pro_text' => esc_html__( 'Go Pro','videography-filmmaker' ),
					'pro_url'  => esc_url( 'https://www.themescaliber.com/themes/filmmaker-wordpress-theme/' ),
				)
			)
		);
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'videography-filmmaker-customize-controls', trailingslashit( esc_url(get_template_directory_uri()) ) . '/js/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'videography-filmmaker-customize-controls', trailingslashit( esc_url(get_template_directory_uri()) ) . '/css/customize-controls.css' );
	}
}

// Doing this customizer thang!
Videography_Filmmaker_Customize::get_instance();