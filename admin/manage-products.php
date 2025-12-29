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
$message = '';

// Handle AJAX requests
if (isset($_POST['action'])) {
    // Log the incoming request for debugging
    error_log("AJAX Request received: " . json_encode($_POST));
    
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        error_log("Delete request for ID: $id");
        
        if ($product->delete($id)) {
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error deleting product']);
        }
        exit;
    }
    
    if ($_POST['action'] === 'toggle_status' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        error_log("Toggle status request for ID: $id");
        
        $currentService = $product->getById($id);
        if ($currentService) {
            // Use the status parameter sent from frontend if available
            if (isset($_POST['status'])) {
                $newStatus = ($_POST['status'] === 'active') ? 'active' : 'inactive';
                error_log("Status from frontend: " . $_POST['status'] . " -> $newStatus");
            } else {
                // Fallback to toggling based on current status
                $currentStatus = $currentService['status'] ?? $currentService['is_active'] ?? 0;
                if (is_numeric($currentStatus)) {
                    $newStatus = $currentStatus ? 'inactive' : 'active';
                } else {
                    $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';
                }
                error_log("Current status: $currentStatus -> $newStatus");
            }
            
            if ($product->updateStatus($id, $newStatus)) {
                error_log("Status updated successfully to: $newStatus");
                echo json_encode(['success' => true, 'message' => 'Product status updated', 'new_status' => $newStatus]);
            } else {
                error_log("Failed to update status");
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error updating product status']);
            }
        } else {
            error_log("Product not found for ID: $id");
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Product not found']);
        }
        exit;
    }
    
    // Keep backward compatibility for old parameter names
    if ($_POST['action'] === 'delete' && isset($_POST['service_id'])) {
        $id = intval($_POST['service_id']);
        if ($product->delete($id)) {
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error deleting product']);
        }
        exit;
    }
    
    if ($_POST['action'] === 'toggle_status' && isset($_POST['service_id'])) {
        $id = intval($_POST['service_id']);
        $currentService = $product->getById($id);
        if ($currentService) {
            // Toggle current status
            $currentStatus = $currentService['status'] ?? $currentService['is_active'] ?? 0;
            if (is_numeric($currentStatus)) {
                $newStatus = $currentStatus ? 'inactive' : 'active';
            } else {
                $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';
            }
            
            if ($product->updateStatus($id, $newStatus)) {
                echo json_encode(['success' => true, 'message' => 'Product status updated', 'new_status' => $newStatus]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error updating product status']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Product not found']);
        }
        exit;
    }
}

// Handle form submissions
if (isset($_POST['delete_service'])) {
    $id = $_POST['service_id'];
    if ($product->delete($id)) {
        $message = '<div class="alert alert-success">Product deleted successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Error deleting product.</div>';
    }
}

if (isset($_POST['toggle_service'])) {
    $id = intval($_POST['service_id']);
    $status = ($_POST['toggle_service'] === 'activate') ? 1 : 0;
    if ($product->setServiceStatus($id, $status)) {
        $message = '<div class="alert alert-success">Product status updated!</div>';
    } else {
        $message = '<div class="alert alert-danger">Error updating status.</div>';
    }
}

// Get data
$services = $product->getAllServices();
$categories = $product->getCategoriesWithDetails();
?>

<?php include 'includes/admin-header.php'; ?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- DataTables Bootstrap CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* Custom Professional Styles */
:root {
    --primary-color: #0d6efd;
    --success-color: #198754;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --info-color: #0dcaf0;
    --secondary-color: #6c757d;
    --light-color: #f8f9fa;
    --dark-color: #212529;
}

.page-header {
    background: linear-gradient(135deg, var(--primary-color), #4c63d2);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 0 0 15px 15px;
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.page-header p {
    margin-bottom: 1rem;
    opacity: 0.9;
}

.stats-cards {
    margin-top: 1.5rem;
}

.stat-card {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 10px;
    padding: 1rem;
    text-align: center;
    backdrop-filter: blur(10px);
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    display: block;
}

.stat-label {
    font-size: 0.875rem;
    opacity: 0.8;
}

.search-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.search-input {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    padding-left: 2.5rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.search-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.filter-select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.filter-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.btn-professional {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
}

.btn-professional:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-decoration: none;
}

.btn-primary-custom {
    background: var(--primary-color);
    color: white;
}

.btn-success-custom {
    background: var(--success-color);
    color: white;
}

.btn-outline-custom {
    background: transparent;
    border: 2px solid rgba(255, 255, 255, 0.8);
    color: white;
}

.btn-outline-custom:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: white;
    color: white;
    transform: translateY(-1px);
}

.data-table-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.table-header {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    padding: 1.5rem;
    border-bottom: 1px solid #dee2e6;
}

.table-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--dark-color);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active {
    background: rgba(25, 135, 84, 0.1);
    color: var(--success-color);
}

