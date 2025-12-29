<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Project.php';

// Initialize database connection
$database = new Database();
$db_connection = $database->getConnection();

// Initialize classes
$category_class = new Category($db_connection);
$service_class = new Product();
$project_class = new Project();

// Get data for homepage
$categories = $category_class->getAllCategories();
$all_services = $service_class->getAll(); // Get all services as products
$featured_products = array_slice(array_filter($all_services, function($s) { return $s['status'] == 'active'; }), 0, 3);
$featured_projects = $project_class->getFeaturedProjects(3);

$page_title = "Professional Cold Storage Solutions | Industrial Refrigeration Equipment | ColdConsult";
$page_description = "Leading provider of industrial cold storage solutions, commercial refrigeration systems, walk-in coolers, and temperature monitoring equipment. Serving pharmaceutical, food & beverage, and logistics industries with 28+ years of expertise.";
$og_image = "https://" . $_SERVER['HTTP_HOST'] . "/images/products/coldstoragehero.jpg";
include __DIR__ . '/../includes/header.php';
?>

<!-- Enhanced Professional Fonts & Performance Styles -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@300;400;500;600;700&display=swap');

:root {
    /* Professional Industrial Fonts */
    --font-primary: 'Inter', 'Red Hat Display', -apple-system, BlinkMacSystemFont, sans-serif;
    --font-display: 'Space Grotesk', 'Inter', sans-serif;
    --font-mono: 'JetBrains Mono', 'Monaco', monospace;
    
    /* Enhanced Color Palette */
    --primary-blue: #1e40af;
    --primary-blue-light: #3b82f6;
    --primary-blue-dark: #1e3a8a;
    --accent-blue: #60a5fa;
    --frost-blue: #dbeafe;
    --ice-white: #f8fafc;
    --steel-gray: #475569;
    --dark-slate: #1e293b;
    
    /* Enhanced Gradients */
    --gradient-hero: linear-gradient(135deg, #0f172a 0%, #1e3a8a 40%, #1e40af 100%);
    --gradient-card: linear-gradient(145deg, rgba(255,255,255,0.9), rgba(248,250,252,0.8));
    --gradient-frost: linear-gradient(135deg, rgba(219,234,254,0.1), rgba(147,197,253,0.05));
    
    /* Performance & Smoothness */
    --transition-ultra-fast: 0.1s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-fast: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-smooth: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-elegant: 0.5s cubic-bezier(0.23, 1, 0.32, 1);
    
    /* Enhanced Shadows */
    --shadow-elevation-1: 0 1px 3px rgba(0,0,0,.12), 0 1px 2px rgba(0,0,0,.24);
    --shadow-elevation-2: 0 3px 6px rgba(0,0,0,.16), 0 3px 6px rgba(0,0,0,.23);
    --shadow-elevation-3: 0 10px 20px rgba(0,0,0,.19), 0 6px 6px rgba(0,0,0,.23);
    --shadow-elevation-4: 0 14px 28px rgba(0,0,0,.25), 0 10px 10px rgba(0,0,0,.22);
    --shadow-professional: 0 25px 50px -12px rgba(30, 64, 175, 0.25);
}

/* Global Performance Optimizations */
* {
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
    scroll-padding-top: 80px;
    font-feature-settings: 'kern' 1, 'liga' 1, 'calt' 1;
    text-rendering: optimizeLegibility;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

body {
    font-family: var(--font-primary);
    font-weight: 400;
    line-height: 1.7;
    color: var(--dark-slate);
    background: var(--ice-white);
}

/* Enhanced Typography */
h1, h2, h3, h4, h5, h6 {
    font-family: var(--font-display);
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: -0.025em;
}

.display-1, .display-2, .display-3, .display-4, .display-5 {
    font-family: var(--font-display);
    font-weight: 800;
    letter-spacing: -0.05em;
}

/* Professional Button Enhancements */
.btn-professional {
    font-family: var(--font-primary);
    font-weight: 600;
    letter-spacing: 0.025em;
    transition: all var(--transition-smooth);
    border-radius: 12px;
    padding: 0.875rem 2rem;
    position: relative;
    overflow: hidden;
}

.btn-professional::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left var(--transition-smooth);
    z-index: 1;
}

.btn-professional:hover::before {
    left: 100%;
}

/* Enhanced Card Components */
.card-professional {
    border: none;
    border-radius: 20px;
    background: var(--gradient-card);
    backdrop-filter: blur(20px);
    box-shadow: var(--shadow-elevation-1);
    transition: all var(--transition-smooth);
    position: relative;
    overflow: hidden;
}

.card-professional:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: var(--shadow-professional);
}

/* Smooth Animations */
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

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

/* Performance Optimizations */
.will-change-transform {
    will-change: transform;
}

.gpu-accelerated {
    transform: translateZ(0);
    backface-visibility: hidden;
    perspective: 1000px;
}

/* Background Pattern Performance Optimizations */
.hero-pattern,
.video-pattern,
.cta-pattern {
    contain: layout style paint;
    will-change: transform;
    transform: translateZ(0);
    backface-visibility: hidden;
}

/* Responsive pattern optimization */
@media (max-width: 768px) {
    .hero-pattern,
    .video-pattern,
    .cta-pattern {
        background-size: 80px 80px !important;
        opacity: 0.05 !important;
    }
}

/* Reduce patterns on lower-end devices */
@media (prefers-reduced-motion: reduce) {
    .hero-pattern,
    .video-pattern,
    .cta-pattern {
        display: none;
    }
}

/* Enhanced Video Styles */
.video-professional {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow-professional);
    position: relative;
    background: var(--gradient-hero);
}

.video-overlay {
    background: linear-gradient(45deg, rgba(30, 64, 175, 0.9), rgba(30, 58, 138, 0.8));
    backdrop-filter: blur(2px);
}
</style>

