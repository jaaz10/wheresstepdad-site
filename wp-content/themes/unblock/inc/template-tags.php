<?php

/**
 * Custom template tags for unBlock
 * Eventually, some of the functionality here could be replaced by core features.
 * @package unBlock
 * @since 1.0.0
 */








/* SITE BRANDING
@since 1.0.1
   ==================================================== */
if (!function_exists('unblock_site_identity')) :
	function unblock_site_identity()
	{

		if (has_custom_logo()) {
			the_custom_logo();
		}

		if (esc_attr(get_theme_mod('unblock_show_site_title', 1))) {
			printf(
				'<%1$s id="site-title"><a class="navbar-brand" href="%2$s" rel="home">%3$s</a></%1$s>',
				is_front_page() && is_home() ? 'h1' : 'p',
				esc_url(home_url('/')),
				esc_html(get_bloginfo('name'))
			);
		}
	}
endif;

/* HEADER BRANDING
@since 1.0.0
==================================================== */
if (!function_exists('unblock_header_branding')) :
	function unblock_header_branding()
	{

		$description  = get_bloginfo('description', 'display');

		echo '<div id="site-branding">';

		unblock_site_identity();

		if ($description) {
			if (esc_attr(get_theme_mod('unblock_show_site_desc', 1))) {
				echo '<p id="site-description" class="col-md-5">' . esc_html($description) . '</p>';
			}
			echo '</div>';
		}
	}
endif;

/* FOOTER BRANDING
@since 1.0.0
==================================================== */
if (!function_exists('unblock_footer_branding')) :
	function unblock_footer_branding()
	{

		echo '<div id="footer-branding">';
		if (has_custom_logo()) {
			the_custom_logo();
		}

		printf(
			'<span id="footer-site-title"><a class="footer-navbar-brand" href="%1$s" rel="home">%2$s</a></span>',
			esc_url(home_url('/')),
			esc_html(get_bloginfo('name'))
		);
		echo '</div>';
	}
endif;

/* DISPLAY FEATURED or CATEGORY LABEL
@since 1.0.1
==================================================== */
if (!function_exists('unblock_featured_category_badge')) :
	function unblock_featured_category_badge()
	{
		$unblock_featured_label_text = get_theme_mod('unblock_featured_label_text', esc_html__('Featured', 'unblock'));

		if (is_sticky() && is_home() && !is_paged()) {
			echo '<li class="sticky-category"><span class="featured-badge">', wp_kses_post($unblock_featured_label_text), '</span></li>';
		} else {
			echo '<li class="sticky-category">', unblock_first_category(), '</li>';
		}
	}
endif;

/* POST DATES
@since 1.0.1
==================================================== */
if (!function_exists('unblock_posted_on')) :
	// Returns the post date
	function unblock_posted_on()
	{

		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if (get_the_time('U') !== get_the_modified_time('U')) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr(get_the_date(DATE_W3C)),
			esc_html(get_the_date()),
			esc_attr(get_the_modified_date(DATE_W3C)),
			esc_html(get_the_modified_date())
		);

		echo '<li class="publish-date"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a></li>';
	}
endif;

/* UPDATED POST DATE
@since 1.0.1
==================================================== */
if (!function_exists('unblock_updated')) :
	function unblock_updated()
	{
		printf(
		/* translators: %s: For the updated date. */
			'<li class="posted-update">' .	esc_html__('Updated <span>%s</span>', 'unblock') . '</li>',
			get_the_modified_date()
		);
	}
endif;


/* META AUTHOR INFO
@since 1.0.1
==================================================== */
if (!function_exists('unblock_posted_by')) :
	function unblock_posted_by()
	{
		printf(
			'<li class="byline"><span class="author vcard">' . esc_html__('By ', 'unblock') . '<a class="url fn n" href="%1$s">%2$s</a></span></li>',
			esc_url(get_author_posts_url(get_the_author_meta('ID'))),
			esc_html(get_the_author())
		);
	}
endif;