.status-inactive {
    background: rgba(220, 53, 69, 0.1);
    color: var(--danger-color);
}

.category-tag {
    background: rgba(13, 110, 253, 0.1);
    color: var(--primary-color);
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}

.action-btn {
    padding: 0.5rem;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    margin: 0 0.125rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
}

.action-btn:hover {
    transform: translateY(-1px);
}

.action-view {
    background: rgba(108, 117, 125, 0.1);
    color: var(--secondary-color);
}

.action-view:hover {
    background: var(--secondary-color);
    color: white;
}

.action-edit {
    background: rgba(13, 110, 253, 0.1);
    color: var(--primary-color);
}

.action-edit:hover {
    background: var(--primary-color);
    color: white;
}

.action-toggle {
    background: rgba(255, 193, 7, 0.1);
    color: var(--warning-color);
}

.action-toggle:hover {
    background: var(--warning-color);
    color: var(--dark-color);
}

.action-delete {
    background: rgba(220, 53, 69, 0.1);
    color: var(--danger-color);
}

.action-delete:hover {
    background: var(--danger-color);
    color: white;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--secondary-color);
}

.empty-state i {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--dark-color);
}

/* DataTables Customization */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    margin: 1rem 0;
}

.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    border: 2px solid #e9ecef;
    border-radius: 6px;
    padding: 0.5rem;
}

.page-link {
    border-radius: 6px;
    margin: 0 2px;
    border: 2px solid #e9ecef;
}