<main>
    <!-- Berlin-Inspired Cold Storage Hero Section -->
    <section class="hero-section position-relative overflow-hidden" style="
        background: var(--gradient-hero);
        min-height: 100vh;
        display: flex;
        align-items: center;
        position: relative;
        margin-top: -80px;
        padding-top: 80px;
    ">
        <!-- Optimized Background Pattern -->
        <div class="position-absolute hero-pattern" style="
            top: 0; 
            left: 0; 
            width: 100vw; 
            height: 100vh; 
            z-index: 1; 
            opacity: 0.1;
            background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><circle cx=\"50\" cy=\"50\" r=\"20\" fill=\"white\" opacity=\"0.3\"/><path d=\"M50,30 L50,70 M30,50 L70,50\" stroke=\"white\" stroke-width=\"2\" opacity=\"0.5\"/></svg>') repeat;
            background-size: 100px 100px;
            will-change: transform;
            contain: layout style paint;
        "></div>
        
        <div class="container position-relative" style="z-index: 10;">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6" data-aos="fade-right" data-aos-duration="800">
                    <div class="hero-content text-white">
                        <div class="badge bg-white text-primary px-4 py-2 rounded-pill mb-4 d-inline-flex align-items-center shadow-lg" 
                             style="font-size: 0.95rem; font-weight: 600; font-family: var(--font-primary);">
                            <i class="fas fa-snowflake me-2 text-primary"></i>
                            Premium Industrial Solutions
                        </div>
                        
                        <h1 class="hero-title display-1 fw-bold mb-4" style="font-family: var(--font-display); line-height: 0.9; letter-spacing: -0.02em;">
                            <span class="d-block">Professional</span>
                            <span class="position-relative text-white">
                                Cold Storage
                                
                            <span class="d-block text-white" style=" margin-top:2px; font-size: 0.85em; opacity: 0.95;">Solutions</span>
                        </h1>
                        
                        <p class="hero-subtitle fs-5 mb-5 opacity-90" style="max-width: 480px; line-height: 1.65; font-family: var(--font-primary); font-weight: 400;">
                            Advanced refrigeration systems and cold storage equipment designed for industrial-scale operations. 
                            <strong style="color: var(--accent-blue);">Trusted by 500+ businesses globally.</strong>
                        </p>
                        
                        <div class="d-flex gap-3 flex-wrap mb-5">
                            <a href="Products.php" class="btn btn-professional btn-light px-5 py-3 fw-semibold will-change-transform" 
                               style="background: linear-gradient(135deg, #ffffff, #f8fafc); color: var(--primary-blue); border: none; box-shadow: var(--shadow-elevation-2);">
                                <i class="fas fa-cube me-2"></i>View Products
                            </a>
                            
                            <a href="contact.php" class="btn btn-professional btn-outline-light px-5 py-3 fw-semibold will-change-transform" 
                               style="border-width: 2px; border-color: rgba(255,255,255,0.8); color: white;">
                                <i class="fas fa-phone me-2"></i>Get Quote
                            </a>
                        </div>
                        
                        <!-- Enhanced Stats Row -->
                        <div class="row g-4 mt-4">
                            <div class="col-4">
                                <div class="text-center p-3 rounded-3 card-professional" 
                                     style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                                    <div class="fs-1 fw-bold text-white" style="font-family: var(--font-display);">500+</div>
                                    <div class="small opacity-85 fw-medium text-uppercase" style="letter-spacing: 0.5px;">Projects</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-3 rounded-3 card-professional" 
                                     style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                                    <div class="fs-1 fw-bold text-white" style="font-family: var(--font-display);">15+</div>
                                    <div class="small opacity-85 fw-medium text-uppercase" style="letter-spacing: 0.5px;">Years</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-3 rounded-3 card-professional" 
                                     style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                                    <div class="fs-1 fw-bold" style="font-family: var(--font-display); color: var(--accent-blue);">24/7</div>
                                    <div class="small opacity-85 fw-medium text-uppercase" style="letter-spacing: 0.5px;">Support</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 col-md-12 mt-4 mt-lg-0" data-aos="fade-left" data-aos-duration="800" data-aos-delay="300">
                    <div class="hero-image position-relative gpu-accelerated" style="display: block; visibility: visible;">
                        <div class="image-container position-relative" style="display: block; min-height: 300px;">
                            <!-- Main Hero Image with Enhanced Styling -->
                            <div class="position-relative rounded-4 overflow-hidden" 
                                 style="border-radius: 24px !important; box-shadow: 0 25px 50px -12px rgba(30, 64, 175, 0.25);">
                                <img src="images/products/coldstoragehero.jpg" 
                                     alt="Industrial Cold Storage Equipment"
                                     class="img-fluid w-100"
                                     style="height: 400px; width: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;"
                                     loading="eager">
                                
                                <!-- Modern Overlay Gradient -->
                                <div class="position-absolute top-0 start-0 w-100 h-100" 
                                     style="background: linear-gradient(135deg, rgba(30,64,175,0.1) 0%, rgba(30,58,138,0.05) 100%); pointer-events: none;"></div>
                            </div>
                            
                            <!-- Enhanced Floating Cards -->
                            <div class="floating-card position-absolute will-change-transform" 
                                 style="top: 10%; right: -5%; animation: float 6s ease-in-out infinite; animation-delay: 0.5s;">
                                <div class="card-professional text-center p-4 shadow-lg" 
                                     style="min-width: 140px; background: var(--gradient-card); border: 1px solid rgba(255,255,255,0.2);">
                                    <div class="fs-1 fw-bold mb-1" style="color: var(--primary-blue); font-family: var(--font-display);">-40°C</div>
                                    <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Ultra Low Temp</div>
                                </div>
                            </div>
                            
                            <div class="floating-card position-absolute will-change-transform" 
                                 style="bottom: 20%; left: -5%; animation: float 6s ease-in-out infinite; animation-delay: 1.5s;">
                                <div class="card-professional text-center p-4 shadow-lg" 
                                     style="min-width: 140px; background: var(--gradient-card); border: 1px solid rgba(255,255,255,0.2);">
                                    <div class="fs-1 fw-bold mb-1" style="color: var(--primary-blue); font-family: var(--font-display);">99.9%</div>
                                    <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Efficiency</div>
                                </div>
                            </div>
                            
                            <!-- Premium Quality Badge -->
                            <div class="floating-card position-absolute will-change-transform" 
                                 style="top: 45%; left: -8%; animation: float 6s ease-in-out infinite; animation-delay: 2.5s;">
                                <div class="text-white rounded-4 p-4 shadow-lg text-center" 
                                     style="min-width: 160px; background: var(--gradient-hero); border: 1px solid rgba(255,255,255,0.1);">
                                    <div class="fs-5 fw-bold mb-1" style="font-family: var(--font-display);">ISO 9001</div>
                                    <div class="small opacity-90 fw-medium">Certified Quality</div>
                                    <i class="fas fa-award mt-2" style="font-size: 1.2rem; color: var(--accent-blue);"></i>
                                </div>
                            </div>
                            
                            <!-- Industry Badge -->
                            <div class="position-absolute top-0 start-0 m-4">
                                <div class="badge px-4 py-2 rounded-pill shadow-sm" 
                                     style="background: rgba(255,255,255,0.95); color: var(--primary-blue); font-weight: 600; backdrop-filter: blur(10px);">
                                    <i class="fas fa-industry me-2"></i>Industrial Grade
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="bt_bb_section py-5" style="background: #f8fafc;">
        <div class="container">
            <div class="row mb-5 text-center" data-aos="fade-up">
                <div class="col-12">
                    <div class="d-inline-flex align-items-center px-4 py-2 rounded-pill mb-4" 
                         style="background: rgba(37, 99, 235, 0.1); color: #2563eb; font-weight: 500;">
                        <i class="fas fa-cube me-2"></i>
                        Our Products
                    </div>
                    <h2 class="display-5 fw-bold text-dark mb-3" style="font-family: 'Red Hat Display', sans-serif;">
                        Featured Cold Storage Equipment
                    </h2>
                    <p class="fs-5 text-muted col-lg-8 mx-auto">
                        Discover our premium range of industrial cold storage solutions designed for maximum efficiency
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <?php if (!empty($featured_products)): ?>
                    <?php foreach ($featured_products as $index => $product): ?>
                        <?php
                        // Complete image path resolution logic (exact copy from Products.php)
                        $product_id = $product['id'] ?? 0;
                        $final_image = '';
                        $image_found = false;
                        
                        // Method 1: Check database image_path first
                        if (!empty($product['image_path'])) {
                            $db_image_paths = [
                                '../uploads/products/' . $product['image_path'],
                                '../images/products/' . $product['image_path'],
                                'uploads/products/' . $product['image_path'],
                                'images/products/' . $product['image_path'],
                                '../images/products/' . $product_id . '/' . $product['image_path']
                            ];
                            
                            foreach ($db_image_paths as $path) {
                                if (file_exists($path)) {
                                    $final_image = $path;
                                    $image_found = true;
                                    break;
                                }
                            }
                        }
                        
                        // Method 2: If no database image found, scan product folder by ID
                        if (!$image_found && $product_id > 0) {
                            $product_folder = '../images/products/' . $product_id;
                            
                            if (is_dir($product_folder)) {
                                $valid_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
                                $found_images = [];
                                
                                // Get all files in the product folder
                                $folder_contents = glob($product_folder . '/*');
                                
                                foreach ($folder_contents as $file) {
                                    if (is_file($file)) {
                                        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                        if (in_array($extension, $valid_extensions)) {
                                            $found_images[] = $file;
                                        }
                                    }
                                }
                                
                                if (!empty($found_images)) {
                                    // Priority selection: look for main/primary images first
                                    $priority_keywords = ['main', 'primary', 'featured', 'thumb', 'cover'];
                                    $selected_image = null;
                                    
                                    // First, try to find images with priority keywords
                                    foreach ($priority_keywords as $keyword) {
                                        foreach ($found_images as $img) {
                                            $filename = basename($img);
                                            if (stripos($filename, $keyword) !== false) {
                                                $selected_image = $img;
                                                break 2;
                                            }
                                        }
                                    }
                                    
                                    // If no priority image found, use the first one
                                    if (!$selected_image) {
                                        $selected_image = $found_images[0];
                                    }
                                    
                                    $final_image = $selected_image;
                                    $image_found = true;
                                }
                            }
                        }
                        
                        // Method 3: Alternative path structures (legacy support)
                        if (!$image_found && $product_id > 0) {
                            $alternative_paths = [
                                '../public/images/products/' . $product_id,
                                './images/products/' . $product_id,
                                'images/products/' . $product_id
                            ];
                            
                            foreach ($alternative_paths as $alt_folder) {
                                if (is_dir($alt_folder)) {
                                    $alt_images = glob($alt_folder . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
                                    if (!empty($alt_images)) {
                                        $final_image = $alt_images[0];
                                        $image_found = true;
                                        break;
                                    }
                                }
                            }
                        }
                        
                        // Method 4: Default fallback images
                        if (!$image_found) {
                            $default_images = [
                                '../images/products/man-working-as-pharmacist (1).jpg',
                                '../public/images/products/plant-picture-clean-room-equipment-stainless-steel-machines.jpg',
                                'images/products/plant-picture-clean-room-equipment-stainless-steel-machines.jpg'
                            ];
                            
                            foreach ($default_images as $default) {
                                if (file_exists($default)) {
                                    $final_image = $default;
                                    $image_found = true;
                                    break;
                                }
                            }
                        }
                        
                        // Final fallback - use a data URL for a placeholder
                        if (!$image_found) {
                            $final_image = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIwIiBoZWlnaHQ9IjIyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGJlYWZlIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxOCIgZmlsbD0iIzNiODJmNiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkNvbGQgU3RvcmFnZTwvdGV4dD48L3N2Zz4=';
                        }
                        ?>
                        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                            <div class="product-card bg-white rounded-4 shadow-sm h-100 border-0" 
                                 style="transition: all 0.3s ease; border-radius: 20px !important;">
                                <div class="position-relative overflow-hidden" style="border-radius: 20px 20px 0 0;">
                                    <a href="Products.php" tabindex="0" aria-label="View all products">
                                        <img src="<?php echo htmlspecialchars($final_image); ?>" 
                                             alt="<?php echo htmlspecialchars($product['title']); ?>"
                                             class="w-100"
                                             style="height: 250px; object-fit: cover; transition: transform 0.3s ease;"
                                             onerror="handleProductImageError(this, <?php echo $product_id; ?>)"
                                             data-product-id="<?php echo $product_id; ?>">
                                    </a>
                                    <div class="position-absolute top-0 start-0 m-3">
                                        <span class="badge px-3 py-2" style="background: #2563eb; border-radius: 20px;">
                                            <?php echo htmlspecialchars($product['category_name'] ?? 'Product'); ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Fallback icon (hidden by default) -->
                                    <div class="fallback-placeholder" style="display: none;">
                                        <i class="fas fa-snowflake"></i>
                                        <span>Cold Storage<br>Equipment</span>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h5 class="fw-bold mb-3 text-dark" style="font-family: 'Red Hat Display', sans-serif;">
                                        <?php echo htmlspecialchars($product['title']); ?>
                                    </h5>
                                    <p class="text-muted mb-4 lh-lg">
                                        <?php echo htmlspecialchars(substr($product['description'], 0, 120)) . '...'; ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="Products.php" class="btn btn-primary" style="background: #2563eb; border: none; border-radius: 8px;">
                                            View All Products
                                        </a>
                                        <a href="contact.php?service=<?php echo $product['id']; ?>" 
                                           class="btn btn-outline-primary" style="color: #2563eb; border-color: #2563eb; border-radius: 8px;">
                                            Get Quote
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Default Products Display -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up">
                        <div class="product-card bg-white rounded-4 shadow-sm h-100 border-0" style="border-radius: 20px !important;">
                            <div class="position-relative overflow-hidden" style="border-radius: 20px 20px 0 0;">
                                <img src="images/products/man-working-as-pharmacist (1).jpg" alt="Industrial Freezers" class="w-100" style="height: 250px; object-fit: cover;">
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge px-3 py-2" style="background: #2563eb; border-radius: 20px;">Freezers</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h5 class="fw-bold mb-3 text-dark">Industrial Freezers</h5>
                                <p class="text-muted mb-4">High-performance freezing systems for food processing and storage applications.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="Products.php" class="btn btn-primary" style="background: #2563eb; border: none;">View Details</a>
                                    <a href="contact.php" class="btn btn-outline-primary" style="color: #2563eb; border-color: #2563eb;">Get Quote</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="product-card bg-white rounded-4 shadow-sm h-100 border-0" style="border-radius: 20px !important;">
                            <div class="position-relative overflow-hidden" style="border-radius: 20px 20px 0 0;">
                                <img src="images/products/metal-wine-storage-tanks-with-dwelling-houses-background-winery.jpg" alt="Cold Storage Rooms" class="w-100" style="height: 250px; object-fit: cover;">
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge px-3 py-2" style="background: #2563eb; border-radius: 20px;">Storage</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h5 class="fw-bold mb-3 text-dark">Cold Storage Rooms</h5>
                                <p class="text-muted mb-4">Custom-built cold storage facilities with precise temperature control systems.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="Products.php" class="btn btn-primary" style="background: #2563eb; border: none;">View Details</a>
                                    <a href="contact.php" class="btn btn-outline-primary" style="color: #2563eb; border-color: #2563eb;">Get Quote</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="product-card bg-white rounded-4 shadow-sm h-100 border-0" style="border-radius: 20px !important;">
                            <div class="position-relative overflow-hidden" style="border-radius: 20px 20px 0 0;">
                                <img src="images/products/plant-picture-clean-room-equipment-stainless-steel-machines.jpg" alt="Refrigeration Systems" class="w-100" style="height: 250px; object-fit: cover;">
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge px-3 py-2" style="background: #2563eb; border-radius: 20px;">Systems</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h5 class="fw-bold mb-3 text-dark">Refrigeration Systems</h5>
                                <p class="text-muted mb-4">Advanced refrigeration technology for commercial and industrial applications.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="Products.php" class="btn btn-primary" style="background: #2563eb; border: none;">View Details</a>
                                    <a href="contact.php" class="btn btn-outline-primary" style="color: #2563eb; border-color: #2563eb;">Get Quote</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="400">
                <a href="Products.php" class="btn btn-primary btn-lg px-5 py-3" 
                   style="background: #2563eb; border: none; border-radius: 10px; font-weight: 600;">
                    View All Products <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="bt_bb_section py-5" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
        <div class="container">
            <div class="row mb-5 text-center" data-aos="fade-up">
                <div class="col-12">
                    <div class="d-inline-flex align-items-center px-4 py-2 rounded-pill mb-4" 
                         style="background: rgba(255, 255, 255, 0.2); color: white; font-weight: 500;">
                        <i class="fas fa-award me-2"></i>
                        Why Choose Us
                    </div>
                    <h2 class="display-5 fw-bold text-white mb-3" style="font-family: 'Red Hat Display', sans-serif;">
                        Leading Cold Storage Solutions
                    </h2>
                    <p class="fs-5 text-white opacity-90 col-lg-8 mx-auto">
                        Trust our expertise and cutting-edge technology for all your cold storage needs
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card text-center p-4 h-100" style="background: rgba(255, 255, 255, 0.1); border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.2);">
                        <div class="feature-icon mb-4">
                            <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-thermometer-half text-primary fs-2" style="color: #2563eb !important;"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-3 text-white">Temperature Control</h4>
                        <p class="text-white opacity-90">
                            Precise temperature management systems with advanced monitoring and alerts for optimal storage conditions.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card text-center p-4 h-100" style="background: rgba(255, 255, 255, 0.1); border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.2);">
                        <div class="feature-icon mb-4">
                            <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-leaf text-primary fs-2" style="color: #2563eb !important;"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-3 text-white">Energy Efficient</h4>
                        <p class="text-white opacity-90">
                            Eco-friendly refrigeration systems designed to minimize energy consumption while maximizing performance.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card text-center p-4 h-100" style="background: rgba(255, 255, 255, 0.1); border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.2);">
                        <div class="feature-icon mb-4">
                            <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-tools text-primary fs-2" style="color: #2563eb !important;"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-3 text-white">24/7 Support</h4>
                        <p class="text-white opacity-90">
                            Round-the-clock technical support and maintenance services to ensure uninterrupted operations.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Video Section -->
    <section class="py-5 position-relative overflow-hidden" style="background: var(--gradient-hero);">
        <!-- Optimized Minimal Background Pattern -->
        <div class="position-absolute video-pattern" style="
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            z-index: 1; 
            opacity: 0.03;
            background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 60 60\"><rect width=\"60\" height=\"60\" fill=\"none\" stroke=\"white\" stroke-width=\"0.5\" opacity=\"0.2\"/></svg>') repeat;
            background-size: 60px 60px;
            contain: layout style paint;
        "></div>
        
        <div class="container position-relative" style="z-index: 10;">
            <div class="row align-items-center">
                <!-- Streamlined Content - Less Text -->
                <div class="col-lg-5 mb-4 mb-lg-0" data-aos="fade-right">
                    <div class="video-content text-white">
                        <span class="badge bg-white text-primary px-4 py-2 rounded-pill mb-4 d-inline-flex align-items-center" 
                              style="font-weight: 600; font-size: 0.9rem;">
                            <i class="fas fa-play me-2"></i>
                            Watch Our Technology
                        </span>
                        
                        <h2 class="display-5 fw-bold mb-4" style="color:white font-family: var(--font-display); line-height: 1.1;">
                            Industrial Cold Storage
                            <span class="gradient-text d-block" style="background: linear-gradient(135deg, #60a5fa, #93c5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                In Action
                            </span>
                        </h2>
                        
                        <p class="fs-5 mb-5 opacity-90" style="max-width: 420px; line-height: 1.6;">
                            Experience how our advanced refrigeration systems deliver precision, efficiency, and reliability.
                        </p>
                        
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="projects.php" class="btn btn-professional btn-light px-5 py-3 fw-semibold">
                                <i class="fas fa-briefcase me-2"></i>View Projects
                            </a>
                            <a href="contact.php" class="btn btn-professional btn-outline-light px-5 py-3 fw-semibold">
                                <i class="fas fa-phone me-2"></i>Get Quote
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Enhanced Video Player -->
                <div class="col-lg-7" data-aos="fade-left" data-aos-delay="200">
                    <div class="video-container position-relative">
                        <!-- Brand Video Player -->
                        <div class="video-professional gpu-accelerated position-relative" 
                             style="aspect-ratio: 16/9; border-radius: 20px; overflow: hidden;">
                            
                            <video 
                                class="w-100 h-100" 
                                autoplay 
                                muted 
                                loop 
                                playsinline
                                controls
                                preload="metadata"
                                style="border-radius: 20px; object-fit: cover;">
                                <source src="images/LandingVideo.mp4" type="video/mp4">
                                <!-- Fallback message for browsers that don't support video -->
                                <p class="text-white text-center p-4">
                                    Your browser doesn't support video playback. 
                                    <a href="images/LandingVideo.mp4" class="text-primary">Download the video</a> to view it.
                                </p>
                            </video>
                            
                            <!-- Professional Overlay Elements -->
                            <div class="position-absolute top-0 end-0 m-4" style="z-index: 5;">
                                <div class="badge bg-success px-3 py-2 rounded-pill shadow-lg" style="backdrop-filter: blur(10px);">
                                    <i class="fas fa-award me-1"></i>
                                    Industry Leading
                                </div>
                            </div>
                        </div>
                        
                        <!-- Minimal Stats Below Video -->
                        <div class="row g-3 mt-4">
                            <div class="col-4">
                                <div class="text-center p-3 rounded-4 card-professional" 
                                     style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.1);">
                                    <div class="fs-3 fw-bold text-white">500+</div>
                                    <small class="text-white opacity-85 fw-medium">Projects</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-3 rounded-4 card-professional" 
                                     style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.1);">
                                    <div class="fs-3 fw-bold text-white">99.9%</div>
                                    <small class="text-white opacity-85 fw-medium">Uptime</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-3 rounded-4 card-professional" 
                                     style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.1);">
                                    <div class="fs-3 fw-bold text-white">24/7</div>
                                    <small class="text-white opacity-85 fw-medium">Support</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Categories Section -->
    <section class="py-5" style="background: var(--ice-white);">
        <div class="container">
            <div class="row mb-5 text-center" data-aos="fade-up">
                <div class="col-12">
                    <span class="badge px-4 py-2 rounded-pill mb-4 d-inline-flex align-items-center" 
                          style="background: var(--frost-blue); color: var(--primary-blue); font-weight: 600; font-family: var(--font-primary);">
                        <i class="fas fa-cube me-2"></i>
                        Our Solutions
                    </span>
                    <h2 class="display-5 fw-bold mb-4" style="font-family: var(--font-display); color: var(--dark-slate);">
                        Industrial Cold Storage
                        <span class="d-block" style="color: var(--primary-blue);">Equipment Range</span>
                    </h2>
                    <p class="fs-5 col-lg-7 mx-auto" style="color: var(--steel-gray); line-height: 1.6;">
                        Professional-grade refrigeration systems engineered for maximum efficiency and reliability
                    </p>
                </div>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Dynamic Category Cards -->
                <?php if (!empty($categories)): ?>
                    <?php
                    // Define icons for different category types
                    $category_icons = [
                        'freezer' => 'fas fa-snowflake',
                        'cold storage' => 'fas fa-warehouse',
                        'commercial' => 'fas fa-building',
                        'monitoring' => 'fas fa-chart-line',
                        'automation' => 'fas fa-cogs',
                        'refrigeration' => 'fas fa-thermometer-half',
                        'walk-in' => 'fas fa-door-open',
                        'blast' => 'fas fa-wind',
                        'pharmaceutical' => 'fas fa-pills',
                        'food' => 'fas fa-utensils'
                    ];
                    
                    // Limit to first 6 categories for homepage display
                    $display_categories = array_slice($categories, 0, 3);
                    
                    foreach ($display_categories as $index => $category):
                        // Determine icon based on category name
                        $icon = 'fas fa-snowflake'; // default icon
                        foreach ($category_icons as $keyword => $cat_icon) {
                            if (stripos($category['name'], $keyword) !== false) {
                                $icon = $cat_icon;
                                break;
                            }
                        }
                        
                        // Generate gradient colors
                        $gradients = [
                            'background: var(--gradient-hero);',
                            'background: linear-gradient(135deg, var(--primary-blue-light), var(--primary-blue));',
                            'background: linear-gradient(135deg, var(--accent-blue), var(--primary-blue-light));',
                            'background: linear-gradient(135deg, var(--primary-blue), var(--accent-blue));',
                            'background: linear-gradient(135deg, #1e40af, #3b82f6);',
                            'background: linear-gradient(135deg, #60a5fa, #1e40af);'
                        ];
                        $gradient = $gradients[$index % count($gradients)];
                    ?>
                        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($index + 1) * 100; ?>">
                            <div class="card-professional text-center p-5 h-100 will-change-transform" 
                                 style="border: 1px solid rgba(30,64,175,0.1);">
                                <div class="category-icon mb-4">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" 
                                         style="width: 90px; height: 90px; <?php echo $gradient; ?> box-shadow: var(--shadow-elevation-2);">
                                        <i class="<?php echo $icon; ?> text-white" style="font-size: 2.2rem;"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold mb-3" style="color: var(--dark-slate); font-family: var(--font-display);">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </h4>
                                <p class="mb-4" style="color: var(--steel-gray); line-height: 1.6;">
                                    <?php 
                                    if (!empty($category['description'])) {
                                        echo htmlspecialchars(substr($category['description'], 0, 80)) . '...';
                                    } else {
                                        echo 'Professional ' . strtolower($category['name']) . ' solutions for your business needs';
                                    }
                                    ?>
                                </p>
                                <a href="Products.php?category=<?php echo $category['id']; ?>" class="btn btn-professional btn-outline-primary px-4 py-2" 
                                   style="color: var(--primary-blue); border-color: var(--primary-blue); font-weight: 600;">
                                    Explore Range
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback static categories if no data available -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="card-professional text-center p-5 h-100 will-change-transform" 
                             style="border: 1px solid rgba(30,64,175,0.1);">
                            <div class="category-icon mb-4">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle" 
                                     style="width: 90px; height: 90px; background: var(--gradient-hero); box-shadow: var(--shadow-elevation-2);">
                                    <i class="fas fa-snowflake text-white" style="font-size: 2.2rem;"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold mb-3" style="color: var(--dark-slate); font-family: var(--font-display);">Industrial Freezers</h4>
                            <p class="mb-4" style="color: var(--steel-gray); line-height: 1.6;">
                                High-capacity freezing systems for commercial and industrial applications
                            </p>
                            <a href="Products.php" class="btn btn-professional btn-outline-primary px-4 py-2" 
                               style="color: var(--primary-blue); border-color: var(--primary-blue); font-weight: 600;">
                                Explore Range
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="card-professional text-center p-5 h-100 will-change-transform" 
                             style="border: 1px solid rgba(30,64,175,0.1);">
                            <div class="category-icon mb-4">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle" 
                                     style="width: 90px; height: 90px; background: linear-gradient(135deg, var(--primary-blue-light), var(--primary-blue)); box-shadow: var(--shadow-elevation-2);">
                                    <i class="fas fa-warehouse text-white" style="font-size: 2.2rem;"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold mb-3" style="color: var(--dark-slate); font-family: var(--font-display);">Cold Storage Rooms</h4>
                            <p class="mb-4" style="color: var(--steel-gray); line-height: 1.6;">
                                Custom-designed facilities with precise temperature and humidity control
                            </p>
                            <a href="Products.php" class="btn btn-professional btn-outline-primary px-4 py-2" 
                               style="color: var(--primary-blue); border-color: var(--primary-blue); font-weight: 600;">
                                Explore Range
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="card-professional text-center p-5 h-100 will-change-transform" 
                             style="border: 1px solid rgba(30,64,175,0.1);">
                            <div class="category-icon mb-4">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle" 
                                     style="width: 90px; height: 90px; background: linear-gradient(135deg, var(--accent-blue), var(--primary-blue-light)); box-shadow: var(--shadow-elevation-2);">
                                    <i class="fas fa-cogs text-white" style="font-size: 2.2rem;"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold mb-3" style="color: var(--dark-slate); font-family: var(--font-display);">Automation Systems</h4>
                            <p class="mb-4" style="color: var(--steel-gray); line-height: 1.6;">
                                Smart HVAC and climate control systems for optimal efficiency
                            </p>
                            <a href="Products.php" class="btn btn-professional btn-outline-primary px-4 py-2" 
                               style="color: var(--primary-blue); border-color: var(--primary-blue); font-weight: 600;">
                                Explore Range
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="400">
                <a href="Products.php" class="btn btn-professional px-5 py-3" 
                   style="background: var(--gradient-hero); border: none; color: white; font-weight: 600; box-shadow: var(--shadow-elevation-3);">
                    Explore All Solutions <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Enhanced CTA Section -->
    <section class="py-5 position-relative overflow-hidden" style="background: var(--gradient-hero);">
        <!-- Optimized Background Pattern -->
        <div class="position-absolute cta-pattern" style="
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            z-index: 1; 
            opacity: 0.05;
            background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 40 40\"><circle cx=\"20\" cy=\"20\" r=\"1\" fill=\"white\" opacity=\"0.3\"/></svg>') repeat;
            background-size: 40px 40px;
            contain: layout style paint;
        "></div>
        
        <div class="container position-relative" style="z-index: 10;">
            <div class="row align-items-center" data-aos="fade-up">
                <div class="col-lg-8 text-center text-lg-start">
                    <h3 class="fw-bold mb-3 text-white" style="font-family: var(--font-display); font-size: 2.5rem;">
                        Ready to Optimize Your 
                        <span style="color: var(--accent-blue);">Cold Storage?</span>
                    </h3>
                    <p class="text-white opacity-90 mb-4 fs-5" style="max-width: 600px; line-height: 1.6;">
                        Get expert consultation and custom solutions engineered for your specific requirements. 
                        <strong style="color: var(--accent-blue);">Free assessment available.</strong>
                    </p>
                </div>
                <div class="col-lg-4 text-center text-lg-end">
                    <div class="d-flex flex-column flex-lg-row gap-3 justify-content-center justify-content-lg-end">
                        <a href="contact.php" class="btn btn-professional btn-light px-5 py-3" 
                           style="color: var(--primary-blue); font-weight: 600; box-shadow: var(--shadow-elevation-2);">
                            <i class="fas fa-phone me-2"></i>Get Free Quote
                        </a>
                        <a href="about.php" class="btn btn-professional btn-outline-light px-5 py-3" 
                           style="border-color: rgba(255,255,255,0.8); color: white; font-weight: 600;">
                            <i class="fas fa-info-circle me-2"></i>Learn More
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="row g-4 mt-4 text-center">
                <div class="col-lg-3 col-6">
                    <div class="text-white">
                        <div class="fs-2 fw-bold" style="font-family: var(--font-display); color: var(--accent-blue);">500+</div>
                        <small class="opacity-85">Projects Delivered</small>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="text-white">
                        <div class="fs-2 fw-bold" style="font-family: var(--font-display); color: var(--accent-blue);">15+</div>
                        <small class="opacity-85">Years Experience</small>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="text-white">
                        <div class="fs-2 fw-bold" style="font-family: var(--font-display); color: var(--accent-blue);">99.9%</div>
                        <small class="opacity-85">System Uptime</small>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="text-white">
                        <div class="fs-2 fw-bold" style="font-family: var(--font-display); color: var(--accent-blue);">24/7</div>
                        <small class="opacity-85">Expert Support</small>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Enhanced Performance & UX JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Performance optimizations
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    
    // Enhanced smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: prefersReducedMotion.matches ? 'auto' : 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Enhanced hover effects for cards
    const cards = document.querySelectorAll('.card-professional, .btn-professional');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            if (!prefersReducedMotion.matches) {
                this.style.transform = 'translateY(-4px) scale(1.02)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '-10% 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe elements for fade-in animations
    document.querySelectorAll('[data-aos]').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        observer.observe(el);
    });
    
    // Enhanced floating animation performance
    const floatingElements = document.querySelectorAll('.floating-card');
    floatingElements.forEach((el, index) => {
        el.style.animationDelay = `${index * 0.5}s`;
        el.style.willChange = 'transform';
    });
    
    // Loading optimization
    window.addEventListener('load', function() {
        document.body.classList.add('loaded');
        
        // Remove will-change after animations complete
        setTimeout(() => {
            floatingElements.forEach(el => {
                el.style.willChange = 'auto';
            });
        }, 3000);
    });
    
    // Professional touch interactions for mobile
    if ('ontouchstart' in window) {
        cards.forEach(card => {
            card.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });
            
            card.addEventListener('touchend', function() {
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 100);
            });
        });
    }
});

