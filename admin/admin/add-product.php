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
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $features = trim($_POST['features'] ?? '');
    $price = trim($_POST['price'] ?? '');

    // Validation
    if ($title === '') $errors[] = 'Product title is required.';
    if (strlen($title) < 3) $errors[] = 'Product title must be at least 3 characters long.';
    if (strlen($title) > 200) $errors[] = 'Product title cannot exceed 200 characters.';
    if ($category_id <= 0) $errors[] = 'Please select a valid category.';
    if ($description === '') $errors[] = 'Product description is required.';
    if (strlen($description) < 10) $errors[] = 'Product description must be at least 10 characters long.';
    if ($price !== '' && (!is_numeric($price) || $price < 0)) $errors[] = 'Price must be a valid positive number.';

    // Validate single image file
    $uploadedFile = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
         $max_size = 250 * 1024; // 250KB
        
        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $errors[] = "Invalid file type. Please upload JPG, PNG, GIF, or WebP images.";
        } elseif ($_FILES['image']['size'] > $max_size) {
             $errors[] = "File too large. Maximum size is 250KB (" . number_format($_FILES['image']['size'] / 1024, 1) . "KB uploaded).";
        } else {
            // Check if it's actually an image
            $imageInfo = getimagesize($_FILES['image']['tmp_name']);
            if ($imageInfo === false) {
                $errors[] = "Uploaded file is not a valid image.";
            } else {
                $uploadedFile = [
                    'name' => $_FILES['image']['name'],
                    'type' => $_FILES['image']['type'],
                    'size' => $_FILES['image']['size'],
                    'tmp' => $_FILES['image']['tmp_name'],
                    'width' => $imageInfo[0],
                    'height' => $imageInfo[1]
                ];
            }
        }
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = "Upload error for image file.";
    }

    if (empty($errors)) {
        try {
            // Create product record first without image
            $productData = [
                'title' => $title,
                'description' => $description,
                'category_id' => $category_id,
                'features' => $features,
                'status' => 'active',
                'image_path' => '' // Empty initially
            ];
            if ($price !== '') {
                $productData['price'] = floatval($price);
            }
            
            $insertId = $product->createWithId($productData);
            if ($insertId === false) {
                throw new Exception('Failed to create product record.');
            }

            // Handle single image upload AFTER product is created
            $imagePath = null;
            if ($uploadedFile) {
                $upload_dir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . $insertId . DIRECTORY_SEPARATOR;
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $original_name = $uploadedFile['name'];
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
                
                if (move_uploaded_file($uploadedFile['tmp'], $upload_path)) {
                    $imagePath = 'images/products/' . $insertId . '/' . $filename;
                    
                    // Update product with image path
                    if (!$product->update($insertId, ['image_path' => $imagePath])) {
                        throw new Exception('Failed to update product with image path.');
                    }
                } else {
                    throw new Exception('Failed to upload image file.');
                }
            }
            
            $success = true;
            $message = "Product '{$title}' has been successfully created" .
                      ($imagePath ? " with image" : "") . ".";
            $_POST = [];
        } catch (Exception $e) {
            $errors[] = 'Error creating product: ' . $e->getMessage();
            error_log("Product creation error: " . $e->getMessage());
        }
    }
}

// Get categories for dropdown
try {
    $categories = $category->getAllCategories();
    if (empty($categories)) {
        // Fallback to direct database query
        $stmt = $db->prepare("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name ASC");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $categories = [];
    error_log("Error fetching categories: " . $e->getMessage());
}

$page_title = "Add New Product";
include 'includes/admin-header.php';
?>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
    --primary-color: #0d6efd;
    --success-color: #198754;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --info-color: #0dcaf0;
    --light-color: #f8f9fa;
    --dark-color: #212529;
    --border-color: #dee2e6;
    --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    --border-radius: 0.5rem;
}

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.page-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--border-color);
    margin-bottom: 2rem;
    padding: 2rem 0;
}

.page-header h1 {
    color: var(--dark-color);
    font-weight: 700;
    margin: 0;
}

