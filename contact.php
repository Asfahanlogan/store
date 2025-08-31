<?php
require_once 'config.php';
$page_title = 'Contact Us';

$message_sent = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || !$email || empty($subject) || empty($message)) {
        $error_message = 'Please fill in all required fields with valid information.';
    } else {
        // Save the contact message to JSON file
        $contact_messages = JsonDB::read('contact_messages.json');
        
        $new_message = [
            'id' => JsonDB::getNextId('contact_messages.json'),
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 'unread'
        ];
        
        $contact_messages[] = $new_message;
        JsonDB::write('contact_messages.json', $contact_messages);
        
        $message_sent = true;
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-gradient mb-3">Contact Us</h1>
        <p class="lead text-light-emphasis">Get in touch with our cybersecurity experts</p>
    </div>

    <div class="row g-5">
        <!-- Contact Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-gradient mb-0">
                        <i class="fas fa-envelope me-2"></i>Send us a Message
                    </h3>
                </div>
                <div class="card-body">
                    <?php if ($message_sent): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Message Sent!</strong> Thank you for contacting us. We'll get back to you within 24 hours.
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    Please provide your name.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    Please provide a valid email address.
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject *</label>
                            <select class="form-select" id="subject" name="subject" required>
                                <option value="">Choose a subject...</option>
                                <option value="General Inquiry" <?php echo ($_POST['subject'] ?? '') === 'General Inquiry' ? 'selected' : ''; ?>>General Inquiry</option>
                                <option value="Technical Support" <?php echo ($_POST['subject'] ?? '') === 'Technical Support' ? 'selected' : ''; ?>>Technical Support</option>
                                <option value="Payment Issue" <?php echo ($_POST['subject'] ?? '') === 'Payment Issue' ? 'selected' : ''; ?>>Payment Issue</option>
                                <option value="Product Request" <?php echo ($_POST['subject'] ?? '') === 'Product Request' ? 'selected' : ''; ?>>Product Request</option>
                                <option value="Bug Report" <?php echo ($_POST['subject'] ?? '') === 'Bug Report' ? 'selected' : ''; ?>>Bug Report</option>
                                <option value="Partnership" <?php echo ($_POST['subject'] ?? '') === 'Partnership' ? 'selected' : ''; ?>>Partnership Opportunity</option>
                                <option value="Other" <?php echo ($_POST['subject'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a subject.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control" id="message" name="message" rows="6" 
                                      placeholder="Please describe your inquiry or issue in detail..." 
                                      required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                            <div class="invalid-feedback">
                                Please provide your message.
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> For technical support, please include your order ID and 
                            detailed description of the issue. We typically respond within 24 hours.
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i>Reset Form
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div class="col-lg-4">
            <!-- Contact Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="text-primary mb-0">
                        <i class="fas fa-address-card me-2"></i>Contact Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h6 class="text-light mb-1">Email</h6>
                            <p class="text-light-emphasis mb-0">support@hackvaultpro.com</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h6 class="text-light mb-1">Support Hours</h6>
                            <p class="text-light-emphasis mb-0">24/7 Online Support</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="feature-icon me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                            <i class="fas fa-reply"></i>
                        </div>
                        <div>
                            <h6 class="text-light mb-1">Response Time</h6>
                            <p class="text-light-emphasis mb-0">Within 24 hours</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div class="feature-icon me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h6 class="text-light mb-1">Security</h6>
                            <p class="text-light-emphasis mb-0">Encrypted Communication</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- FAQ -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="text-primary mb-0">
                        <i class="fas fa-question-circle me-2"></i>Frequently Asked
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion accordion-dark" id="faqAccordion">
                        <div class="accordion-item bg-dark border-secondary">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-dark text-light" 
                                        type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How long does payment processing take?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-light-emphasis">
                                    Cryptocurrency payments are typically confirmed within 10-60 minutes, 
                                    depending on network congestion and transaction fees.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item bg-dark border-secondary">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-dark text-light" 
                                        type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Are the tools legal to use?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-light-emphasis">
                                    Yes, all our tools are designed for legal purposes: education, 
                                    authorized penetration testing, and bug bounty programs.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item bg-dark border-secondary">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-dark text-light" 
                                        type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Do you provide technical support?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-light-emphasis">
                                    Yes, we provide 24/7 technical support via email and our dedicated 
                                    support portal for all customers.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Social Media -->
            <div class="card">
                <div class="card-header">
                    <h5 class="text-primary mb-0">
                        <i class="fas fa-share-alt me-2"></i>Connect With Us
                    </h5>
                </div>
                <div class="card-body text-center">
                    <p class="text-light-emphasis mb-3">
                        Follow us on social media for updates and security tips
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="btn btn-outline-light btn-lg">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-lg">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-lg">
                            <i class="fab fa-discord"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-lg">
                            <i class="fab fa-telegram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Additional Information -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="feature-icon mx-auto mb-3">
                                <i class="fas fa-headset"></i>
                            </div>
                            <h5 class="text-light">24/7 Support</h5>
                            <p class="text-light-emphasis">Round-the-clock assistance for all your needs</p>
                        </div>
                        <div class="col-md-3">
                            <div class="feature-icon mx-auto mb-3">
                                <i class="fas fa-shield-check"></i>
                            </div>
                            <h5 class="text-light">Secure Communication</h5>
                            <p class="text-light-emphasis">All communications are encrypted and secure</p>
                        </div>
                        <div class="col-md-3">
                            <div class="feature-icon mx-auto mb-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5 class="text-light">Expert Team</h5>
                            <p class="text-light-emphasis">Cybersecurity professionals at your service</p>
                        </div>
                        <div class="col-md-3">
                            <div class="feature-icon mx-auto mb-3">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <h5 class="text-light">Fast Response</h5>
                            <p class="text-light-emphasis">Quick resolution to your queries and issues</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
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

// Character counter for message textarea
document.getElementById('message').addEventListener('input', function() {
    const maxLength = 1000;
    const currentLength = this.value.length;
    
    // Create or update character counter
    let counter = document.getElementById('char-counter');
    if (!counter) {
        counter = document.createElement('small');
        counter.id = 'char-counter';
        counter.className = 'text-muted';
        this.parentNode.appendChild(counter);
    }
    
    counter.textContent = `${currentLength}/${maxLength} characters`;
    
    if (currentLength > maxLength) {
        counter.className = 'text-danger';
        this.setCustomValidity('Message is too long');
    } else {
        counter.className = 'text-muted';
        this.setCustomValidity('');
    }
});
</script>

<?php include 'includes/footer.php'; ?>