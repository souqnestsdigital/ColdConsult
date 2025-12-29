<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/config.php';
require_once '../../classes/Admin.php';

$admin = new Admin();
if (!$admin->isLoggedIn()) { 
    header('Location: index.php'); 
    exit; 
}

// Current page for highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' : ''; ?>ColdConsult Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="includes/dashboard-bootstrap.css">
    <style>
        /* Additional text visibility fixes */
        body { 
            background-color: #f8f9fa !important; 
            color: #212529 !important;
        }
        .card { 
            background-color: #ffffff !important; 
        }
        .text-primary { 
            color: #0d6efd !important; 
        }
        .text-success { 
            color: #198754 !important; 
        }
        .text-warning { 
            color: #ffc107 !important; 
        }
        .text-info { 
            color: #0dcaf0 !important; 
        }
        /* Navbar improvements */
        .admin-navbar { 
            background: #ffffff !important; 
            border-bottom: 1px solid #dee2e6 !important; 
        }
        .admin-navbar .navbar-brand { 
            color: #212529 !important; 
            font-weight: 600 !important;
        }
        .admin-navbar .navbar-nav .nav-link { 
            color: #495057 !important; 
            font-weight: 500 !important;
        }
        .admin-navbar .navbar-nav .nav-link.active,
        .admin-navbar .navbar-nav .nav-link:hover { 
            background: #f8f9fa !important; 
            color: #0d6efd !important; 
        }
        .logout-btn { 
            color: #dc3545 !important; 
            border-color: #dc3545 !important; 
        }
        .logout-btn:hover { 
            background: #dc3545 !important; 
            border-color: #dc3545 !important; 
            color: #fff !important; 
        }
    </style>
</head>
<body class="bg-white">
    <nav class="navbar navbar-expand-lg admin-navbar sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-semibold" href="dashboard.php">
                <i class="fas fa-snowflake me-2 text-primary"></i> ColdConsult Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php"><i class="fas fa-gauge me-2"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo in_array($current_page, ['manage-products.php','view-product.php','edit-product.php','add-product.php']) ? 'active' : ''; ?>" href="manage-products.php"><i class="fas fa-boxes-stacked me-2"></i>Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'manage-categories.php' ? 'active' : ''; ?>" href="manage-categories.php"><i class="fas fa-tags me-2"></i>Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php" target="_blank"><i class="fas fa-arrow-up-right-from-square me-2"></i>View Site</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3"><i class="fas fa-user-circle me-1"></i>Admin</span>
                    <a href="logout.php" class="nav-link logout-btn" onclick="return confirm('Logout now?')">
                        <i class="fas fa-right-from-bracket"></i>
                        <span class="d-none d-md-inline ms-1">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container-fluid mt-4">