.page-header .breadcrumb {
    background: none;
    padding: 0;
    margin: 0.5rem 0 0 0;
}

.page-header .breadcrumb-item a {
    color: var(--primary-color);
    text-decoration: none;
}

.main-content {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    padding: 2rem;
    margin-bottom: 2rem;
}

.form-section {
    background: #fff;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
}

.form-section h5 {
    color: var(--dark-color);
    font-weight: 600;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-section h5 i {
    color: var(--primary-color);
}

.form-control {
    border: 2px solid var(--border-color);
    border-radius: 0.375rem;
    padding: 0.75rem 1rem;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.form-control.is-invalid {
    border-color: var(--danger-color);
}

.form-control.is-valid {
    border-color: var(--success-color);
}

.file-upload-area {
    border: 2px dashed var(--border-color);
    border-radius: var(--border-radius);
    padding: 2rem;
    text-align: center;
    background: var(--light-color);
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.file-upload-area:hover {
    border-color: var(--primary-color);
    background: rgba(13, 110, 253, 0.05);
}

.file-upload-area.drag-over {
    border-color: var(--success-color);
    background: rgba(25, 135, 84, 0.1);
    transform: scale(1.02);
}

.upload-icon {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.upload-text {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 0.5rem;
}

.upload-subtext {
    color: #6c757d;
    font-size: 0.9rem;
}

.image-preview {
    margin-top: 1.5rem;
    padding: 1rem;
    background: #fff;
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}
    border: 1px solid var(--border-color);
}

.image-preview-item {
    position: relative;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: transform 0.2s ease;
}

.image-preview-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.image-preview-item img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    display: block;
}

.image-remove-btn {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    width: 28px;
    height: 28px;
    border: none;
    border-radius: 50%;
    background: var(--danger-color);
    color: white;
    font-size: 0.8rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.9;
    transition: all 0.2s ease;
}

.image-remove-btn:hover {
    opacity: 1;
    transform: scale(1.1);
}

.form-actions {
    background: var(--light-color);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
    border: 2px solid transparent;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-sm);
}

.btn-primary {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background: #0b5ed7;
    border-color: #0a58ca;
}

.btn-secondary {
    background: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background: #5c636a;
    border-color: #565e64;
}

.alert {
    border: none;
    border-radius: var(--border-radius);
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    font-weight: 500;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #0f5132;
    border-left: 4px solid var(--success-color);
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c2c7 100%);
    color: #842029;
    border-left: 4px solid var(--danger-color);
}

.character-counter {
    font-size: 0.8rem;
    color: #6c757d;
    text-align: right;
    margin-top: 0.25rem;
}

.required-field::after {
    content: ' *';
    color: var(--danger-color);
    font-weight: bold;
}

