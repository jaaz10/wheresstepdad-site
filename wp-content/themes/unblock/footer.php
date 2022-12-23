<?php

/**
 * The template for displaying the footer
 * @package unBlock
 * @since 1.0.1
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

$unblock_footer_style = apply_filters('unblock_footer_style', get_theme_mod('unblock_footer_style', 'footer1'));
$unblock_back_to_top_text = esc_attr(get_theme_mod('unblock_back_to_top_text', esc_html__('Back To Top', 'unblock')));
?>

</div><!-- .col -->
</div><!-- .row -->
</div><!-- .container -->
</main><!-- #main -->
</div><!-- #content -->

<?php
// Action hook for any content placed before the bottom sidebar
do_action('unblock_before_bottom_sidebar');

get_template_part('template-parts/sidebars/sidebar', 'bottom');

// Action hook for any content placed after the bottom sidebar
do_action('unblock_after_bottom_sidebar');
?>

<section id="<?php echo esc_attr($unblock_footer_style); ?>" class="site-footer">
	<?php // Get our footer
	unblock_footer_styles();
	?>
</section>

<?php if (!esc_attr(get_theme_mod('unblock_hide_backtotop', 0))) { ?>
	<div id="back-to-top-wrapper">
		<a title="<?php echo esc_attr($unblock_back_to_top_text);  ?>" id="back-to-top">&lsqb; <span><?php echo esc_html($unblock_back_to_top_text); ?></span> &rsqb;</a>
	</div>
<?php } ?>

</div><!-- #page-->

<?php wp_footer(); ?>
</body>

</html>