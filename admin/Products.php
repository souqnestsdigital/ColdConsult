<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';

$page_title = 'Industrial Cold Storage Equipment | Commercial Refrigeration Systems | ColdConsult';
$page_description = 'Explore our comprehensive range of industrial cold storage equipment including freezers, commercial refrigerators, walk-in coolers, blast chillers, and temperature monitoring systems. Professional-grade solutions for pharmaceutical, food service, and logistics industries.';
$og_image = "https://" . $_SERVER['HTTP_HOST'] . "/images/products/plant-picture-clean-room-equipment-stainless-steel-machines.jpg";

// Initialize database connection and handle errors gracefully
$products = [];
$categories = [];
$error_message = '';

try {
    $database = new Database();
    $db = $database->getConnection();
    $product = new Product();
    $category = new Category($db);
    
    // Get filter parameters with proper sanitization
    $category_filter = isset($_GET['category']) ? intval($_GET['category']) : '';
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $price_range = isset($_GET['price_range']) ? $_GET['price_range'] : '';
    $sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'name';
    $per_page = isset($_GET['per_page']) ? min(max(intval($_GET['per_page']), 6), 24) : 12;
    $current_page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
    
    // Get all products and categories
    $all_products = $product->getAll();
    $categories = $category->getActiveCategories();
    
    // Apply filters
    $filtered_products = $all_products;
    
    if ($category_filter) {
        $filtered_products = array_filter($filtered_products, function($p) use ($category_filter) {
            return $p['category_id'] == $category_filter && $p['status'] == 'active';
        });
    }
    
    if ($search) {
        $search_term = strtolower($search);
        $filtered_products = array_filter($filtered_products, function($p) use ($search_term) {
            return (stripos($p['title'], $search_term) !== false || 
                    stripos($p['description'], $search_term) !== false || 
                    stripos($p['features'], $search_term) !== false) && 
                    $p['status'] == 'active';
        });
    }
    
    if (!$category_filter && !$search) {
        $filtered_products = array_filter($filtered_products, function($p) {
            return $p['status'] == 'active';
        });
    }
    
    // Apply price range filter
    if ($price_range) {
        switch ($price_range) {
            case 'under_1000':
                $filtered_products = array_filter($filtered_products, function($p) {
                    return isset($p['price']) && $p['price'] < 1000;
                });
                break;
            case '1000_5000':
                $filtered_products = array_filter($filtered_products, function($p) {
                    return isset($p['price']) && $p['price'] >= 1000 && $p['price'] <= 5000;
                });
                break;
            case '5000_20000':
                $filtered_products = array_filter($filtered_products, function($p) {
                    return isset($p['price']) && $p['price'] >= 5000 && $p['price'] <= 20000;
                });
                break;
            case 'over_20000':
                $filtered_products = array_filter($filtered_products, function($p) {
                    return isset($p['price']) && $p['price'] > 20000;
                });
                break;
        }
    }
    
    // Sort products
    switch ($sort_by) {
        case 'price_low':
            usort($filtered_products, function($a, $b) {
                return ($a['price'] ?? 0) <=> ($b['price'] ?? 0);
            });
            break;
        case 'price_high':
            usort($filtered_products, function($a, $b) {
                return ($b['price'] ?? 0) <=> ($a['price'] ?? 0);
            });
            break;
        case 'newest':
            usort($filtered_products, function($a, $b) {
                return strtotime($b['created_at'] ?? '1970-01-01') <=> strtotime($a['created_at'] ?? '1970-01-01');
            });
            break;
        default: // name
            usort($filtered_products, function($a, $b) {
                return strcasecmp($a['title'] ?? '', $b['title'] ?? '');
            });
            break;
    }
    
    // Pagination
    $total_products = count($filtered_products);
    $total_pages = ceil($total_products / $per_page);
    $offset = ($current_page - 1) * $per_page;
    $products = array_slice($filtered_products, $offset, $per_page);
    
} catch (Exception $e) {
    error_log("Error in Products.php: " . $e->getMessage());
    $error_message = "We're experiencing technical difficulties. Please try again later.";
    
    // Fallback products for demonstration
    $products = [
        [
            'id' => 1,
            'title' => 'CryoMax Industrial Freezer CF-5000',
            'description' => 'High-capacity industrial freezer designed for pharmaceutical and food storage with precision temperature control and advanced monitoring systems.',
            'category_name' => 'Industrial Freezers',
            'price' => 45999,
            'image_path' => '',
            'status' => 'active'
        ],
        [
            'id' => 2,
            'title' => 'TempGuard IoT Monitoring System',
            'description' => 'Wireless temperature and humidity monitoring with real-time alerts, cloud dashboard, and compliance reporting for critical storage applications.',
            'category_name' => 'Monitoring Systems',
            'price' => 2999,
            'image_path' => '',
            'status' => 'active'
        ],
        [
            'id' => 3,
            'title' => 'ChillPro Commercial Refrigerator CR-3000',
            'description' => 'Energy-efficient commercial refrigerator perfect for restaurants, laboratories, and medical facilities requiring consistent temperature control.',
            'category_name' => 'Commercial Refrigeration',
            'price' => 8999,
            'image_path' => '',
            'status' => 'active'
        ]
    ];
    $total_products = count($products);
    $total_pages = 1;
}

