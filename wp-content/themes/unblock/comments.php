<?php

/**
 * Comments template file
 * The template for displaying Comments
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// Check if a password is required to continue
if (post_password_required()) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php if (have_comments()) : ?>

		<h3 id="comments-heading">
			<?php 
			printf(_nx('One comment', '%1$s comments', get_comments_number(), 'comments title', 'unblock'), number_format_i18n(get_comments_number())); ?>
		</h3>

		<ol class="comment-list">
			<?php
			wp_list_comments(array(
				'walker' => new unBlock_Comment_Walker(),
				'style'      => 'ol',
				'short_ping' => true,
				'avatar_size' => 70,
			));
			?>
		</ol><!-- .comment-list -->

		<?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
			<nav id="comment-nav-below" class="comment-navigation" role="navigation">
				<div class="nav-links">
					<div class="nav-previous"><?php previous_comments_link(esc_html__('&larr; Older Comments', 'unblock')); ?></div>
					<div class="nav-next"><?php next_comments_link(esc_html__('Newer Comments &rarr;', 'unblock')); ?></div>
				</div>
			</nav><!-- #comment-nav-below -->
		<?php endif; // Check for comment navigation. 
		?>

		<?php if (!comments_open()) : ?>
			<p class="no-comments"><?php esc_html_e('Comments are closed.', 'unblock'); ?></p>
		<?php endif; ?>

	<?php endif; // have_comments() 
	?>

	<?php
	comment_form(array(
		'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="5" placeholder="' . esc_attr__('Comment', 'unblock') . '" aria-required="true"></textarea></p>',
		'title_reply_before'  => '<h3 id="reply-title" class="comment-reply-title section-title">'
	));
	?>

</div><!-- #comments -->