// Enhanced video loading optimization
if ('IntersectionObserver' in window) {
    const videoObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const iframe = entry.target.querySelector('iframe');
                if (iframe && !iframe.src.includes('autoplay=1')) {
                    // Video will autoplay when visible
                }
            }
        });
    });
    
    const videoSection = document.querySelector('.video-professional');
    if (videoSection) {
        videoObserver.observe(videoSection.closest('section'));
    }
}
</script>
<style>
/* Hero Section Styles */
.hero-section {
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100vw;
    height: 100vh;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="white" opacity="0.1"/></svg>') repeat;
    background-size: 100px 100px;
    z-index: 2;
    contain: layout style paint;
    will-change: transform;
}

/* Floating Animation */
@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-10px) rotate(1deg); }
    66% { transform: translateY(5px) rotate(-1deg); }
}

.floating-card {
    animation: float 6s ease-in-out infinite;
}

.floating-card:nth-child(2) {
    animation-delay: 2s;
}

.floating-card:nth-child(3) {
    animation-delay: 4s;
}

/* Product Cards */
.product-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0, 0, 0, 0.1);
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px -12px rgba(37, 99, 235, 0.25);
    border-color: var(--primary-blue);
}

.product-card:hover img {
    transform: scale(1.05);
}

.product-card:hover .position-absolute {
    opacity: 1;
}