include '../includes/header.php';
?>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
    --primary-blue: #1e40af;
    --primary-blue-light: #3b82f6;
    --accent-blue: #60a5fa;
    --frost-blue: #dbeafe;
    --ice-white: #f8fafc;
    --steel-gray: #475569;
    --dark-slate: #1e293b;
    --temperature-orange: #f97316;
    --success-green: #10b981;
    --warning-amber: #f59e0b;
    --border-radius: 1rem;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}

/* Typography */
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    line-height: 1.6;
    color: var(--dark-slate);
}

h1, h2, h3, h4, h5, h6 {
    font-family: 'Space Grotesk', 'Inter', sans-serif;
    font-weight: 600;
    letter-spacing: -0.025em;
}

/* Hero Section */
.products-hero {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-light) 100%);
    position: relative;
    overflow: hidden;
    padding: 6rem 0 4rem 0;
    color: white;
}

.products-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('../images/products/plant-picture-clean-room-equipment-stainless-steel-machines.jpg') center/cover;
    opacity: 0.1;
    z-index: 1;
}

.hero-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 20% 80%, rgba(96, 165, 250, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(30, 64, 175, 0.3) 0%, transparent 50%);
    z-index: 2;
}

.hero-content {
    position: relative;
    z-index: 3;
}

.temperature-badge {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    padding: 0.5rem 1.25rem;
    color: white;
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    margin: 0.25rem;
    transition: all 0.3s ease;
}

.temperature-badge:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
}

/* Filter Panel */
.filter-panel {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    padding: 2rem;
    margin: -3rem 0 3rem 0;
    position: relative;
    z-index: 4;
    border: 1px solid rgba(226, 232, 240, 0.8);
}

.filter-section {
    margin-bottom: 1.5rem;
}

.filter-section:last-child {
    margin-bottom: 0;
}

.filter-label {
    font-weight: 600;
    color: var(--dark-slate);
    margin-bottom: 0.75rem;
    display: block;
    font-size: 0.9rem;
}

.form-control, .form-select {
    border: 2px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 0.2rem rgba(30, 64, 175, 0.25);
}

