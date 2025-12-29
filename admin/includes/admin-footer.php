</div> <!-- End main content container -->
    
    <!-- Professional Admin Footer -->
    <footer class="admin-footer mt-auto">
        <div class="container-fluid">
            <div class="row align-items-center py-3">
                <!-- Copyright Section -->
                <div class="col-md-6 col-12 text-center text-md-start">
                    <p class="footer-text mb-0">
                        <i class="fas fa-copyright text-muted me-1"></i>
                        <span class="text-muted">
                            <?php echo date('Y'); ?> ColdConsult. All rights reserved.
                        </span>
                    </p>
                </div>
                
                <!-- Links & Version Section -->
                <div class="col-md-6 col-12 text-center text-md-end mt-2 mt-md-0">
                    <div class="footer-links d-flex justify-content-center justify-content-md-end align-items-center flex-wrap">
                        <a href="../index.php" target="_blank" class="footer-link me-3">
                            <i class="fas fa-external-link-alt me-1"></i>
                            <span>Visit Website</span>
                        </a>
                        <span class="footer-divider me-3">|</span>
                        <span class="footer-version">
                            <i class="fas fa-code me-1 text-primary"></i>
                            <small class="text-muted">Admin Panel v1.0</small>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Additional Footer Info (Optional) -->
            <div class="row">
                <div class="col-12">
                    <hr class="footer-divider-line my-2">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <small class="text-muted">
                            <i class="fas fa-server me-1"></i>
                            Server Time: <?php echo date('M d, Y - H:i'); ?>
                        </small>
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>
                            Logged in as: <strong>Admin</strong>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap 5 JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Admin JavaScript -->
    <script>
        // Global admin functionality
        document.addEventListener('DOMContentLoaded', function() {
            
            // Enhanced Navigation Loading States
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!this.target && !this.onclick && !this.classList.contains('active')) {
                        // Create loading indicator
                        const originalContent = this.innerHTML;
                        this.style.opacity = '0.7';
                        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
                        
                        // Reset after a short delay (in case of quick page loads)
                        setTimeout(() => {
                            this.innerHTML = originalContent;
                            this.style.opacity = '1';
                        }, 2000);
                    }
                });
            });

            // Enhanced Alert Auto-hide with Animation
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                // Add close button if not present
                if (!alert.querySelector('.btn-close')) {
                    const closeBtn = document.createElement('button');
                    closeBtn.type = 'button';
                    closeBtn.className = 'btn-close';
                    closeBtn.setAttribute('data-bs-dismiss', 'alert');
                    closeBtn.setAttribute('aria-label', 'Close');
                    alert.appendChild(closeBtn);
                }
                
                // Auto-hide after 6 seconds
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 6000);
            });

            // Enhanced Delete Confirmation with Bootstrap Modal
            const deleteButtons = document.querySelectorAll('[data-action="delete"], .delete-btn, .btn-danger[href*="delete"]');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const itemName = this.getAttribute('data-item-name') || 'this item';
                    const deleteUrl = this.href || this.getAttribute('data-url');
                    
                    showConfirmModal(
                        'Confirm Deletion',
                        `Are you sure you want to delete "${itemName}"? This action cannot be undone.`,
                        'danger',
                        () => {
                            if (deleteUrl) {
                                window.location.href = deleteUrl;
                            } else if (this.form) {
                                this.form.submit();
                            }
                        }
                    );
                });
            });

            // Enhanced Form Submission with Loading States
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        const originalText = submitBtn.innerHTML;
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                        
                        // Re-enable after 5 seconds as fallback
                        setTimeout(() => {
                            if (submitBtn.disabled) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalText;
                            }
                        }, 5000);
                    }
                });
            });

            // Add smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
        });

        // Enhanced Utility Functions
        
        /**
         * Show Bootstrap alert with auto-dismiss
         */
        function showAlert(message, type = 'success', duration = 5000) {
            const alertContainer = document.querySelector('.container-fluid') || document.body;
            const alertId = 'alert-' + Date.now();
            
            const alertHTML = `
                <div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert" id="${alertId}">
                    <i class="fas fa-${getAlertIcon(type)} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            alertContainer.insertAdjacentHTML('afterbegin', alertHTML);
            
            // Auto-dismiss
            setTimeout(() => {
                const alertElement = document.getElementById(alertId);
                if (alertElement) {
                    const bsAlert = new bootstrap.Alert(alertElement);
                    bsAlert.close();
                }
            }, duration);
        }
        
        /**
         * Get appropriate icon for alert type
         */
        function getAlertIcon(type) {
            const icons = {
                'success': 'check-circle',
                'danger': 'exclamation-triangle',
                'warning': 'exclamation-triangle',
                'info': 'info-circle',
                'primary': 'info-circle'
            };
            return icons[type] || 'info-circle';
        }
        
        /**
         * Show confirmation modal using Bootstrap
         */
        function showConfirmModal(title, message, type = 'primary', onConfirm = null, onCancel = null) {
            const modalId = 'confirmModal-' + Date.now();
            const buttonClass = type === 'danger' ? 'btn-danger' : 'btn-primary';
            const buttonText = type === 'danger' ? 'Delete' : 'Confirm';
            
            const modalHTML = `
                <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${title}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                ${message}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn ${buttonClass}" id="confirmBtn">${buttonText}</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            const modal = new bootstrap.Modal(document.getElementById(modalId));
            const confirmBtn = document.getElementById('confirmBtn');
            
            confirmBtn.addEventListener('click', () => {
                modal.hide();
                if (onConfirm) onConfirm();
            });
            
            // Clean up modal after hide
            document.getElementById(modalId).addEventListener('hidden.bs.modal', () => {
                document.getElementById(modalId).remove();
                if (onCancel) onCancel();
            });
            
            modal.show();
        }
        
        /**
         * Simple delete confirmation (fallback)
         */
        function confirmDelete(message = 'Are you sure you want to delete this item?') {
            return confirm(message);
        }
        
        /**
         * Show loading spinner overlay
         */
        function showLoadingOverlay(message = 'Processing...') {
            const overlay = document.createElement('div');
            overlay.id = 'loadingOverlay';
            overlay.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center';
            overlay.style.cssText = 'background-color: rgba(0,0,0,0.5); z-index: 9999;';
            overlay.innerHTML = `
                <div class="bg-white p-4 rounded shadow text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mb-0">${message}</p>
                </div>
            `;
            document.body.appendChild(overlay);
        }
        
        /**
         * Hide loading spinner overlay
         */
        function hideLoadingOverlay() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.remove();
        }
    </script>
    
    <!-- Additional Footer Styling -->
    <style>
        .admin-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            margin-top: auto;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }
        
        .footer-text {
            font-size: 0.9rem;
        }
        
        .footer-link {
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .footer-link:hover {
            color: #007bff;
            text-decoration: none;
        }
        
        .footer-divider {
            color: #dee2e6;
            font-weight: 300;
        }
        
        .footer-divider-line {
            border-top: 1px solid #dee2e6;
            opacity: 0.5;
        }
        
        .footer-version {
            font-size: 0.85rem;
        }
        
        /* Ensure footer stays at bottom */
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .container-fluid {
            flex: 1;
        }
        
        @media (max-width: 768px) {
            .footer-links {
                gap: 0.5rem;
            }
            
            .footer-divider {
                display: none;
            }
        }
    </style>
</body>
</html>