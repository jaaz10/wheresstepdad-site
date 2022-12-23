<?php

/**
 * Theme footer layouts
 * @package unBlock
 * @since 1.0.1
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}


/* BLOG LAYOUT
   ====================================================*/
if (!function_exists('unblock_footer_styles')) :
	function unblock_footer_styles()
	{

		$unblock_footer_style = apply_filters('unblock_footer_style', get_theme_mod('unblock_footer_style', 'footer1'));

		// Start 
?>
		<footer class="container px-4 flex-wrap align-items-center">
			<div class="col-md-6 d-flex align-items-center">
				<?php unblock_footer_branding(); ?>
			</div>

			<?php //Social Menu
			if (has_nav_menu('social')) {
				wp_nav_menu(
					array(
						'menu_id'  => 'social-nav',
						'menu_class'     => 'col-md-6 justify-content-end',
						'theme_location' => 'social',
						'container'         => 'ul',
						'depth'          => 1,
						'link_before'    => '<span>',
						'link_after'     => '</span>',
					)
				);
			}
			?>

			<div id="copyright" class="text-muted">
				<?php esc_html_e('Copyright &copy;', 'unblock');
				echo esc_html(date_i18n(__('Y', 'unblock'))); ?>
				<span id="copyright-name"><?php echo esc_html(get_theme_mod('unblock_copyright')); ?></span>.
				<?php esc_html_e('All rights reserved.', 'unblock'); ?>
			</div>

		</footer>
<?php // End
	}
endif;
