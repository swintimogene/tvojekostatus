/**
    * @package Fortune Theme
	
    1. Main Navigation
    2. Magnific Popup
    3. Carousel (based on owl carousel plugin)
    4. Content Slider (based on owl carousel plugin)
    5. FitVid (responsive video)
    6. Sticky Header
*/
jQuery('.dropdown-submenu a').on('hover',function() {
    isRTL = jQuery('html').attr('dir');
    var dropdownList = jQuery(this).next('.dropdown-menu');
    var dropdownOffset = jQuery(this).offset();
    var offsetLeft = dropdownOffset.left;
    var dropdownWidth = dropdownList.width();
    var docWidth = jQuery(window).width()-2;

    var subDropdown = jQuery('.dropdown-menu').eq(1);
    var subDropdownWidth = subDropdown.width();

   // var isDropdownVisible = (offsetLeft + dropdownWidth <= docWidth);
    var isSubDropdownVisible = (offsetLeft + dropdownWidth + subDropdownWidth <= docWidth);
    if (!isSubDropdownVisible) {
        var Float = isRTL=='rtl' ? 'left' : 'right';
        dropdownList.css(Float,'100%');
    } else {
        var Float = isRTL=='rtl' ? 'right' : 'left';
        dropdownList.css(Float,'100%');
    }
});
;(function($){
  "use strict";


 /* ----------------------------------------------------------- */
  /*  1. Fancy Number input Button
  /* ----------------------------------------------------------- */

jQuery("input[type='button']").on("click", function() {
  var oldValue='';
  var button = jQuery(this);
  if (button.val() == "+") { 
    oldValue = button.prev("input").val();
    var newVal = parseFloat(oldValue) + 1; button.prev("input").val(newVal);
    jQuery('input[name="update_cart"]').prop('disabled',false);
  } else {
   // Don't allow decrementing below zero oldValue = button.next("input").val();
      oldValue = button.next("input").val(); if (oldValue > 0) {
      var newVal = parseFloat(oldValue) - 1;
      jQuery('input[name="update_cart"]').prop('disabled',false);
    } else {
      newVal = 0;
    } button.next("input").val(newVal)
  }

});
  /* ----------------------------------------------------------- */
  /*  1. Main Navigation
  /* ----------------------------------------------------------- */


  jQuery(document).ready(function () {
    var bMobile;  // true if in mobile mode
    var isMobile;
    // Initiate event handlers
    function init() {
        var isMobile = {
            Android: function () {
                return navigator.userAgent.match(/Android/i);
            },
            BlackBerry: function () {
                return navigator.userAgent.match(/BlackBerry/i);
            },
            iOS: function () {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i);
            },
            Opera: function () {
                return navigator.userAgent.match(/Opera Mini/i);
            },
            Windows: function () {
                return navigator.userAgent.match(/IEMobile/i);
            },
            any: function () {
                return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
            }
        };

        var oMenus = jQuery('.navbar-nav  .dropdown'), nTimer;
        if (isMobile.any()) {
            // Set up menu on click for Mobile and ipad mode
            oMenus.on({
                'mouseenter touchstart': function (event) {
                    event.preventDefault();
                    clearTimeout(nTimer);
                    oMenus.removeClass('open');
                    jQuery(this).addClass('open').slideDown();
                }, });
            
              
            jQuery('ul.dropdown-menu li a').on('click touchend', function () {
              if(jQuery(this).parent().hasClass('menu-item-has-children')){
                jQuery(this).parent().toggleClass('open');
                return false;
              }else{
                var link = jQuery(this).attr('href');
                window.open(link, '_self'); // opens in new window as requested
                return false; // prevent anchor click
              }
            });
          
        } else {
            oMenus.on({'mouseenter touchstart': function (event) {
                event.preventDefault();
                clearTimeout(nTimer);
                oMenus.removeClass('open');
                jQuery(this).addClass('open').slideDown();
            },
            });
        }
    }
    jQuery('button.navbar-toggle').click(function(){ jQuery('.navbar-collapse').toggleClass(' in'); jQuery('.navbar-collapse').attr('aria-expanded',true); })
    jQuery(document).ready(function () {
        // Your other code to run on DOM ready...
        init();
    });
    jQuery(window).resize(init);

    jQuery('article,.page-content, .footer-widgets').find('table').addClass('table table-striped table-bordered');
    jQuery('.woocommerce-checkout .input-text').addClass('form-control');
});


  
  /* ----------------------------------------------------------- */
  /*  3. Magnific Popup
  /* ----------------------------------------------------------- */
  $('.popup-link').magnificPopup({
      type:'image',
      // Delay in milliseconds before popup is removed
      removalDelay: 300,

      gallery:{
          enabled:true
      },

      // Class that is added to popup wrapper and background
      // make it unique to apply your CSS animations just to this exact popup
      mainClass: 'mfp-fade'
  });


  /* ----------------------------------------------------------- */
  /*  5. Carousel (based on owl carousel plugin)
  /* ----------------------------------------------------------- */
  var owl = $("#owl-carousel");

  owl.owlCarousel({
      items : 4, //4 items above 1000px browser width
      itemsDesktop : [1000,4], //4 items between 1000px and 901px
      itemsDesktopSmall : [900,2], // 4 items betweem 900px and 601px
      itemsTablet: [600,2], //2 items between 600 and 0;
      itemsMobile : [480,1], // itemsMobile disabled - inherit from itemsTablet option
      pagination : false,
      scrollPerPage: false
  });

  // Custom Navigation Events
  $("#carousel-next").click(function(){
      owl.trigger('owl.next');
  });
  $("#carousel-prev").click(function(){
      owl.trigger('owl.prev');
  });


  // carousel with 3 elements
  (function($) {
      var owl = $(".owl-carousel-3");

      owl.owlCarousel({
          items : 3, //3 items above 1000px browser width
          itemsDesktop : [1000,2], //4 items between 1000px and 901px
          itemsDesktopSmall : [900,2], // 4 items betweem 900px and 601px
          itemsTablet: [600,2], //2 items between 600 and 0;
          itemsMobile : [480,1], // itemsMobile disabled - inherit from itemsTablet option
          pagination : false
      });

      // Custom Navigation Events
      $("#carousel-next-alt").click(function(){
          owl.trigger('owl.next');
      });
      $("#carousel-prev-alt").click(function(){
          owl.trigger('owl.prev');
  });
  })(jQuery);



  /* ----------------------------------------------------------- */
  /*  6. Content Slider (based on owl carousel plugin)
  /* ----------------------------------------------------------- */
  $(".owl-slider").owlCarousel({

      navigation : true, // Show next and prev buttons
      slideSpeed : 300,
      paginationSpeed : 400,
      singleItem:true,
      navigationText: ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
      pagination: true,
      autoPlay : false
      
  });
  

  /* ----------------------------------------------------------- */
  /*  7. FitVid (responsive video)
  /* ----------------------------------------------------------- */
  $(".video-holder, .audio-holder").fitVids();


  /* ----------------------------------------------------------- */
  /*  -- Misc
  /* ----------------------------------------------------------- */

  $('.title-accent > h3').each(function(){
      var me = $(this);
      me.html(me.html().replace(/^(\w+)/, '<span>$1</span>'));
  });

  // Back to Top
  $("#back-top").hide();
  
  if($(window).width() > 991) {
      $('body').append('<div id="back-top"><a href="#top"><i class="fa fa-chevron-up"></i></a></div>')
      $(window).scroll(function () {
          if ($(this).scrollTop() > 100) {
              $('#back-top').fadeIn();
          } else {
              $('#back-top').fadeOut();
          }
      });

      // scroll body to 0px on click
      $('#back-top a').click(function(e) {
          e.preventDefault();
          $('body,html').animate({
              scrollTop: 0
          }, 400);
          return false;
      });
  };

  // Animation on scroll
  var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
  if (isMobile == false) {
      
      $("[data-animation]").each(function() {

      var $this = $(this);

      $this.addClass("animation");

      if(!$("html").hasClass("no-csstransitions") && $(window).width() > 767) {

          $this.appear(function() {

              var delay = ($this.attr("data-animation-delay") ? $this.attr("data-animation-delay") : 1);

              if(delay > 1) $this.css("animation-delay", delay + "ms");
              $this.addClass($this.attr("data-animation"));

              setTimeout(function() {
                  $this.addClass("animation-visible");
              }, delay);

          }, {accX: 0, accY: -170});

      } else {

          $this.addClass("animation-visible");

      }

  });  
  }


  /* ----------------------------------------------------------- */
  /*  8. Sticky Header
  /* ----------------------------------------------------------- */

  // Set options
  var headerSticky = $('header.header');
  // Check for sticky header
  if ( ( headerSticky.hasClass('header-default') || headerSticky.hasClass('header-transparent') || headerSticky.hasClass('menu-colored') || headerSticky.hasClass('menu-pills') ) && header.is_sticky==1 ) {
    var options = {

      offset: 50, // OR — offset: '.classToActivateAt',
      throttle: 200,

      onStick: function() {},
      onUnstick: function() {},
      onDestroy: function() {},

    };
    // Create a new instance of Headhesive
    var headhesive = new Headhesive('header.header', options);
  }

  /* ----------------------------------------------------------- */
  /*  13. Header Transparent
  /* ----------------------------------------------------------- */
  if($('.header').hasClass('header-transparent') || $('.header').hasClass('header-fixed')) {
    $(window).scroll(function() {    
      var scroll = $(window).scrollTop();

      if (scroll >= 400) {
          $(".header:not(.headhesive)").addClass("hidden");
      } else {
          $(".header:not(.headhesive)").removeClass("hidden");
      }
    });
  };

})(jQuery);