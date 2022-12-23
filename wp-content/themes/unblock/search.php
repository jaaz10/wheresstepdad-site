<?php

/**
 * Search template
 * The template for displaying search results.
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header(); ?>

<div class="row column-wrapper">
    <header id="page-header">
        <h1 class="entry-title">
            <?php
            esc_html_e('Search', 'unblock');
            ?>
        </h1>

        <div id="page-intro">
            <p><?php
                /* translators: %s: search query. */
                printf(esc_html__('These are the search results for the following keyword(s): %s', 'unblock'), '<span id="search-words">' . get_search_query() . '</span>');
                ?></p>
        </div>

    </header><!-- .page-header -->

    <?php if (have_posts()) :

        // Start the loop.
        while (have_posts()) : the_post();

            // Get the post content
            get_template_part('template-parts/content/content', 'search');

        // End the loop.
        endwhile;

    endif; ?>
</div>

<?php // Blog navigation
unblock_paging_nav(); ?>

<?php
get_footer();
