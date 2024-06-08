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
        
});



function openSearchModal() {
  document.getElementById("searchModal").style.display = "block";
}

function closeSearchModal() {
  document.getElementById("searchModal").style.display = "none";
}

// Κλείσιμο μοντάλ αν πατηθεί εκτός περιοχής του μοντάλ
window.onclick = function(event) {
  var modal = document.getElementById("searchModal");
  if (event.target == modal) {
    modal.style.display = "none";
  }
}

// Προσθήκη event listener για το φορτωμένο της σελίδας
document.addEventListener("DOMContentLoaded", function() {
  // Κρύψτε το μοντάλ κατά το φορτωμένο της σελίδας
  document.getElementById("searchModal").style.display = "none";
});







