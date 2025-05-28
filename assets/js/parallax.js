// PARALLAX EFFECT - Καθαρός και απλός
(function() {
    'use strict';
    
    let parallaxElements = [];
    let ticking = false;
    
    function updateParallax() {
        const scrollTop = window.pageYOffset;
        
        parallaxElements.forEach(element => {
            const translateY = scrollTop * -0.2;
            element.style.transform = `translate3d(0, ${translateY}px, 0)`;
        });
        
        ticking = false;
    }
    
    function onScroll() {
        if (!ticking) {
            requestAnimationFrame(updateParallax);
            ticking = true;
        }
    }
    
    function init() {
        parallaxElements = document.querySelectorAll('.outerimagelarge .imagewrapper');
        
        if (parallaxElements.length > 0) {
            window.addEventListener('scroll', onScroll, { passive: true });
        }
    }
    
    // Αρχικοποίηση
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();