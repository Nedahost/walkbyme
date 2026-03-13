class ProductLightbox {
    constructor() {
        this.lightbox = document.getElementById('productLightbox');
        if (!this.lightbox) return;

        this.image = this.lightbox.querySelector('.product-lightbox__image');
        this.counter = this.lightbox.querySelector('.product-lightbox__current');
        this.closeBtn = this.lightbox.querySelector('.product-lightbox__close');
        this.prevBtn = this.lightbox.querySelector('.product-lightbox__nav--prev');
        this.nextBtn = this.lightbox.querySelector('.product-lightbox__nav--next');

        this.images = [];
        this.currentIndex = 0;
        this.isZoomed = false;

        this.collectImages();
        this.bindEvents();
    }

    collectImages() {
        const seen = new Set();
        document.querySelectorAll('.product-gallery__item img, .product-gallery__slide img').forEach(img => {
            const src = img.getAttribute('src');
            if (src && !seen.has(src)) {
                seen.add(src);
                this.images.push(src);
            }
        });
    }

    bindEvents() {
        document.querySelectorAll('.product-gallery__item img, .product-gallery__slide img').forEach(img => {
            img.addEventListener('click', (e) => {
                const src = e.target.getAttribute('src');
                const index = this.images.indexOf(src);
                if (index !== -1) {
                    this.open(index);
                }
            });
        });

        this.closeBtn.addEventListener('click', () => this.close());
        this.prevBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.prev();
        });
        this.nextBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.next();
        });

        this.lightbox.addEventListener('click', (e) => {
            if (e.target === this.lightbox) {
                this.close();
            }
        });

        this.image.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleZoom();
        });

        document.addEventListener('keydown', (e) => {
            if (!this.lightbox.classList.contains('is-active')) return;
            if (e.key === 'Escape') this.close();
            if (e.key === 'ArrowLeft') this.prev();
            if (e.key === 'ArrowRight') this.next();
        });

        let startX = 0;
        this.image.addEventListener('touchstart', (e) => {
            if (this.isZoomed) return;
            startX = e.touches[0].clientX;
        }, { passive: true });

        this.image.addEventListener('touchend', (e) => {
            if (this.isZoomed) return;
            const diff = e.changedTouches[0].clientX - startX;
            if (Math.abs(diff) > 50) {
                if (diff < 0) {
                    this.next();
                } else {
                    this.prev();
                }
            }
        });
    }

    open(index) {
        this.currentIndex = index;
        this.updateImage();
        this.lightbox.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    close() {
        this.lightbox.classList.remove('is-active');
        this.image.classList.remove('is-zoomed');
        this.isZoomed = false;
        document.body.style.overflow = '';
    }

    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this.updateImage();
    }

    next() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
        this.updateImage();
    }

    toggleZoom() {
        this.isZoomed = !this.isZoomed;
        this.image.classList.toggle('is-zoomed');
    }

    updateImage() {
        this.image.src = this.images[this.currentIndex];
        this.counter.textContent = this.currentIndex + 1;
        this.image.classList.remove('is-zoomed');
        this.isZoomed = false;
    }
}

jQuery(document).ready(function($) {
    
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
    }
   
    // Search
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

    // Accordion
    document.querySelectorAll('.accordion__title').forEach(title => {
        title.addEventListener('click', function() {
            const content = this.nextElementSibling;
            const isActive = this.classList.contains('is-active');
            
            if (isActive) {
                this.classList.remove('is-active');
                content.classList.remove('is-open');
                gsap.to(content, {
                    height: 0,
                    duration: 0.3,
                    ease: "power1.inOut",
                    onComplete: () => gsap.set(content, { display: 'none' })
                });
            } else {
                this.classList.add('is-active');
                content.classList.add('is-open');
                gsap.set(content, { display: 'block', height: 0, overflow: 'hidden' });
                gsap.to(content, {
                    height: "auto",
                    duration: 0.3,
                    ease: "power1.inOut"
                });
            }
        });
    });

    // Mobile carousel counter
    const track = document.querySelector('.product-gallery__track');
    const galleryCounter = document.querySelector('.product-gallery__counter .product-gallery__current');
    
    if (track && galleryCounter) {
        const slides = track.querySelectorAll('.product-gallery__slide');
        
        track.addEventListener('scroll', () => {
            const scrollLeft = track.scrollLeft;
            const slideWidth = slides[0].offsetWidth;
            const current = Math.round(scrollLeft / slideWidth) + 1;
            galleryCounter.textContent = current;
        });
    }

    // Lightbox
    new ProductLightbox();
});



// Footer accordion (mobile only)
function initFooterAccordion() {
    const isMobile = window.innerWidth < 576;
    const titles = document.querySelectorAll('.footer-widgets .widget_nav_menu h3');

    titles.forEach(title => {
        // Αφαίρεσε παλιό listener αν υπάρχει
        title.removeEventListener('click', title._accordionHandler);

        if (isMobile) {
            const content = title.nextElementSibling;
            
            // Κλείσε όλα αρχικά
            if (content) {
                content.classList.remove('is-open');
                title.classList.remove('is-active');
            }

            title._accordionHandler = function() {
                const content = this.nextElementSibling;
                if (!content) return;

                const isActive = this.classList.contains('is-active');

                // Κλείσε τα υπόλοιπα
                titles.forEach(t => {
                    t.classList.remove('is-active');
                    if (t.nextElementSibling) {
                        t.nextElementSibling.classList.remove('is-open');
                    }
                });

                if (!isActive) {
                    this.classList.add('is-active');
                    content.classList.add('is-open');
                }
            };

            title.addEventListener('click', title._accordionHandler);
        } else {
            // Desktop: σβήσε active states
            title.classList.remove('is-active');
            const content = title.nextElementSibling;
            if (content) {
                content.classList.remove('is-open');
            }
        }
    });
}

// Init + resize
initFooterAccordion();
window.addEventListener('resize', initFooterAccordion);