.page-link:hover {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header h1 {
        font-size: 1.5rem;
    }
    
    .stats-cards {
        margin-top: 1rem;
    }
    
    .stat-card {
        margin-bottom: 1rem;
    }
    
    .search-card {
        padding: 1rem;
    }
    
    .btn-professional {
        width: 100%;
        margin-bottom: 0.5rem;
        justify-content: center;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .action-btn {
        width: 28px;
        height: 28px;
        font-size: 0.75rem;
    }
}

@media (max-width: 576px) {
    .page-header {
        padding: 1.5rem 0;
    }
    
    .page-header h1 {
        font-size: 1.25rem;
    }
    
    .stats-cards .col-6 {
        margin-bottom: 1rem;
    }
    
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .table td,
    .table th {
        padding: 0.5rem 0.25rem;
    }
    
    .btn-professional {
        padding: 0.6rem 1rem;
        font-size: 0.875rem;
    }
}
</style>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1>
                        <i class="fas fa-boxes me-3"></i>
                        Manage Products
                    </h1>
                    <p class="mb-0">
                        Efficiently manage your ColdConsult products with advanced tools and filters
                    </p>
                    <!-- Stats Cards -->
                    <div class="stats-cards d-none d-md-block">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <span class="stat-number"><?php echo count($services); ?></span>
                                    <span class="stat-label">Total Products</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <span class="stat-number"><?php echo count(array_filter($services, function($s) { return $s['is_active']; })); ?></span>
                                    <span class="stat-label">Active Products</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <span class="stat-number"><?php echo count($categories); ?></span>
                                    <span class="stat-label">Categories</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="dashboard.php" class="btn-professional btn-outline-custom me-2">
                        <i class="fas fa-arrow-left"></i>
                        <span class="d-none d-sm-inline">Back to Dashboard</span>
                    </a>
                    <a href="add-product.php" class="btn-professional btn-success-custom">
                        <i class="fas fa-plus"></i>
                        <span class="d-none d-sm-inline">Add Product</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if ($message): ?>
        <div class="alert alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Search and Filters -->
    <div class="search-card">
        <div class="row g-3 align-items-center">
            <div class="col-lg-6">
                <div class="position-relative">
                    <i class="fas fa-search position-absolute" style="left: 0.75rem; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                    <input type="text" 
                           class="form-control search-input" 
                           id="productSearch" 
                           placeholder="Search products by title, description, or category...">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-2">
                    <div class="col-md-4">
                        <select class="form-select filter-select" id="categoryFilter">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['name']); ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select filter-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Inactive Only</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <a href="manage-categories.php" class="btn-professional btn-primary-custom w-100">
                            <i class="fas fa-tags"></i>
                            <span class="d-none d-lg-inline">Categories</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="data-table-card">
        <div class="table-header">
            <h2 class="table-title">
                <i class="fas fa-list text-primary"></i>
                Products Directory
                <span class="badge bg-primary rounded-pill ms-2"><?php echo count($services); ?></span>
            </h2>
        </div>
        <div class="table-responsive">
            <?php if (!empty($services)): ?>
                <table class="table table-hover mb-0" id="productsTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th width="35%">Product Details</th>
                            <th width="15%">Category</th>
                            <th width="10%">Price</th>
                            <th width="10%">Status</th>
                            <th width="15%">Created Date</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $svc): ?>
                            <tr data-product-id="<?php echo $svc['id']; ?>">
                                <td>
                                    <input type="checkbox" class="form-check-input product-checkbox" value="<?php echo $svc['id']; ?>">
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($svc['title']); ?></h6>
                                        <p class="mb-0 text-muted small">
                                            <?php echo substr(htmlspecialchars($svc['description']), 0, 100) . (strlen($svc['description']) > 100 ? '...' : ''); ?>
                                        </p>
                                        <?php if (!empty($svc['image_path'])): ?>
                                            <small class="text-info">
                                                <i class="fas fa-image me-1"></i>Has Image
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($svc['category_name'])): ?>
                                        <span class="category-tag"><?php echo htmlspecialchars($svc['category_name']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Uncategorized</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($svc['price']) && $svc['price'] !== null && $svc['price'] !== ''): ?>
                                        <span class="badge bg-info">₹<?php echo number_format((float)$svc['price'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $svc['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <i class="fas fa-<?php echo $svc['is_active'] ? 'check-circle' : 'times-circle'; ?> me-1"></i>
                                        <?php echo $svc['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        <?php echo date('M j, Y', strtotime($svc['created_at'])); ?>
                                        <br>
                                        <span class="text-xs"><?php echo date('g:i A', strtotime($svc['created_at'])); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <a href="view-product.php?id=<?php echo $svc['id']; ?>" 
                                           class="action-btn action-view" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="edit-product.php?id=<?php echo $svc['id']; ?>" 
                                           class="action-btn action-edit" 
                                           title="Edit Product">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button onclick="toggleProductStatus(<?php echo $svc['id']; ?>, <?php echo $svc['is_active'] ? 'false' : 'true'; ?>)" 
                                                class="action-btn action-toggle" 
                                                title="<?php echo $svc['is_active'] ? 'Deactivate' : 'Activate'; ?> Product">
                                            <i class="fas fa-<?php echo $svc['is_active'] ? 'toggle-off' : 'toggle-on'; ?>"></i>
                                        </button>
                                        
                                        <button onclick="deleteProduct(<?php echo $svc['id']; ?>, '<?php echo htmlspecialchars($svc['title'], ENT_QUOTES); ?>')" 
                                                class="action-btn action-delete" 
                                                title="Delete Product">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Bulk Actions -->
                <div class="d-flex justify-content-between align-items-center p-3 border-top" id="bulkActionsBar" style="display: none;">
                    <div>
                        <span class="text-muted me-3" id="selectedCount">0 selected</span>
                        <button class="btn btn-sm btn-warning me-2" onclick="bulkToggleStatus()">
                            <i class="fas fa-toggle-on me-1"></i>
                            Toggle Status
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="bulkDelete()">
                            <i class="fas fa-trash-alt me-1"></i>
                            Delete Selected
                        </button>
                    </div>
                    <div class="text-muted">
                        Showing <?php echo count($services); ?> of <?php echo count($services); ?> products
                    </div>
                </div>
                
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-boxes"></i>
                    <h3>No Products Found</h3>
                    <p>Start building your ColdConsult business by adding your first product</p>
                    <a href="add-product.php" class="btn-professional btn-primary-custom">
                        <i class="fas fa-plus me-2"></i>
                        Add Your First Product
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function() {
    // Track custom search functions
    let customSearchFunctions = [];
    
    // Initialize DataTable
    const productsTable = $('#productsTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        order: [[5, 'desc']], // Sort by created date
        columnDefs: [
            { orderable: false, targets: [0, 6] },
            { searchable: false, targets: [0, 6] }
        ],
        language: {
            search: "",
            searchPlaceholder: "Search products...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ products",
            infoEmpty: "No products available",
            infoFiltered: "(filtered from _MAX_ total products)"
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
    });

    // Hide default search and use custom
    $('.dataTables_filter').hide();
    
    // Custom search
    $('#productSearch').on('keyup', function() {
        productsTable.search(this.value).draw();
    }); 
    $('#categoryFilter').on('change', function() {
        productsTable.column(2).search(this.value).draw();
    });
    $('#statusFilter').on('change', function() {
        const selectedStatus = this.value;
        customSearchFunctions.forEach(func => {
            const index = $.fn.dataTable.ext.search.indexOf(func);
            if (index > -1) {
                $.fn.dataTable.ext.search.splice(index, 1);
            }
        });
        customSearchFunctions = [];
        
        if (selectedStatus !== '') {
            const customFilter = function(settings, data, dataIndex) {
                const statusColumn = data[4];
                
                if (selectedStatus === 'active') {
                    return statusColumn.includes('Active') && !statusColumn.includes('Inactive');
                } else if (selectedStatus === 'inactive') {
                    return statusColumn.includes('Inactive');
                }
                
                return true;
            };
            
            $.fn.dataTable.ext.search.push(customFilter);
            customSearchFunctions.push(customFilter);
        }
        
        productsTable.draw();
    });

    // Checkbox functionality
    $('#selectAll').on('change', function() {
        $('.product-checkbox').prop('checked', this.checked);
        updateBulkActions();
    });

    $(document).on('change', '.product-checkbox', function() {
        updateBulkActions();
        const totalCheckboxes = $('.product-checkbox').length;
        const checkedCheckboxes = $('.product-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    function updateBulkActions() {
        const checkedBoxes = $('.product-checkbox:checked');
        const bulkActionsBar = $('#bulkActionsBar');
        const selectedCount = $('#selectedCount');
        
        if (checkedBoxes.length > 0) {
            bulkActionsBar.show();
            selectedCount.text(checkedBoxes.length + ' selected');
        } else {
            bulkActionsBar.hide();
        }
    }
});

// Toggle product status
function toggleProductStatus(productId, newStatus) {
    const statusText = newStatus === 'true' ? 'activate' : 'deactivate';
    
    if (!confirm(`Are you sure you want to ${statusText} this product?`)) {
        return;
    }

    $.ajax({
        url: window.location.href,
        method: 'POST',
        data: {
            action: 'toggle_status',
            service_id: productId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                window.location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error updating product status. Please try again.');
        }
    });
}

// Delete product
function deleteProduct(productId, productName) {
    if (!confirm(`Are you sure you want to delete "${productName}"?\n\nThis action cannot be undone.`)) {
        return;
    }
    
    const confirmText = prompt('Type "DELETE" to confirm:');
    if (confirmText !== 'DELETE') {
        alert('Deletion cancelled. You must type "DELETE" exactly.');
        return;
    }

    $.ajax({
        url: window.location.href,
        method: 'POST',
        data: {
            action: 'delete',
            service_id: productId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                window.location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error deleting product. Please try again.');
        }
    });
}

// Bulk operations
function bulkToggleStatus() {
    const selectedProducts = $('.product-checkbox:checked');
    if (selectedProducts.length === 0) return;
    
    if (!confirm(`Toggle status for ${selectedProducts.length} selected products?`)) {
        return;
    }
    
    selectedProducts.each(function() {
        const productId = $(this).val();
        const row = $(this).closest('tr');
        const isActive = row.find('.status-active').length > 0;
        toggleProductStatus(productId, !isActive);
    });
}

function bulkDelete() {
    const selectedProducts = $('.product-checkbox:checked');
    if (selectedProducts.length === 0) return;
    
    if (!confirm(`Are you sure you want to delete ${selectedProducts.length} selected products?\n\nThis action cannot be undone.`)) {
        return;
    }
    
    const confirmText = prompt('Type "DELETE ALL" to confirm:');
    if (confirmText !== 'DELETE ALL') {
        alert('Bulk deletion cancelled.');
        return;
    }
    
    selectedProducts.each(function() {
        const productId = $(this).val();
        const productName = $(this).closest('tr').find('h6').text();
        deleteProduct(productId, productName);
    });
}

// Auto-hide alerts
setTimeout(() => {
    $('.alert').fadeOut(500);
}, 5000);
</script>

<?php include 'includes/admin-footer.php'; ?>