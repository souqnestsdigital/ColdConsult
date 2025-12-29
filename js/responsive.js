/**
 * ColdStorage Responsive JavaScript
 * Modern responsive behavior and interactions
 * Version: 1.0
 * Author: ColdStorage Team
 */

class ResponsiveManager {
    constructor() {
        this.init();
        this.bindEvents();
        this.setupBreakpointWatchers();
    }

    init() {
        this.breakpoints = {
            xs: 480,
            sm: 576,
            md: 768,
            lg: 992,
            xl: 1200,
            xxl: 1400
        };

        this.currentBreakpoint = this.getCurrentBreakpoint();
        this.previousBreakpoint = this.currentBreakpoint;
        
        // Debounce resize handler
        this.resizeTimeout = null;
        this.scrollTimeout = null;
        
        // Performance tracking
        this.performanceMode = this.detectPerformanceMode();
    }

    bindEvents() {
        // Optimized resize handler
        window.addEventListener('resize', this.debounce(() => {
            this.handleResize();
        }, 250));

        // Optimized scroll handler for navbar
        window.addEventListener('scroll', this.throttle(() => {
            this.handleScroll();
        }, 16)); // 60fps

        // Orientation change handler
        window.addEventListener('orientationchange', () => {
            setTimeout(() => {
                this.handleOrientationChange();
            }, 100);
        });

        // Touch device detection
        this.setupTouchHandlers();
        
        // Intersection observer for animations
        this.setupIntersectionObserver();
    }

    getCurrentBreakpoint() {
        const width = window.innerWidth;
        
        if (width < this.breakpoints.xs) return 'xs';
        if (width < this.breakpoints.sm) return 'sm';
        if (width < this.breakpoints.md) return 'md';
        if (width < this.breakpoints.lg) return 'lg';
        if (width < this.breakpoints.xl) return 'xl';
        return 'xxl';
    }

    handleResize() {
        this.previousBreakpoint = this.currentBreakpoint;
        this.currentBreakpoint = this.getCurrentBreakpoint();

        // Trigger breakpoint change event if needed
        if (this.previousBreakpoint !== this.currentBreakpoint) {
            this.onBreakpointChange();
        }

        // Update navbar
        this.updateNavbarResponsive();
        
        // Update cards layout
        this.updateCardsLayout();
        
        // Update hero section
        this.updateHeroSection();

        // Dispatch custom event
        window.dispatchEvent(new CustomEvent('responsiveResize', {
            detail: {
                breakpoint: this.currentBreakpoint,
                previousBreakpoint: this.previousBreakpoint,
                width: window.innerWidth,
                height: window.innerHeight
            }
        }));
    }

    handleScroll() {
        const navbar = document.querySelector('.navbar');
        if (!navbar) return;

        const scrollY = window.scrollY;
        const scrollThreshold = 50;

        if (scrollY > scrollThreshold) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }

        // Add scroll direction class
        if (this.lastScrollY < scrollY) {
            navbar.classList.add('scroll-down');
            navbar.classList.remove('scroll-up');
        } else {
            navbar.classList.add('scroll-up');
            navbar.classList.remove('scroll-down');
        }
        
