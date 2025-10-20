jQuery(document).ready(function($) {
    
    // Έλεγχος αν υπάρχει το Slick plugin
    if (typeof $.fn.slick !== 'undefined') {
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
        
        // Έλεγχος αν υπάρχουν τα elements πριν τα αρχικοποιήσεις
        if ($(".carousel").length) {
            $(".carousel").slick({
                ...slickOptions,
                slidesToShow: 4,
                slidesToScroll: 4
            });
        }
        
        if ($(".slideshow").length) {
            $(".slideshow").slick({
                ...slickOptions,
                dots: true,
                slidesToShow: 1,
                slidesToScroll: 1
            });
        }
        
        $(".slideshow, .carousel").on('init', function(event, slick){
            $(this).addClass('slick-initialized');
        });
    }
   
    // Search functionality
    $('.search-trigger').click(function() {
        $('.search-overlay').addClass('active');
        setTimeout(function() {
            $('.search-field').focus();
        }, 300);
    });
    
    $('.close-search').click(function() {
        $('.search-overlay').removeClass('active');
    });
    
    $(document).keyup(function(e) {
        if (e.key === "Escape") {
            $('.search-overlay').removeClass('active');
        }
    });
    
    // Lightbox για τις εικόνες των προϊόντων
    $('.woocommerce-product-gallery__image').on('click', function(e) {
        e.preventDefault();
        $(this).closest('.woocommerce-product-gallery').find('.woocommerce-product-gallery__trigger').click();
    });
    
    // Accordion functionality
    $('.accordion-title').click(function() {
        var $content = $(this).siblings('.accordion-content');
        
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $content.slideUp(300);
        } else {
            $(this).addClass('active');
            $content.slideDown(300);
        }
    });
});