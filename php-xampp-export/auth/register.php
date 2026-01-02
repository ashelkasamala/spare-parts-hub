<?php
$pageTitle = 'Create Account';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /ashels-autospare/');
    exit;
}

$errors = [];
$formData = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => ''
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        // Sanitize inputs
        $formData['first_name'] = sanitize($_POST['first_name'] ?? '');
        $formData['last_name'] = sanitize($_POST['last_name'] ?? '');
        $formData['email'] = sanitize($_POST['email'] ?? '');
        $formData['phone'] = sanitize($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate inputs
        if (empty($formData['first_name'])) {
            $errors[] = 'First name is required.';
        }
        
        if (empty($formData['last_name'])) {
            $errors[] = 'Last name is required.';
        }
        
        if (empty($formData['email'])) {
            $errors[] = 'Email address is required.';
        } elseif (!isValidEmail($formData['email'])) {
            $errors[] = 'Please enter a valid email address.';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }
        
        // Check if email already exists
        if (empty($errors)) {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$formData['email']]);
            
            if ($stmt->fetch()) {
                $errors[] = 'An account with this email already exists.';
            }
        }
        
        // Create account
        if (empty($errors)) {
            $pdo = getDBConnection();
            
            try {
                $pdo->beginTransaction();
                
                // Insert user
                $stmt = $pdo->prepare("
                    INSERT INTO users (email, password_hash, first_name, last_name, phone, is_active)
                    VALUES (?, ?, ?, ?, ?, 1)
                ");
                $stmt->execute([
                    $formData['email'],
                    hashPassword($password),
                    $formData['first_name'],
                    $formData['last_name'],
                    $formData['phone']
                ]);
                
                $userId = $pdo->lastInsertId();
                
                // Assign customer role
                $stmt = $pdo->prepare("
                    INSERT INTO user_roles (user_id, role_id)
                    SELECT ?, id FROM roles WHERE role_name = 'customer'
                ");
                $stmt->execute([$userId]);
                
                $pdo->commit();
                
                // Log the user in
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $formData['email'];
                $_SESSION['user_name'] = $formData['first_name'] . ' ' . $formData['last_name'];
                
                logActivity('register', 'user', $userId);
                
                setFlashMessage('success', 'Account created successfully! Welcome to Ashel\'s Autospare.');
                header('Location: /ashels-autospare/customer/dashboard.php');
                exit;
                
            } catch (Exception $e) {
                $pdo->rollBack();
                $errors[] = 'An error occurred. Please try again.';
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section" style="min-height: calc(100vh - var(--header-height));">
    <div class="container">
        <div style="max-width: 500px; margin: 0 auto;">
            <div class="text-center mb-4">
                <h1>Create Account</h1>
                <p style="color: var(--text-secondary);">Join Ashel's Autospare today</p>
            </div>
            
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
            
            <div class="card" style="padding: 2rem;">
                <!-- Google Signup Button -->
                <a href="/ashels-autospare/auth/google-auth.php" class="btn btn-secondary" style="width: 100%; margin-bottom: 1.5rem;">
                    <i class="fab fa-google"></i> Sign up with Google
                </a>
                
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="flex: 1; height: 1px; background: var(--border-color);"></div>
                    <span style="color: var(--text-muted); font-size: 0.875rem;">or</span>
                    <div style="flex: 1; height: 1px; background: var(--border-color);"></div>
                </div>
                
                <form method="POST" data-validate>
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label" for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($formData['first_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($formData['last_name']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($formData['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number <span style="color: var(--text-muted);">(optional)</span></label>
                        <input type="tel" id="phone" name="phone" class="form-control" 
                               value="<?php echo htmlspecialchars($formData['phone']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="Minimum 8 characters" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="terms" required style="margin-top: 0.25rem; accent-color: var(--accent-primary);">
                            <span style="font-size: 0.875rem; color: var(--text-secondary);">
                                I agree to the <a href="/ashels-autospare/terms.php">Terms of Service</a> and 
                                <a href="/ashels-autospare/privacy.php">Privacy Policy</a>
                            </span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>
            </div>
            
            <p class="text-center mt-3" style="color: var(--text-secondary);">
                Already have an account? <a href="/ashels-autospare/auth/login.php">Sign in</a>
            </p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
