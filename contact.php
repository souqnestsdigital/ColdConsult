<?php
require_once '../config/config.php';
$page_title = 'Contact Us - Cold Storage Solutions | Get Professional Consultation';
$page_description = 'Contact Cold Storage Solutions for professional consultation, equipment quotes, and technical support. Expert industrial refrigeration services available 24/7.';

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message_text = trim($_POST['message'] ?? '');
    
    // Basic validation
    $errors = [];
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if (empty($subject)) $errors[] = 'Subject is required';
    if (empty($message_text)) $errors[] = 'Message is required';
    
    if (empty($errors)) {
        // In production, integrate with email service or database
        $message = 'Thank you for your message! We will get back to you within 24 hours.';
        $message_type = 'success';
        
        // Clear form data on success
        $name = $email = $phone = $company = $subject = $message_text = '';
    } else {
        $message = implode(', ', $errors);
        $message_type = 'danger';
    }
}

include '../includes/header.php';
?>

<!-- Hero Section -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-3">Contact Us</h1>
                <p class="lead mb-4">Get professional consultation for your cold storage needs. Our experts are ready to help.</p>
                <div class="d-flex justify-content-center flex-wrap gap-3">
                    <span class="badge bg-light text-primary px-3 py-2 rounded-pill">
                        <i class="fas fa-phone me-2"></i>24/7 Support
                    </span>
                    <span class="badge bg-light text-primary px-3 py-2 rounded-pill">
                        <i class="fas fa-clock me-2"></i>Quick Response
                    </span>
                    <span class="badge bg-light text-primary px-3 py-2 rounded-pill">
                        <i class="fas fa-handshake me-2"></i>Free Consultation
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Contact Section -->
<section class="py-4">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5" style="min-height: 600px;">
                        <h3 class="h4 mb-4 text-center">Send us a Message</h3>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                                <?php echo htmlspecialchars($message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                                    <div class="invalid-feedback">Please provide your name.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                                    <div class="invalid-feedback">Please provide a valid email.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="company" class="form-label">Company</label>
                                    <input type="text" class="form-control" id="company" name="company" 
                                           value="<?php echo htmlspecialchars($company ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                    <select class="form-select" id="subject" name="subject" required>
                                        <option value="">Choose a subject...</option>
                                        <option value="general" <?php echo (($subject ?? '') === 'general') ? 'selected' : ''; ?>>General Inquiry</option>
                                        <option value="cold-storage" <?php echo (($subject ?? '') === 'cold-storage') ? 'selected' : ''; ?>>Cold Storage Solutions</option>
                                        <option value="equipment" <?php echo (($subject ?? '') === 'equipment') ? 'selected' : ''; ?>>Equipment Inquiry</option>
                                        <option value="maintenance" <?php echo (($subject ?? '') === 'maintenance') ? 'selected' : ''; ?>>Maintenance Service</option>
                                        <option value="quote" <?php echo (($subject ?? '') === 'quote') ? 'selected' : ''; ?>>Request Quote</option>
                                        <option value="support" <?php echo (($subject ?? '') === 'support') ? 'selected' : ''; ?>>Technical Support</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a subject.</div>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="message" name="message" rows="8" 
                                              placeholder="Please describe your cold storage requirements, project timeline, capacity needs, temperature specifications, or any specific questions you may have..." required><?php echo htmlspecialchars($message_text ?? ''); ?></textarea>
                                    <div class="invalid-feedback">Please provide your message.</div>
                                    <div class="form-text mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Include details such as: storage capacity, temperature range, location, timeline, and budget for better assistance.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg px-4">
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="col-lg-4">
                <div class="row g-4">
                    <!-- Address -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-map-marker-alt fs-4"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Visit Our Office</h5>
                                <p class="text-muted mb-0">
                                    No 13 Nilmani Apartment<br>
                                    Cold Storage District<br>
                                    Nashik, Maharashtra 400001
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Phone -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-phone fs-4"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Call Us</h5>
                                <p class="text-muted mb-2">
                                    <strong>India:</strong> +91 86989 55489<br>
                                    <strong>Saudi Arabia:</strong> +966 59333 2875
                                </p>
                                <small class="text-muted">24/7 Emergency Support</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-envelope fs-4"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Email Us</h5>
                                <p class="text-muted mb-0">
                                    <strong>Info:</strong> info@coldconsult.com<br>
                                    <strong>Sales:</strong> sales@coldconsult.com<br>
                                    <strong>Support:</strong> support@coldconsult.com
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Business Hours -->
                    
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map & Features Section -->
<section class="pt-1 pb-4">
    <div class="container">
        <!-- Map Section -->
        <div class="row align-items-center mb-4">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <h3 class="h4 mb-3">Find Us on the Map</h3>
                <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-sm">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d30267.800719583563!2d73.78405985108762!3d18.507420981756287!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bc2bfb732af849d%3A0xd4078b48b3fe44f0!2sKothrud%2C%20Pune%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1759117669394!5m2!1sen!2sin" 
                        width="100%" 
                        height="350" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
            <div class="col-lg-4">
                <h3 class="h4 mb-3">Our Locations</h3>
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-primary mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>Nashik Main
                        </h6>
                        <p class="mb-1 text-muted small">No 13 Nilmani Apartment, Nashik</p>
                        <p class="mb-0 small text-muted">
                            <i class="fas fa-phone me-2"></i>+91 86989 55489
                        </p>
                    </div>
                </div>
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-primary mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>Saudi Arabia
                        </h6>
                        <p class="mb-1 text-muted small">Jubail, Eastern Province</p>
                        <p class="mb-0 small text-muted">
                            <i class="fas fa-phone me-2"></i>+966 59333 2875
                        </p>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-primary mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>Bangalore Office
                        </h6>
                        <p classx="mb-1 text-muted small">789 Tech Valley, Bangalore</p>
                        <p class="mb-0 small text-muted">
                            <i class="fas fa-phone me-2"></i>+91 98765 43213
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Features Section -->
        <div class="row text-center mb-4">
            <div class="col-lg-8 mx-auto">
                <h3 class="h4 mb-3">Why Choose Us?</h3>
                <p class="text-muted">Professional cold storage solutions with industry-leading expertise and support.</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center">
                    <div class="card-body p-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h6 class="fw-bold mb-2">Quick Response</h6>
                        <p class="text-muted mb-0 small">We respond to all inquiries within 4 hours during business days</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center">
                    <div class="card-body p-3">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-users"></i>
                        </div>
                        <h6 class="fw-bold mb-2">Expert Team</h6>
                        <p class="text-muted mb-0 small">Certified engineers with 10+ years of cold storage experience</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center">
                    <div class="card-body p-3">
                        <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-star"></i>
                        </div>
                        <h6 class="fw-bold mb-2">Quality Service</h6>
                        <p class="text-muted mb-0 small">98% client satisfaction rate with comprehensive warranty</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Bootstrap form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>

<?php include '../includes/footer.php'; ?>