<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$page_title = 'Cold Storage Projects | Refrigeration Installations | ColdConsult';
$page_description = "Explore our comprehensive range of cold storage categories including industrial freezers, commercial refrigeration, walk-in coolers, and temperature monitoring systems.";
$og_image = "https://" . $_SERVER['HTTP_HOST'] . "/images/products/metal-wine-storage-tanks-with-dwelling-houses-background-winery.jpg";
include __DIR__ . '/../includes/header.php';
?>

<style>
/* Projects Page - Interactive Timeline & Masonry Layout */
.projects-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e40af 50%, #2563eb 100%);
    color: white;
    padding: 120px 0 80px;
    position: relative;
    overflow: hidden;
}

.projects-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="circuit" width="40" height="40" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="2" fill="rgba(255,255,255,0.1)"/><line x1="10" y1="10" x2="30" y2="10" stroke="rgba(255,255,255,0.1)" stroke-width="1"/><line x1="30" y1="10" x2="30" y2="30" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23circuit)"/></svg>');
    opacity: 0.4;
}

.projects-hero .container {
    position: relative;
    z-index: 2;
}

/* Interactive Stats with Animation */
.hero-stats {
    display: flex;
    justify-content: space-around;
    margin-top: 4rem;
    flex-wrap: wrap;
    gap: 2rem;
}

.stat-bubble {
    position: relative;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: all 0.5s ease;
    cursor: pointer;
}

.stat-bubble:hover {
    transform: scale(1.1) rotate(5deg);
    background: rgba(255, 255, 255, 0.2);
    box-shadow: 0 0 30px rgba(96, 165, 250, 0.5);
}

.stat-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: #60a5fa;
}

.stat-label {
    font-size: 0.8rem;
    text-align: center;
    margin-top: 0.3rem;
}

/* Unique Tab Navigation */
.filter-navigation {
    margin: 4rem 0;
    position: relative;
}

.nav-slider {
    display: flex;
    overflow-x: auto;
    scroll-behavior: smooth;
    padding: 1rem 0;
    gap: 1rem;
    justify-content: center;
}

.nav-item {
    position: relative;
    min-width: 160px;
    height: 80px;
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,250,252,0.9) 100%);
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
    backdrop-filter: blur(10px);
}

.nav-item:hover {
    transform: translateY(-10px) scale(1.05);
    box-shadow: 0 20px 40px rgba(37, 99, 235, 0.2);
    border-color: #60a5fa;
}

.nav-item.active {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: white;
    transform: translateY(-5px);
}

.nav-icon {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.nav-text {
    font-size: 0.9rem;
    font-weight: 600;
}

/* Timeline Layout for Featured Projects */
.timeline-container {
    position: relative;
    margin: 4rem 0;
}

.timeline-line {
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(to bottom, #60a5fa, #2563eb, #1d4ed8);
    transform: translateX(-50%);
    z-index: 1;
}

.timeline-item {
    display: flex;
    margin-bottom: 4rem;
    position: relative;
    z-index: 2;
}

.timeline-item:nth-child(even) {
    flex-direction: row-reverse;
}

.timeline-content {
    width: 45%;
    padding: 2rem;
    background: white;
    border-radius: 25px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    position: relative;
    transition: all 0.4s ease;
}

.timeline-content:hover {
    transform: scale(1.02);
    box-shadow: 0 25px 50px rgba(37, 99, 235, 0.15);
}

.timeline-content::before {
    content: '';
    position: absolute;
    top: 30px;
    width: 0;
    height: 0;
    border: 15px solid transparent;
}

.timeline-item:nth-child(odd) .timeline-content::before {
    right: -30px;
    border-left-color: white;
}

.timeline-item:nth-child(even) .timeline-content::before {
    left: -30px;
    border-right-color: white;
}

.timeline-dot {
    position: absolute;
    left: 50%;
    top: 30px;
    width: 20px;
    height: 20px;
    background: linear-gradient(135deg, #f97316, #ea580c);
    border-radius: 50%;
    transform: translateX(-50%);
    z-index: 3;
    border: 4px solid white;
    box-shadow: 0 0 20px rgba(249, 115, 22, 0.5);
}

.timeline-dot.featured {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
}

/* Masonry Layout for All Projects */
.masonry-container {
    columns: 3;
    column-gap: 2rem;
    margin-top: 3rem;
}

.masonry-item {
    break-inside: avoid;
    margin-bottom: 2rem;
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    transition: all 0.4s ease;
    position: relative;
}

.masonry-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(37, 99, 235, 0.15);
}

.masonry-image {
    width: 100%;
    height: auto;
    min-height: 200px;
    background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
    position: relative;
    overflow: hidden;
}

.masonry-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.masonry-item:hover .masonry-image img {
    transform: scale(1.1);
}

.masonry-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(37, 99, 235, 0.8), rgba(29, 78, 216, 0.6));
    opacity: 0;
    transition: all 0.4s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    font-weight: 600;
}

