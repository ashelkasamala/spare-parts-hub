<?php
$pageTitle = 'Contact Us';
require_once __DIR__ . '/includes/functions.php';

$success = false;
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $subject = sanitize($_POST['subject'] ?? '');
        $message = sanitize($_POST['message'] ?? '');
        
        // Validate
        if (empty($name)) $errors[] = 'Name is required.';
        if (empty($email)) $errors[] = 'Email is required.';
        elseif (!isValidEmail($email)) $errors[] = 'Please enter a valid email.';
        if (empty($subject)) $errors[] = 'Subject is required.';
        if (empty($message)) $errors[] = 'Message is required.';
        
        if (empty($errors)) {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                INSERT INTO contact_messages (name, email, phone, subject, message)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$name, $email, $phone, $subject, $message])) {
                $success = true;
                // Clear form data
                $name = $email = $phone = $subject = $message = '';
            } else {
                $errors[] = 'An error occurred. Please try again.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">Contact Us</span>
            <h1>Get In Touch</h1>
            <p class="section-description">Have questions? We're here to help you find the right parts.</p>
        </div>
        
        <div class="grid grid-2" style="gap: 4rem;">
            <div>
                <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    Thank you for your message! We'll get back to you shortly.
                    <button class="alert-close">&times;</button>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 1.25rem;">
                        <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button class="alert-close">&times;</button>
                </div>
                <?php endif; ?>
                
                <form method="POST" data-validate>
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="form-group">
                        <label class="form-label" for="name">Your Name *</label>
                        <input type="text" id="name" name="name" class="form-control" 
                               value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" 
                               value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" class="form-control" 
                               value="<?php echo htmlspecialchars($subject ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="message">Message *</label>
                        <textarea id="message" name="message" class="form-control" rows="6" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
            
            <div>
                <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1.5rem;">Contact Information</h3>
                    
                    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                        <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: var(--accent-glow); border-radius: 0.5rem; flex-shrink: 0;">
                            <i class="fas fa-map-marker-alt" style="color: var(--accent-primary);"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 1rem; margin-bottom: 0.25rem;">Address</h4>
                            <p style="color: var(--text-secondary); margin: 0;">Industrial Area<br>Nairobi, Kenya</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                        <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: var(--accent-glow); border-radius: 0.5rem; flex-shrink: 0;">
                            <i class="fas fa-phone" style="color: var(--accent-primary);"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 1rem; margin-bottom: 0.25rem;">Phone</h4>
                            <p style="margin: 0;"><a href="tel:+254700123456">+254 700 123 456</a></p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                        <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: var(--accent-glow); border-radius: 0.5rem; flex-shrink: 0;">
                            <i class="fas fa-envelope" style="color: var(--accent-primary);"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 1rem; margin-bottom: 0.25rem;">Email</h4>
                            <p style="margin: 0;"><a href="mailto:info@ashelsautospare.com">info@ashelsautospare.com</a></p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: var(--accent-glow); border-radius: 0.5rem; flex-shrink: 0;">
                            <i class="fas fa-clock" style="color: var(--accent-primary);"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 1rem; margin-bottom: 0.25rem;">Business Hours</h4>
                            <p style="color: var(--text-secondary); margin: 0;">Monday - Saturday<br>8:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                </div>
                
                <!-- Map Placeholder -->
                <div class="card" style="aspect-ratio: 16/10; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary);">
                    <div class="text-center">
                        <i class="fas fa-map" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                        <p style="color: var(--text-muted);">Map integration available</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
