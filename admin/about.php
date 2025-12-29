<?php
require_once '../config/config.php';
$page_title = 'About ColdConsult | Professional Cold Storage Solutions';
$page_description = 'Learn about ColdStorage Solutions - industry leader in cold storage and refrigeration since 1995. Serving pharmaceutical, food & beverage, chemical, and logistics industries with innovative temperature-controlled storage solutions and 24/7 support.';
$og_image = "https://" . $_SERVER['HTTP_HOST'] . "/images/products/man-working-as-pharmacist.jpg";
include '../includes/header.php';
?>

    <style>
    /* Import Professional Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');
    
    :root {
        --cold-blue: #1e3a8a;
        --arctic-blue: #3b82f6;
        --ice-white: #f8fafc;
        --frost-gray: #e2e8f0;
        --steel-gray: #475569;
    }
    
    /* Professional Typography */
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        font-optical-sizing: auto;
    }
    
    h1, h2, h3, h4, h5, h6 {
        font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        font-weight: 600;
        letter-spacing: -0.025em;
    }
    
    .display-1, .display-2, .display-3, .display-4, .display-5, .display-6 {
        font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        font-weight: 700;
        letter-spacing: -0.05em;
    }
    
    code, pre, .font-monospace {
        font-family: 'JetBrains Mono', 'Consolas', monospace;
    }    .reveal {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.6s ease;
    }
    
    .reveal.active {
        opacity: 1;
        transform: translateY(0);
    }
    
    .gradient-text {
        background: linear-gradient(135deg, var(--arctic-blue), var(--cold-blue));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .temperature-indicator {
        background: linear-gradient(135deg, var(--arctic-blue), var(--cold-blue));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 25px rgba(30, 58, 138, 0.25);
    }
    
    .ice-card {
        background: rgba(248, 250, 252, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(226, 232, 240, 0.5);
        border-radius: 16px;
        padding: 2rem;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .ice-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(30, 58, 138, 0.1);
    }
    
    .frost-glass {
        background: rgba(30, 58, 138, 0.85);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(59, 130, 246, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    .cold-stat {
        text-align: center;
        padding: 2rem 1rem;
        background: rgba(248, 250, 252, 0.9);
        backdrop-filter: blur(5px);
        border-radius: 12px;
        transition: all 0.3s ease;
        border: 1px solid rgba(226, 232, 240, 0.5);
        height: 100%;
    }
    
    .cold-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(30, 58, 138, 0.1);
    }
    
    .hero-about {
        background: linear-gradient(135deg, var(--cold-blue) 0%, var(--arctic-blue) 100%);
        min-height: 60vh;
        display: flex;
        align-items: center;
    }
    
    /* Centered Timeline with Connecting Line */
    .timeline-container {
        position: relative;
    }
    
    .timeline-item-center {
        position: relative;
        z-index: 2;
    }
    
    .timeline-connecting-line {
        animation: timeline-grow 2s ease-in-out forwards;
        transform-origin: top;
        transform: scaleY(0);
    }
    
    @keyframes timeline-grow {
        to {
            transform: scaleY(1);
        }
    }
    
    /* Enhanced spacing for centered timeline */
    .timeline-item-center .ice-card {
        margin-top: 20px;
        background: rgba(248, 250, 252, 0.95);
        backdrop-filter: blur(15px);
    }
    
    @media (max-width: 768px) {
        .hero-about {
            min-height: 50vh;
        }
        
        .ice-card {
            padding: 1.5rem;
        }
        
        .display-3, .display-4 {
            font-size: 2.5rem;
        }
        
        /* Mobile Timeline Adjustments */
        .timeline-connecting-line {
            display: none !important;
        }
        
        /* Reduce timeline item max-width on mobile */
        .timeline-item-center .ice-card {
            max-width: 100% !important;
        }
        
        /* Adjust spacing for mobile */
        .frost-glass {
            padding: 2rem !important;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero-about">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-3 fw-bold text-white mb-4 reveal">
                    About <span class="text-warning">ColdTech Solutions</span>
                </h1>
                <p class="lead text-white-50 mb-5 col-lg-10 mx-auto reveal">
                    Industry leaders in advanced cold storage technology, automation, and temperature-critical solutions. 
                    Trusted by Fortune 100 companies worldwide for over 28 years.
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center reveal">
                    <div class="frost-glass px-4 py-2 rounded-pill text-white">
                        <i class="fas fa-award me-2"></i>ISO 9001 Certified
                    </div>
                    <div class="frost-glass px-4 py-2 rounded-pill text-white">
                        <i class="fas fa-globe me-2"></i>10+ Countries
                    </div>
                    <div class="frost-glass px-4 py-2 rounded-pill text-white">
                        <i class="fas fa-users me-2"></i>500+ Projects
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Company Stats -->
<section class="py-5" style="background: linear-gradient(135deg, var(--frost-gray) 0%, var(--ice-white) 100%);">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="cold-stat reveal">
                    <div class="temperature-indicator mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-calendar-alt text-white"></i>
                    </div>
                    <h3 class="fw-bold mb-2" style="color: var(--cold-blue);">28+ Years</h3>
                    <p class="text-muted mb-0">Industry Experience</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="cold-stat reveal">
                    <div class="temperature-indicator mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-project-diagram text-white"></i>
                    </div>
                    <h3 class="fw-bold mb-2" style="color: var(--cold-blue);">500+</h3>
                    <p class="text-muted mb-0">Completed Projects</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="cold-stat reveal">
                    <div class="temperature-indicator mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-globe-americas text-white"></i>
                    </div>
                    <h3 class="fw-bold mb-2" style="color: var(--cold-blue);">25+</h3>
                    <p class="text-muted mb-0">Countries Served</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="cold-stat reveal">
                    <div class="temperature-indicator mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <h3 class="fw-bold mb-2" style="color: var(--cold-blue);">ISO 9001</h3>
                    <p class="text-muted mb-0">Quality Certified</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Story Section -->
<section class="py-5" style="background: linear-gradient(135deg, var(--ice-white) 0%, #ffffff 50%, var(--frost-gray) 100%);">
    <div class="container">
        <!-- Section Header -->
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5 reveal">
                <h2 class="display-4 fw-bold mb-4" style="color: var(--cold-blue);">
                    From Vision to <span class="gradient-text">Industry Leadership</span>
                </h2>
                <p class="lead" style="color: var(--steel-gray);">
                    Three decades of innovation in temperature-critical solutions
                </p>
            </div>
        </div>
        
        <!-- Story Timeline -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="position-relative">
                    <!-- Connecting Line between cards -->
                    <div class="timeline-connecting-line" style="position: absolute; left: 50%; transform: translateX(-50%); top: 80px; bottom: 80px; width: 3px; background: linear-gradient(180deg, rgba(30, 58, 138, 0.3), rgba(59, 130, 246, 0.2)); border-radius: 1.5px; z-index: 1; opacity: 0.6;"></div>
                    
                    <!-- Timeline Items -->
                    <div class="timeline-container">
                        <!-- 1995 - Foundation -->
                        <div class="timeline-item-center reveal mb-5">
                            <div class="d-flex justify-content-center">
                                <div class="ice-card p-4 text-center" style="max-width: 500px; position: relative;">
                                    <div class="d-flex align-items-center justify-content-center mb-3">
                                        <div class="temperature-indicator me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, #ef4444, #dc2626);">
                                            <i class="fas fa-rocket text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-0" style="color: var(--cold-blue);">1995</h5>
                                            <small class="text-muted">The Beginning</small>
                                        </div>
                                    </div>
                                    <p class="mb-0" style="color: var(--steel-gray);">
                                        Started as a small refrigeration repair shop with a big dream: to revolutionize how businesses handle temperature-critical operations.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 2005 - First Innovation -->
                        <div class="timeline-item-center reveal mb-5">
                            <div class="d-flex justify-content-center">
                                <div class="ice-card p-4 text-center" style="max-width: 500px; position: relative;">
                                    <div class="d-flex align-items-center justify-content-center mb-3">
                                        <div class="temperature-indicator me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, #f59e0b, #d97706);">
                                            <i class="fas fa-lightbulb text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-0" style="color: var(--cold-blue);">2005</h5>
                                            <small class="text-muted">Innovation Breakthrough</small>
                                        </div>
                                    </div>
                                    <p class="mb-0" style="color: var(--steel-gray);">
                                        Developed our first automated temperature monitoring system, setting new industry standards for precision and reliability.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 2015 - Global Expansion -->
                        <div class="timeline-item-center reveal mb-5">
                            <div class="d-flex justify-content-center">
                                <div class="ice-card p-4 text-center" style="max-width: 500px; position: relative;">
                                    <div class="d-flex align-items-center justify-content-center mb-3">
                                        <div class="temperature-indicator me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, #10b981, #059669);">
                                            <i class="fas fa-globe text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-0" style="color: var(--cold-blue);">2015</h5>
                                            <small class="text-muted">Global Reach</small>
                                        </div>
                                    </div>
                                    <p class="mb-0" style="color: var(--steel-gray);">
                                        Expanded internationally, bringing our expertise to pharmaceutical and food processing giants across 10+ countries.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 2020 - AI Revolution -->
                        <div class="timeline-item-center reveal mb-5">
                            <div class="d-flex justify-content-center">
                                <div class="ice-card p-4 text-center" style="max-width: 500px; position: relative;">
                                    <div class="d-flex align-items-center justify-content-center mb-3">
                                        <div class="temperature-indicator me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                                            <i class="fas fa-brain text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-0" style="color: var(--cold-blue);">2020</h5>
                                            <small class="text-muted">AI Integration</small>
                                        </div>
                                    </div>
                                    <p class="mb-0" style="color: var(--steel-gray);">
                                        Launched AI-powered predictive maintenance systems, reducing equipment failures by 85% and energy consumption by 30%.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 2025 - Today -->
                        <div class="timeline-item-center reveal mb-5">
                            <div class="d-flex justify-content-center">
                                <div class="ice-card p-4 text-center" style="max-width: 500px; position: relative;">
                                    <div class="d-flex align-items-center justify-content-center mb-3">
                                        <div class="temperature-indicator me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--arctic-blue), var(--cold-blue));">
                                            <i class="fas fa-crown text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-0" style="color: var(--cold-blue);">Today</h5>
                                            <small class="text-muted">Industry Leader</small>
                                        </div>
                                    </div>
                                    <p class="mb-0" style="color: var(--steel-gray);">
                                        Leading the cold storage revolution with IoT, AI, and sustainable solutions. Trusted by Fortune 500 companies worldwide.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mission Statement -->
        <div class="row mt-5 justify-content-center">
            <div class="col-lg-8 mx-auto text-center reveal">
                <div class="frost-glass p-5 rounded-4">
                    <h3 class="fw-bold mb-4 text-white">Our Mission</h3>
                    <p class="lead mb-4 text-white-50">
                        "To preserve what matters most through innovative temperature control solutions that protect lives, ensure quality, and drive sustainable operations across industries."
                    </p>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="temperature-indicator mx-auto mb-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, #06b6d4, #0891b2);">
                                    <i class="fas fa-shield-alt text-white"></i>
                                </div>
                                <h6 class="fw-bold text-white">Reliability</h6>
                                <small class="text-white-50">99.9% uptime guarantee</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="temperature-indicator mx-auto mb-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, #10b981, #059669);">
                                    <i class="fas fa-leaf text-white"></i>
                                </div>
                                <h6 class="fw-bold text-white">Sustainability</h6>
                                <small class="text-white-50">30% energy reduction</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="temperature-indicator mx-auto mb-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                                    <i class="fas fa-rocket text-white"></i>
                                </div>
                                <h6 class="fw-bold text-white">Innovation</h6>
                                <small class="text-white-50">Cutting-edge AI technology</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Expertise Section -->
<section class="py-5" style="background: linear-gradient(135deg, var(--frost-gray) 0%, var(--ice-white) 100%);">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <h2 class="display-5 fw-bold mb-4" style="color: var(--cold-blue);">
                Our <span class="gradient-text">Expertise</span>
            </h2>
            <p class="lead col-lg-8 mx-auto" style="color: var(--steel-gray);">
                Comprehensive solutions for every aspect of cold storage operations
            </p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="ice-card reveal">
                    <div class="temperature-indicator mx-auto mb-4" style="width: 60px; height: 60px;">
                        <i class="fas fa-thermometer-three-quarters text-white"></i>
                    </div>
                    <h5 class="fw-bold mb-3" style="color: var(--cold-blue);">Temperature Control</h5>
                    <p class="text-muted mb-4">
                        Precision refrigeration systems maintaining temperatures from -30°C to +15°C with ±0.5°C accuracy.
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Multi-zone control</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Energy optimization</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Alarm systems</li>
                    </ul>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="ice-card reveal">
                    <div class="temperature-indicator mx-auto mb-4" style="width: 60px; height: 60px;">
                        <i class="fas fa-eye text-white"></i>
                    </div>
                    <h5 class="fw-bold mb-3" style="color: var(--cold-blue);">Remote Monitoring</h5>
                    <p class="text-muted mb-4">
                        24/7 real-time monitoring with cloud-based dashboards and instant alerts.
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Mobile app access</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Historical data</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Predictive analytics</li>
                    </ul>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="ice-card reveal">
                    <div class="temperature-indicator mx-auto mb-4" style="width: 60px; height: 60px;">
                        <i class="fas fa-cogs text-white"></i>
                    </div>
                    <h5 class="fw-bold mb-3" style="color: var(--cold-blue);">Automation Solutions</h5>
                    <p class="text-muted mb-4">
                        Automated inventory management, robotic storage systems, and intelligent workflow optimization.
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>RFID tracking</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Robotic handling</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>WMS integration</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Industries Served -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <h2 class="display-5 fw-bold mb-4" style="color: var(--cold-blue);">
                Industries We <span class="gradient-text">Serve</span>
            </h2>
            <p class="lead col-lg-8 mx-auto" style="color: var(--steel-gray);">
                Trusted by leading companies across temperature-sensitive industries
            </p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="ice-card text-center reveal">
                    <div class="temperature-indicator mx-auto mb-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, #4CAF50, #8BC34A);">
                        <i class="fas fa-seedling text-white"></i>
                    </div>
                    <h6 class="fw-bold mb-2" style="color: var(--cold-blue);">Food & Beverage</h6>
                    <p class="text-muted small">Fresh produce, dairy, frozen foods, and beverage storage solutions</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="ice-card text-center reveal">
                    <div class="temperature-indicator mx-auto mb-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, #2196F3, #03A9F4);">
                        <i class="fas fa-pills text-white"></i>
                    </div>
                    <h6 class="fw-bold mb-2" style="color: var(--cold-blue);">Pharmaceutical</h6>
                    <p class="text-muted small">Vaccines, medicines, and temperature-critical drug storage</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="ice-card text-center reveal">
                    <div class="temperature-indicator mx-auto mb-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, #FF9800, #FF5722);">
                        <i class="fas fa-flask text-white"></i>
                    </div>
                    <h6 class="fw-bold mb-2" style="color: var(--cold-blue);">Chemical</h6>
                    <p class="text-muted small">Temperature-sensitive chemicals and industrial compounds</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="ice-card text-center reveal">
                    <div class="temperature-indicator mx-auto mb-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, #9C27B0, #E91E63);">
                        <i class="fas fa-shipping-fast text-white"></i>
                    </div>
                    <h6 class="fw-bold mb-2" style="color: var(--cold-blue);">Logistics</h6>
                    <p class="text-muted small">Cold chain distribution and transportation hubs</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA Section -->
<section class="py-5" style="background: linear-gradient(135deg, var(--cold-blue) 0%, var(--arctic-blue) 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center reveal">
                <h2 class="fw-bold text-white mb-4">Ready to Transform Your Cold Storage?</h2>
                <p class="lead text-white-50 mb-4">
                    Let's discuss how our expertise can optimize your temperature-controlled operations.
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <a href="contact.php" class="btn btn-warning btn-lg px-5 py-3 rounded-pill">
                        <i class="fas fa-phone me-2"></i>Get Free Consultation
                    </a>
                    <a href="Products.php" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill">
                        <i class="fas fa-eye me-2"></i>View Our Products
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reveal animation on scroll
    const revealElements = document.querySelectorAll('.reveal');
    
    const revealOnScroll = () => {
        revealElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            if (elementTop < windowHeight - 100) {
                element.classList.add('active');
            }
        });
    };
    
    window.addEventListener('scroll', revealOnScroll);
    revealOnScroll(); // Initial check
    
    // Smooth hover effects for cards
    const cards = document.querySelectorAll('.ice-card, .cold-stat');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>