/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 * @package unBlock
 * @since 1.0.0
 */

(function() {
  var container, button, menu, links, i, len;

  container = document.getElementById("site-navigation");
  if (!container) {
    return;
  }

  button = document.getElementById("primary-menu-toggle");
  if ("undefined" === typeof button) {
    return;
  }

  menu = container.getElementsByTagName("ul")[0];

  // Hide menu toggle button if menu is empty and return early.
  if ("undefined" === typeof menu) {
    button.style.display = "none";
    return;
  }

  menu.setAttribute("aria-expanded", "false");
  if (-1 === menu.className.indexOf("nav-menu")) {
    menu.className += " nav-menu";
  }

  button.onclick = function() {
    if (-1 !== container.className.indexOf("toggled")) {
      container.className = container.className.replace(" toggled", "");
      button.setAttribute("aria-expanded", "false");
      menu.setAttribute("aria-expanded", "false");
    } else {
      container.className += " toggled";
      button.setAttribute("aria-expanded", "true");
      menu.setAttribute("aria-expanded", "true");
    }
  };

  // Get all the link elements within the menu.
  links = menu.getElementsByTagName("a");

  // Each time a menu link is focused or blurred, toggle focus.
  for (i = 0, len = links.length; i < len; i++) {
    links[i].addEventListener("focus", toggleFocus, true);
    links[i].addEventListener("blur", toggleFocus, true);
  }

  function toggleFocus() {
    var self = this;

    // Move up through the ancestors of the current link until we hit .nav-menu.
    while (-1 === self.className.indexOf("nav-menu")) {
      // On li elements toggle the class .focus.
      if ("li" === self.tagName.toLowerCase()) {
        if (-1 !== self.className.indexOf("focus")) {
          self.className = self.className.replace(" focus", "");
        } else {
          self.className += " focus";
        }
      }

      self = self.parentElement;
    }
  }

  // Toggles `focus` class to allow submenu access on tablets.

  (function(container) {
    var touchStartFn,
      i,
      parentLink = container.querySelectorAll(
        ".menu-item-has-children > a, .page_item_has_children > a"
      );

    if ("ontouchstart" in window) {
      touchStartFn = function(e) {
        var menuItem = this.parentNode,
          i;

        if (!menuItem.classList.contains("focus")) {
          e.preventDefault();
          for (i = 0; i < menuItem.parentNode.children.length; ++i) {
            if (menuItem === menuItem.parentNode.children[i]) {
              continue;
            }
            menuItem.parentNode.children[i].classList.remove("focus");
          }
          menuItem.classList.add("focus");
        } else {
          menuItem.classList.remove("focus");
        }
      };

      for (i = 0; i < parentLink.length; ++i) {
        parentLink[i].addEventListener("touchstart", touchStartFn, false);
      }
    }
  })(container);
})();

(function($, root, undefined) {
  $(document).ready(function() {
    "use strict";

    var menu = $("#site-navigation");
    $(window).resize(function() {
      var w = $(window).width();
      if (w > 991 && menu.is(":hidden")) {
        menu.removeAttr("style");
      }
    });

    $("#primary-menu-toggle").click(function() {
      $("body").toggleClass("overflow");
      $("#site-navigation").slideToggle();
    });
  });
})(jQuery, this);

// Smooth scroll to top
jQuery(document).ready(function($) {
  var $body = $("body");

  $("#back-to-top").click(function() {
    $("html, body").scrollTop(0);
    return false;
  });
});
