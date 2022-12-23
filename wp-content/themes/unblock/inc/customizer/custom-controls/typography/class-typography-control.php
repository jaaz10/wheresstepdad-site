<?php
/**
 * Customizer Typography Control
 * @package unBlock
 * @since 1.0.0
 * 
 * Taken from Kirki.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( ! class_exists( 'unBlock_Typography_Control' ) ) {
    
    class unBlock_Typography_Control extends WP_Customize_Control {
    
    	public $tooltip = '';
    	public $js_vars = array();
    	public $output = array();
    	public $option_type = 'theme_mod';
    	public $type = 'typography';
    
    	/**
    	 * Refresh the parameters passed to the JavaScript via JSON.
    	 *
    	 * @access public
    	 * @return void
    	 */
    	public function to_json() {
    		parent::to_json();
    
    		if ( isset( $this->default ) ) {
    			$this->json['default'] = $this->default;
    		} else {
    			$this->json['default'] = $this->setting->default;
    		}
    		$this->json['js_vars'] = $this->js_vars;
    		$this->json['output']  = $this->output;
    		$this->json['value']   = $this->value();
    		$this->json['choices'] = $this->choices;
    		$this->json['link']    = $this->get_link();
    		$this->json['tooltip'] = $this->tooltip;
    		$this->json['id']      = $this->id;
    		$this->json['l10n']    = apply_filters( 'unblock-typography-control/il8n/strings', array(
    			'on'                 => esc_attr__( 'ON', 'unblock' ),
    			'off'                => esc_attr__( 'OFF', 'unblock' ),
    			'all'                => esc_attr__( 'All', 'unblock' ),
    			'cyrillic'           => esc_attr__( 'Cyrillic', 'unblock' ),
    			'cyrillic-ext'       => esc_attr__( 'Cyrillic Extended', 'unblock' ),
    			'devanagari'         => esc_attr__( 'Devanagari', 'unblock' ),
    			'greek'              => esc_attr__( 'Greek', 'unblock' ),
    			'greek-ext'          => esc_attr__( 'Greek Extended', 'unblock' ),
    			'khmer'              => esc_attr__( 'Khmer', 'unblock' ),
    			'latin'              => esc_attr__( 'Latin', 'unblock' ),
    			'latin-ext'          => esc_attr__( 'Latin Extended', 'unblock' ),
    			'vietnamese'         => esc_attr__( 'Vietnamese', 'unblock' ),
    			'hebrew'             => esc_attr__( 'Hebrew', 'unblock' ),
    			'arabic'             => esc_attr__( 'Arabic', 'unblock' ),
    			'bengali'            => esc_attr__( 'Bengali', 'unblock' ),
    			'gujarati'           => esc_attr__( 'Gujarati', 'unblock' ),
    			'tamil'              => esc_attr__( 'Tamil', 'unblock' ),
    			'telugu'             => esc_attr__( 'Telugu', 'unblock' ),
    			'thai'               => esc_attr__( 'Thai', 'unblock' ),
    			'serif'              => _x( 'Serif', 'font style', 'unblock' ),
    			'sans-serif'         => _x( 'Sans Serif', 'font style', 'unblock' ),
    			'monospace'          => _x( 'Monospace', 'font style', 'unblock' ),
    			'font-family'        => esc_attr__( 'Font Family', 'unblock' ),
    			'font-size'          => esc_attr__( 'Font Size', 'unblock' ),
    			'font-weight'        => esc_attr__( 'Font Weight', 'unblock' ),
    			'line-height'        => esc_attr__( 'Line Height', 'unblock' ),
    			'font-style'         => esc_attr__( 'Font Style', 'unblock' ),
    			'letter-spacing'     => esc_attr__( 'Letter Spacing', 'unblock' ),
    			'text-align'         => esc_attr__( 'Text Align', 'unblock' ),
    			'text-transform'     => esc_attr__( 'Text Transform', 'unblock' ),
    			'none'               => esc_attr__( 'None', 'unblock' ),
    			'uppercase'          => esc_attr__( 'Uppercase', 'unblock' ),
    			'lowercase'          => esc_attr__( 'Lowercase', 'unblock' ),
    			'top'                => esc_attr__( 'Top', 'unblock' ),
    			'bottom'             => esc_attr__( 'Bottom', 'unblock' ),
    			'left'               => esc_attr__( 'Left', 'unblock' ),
    			'right'              => esc_attr__( 'Right', 'unblock' ),
    			'center'             => esc_attr__( 'Center', 'unblock' ),
    			'justify'            => esc_attr__( 'Justify', 'unblock' ),
    			'color'              => esc_attr__( 'Color', 'unblock' ),
    			'select-font-family' => esc_attr__( 'Select a font-family', 'unblock' ),
    			'variant'            => esc_attr__( 'Variant', 'unblock' ),
    			'style'              => esc_attr__( 'Style', 'unblock' ),
    			'size'               => esc_attr__( 'Size', 'unblock' ),
    			'height'             => esc_attr__( 'Height', 'unblock' ),
    			'spacing'            => esc_attr__( 'Spacing', 'unblock' ),
    			'ultra-light'        => esc_attr__( 'Ultra-Light 100', 'unblock' ),
    			'ultra-light-italic' => esc_attr__( 'Ultra-Light 100 Italic', 'unblock' ),
    			'light'              => esc_attr__( 'Light 200', 'unblock' ),
    			'light-italic'       => esc_attr__( 'Light 200 Italic', 'unblock' ),
    			'book'               => esc_attr__( 'Book 300', 'unblock' ),
    			'book-italic'        => esc_attr__( 'Book 300 Italic', 'unblock' ),
    			'regular'            => esc_attr__( 'Normal 400', 'unblock' ),
    			'italic'             => esc_attr__( 'Normal 400 Italic', 'unblock' ),
    			'medium'             => esc_attr__( 'Medium 500', 'unblock' ),
    			'medium-italic'      => esc_attr__( 'Medium 500 Italic', 'unblock' ),
    			'semi-bold'          => esc_attr__( 'Semi-Bold 600', 'unblock' ),
    			'semi-bold-italic'   => esc_attr__( 'Semi-Bold 600 Italic', 'unblock' ),
    			'bold'               => esc_attr__( 'Bold 700', 'unblock' ),
    			'bold-italic'        => esc_attr__( 'Bold 700 Italic', 'unblock' ),
    			'extra-bold'         => esc_attr__( 'Extra-Bold 800', 'unblock' ),
    			'extra-bold-italic'  => esc_attr__( 'Extra-Bold 800 Italic', 'unblock' ),
    			'ultra-bold'         => esc_attr__( 'Ultra-Bold 900', 'unblock' ),
    			'ultra-bold-italic'  => esc_attr__( 'Ultra-Bold 900 Italic', 'unblock' ),
    			'invalid-value'      => esc_attr__( 'Invalid Value', 'unblock' ),
    		) );
    
    		$defaults = array( 'font-family'=> false );
    
    		$this->json['default'] = wp_parse_args( $this->json['default'], $defaults );
    	}
    
    	/**
    	 * Enqueue scripts and styles.
    	 *
    	 * @access public
    	 * @return void
    	 */
    	public function enqueue() {
    		wp_enqueue_style( 'unblock-typography', get_template_directory_uri() . '/inc/customizer/custom-controls/typography/typography.css', null );
            /*
    		 * JavaScript
    		 */
            wp_enqueue_script( 'jquery-ui-core' );
    		wp_enqueue_script( 'jquery-ui-tooltip' );
    		wp_enqueue_script( 'jquery-stepper-min-js' );
    		
    		// Selectize
    		wp_enqueue_script( 'selectize', get_template_directory_uri() . '/inc/js/selectize.js', array( 'jquery' ), false, true );
    
    		// Typography
    		wp_enqueue_script( 'unblock-typography', get_template_directory_uri() . '/inc/customizer/custom-controls/typography/typography.js', array(
    			'jquery',
    			'selectize'
    		), false, true );
    
    		$google_fonts   = unBlock_Fonts::get_google_fonts();
    		$standard_fonts = unBlock_Fonts::get_standard_fonts();
    		$all_variants   = unBlock_Fonts::get_all_variants();
    
    		$standard_fonts_final = array();
    		foreach ( $standard_fonts as $key => $value ) {
    			$standard_fonts_final[] = array(
    				'family'      => $key,
    				'label'       => $value['label'],
    				'is_standard' => true,
    				'variants'    => array(
    					array(
    						'id'    => 'regular',
    						'label' => $all_variants['regular'],
    					),
    					array(
    						'id'    => 'italic',
    						'label' => $all_variants['italic'],
    					),
    					array(
    						'id'    => '700',
    						'label' => $all_variants['700'],
    					),
    					array(
    						'id'    => '700italic',
    						'label' => $all_variants['700italic'],
    					),
    				),
    			);
    		}
    
    		$google_fonts_final = array();
    
    		if ( is_array( $google_fonts ) ) {
    			foreach ( $google_fonts as $family => $args ) {
    				$label    = ( isset( $args['label'] ) ) ? $args['label'] : $family;
    				$variants = ( isset( $args['variants'] ) ) ? $args['variants'] : array( 'regular', '700' );
    
    				$available_variants = array();
    				foreach ( $variants as $variant ) {
    					if ( array_key_exists( $variant, $all_variants ) ) {
    						$available_variants[] = array( 'id' => $variant, 'label' => $all_variants[ $variant ] );
    					}
    				}
    
    				$google_fonts_final[] = array(
    					'family'   => $family,
    					'label'    => $label,
    					'variants' => $available_variants
    				);
    			}
    		}
    
    		$final = array_merge( $standard_fonts_final, $google_fonts_final );
    		wp_localize_script( 'unblock-typography', 'unblock_all_fonts', $final );
    	}
    
    	/**
    	 * An Underscore (JS) template for this control's content (but not its container).
    	 *
    	 * Class variables for this control class are available in the `data` JS object;
    	 * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
    	 *
    	 * I put this in a separate file because PhpStorm didn't like it and it fucked with my formatting.
    	 *
    	 * @see    WP_Customize_Control::print_template()
    	 *
    	 * @access protected
    	 * @return void
    	 */
    	protected function content_template() { ?>
    		<# if ( data.tooltip ) { #>
                <a href="#" class="tooltip hint--left" data-hint="{{ data.tooltip }}"><span class='dashicons dashicons-info'></span></a>
            <# } #>
            
            <label class="customizer-text">
                <# if ( data.label ) { #>
                    <span class="customize-control-title">{{{ data.label }}}</span>
                <# } #>
                <# if ( data.description ) { #>
                    <span class="description customize-control-description">{{{ data.description }}}</span>
                <# } #>
            </label>
            
            <div class="wrapper">
                <# if ( data.default['font-family'] ) { #>
                    <# if ( '' == data.value['font-family'] ) { data.value['font-family'] = data.default['font-family']; } #>
                    <# if ( data.choices['fonts'] ) { data.fonts = data.choices['fonts']; } #>
                    <div class="font-family">
                        <h5>{{ data.l10n['font-family'] }}</h5>
                        <select id="typography-font-family-{{{ data.id }}}" placeholder="{{ data.l10n['select-font-family'] }}"></select>
                    </div>
                    <div class="variant variant-wrapper">
                        <h5>{{ data.l10n['style'] }}</h5>
                        <select class="variant" id="typography-variant-{{{ data.id }}}"></select>
                    </div>
                <# } #>   
                
            </div>
            <?php
    	}
    
    }
}