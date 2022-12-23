<?php
/**
 * Customizer Control: Note.
 * @package unBlock
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'unBlock_Note_Control' ) ) {

	class unBlock_Note_Control extends WP_Customize_Control {
		public function render_content() {
			?>

<label>
    <span class="customizer-control-heading"><?php echo wp_kses_post( $this->label ); ?></span>
</label>
<span class="customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>

<?php
		}
	}
}



// Used to create spacers or separator lines between settings.
if ( ! class_exists( 'unBlock_Spacer_Control' ) ) {
class unBlock_Spacer_Control extends WP_Customize_Control{
	public function render_content(){
		?>
<p>
    <hr>
</p>
<?php
	}
}
}