/* Category Cards */
.category-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0, 0, 0, 0.1);
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px -12px rgba(37, 99, 235, 0.2);
    background: var(--white) !important;
    border-color: var(--primary-blue);
}

/* Feature Cards */
.feature-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.feature-card:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.2) !important;
}

/* Button Styles */
.btn-primary {
    background: var(--primary-blue);
    border-color: var(--primary-blue);
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: var(--secondary-blue);
    border-color: var(--secondary-blue);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
}

.btn-outline-primary {
    color: var(--primary-blue);
    border-color: var(--primary-blue);
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: var(--primary-blue);
    border-color: var(--primary-blue);
    color: white;
    transform: translateY(-2px);
}

/* Fix for outline button hover text visibility */
.btn-professional.btn-outline-light:hover {
    background: white !important;
    border-color: white !important;
    color: var(--primary-blue) !important;
}

.btn-professional.btn-outline-light:hover i {
    color: var(--primary-blue) !important;
}

/* Fix for btn-outline-primary hover text visibility */
.btn-outline-primary:hover {
    background: white !important;
    border-color: var(--primary-blue) !important;
    color: var(--primary-blue) !important;
    transform: translateY(-2px);
}

.btn-outline-primary:hover i {
    color: var(--primary-blue) !important;
}

/* Badge Styles */
.badge {
    font-weight: 500;
    font-size: 0.875rem;
}