.btn-filter {
    background: var(--primary-blue);
    border: 2px solid var(--primary-blue);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-filter:hover {
    background: var(--primary-blue-light);
    border-color: var(--primary-blue-light);
    color: white;
    transform: translateY(-1px);
}

.btn-clear {
    background: transparent;
    border: 2px solid #e2e8f0;
    color: var(--steel-gray);
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-clear:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: var(--dark-slate);
}

/* Results Header */
.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1.5rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.results-info {
    font-size: 0.95rem;
    color: var(--steel-gray);
}

.results-count {
    font-weight: 600;
    color: var(--primary-blue);
}

.sort-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.sort-label {
    font-size: 0.9rem;
    color: var(--steel-gray);
    font-weight: 500;
}

.sort-select {
    min-width: 180px;
}

/* Product Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.product-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
    overflow: hidden;
    border: 1px solid rgba(226, 232, 240, 0.6);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-4px);
    border-color: var(--accent-blue);
}

.product-image {
    position: relative;
    height: 240px;
    background: linear-gradient(135deg, var(--frost-blue) 0%, #f1f5f9 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: var(--border-radius) var(--border-radius) 0 0;
}

.product-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
    border-radius: var(--border-radius) var(--border-radius) 0 0;
    display: block;
}

.product-card:hover .product-img {
    transform: scale(1.05);
}

.fallback-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    color: #1976d2;
    font-size: 14px;
    text-align: center;
    border-radius: var(--border-radius) var(--border-radius) 0 0;
}

.fallback-placeholder i {
    font-size: 3rem;
    margin-bottom: 0.5rem;
    color: var(--primary-blue);
    opacity: 0.8;
}

.fallback-placeholder span {
    font-weight: 500;
    line-height: 1.2;
    opacity: 0.8;
}

.product-image .fa-snowflake,
.product-image .fa-thermometer-half,
.product-image .fa-warehouse,
.product-image .fa-wind {
    font-size: 4rem;
    color: var(--primary-blue);
    opacity: 0.8;
}

.product-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: var(--temperature-orange);
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.product-badge.featured {
    background: var(--success-green);
}

.product-badge.new {
    background: var(--warning-amber);
}

.temperature-indicator {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: rgba(30, 64, 175, 0.9);
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

.product-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-category {
    color: var(--primary-blue);
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.product-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--dark-slate);
    margin-bottom: 1rem;
    line-height: 1.3;
}

.product-description {
    color: var(--steel-gray);
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
    flex: 1;
}

.product-specs {
    margin-bottom: 1.5rem;
}

.spec-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.85rem;
}

.spec-item:last-child {
    border-bottom: none;
}

.spec-label {
    color: var(--steel-gray);
    font-weight: 500;
}

.spec-value {
    color: var(--dark-slate);
    font-weight: 600;
}

.product-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-blue);
    margin-bottom: 1rem;
}

.product-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
}

.btn-product {
    background: var(--primary-blue);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    border: 2px solid var(--primary-blue);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-product:hover {
    background: var(--primary-blue-light);
    border-color: var(--primary-blue-light);
    color: white;
    transform: translateY(-1px);
}

.btn-secondary {
    background: transparent;
    color: var(--steel-gray);
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.85rem;
    transition: all 0.2s ease;
    border: 1px solid #e2e8f0;
}

.btn-secondary:hover {
    background: #f8fafc;
    color: var(--dark-slate);
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    color: var(--steel-gray);
}

.rating-stars {
    color: #fbbf24;
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 3rem;
}

.pagination {
    gap: 0.5rem;
}

.page-link {
    border: 2px solid #e2e8f0;
    color: var(--steel-gray);
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.page-link:hover {
    background: var(--frost-blue);
    border-color: var(--primary-blue);
    color: var(--primary-blue);
}

.page-item.active .page-link {
    background: var(--primary-blue);
    border-color: var(--primary-blue);
    color: white;
}

/* Loading State */
.loading-placeholder {
    background: #f1f5f9;
    border-radius: var(--border-radius);
    padding: 4rem 2rem;
    text-align: center;
    color: var(--steel-gray);
}

.spinner-border {
    color: var(--primary-blue);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--steel-gray);
}

.empty-state i {
    font-size: 4rem;
    color: var(--frost-blue);
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    color: var(--dark-slate);
    margin-bottom: 1rem;
}

