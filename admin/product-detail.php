<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';

$page_title = 'Product Details | ColdConsult Equipment';
$page_description = 'Detailed specifications and technical information for professional cold storage products, refrigeration systems, and temperature monitoring equipment. Compare features, pricing, and performance data.';
$og_image = "https://" . $_SERVER['HTTP_HOST'] . "/images/products/young-man-working-warehouse-with-boxes.jpg";

// Get product ID from URL
$product_id = $_GET['id'] ?? null;

// Initialize database connection and classes
$product_details = null;
$related_products = [];
$categories = [];

try {
    $database = new Database();
    $db = $database->getConnection();
    $product = new Product();
    $category = new Category($db);
    
    // Get product details
    if ($product_id) {
        $product_details = $product->getById($product_id);
    }
    
    // Get related products from the same category
    if ($product_details && $product_details['category_id']) {
        $all_products = $product->getAll();
        $related_products = array_filter($all_products, function($p) use ($product_details, $product_id) {
            return $p['category_id'] == $product_details['category_id'] && 
                   $p['id'] != $product_id && 
                   $p['status'] == 'active';
        });
        $related_products = array_slice($related_products, 0, 3); // Limit to 3
    }
    
    // Get all categories for navigation
    $categories = $category->getActiveCategories();
    
} catch (Exception $e) {
    error_log("Database error in product-detail.php: " . $e->getMessage());
}

// Fallback product data based on ID
$fallback_products = [
    1 => [
        'id' => 1,
        'title' => 'CryoMax Industrial Freezer CF-5000',
        'category_name' => 'Industrial Freezers',
        'price' => 45999,
        'description' => 'High-capacity industrial freezer designed for pharmaceutical and food storage with precision temperature control and advanced monitoring systems.',
        'features' => "±0.1°C Temperature Precision\nSmart IoT Integration\nEmergency Backup System\nFDA Compliant\nDigital Temperature Display\nAuto-Defrost Function\nLockable Security\nEnergy Efficient Design",
        'image_path' => '',
        'status' => 'active',
        'category_id' => 1
    ],
    2 => [
        'id' => 2,
        'title' => 'TempGuard Pro Monitoring System',
        'category_name' => 'Monitoring Systems',
        'price' => 12499,
        'description' => 'Advanced wireless temperature monitoring system with cloud connectivity, real-time alerts, and comprehensive data logging capabilities.',
        'features' => "Cloud-based Dashboard\nMobile App Alerts\nHistorical Data Analytics\nCompliance Reporting\nReal-time Monitoring\nWireless Connectivity\nLong Battery Life\nScalable System",
        'image_path' => '',
        'status' => 'active',
        'category_id' => 3
    ],
    3 => [
        'id' => 3,
        'title' => 'ChillPro Commercial Refrigerator CR-3000',
        'category_name' => 'Commercial Refrigeration',
        'price' => 8999,
        'description' => 'Energy-efficient commercial refrigerator perfect for restaurants, laboratories, and medical facilities requiring consistent temperature control.',
        'features' => "Digital Temperature Display\nAuto-Defrost Function\nMultiple Shelving Options\nLockable Security\nLED Interior Lighting\nGlass Door Option\nForced Air Circulation\nEasy Maintenance",
        'image_path' => '',
        'status' => 'active',
        'category_id' => 2
    ]
];

// Use fallback data if database product not found
if (!$product_details && isset($fallback_products[$product_id])) {
    $product_details = $fallback_products[$product_id];
} elseif (!$product_details) {
    $product_details = $fallback_products[1]; // Default to first product
}

// If no product found or invalid ID, redirect to products page
if (!$product_details || $product_details['status'] !== 'active') {
    header('Location: Products.php');
    exit;
}

// Process features for display
$features_array = [];
if (!empty($product_details['features'])) {
    $features_text = $product_details['features'];
    $features_array = array_filter(array_map('trim', explode("\n", $features_text)));
}

