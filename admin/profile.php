<?php include 'includes/admin-header.php'; ?>

<?php
// Handle profile update
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $email = trim($_POST['email'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate inputs
        if (empty($email)) {
            $message = 'Email is required.';
            $messageType = 'danger';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Please enter a valid email address.';
            $messageType = 'danger';
        } elseif (!empty($newPassword)) {
            // If changing password, validate
            if (empty($currentPassword)) {
                $message = 'Current password is required to change password.';
                $messageType = 'danger';
            } elseif (strlen($newPassword) < 6) {
                $message = 'New password must be at least 6 characters long.';
                $messageType = 'danger';
            } elseif ($newPassword !== $confirmPassword) {
                $message = 'New passwords do not match.';
                $messageType = 'danger';
            } else {
                // Verify current password
                $currentAdminData = $admin->getCurrentAdmin();
                if (!password_verify($currentPassword, $currentAdminData['password'])) {
                    $message = 'Current password is incorrect.';
                    $messageType = 'danger';
                } else {
                    // Update with new password
                    if ($admin->updateProfile($email, $newPassword)) {
                        $message = 'Profile updated successfully with new password.';
                        $messageType = 'success';
                    } else {
                        $message = 'Failed to update profile. Please try again.';
                        $messageType = 'danger';
                    }
                }
            }
        } else {
            // Update email only
            if ($admin->updateProfile($email)) {
                $message = 'Profile updated successfully.';
                $messageType = 'success';
            } else {
                $message = 'Failed to update profile. Please try again.';
                $messageType = 'danger';
            }
        }
    }
}

// Get current admin data (refresh after potential update)
$currentAdminData = $admin->getCurrentAdmin();
?>

<!-- Profile Content -->
<div class="page-header">
    <h1 class="page-title">Profile Settings</h1>
    <p class="page-description">Manage your admin account settings and preferences.</p>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <i class="alert-icon fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
        <div class="alert-content"><?php echo htmlspecialchars($message); ?></div>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
    <!-- Profile Form -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Account Information</h3>
        </div>
        <div class="card-body">
            <form method="POST" data-validate>
                <input type="hidden" name="action" value="update_profile">
                
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" 
                           id="username" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($currentAdminData['username']); ?>" 
                           disabled>
                    <small class="text-muted">Username cannot be changed for security reasons.</small>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($currentAdminData['email']); ?>" 
                           required>
                </div>
                
                <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--gray-200);">
                
                <h4 style="margin-bottom: 1rem; color: var(--gray-900); font-size: var(--font-size-lg);">Change Password</h4>
                <p class="text-muted" style="margin-bottom: 1.5rem; font-size: var(--font-size-sm);">
                    Leave password fields empty if you don't want to change your password.
                </p>
                
                <div class="form-group">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" 
                           id="current_password" 
                           name="current_password" 
                           class="form-control">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" 
                               id="new_password" 
                               name="new_password" 
                               class="form-control"
                               minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               class="form-control">
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update Profile
                    </button>
                    <a href="dashboard.php" class="btn btn-outline">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Account Info Sidebar -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Account Summary -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Account Summary</h3>
            </div>
            <div class="card-body">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div class="user-avatar" style="width: 80px; height: 80px; font-size: 2rem; margin: 0 auto 1rem;">
                        <?php echo strtoupper(substr($currentAdminData['username'], 0, 1)); ?>
                    </div>
                    <h4 style="margin-bottom: 0.25rem;"><?php echo htmlspecialchars($currentAdminData['username']); ?></h4>
                    <p class="text-muted">Administrator</p>
                </div>
                
                <div style="font-size: var(--font-size-sm); color: var(--gray-600);">
                    <div style="display: flex; justify-content: between; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--gray-200);">
                        <span>Email:</span>
                        <span class="font-medium"><?php echo htmlspecialchars($currentAdminData['email']); ?></span>
                    </div>
                    <div style="display: flex; justify-content: between; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--gray-200);">
                        <span>Account Created:</span>
                        <span class="font-medium">
                            <?php echo date('M j, Y', strtotime($currentAdminData['created_at'])); ?>
                        </span>
                    </div>
                    <div style="display: flex; justify-content: between; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--gray-200);">
                        <span>Last Login:</span>
                        <span class="font-medium">
                            <?php 
                            echo $currentAdminData['last_login'] 
                                ? date('M j, Y g:i A', strtotime($currentAdminData['last_login'])) 
                                : 'First login';
                            ?>
                        </span>
                    </div>
                    <div style="display: flex; justify-content: between;">
                        <span>Status:</span>
                        <span class="badge badge-success">Active</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Security Tips -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Security Tips</h3>
            </div>
            <div class="card-body">
                <div style="font-size: var(--font-size-sm); color: var(--gray-600);">
                    <div style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 1rem;">
                        <i class="fas fa-lock text-primary" style="margin-top: 0.125rem;"></i>
                        <div>
                            <div class="font-medium text-gray-900">Use Strong Passwords</div>
                            <div>Use at least 8 characters with a mix of letters, numbers, and symbols.</div>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 1rem;">
                        <i class="fas fa-shield-alt text-success" style="margin-top: 0.125rem;"></i>
                        <div>
                            <div class="font-medium text-gray-900">Regular Updates</div>
                            <div>Change your password regularly to maintain security.</div>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <i class="fas fa-eye text-warning" style="margin-top: 0.125rem;"></i>
                        <div>
                            <div class="font-medium text-gray-900">Monitor Access</div>
                            <div>Always log out when finished and don't share credentials.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <a href="dashboard.php" class="btn btn-outline btn-sm">
                        <i class="fas fa-tachometer-alt"></i>
                        Back to Dashboard
                    </a>
                    <a href="manage-products.php" class="btn btn-outline btn-sm">
                        <i class="fas fa-cogs"></i>
                        Manage Services
                    </a>
                    <a href="logout.php" class="btn btn-danger btn-sm" data-confirm="Are you sure you want to logout?">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (newPassword && confirmPassword && newPassword !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});

// Show/hide password confirmation based on new password
document.getElementById('new_password').addEventListener('input', function() {
    const confirmField = document.getElementById('confirm_password');
    const currentField = document.getElementById('current_password');
    
    if (this.value) {
        confirmField.setAttribute('required', 'required');
        currentField.setAttribute('required', 'required');
    } else {
        confirmField.removeAttribute('required');
        currentField.removeAttribute('required');
        confirmField.value = '';
        currentField.value = '';
    }
});
</script>

<?php include 'includes/admin-footer.php'; ?>