/* Error State */
.error-alert {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .products-hero {
        padding: 4rem 0 3rem 0;
    }
    
    .filter-panel {
        padding: 1.5rem;
        margin: -2rem 0 2rem 0;
    }
    
    .filter-section {
        margin-bottom: 1rem;
    }
    
    .results-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .sort-controls {
        width: 100%;
        justify-content: flex-end;
    }
    
    .products-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .product-actions {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .btn-product,
    .btn-secondary {
        width: 100%;
        text-align: center;
        justify-content: center;
    }
    
    .temperature-badge {
        font-size: 0.75rem;
        padding: 0.375rem 1rem;
    }
}

@media (max-width: 576px) {
    .filter-panel {
        padding: 1rem;
    }
    
    .product-content {
        padding: 1rem;
    }
    
    .product-image {
        height: 200px;
    }
    
    .hero-content h1 {
        font-size: 2rem;
    }
}
</style>

<!-- Products Hero Section -->
<section class="products-hero">
    <div class="hero-pattern"></div>
    <div class="container hero-content">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-3 fw-bold mb-4">
                    Professional <span style="color: #f97316;">Cold Storage</span> Solutions
                </h1>
                <p class="lead mb-4 col-lg-10 mx-auto opacity-90">
                    Discover our comprehensive range of industrial-grade cold storage equipment, 
                    refrigeration systems, and temperature monitoring solutions designed for mission-critical applications.
                </p>
                <div class="d-flex flex-wrap justify-content-center mb-4">
                    <div class="temperature-badge">
                        <i class="fas fa-snowflake me-2"></i>-30°C to +5°C
                    </div>
                    <div class="temperature-badge">
                        <i class="fas fa-shield-alt me-2"></i>GMP Compliant
                    </div>
                    <div class="temperature-badge">
                        <i class="fas fa-wifi me-2"></i>IoT Enabled
                    </div>
                    <div class="temperature-badge">
                        <i class="fas fa-clock me-2"></i>24/7 Support
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filter Panel -->
<div class="container">
    <div class="filter-panel">
        <?php if ($error_message): ?>
            <div class="error-alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <form method="GET" action="" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="filter-label">Search Products</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Search by name or features..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            
            <div class="col-md-2">
                <label class="filter-label">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="1">Industrial Freezers</option>
                        <option value="2">Commercial Refrigeration</option>
                        <option value="3">Monitoring Systems</option>
                        <option value="4">Walk-in Coolers</option>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="filter-label">Price Range</label>
                <select name="price_range" class="form-select">
                    <option value="">All Prices</option>
                    <option value="under_1000" <?php echo $price_range == 'under_1000' ? 'selected' : ''; ?>>Under $1,000</option>
                    <option value="1000_5000" <?php echo $price_range == '1000_5000' ? 'selected' : ''; ?>>$1,000 - $5,000</option>
                    <option value="5000_20000" <?php echo $price_range == '5000_20000' ? 'selected' : ''; ?>>$5,000 - $20,000</option>
                    <option value="over_20000" <?php echo $price_range == 'over_20000' ? 'selected' : ''; ?>>Over $20,000</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="filter-label">Sort By</label>
                <select name="sort" class="form-select sort-select">
                    <option value="name" <?php echo $sort_by == 'name' ? 'selected' : ''; ?>>Name A-Z</option>
                    <option value="price_low" <?php echo $sort_by == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo $sort_by == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="newest" <?php echo $sort_by == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-filter flex-fill">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                    <a href="Products.php" class="btn btn-clear">
                        <i class="fas fa-times me-2"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Products Section -->
<div class="container">
    <!-- Results Header -->
    <div class="results-header">
        <div class="results-info">
            Showing <span class="results-count"><?php echo count($products); ?></span> 
            of <span class="results-count"><?php echo $total_products; ?></span> products
            <?php if ($search): ?>
                for "<strong><?php echo htmlspecialchars($search); ?></strong>"
            <?php endif; ?>
            <?php if ($category_filter && !empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <?php if ($cat['id'] == $category_filter): ?>
                        in <strong><?php echo htmlspecialchars($cat['name']); ?></strong>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="sort-controls d-none d-md-flex">
            <span class="sort-label">View:</span>
            <select class="form-select form-select-sm" style="width: auto;" onchange="updatePerPage(this.value)">
                <option value="12" <?php echo $per_page == 12 ? 'selected' : ''; ?>>12 per page</option>
                <option value="18" <?php echo $per_page == 18 ? 'selected' : ''; ?>>18 per page</option>
                <option value="24" <?php echo $per_page == 24 ? 'selected' : ''; ?>>24 per page</option>
            </select>
        </div>
    </div>

    <!-- Products Grid -->
    <?php if (!empty($products)): ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php
                        // Complete rewrite of image detection logic
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
                        
                        // Final fallback - use a data URL for a placeholder
                        if (!$image_found) {
                            $final_image = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIwIiBoZWlnaHQ9IjIyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGJlYWZlIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxOCIgZmlsbD0iIzNiODJmNiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkNvbGQgU3RvcmFnZTwvdGV4dD48L3N2Zz4=';
                        }
                        
                        // Debug log (comment out in production)
                        // error_log("Product ID $product_id: Final image = $final_image");
                        // error_log("Product ID $product_id: Image found = " . ($image_found ? 'YES' : 'NO'));
                        // if ($product_id > 0) {
                        //     $debug_folder = '../images/products/' . $product_id;
                        //     error_log("Product ID $product_id: Checking folder = $debug_folder");
                        //     if (is_dir($debug_folder)) {
                        //         $debug_files = glob($debug_folder . '/*');
                        //         error_log("Product ID $product_id: Found files = " . print_r($debug_files, true));
                        //     }
                        // }
                        ?>
                        
                        <img src="<?php echo htmlspecialchars($final_image); ?>"
                             alt="<?php echo htmlspecialchars($product['title'] ?? 'Product Image'); ?>"
                             loading="lazy"
                             class="product-img"
                             onerror="handleProductImageError(this, <?php echo $product_id; ?>)"
                             data-product-id="<?php echo $product_id; ?>"
                             style="width:100%;height:100%;object-fit:cover;">
                        
                        <!-- Fallback icon (hidden by default) -->
                        <div class="fallback-placeholder" style="display: none;">
                            <i class="fas fa-snowflake"></i>
                            <span>Cold Storage<br>Equipment</span>
                        </div>
                    </div>
                    <div class="product-content">
                        <div class="product-category">
                            <?php echo htmlspecialchars($product['category_name'] ?? 'Cold Storage Equipment'); ?>
                        </div>
                        <h3 class="product-title">
                            <?php echo htmlspecialchars($product['title']); ?>
                        </h3>
                        <?php if (isset($product['price']) && $product['price'] > 0): ?>
                            <div class="product-price">
                                $<?php echo number_format($product['price'], 0); ?>
                            </div>
                        <?php else: ?>
                            <div class="product-price quote-price">
                                Request Quote
                            </div>
                        <?php endif; ?>
                        <div class="product-actions" style="margin-top: 1rem;">
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn-product primary w-100">
                                <i class="fas fa-info-circle"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination-wrapper">
                <nav aria-label="Products pagination">
                    <ul class="pagination">
                        <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-search"></i>
            <h3>No Products Found</h3>
            <p class="mb-4">We couldn't find any products matching your criteria. Try adjusting your filters or search terms.</p>
            <a href="Products.php" class="btn-product">
                <i class="fas fa-arrow-left me-2"></i>View All Products
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Call to Action Section -->
<section class="py-5 mt-5" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-light) 100%);">
    <div class="container">
        <div class="row align-items-center text-white">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-3">Need Custom Cold Storage Solutions?</h3>
                <p class="lead mb-0">
                    Our engineering team can design and build custom cold storage systems tailored to your specific requirements.
                    Get professional consultation and detailed specifications for your project.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="contact.php" class="btn btn-light btn-lg px-4 py-3 fw-semibold">
                    <i class="fas fa-comments me-2"></i>Get Consultation
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Update per page parameter
function updatePerPage(value) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', value);
    url.searchParams.set('page', '1'); // Reset to first page
    window.location.href = url.toString();
}

