// PARALLAX EFFECT - Βελτιωμένη έκδοση
(function() {
    'use strict';
   
    let parallaxElements = [];
    let ticking = false;
    let isReducedMotion = false;
   
    function updateParallax() {
        if (isReducedMotion) return;
        
        const scrollTop = window.pageYOffset;
        const windowHeight = window.innerHeight;
       
        parallaxElements.forEach(element => {
            const rect = element.getBoundingClientRect();
            const elementTop = rect.top + scrollTop;
            const elementHeight = rect.height;
            
            // Έλεγχος αν το element είναι ορατό
            if (elementTop + elementHeight < scrollTop || elementTop > scrollTop + windowHeight) {
                return; // Skip αν δεν είναι ορατό
            }
            
            // Smooth parallax effect
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
    
    function onResize() {
        // Debounce resize events
        clearTimeout(window.parallaxResizeTimeout);
        window.parallaxResizeTimeout = setTimeout(() => {
            // Re-calculate positions if needed
            updateParallax();
        }, 150);
    }
   
    function init() {
        // Check for reduced motion preference
        isReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        
        parallaxElements = document.querySelectorAll('.outerimagelarge .imagewrapper');
       
        if (parallaxElements.length > 0 && !isReducedMotion) {
            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', onResize, { passive: true });
            
            // Initial call
            updateParallax();
        }
    }
    
    function cleanup() {
        window.removeEventListener('scroll', onScroll);
        window.removeEventListener('resize', onResize);
        clearTimeout(window.parallaxResizeTimeout);
    }
   
    // Αρχικοποίηση
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Cleanup για SPA applications
    window.addEventListener('beforeunload', cleanup);
   
})();