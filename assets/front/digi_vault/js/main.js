// ==================================================
// * Project Name   : Digibank - Advanced Multi Wallet Digital Banking System with Virtual Card and Rewards
// * File           :  JS Base
// * Version        :  1.0
// * Last change    :  3 Feb 2024, Saturday
// * Author         :  tdevs (https://codecanyon.net/user/tdevs/portfolio)
// ==================================================

(function ($) {
  'use strict';

  // When the window has fully loaded
  $(window).on('load', function (event) {
    $('.preloader').delay(500).fadeOut(500);
  });

  // Initialize mobile-menu for smaller screens
  var tpMenuWrap = $('.td-mobile-menu-active > ul').clone();
  var tpSideMenu = $('.td-offcanvas-menu nav');
  tpSideMenu.append(tpMenuWrap);
  if ($(tpSideMenu).find('.td-dp-menu, .td-mega-menu').length != 0) {
    $(tpSideMenu).find('.td-dp-menu, .td-mega-menu').parent().append('<button class="tp-menu-close"><i class="fas fa-chevron-right"></i></button>');
  }

  var sideMenuList = $('.td-offcanvas-menu nav > ul > li button.tp-menu-close, .td-offcanvas-menu nav > ul li.has-dropdown > a');
  $(sideMenuList).on('click', function (e) {
    e.preventDefault();
    if (!($(this).parent().hasClass('active'))) {
      $(this).parent().addClass('active');
      $(this).siblings('.td-dp-menu, .td-mega-menu').slideDown();
    } else {
      $(this).siblings('.td-dp-menu, .td-mega-menu').slideUp();
      $(this).parent().removeClass('active');
    }
  });

  // Offcanvas Js
  $(".offcanvas-close,.offcanvas-overlay").on("click", function () {
    $(".offcanvas-area").removeClass("info-open");
    $(".offcanvas-overlay").removeClass("overlay-open");
  });
  $(".sidebar-toggle").on("click", function () {
    $(".offcanvas-area").addClass("info-open");
    $(".offcanvas-overlay").addClass("overlay-open");
  });

  // Body overlay Js
  $(".body-overlay").on("click", function () {
    $(".offcanvas-area").removeClass("opened");
    $(".body-overlay").removeClass("opened");
  });

  // Header sticky
  $(window).scroll(function () {
    if ($(this).scrollTop() > 250) {
      $("#header-sticky").addClass("active-sticky");
    } else {
      $("#header-sticky").removeClass("active-sticky");
    }
  });

  // Back to top js  
  if ($(".back-to-top-wrap path").length > 0) {
    var progressPath = document.querySelector(".back-to-top-wrap path");
    var pathLength = progressPath.getTotalLength();
    progressPath.style.transition = progressPath.style.WebkitTransition =
      "none";
    progressPath.style.strokeDasharray = pathLength + " " + pathLength;
    progressPath.style.strokeDashoffset = pathLength;
    progressPath.getBoundingClientRect();
    progressPath.style.transition = progressPath.style.WebkitTransition =
      "stroke-dashoffset 10ms linear";
    var updateProgress = function () {
      var scroll = $(window).scrollTop();
      var height = $(document).height() - $(window).height();
      var progress = pathLength - (scroll * pathLength) / height;
      progressPath.style.strokeDashoffset = progress;
    };
    updateProgress();
    $(window).scroll(updateProgress);
    var offset = 150;
    var duration = 550;
    jQuery(window).on("scroll", function () {
      if (jQuery(this).scrollTop() > offset) {
        jQuery(".back-to-top-wrap").addClass("active-progress");
      } else {
        jQuery(".back-to-top-wrap").removeClass("active-progress");
      }
    });
    jQuery(".back-to-top-wrap").on("click", function (event) {
      event.preventDefault();
      jQuery("html, body").animate({
        scrollTop: 0
      }, duration);
      return false;
    });
  }

  // Data Css js
  $("[data-background").each(function () {
    $(this).css(
      "background-image",
      "url( " + $(this).attr("data-background") + "  )"
    );
  });

  $("[data-width]").each(function () {
    $(this).css("width", $(this).attr("data-width"));
  });

  $("[data-bg-color]").each(function () {
    $(this).css("background-color", $(this).attr("data-bg-color"));
  });

  // Initialize Select2
  $(function () {
    if ($.fn.select2) {
      // Function to render icons
      function renderIcon(option) {
        if (!option.id) return option.text; // Return plain text for placeholder

        const iconClass = $(option.element).data("icon"); // Get the icon class
        const iconHtml = iconClass ? `<i class="${iconClass} mr-2"></i>` : "";

        return $(`<span>${iconHtml} ${option.text}</span>`);
      }

      // Initialize Select2 with Icons (Now with Search Enabled)
      $('#select2Icons').select2({
        dropdownParent: $('#select2Icons').parent(),
        templateResult: renderIcon,
        minimumResultsForSearch: -1,
        templateSelection: renderIcon,
        escapeMarkup: function (markup) {
          return markup; // Allows HTML rendering
        }
      });

      // Function to render flags
      function renderFlag(option) {
        if (!option.id) return option.text; // Return plain text for placeholder

        const flagUrl = $(option.element).data("flag"); // Get flag URL
        const flagHtml = flagUrl ? `<img src="${flagUrl}" class="mr-2" width="20" height="15" style="border-radius: 3px;">` : "";

        return $(`<span>${flagHtml} ${option.text}</span>`);
      }

      // Initialize Select2 with Flags (Search Enabled)
      $('#select2Flags').select2({
        dropdownParent: $('#select2Flags').parent(),
        templateResult: renderFlag,
        templateSelection: renderFlag,
        escapeMarkup: function (markup) {
          return markup; // Allows HTML rendering
        }
      });
    }
  });

  // MagnificPopup image view
  $(document).ready(function () {
    if ($.fn.magnificPopup) {
      if ($(".popup-image").length) {
        $(".popup-image").magnificPopup({
          type: "image",
          gallery: { enabled: true },
        });
      }

      if ($(".popup-video").length) {
        $(".popup-video").magnificPopup({
          type: "iframe",
        });
      }
    }
  });

  // Set the default language to English
  var tnum = "en";

  $(document).ready(function () {
    if (localStorage.getItem("primary") != null) {
      var primary_val = localStorage.getItem("primary");
      $("#ColorPicker1").val(primary_val);
      var secondary_val = localStorage.getItem("secondary");
      $("#ColorPicker2").val(secondary_val);
    }

    $(document).on("click", function (e) {
      $(".translate_wrapper, .more_lang").removeClass("active");
    });
    $(".translate_wrapper .current_lang").on("click", function (e) {
      e.stopPropagation();
      $(this).parent().toggleClass("active");

      setTimeout(function () {
        $(".more_lang").toggleClass("active");
      }, 5);
    });

    /*TRANSLATE*/
    translate(tnum);

    $(".more_lang .lang").on("click", function () {
      $(this).addClass("selected").siblings().removeClass("selected");
      $(".more_lang").removeClass("active");

      var i = $(this).find("i").attr("class");
      var lang = $(this).attr("data-value");
      var tnum = lang;
      translate(tnum);

      $(".current_lang .lang-txt").text(lang);
      $(".current_lang i").attr("class", i);
    });
  });

  var trans = [
    {
      en: "General",
      pt: "Geral",
      es: "Generalo",
      fr: "GÃƒÂ©nÃƒÂ©rale",
      de: "Generel",
      cn: "Ã¤Â¸â‚¬Ã¨Ë†Â¬",
      ae: "Ã˜Â­Ã˜Â¬Ã™â€ Ã˜Â±Ã˜Â§Ã™â€ž Ã™â€žÃ™Ë†Ã˜Â§Ã˜Â¡",
    },
    {
      en: "Dashboards,widgets & layout.",
      pt: "PainÃƒÂ©is, widgets e layout.",
      es: "Paneloj, fenestraÃ„Âµoj kaj aranÃ„Âo.",
      fr: "Tableaux de bord, widgets et mise en page.",
      de: "Dashboards, widgets en lay-out.",
      cn: "Ã¤Â»ÂªÃ¨Â¡Â¨Ã¦ÂÂ¿Ã¯Â¼Å’Ã¥Â°ÂÃ¥Â·Â¥Ã¥â€¦Â·Ã¥â€™Å’Ã¥Â¸Æ’Ã¥Â±â‚¬Ã£â‚¬â€š",
      ae: "Ã™â€žÃ™Ë†Ã˜Â­Ã˜Â§Ã˜Âª Ã˜Â§Ã™â€žÃ™â€¦Ã˜Â¹Ã™â€žÃ™Ë†Ã™â€¦Ã˜Â§Ã˜Âª Ã™Ë†Ã˜Â§Ã™â€žÃ˜Â£Ã˜Â¯Ã™Ë†Ã˜Â§Ã˜Âª Ã™Ë†Ã˜Â§Ã™â€žÃ˜ÂªÃ˜Â®Ã˜Â·Ã™Å Ã˜Â·.",
    },
    {
      en: "Dashboards",
      pt: "PainÃƒÂ©is",
      es: "Paneloj",
      fr: "Tableaux",
      de: "Dashboards",
      cn: " Ã¤Â»ÂªÃ¨Â¡Â¨Ã¦ÂÂ¿ ",
      ae: "Ã™Ë†Ã˜Â­Ã˜Â§Ã˜Âª Ã˜Â§Ã™â€žÃ™â€šÃ™Å Ã˜Â§Ã˜Â¯Ã˜Â© ",
    },
    {
      en: "Default",
      pt: "PadrÃƒÂ£o",
      es: "Vaikimisi",
      fr: "DÃƒÂ©faut",
      de: "Standaard",
      cn: "Ã©â€ºÂ»Ã¥Â­ÂÃ¥â€¢â€ Ã¥â€¹â„¢",
      ae: "Ã™Ë†Ã˜Â¥Ã™ÂÃ˜ÂªÃ˜Â±Ã˜Â§Ã˜Â¶Ã™Å ",
    },
    {
      en: "Ecommerce",
      pt: "ComÃƒÂ©rcio eletrÃƒÂ´nico",
      es: "Komerco",
      fr: "Commerce ÃƒÂ©lectronique",
      de: "E-commerce",
      cn: "Ã©â€ºÂ»Ã¥Â­ÂÃ¥â€¢â€ Ã¥â€¹â„¢",
      ae: "Ã™Ë†Ã˜Â§Ã™â€žÃ˜ÂªÃ˜Â¬Ã˜Â§Ã˜Â±Ã˜Â© Ã˜Â§Ã™â€žÃ˜Â¥Ã™â€žÃ™Æ’Ã˜ÂªÃ˜Â±Ã™Ë†Ã™â€ Ã™Å Ã˜Â©",
    },
    {
      en: "Widgets",
      pt: "Ferramenta",
      es: "Vidin",
      fr: "Widgets",
      de: "Widgets",
      cn: "Ã¥Â°ÂÃ©Æ’Â¨Ã¤Â»Â¶",
      ae: "Ã™Ë†Ã˜Â§Ã™â€žÃ˜Â­Ã˜Â§Ã˜Â¬Ã™Å Ã˜Â§Ã˜Âª",
    },
    {
      en: "Page layout",
      pt: "Layout da pÃƒÂ¡gina",
      es: "PaÃ„Âa aranÃ„Âo",
      fr: "Tableaux",
      de: "Mise en page",
      cn: "Ã© ÂÃ©ÂÂ¢Ã¤Â½Ë†Ã¥Â±â‚¬",
      ae: "Ã™Ë†Ã˜ÂªÃ˜Â®Ã˜Â·Ã™Å Ã˜Â· Ã˜Â§Ã™â€žÃ˜ÂµÃ™ÂÃ˜Â­Ã˜Â©",
    },
    {
      en: "Applications",
      pt: "FormulÃƒÂ¡rios",
      es: "Aplikoj",
      fr: "Applications",
      de: "Toepassingen",
      cn: "Ã¦â€¡â€°Ã§â€Â¨Ã© ËœÃ¥Å¸Å¸",
      ae: "Ã™Ë†Ã˜Â§Ã™â€žÃ˜ÂªÃ˜Â·Ã˜Â¨Ã™Å Ã™â€šÃ˜Â§Ã˜Âª",
    },
    {
      en: "Ready to use Apps",
      pt: "Pronto para usar aplicativos",
      es: "Preta uzi Apps",
      fr: " Applications prÃƒÂªtes Ãƒ  lemploi ",
      de: "Klaar om apps te gebruiken",
      cn: "Ã¤Â»ÂªÃ¨Â¡Â¨Ã¦ÂÂ¿",
      ae: "Ã˜Â¬Ã˜Â§Ã™â€¡Ã˜Â² Ã™â€žÃ˜Â§Ã˜Â³Ã˜ÂªÃ˜Â®Ã˜Â¯Ã˜Â§Ã™â€¦ Ã˜Â§Ã™â€žÃ˜ÂªÃ˜Â·Ã˜Â¨Ã™Å Ã™â€šÃ˜Â§Ã˜Âª",
    },
  ];

  function translate(tnum) {
    for (var i = 1; i < 9; i++) {

      $(".lan-" + i).text(trans[i - 1][tnum]);
    }
  }

  // InHover Active Js
  $('.hover_active').on('mouseenter', function () {
    $(this).addClass('active').parent().siblings().find('.hover_active').removeClass('active');
  });

  // Initialize alert 
  document.querySelectorAll('.close-btn').forEach((btn) => {
    btn.addEventListener('click', function () {
      const alertBox = this.closest('.alert-box'); // Find the parent alert box
      alertBox.classList.add('hidden');
      setTimeout(() => {
        alertBox.style.display = 'none';
      }, 400); // Match the transition duration
    });
  });

})(jQuery);