// Loading states for better UX
document.addEventListener('DOMContentLoaded', function() {
    // Add loading state to filter form
    const filterForm = document.querySelector('form');
    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Filtering...';
                submitBtn.disabled = true;
            }
        });
    }
    
    // Add smooth scrolling to pagination
    const paginationLinks = document.querySelectorAll('.pagination .page-link');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
    
    // Add hover effects to product cards
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});

// Enhanced image error handling for products
function handleProductImageError(img, productId) {
    console.log('Image failed to load for product:', productId);
    
    // Try alternative image sources
    const alternatives = [
        '../images/products/' + productId + '/',
        'images/products/' + productId + '/',
        '../public/images/products/' + productId + '/',
        './images/products/' + productId + '/'
    ];
    
    // If this is the first error, try finding images in different paths
    if (!img.hasAttribute('data-tried-alternatives')) {
        img.setAttribute('data-tried-alternatives', 'true');
        
        // Try each alternative path
        tryAlternativeImages(img, productId, alternatives, 0);
    } else {
        // All alternatives failed, show fallback
        showImageFallback(img);
    }
}

function tryAlternativeImages(img, productId, alternatives, index) {
    if (index >= alternatives.length) {
        showImageFallback(img);
        return;
    }
    
    // Common image filenames to try
    const commonFilenames = [
        'main.jpg', 'main.png', 'primary.jpg', 'primary.png',
        'thumb.jpg', 'thumb.png', 'image.jpg', 'image.png',
        productId + '.jpg', productId + '.png',
        'product.jpg', 'product.png'
    ];
    
    tryImageFilenames(img, alternatives[index], commonFilenames, 0, () => {
        // If this path failed, try next alternative
        tryAlternativeImages(img, productId, alternatives, index + 1);
    });
}

