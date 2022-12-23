<?php

/**
 * The template for the bottom sidebar group
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// If no active sidebars - then load nothing
if (
	!is_active_sidebar('bottom1')
	&& !is_active_sidebar('bottom2')
	&& !is_active_sidebar('bottom3')
	&& !is_active_sidebar('bottom4')
)
	return;
?>

<aside id="bottom-sidebar">
	<div class="container px-4">
		<div class="row gx-5">

			<?php if (is_active_sidebar('bottom1')) : ?>
				<div id="bottom1" <?php unblock_bottom_group(); ?>>
					<?php dynamic_sidebar('bottom1'); ?>
				</div>
			<?php endif; ?>

			<?php if (is_active_sidebar('bottom2')) : ?>
				<div id="bottom2" <?php unblock_bottom_group(); ?>>
					<?php dynamic_sidebar('bottom2'); ?>
				</div>
			<?php endif; ?>

			<?php if (is_active_sidebar('bottom3')) : ?>
				<div id="bottom3" <?php unblock_bottom_group(); ?>>
					<?php dynamic_sidebar('bottom3'); ?>
				</div>
			<?php endif; ?>

			<?php if (is_active_sidebar('bottom4')) : ?>
				<div id="bottom4" <?php unblock_bottom_group(); ?>>
					<?php dynamic_sidebar('bottom4'); ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</aside>