/* Shadow Utilities */
.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Desktop and Laptop Styles - Ensure Image Visibility */
@media (min-width: 992px) {
    .hero-image {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    .hero-image img {
        height: 450px !important;
        width: 100% !important;
        object-fit: cover !important;
        display: block !important;
    }
    
    .image-container {
        display: block !important;
        position: relative !important;
    }
    
    .col-lg-6 {
        display: block !important;
    }
}

/* Tablet Styles */
@media (min-width: 768px) and (max-width: 991.98px) {
    .hero-image {
        margin-top: 2rem;
        display: block !important;
    }
    
    .hero-image img {
        height: 350px !important;
    }
    
    .floating-card {
        display: none !important;
    }
}

/* Mobile Responsive Design */
@media (max-width: 767.98px) {
    .hero-section {
        min-height: 80vh;
        padding-top: 80px !important;
    }
    
    .display-1, .display-2 {
        font-size: 2.2rem !important;
    }
    
    .hero-image {
        margin-top: 2rem;
        display: block !important;
    }
    
    .hero-image img {
        height: 280px !important;
    }
    
    .floating-card {
        display: none !important;
    }
    
    .col-4 {
        text-align: center;
        margin-bottom: 1rem;
    }
    
    .btn-professional {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}

/* AOS Animation Enhancements */
[data-aos] {
    pointer-events: none;
}

[data-aos].aos-animate {
    pointer-events: auto;
}

/* Custom scrollbar for webkit browsers */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
    background: var(--primary-blue);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--secondary-blue);
}