// Set page title and description based on product
$page_title = htmlspecialchars($product_details['title']) . ' - Cold Storage Solutions | ColdTech';
$page_description = 'Learn more about ' . htmlspecialchars($product_details['title']) . '. ' . 
                   htmlspecialchars(substr($product_details['description'], 0, 150)) . '...';

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
        --temperature-orange: #f97316;
        --success-green: #10b981;
        --warning-amber: #f59e0b;
    }
    
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        font-optical-sizing: auto;
    }
    
    h1, h2, h3, h4, h5, h6 {
        font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        font-weight: 600;
        letter-spacing: -0.025em;
    }
    
    /* Breadcrumb */
    .breadcrumb-section {
        background: var(--frost-gray);
        padding: 1rem 0;
    }
    
    .breadcrumb {
        background: transparent;
        margin: 0;
        padding: 0;
    }
    
    .breadcrumb-item a {
        color: var(--arctic-blue);
        text-decoration: none;
    }
    
    .breadcrumb-item.active {
        color: var(--steel-gray);
    }
    
    /* Product Detail Hero */
    .product-hero {
        padding: 3rem 0;
        background: linear-gradient(135deg, var(--ice-white) 0%, white 100%);
    }
    
    .product-image-container {
        position: relative;
        background: var(--frost-gray);
        border-radius: 20px;
        height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(30, 58, 138, 0.1);
    }
    
    .product-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .product-placeholder {
        font-size: 6rem;
        color: var(--arctic-blue);
        opacity: 0.7;
    }
    
    .product-badge-large {
        position: absolute;
        top: 2rem;
        right: 2rem;
        background: var(--temperature-orange);
        color: white;
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 5px 15px rgba(249, 115, 22, 0.3);
    }
    
    .temperature-display {
        position: absolute;
        bottom: 2rem;
        left: 2rem;
        background: rgba(30, 58, 138, 0.9);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 15px;
        backdrop-filter: blur(10px);
    }
    
    .temp-range {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    
    .temp-label {
        font-size: 0.9rem;
        opacity: 0.8;
    }
    
    /* Product Info */
    .product-info {
        padding: 3rem 0;
    }
    
    .product-category-tag {
        background: var(--arctic-blue);
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        margin-bottom: 1.5rem;
    }
    
    .product-title-large {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--cold-blue);
        margin-bottom: 1.5rem;
        line-height: 1.2;
    }
    
    .product-price {
        font-size: 2rem;
        font-weight: 700;
        color: var(--temperature-orange);
        margin-bottom: 2rem;
    }
    
    .product-description-long {
        font-size: 1.1rem;
        line-height: 1.7;
        color: var(--steel-gray);
        margin-bottom: 2rem;
    }
    
    /* Action Buttons */
    .product-actions-large {
        display: flex;
        gap: 1rem;
        margin-bottom: 3rem;
        flex-wrap: wrap;
    }
    
    .btn-primary-large {
        background: linear-gradient(135deg, var(--arctic-blue), var(--cold-blue));
        border: none;
        color: white;
        padding: 1rem 2.5rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        flex: 1;
        justify-content: center;
        min-width: 200px;
    }
    
    .btn-primary-large:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .btn-secondary-large {
        background: transparent;
        border: 3px solid var(--arctic-blue);
        color: var(--arctic-blue);
        padding: 1rem 2.5rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        flex: 1;
        justify-content: center;
        min-width: 200px;
    }
    
    .btn-secondary-large:hover {
        background: var(--arctic-blue);
        color: white;
        transform: translateY(-3px);
        text-decoration: none;
    }
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    
    .btn-secondary-large:hover {
        background: var(--arctic-blue);
        color: white;
        text-decoration: none;
    }
    
    /* Specifications Tabs */
    .specs-section {
        background: var(--ice-white);
        padding: 4rem 0;
    }
    
    .spec-tabs {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    
    .spec-tab {
        background: white;
        border: none;
        padding: 2rem 1.5rem;
        border-radius: 20px;
        font-weight: 600;
        color: var(--steel-gray);
        transition: all 0.4s ease;
        cursor: pointer;
        box-shadow: 0 8px 25px rgba(30, 58, 138, 0.1);
        text-align: center;
        min-height: 140px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    .spec-tab::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(45deg, var(--arctic-blue), var(--cold-blue));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .spec-tab.active::before,
    .spec-tab:hover::before {
        transform: scaleX(1);
    }
    
    .spec-tab.active,
    .spec-tab:hover {
        background: white;
        color: var(--cold-blue);
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(30, 58, 138, 0.2);
    }
    
    .spec-tab-icon {
        font-size: 2rem;
        margin-bottom: 0.75rem;
        color: var(--arctic-blue);
        transition: all 0.3s ease;
    }
    
    .spec-tab.active .spec-tab-icon,
    .spec-tab:hover .spec-tab-icon {
        color: var(--cold-blue);
        transform: scale(1.1);
    }
    
    .spec-tab-title {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        transition: all 0.3s ease;
    }
    
    .spec-content {
        display: none;
        background: white;
        border-radius: 20px;
        padding: 3rem;
        box-shadow: 0 10px 30px rgba(30, 58, 138, 0.1);
        border: 1px solid var(--frost-gray);
        animation: fadeInUp 0.5s ease;
    }
    
    .spec-content.active {
        display: block;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Features Grid */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    
    .feature-item {
        background: var(--frost-gray);
        border-radius: 15px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }
    
    .feature-item:hover {
        background: var(--arctic-blue);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
    }
    
    .feature-icon {
        width: 50px;
        height: 50px;
        background: var(--arctic-blue);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .feature-item:hover .feature-icon {
        background: white;
        color: var(--arctic-blue);
    }
    
    .feature-text {
        font-weight: 600;
        font-size: 1rem;
    }
    
    /* Specifications Table */
    .specs-table {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(30, 58, 138, 0.08);
    }
    
    .spec-row {
        display: flex;
        border-bottom: 1px solid var(--frost-gray);
        transition: all 0.3s ease;
    }
    
    .spec-row:hover {
        background: var(--ice-white);
    }
    
    .spec-row:last-child {
        border-bottom: none;
    }
    
    .spec-label,
    .spec-value {
        padding: 1.5rem;
        font-size: 1rem;
    }
    
    .spec-label {
        background: var(--frost-gray);
        font-weight: 600;
        color: var(--cold-blue);
        flex: 1;
        border-right: 1px solid var(--ice-white);
    }
    
    .spec-value {
        flex: 2;
        color: var(--steel-gray);
        font-weight: 500;
    }
    
    /* Quick Specs Cards */
    .quick-specs {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    
    .quick-spec-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 5px 20px rgba(30, 58, 138, 0.08);
        transition: all 0.3s ease;
    }
    
    .quick-spec-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(30, 58, 138, 0.15);
    }
    
    .quick-spec-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--arctic-blue), var(--cold-blue));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: white;
        font-size: 1.5rem;
    }
    
    .quick-spec-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--cold-blue);
        margin-bottom: 0.5rem;
    }
    
    .quick-spec-label {
        color: var(--steel-gray);
        font-size: 0.95rem;
        font-weight: 500;
    }
    
    /* Related Products */
    .related-products {
        padding: 4rem 0;
        background: var(--frost-gray);
    }
    
    .section-title {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .section-title h2 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--cold-blue);
        margin-bottom: 1rem;
    }
    
    .section-subtitle {
        color: var(--steel-gray);
        font-size: 1.1rem;
    }
    
    /* Support Section */
    .support-section {
        background: linear-gradient(135deg, var(--cold-blue) 0%, var(--arctic-blue) 100%);
        padding: 4rem 0;
        color: white;
    }
    
    .support-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 2.5rem;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .support-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.15);
    }
    
    .support-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
    }
    
    .support-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    
    .support-description {
        margin-bottom: 1.5rem;
        opacity: 0.9;
    }
    
    .support-btn {
        display: inline-block !important;
        background: white !important;
        color: var(--arctic-blue) !important;
        border: 2px solid var(--arctic-blue) !important;
        padding: 0.75rem 2rem !important;
        border-radius: 50px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        transition: all 0.3s ease !important;
        cursor: pointer !important;
        font-size: 1rem !important;
        line-height: 1.5 !important;
        box-shadow: none !important;
    }
    
    .support-btn:hover,
    .support-btn:focus {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3) !important;
        background: var(--arctic-blue) !important;
        color: white !important;
        text-decoration: none !important;
        border-color: var(--arctic-blue) !important;
        outline: none !important;
    }
    
    .support-btn:active {
        transform: translateY(0) !important;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2) !important;
    }
    
    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .product-hero {
            padding: 2rem 0;
        }
        
        .product-title-large {
            font-size: 1.8rem;
            line-height: 1.3;
            margin-bottom: 1rem;
        }
        
        .product-price {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .product-description-long {
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .product-actions-large {
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }
        
        .btn-primary-large,
        .btn-secondary-large {
            width: 100%;
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
            min-width: unset;
        }
        
        .product-image-container {
            height: 280px;
            margin-bottom: 2rem;
        }
        
        .product-badge-large {
            top: 1rem;
            right: 1rem;
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }
        
        .temperature-display {
            bottom: 1rem;
            left: 1rem;
            padding: 0.75rem 1rem;
        }
        
        .temp-range {
            font-size: 1.1rem;
        }
        
        .temp-label {
            font-size: 0.8rem;
        }
        
        .support-btn {
            padding: 0.625rem 1.5rem;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            width: 100%;
            text-align: center;
        }
        
        .support-card {
            padding: 2rem 1.5rem;
            margin-bottom: 1rem;
        }
        
        .spec-tabs {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .spec-tab {
            min-height: 120px;
            padding: 1.5rem 1rem;
        }
        
        .spec-tab-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .spec-tab-title {
            font-size: 0.9rem;
        }
        
        .spec-row {
            flex-direction: column;
        }
        
        .spec-label {
            border-right: none;
            border-bottom: 1px solid var(--ice-white);
        }
        
        .quick-specs {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }
        
        .features-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .feature-item {
            padding: 1rem;
            flex-direction: column;
            text-align: center;
            gap: 0.75rem;
        }
        
        .feature-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
            margin-right: 0;
            margin-bottom: 0.5rem;
        }
        
        .feature-text {
            font-size: 0.9rem;
            text-align: center;
        }
        
        .quick-specs {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .quick-spec-card {
            padding: 1.5rem 1rem;
        }
        
        .quick-spec-icon {
            width: 50px;
            height: 50px;
            font-size: 1.3rem;
        }
        
        .quick-spec-value {
            font-size: 1.4rem;
        }
        
        .quick-spec-label {
            font-size: 0.85rem;
        }
        
        .breadcrumb-section {
            padding: 0.75rem 0;
        }
        
        .breadcrumb-item {
            font-size: 0.9rem;
        }
        
        /* Section spacing */
        .specs-section {
            padding: 3rem 0;
        }
        
        .support-section {
            padding: 3rem 0;
        }
        
        .related-products {
            padding: 3rem 0;
        }
        
        .section-title h2 {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }
        
        .section-subtitle {
            font-size: 1rem;
        }
    }
    
    /* Tablet Responsive (768px to 991px) */
    @media (min-width: 768px) and (max-width: 991px) {
        .product-title-large {
            font-size: 2.2rem;
        }
        
        .product-price {
            font-size: 1.8rem;
        }
        
        .product-actions-large {
            flex-direction: row;
            gap: 1rem;
        }
        
        .btn-primary-large,
        .btn-secondary-large {
            width: auto;
            flex: 1;
            max-width: 250px;
        }
        
        .features-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        
        .feature-item {
            flex-direction: row;
            text-align: left;
            padding: 1.5rem;
        }
        
        .feature-icon {
            margin-right: 1rem;
            margin-bottom: 0;
        }
        
        .feature-text {
            text-align: left;
            font-size: 1rem;
        }
        
        .quick-specs {
            grid-template-columns: repeat(3, 1fr);
        }
        
        .product-image-container {
            height: 400px;
        }
    }
    
    /* Small Desktop (992px to 1199px) */
    @media (min-width: 992px) and (max-width: 1199px) {
        .features-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }
        
        .quick-specs {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    
    /* Large Desktop (1200px and up) */
    @media (min-width: 1200px) {
        .features-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    /* Extra small devices (landscape phones, 576px and up) */
    @media (min-width: 576px) and (max-width: 767px) {
        .product-actions-large {
            flex-direction: row;
            gap: 0.75rem;
        }
        
        .btn-primary-large,
        .btn-secondary-large {
            flex: 1;
            padding: 0.875rem 1rem;
            font-size: 0.95rem;
        }
        
        .quick-specs {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    /* Very small screens (320px to 575px) */
    @media (max-width: 575px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .product-hero {
            padding: 1.5rem 0;
        }
        
        .product-title-large {
            font-size: 1.5rem;
        }
        
        .product-price {
            font-size: 1.3rem;
        }
        
        .product-image-container {
            height: 250px;
            border-radius: 15px;
        }
        
        .product-badge-large {
            top: 0.5rem;
            right: 0.5rem;
            padding: 0.4rem 0.8rem;
            font-size: 0.7rem;
        }
        
        .temperature-display {
            bottom: 0.5rem;
            left: 0.5rem;
            padding: 0.5rem 0.75rem;
        }
        
        .quick-specs {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
        
        .quick-spec-card {
            padding: 1.25rem 1rem;
        }
        
        .support-card {
            padding: 1.5rem 1rem;
        }
        
        .section-title h2 {
            font-size: 1.75rem;
        }
    }
        
        .product-image-container {
            height: 300px;
        }
        
        .product-badge-large {
            top: 1rem;
            right: 1rem;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }
        
        .temperature-display {
            bottom: 1rem;
            left: 1rem;
            padding: 0.75rem 1rem;
        }
        
        .temp-range {
            font-size: 1.2rem;
        }
    }
    
    @media (max-width: 576px) {
        .spec-tabs {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }
        
        .spec-tab {
            min-height: 100px;
            padding: 1rem 0.75rem;
        }
        
        .spec-tab-icon {
            font-size: 1.3rem;
            margin-bottom: 0.4rem;
        }
        
        .spec-tab-title {
            font-size: 0.8rem;
        }
        
        .spec-content {
            padding: 2rem 1.5rem;
        }
    }
</style>

<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="Products.php">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product_details['title']); ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Product Hero -->
<section class="product-hero">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6 order-2 order-lg-1">
                <div class="product-image-container">
                    <?php 
                    // Complete image path resolution logic (exact copy from Products.php)
                    $product_id = $product_details['id'] ?? 0;
                    $final_image = '';
                    $image_found = false;
                    
                    // Method 1: Check database image_path first
                    if (!empty($product_details['image_path'])) {
                        $db_image_paths = [
                            '../uploads/products/' . $product_details['image_path'],
                            '../images/products/' . $product_details['image_path'],
                            'uploads/products/' . $product_details['image_path'],
                            'images/products/' . $product_details['image_path'],
                            '../images/products/' . $product_id . '/' . $product_details['image_path']
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
                            '../images/products/plant-picture-clean-room-equipment-stainless-steel-machines.jpg',
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
                    ?>
                    
                    <?php if ($image_found && !empty($final_image)): ?>
                        <img src="<?php echo htmlspecialchars($final_image); ?>" 
                             alt="<?php echo htmlspecialchars($product_details['title'] ?? 'Product'); ?>"
                             class="img-fluid">
                    <?php else: ?>
                        <div class="product-placeholder">
                            <i class="fas fa-snowflake"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="product-badge-large">New Product</div>
                    <div class="temperature-display">
                        <div class="temp-range">-8°C to +8°C</div>
                        <div class="temp-label">Application Temp</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 order-1 order-lg-2">
                <div class="product-info">
                    <span class="product-category-tag"><?php echo htmlspecialchars($product_details['category_name'] ?? 'Cold Storage Equipment'); ?></span>
                    <h1 class="product-title-large"><?php echo htmlspecialchars($product_details['title']); ?></h1>
                    <?php if (!empty($product_details['price'])): ?>
                        <div class="product-price">$<?php echo number_format($product_details['price']); ?></div>
                    <?php endif; ?>
                    <p class="product-description-long"><?php echo htmlspecialchars($product_details['description']); ?></p>
                    
                    <div class="product-actions-large">
                        <a href="contact.php" class="btn-primary-large">
                            <i class="fas fa-envelope me-2"></i>Request Quote
                        </a>
                        <a href="tel:+1-555-123-4567" class="btn-secondary-large">
                            <i class="fas fa-phone me-2"></i>Call Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Specs -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="quick-specs">
            <div class="quick-spec-card">
                <div class="quick-spec-icon">
                    <i class="fas fa-thermometer-half"></i>
                </div>
                <div class="quick-spec-value">±0.1°C</div>
                <div class="quick-spec-label">Temperature Precision</div>
            </div>
            <div class="quick-spec-card">
                <div class="quick-spec-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="quick-spec-value">24/7</div>
                <div class="quick-spec-label">Monitoring</div>
            </div>
            <div class="quick-spec-card">
                <div class="quick-spec-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <div class="quick-spec-value">Energy Efficient</div>
                <div class="quick-spec-label">Eco-Friendly</div>
            </div>
            <div class="quick-spec-card">
                <div class="quick-spec-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="quick-spec-value">2 Years</div>
                <div class="quick-spec-label">Warranty</div>
            </div>
        </div>
    </div>
</section>
                
          
<!-- Product Features -->
<section class="specs-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold mb-3" style="color: var(--cold-blue);">Product Features</h2>
            <p class="lead text-muted">Explore detailed information about this product's capabilities and benefits</p>
        </div>
        
        <!-- Features Content -->
        <div class="spec-content active">
            <h3 class="mb-4" style="color: var(--cold-blue); font-weight: 700;">Key Features & Benefits</h3>
            <?php 
            // Handle features - could be text with newlines or array
            $features_list = [];
            if (!empty($product_details['features'])) {
                if (is_array($product_details['features'])) {
                    $features_list = $product_details['features'];
                } else {
                    // Split by newlines if it's text
                    $features_list = array_filter(explode("\n", $product_details['features']));
                }
            }
            
            if (!empty($features_list)): ?>
                <div class="feature-grid">
                    <?php foreach ($features_list as $feature): ?>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="feature-text"><?php echo htmlspecialchars(trim($feature)); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-features text-center py-4">
                    <i class="fas fa-info-circle text-muted mb-3" style="font-size: 2rem;"></i>
                    <p class="mb-0">Detailed features information will be available soon. Please contact us for complete specifications.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Related Products -->

<!-- Support Section -->
<section class="support-section">
    <div class="container">
        <div class="row align-items-center text-center mb-5">
            <div class="col-12">
                <h2 class="fw-bold mb-3">Professional Support & Services</h2>
                <p class="lead opacity-75">
                    Our expert team provides comprehensive support throughout your cold storage journey
                </p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="support-card">
                    <div class="support-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h4 class="support-title">Professional Installation</h4>
                    <p class="support-description">
                        Expert installation and commissioning by certified technicians with full system testing and validation.
                    </p>
                    <a href="contact.php" class="support-btn">Schedule Installation</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="support-card">
                    <div class="support-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4 class="support-title">24/7 Technical Support</h4>
                    <p class="support-description">
                        Round-the-clock technical support with remote monitoring and emergency response capabilities.
                    </p>
                    <a href="contact.php" class="support-btn">Get Support</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mx-auto">
                <div class="support-card">
                    <div class="support-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <h4 class="support-title">Maintenance Plans</h4>
                    <p class="support-description">
                        Comprehensive maintenance programs to ensure optimal performance and extend equipment lifespan.
                    </p>
                    <a href="contact.php" class="support-btn">View Plans</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
<section class="related-products">
    <div class="container">
        <div class="section-title">
            <h2>Related Products</h2>
            <p class="section-subtitle">Complete your cold storage solution with these complementary products</p>
        </div>
        <div class="row g-4">
            <?php if (!empty($related_products)): ?>
                <?php foreach ($related_products as $related_product): ?>
                    <?php
                    // Get image for related product using same logic as other pages
                    $related_product_id = $related_product['id'] ?? 0;
                    $related_final_image = '';
                    $related_image_found = false;
                    
                    if (!empty($related_product['image_path'])) {
                        $related_db_image_paths = [
                            '../images/products/' . $related_product['image_path'],
                            '../images/products/' . $related_product_id . '/' . $related_product['image_path'],
                            'images/products/' . $related_product['image_path'],
                            'images/products/' . $related_product_id . '/' . $related_product['image_path']
                        ];
                        
                        foreach ($related_db_image_paths as $path) {
                            if (file_exists($path)) {
                                $related_final_image = $path;
                                $related_image_found = true;
                                break;
                            }
                        }
                    }
                    
                    if (!$related_image_found && $related_product_id > 0) {
                        $related_product_folder = '../images/products/' . $related_product_id;
                        if (is_dir($related_product_folder)) {
                            $related_found_images = glob($related_product_folder . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
                            if (!empty($related_found_images)) {
                                $related_final_image = $related_found_images[0];
                                $related_image_found = true;
                            }
                        }
                    }
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <?php if ($related_image_found): ?>
                                <div class="card-img-top" style="height: 200px; overflow: hidden;">
                                    <img src="<?php echo htmlspecialchars($related_final_image); ?>" 
                                         alt="<?php echo htmlspecialchars($related_product['title']); ?>"
                                         class="img-fluid w-100 h-100"
                                         style="object-fit: cover;">
                                </div>
                            <?php endif; ?>
                            <div class="card-body text-center p-4 d-flex flex-column">
                                <?php if (!$related_image_found): ?>
                                    <i class="fas fa-snowflake text-primary mb-3" style="font-size: 3rem; color: var(--arctic-blue) !important;"></i>
                                <?php endif; ?>
                                <h5 class="card-title"><?php echo htmlspecialchars($related_product['title']); ?></h5>
                                <p class="card-text flex-grow-1"><?php echo htmlspecialchars(substr($related_product['description'], 0, 100)) . '...'; ?></p>
                                <a href="Products.php" class="btn btn-outline-primary mt-auto">View All Products</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback if no related products found -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-snowflake text-primary mb-3" style="font-size: 3rem; color: var(--arctic-blue) !important;"></i>
                            <h5 class="card-title">Cold Storage Equipment</h5>
                            <p class="card-text">Explore our comprehensive range of industrial cold storage solutions.</p>
                            <a href="Products.php" class="btn btn-outline-primary">View All Products</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-thermometer-half text-primary mb-3" style="font-size: 3rem; color: var(--arctic-blue) !important;"></i>
                            <h5 class="card-title">Temperature Monitoring</h5>
                            <p class="card-text">Advanced monitoring systems for real-time temperature tracking and alerts.</p>
                            <a href="Products.php" class="btn btn-outline-primary">View All Products</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-cogs text-primary mb-3" style="font-size: 3rem; color: var(--arctic-blue) !important;"></i>
                            <h5 class="card-title">Professional Services</h5>
                            <p class="card-text">Professional installation, maintenance and support for your equipment.</p>
                            <a href="contact.php" class="btn btn-outline-primary">Contact Us</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
// Enhanced Product Detail Page Functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Product detail page loaded');
    
    // Smooth scrolling for anchor links
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
    
    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // Enhanced button interactions
    document.querySelectorAll('.btn-primary-large, .btn-secondary-large, .support-btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
        
        btn.addEventListener('focus', function() {
            this.style.outline = '3px solid rgba(59, 130, 246, 0.3)';
            this.style.outlineOffset = '2px';
        });
        
        btn.addEventListener('blur', function() {
            this.style.outline = 'none';
        });
    });
    
    // Touch device optimizations
    if ('ontouchstart' in window) {
        // Add touch-friendly interactions
        document.querySelectorAll('.feature-item, .quick-spec-card, .support-card').forEach(card => {
            card.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });
            
            card.addEventListener('touchend', function() {
                this.style.transform = 'scale(1)';
            });
        });
    }
    
    // Responsive image sizing
    function adjustImageSizes() {
        const productImage = document.querySelector('.product-image-container img');
        if (productImage) {
            if (window.innerWidth < 768) {
                productImage.style.objectFit = 'cover';
            } else {
                productImage.style.objectFit = 'contain';
            }
        }
    }
    
    // Call on load and resize
    adjustImageSizes();
    window.addEventListener('resize', adjustImageSizes);
    
    // Add loading states for buttons
    document.querySelectorAll('a[href^="tel:"], a[href^="mailto:"]').forEach(link => {
        link.addEventListener('click', function() {
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Connecting...';
            
            setTimeout(() => {
                this.innerHTML = originalText;
            }, 2000);
        });
    });
    
    // Performance monitoring
    if ('performance' in window && 'measure' in performance) {
        window.addEventListener('load', function() {
            setTimeout(() => {
                const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
                console.log('Page load time:', loadTime + 'ms');
            }, 0);
        });
    }
    
    // Accessibility improvements
    document.querySelectorAll('.quick-spec-card, .feature-item, .support-card').forEach(card => {
        if (!card.hasAttribute('tabindex')) {
            card.setAttribute('tabindex', '0');
        }
        
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const link = this.querySelector('a');
                if (link) {
                    link.click();
                }
            }
        });
    });
});

// Viewport-based animations
function animateOnScroll() {
    const elements = document.querySelectorAll('.feature-item, .quick-spec-card, .support-card');
    
    elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;
        
        if (elementTop < window.innerHeight - elementVisible) {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }
    });
}

// Initial styles for animation
document.addEventListener('DOMContentLoaded', function() {
    const animatedElements = document.querySelectorAll('.feature-item, .quick-spec-card, .support-card');
    animatedElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });
    
    // Trigger initial animation check
    animateOnScroll();
});

window.addEventListener('scroll', animateOnScroll);
</script>

<?php include '../includes/footer.php'; ?>