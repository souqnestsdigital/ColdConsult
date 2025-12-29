<?php include 'includes/admin-header.php'; ?>
<?php require_once '../../classes/Product.php'; ?>
<?php
// Get current admin information for display
$currentAdmin = null;
if (isset($admin) && method_exists($admin, 'getCurrentAdmin')) {
    $currentAdmin = $admin->getCurrentAdmin();
}
// Use safe defaults if admin data unavailable
if (!$currentAdmin) {
    $currentAdmin = ['username' => 'Admin', 'last_login' => null];
}
?>

<!-- Dashboard Content -->
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold" style="color: #212529 !important;">Dashboard</h1>
            <p class="mb-0" style="color: #6c757d !important;">Welcome back, <?php echo htmlspecialchars($currentAdmin['username'] ?? 'Admin'); ?>! Here's your ColdConsult system overview.</p>
        </div>
        <div class="small" style="color: #6c757d !important;">
            <i class="fas fa-clock me-1"></i>
            <?php echo date('M j, Y g:i A'); ?>
        </div>
    </div>

<?php
// Get dashboard statistics
$stats = $admin->getDashboardStats();
$product = new Product();

// Get additional statistics
$totalServices = $product->getTotalCount();
$activeServices = count($product->getActiveProducts());
$categories = $product->getCategoriesWithDetails();
$recentServices = $product->getAllServices();
if (is_array($recentServices)) {
    // Limit to 4 products for better space utilization
    $recentServices = array_slice($recentServices, 0, 4);
} else {
    $recentServices = [];
}
?>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-gradient rounded-3 p-3 text-white">
                                <i class="fas fa-box fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small fw-semibold text-uppercase" style="color: #6c757d !important;">Total Products</div>
                            <div class="fs-2 fw-bold" style="color: #0d6efd !important;"><?php echo number_format($totalServices); ?></div>
                            <div class="small" style="color: #6c757d !important;">
                                <i class="fas fa-info-circle me-1"></i>
                                Total registered products
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-gradient rounded-3 p-3 text-white">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small fw-semibold text-uppercase" style="color: #6c757d !important;">Active Products</div>
                            <div class="fs-2 fw-bold" style="color: #198754 !important;"><?php echo number_format($activeServices); ?></div>
                            <div class="small" style="color: #6c757d !important;">
                                <i class="fas fa-check-circle me-1"></i>
                                Currently active products
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-gradient rounded-3 p-3 text-white">
                                <i class="fas fa-tags fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small fw-semibold text-uppercase" style="color: #6c757d !important;">Categories</div>
                            <div class="fs-2 fw-bold" style="color: #fd7e14 !important;"><?php echo number_format(count($categories)); ?></div>
                            <div class="small" style="color: #6c757d !important;">
                                <i class="fas fa-tags me-1"></i>
                                Product categories
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-gradient rounded-3 p-3 text-white">
                                <i class="fas fa-pause-circle fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small fw-semibold text-uppercase" style="color: #6c757d !important;">Inactive Products</div>
                            <div class="fs-2 fw-bold" style="color: #0dcaf0 !important;"><?php echo number_format($totalServices - $activeServices); ?></div>
                            <div class="small" style="color: #6c757d !important;">
                                <i class="fas fa-pause-circle me-1"></i>
                                Inactive products
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="row g-4">
        <!-- Recent Products -->
        <div class="col-lg-8">
            <div class="card border-primary">
                <div class="card-header bg-primary bg-gradient text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-box me-2"></i>Recent Products
                        </h5>
                        <a href="manage-products.php" class="btn btn-light btn-sm">
                            <i class="fas fa-list me-1"></i>View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recentServices)): ?>
                        <div class="table-responsive recent-products-table">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Product</th>
                                        <th class="border-0">Category</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0">Created</th>
                                        <th class="border-0">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentServices as $service_item): ?>
                                        <tr>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($service_item['image_path'])): ?>
                                                        <?php $base = defined('SITE_URL') ? rtrim(str_replace('/public','', SITE_URL), '/') : ''; ?>
                                                        <img src="<?php echo $base . '/' . ltrim($service_item['image_path'], '/'); ?>" 
                                                             class="rounded me-3" style="width: 45px; height: 45px; object-fit: cover;" alt="Product">
                                                    <?php else: ?>
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="fw-semibold" style="color: #212529 !important;"><?php echo htmlspecialchars($service_item['title']); ?></div>
                                                        <div class="small" style="color: #6c757d !important;">
                                                            <?php echo htmlspecialchars(substr($service_item['description'], 0, 50)) . '...'; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge bg-secondary">
                                                    <?php 
                                                    $categoryName = 'Uncategorized';
                                                    foreach ($categories as $cat) {
                                                        if ($cat['id'] == $service_item['category_id']) {
                                                            $categoryName = $cat['name'];
                                                            break;
                                                        }
                                                    }
                                                    echo htmlspecialchars($categoryName);
                                                    ?>
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge <?php echo $service_item['status'] === 'active' ? 'bg-success' : 'bg-warning'; ?>">
                                                    <?php echo ucfirst($service_item['status']); ?>
                                                </span>
                                            </td>
                                            <td class="align-middle small" style="color: #6c757d !important;">
                                                <?php echo date('M j, Y', strtotime($service_item['created_at'])); ?>
                                            </td>
                                            <td class="align-middle">
                                                <div class="btn-group" role="group">
                                                    <a href="view-product.php?id=<?php echo $service_item['id']; ?>" 
                                                       class="btn btn-outline-primary btn-sm" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="edit-product.php?id=<?php echo $service_item['id']; ?>" 
                                                       class="btn btn-outline-secondary btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-inbox" style="font-size: 2.5rem; color: #6c757d; opacity: 0.5;"></i>
                            <p class="mt-3 mb-3" style="color: #6c757d !important;">No products found.</p>
                            <a href="add-product.php" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Add your first product
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- System Status -->
            <div class="card border-success mt-4">
                <div class="card-header bg-success bg-gradient text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-heartbeat me-2"></i>System Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center p-2 bg-light rounded system-status-item">
                            <div class="flex-shrink-0">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center system-status-icon" style="width: 35px; height: 35px;">
                                    <i class="fas fa-database text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 fw-semibold system-status-title" style="color: #198754 !important;">Database</h6>
                                <small style="color: #6c757d !important;">Connected & Active</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-2 bg-light rounded system-status-item">
                            <div class="flex-shrink-0">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center system-status-icon" style="width: 35px; height: 35px;">
                                    <i class="fas fa-folder text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 fw-semibold system-status-title" style="color: #198754 !important;">File System</h6>
                                <small style="color: #6c757d !important;">Working Properly</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-2 bg-light rounded system-status-item">
                            <div class="flex-shrink-0">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center system-status-icon" style="width: 35px; height: 35px;">
                                    <i class="fas fa-shield-alt text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 fw-semibold system-status-title" style="color: #198754 !important;">Admin Panel</h6>
                                <small style="color: #6c757d !important;">Online & Secure</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Sidebar Content -->
        <div class="col-lg-4">
            <div class="row g-4">
                <!-- Quick Actions -->
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary bg-gradient text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-bolt me-2"></i>Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-3">
                                <a href="add-product.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add New Product
                                </a>
                                <a href="manage-categories.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-tags me-2"></i>Manage Categories
                                </a>
                                <a href="../index.php" class="btn btn-outline-info" target="_blank">
                                    <i class="fas fa-external-link-alt me-2"></i>View Website
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Categories Overview -->
                <div class="col-12">
                    <div class="card border-warning">
                        <div class="card-header bg-warning bg-gradient text-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-tags me-2"></i>Categories
                                </h5>
                                <a href="manage-categories.php" class="btn btn-light btn-sm">
                                    <i class="fas fa-cog me-1"></i>Manage
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($categories)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach (array_slice($categories, 0, 5) as $category): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                            <div>
                                                <div class="fw-semibold" style="color: #212529 !important;"><?php echo htmlspecialchars($category['name']); ?></div>
                                                <div class="small" style="color: #6c757d !important;">
                                                    <?php echo $category['service_count']; ?> products
                                                </div>
                                            </div>
                                            <?php if (!empty($category['icon'])): ?>
                                                <i class="<?php echo htmlspecialchars($category['icon']); ?> text-warning fs-5"></i>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-tags" style="font-size: 2rem; color: #6c757d; opacity: 0.5;"></i>
                                    <p class="mt-2 mb-3" style="color: #6c757d !important;">No categories yet.</p>
                                    <a href="manage-categories.php" class="btn btn-warning btn-sm">
                                        <i class="fas fa-plus me-1"></i>Add Category
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- System Info -->
                <div class="col-12">
                    <div class="card border-info">
                        <div class="card-header bg-info bg-gradient text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-server me-2"></i>System Info
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="small" style="color: #6c757d !important;">PHP Version</div>
                                    <div class="fw-semibold" style="color: #0dcaf0 !important;"><?php echo PHP_VERSION; ?></div>
                                </div>
                                <div class="col-6">
                                    <div class="small" style="color: #6c757d !important;">Admin User</div>
                                    <div class="fw-semibold" style="color: #0dcaf0 !important;"><?php echo htmlspecialchars($currentAdmin['username'] ?? 'Admin'); ?></div>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="small" style="color: #6c757d !important;">Last Login</div>
                                    <div class="fw-semibold" style="color: #0dcaf0 !important;">
                                        <?php
                                        $lastLogin = (isset($currentAdmin['last_login']) && $currentAdmin['last_login'])
                                            ? date('M j, Y g:i A', strtotime($currentAdmin['last_login']))
                                            : 'First time';
                                        echo $lastLogin;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>