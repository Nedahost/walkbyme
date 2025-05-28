jQuery(document).ready(function($) {
    const slickOptions = {
        dots: false,
        arrows: true,
        autoplay: false,
        infinite: false,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: false
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    dots: false,
                    arrows: false
                }
            }
        ]
    };
    $(".carousel").slick({
        ...slickOptions,
        slidesToShow: 4,
        slidesToScroll: 4
    });
    $(".slideshow").slick({
        ...slickOptions,
        dots: true,
        slidesToShow: 1,
        slidesToScroll: 1
    });
    $(".slideshow, .carousel").on('init', function(event, slick){
      $(this).addClass('slick-initialized');
    });
  });
  
  //lightbox για τις εικόνες των προϊόντων
  jQuery(document).ready(function($) {
    // Ενεργοποίηση lightbox με κλικ στην εικόνα του προϊόντος
    $('.woocommerce-product-gallery__image').on('click', function(e) {
        e.preventDefault();
        $(this).closest('.woocommerce-product-gallery').find('.woocommerce-product-gallery__trigger').click();
    });
  });
  
  // Accordion functionality
  jQuery(document).ready(function($) {
      $('.accordion-title').click(function() {
          var $content = $(this).siblings('.accordion-content');
          var $currentAccordion = $(this).closest('.accordion-item');
         
          if ($(this).hasClass('active')) {
              // Κλείσε μόνο αυτό το accordion
              $(this).removeClass('active');
              $content.slideUp(300);
          } else {
              // Άνοιξε αυτό το accordion χωρίς να κλείσεις τα άλλα
              $(this).addClass('active');
              $content.slideDown(300);
          }
      });
  });