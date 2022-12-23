<?php

/**
 * The 404 error template file
 * Whenever a page is gone or not accessible, this page will be displayed.
 * @package unBlock
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

get_header();
?>


<header id="page-header">
  <h1 class="entry-title"><?php esc_html_e('404', 'unblock'); ?></h1>
  <div id="page-excerpt"><?php esc_html_e('Unfortunately, it looks like the page you were wanting is missing or has been moved. Perhaps try performing a search?', 'unblock'); ?></div>
</header><!-- .page-header -->

<div class="row column-wrapper">
  <section class="error-404 not-found col">
    <div class="content-inner">
      <div class="page-content">

        <?php get_search_form(); ?>

        <a href="<?php echo esc_url(home_url('/')); ?>" class="error-home-link"><i class="bi bi-house-fill"></i> <?php esc_html_e('Back To Home', 'unblock'); ?></a>
      </div><!-- .page-content -->
    </div>
  </section><!-- .error-404 -->
</div>

<?php
get_footer();
