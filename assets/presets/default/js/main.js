(function ($) {
  'use strict';

  //============================ Scroll To Top Js Start ========================
  var btn = $('.scroll-top');

  $(window).on('scroll', function () {
    if ($(window).scrollTop() > 300) {
      btn.addClass('show');
    } else {
      btn.removeClass('show');
    }
  });

  btn.on('click', function (e) {
    e.preventDefault();
    $('html, body').animate({
      scrollTop: 0
    }, '300');
  });
  //============================ Scroll To Top Js End ========================


  // ========================= Header Sticky Js Start ==============
  $(window).on('scroll', function () {
    if ($(window).scrollTop() >= 300) {
      $('.header__area').addClass('fixed-header');
    } else {
      $('.header__area').removeClass('fixed-header');
    }
  });
  // ========================= Header Sticky Js End===================


  //============================ Offcanvas Js Start ============================
  $(document).on('click', '.menu__open', function () {
    $('.offcanvas__area, .overlay').addClass('active');
  });

  $(document).on('click', '.menu__close, .overlay', function () {
    $('.offcanvas__area, .overlay').removeClass('active');
  });

  //============================ Offcanvas Js End ==============================


  //============================ Sidebar Js Start ============================
  $(document).on('click', '.sidebar__open', function () {
    $('.dashboard__sidebar, .overlay').addClass('active');
  });

  $(document).on('click', '.sidebar__close, .overlay', function () {
    $('.dashboard__sidebar, .overlay').removeClass('active');
  });

  //============================ Sidebar Js End ==============================


  //============================ Filter Js Start ============================
  $(document).on('click', '.filter__btn', function () {
    $('.filter__main, .overlay').addClass('active');
  });

  $(document).on('click', '.filter__close, .overlay', function () {
    $('.filter__main, .overlay').removeClass('active');
  });

  //============================ Filter Js End ==============================


  // ========================== Add Attribute For Bg Image Js Start =====================
  $('.bg--img').css('background-image', function () {
    var bg = 'url(' + $(this).data('background-image') + ')';
    return bg;
  });
  // ========================== Add Attribute For Bg Image Js End =====================


  // ========================= FullCalendar Popup Js End ===================
  if ($('#calendar').length) {
    document.addEventListener('DOMContentLoaded', function () {
      var calendarEl = document.getElementById('calendar');

      var calendar = new window.FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        themeSystem: 'bootstrap5',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: [{
            title: 'Meeting',
            start: '2025-09-20T10:30:00',
            end: '2025-09-20T12:30:00',
            color: '#0d6efd'
          },
          {
            title: 'Conference',
            start: '2025-09-22',
            end: '2025-09-24',
            color: '#198754'
          },
          {
            title: 'Lunch',
            start: '2025-09-23T13:00:00',
            color: '#dc3545'
          }
        ]
      });

      calendar.render();
    });
  }
  // ========================= FullCalendar Popup Js End ===================


  // ========================= Testimonial Swiper Js Start =====================
  const swiperTestimonials = new Swiper('.testimonial__slider', {
    loop: true,
    speed: 1000,
    spaceBeteen: 32,
    effect: 'creative',
    creativeEffect: {
      prev: {
        scale: ['0.9'],
        opacity: 0,
      },
      next: {
        scale: ['0.9'],
        opacity: 0,
      },
    },
    autoplay: {
      delay: 2000,
      disableOnInteraction: false,
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
  });
  // ========================= Testimonial Swiper Js End =====================


  // ========================= Auth Swiper Js Start =====================
  const swiperAuth = new Swiper('.auth__slider', {
    loop: true,
    speed: 1000,
    spaceBeteen: 32,
    autoplay: {
      delay: 2000,
      disableOnInteraction: false,
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
  });
  // ========================= Auth Swiper Js End =====================




  // ========================= Select2 Js Start =====================
  if ($('.select2').length) {
    $('.select2').select2();
  }
  // ========================= Select2 Js End =====================


  // ========================= Show Hide Password Js Start ===================
  if ($('.password-show-hide').length) {
    $('.password-show-hide').each(function () {
      $(this).on('click', function () {
        let inputField = $(this).closest('.password__field').find('input');
        let openEye = $(this).find('.open-eye-icon');
        let closeEye = $(this).find('.close-eye-icon');

        if (inputField.attr('type') === "password") {
          inputField.attr('type', 'text');
          openEye.hide();
          closeEye.show();
        } else {
          inputField.attr('type', 'password');
          openEye.show();
          closeEye.hide();
        }
      });
    });
  }
  // ========================= Show Hide Password Js End ===================


  // ========================= Scroll Reveal Js Start ===================
  const sr = ScrollReveal({
    origin: 'top',
    distance: '60px',
    duration: 1500,
    delay: 100,
    reset: false,
  })

  sr.reveal('.hero__wrapper, .section__heading', {
    delay: 60,
    origin: 'top',
  })

  sr.reveal('.hero__main, .pricing__wrap, .testimonial__wrap, .contact__wrap', {
    delay: 60,
    origin: 'bottom',
  })

  sr.reveal('.key__feature__single, .feature__single, .accordion-item, .blog__card, .about__single, .contact__card', {
    delay: 60,
    interval: 100,
    origin: 'bottom',
  })
  // ========================= Scroll Reveal Js End ===================


  // ========================== Table Data Label Js Start =====================
  Array.from(document.querySelectorAll('table')).forEach(table => {
    let heading = table.querySelectorAll('thead tr th');
    Array.from(table.querySelectorAll('tbody tr')).forEach((row) => {
      let columArray = Array.from(row.querySelectorAll('td'));
      if (columArray.length <= 1) return;
      columArray.forEach((colum, i) => {
        colum.setAttribute('data-label', heading[i].innerText)
      });
    });
  });
  // ========================== Table Data Label Js End =====================


  // ========================== Label Required Js Start =====================
  $.each($('input, select, textarea'), function (i, element) {
    if (element.hasAttribute('required')) {
      $(element).closest('.form-group').find('label').first().addClass('required');
    }
  });
  // ========================== Label Required Js End =====================


  // ========================= Preloader Js Start =====================
  $(window).on("load", function () {
    $(".preloader").fadeOut();
  })
  // ========================= Preloader Js End=====================

})(jQuery);