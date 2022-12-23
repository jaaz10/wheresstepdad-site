<?php

/**
 * The template for displaying the default blog summaries
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

  <?php unblock_post_thumbnail(); ?>

  <header class="entry-header">

    <?php
    // Blog post title    
    the_title(sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>');

    // Featured or category label and Post Entry meta
    unblock_entry_meta()
    ?>

  </header>



  <div class="entry-content">
    <?php
    if (esc_attr(get_theme_mod('unblock_blog_excerpt', 1))) {
      the_excerpt();
      unblock_read_more_link();
    } else {
      the_content();
    }

    unblock_multipage_pagination();
    ?>
  </div><!-- .entry-content -->

</article>