function tryImageFilenames(img, basePath, filenames, index, onAllFailed) {
    if (index >= filenames.length) {
        onAllFailed();
        return;
    }
    
    const testImg = new Image();
    testImg.onload = function() {
        // Success! Update the main image
        img.src = basePath + filenames[index];
        img.style.display = 'block';
        hideImageFallback(img);
    };
    testImg.onerror = function() {
        // Try next filename
        tryImageFilenames(img, basePath, filenames, index + 1, onAllFailed);
    };
    testImg.src = basePath + filenames[index];
}

function showImageFallback(img) {
    // Hide the image and show placeholder
    img.style.display = 'none';
    const fallback = img.parentNode.querySelector('.fallback-placeholder');
    if (fallback) {
        fallback.style.display = 'flex';
        fallback.style.alignItems = 'center';
        fallback.style.justifyContent = 'center';
        fallback.style.height = '220px';
        fallback.style.backgroundColor = '#f8f9fa';
        fallback.style.color = '#6c757d';
        fallback.style.fontSize = '14px';
        fallback.style.textAlign = 'center';
        fallback.style.borderRadius = '1rem 1rem 0 0';
        fallback.style.flexDirection = 'column';
    }
}

function hideImageFallback(img) {
    const fallback = img.parentNode.querySelector('.fallback-placeholder');
    if (fallback) {
        fallback.style.display = 'none';
    }
}

// Backward compatibility function
function handleImageError(img) {
    const productId = img.getAttribute('data-product-id');
    if (productId) {
        handleProductImageError(img, productId);
    } else {
        showImageFallback(img);
    }
}

// Search suggestions (can be enhanced with AJAX)
const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        // Add debounced search suggestions here if needed
    });
}
</script>

<?php include '../includes/footer.php'; ?>