/* META COMMENT COUNT & LINK
@since 1.0.0
==================================================== */
if (!function_exists('unblock_comment_link')) :
	function unblock_comment_link()
	{
		if (!post_password_required() && (comments_open() || get_comments_number())) {
			echo '<li class="comments-link">';
			/* translators: %s: Name of current post. Only visible to screen readers. */
			comments_popup_link(sprintf(__('Make a comment <span class="screen-reader-text">on %s</span>', 'unblock'), get_the_title()));

			echo '</li>';
		}
	}
endif;

/* META POST FORMAT
@since 1.0.1
==================================================== */
if (!function_exists('unblock_post_format')) :
	// Returns the post date
	function unblock_post_format()
	{

		$format = get_post_format();
		if (current_theme_supports('post-formats', $format)) {
			printf(
				'<li class="post-format"><a href="%1$s">%2$s</a></li>',

				esc_url(get_post_format_link($format)),
				esc_html(get_post_format_string($format))
			);
		}
	}
endif;

/* META EDIT LINK
@since 1.0.0
==================================================== */
if (!function_exists('unblock_edit_link')) :
	function unblock_edit_link()
	{
		edit_post_link(
			sprintf(
				/* translators: %s: Name of current post */
				__('Edit Post<span class="screen-reader-text">%s</span>', 'unblock'),
				get_the_title()
			),
			'<li class="edit-link"></li>'
		);
	}
endif;


/* ENTRY META DETAILS
@since 1.0.0
==================================================== */
if (!function_exists('unblock_entry_meta')) :

	function unblock_entry_meta()
	{

		echo '<ul class="entry-meta">';
		unblock_featured_category_badge();
		unblock_post_format();
		unblock_posted_by();
		unblock_posted_on();
		unblock_comment_link();
		if (!esc_attr(get_theme_mod('unblock_hide_edit', 0))) {
			unblock_edit_link();
		}

		echo '</ul>';
	}
endif;

/* DISPLAY MINI SUMMARY POST META INFO
@since 1.0.0
==================================================== */
if (!function_exists('unblock_entry_mini_meta')) :
	function unblock_entry_mini_meta()
	{
		echo '<ul class="entry-meta">';
		unblock_posted_on();
		echo '</ul>';
	}
endif;


/* POST THUMBNAILS
@since 1.0.1
==================================================== */
if (!function_exists('unblock_post_thumbnail')) :
	function unblock_post_thumbnail()
	{
		$thumbnail_caption = '';

		if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
			return;
		}

		$thumbnail_size = get_theme_mod('unblock_thumbnail_size', 'default');

		$thumbnail_post = get_post(get_post_thumbnail_id());

		if ($thumbnail_post) {
			$thumbnail_caption = $thumbnail_post->post_excerpt;
		}

		// If the full post or a page
		if (is_singular()) :
?>

			<figure class="post-thumbnail<?php if ($thumbnail_caption) : ?> has-caption<?php endif; ?>">
				<?php
				the_post_thumbnail('post-thumbnail', array('class' => 'default-thumbnail', 'alt' => get_the_title()));
				if ($thumbnail_caption && !is_search()) {
					printf(
					'<figcaption class="wp-caption-text post-thumbnail-caption">%s</figcaption>', $thumbnail_caption);
				}
				?>
			</figure><!-- .post-thumbnail -->

		<?php // If this is the blog
		else : ?>

			<figure class="post-thumbnail<?php if ($thumbnail_caption) : ?> has-caption<?php endif; ?>">
				<a class="post-thumbnail-link " href="<?php esc_url(the_permalink()); ?>" aria-hidden="true">
					<?php the_post_thumbnail('post-thumbnail', array('class' => 'default-thumbnail', 'alt' => get_the_title())); ?>
				</a>
				<?php
				if ($thumbnail_caption && !is_search()) {
					printf(
					'<figcaption class="wp-caption-text post-thumbnail-caption">%s</figcaption>', $thumbnail_caption);
				}
				?>
			</figure>

<?php endif; // End is_singular()

	}
endif;


/* BIO INFO
@since 1.0.1
==================================================== */
if (!function_exists('unblock_bio_info')) :
	function unblock_bio_info()
	{

		echo '<div class="author-info">';
		printf( 
		'<div class="author-avatar">%s</div>', get_avatar(get_the_author_meta('user_email'), 100));
		echo '<div class="author-description"><div class="author-bio">';
		printf(
		'<h3 class="author-heading section-title">%s</h3>', get_the_author());
		the_author_meta('description');
		echo '</div><a class="author-link" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '" rel="author">';
		printf(
		/* translators: %s: For the author name. */
		esc_html__('View all posts by %s &rarr;', 'unblock'), get_the_author());
		echo '</a></div></div>';
	}
