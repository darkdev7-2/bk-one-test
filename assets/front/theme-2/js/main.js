/*----------------------------------------*/
/* Digibank-2.0 main jQuery
/*----------------------------------------*/

(function ($) {
  'use strict';

  // Pre loader activation
  $(window).on('load', function (event) {
    $('#preloader').delay(500).fadeOut(500);
  });

  // Mobile menu
  if ($("#mobile-menu").length > 0) {
    $("#mobile-menu").meanmenu({
      meanMenuContainer: ".mobile-menu",
      meanScreenWidth: "991",
      meanExpand: ['<i class="fa-regular fa-angle-right"></i>'],
    });
  }

  // Sidebar Toggle
  $(".offcanvas-close,.offcanvas-overlay").on("click", function () {
    $(".offcanvas-area").removeClass("info-open");
    $(".offcanvas-overlay").removeClass("overlay-open");
  });
  $(".sidebar-toggle").on("click", function () {
    $(".offcanvas-area").addClass("info-open");
    $(".offcanvas-overlay").addClass("overlay-open");
  });

  //Body overlay Js
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

  // Nice Select
  $('.single-input select').niceSelect();

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

  // Language Switcher
  document.addEventListener('DOMContentLoaded', function () {
    const langToggle = document.getElementById('header-lang-toggle');
    const langList = document.getElementById('language-list');
    const langOptions = langList ? langList.querySelectorAll('a') : [];

    if (langToggle && langList) {
      langToggle.addEventListener('click', (event) => {
        event.stopPropagation();
        langList.classList.toggle('lang-list-open');
      });

      langOptions.forEach(option => {
        option.addEventListener('click', (event) => {
          event.preventDefault();
          const selectedLang = event.target.getAttribute('data-lang');

          // Update the text in the toggle
          const langTextSpan = langToggle.querySelector('.lang-text');
          if (langTextSpan) {
            langTextSpan.textContent = selectedLang;
          }

          // Remove active class from all options
          langOptions.forEach(opt => opt.classList.remove('active'));

          // Add active class to the selected option
          event.target.classList.add('active');

          // Hide the language list
          langList.classList.remove('lang-list-open');
        });
      });

      document.addEventListener('click', (event) => {
        if (!langToggle.contains(event.target) && !langList.contains(event.target)) {
          langList.classList.remove('lang-list-open');
        }
      });
    }
  });

  // color Switcher
  const themeSwitcher = document.getElementById('themeSwitcher');

  // Load theme preference from localStorage
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-theme');
    themeSwitcher.checked = true;
  }

  themeSwitcher.addEventListener('change', function () {
    if (themeSwitcher.checked) {
      document.body.classList.add('dark-theme');
      localStorage.setItem('theme', 'dark');
    } else {
      document.body.classList.remove('dark-theme');
      localStorage.setItem('theme', 'light');
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

  // Odometer active
  var odo = $('.odometer');
  odo.each(function () {
    $('.odometer').appear(function (e) {
      var countNumber = $(this).attr('data-count');
      $(this).html(countNumber);
    });
  });

  // Datepicker active
  if ($('#d_today').length) {
    (function () {
      const d_today = new Datepicker(document.querySelector('#d_today'), {
        buttonClass: 'btn',
        todayHighlight: true
      });
    })()
  }

  // FAQ active item
  $(document).on('click', '.accordion-item', function () {
    $('.accordion-item').removeClass('active');
    $(this).addClass('active');
  });

  // Image Preview
  $(document).on('change', 'input[type="file"]', function (event) {
    var $file = $(this),
      $label = $file.next('label'),
      $labelText = $label.find('span'),
      labelDefault = $labelText.text();

    var fileName = $file.val().split('\\').pop(),
      tmppath = URL.createObjectURL(event.target.files[0]);

    // Check successfully selection
    if (fileName) {
      $label.addClass('file-ok').css('background-image', 'url(' + tmppath + ')');
      $labelText.text(fileName);
    } else {
      $label.removeClass('file-ok');
      $labelText.text(labelDefault);
    }
  });

  // feedback-active
  if ($(".feedback-active").length > 0) {
    var feedback = new Swiper(".feedback-active", {
      slidesPerView: 3,
      spaceBetween: 30,
      loop: true,
      roundLengths: true,
      autoplay: {
        delay: 3000,
      },
      navigation: {
        nextEl: ".feedback-button-prev",
        prevEl: ".feedback-button-next",
      },
      pagination: {
        el: ".td-swiper-dot",
        clickable: true,
      },
      breakpoints: {
        1200: {
          slidesPerView: 3,
        },
        992: {
          slidesPerView: 3,
        },
        768: {
          slidesPerView: 2,
        },
        576: {
          slidesPerView: 1,
        },
        0: {
          slidesPerView: 1,
        },
      },
    });
  }

  // sponsor active
  if ($(".sponsor_active").length > 0) {
    var sponsor = new Swiper(".sponsor_active", {
      slidesPerView: 5,
      spaceBetween: 30,
      loop: true,
      roundLengths: true,
      autoplay: {
        delay: 3000,
      },
      breakpoints: {
        1400: {
          slidesPerView: 6,
        },
        1200: {
          slidesPerView: 5,
        },
        992: {
          slidesPerView: 4,
        },
        768: {
          slidesPerView: 3,
        },
        576: {
          slidesPerView: 2,
        },
        0: {
          slidesPerView: 1,
        },
      },
    });
  }

})(jQuery);