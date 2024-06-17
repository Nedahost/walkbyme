jQuery(document).on("ready", function() {
        
        
        jQuery(".carousel").slick({
        dots: false,
        arrows:true,
        autoplay:false,
        infinite: false,
        slidesToShow: 4,
        slidesToScroll: 4,
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
            arrows:false
          }
        }
        // You can unslick at a given breakpoint now by adding:
        // settings: "unslick"
        // instead of a settings object
      ]
        });

        jQuery(".slideshow").slick({
          dots: false,
          arrows:true,
          autoplay:false,
          infinite: false,
          slidesToShow: 1,
          slidesToScroll: 1,
          
          });
        
});



function openSearchModal() {
  var modal = document.getElementById("searchModal");
  modal.style.display = "block";
  modal.querySelector('input[name="s"]').focus();
}

function closeSearchModal() {
  document.getElementById("searchModal").style.display = "none";
}

// Κλείσιμο του modal αν πατηθεί εκτός περιοχής του modal
window.onclick = function(event) {
  var modal = document.getElementById("searchModal");
  if (event.target == modal) {
      modal.style.display = "none";
  }
}

// Προσθήκη event listener για το φορτωμένο της σελίδας
document.addEventListener("DOMContentLoaded", function() {
  // Κρύψτε το modal κατά το φορτωμένο της σελίδας
  document.getElementById("searchModal").style.display = "none";
});

// Κλείσιμο με το κουμπί Esc
document.addEventListener("keydown", function(event) {
  if (event.key === "Escape") {
      closeSearchModal();
  }
});





//lightbox για τις εικόνες των προϊόντων
jQuery(document).ready(function($) {
  // Ενεργοποίηση lightbox με κλικ στην εικόνα του προϊόντος
  $('.woocommerce-product-gallery__image').on('click', function(e) {
      e.preventDefault();
      $(this).closest('.woocommerce-product-gallery').find('.woocommerce-product-gallery__trigger').click();
  });
});



jQuery(document).ready(function($) {
  $('.accordion-title').click(function() {
      if ($(this).hasClass('active')) {
          $(this).removeClass('active');
          $(this).siblings('.accordion-content').slideUp(300);
      } else {
          $('.accordion-title').removeClass('active');
          $('.accordion-content').slideUp(300);
          $(this).addClass('active');
          $(this).siblings('.accordion-content').slideDown(300);
      }
  });
});
