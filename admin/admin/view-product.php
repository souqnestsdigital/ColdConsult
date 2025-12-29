<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/Admin.php';
require_once '../../classes/Product.php';
require_once '../../classes/Category.php';

$admin = new Admin();
if (!$admin->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$product = new Product();
$category = new Category($db);

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$product_id) {
    header('Location: manage-products.php?error=invalid_id');
    exit;
}

// Get product details
try {
    $product_data = $product->getById($product_id);
    if (!$product_data) {
        header('Location: manage-products.php?error=product_not_found');
        exit;
    }
} catch (Exception $e) {
    error_log("Error fetching product: " . $e->getMessage());
    header('Location: manage-products.php?error=database_error');
    exit;
}

// Get category information
$category_name = $product_data['category_name'] ?? 'Uncategorized';
$page_title = "Product Details - " . htmlspecialchars($product_data['title']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - ColdConsult Admin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .admin-navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .breadcrumb {
            background: transparent;
            margin-bottom: 0;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: white;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-bottom: 1px solid var(--border-color);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .status-active {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .action-buttons .btn {
            margin: 0.25rem;
            min-width: 120px;
        }

        .info-card {
            background: white;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-label {
            font-weight: 600;
            color: var(--secondary-color);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .info-value {
            color: #1e293b;
            font-size: 1rem;
        }

        .features-list {
            list-style: none;
            padding: 0;
        }

        .features-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
        }

        .features-list li:last-child {
            border-bottom: none;
        }

        .features-list li i {
            color: var(--success-color);
            margin-right: 0.75rem;
            width: 16px;
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 1.5rem 0;
            }
            
            .action-buttons .btn {
                width: 100%;
                margin: 0.25rem 0;
            }
            
            .product-image {
                height: 250px;
            }
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg admin-navbar">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-snowflake me-2 text-primary"></i>
                ColdConsult Admin
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-gauge me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="manage-products.php">
                            <i class="fas fa-boxes-stacked me-1"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage-categories.php">
                            <i class="fas fa-tags me-1"></i> Categories
                        </a>
                    </li>
                </ul>
                
                <div class="navbar-nav">
                    <a class="nav-link" href="../index.php" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i> View Site
                    </a>
                    <a class="nav-link text-danger" href="logout.php">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="manage-products.php">Products</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <?php echo htmlspecialchars($product_data['title']); ?>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="h2 mb-0">Product Details</h1>
                    <p class="mb-0 opacity-75">View and manage product information</p>
                </div>
                <div class="col-lg-6">
                    <div class="text-lg-end action-buttons">
                        <a href="edit-product.php?id=<?php echo $product_id; ?>" class="btn btn-light">
                            <i class="fas fa-edit me-1"></i> Edit Product
                        </a>
                        <a href="manage-products.php" class="btn btn-outline-light">
                            <i class="fas fa-arrow-left me-1"></i> Back to Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Product Image and Basic Info -->
            <div class="col-lg-4 mb-4">
                <div class="product-card">
                    <?php if (!empty($product_data['image_path'])): ?>
                        <img src="../../<?php echo htmlspecialchars($product_data['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($product_data['title']); ?>" 
                             class="product-image"
                             onerror="this.src='../../public/images/products/coldstoragehero.jpg'">
                    <?php else: ?>
                        <div class="product-image d-flex align-items-center justify-content-center bg-light">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><?php echo htmlspecialchars($product_data['title']); ?></h5>
                            <span class="status-badge <?php echo $product_data['status'] === 'active' ? 'status-active' : 'status-inactive'; ?>">
                                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                <?php echo ucfirst($product_data['status'] ?? 'inactive'); ?>
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <div class="info-label">Category</div>
                            <div class="info-value">
                                <i class="fas fa-tag me-1"></i>
                                <?php echo htmlspecialchars($category_name); ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="info-label">Product ID</div>
                            <div class="info-value">#<?php echo $product_id; ?></div>
                        </div>
                        
                        <?php if (isset($product_data['price']) && !empty($product_data['price'])): ?>
                        <div class="mb-3">
                            <div class="info-label">Price</div>
                            <div class="info-value h5 text-primary mb-0">
                                $<?php echo number_format($product_data['price'], 2); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-lg-8">
                <!-- Description -->
                <div class="info-card">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-align-left text-primary me-2"></i>
                        <h5 class="mb-0">Description</h5>
                    </div>
                    <div class="info-value">
                        <?php if (!empty($product_data['description'])): ?>
                            <?php echo nl2br(htmlspecialchars($product_data['description'])); ?>
                        <?php else: ?>
                            <em class="text-muted">No description available.</em>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Features -->
                <?php if (!empty($product_data['features'])): ?>
                <div class="info-card">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-star text-primary me-2"></i>
                        <h5 class="mb-0">Features</h5>
                    </div>
                    <ul class="features-list">
                        <?php 
                        $features = explode("\n", $product_data['features']);
                        foreach ($features as $feature): 
                            $feature = trim($feature);
                            if (!empty($feature)):
                        ?>
                            <li>
                                <i class="fas fa-check"></i>
                                <?php echo htmlspecialchars($feature); ?>
                            </li>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Technical Information -->
                <div class="info-card">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        <h5 class="mb-0">Technical Information</h5>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Created Date</div>
                            <div class="info-value">
                                <i class="fas fa-calendar me-1"></i>
                                <?php echo date('M j, Y', strtotime($product_data['created_at'])); ?>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Last Updated</div>
                            <div class="info-value">
                                <i class="fas fa-clock me-1"></i>
                                <?php echo date('M j, Y g:i A', strtotime($product_data['updated_at'] ?? $product_data['created_at'])); ?>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                <span class="status-badge <?php echo $product_data['status'] === 'active' ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo ucfirst($product_data['status'] ?? 'inactive'); ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Visibility</div>
                            <div class="info-value">
                                <?php if (($product_data['is_active'] ?? 0) && $product_data['status'] === 'active'): ?>
                                    <span class="text-success">
                                        <i class="fas fa-eye me-1"></i> Visible on Website
                                    </span>
                                <?php else: ?>
                                    <span class="text-warning">
                                        <i class="fas fa-eye-slash me-1"></i> Hidden from Website
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="info-card">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-cogs text-primary me-2"></i>
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    
                    <div class="d-flex flex-wrap gap-2">
                        <a href="edit-product.php?id=<?php echo $product_id; ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit Product
                        </a>
                        
                        <a href="../product-detail.php?id=<?php echo $product_id; ?>" 
                           class="btn btn-outline-primary" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i> View on Website
                        </a>
                        
                        <button type="button" class="btn btn-outline-warning" 
                                onclick="toggleStatus(<?php echo $product_id; ?>, '<?php echo $product_data['status']; ?>')">
                            <i class="fas fa-toggle-on me-1"></i>
                            <?php echo $product_data['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                        </button>
                        
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-1"></i> Delete Product
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Are you sure you want to delete this product?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-warning me-2"></i>
                        <strong>Product:</strong> <?php echo htmlspecialchars($product_data['title']); ?>
                        <br>
                        <strong>This action cannot be undone!</strong>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="deleteProduct()">
                        <i class="fas fa-trash me-1"></i> Delete Product
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function confirmDelete() {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        function deleteProduct() {
            console.log('Delete function called');
            
            // Show loading state
            const deleteBtn = document.querySelector('#deleteModal .btn-danger');
            const originalText = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Deleting...';
            deleteBtn.disabled = true;

            // Make AJAX request to delete
            console.log('Sending delete request for product ID: <?php echo $product_id; ?>');
            
            fetch('manage-products.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=delete&id=<?php echo $product_id; ?>'
            })
            .then(response => {
                console.log('Delete response status:', response.status);
                console.log('Delete response headers:', response.headers);
                return response.text().then(text => {
                    console.log('Delete response text:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Failed to parse JSON:', e);
                        console.error('Response was:', text);
                        throw new Error('Invalid JSON response: ' + text);
                    }
                });
            })
            .then(data => {
                console.log('Delete parsed data:', data);
                if (data.success) {
                    // Show success message and redirect
                    window.location.href = 'manage-products.php?success=product_deleted';
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete product'));
                    deleteBtn.innerHTML = originalText;
                    deleteBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                alert('An error occurred while deleting the product: ' + error.message);
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;
            });
        }

        function toggleStatus(productId, currentStatus) {
            console.log('Toggle status called for product:', productId, 'current status:', currentStatus);
            
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            const action = newStatus === 'active' ? 'activate' : 'deactivate';
            
            if (!confirm(`Are you sure you want to ${action} this product?`)) {
                return;
            }

            console.log('Sending toggle request:', {productId, currentStatus, newStatus});

            // Make AJAX request to toggle status
            fetch('manage-products.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=toggle_status&id=${productId}&status=${newStatus}`
            })
            .then(response => {
                console.log('Toggle response status:', response.status);
                return response.text().then(text => {
                    console.log('Toggle response text:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Failed to parse JSON:', e);
                        console.error('Response was:', text);
                        throw new Error('Invalid JSON response: ' + text);
                    }
                });
            })
            .then(data => {
                console.log('Toggle parsed data:', data);
                if (data.success) {
                    // Reload page to show updated status
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to update status'));
                }
            })
            .catch(error => {
                console.error('Toggle error:', error);
                alert('An error occurred while updating the status: ' + error.message);
            });
        }

        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    if (alert.classList.contains('alert-dismissible')) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                });
            }, 5000);
        });
    </script>
</body>
</html>