.masonry-item:hover .masonry-overlay {
    opacity: 1;
}

.masonry-content {
    padding: 1.5rem;
}

.masonry-category {
    background: linear-gradient(135deg, #60a5fa, #3b82f6);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 1rem;
}

.masonry-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.8rem;
    line-height: 1.3;
}

.masonry-description {
    color: #64748b;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.masonry-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: #94a3b8;
    border-top: 1px solid #e2e8f0;
    padding-top: 1rem;
}

/* Floating Action Elements */
.floating-stats {
    position: fixed;
    right: 2rem;
    top: 50%;
    transform: translateY(-50%);
    z-index: 100;
}

.floating-stat {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    text-align: center;
    min-width: 80px;
    transition: all 0.3s ease;
}

.floating-stat:hover {
    transform: scale(1.1);
    background: rgba(37, 99, 235, 0.9);
    color: white;
}

.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 1rem;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #60a5fa, #2563eb);
    border-radius: 2px;
}

.section-subtitle {
    font-size: 1.2rem;
    color: #64748b;
    max-width: 600px;
    margin: 0 auto;
}

@media (max-width: 1024px) {
    .masonry-container {
        columns: 2;
    }
    
    .timeline-line {
        left: 30px;
    }
    
    .timeline-item,
    .timeline-item:nth-child(even) {
        flex-direction: row;
    }
    
    .timeline-content {
        width: calc(100% - 80px);
        margin-left: 60px;
    }
    
    .timeline-dot {
        left: 30px;
    }
    
    .floating-stats {
        display: none;
    }
}

@media (max-width: 768px) {
    .masonry-container {
        columns: 1;
    }
    
    .hero-stats {
        justify-content: center;
        gap: 1rem;
    }
    
    .stat-bubble {
        width: 100px;
        height: 100px;
    }
    
    .nav-slider {
        justify-content: flex-start;
    }
    
    .timeline-content {
        padding: 1.5rem;
    }
}
</style>

<main>
    <!-- Hero Section -->
    <section class="projects-hero">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4" data-aos="fade-up">Cold Storage Categories</h1>
                    <p class="lead mb-0" data-aos="fade-up" data-aos-delay="100">
                        Explore our comprehensive range of cold storage solutions across different industries
                    </p>
                </div>
            </div>
            
            <div class="hero-stats" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-bubble">
                    <span class="stat-number">15+</span>
                    <span class="stat-label">Categories</span>
                </div>
                <div class="stat-bubble">
                    <span class="stat-number">28+</span>
                    <span class="stat-label">Years Experience</span>
                </div>
                <div class="stat-bubble">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">Satisfaction</span>
                </div>
                <div class="stat-bubble">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Support</span>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// AOS Animation Library
AOS.init({
    duration: 800,
    easing: 'ease-out-cubic',
    once: true,
    offset: 100
});

// Interactive Navigation
document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.nav-item');
    const masonryItems = document.querySelectorAll('.masonry-item');
    
    navItems.forEach(nav => {
        nav.addEventListener('click', function() {
            // Remove active class from all nav items
            navItems.forEach(n => n.classList.remove('active'));
            // Add active class to clicked nav
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            // Filter masonry items with animation
            masonryItems.forEach((item, index) => {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.style.display = 'block';
                    item.style.animation = `fadeInUp 0.6s ease forwards ${index * 0.1}s`;
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    
    // Animated counter for floating stats
    function animateCounter(element, target) {
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target + (target < 100 ? '+' : '%');
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current) + (target < 100 ? '+' : '%');
            }
        }, 50);
    }
    
    // Trigger counter animation on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counters = entry.target.querySelectorAll('.stat-number');
                counters.forEach(counter => {
                    const target = parseInt(counter.textContent);
                    animateCounter(counter, target);
                });
                observer.unobserve(entry.target);
            }
        });
    });
    
    const statsSection = document.querySelector('.hero-stats');
    if (statsSection) {
        observer.observe(statsSection);
    }
    
    // Parallax effect for timeline
    window.addEventListener('scroll', () => {
        const timelineLine = document.querySelector('.timeline-line');
        if (timelineLine) {
            const scrolled = window.pageYOffset;
            const parallax = scrolled * 0.1;
            timelineLine.style.transform = `translateX(-50%) translateY(${parallax}px)`;
        }
    });
    
    // Hover effects for masonry items
    masonryItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});

// Add custom animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    
    .stat-bubble:hover {
        animation: pulse 2s infinite;
    }
    
    @keyframes slideInFromLeft {
        from {
            opacity: 0;
            transform: translateX(-100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideInFromRight {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .timeline-item:nth-child(odd) .timeline-content {
        animation: slideInFromLeft 0.6s ease forwards;
    }
    
    .timeline-item:nth-child(even) .timeline-content {
        animation: slideInFromRight 0.6s ease forwards;
    }
`;
document.head.appendChild(style);
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>