/* Loading state for images */
img {
    transition: opacity 0.3s ease;
}

img[loading="lazy"] {
    opacity: 0;
}

img[loading="lazy"].loaded {
    opacity: 1;
}

/* Video Section Styles */
.video-section {
    position: relative;
    overflow: hidden;
}

.video-placeholder {
    transition: all 0.3s ease;
    position: relative;
}

.video-placeholder:hover {
    transform: scale(1.02);
}

.video-play-btn {
    transition: all 0.3s ease;
    cursor: pointer;
}

.video-play-btn:hover {
    transform: scale(1.1) !important;
    box-shadow: 0 15px 35px rgba(37, 99, 235, 0.4) !important;
}

.video-wrapper {
    transition: all 0.3s ease;
}

.video-wrapper:hover {
    transform: translateY(-5px);
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2) !important;
}

.video-stats .col-4:hover > div {
    background: rgba(255, 255, 255, 0.2) !important;
    transform: translateY(-2px);
}

.video-stats .col-4 > div {
    transition: all 0.3s ease;
    cursor: pointer;
}

/* Video responsive adjustments */
@media (max-width: 768px) {
    .video-content {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .video-content .row .col-6 {
        margin-bottom: 1rem;
    }
    
    .video-content .d-flex {
        justify-content: center;
        text-align: left;
    }
    
    .video-play-btn {
        width: 60px !important;
        height: 60px !important;
    }
    
    .video-play-btn i {
        font-size: 1.4rem !important;
    }
    
    .video-stats {
        margin-top: 2rem !important;
    }
    
    .video-stats .fs-4 {
        font-size: 1.5rem !important;
    }
}

@media (max-width: 576px) {
    .video-content .row .col-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .video-placeholder {
        margin-bottom: 1rem;
    }
    
    .video-stats .col-4 {
        flex: 0 0 100%;
        max-width: 100%;
        margin-bottom: 0.5rem;
    }
}

/* Animation for video loading */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.video-loading .spinner-border {
    animation: pulse 1.5s infinite;
}

/* Floating badges animation */
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.position-absolute .badge {
    animation: float 3s ease-in-out infinite;
}

.position-absolute .badge:nth-child(2) {
    animation-delay: 1.5s;
}
</style>

<!-- Additional Scripts -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    // Initialize AOS (Animate On Scroll)
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        offset: 100
    });
    
    // Add loading class to images when they load
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('img[loading="lazy"]');
        images.forEach(img => {
            img.addEventListener('load', function() {
                this.classList.add('loaded');
            });
        });
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Video player functionality
    function playVideo(element) {
        const videoWrapper = element.closest('.video-wrapper');
        const placeholder = element;
        const video = videoWrapper.querySelector('video');
        const loading = placeholder.querySelector('.video-loading');
        const playBtn = placeholder.querySelector('.video-play-btn');
        
        // Show loading state
        if (loading && playBtn) {
            playBtn.classList.add('d-none');
            loading.classList.remove('d-none');
        }
        
        // Simulate loading delay (remove this in production with real video)
        setTimeout(() => {
            // Hide placeholder and show video
            placeholder.classList.add('d-none');
            video.classList.remove('d-none');
            
            // Try to play video
            if (video.src || video.querySelector('source')) {
                video.play().catch(e => {
                    // Handle autoplay prevention gracefully
                });
            } else {
                // Fallback: Show message or handle gracefully
            }
        }, 800);
    }
    
    // Video hover effects
    document.addEventListener('DOMContentLoaded', function() {
        const videoPlaceholder = document.querySelector('.video-placeholder');
        const playBtn = document.querySelector('.video-play-btn');
        
        if (videoPlaceholder && playBtn) {
            videoPlaceholder.addEventListener('mouseenter', function() {
                playBtn.style.transform = 'scale(1.1)';
                playBtn.style.boxShadow = '0 10px 30px rgba(37, 99, 235, 0.4)';
            });
            
            videoPlaceholder.addEventListener('mouseleave', function() {
                playBtn.style.transform = 'scale(1)';
                playBtn.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.1)';
            });
        }
    });
</script>

<?php include '../includes/footer.php'; ?>
