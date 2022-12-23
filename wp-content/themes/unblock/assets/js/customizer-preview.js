/**
 * Live-update changed settings in real time in the Customizer preview.
 * @package unBlock
 * @since 1.0.1
 */

(function($) {
  var api = wp.customize,
    $head = $("head");

  /* HEX to RGBA functions
  ==================================================== */

  function hexToRgba(hex, opacity) {
    var red = parseInt(hex.substring(1, 3), 16),
      green = parseInt(hex.substring(3, 5), 16),
      blue = parseInt(hex.substring(5, 7), 16);

    return "rgba( " + red + ", " + green + ", " + blue + ", " + opacity + " )";
  }

  /* SHOW AND HIDE functions
  ==================================================== */
  function hideElement(element) {
    $(element).css({
      clip: "rect(1px, 1px, 1px, 1px)",
      position: "absolute",
      width: "1px",
      height: "1px",
      overflow: "hidden"
    });
  }

  function showElement(element) {
    $(element).css({
      clip: "auto",
      position: "relative",
      width: "auto",
      height: "auto",
      overflow: "visible"
    });
  }

  /* SHOW OR HIDE
  ==================================================== */
  // Back to Top
  api("unblock_hide_backtotop", function(value) {
    value.bind(function(newval) {
      if (false === newval) {
        showElement("#back-to-top-wrapper");
      } else {
        hideElement("#back-to-top-wrapper");
      }
    });
  });

  /* TEXT PREVIEWS
  ==================================================== */

  // Blog Title
  api("blogname", function(value) {
    value.bind(function(to) {
      $(".site-title a").text(to);
    });
  });

  // Tagline
  api("tagline", function(value) {
    value.bind(function(to) {
      $(".tagline").html(to);
    });
  });

  // Copyright
  api("unblock_copyright", function(value) {
    value.bind(function(newval) {
      $("#copyright-name").html(newval);
    });
  });

  // Back to top text
  api("unblock_back_to_top_text", function(value) {
    value.bind(function(newval) {
      $("#back-to-top").html(newval);
    });
  });

  /* SIZING
  ==================================================== */

  // Logo size
  api("unblock_logo_width", function(value) {
    value.bind(function(to) {
      $(".custom-logo").css("width", parseFloat(to) + "rem");
    });
  });

  // Site title size
  api("site_title_font_size", function(value) {
    value.bind(function(to) {
      $("#site-title").css("font-size", parseFloat(to) + "rem");
    });
  });

  // Headings size
  api("unblock_heading_font_size", function(value) {
    value.bind(function(to) {
      $("#page-header .entry-title").css("font-size", parseFloat(to) + "rem");
      $("#post-header .entry-title").css("font-size", parseFloat(to) + "rem");
    });
  });

  /* MISC
  ==================================================== */
  // Page headings transform
  api("unblock_heading_transform_style", function(value) {
    value.bind(function(to) {
      var style = $("#custom-headings-transform-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-headings-transform-css">#page-header .entry-title, #post-header .entry-title { text-transform:' +
          to +
          " }</style>"
      ).appendTo($head);
    });
  });

  // Heading Excerpt transform
  api("unblock_heading_excerpt_transform_style", function(value) {
    value.bind(function(to) {
      var style = $("#custom-heading-excerpt-transform-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-heading-excerpt-transform-css">#page-excerpt, #page-intro, #archive-description { text-transform:' +
          to +
          " }</style>"
      ).appendTo($head);
    });
  });

  /* COLOUR
  ==================================================== */

  // Site title colour
  api("unblock_site_title_colour", function(value) {
    value.bind(function(to) {
      var style = $("#custom-site-title-color-css"),
        css = "color: " + to;

      style.remove();
      style = $(
        '<style type="text/css" id="custom-site-title-color-css"> #site-title a { ' +
          css +
          " } </style>"
      ).appendTo($head);
    });
  });

  // Site tagline colour
  api("unblock_tagline_colour", function(value) {
    value.bind(function(to) {
      var style = $("#custom-tagline-color-css"),
        css = "color: " + to;

      style.remove();
      style = $(
        '<style type="text/css" id="custom-tagline-color-css"> #site-description { ' +
          css +
          " } </style>"
      ).appendTo($head);
    });
  });

  // Content background
  api("unblock_content_bg_colour", function(value) {
    value.bind(function(to) {
      var style = $("#custom-content-area-background-color-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-content-area-background-color-css"> body, .site-content, .site-footer, .offcanvas-navigation, .topnav { background-color:' +
          to +
          " }</style>"
      ).appendTo($head);
    });
  });

  // Bottom sidebar background
  api("unblock_bottom_sidebar_bg", function(value) {
    value.bind(function(to) {
      var style = $("#custom-bottom-sidebar-bg-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-bottom-sidebar-bg-css"> #bottom-sidebar { background:' +
          to +
          " }</style>"
      ).appendTo($head);
    });
  });

  // Bottom sidebar text
  api("unblock_bottom_sidebar_text", function(value) {
    value.bind(function(to) {
      var style = $("#custom-bottom-sidebar-text-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-bottom-sidebar-text-css">#bottom-sidebar, #bottom-sidebar .widget-title, #bottom-sidebar a { color:' +
          hexToRgba(to, 0.7) +
          " }</style>"
      ).appendTo($head);
    });
  });

  // Body text colour
  api("unblock_content_area_text_colour", function(value) {
    value.bind(function(to) {
      var style = $("#custom-content-area-text-colour-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-content-area-text-colour-css">body { color:' +
          to +
          " } </style>"
      ).appendTo($head);
    });
  });

 

  // Headings colour
  api("unblock_headings_colour", function(value) {
    value.bind(function(to) {
      var style = $("#custom-headings-colour-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-headings-colour-css">h1, h2, h3, h4, h5, h6, h1 a, h2 a, h3 a, h4 a, h5 a, h6 a { color:' +
          to +
          " } h1 a:focus,  h2 a:focus,  h3 a:focus,  h4 a:focus,  h5 a:focus,  h6 a:focus, h1 a:hover,  h2 a:hover,  h3 a:hover,  h4 a:hover,  h5 a:hover, h6 a:hover { color:" +
          hexToRgba(to, 0.7) +
          " }  </style>"
      ).appendTo($head);
    });
  });

  // Headings line colour
  api("unblock_heading_line_color", function(value) {
    value.bind(function(to) {
      var style = $("#custom-headings-line-colour-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-headings-line-colour-css"> .entry-header::after { background-color:' +
          to +
          " }  </style>"
      ).appendTo($head);
    });
  });

  // Primary menu link colour
  api("unblock_nav_colour", function(value) {
    value.bind(function(to) {
      var style = $("#custom-nav-link-colour-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-nav-link-colour-css">#mainmenu a, #mainmenu ul a, #mainmenu ul a:visited { color:' +
          to +
          " }  </style>"
      ).appendTo($head);
    });
  });

  // Mobile menu line separators
  api("unblock_mobile_line_separators", function(value) {
    value.bind(function(to) {
      var style = $("#custom-mobile-menu-separators-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-mobile-menu-separators-css">@media (max-width: 991px) { #mainmenu li, #mainmenu .sub-menu { border-color:' +
          hexToRgba(to, 0.08) +
          " } #mainmenu a>span::before { background-color:" +
          hexToRgba(to, 0.15) +
          " }}  </style>"
      ).appendTo($head);
    });
  });

  // Content links
  api("unblock_content_links", function(value) {
    value.bind(function(to) {
      var style = $("#custom-content-links-colour-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-content-links-colour-css"> .entry-meta a, .page-content a, .entry-summary a, .entry-content a:not(.wp-block-button__link), .author-content a, .comment-content a, .textwidget a, .comment-navigation a, .pingback .comment-body>a, .comment-meta a, .logged-in-as a, .widget_calendar a, .entry-content .wp-block-calendar tfoot a { color:' +
          to +
          " }  </style>"
      ).appendTo($head);
    });
  });

  // Banner caption colour
  api("unblock_banner_caption", function(value) {
    value.bind(function(to) {
      var style = $("#custom-banner-caption-colour-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-banner-caption-colour-css">#banner-sidebar .wp-caption-text { background-color:' +
          hexToRgba(to, 0.7) +
          " }  </style>"
      ).appendTo($head);
    });
  });

  // Banner caption text colour
  api("unblock_banner_caption_text_color", function(value) {
    value.bind(function(to) {
      var style = $("#custom-banner-caption-text-colour-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-banner-caption-text-colour-css"> #banner-sidebar .wp-caption-text { color:' +
          to +
          " } </style>"
      ).appendTo($head);
    });
  });

  // Primary Colour
  api("unblock_custom_primary_colour", function(value) {
    value.bind(function(to) {
      var style = $("#custom-primary-colour-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-primary-colour-css"> :root { --unblock-colour-primary:' +
          to +
          " } </style>"
      ).appendTo($head);
    });
  });

  // Secondary Colour
  api("unblock_custom_secondary_colour", function(value) {
    value.bind(function(to) {
      var style = $("#custom-secondary-colour-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-secondary-colour-css"> :root { --unblock-colour-secondary:' +
          to +
          " } </style>"
      ).appendTo($head);
    });
  });

  // Tertiary Colour
  api("unblock_custom_tertiary_colour", function(value) {
    value.bind(function(to) {
      var style = $("#custom-tertiary-colour-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-tertiary-colour-css"> :root { --unblock-colour-tertiary:' +
          to +
          " } </style>"
      ).appendTo($head);
    });
  });

  // Pagination background
  api("unblock_pagination_bg", function(value) {
    value.bind(function(to) {
      var style = $("#custom-pagination-background-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-pagination-background-css"> .page-numbers { background-color:' +
          to +
          " } </style>"
      ).appendTo($head);
    });
  });

  // Pagination numbers
  api("unblock_pagination_numbers", function(value) {
    value.bind(function(to) {
      var style = $("#custom-pagination-numbers-css");

      style.remove();
      style = $(
        '<style type="text/css" id="custom-pagination-numbers-css"> .page-numbers { color:' +
          to +
          " } </style>"
      ).appendTo($head);
    });
  });

  /* SHOW or HIDE
 ======================= */

  // Hide site title
  api("unblock_show_site_title", function(value) {
    value.bind(function(newval) {
      if (false === newval) {
        hideElement("#site-title");
      } else {
        showElement("#site-title");
      }
    });
  });

  // Hide site description
  api("unblock_show_site_desc", function(value) {
    value.bind(function(newval) {
      if (false === newval) {
        hideElement("#site-description");
      } else {
        showElement("#site-description");
      }
    });
  });

  // Hide archiver prefix
  api("unblock_hide_prefix_archive", function(value) {
    value.bind(function(newval) {
      if (false === newval) {
        showElement(".archive-prefix");
      } else {
        hideElement(".archive-prefix");
      }
    });
  });

  // Hide banner captions
  api("unblock_hide_banner_captions", function(value) {
    value.bind(function(newval) {
      if (false === newval) {
        showElement("#banner-sidebar .wp-caption-text");
      } else {
        hideElement("#banner-sidebar .wp-caption-text");
      }
    });
  });
})(jQuery);
