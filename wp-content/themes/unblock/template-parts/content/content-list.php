<?php

/**
 * The template for the blog list layout content
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('row align-items-center'); ?>>


  <?php if (has_post_thumbnail()) : ?>
    <div class="col-md-12 col-lg-6">
      <?php unblock_post_thumbnail(); ?>
    </div>
  <?php endif; ?>


  <?php if (!has_post_thumbnail()) : ?>
    <div class="col">
    <?php else : ?>
      <div class="col-md-12 col-lg-6">
      <?php endif; ?>

      <header class="entry-header">
        <?php
        the_title(sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>');
        unblock_entry_meta();
        ?>
      </header>

      <div class="entry-content">
        <?php the_excerpt();
        if (!esc_attr(get_theme_mod('unblock_hide_excerpt_more_link', 0))) {
          unblock_read_more_link();
        }
        ?>
      </div><!-- .entry-summary -->
      </div>

</article><!-- #post-## -->