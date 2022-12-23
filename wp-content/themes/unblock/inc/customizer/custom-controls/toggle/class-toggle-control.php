<?php
/**
 * Customizer Control: toggle.
 * @package unBlock 
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'unBlock_Toggle_Control' ) ) {

	/**
	 * Toggle control (modified checkbox).
	 */
	class unBlock_Toggle_Control extends Wp_Customize_Control {

		public $type = 'toggle';
        
        public $tooltip = '';
        
        public function to_json() {
			parent::to_json();
			
            if ( isset( $this->default ) ) {
				$this->json['default'] = $this->default;
			} else {
				$this->json['default'] = $this->setting->default;
			}
			
            $this->json['value']   = $this->value();
			$this->json['link']    = $this->get_link();
            $this->json['id']      = $this->id;
            $this->json['tooltip'] = $this->tooltip;
						
            $this->json['inputAttrs'] = '';
			foreach ( $this->input_attrs as $attr => $value ) {
				$this->json['inputAttrs'] .= $attr . '="' . esc_attr( $value ) . '" ';
			}
		}
        
        public function enqueue() {            
            wp_enqueue_style( 'unblock-toggle', get_template_directory_uri() . '/inc/customizer/custom-controls/toggle/toggle.css', null );
            wp_enqueue_script( 'unblock-toggle', get_template_directory_uri() . '/inc/customizer/custom-controls/toggle/toggle.js', array( 'jquery' ), false, true ); //for toggle        
        }
        
		protected function content_template() {
			?>
<# if ( data.tooltip ) { #>
    <a href="#" class="tooltip hint--left" data-hint="{{ data.tooltip }}"><span class='dashicons dashicons-info'></span></a>
    <# } #>
        <label for="toggle_{{ data.id }}">
            <span class="customize-control-title">
                {{{ data.label }}}
            </span>
            <# if ( data.description ) { #>
                <span class="description customize-control-description">{{{ data.description }}}</span>
                <# } #>
                    <input {{{ data.inputAttrs }}} name="toggle_{{ data.id }}" id="toggle_{{ data.id }}" type="checkbox" value="{{ data.value }}" {{{ data.link }}}<# if ( '1'==data.value ) { #> checked<# } #> hidden />
                        <span class="switch"></span>
        </label>
        <?php
		}
	}
}