@media (max-width: 768px) {
    .main-content {
        padding: 1rem;
    }
    
    .form-section {
        padding: 1rem;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-actions .btn {
        width: 100%;
    }
    
    .images-preview {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 0.75rem;
    }
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-spinner {
    background: white;
    padding: 2rem;
    border-radius: var(--border-radius);
    text-align: center;
    box-shadow: var(--shadow);
}

.spinner-border {
    color: var(--primary-color);
}
</style>

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
                        <li class="breadcrumb-item active">Add New Product</li>
                    </ol>
                </nav>
                <h1><i class="fas fa-plus-circle me-2"></i>Add New Product</h1>
                <p class="mb-0 opacity-75">Create a new product with images and details</p>
            </div>
            <div class="col-lg-6">
                <div class="text-lg-end">
                    <a href="manage-products.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container-fluid">
    <div class="main-content">
        <!-- Success/Error Messages -->
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($message); ?>
                <a href="manage-products.php" class="alert-link ms-2">View all products</a>
            </div>
        <?php elseif (!empty($errors)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" id="productForm" novalidate>
            <!-- Basic Information -->
            <div class="form-section">
                <h5><i class="fas fa-info-circle"></i>Basic Information</h5>
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label required-field">Product Title</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                                   required maxlength="200" minlength="3">
                            <div class="character-counter">
                                <span id="titleCounter">0</span>/200 characters
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="category_id" class="form-label required-field">Category</label>
                            <select class="form-control" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo (int)$cat['id']; ?>" 
                                            <?php echo (isset($_POST['category_id']) && (int)$_POST['category_id'] === (int)$cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="price" class="form-label">Price (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="price" name="price" 
                                       step="0.01" min="0" max="999999.99" placeholder="0.00"
                                       value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="form-section">
                <h5><i class="fas fa-align-left"></i>Description</h5>
                <div class="mb-3">
                    <label for="description" class="form-label required-field">Product Description</label>
                    <textarea class="form-control" id="description" name="description" rows="5" 
                              required minlength="10" maxlength="2000"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    <div class="character-counter">
                        <span id="descriptionCounter">0</span>/2000 characters
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="form-section">
                <h5><i class="fas fa-list-ul"></i>Features</h5>
                <div class="mb-3">
                    <label for="features" class="form-label">Product Features (one per line)</label>
                    <textarea class="form-control" id="features" name="features" rows="4" 
                              placeholder="Energy efficient&#10;Durable construction&#10;Easy maintenance&#10;Advanced temperature control"><?php echo isset($_POST['features']) ? htmlspecialchars($_POST['features']) : ''; ?></textarea>
                    <div class="form-text">Enter each feature on a new line</div>
                </div>
            </div>

            <!-- Images -->
            <div class="form-section">
                <h5><i class="fas fa-image"></i>Product Image</h5>
                <div class="mb-3">
                    <label class="form-label">Upload Image</label>
                    <div id="uploadArea" class="file-upload-area">
                        <input type="file" id="image" name="image" accept="image/*" style="display: none;">
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-text">Click to choose image or drag & drop here</div>
                        <div class="upload-subtext">JPG, PNG, GIF, WebP up to 5MB</div>
                    </div>
                    <div id="imagePreview" class="image-preview" style="display: none;"></div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <div>
                    <a href="manage-products.php" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-plus me-1"></i> Create Product
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="mt-3">Creating product...</div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counters
    const titleInput = document.getElementById('title');
    const titleCounter = document.getElementById('titleCounter');
    const descriptionInput = document.getElementById('description');
    const descriptionCounter = document.getElementById('descriptionCounter');

    function updateCounter(input, counter) {
        counter.textContent = input.value.length;
        
        // Color coding
        const maxLength = input.getAttribute('maxlength');
        const percentage = (input.value.length / maxLength) * 100;
        
        if (percentage > 90) {
            counter.style.color = '#dc3545';
        } else if (percentage > 75) {
            counter.style.color = '#ffc107';
        } else {
            counter.style.color = '#6c757d';
        }
    }

    titleInput.addEventListener('input', () => updateCounter(titleInput, titleCounter));
    descriptionInput.addEventListener('input', () => updateCounter(descriptionInput, descriptionCounter));

    // Initialize counters
    updateCounter(titleInput, titleCounter);
    updateCounter(descriptionInput, descriptionCounter);

    // File upload functionality
    const uploadArea = document.getElementById('uploadArea');
    const imageInput = document.getElementById('image');
    const preview = document.getElementById('imagePreview');
    let selectedFile = null;

    function refreshPreview() {
        preview.innerHTML = '';
        
        if (!selectedFile) {
            preview.style.display = 'none';
            return;
        }
        
        preview.style.display = 'block';
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const item = document.createElement('div');
            item.className = 'image-preview-item';
            item.innerHTML = `
                <img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; object-fit: cover;">
                <button type="button" class="image-remove-btn" title="Remove image" aria-label="Remove image"
                        style="position: absolute; top: 5px; right: 5px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; font-size: 12px; cursor: pointer;">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            item.style.position = 'relative';
            item.style.display = 'inline-block';
            item.style.margin = '10px 0';
            
            // Add remove functionality
            item.querySelector('.image-remove-btn').addEventListener('click', function() {
                selectedFile = null;
                imageInput.value = '';
                refreshPreview();
            });
            
            preview.appendChild(item);
        };
        reader.readAsDataURL(selectedFile);
    }

    function validateAndSetFile(file) {
        const maxSize = 250 * 1024; // 250KB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        
        // Check file type
        if (!allowedTypes.includes(file.type)) {
            alert(`File "${file.name}" is not a valid image type.`);
            return false;
        }
        
        // Check file size
        if (file.size > maxSize) {
            const fileSizeKB = (file.size / 1024).toFixed(1);
            alert(`File "${file.name}" is too large (${fileSizeKB}KB). Maximum size is 250KB.`);
            return false;
        }
        
        selectedFile = file;
        refreshPreview();
        return true;
    }

    function handleExternalImage(url) {
        console.log('Handling external image:', url);
        
        // Show loading state
        uploadArea.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading image...';
        
        // Clean up URL
        url = url.trim();
        
        // Check if it's a data URL
        if (url.startsWith('data:image/')) {
            handleDataUrl(url);
            return;
        }
        
        // Try to fetch the image using a proxy approach
        fetch(url, {
            method: 'GET',
            mode: 'no-cors' // This won't give us the response but will cache it
        }).then(() => {
            // Now try to load it as an image
            const img = new Image();
            img.crossOrigin = 'anonymous';
            
            img.onload = function() {
                console.log('Image loaded successfully');
                convertImageToFile(img, url);
            };
            
            img.onerror = function() {
                console.log('Image failed to load, trying alternative method');
                // Try without crossOrigin for same-origin images
                const img2 = new Image();
                img2.onload = function() {
                    console.log('Image loaded without crossOrigin');
                    convertImageToFile(img2, url);
                };
                img2.onerror = function() {
                    console.log('All image loading methods failed');
                    handleImageError();
                };
                img2.src = url;
            };
            
            img.src = url;
        }).catch(() => {
            // If fetch fails, try direct image loading
            console.log('Fetch failed, trying direct image loading');
            const img = new Image();
            img.onload = function() {
                convertImageToFile(img, url);
            };
            img.onerror = handleImageError;
            img.src = url;
        });
    }
    
    function handleDataUrl(dataUrl) {
        console.log('Handling data URL');
        try {
            // Convert data URL to blob
            const arr = dataUrl.split(',');
            const mime = arr[0].match(/:(.*?);/)[1];
            const bstr = atob(arr[1]);
            let n = bstr.length;
            const u8arr = new Uint8Array(n);
            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }
            const blob = new Blob([u8arr], { type: mime });
            const file = new File([blob], 'external-image.' + mime.split('/')[1], { type: mime });
            
            if (validateAndSetFile(file)) {
                const dt = new DataTransfer();
                dt.items.add(file);
                imageInput.files = dt.files;
            }
        } catch (error) {
            console.error('Error processing data URL:', error);
            handleImageError();
        }
        restoreUploadArea();
    }
    
    function convertImageToFile(img, originalUrl) {
        try {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
            
            canvas.toBlob(function(blob) {
                if (blob) {
                    const filename = getFilenameFromUrl(originalUrl);
                    const file = new File([blob], filename, { type: blob.type || 'image/jpeg' });
                    
                    if (validateAndSetFile(file)) {
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        imageInput.files = dt.files;
                        console.log('Successfully converted external image to file');
                    }
                } else {
                    handleImageError();
                }
                restoreUploadArea();
            }, 'image/jpeg', 0.9);
        } catch (error) {
            console.error('Error converting image:', error);
            handleImageError();
            restoreUploadArea();
        }
    }
    
    function getFilenameFromUrl(url) {
        try {
            const urlObj = new URL(url);
            let filename = urlObj.pathname.split('/').pop();
            if (!filename || !filename.includes('.')) {
                filename = 'external-image.jpg';
            }
            // Remove query parameters
            filename = filename.split('?')[0];
            return filename;
        } catch {
            return 'external-image.jpg';
        }
    }
    
    function handleImageError() {
        alert('Could not load the external image. This might be due to:\n- CORS restrictions\n- Invalid image URL\n- Network issues\n\nTry downloading the image first and then drag the file directly.');
        restoreUploadArea();
    }

    function restoreUploadArea() {
        uploadArea.innerHTML = `
            <i class="fas fa-cloud-upload-alt mb-3" style="font-size: 3rem; color: #6b7280;"></i>
            <div class="upload-text">Click to choose image or drag & drop here</div>
            <div class="upload-subtext">PNG, JPG, GIF, WebP up to 250KB</div>
        `;
    }

    // Upload area click
    uploadArea.addEventListener('click', function(e) {
        if (!e.target.closest('.image-remove-btn')) {
            imageInput.click();
        }
    });

    // Enhanced drag and drop events
    uploadArea.addEventListener('dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.add('drag-over');
        console.log('Drag enter');
    });

    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.add('drag-over');
        // Allow all types of drops
        e.dataTransfer.dropEffect = 'copy';
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        // Only remove class if we're actually leaving the upload area
        if (!uploadArea.contains(e.relatedTarget)) {
            uploadArea.classList.remove('drag-over');
            console.log('Drag leave');
        }
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.remove('drag-over');
        
        console.log('Drop event triggered');
        console.log('DataTransfer types:', e.dataTransfer.types);
        
        // Handle file drops (local files)
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            console.log('File dropped:', e.dataTransfer.files[0]);
            validateAndSetFile(e.dataTransfer.files[0]);
            return;
        }
        
        // Try different data types for external content
        let imageUrl = null;
        
        // Method 1: Direct URL
        try {
            const urlData = e.dataTransfer.getData('text/uri-list');
            console.log('URL data:', urlData);
            if (urlData && urlData.trim()) {
                imageUrl = urlData.trim().split('\n')[0]; // Take first URL if multiple
            }
        } catch (err) {
            console.log('URL data error:', err);
        }
        
        // Method 2: Plain text (might be URL)
        if (!imageUrl) {
            try {
                const textData = e.dataTransfer.getData('text/plain');
                console.log('Text data:', textData);
                if (textData && textData.match(/^https?:\/\/.+\.(jpg|jpeg|png|gif|webp)(\?.*)?$/i)) {
                    imageUrl = textData.trim();
                }
            } catch (err) {
                console.log('Text data error:', err);
            }
        }
        
        // Method 3: HTML content
        if (!imageUrl) {
            try {
                const htmlData = e.dataTransfer.getData('text/html');
                console.log('HTML data:', htmlData);
                if (htmlData) {
                    const imgMatch = htmlData.match(/<img[^>]+src=["']([^"']+)["']/i);
                    if (imgMatch && imgMatch[1]) {
                        imageUrl = imgMatch[1];
                    }
                }
            } catch (err) {
                console.log('HTML data error:', err);
            }
        }
        
        // Method 4: Check for image data directly
        if (!imageUrl) {
            for (let i = 0; i < e.dataTransfer.types.length; i++) {
                const type = e.dataTransfer.types[i];
                console.log('Checking type:', type);
                
                if (type.startsWith('image/')) {
                    try {
                        const data = e.dataTransfer.getData(type);
                        if (data) {
                            imageUrl = data;
                            break;
                        }
                    } catch (err) {
                        console.log('Image type error:', err);
                    }
                }
            }
        }
        
        if (imageUrl) {
            console.log('Processing image URL:', imageUrl);
            handleExternalImage(imageUrl);
        } else {
            console.log('No valid image data found in drop');
            alert('Could not process the dropped content. Please try dragging a direct image file or image URL.');
        }
    });

    // File input change
    imageInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            validateAndSetFile(e.target.files[0]);
        }
    });

    // Form submission
    const form = document.getElementById('productForm');
    const submitBtn = document.getElementById('submitBtn');
    const loadingOverlay = document.getElementById('loadingOverlay');

    form.addEventListener('submit', function(e) {
        // Show loading overlay
        loadingOverlay.style.display = 'flex';
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creating...';
    });

    // Form validation
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });

    function validateField(field) {
        const isValid = field.checkValidity();
        
        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
        }
        
        return isValid;
    }

    // Auto-hide alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            if (alert.classList.contains('alert-success')) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        });
    }, 5000);
});
</script>

<?php include 'includes/admin-footer.php'; ?>