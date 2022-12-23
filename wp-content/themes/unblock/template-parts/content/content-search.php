<?php

/**
 * The search results template file used to display search results.
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

?>


<article id="post-<?php the_ID(); ?>" <?php post_class('content-outer'); ?>>
  <div class="content-inner">
    <div class="row">
      <?php if (has_post_thumbnail()) : ?>
        <div class="col-md-12 col-lg-3">
          <?php unblock_post_thumbnail(); ?>
        </div>
      <?php endif; ?>

      <?php if (!has_post_thumbnail()) : ?>
        <div class="col">
        <?php else : ?>
          <div class="col-md-12 col-lg-9">
          <?php endif; ?>


          <header class="entry-header">

            <?php
            the_title(sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>');

            // Entry meta	  
            unblock_entry_mini_meta();
            ?>
          </header>

          <div class="entry-summary">

            <?php
            the_excerpt();
            unblock_read_more_link();
            unblock_multipage_pagination();
            ?>

          </div><!-- .entry-summary -->
          </div>
        </div>


    </div>
</article><!-- #post-## -->