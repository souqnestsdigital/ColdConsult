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
$errors = '';

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$product_id) {
    header('Location: manage-products.php?error=invalid_id');
    exit;
}

// Get current product data
$current_product = $product->getById($product_id);
if (!$current_product) {
    header('Location: manage-products.php?error=product_not_found');
    exit;
}

// Get all categories for dropdown
$categories = $category->getAllCategories();

// Handle form submission
if ($_POST) {
    $title = trim($_POST['title'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $features = trim($_POST['features'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $status = $_POST['status'] ?? 'active';
    
    // Validation
    if (empty($title)) {
        $errors[] = "Product title is required.";
    } elseif (strlen($title) < 3) {
        $errors[] = "Product title must be at least 3 characters.";
    } elseif (strlen($title) > 255) {
        $errors[] = "Product title must not exceed 255 characters.";
    }
    
    if (empty($description)) {
        $errors[] = "Product description is required.";
    } elseif (strlen($description) < 10) {
        $errors[] = "Product description must be at least 10 characters.";
    }
    
    if ($category_id === 0) {
        $errors[] = "Please select a category.";
    }
    
    if (!empty($price) && !is_numeric($price)) {
        $errors[] = "Price must be a valid number.";
    }
    
    // Handle image upload
    $image_path = $current_product['image_path'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . $product_id . DIRECTORY_SEPARATOR;
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 250 * 1024; // 250KB
        
        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $errors[] = "Invalid file type. Please upload JPG, PNG, GIF, or WebP images.";
        } elseif ($_FILES['image']['size'] > $max_size) {
           $errors[] = "File too large. Maximum size is 250KB (" . number_format($_FILES['image']['size'] / 1024, 1) . "KB uploaded).";
        } else {
            $original_name = $_FILES['image']['name'];
            $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
            $base_name = pathinfo($original_name, PATHINFO_FILENAME);
            
            // Sanitize filename
            $safe_name = preg_replace('/[^a-zA-Z0-9\-_]/', '-', $base_name);
            $safe_name = preg_replace('/-+/', '-', $safe_name);
            $safe_name = trim($safe_name, '-');
            
            if (empty($safe_name)) {
                $safe_name = 'product-image';
            }
            
            $filename = $safe_name . '.' . strtolower($file_extension);
            $upload_path = $upload_dir . $filename;
            
            // Add timestamp if file exists
            if (file_exists($upload_path)) {
                $filename = $safe_name . '-' . time() . '.' . strtolower($file_extension);
                $upload_path = $upload_dir . $filename;
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Delete old image if it exists
                if (!empty($current_product['image_path'])) {
                    $old_path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . $product_id . DIRECTORY_SEPARATOR . basename($current_product['image_path']);
                    if (is_file($old_path)) {
                        @unlink($old_path);
                    }
                }
                $image_path = 'images/products/' . $product_id . '/' . $filename;
            } else {
                $errors[] = "Failed to upload image. Please try again.";
            }
        }
    }
    
    // Update product if no errors
    if (empty($errors)) {
        $update_data = [
            'title' => $title,
            'category_id' => $category_id,
            'description' => $description,
            'features' => $features,
            'status' => $status,
            'image_path' => $image_path
        ];
        
        // Add price if provided and valid
        if (!empty($price) && is_numeric($price)) {
            $update_data['price'] = floatval($price);
        }
        
        if ($product->update($product_id, $update_data)) {
            $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                          <i class="fas fa-check-circle me-2"></i>Product updated successfully!
                          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>';
            // Refresh current product data
            $current_product = $product->getById($product_id);
        } else {
            $errors[] = "Failed to update product. Please try again.";
        }
    }
}

$page_title = "Edit Product - " . htmlspecialchars($current_product['title']);
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

        .form-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .image-preview {
            max-width: 300px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 0.5rem;
            background: #f9fafb;
        }

        .image-preview img {
            width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            background: #f9fafb;
            transition: all 0.2s ease;
        }

        .upload-area:hover {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
        }

        .upload-area.drag-over {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.1);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1.5rem;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .required {
            color: var(--danger-color);
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 1.5rem 0;
            }
            
            .form-card {
                padding: 1.5rem;
            }
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
                                Edit Product
                            </li>
                        </ol>
                    </nav>
                    <h1 class="h2 mb-0">Edit Product</h1>
                    <p class="mb-0 opacity-75">Update product information and settings</p>
                </div>
                <div class="col-lg-6">
                    <div class="text-lg-end">
                        <a href="view-product.php?id=<?php echo $product_id; ?>" class="btn btn-light me-2">
                            <i class="fas fa-eye me-1"></i> View Product
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
        <!-- Messages -->
        <?php if ($message): ?>
            <?php echo $message; ?>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" id="productForm">
            <div class="row">
                <!-- Main Form -->
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="form-card">
                        <div class="d-flex align-items-center mb-4">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            <h5 class="mb-0">Basic Information</h5>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">
                                    Product Title <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($current_product['title']); ?>" 
                                       required maxlength="255">
                                <div class="form-text">Enter a clear, descriptive title for your product</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="category_id" class="form-label">
                                    Category <span class="required">*</span>
                                </label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" 
                                                <?php echo ($cat['id'] == $current_product['category_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                Description <span class="required">*</span>
                            </label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="4" required><?php echo htmlspecialchars($current_product['description']); ?></textarea>
                            <div class="form-text">Provide a detailed description of the product</div>
                        </div>

                        <div class="mb-3">
                            <label for="features" class="form-label">Features</label>
                            <textarea class="form-control" id="features" name="features" 
                                      rows="4" placeholder="Enter each feature on a new line"><?php echo htmlspecialchars($current_product['features'] ?? ''); ?></textarea>
                            <div class="form-text">List key features, one per line</div>
                        </div>
                    </div>

                    <!-- Pricing & Status -->
                    <div class="form-card">
                        <div class="d-flex align-items-center mb-4">
                            <i class="fas fa-cogs text-primary me-2"></i>
                            <h5 class="mb-0">Pricing & Status</h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="price" name="price" 
                                           value="<?php echo htmlspecialchars($current_product['price'] ?? ''); ?>" 
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                                <div class="form-text">Leave empty if pricing is upon request</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?php echo ($current_product['status'] === 'active') ? 'selected' : ''; ?>>
                                        Active
                                    </option>
                                    <option value="inactive" <?php echo ($current_product['status'] === 'inactive') ? 'selected' : ''; ?>>
                                        Inactive
                                    </option>
                                </select>
                                <div class="form-text">Active products are visible on the website</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Current Image -->
                    <?php if (!empty($current_product['image_path'])): ?>
                    <div class="form-card">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-image text-primary me-2"></i>
                            <h6 class="mb-0">Current Image</h6>
                        </div>
                        <div class="image-preview">
                            <img src="../../<?php echo htmlspecialchars($current_product['image_path']); ?>" 
                                 alt="Current product image"
                                 onerror="this.src='../../public/images/products/coldstoragehero.jpg'">
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Upload New Image -->
                    <div class="form-card">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-upload text-primary me-2"></i>
                            <h6 class="mb-0">Product Image</h6>
                        </div>

                        <div class="upload-area" id="uploadArea">
                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                            <p class="mb-2">Drag & drop an image here or</p>
                            <input type="file" class="form-control" id="image" name="image" 
                                   accept="image/*" style="display: none;">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('image').click()">
                                Choose File
                            </button>
                            <div class="form-text mt-2">
                                JPG, PNG, GIF, WebP up to 250KB
                            </div>
                        </div>

                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <img id="previewImg" class="image-preview" alt="Preview">
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="clearImagePreview()">
                                <i class="fas fa-times me-1"></i> Remove
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="form-card">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-save text-primary me-2"></i>
                            <h6 class="mb-0">Actions</h6>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Product
                            </button>
                            <a href="view-product.php?id=<?php echo $product_id; ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-eye me-1"></i> Preview Changes
                            </a>
                            <a href="manage-products.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Image upload handling
        const fileInput = document.getElementById('image');
        const uploadArea = document.getElementById('uploadArea');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');

        // File input change handler
        fileInput.addEventListener('change', function(e) {
            handleFileSelect(e.target.files[0]);
        });

        // Drag and drop handlers
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('drag-over');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelect(files[0]);
            }
        });

        function handleFileSelect(file) {
            if (!file) return;

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (JPG, PNG, GIF, WebP)');
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);

            // Set the file to the input
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
        }

        function clearImagePreview() {
            imagePreview.style.display = 'none';
            fileInput.value = '';
        }

        // Form validation
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            const category = document.getElementById('category_id').value;

            if (!title || title.length < 3) {
                e.preventDefault();
                alert('Product title must be at least 3 characters long');
                document.getElementById('title').focus();
                return;
            }

            if (!description || description.length < 10) {
                e.preventDefault();
                alert('Product description must be at least 10 characters long');
                document.getElementById('description').focus();
                return;
            }

            if (!category) {
                e.preventDefault();
                alert('Please select a category');
                document.getElementById('category_id').focus();
                return;
            }

            // Show loading state
            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';
            submitBtn.disabled = true;

            // Allow form to submit
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });

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

        // Character counters
        const titleInput = document.getElementById('title');
        const descriptionInput = document.getElementById('description');

        function updateCharCount(input, maxLength) {
            const current = input.value.length;
            const remaining = maxLength - current;
            
            let countElement = input.parentNode.querySelector('.char-count');
            if (!countElement) {
                countElement = document.createElement('div');
                countElement.className = 'form-text char-count';
                input.parentNode.appendChild(countElement);
            }
            
            countElement.textContent = `${current}/${maxLength} characters`;
            countElement.style.color = remaining < 20 ? '#ef4444' : '#64748b';
        }

        titleInput.addEventListener('input', () => updateCharCount(titleInput, 255));
        descriptionInput.addEventListener('input', () => updateCharCount(descriptionInput, 1000));
    </script>
</body>
</html>