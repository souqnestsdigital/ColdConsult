<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/Admin.php';
require_once '../../classes/Category.php';

$admin = new Admin();
if (!$admin->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$category = new Category($db);
$message = '';
$errors = [];

// Handle AJAX requests
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        try {
            if ($category->deleteCategory($id)) {
                echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    if ($_POST['action'] === 'toggle_status' && isset($_POST['id'])) {
        header('Content-Type: application/json');
        
        $id = intval($_POST['id']);
        
        error_log("Toggle status request for category ID: " . $id);
        
        try {
            // Get current status first
            $currentCategory = $category->getCategoryById($id);
            if (!$currentCategory) {
                error_log("Category not found for ID: " . $id);
                echo json_encode(['success' => false, 'message' => 'Category not found']);
                exit;
            }
            
            error_log("Current category status: " . ($currentCategory['is_active'] ?? 'null'));
            
            // Toggle the status (if active=1, make it 0; if active=0, make it 1)
            $newStatus = ($currentCategory['is_active'] == 1) ? 0 : 1;
            
            error_log("New status will be: " . $newStatus);
            
            if ($category->toggleStatus($id, $newStatus)) {
                error_log("Status toggle successful for category ID: " . $id);
                $statusText = $newStatus ? 'activated' : 'deactivated';
                echo json_encode([
                    'success' => true, 
                    'message' => "Category successfully {$statusText}!",
                    'new_status' => $newStatus,
                    'category_id' => $id
                ]);
            } else {
                error_log("Status toggle failed for category ID: " . $id);
                echo json_encode(['success' => false, 'message' => 'Failed to update category status']);
            }
        } catch (Exception $e) {
            error_log("Exception in toggle status: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}

// Handle form submissions
if ($_POST && !isset($_POST['action'])) {
    $form_action = $_POST['form_action'] ?? '';
    
    if ($form_action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $icon = trim($_POST['icon'] ?? '');
        
        // Validation
        if (empty($name)) {
            $errors[] = "Category name is required.";
        } elseif (strlen($name) < 2) {
            $errors[] = "Category name must be at least 2 characters.";
        } elseif (strlen($name) > 100) {
            $errors[] = "Category name must not exceed 100 characters.";
        }
        
        if (empty($errors)) {
            try {
                if ($category->createCategory($name, $description, $icon)) {
                    $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                  <i class="fas fa-check-circle me-2"></i>Category created successfully!
                                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>';
                } else {
                    $errors[] = "Failed to create category. Please try again.";
                }
            } catch (Exception $e) {
                $errors[] = "Error: " . $e->getMessage();
            }
        }
    }
    
    if ($form_action === 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $icon = trim($_POST['icon'] ?? '');
        
        // Validation
        if ($id <= 0) {
            $errors[] = "Invalid category ID.";
        }
        if (empty($name)) {
            $errors[] = "Category name is required.";
        } elseif (strlen($name) < 2) {
            $errors[] = "Category name must be at least 2 characters.";
        } elseif (strlen($name) > 100) {
            $errors[] = "Category name must not exceed 100 characters.";
        }
        
        if (empty($errors)) {
            try {
                if ($category->updateCategory($id, $name, $description, $icon)) {
                    $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                  <i class="fas fa-check-circle me-2"></i>Category updated successfully!
                                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>';
                } else {
                    $errors[] = "Failed to update category. Please try again.";
                }
            } catch (Exception $e) {
                $errors[] = "Error: " . $e->getMessage();
            }
        }
    }
    
    // Display validation errors
    if (!empty($errors)) {
        $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:<br>
                      • ' . implode('<br>• ', $errors) . '
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
    }
}

// Get all categories
try {
    $categories = $category->getCategoriesWithServiceCount();
} catch (Exception $e) {
    $categories = [];
    $message = '<div class="alert alert-danger">Error loading categories: ' . $e->getMessage() . '</div>';
}
?>
<?php include 'includes/admin-header.php'; ?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* Professional Category Management Styles */
:root {
    --primary-color: #0d6efd;
    --success-color: #198754;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --info-color: #0dcaf0;
    --secondary-color: #6c757d;
    --light-color: #f8f9fa;
    --dark-color: #212529;
    --purple-color: #6f42c1;
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

.stats-card {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 10px;
    padding: 1.5rem;
    text-align: center;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin-bottom: 1rem;
}

.stats-card .stat-number {
    font-size: 2rem;
    font-weight: 700;
    display: block;
    margin-bottom: 0.5rem;
}

.stats-card .stat-label {
    font-size: 0.875rem;
    opacity: 0.8;
}

.stats-card .stat-icon {
    font-size: 2.5rem;
    opacity: 0.6;
    margin-bottom: 1rem;
}

.category-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 2rem;
    border: none;
}

.card-header-custom {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    padding: 1.5rem;
    border-bottom: 1px solid #dee2e6;
}

.card-title-custom {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--dark-color);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
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

.btn-purple-custom {
    background: var(--purple-color);
    color: white;
}

.btn-outline-custom {
    background: transparent;
    border: 2px solid var(--primary-color);
    color: var(--primary-color);
}

.btn-outline-custom:hover {
    background: var(--primary-color);
    color: white;
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

/* Modal Enhancements */
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.modal-header {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-bottom: 1px solid #dee2e6;
    border-radius: 15px 15px 0 0;
    padding: 1.5rem;
}

.modal-title {
    font-weight: 600;
    color: var(--dark-color);
}

.form-control, .form-select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    font-size: 1rem;
    transition: all 0.3s ease;
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
                        <i class="fas fa-tags me-3"></i>
                        Manage Categories
                    </h1>
                    <p class="mb-0">
                        Organize and manage your product categories efficiently
                    </p>
                    <!-- Stats Cards -->
                    <div class="row g-3 mt-3 d-none d-md-flex">
                        <div class="col-md-4">
                            <div class="stats-card">
                                <i class="fas fa-layer-group stat-icon"></i>
                                <span class="stat-number"><?php echo count($categories); ?></span>
                                <span class="stat-label">Total Categories</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card">
                                <i class="fas fa-check-circle stat-icon"></i>
                                <span class="stat-number">
                                    <?php echo array_reduce($categories, function($count, $cat) { 
                                        return ($cat['is_active'] ?? 1) ? $count + 1 : $count; 
                                    }, 0); ?>
                                </span>
                                <span class="stat-label">Active Categories</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card">
                                <i class="fas fa-box stat-icon"></i>
                                <span class="stat-number">
                                    <?php echo array_sum(array_column($categories, 'service_count')); ?>
                                </span>
                                <span class="stat-label">Total Products</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="dashboard.php" class="btn-professional btn-purple-custom me-2">
                        <i class="fas fa-arrow-left"></i>
                        <span class="d-none d-sm-inline">Back to Dashboard</span>
                    </a>
                    <button type="button" class="btn-professional btn-success-custom" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus"></i>
                        <span class="d-none d-sm-inline">Add Category</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if ($message): ?>
        <div class="row">
            <div class="col-12">
                <?php echo $message; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Search Card -->
    <div class="search-card">
        <div class="row g-3 align-items-center">
            <div class="col-lg-8">
                <div class="position-relative">
                    <i class="fas fa-search position-absolute" style="left: 0.75rem; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                    <input type="text" 
                           class="form-control search-input" 
                           id="categorySearch" 
                           placeholder="Search categories by name or description...">
                </div>
            </div>
            <div class="col-lg-4">
                <select class="form-select" id="statusFilter">
                    <option value="">All Categories</option>
                    <option value="active">Active Only</option>
                    <option value="inactive">Inactive Only</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="category-card">
        <div class="card-header-custom">
            <h2 class="card-title-custom">
                <i class="fas fa-list text-primary"></i>
                Categories Directory
                <span class="badge bg-primary rounded-pill ms-2"><?php echo count($categories); ?></span>
            </h2>
        </div>
        <div class="table-responsive">
            <?php if (!empty($categories)): ?>
                <table class="table table-hover mb-0" id="categoriesTable">
                    <thead class="table-light">
                        <tr>
                            <th width="25%">Category Name</th>
                            <th width="35%">Description</th>
                            <th width="10%">Products</th>
                            <th width="10%">Status</th>
                            <th width="10%">Created</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr data-category-id="<?php echo $cat['id']; ?>" data-is-active="<?php echo $cat['is_active'] ?? 1; ?>">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($cat['icon'])): ?>
                                            <i class="<?php echo htmlspecialchars($cat['icon']); ?> me-2 text-primary"></i>
                                        <?php else: ?>
                                            <i class="fas fa-folder me-2 text-secondary"></i>
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($cat['name']); ?></h6>
                                            <!-- Debug info -->
                                            <small class="text-muted">ID: <?php echo $cat['id']; ?> | Active: <?php echo $cat['is_active'] ?? 'NULL'; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        <?php echo !empty($cat['description']) ? htmlspecialchars($cat['description']) : 'No description'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info rounded-pill">
                                        <?php echo $cat['service_count'] ?? 0; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo ($cat['is_active'] ?? 1) ? 'status-active' : 'status-inactive'; ?>">
                                        <i class="fas fa-<?php echo ($cat['is_active'] ?? 1) ? 'check-circle' : 'times-circle'; ?> me-1"></i>
                                        <?php echo ($cat['is_active'] ?? 1) ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        <?php echo isset($cat['created_at']) ? date('M j, Y', strtotime($cat['created_at'])) : 'N/A'; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <button onclick="editCategory(<?php echo $cat['id']; ?>)" 
                                                class="action-btn action-edit" 
                                                title="Edit Category">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <button onclick="toggleCategoryStatus(<?php echo $cat['id']; ?>, <?php echo ($cat['is_active'] ?? 1) ? 'false' : 'true'; ?>)" 
                                                class="action-btn action-toggle" 
                                                title="<?php echo ($cat['is_active'] ?? 1) ? 'Deactivate' : 'Activate'; ?> Category">
                                            <i class="fas fa-<?php echo ($cat['is_active'] ?? 1) ? 'toggle-off' : 'toggle-on'; ?>"></i>
                                        </button>
                                        
                                        <button onclick="deleteCategory(<?php echo $cat['id']; ?>, '<?php echo htmlspecialchars($cat['name'], ENT_QUOTES); ?>')" 
                                                class="action-btn action-delete" 
                                                title="Delete Category">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-tags"></i>
                    <h3>No Categories Found</h3>
                    <p>Start organizing your products by creating your first category</p>
                    <button type="button" class="btn-professional btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus me-2"></i>
                        Create Your First Category
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">
                    <i class="fas fa-plus me-2"></i>Add New Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="form_action" value="add">
                    
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="categoryName" name="name" required 
                               placeholder="Enter category name" maxlength="100">
                    </div>
                    
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="categoryDescription" name="description" rows="3"
                                  placeholder="Enter category description (optional)"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="categoryIcon" class="form-label">Icon Class</label>
                        <input type="text" class="form-control" id="categoryIcon" name="icon"
                               placeholder="e.g., fas fa-snowflake (Font Awesome classes)">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Use Font Awesome icon classes like "fas fa-snowflake" or "fas fa-box"
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editCategoryForm">
                <div class="modal-body">
                    <input type="hidden" name="form_action" value="edit">
                    <input type="hidden" name="id" id="editCategoryId">
                    
                    <div class="mb-3">
                        <label for="editCategoryName" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="editCategoryName" name="name" required 
                               placeholder="Enter category name" maxlength="100">
                    </div>
                    
                    <div class="mb-3">
                        <label for="editCategoryDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editCategoryDescription" name="description" rows="3"
                                  placeholder="Enter category description (optional)"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editCategoryIcon" class="form-label">Icon Class</label>
                        <input type="text" class="form-control" id="editCategoryIcon" name="icon"
                               placeholder="e.g., fas fa-snowflake (Font Awesome classes)">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Use Font Awesome icon classes like "fas fa-snowflake" or "fas fa-box"
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function() {
    // Search functionality
    $('#categorySearch').on('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        filterTable();
    });
    
    // Status filter
    $('#statusFilter').on('change', function() {
        filterTable();
    });
    
    function filterTable() {
        const searchTerm = $('#categorySearch').val().toLowerCase();
        const statusFilter = $('#statusFilter').val();
        
        $('#categoriesTable tbody tr').each(function() {
            const row = $(this);
            const name = row.find('h6').text().toLowerCase();
            const description = row.find('td:eq(1)').text().toLowerCase();
            const status = row.find('.status-active').length > 0 ? 'active' : 'inactive';
            
            let showRow = true;
            
            // Search filter
            if (searchTerm && !name.includes(searchTerm) && !description.includes(searchTerm)) {
                showRow = false;
            }
            
            // Status filter
            if (statusFilter && statusFilter !== status) {
                showRow = false;
            }
            
            row.toggle(showRow);
        });
        
        // Show/hide empty state
        const visibleRows = $('#categoriesTable tbody tr:visible').length;
        if (visibleRows === 0 && $('#categoriesTable tbody tr').length > 0) {
            if (!$('#noResultsMessage').length) {
                $('#categoriesTable').after('<div id="noResultsMessage" class="text-center py-4 text-muted"><i class="fas fa-search me-2"></i>No categories match your search criteria.</div>');
            }
        } else {
            $('#noResultsMessage').remove();
        }
    }
});

// Edit category function
function editCategory(categoryId) {
    // Find the category data from the table
    const row = $(`tr[data-category-id="${categoryId}"]`);
    const name = row.find('h6').text();
    const description = row.find('td:eq(1)').text();
    const iconClass = row.find('td:eq(0) i').attr('class');
    
    // Populate the edit modal
    $('#editCategoryId').val(categoryId);
    $('#editCategoryName').val(name);
    $('#editCategoryDescription').val(description === 'No description' ? '' : description);
    $('#editCategoryIcon').val(iconClass || '');
    
    // Show the modal
    $('#editCategoryModal').modal('show');
}

// Toggle category status
function toggleCategoryStatus(categoryId, newStatus) {
    console.log('Toggle request:', {categoryId, newStatus});
    
    const statusText = newStatus === 'true' ? 'activate' : 'deactivate';
    
    if (!confirm(`Are you sure you want to ${statusText} this category?`)) {
        return;
    }

    // Show loading state
    const row = $(`tr[data-category-id="${categoryId}"]`);
    const currentActiveStatus = row.data('is-active');
    
    console.log('Current status from data attribute:', currentActiveStatus);
    
    $.ajax({
        url: window.location.href,
        method: 'POST',
        data: {
            action: 'toggle_status',
            id: categoryId
        },
        dataType: 'json',
        beforeSend: function() {
            console.log('Sending AJAX request...');
            // Disable the button to prevent double-clicks
            row.find('.action-toggle').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        },
        success: function(response) {
            console.log('AJAX response:', response);
            if (response.success) {
                // Show success message briefly before reload
                const alertDiv = $(`
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>${response.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
                $('body').prepend(alertDiv);
                
                // Reload after short delay
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                alert('Error: ' + response.message);
                // Restore button state
                row.find('.action-toggle').prop('disabled', false).html('<i class="fas fa-toggle-on"></i>');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', {xhr, status, error});
            console.error('Response text:', xhr.responseText);
            alert('Error updating category status. Please try again.');
            // Restore button state
            row.find('.action-toggle').prop('disabled', false).html('<i class="fas fa-toggle-on"></i>');
        }
    });
}

// Delete category
function deleteCategory(categoryId, categoryName) {
    if (!confirm(`Are you sure you want to delete the category "${categoryName}"?\n\nThis action cannot be undone.`)) {
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
            id: categoryId
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
            alert('Error deleting category. Please try again.');
        }
    });
}

// Auto-hide alerts
setTimeout(() => {
    $('.alert').fadeOut(500);
}, 5000);
</script>

<?php include 'includes/admin-footer.php'; ?>