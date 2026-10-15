(function ($) {
  "use strict";

  var MOBILE_QUERY = window.matchMedia ? window.matchMedia("(max-width: 768px)") : null;
  var OPEN_CLASS = "mobile-nav-open";

  function isMobile() {
    return MOBILE_QUERY ? MOBILE_QUERY.matches : window.innerWidth <= 768;
  }

  function ensureBackdrop() {
    if ($("#sidebarBackdrop").length === 0) {
      $('<div id="sidebarBackdrop" class="sidebar-backdrop"></div>').appendTo("body");
    }
  }

  /* ---- Desktop: original RuangAdmin behaviour (unchanged) ---- */
  function toggleDesktop() {
    $("body").toggleClass("sidebar-toggled");
    $(".sidebar").toggleClass("toggled");
    if ($(".sidebar").hasClass("toggled")) {
      $(".sidebar .collapse").collapse("hide");
    }
  }

  /* ---- Mobile: open / close / toggle the drawer ---- */
  function openDrawer() {
    ensureBackdrop();
    clearDesktopState();
    $("body").addClass(OPEN_CLASS);
    $("#sidebarToggleTop").attr("aria-expanded", "true");
    $(".sidebar .collapse").collapse("hide");
  }

  function closeDrawer() {
    $("body").removeClass(OPEN_CLASS);
    $("#sidebarToggleTop").attr("aria-expanded", "false");
    $(".sidebar .collapse").collapse("hide");
  }

  function toggleDrawer() {
    if ($("body").hasClass(OPEN_CLASS)) {
      closeDrawer();
    } else {
      openDrawer();
    }
  }

  function clearDesktopState() {
    $("body").removeClass("sidebar-toggled");
    $(".sidebar").removeClass("toggled");
  }

  function handleToggle(e) {
    e.preventDefault();
    if (isMobile()) {
      toggleDrawer();
    } else {
      toggleDesktop();
    }
  }

  /* ---------- Hamburger toggle ---------- */
  $(document).on("click", "#sidebarToggleTop", handleToggle);

  /* ---------- Overlay closes the drawer ---------- */
  ensureBackdrop();
  $(document).on("click", "#sidebarBackdrop", closeDrawer);

  /* ---------- ESC closes the drawer ---------- */
  $(document).on("keyup", function (e) {
    if ((e.key === "Escape" || e.which === 27) && $("body").hasClass(OPEN_CLASS)) {
      closeDrawer();
    }
  });

  /* ---------- Menu link behaviour on mobile ---------- */
  // Plain links (real navigation) close the drawer; submenu togglers do not,
  // so Bootstrap collapse submenus keep working inside the drawer.
  $(document).on("click", ".sidebar .nav-link", function () {
    if (!isMobile() || !$("body").hasClass(OPEN_CLASS)) return;
    var $link = $(this);
    var href = $link.attr("href");
    var isCollapseToggle = typeof $link.attr("data-toggle") !== "undefined";
    var isNonNavigating = !href || href === "#" || href === "javascript:void(0)" || href.charAt(0) === "#";
    if (!isCollapseToggle && !isNonNavigating) {
      closeDrawer();
    }
  });

  // Submenu items always navigate -> close.
  $(document).on("click", ".sidebar .collapse-item", function () {
    if (isMobile() && $("body").hasClass(OPEN_CLASS)) {
      closeDrawer();
    }
  });

  /* ---------- Resize / orientation safe handling ---------- */
  var resizeTimer = null;
  $(window).on("resize orientationchange", function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      if (isMobile()) {
        clearDesktopState();
      } else {
        closeDrawer();
      }
    }, 150);
  });
})(jQuery);