endif;


/* CATEGORY LIST
@since 1.0.1
==================================================== */
if (!function_exists('unblock_post_categories')) :
	function unblock_post_categories()
	{
		$categories_list = get_the_category_list(esc_attr_x(', ', 'Used between list items, there is a space after the comma.', 'unblock'));
		if ($categories_list) {
			printf(
			/* translators: %1$s: For the list of category names. */
			'<span class="post-cats">' . esc_html__('Categories: %1$s', 'unblock') . '</span>', $categories_list);
		}
	}
endif;

/* DISPLAY POST TAGS
@since 1.0.0
==================================================== */
if (!function_exists('unblock_post_tags')) :
	function unblock_post_tags()
	{
		$tags_list = get_the_tag_list('', esc_html_x(', ', 'list item separator', 'unblock'));
		if ($tags_list) {
			/* translators: %1$s: For the list of tags. */
			printf('<div class="post-tags">' . esc_html__('Tags: %1$s', 'unblock') . '</div>', $tags_list);
		}
	}
endif;

/* POST FOOTER
@since 1.0.0
==================================================== */
if (!function_exists('unblock_entry_footer')) :
	function unblock_entry_footer()
	{

		echo '<footer class="entry-footer">';

		// Post categories
		if (!esc_attr(get_theme_mod('unblock_hide_post_category_tags', 0))) {
			unblock_post_categories();
		}
		// Post tags
		if (!esc_attr(get_theme_mod('unblock_hide_post_tags', 0))) {
			unblock_post_tags();
		}

		echo '</footer>';
	}
endif;


/* DISPLAY A SINGLE POST CATEGORY
@since 1.0.1
==================================================== */
if (!function_exists('unblock_first_category')) :
	function unblock_first_category()
	{
		$category = get_the_category();
		$first_category = $category[0];
		echo sprintf(
			'<span class="category-badge"><a href="%s">%s</a></span>',
			get_category_link($first_category),
			$first_category->name
		); // phpcs:ignore WordPress.Security.EscapeOutput
	}
endif;



/* BLOG  NAVIGATION
Navigation for the blog
@since 1.0.0
==================================================== */
if (!function_exists('unblock_paging_nav')) :
	function unblock_paging_nav()
	{
		the_posts_pagination(array(
			'prev_text'          => is_rtl() ? '<i class="bi bi-caret-right-fill"></i>' : '<i class="bi bi-caret-left-fill"></i>',
			'next_text'          =>  is_rtl() ? '<i class="bi bi-caret-left-fill"></i>' : '<i class="bi bi-caret-right-fill"></i>',
			'before_page_number' => ''
		));
	}
endif;


/* POST NAVIGATION
Navigation for full posts.
@since 1.0.0
==================================================== */
if (!function_exists('unblock_post_pagination')) :
	function unblock_post_pagination()
	{

		the_post_navigation(array(
			'next_text' => '<span class="nav-meta">' . (is_singular('post') ? esc_html__('Next post', 'unblock') : esc_html__('Next', 'unblock')) . '</span> <span class="post-title">%title</span>',
			'prev_text' => '<span class="nav-meta">' . (is_singular('post') ? esc_html__('Previous post', 'unblock') : esc_html__('Previous', 'unblock')) . '</span> <span class="post-title">%title</span>',
		));
	}
endif;


/* MULTIPAGE NAVIGATION
Navigation for splitting posts or pages into more than one page.
@since 1.0.0
==================================================== */
if (!function_exists('unblock_multipage_pagination')) :
	function unblock_multipage_pagination()
	{

		wp_link_pages(array(
			'before'      => '<div class="multi-page-links"><span class="multi-page-links-title">' . esc_html__('Pages:', 'unblock') . '</span>',
			'after'       => '</div>',
			'link_before' => '<span>',
			'link_after'  => '</span>',
			'pagelink'    => '%',
			'separator'   => false,
		));
	}
endif;
