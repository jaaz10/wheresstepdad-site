<?php

/**
 * Theme header layouts
 * @package unBlock
 * @since 1.0.1
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/* BLOG LAYOUT
   ====================================================*/
if (!function_exists('unblock_header_styles')) :
	function unblock_header_styles()
	{

		$unblock_header_style = apply_filters('unblock_header_style', get_theme_mod('unblock_header_style', 'header1'));

		// Start 
?>
		<header id="<?php echo esc_attr($unblock_header_style); ?>">
			<nav class="navbar navbar-expand-lg navbar-light bg-light" aria-label="header1 navbar">
				<div class="container px-4">
					<?php unblock_site_identity(); ?>
					<button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#header1Style" aria-controls="header1Style" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>

					<div class="navbar-collapse collapse" id="header1Style">
						<?php
						if (has_nav_menu('primary')) {
							wp_nav_menu(array(
								'menu_id'              => 'mainmenu',
								'theme_location' => 'primary',
								'container'         => 'ul',
								'menu_class'     => 'navbar-nav me-auto mb-2 mb-lg-0',
								'link_before'  => '<span>',
								'link_after'   => '</span>',
							));
						} else {
							echo '<ul class="navbar-nav me-auto mb-lg-0">';
							wp_list_pages(array(
								'match_menu_classes' 	=> true,
								'title_li' 				=> false,
								'link_before'  => '<span>',
								'link_after'   => '</span>',
							));
							echo '</ul>';
						}
						?>
					</div>
				</div>
			</nav>
		</header>
<?php // End
	}
endif;
