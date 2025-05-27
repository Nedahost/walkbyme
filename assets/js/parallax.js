/**
 * Simple iOS-Compatible Parallax Effect
 * Lightweight and optimized for all devices
 */

(function() {
    'use strict';
    
    console.log('Parallax script loaded'); // Debug log
    
    let parallaxElements = [];
    let isScrolling = false;
    
    // Ανίχνευση iOS devices
    function isIOS() {
        return /iPad|iPhone|iPod/.test(navigator.userAgent) || 
               (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    }
    
    // Απλός έλεγχος για υποστήριξη parallax
    function canUseParallax() {
        // Όχι parallax για πολύ μικρές οθόνες
        if (window.innerWidth < 768) {
            console.log('Parallax disabled: small screen');
            return false;
        }
        
        // Όχι parallax αν ο χρήστης προτιμά μειωμένη κίνηση
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            console.log('Parallax disabled: reduced motion preference');
            return false;
        }
        
        return true;
    }
    
    // Βρες όλα τα parallax elements
    function findParallaxElements() {
        const elements = document.querySelectorAll('.outerimagelarge .imagewrapper');
        console.log('Found parallax elements:', elements.length);
        
        parallaxElements = Array.from(elements).map(element => {
            const container = element.closest('.outerimagelarge');
            return {
                element: element,
                container: container,
                speed: 0.5 // Απλή ταχύτητα
            };
        });
        
        return parallaxElements.length > 0;
    }
    
    // Ενημέρωση parallax effect
    function updateParallax() {
        if (!canUseParallax() || parallaxElements.length === 0) {
            return;
        }
        
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        parallaxElements.forEach(item => {
            const { element, container, speed } = item;
            
            if (!container) return;
            
            const containerRect = container.getBoundingClientRect();
            const containerTop = containerRect.top + scrollTop;
            const containerHeight = container.offsetHeight;
            const windowHeight = window.innerHeight;
            
            // Έλεγχος αν το container είναι visible
            if (containerRect.bottom >= 0 && containerRect.top <= windowHeight) {
                // Υπολογισμός της θέσης του container σε σχέση με το viewport
                const elementTop = containerTop;
                const relativePos = scrollTop - elementTop;
                
                // Απλός υπολογισμός parallax
                const yPos = relativePos * speed;
                
                element.style.transform = `translate3d(0, ${yPos}px, 0)`;
                
                console.log(`Parallax update: scrollTop=${scrollTop}, yPos=${yPos}`); // Debug
            }
        });
        
        isScrolling = false;
    }
    
    // Optimized scroll handler
    function handleScroll() {
        if (!isScrolling) {
            requestAnimationFrame(updateParallax);
            isScrolling = true;
        }
    }
    
    // Reset parallax transforms
    function resetParallax() {
        parallaxElements.forEach(item => {
            item.element.style.transform = 'translate3d(0, 0, 0)';
        });
        console.log('Parallax reset');
    }
    
    // Handle window resize
    function handleResize() {
        console.log('Window resized, checking parallax support...');
        
        if (!canUseParallax()) {
            resetParallax();
            window.removeEventListener('scroll', handleScroll);
            console.log('Parallax disabled after resize');
        } else {
            window.addEventListener('scroll', handleScroll, { passive: true });
            // Re-calculate element positions
            setTimeout(() => {
                findParallaxElements();
                updateParallax();
            }, 100);
            console.log('Parallax re-enabled after resize');
        }
    }
    
    // Initialize parallax
    function initParallax() {
        console.log('Initializing parallax...');
        
        if (!findParallaxElements()) {
            console.log('No parallax elements found');
            return;
        }
        
        if (!canUseParallax()) {
            console.log('Parallax not supported on this device');
            return;
        }
        
        // Add event listeners with iOS-specific handling
        if (isIOS()) {
            console.log('iOS detected - using touchmove for parallax');
            // Για iOS προσθέτουμε και touchmove events
            window.addEventListener('scroll', handleScroll, { passive: true });
            window.addEventListener('touchmove', handleScroll, { passive: true });
            document.body.addEventListener('touchmove', handleScroll, { passive: true });
        } else {
            window.addEventListener('scroll', handleScroll, { passive: true });
        }
        
        window.addEventListener('resize', handleResize);
        
        // Initial update
        setTimeout(updateParallax, 100);
        
        console.log('Parallax initialized successfully');
    }
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initParallax);
    } else {
        initParallax();
    }
    
    // Fallback - try again after window load
    window.addEventListener('load', function() {
        if (parallaxElements.length === 0) {
            console.log('Retrying parallax initialization after window load');
            setTimeout(initParallax, 500);
        }
    });
    
})();