        this.lastScrollY = scrollY;
    }

    handleOrientationChange() {
        // Force re-calculation after orientation change
        setTimeout(() => {
            this.handleResize();
            this.updateViewportHeight();
        }, 300);
    }

    onBreakpointChange() {
        console.log(`Breakpoint changed from ${this.previousBreakpoint} to ${this.currentBreakpoint}`);
        
        // Update navigation behavior
        this.updateNavigationBehavior();
        
        // Update animations based on device
        this.updateAnimations();
        
        // Update touch interactions
        this.updateTouchInteractions();
    }

    updateNavbarResponsive() {
        const navbar = document.querySelector('.navbar');
        const navbarCollapse = document.querySelector('.navbar-collapse');
        
        if (!navbar) return;

        // Mobile navbar behavior
        if (this.currentBreakpoint === 'xs' || this.currentBreakpoint === 'sm') {
            navbar.classList.add('navbar-mobile');
            
            // Auto-close mobile menu on link click
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                        const toggler = document.querySelector('.navbar-toggler');
                        if (toggler) toggler.click();
                    }
                });
            });
        } else {
            navbar.classList.remove('navbar-mobile');
        }
    }

    updateCardsLayout() {
        const cards = document.querySelectorAll('.product-card, .feature-card, .project-card');
        
        cards.forEach(card => {
            if (this.currentBreakpoint === 'xs' || this.currentBreakpoint === 'sm') {
                card.classList.add('card-mobile');
            } else {
                card.classList.remove('card-mobile');
            }
        });
    }

    updateHeroSection() {
        const heroSection = document.querySelector('.hero-section');
        if (!heroSection) return;

        // Adjust hero height based on device
        if (this.currentBreakpoint === 'xs') {
            heroSection.style.minHeight = '70vh';
        } else if (this.currentBreakpoint === 'sm') {
            heroSection.style.minHeight = '75vh';
        } else if (this.currentBreakpoint === 'md') {
            heroSection.style.minHeight = '80vh';
        } else {
            heroSection.style.minHeight = '100vh';
        }
    }

    updateNavigationBehavior() {
        const navbar = document.querySelector('.navbar');
        if (!navbar) return;

        // Add smooth scroll padding adjustment
        const navbarHeight = navbar.offsetHeight;
        document.documentElement.style.setProperty('--navbar-height', `${navbarHeight}px`);
        document.documentElement.style.scrollPaddingTop = `${navbarHeight + 20}px`;
    }

    updateAnimations() {
        const shouldReduceAnimations = 
            this.performanceMode === 'low' || 
            this.currentBreakpoint === 'xs' ||
            window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        document.documentElement.classList.toggle('reduce-animations', shouldReduceAnimations);
    }

    updateTouchInteractions() {
        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        
        if (isTouchDevice) {
            document.body.classList.add('touch-device');
            
            // Add touch-friendly hover alternatives
            this.setupTouchHovers();
        } else {
            document.body.classList.add('no-touch-device');
        }
    }

    setupTouchHovers() {
        const hoverElements = document.querySelectorAll('.card, .btn, .nav-link');
        
        hoverElements.forEach(element => {
            element.addEventListener('touchstart', () => {
                element.classList.add('touch-active');
            });
            
            element.addEventListener('touchend', () => {
                setTimeout(() => {
                    element.classList.remove('touch-active');
                }, 300);
            });
        });
    }

    setupTouchHandlers() {
        // Prevent zoom on double tap for better UX
        let lastTouchEnd = 0;
        document.addEventListener('touchend', (e) => {
            const now = Date.now();
            if (now - lastTouchEnd <= 300) {
                if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                }
            }
            lastTouchEnd = now;
        }, { passive: false });

        // Handle swipe gestures for mobile navigation
        this.setupSwipeNavigation();
    }

    setupSwipeNavigation() {
        let startX = 0;
        let startY = 0;
        
        document.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        }, { passive: true });

        document.addEventListener('touchmove', (e) => {
            if (!startX || !startY) return;

            const diffX = startX - e.touches[0].clientX;
            const diffY = startY - e.touches[0].clientY;

            // Horizontal swipe detection
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
                // Could add swipe navigation here if needed
            }
        }, { passive: true });
    }

    setupIntersectionObserver() {
        if (!('IntersectionObserver' in window)) return;

        const options = {
            root: null,
            rootMargin: '0px 0px -10% 0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    
                    // Lazy load images if needed
                    this.lazyLoadImages(entry.target);
                }
            });
        }, options);

        // Observe elements that should animate on scroll
        const animateElements = document.querySelectorAll('[data-aos], .feature-card, .product-card, .project-card');
        animateElements.forEach(el => observer.observe(el));
    }

    lazyLoadImages(container) {
        const images = container.querySelectorAll('img[data-src]');
        images.forEach(img => {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
            img.classList.add('loaded');
        });
    }

    setupBreakpointWatchers() {
        // CSS breakpoint detection
        Object.keys(this.breakpoints).forEach(bp => {
            const mediaQuery = window.matchMedia(`(min-width: ${this.breakpoints[bp]}px)`);
            mediaQuery.addEventListener('change', () => {
                this.handleResize();
            });
        });
    }

    updateViewportHeight() {
        // Fix viewport height issues on mobile
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }

    detectPerformanceMode() {
        // Simple performance detection
        const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        
        if (connection) {
            if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
                return 'low';
            }
            if (connection.effectiveType === '3g') {
                return 'medium';
            }
        }
        
        // CPU core detection
        if (navigator.hardwareConcurrency && navigator.hardwareConcurrency <= 2) {
            return 'low';
        }
        
        return 'high';
    }

    // Utility functions
    debounce(func, wait) {
        return (...args) => {
            clearTimeout(this.resizeTimeout);
            this.resizeTimeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    throttle(func, limit) {
        let inThrottle;
        return (...args) => {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Public API methods
    getBreakpoint() {
        return this.currentBreakpoint;
    }

    isMobile() {
        return this.currentBreakpoint === 'xs' || this.currentBreakpoint === 'sm';
    }

    isTablet() {
        return this.currentBreakpoint === 'md';
    }

    isDesktop() {
        return this.currentBreakpoint === 'lg' || this.currentBreakpoint === 'xl' || this.currentBreakpoint === 'xxl';
    }

    // Smooth scroll with offset
    smoothScrollTo(target, offset = 0) {
        const element = typeof target === 'string' ? document.querySelector(target) : target;
        if (!element) return;

        const navbarHeight = document.querySelector('.navbar')?.offsetHeight || 0;
        const elementPosition = element.getBoundingClientRect().top + window.pageYOffset;
        const offsetPosition = elementPosition - navbarHeight - offset;

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
}

// Enhanced form handling for responsive
class ResponsiveFormHandler {
    constructor() {
        this.setupFormValidation();
        this.setupInputEnhancements();
    }

    setupFormValidation() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });
        });
    }

    setupInputEnhancements() {
        // Floating labels
        const inputs = document.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', () => {
                if (!input.value) {
                    input.parentElement.classList.remove('focused');
                }
            });
        });
    }

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                this.showError(input, 'This field is required');
                isValid = false;
            } else {
                this.clearError(input);
            }
        });
        
        return isValid;
    }

    showError(input, message) {
        input.classList.add('error');
        let errorElement = input.parentElement.querySelector('.error-message');
        
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            input.parentElement.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
    }

    clearError(input) {
        input.classList.remove('error');
        const errorElement = input.parentElement.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();
        }
    }
}

// Initialize responsive system
document.addEventListener('DOMContentLoaded', () => {
    // Initialize responsive manager
    window.responsiveManager = new ResponsiveManager();
    
    // Initialize form handler
    window.formHandler = new ResponsiveFormHandler();
    
    // Set initial viewport height
    window.responsiveManager.updateViewportHeight();
    
    // Add loaded class to body for CSS animations
    document.body.classList.add('responsive-loaded');
    
    console.log('ColdStorage Responsive System initialized');
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ResponsiveManager, ResponsiveFormHandler };
}