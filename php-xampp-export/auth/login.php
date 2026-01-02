<?php
$pageTitle = 'Login';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /ashels-autospare/');
    exit;
}

$error = '';
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = 'Please enter both email and password.';
        } else {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && verifyPassword($password, $user['password_hash'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                
                // Update last login
                $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $stmt->execute([$user['id']]);
                
                // Log activity
                logActivity('login', 'user', $user['id']);
                
                // Redirect to intended URL or dashboard
                $redirect = $_SESSION['redirect_url'] ?? '/ashels-autospare/';
                unset($_SESSION['redirect_url']);
                
                // Redirect based on role
                if (hasRole($user['id'], 'admin') || hasRole($user['id'], 'staff')) {
                    $redirect = '/ashels-autospare/admin/dashboard.php';
                } else {
                    $redirect = '/ashels-autospare/customer/dashboard.php';
                }
                
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section" style="min-height: calc(100vh - var(--header-height));">
    <div class="container">
        <div style="max-width: 450px; margin: 0 auto;">
            <div class="text-center mb-4">
                <h1>Welcome Back</h1>
                <p style="color: var(--text-secondary);">Sign in to access your account</p>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
                <button class="alert-close">&times;</button>
            </div>
            <?php endif; ?>
            
            <div class="card" style="padding: 2rem;">
                <!-- Google Login Button -->
                <a href="/ashels-autospare/auth/google-auth.php" class="btn btn-secondary" style="width: 100%; margin-bottom: 1.5rem;">
                    <i class="fab fa-google"></i> Continue with Google
                </a>
                
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="flex: 1; height: 1px; background: var(--border-color);"></div>
                    <span style="color: var(--text-muted); font-size: 0.875rem;">or</span>
                    <div style="flex: 1; height: 1px; background: var(--border-color);"></div>
                </div>
                
                <form method="POST" data-validate>
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="remember" style="accent-color: var(--accent-primary);">
                            <span style="font-size: 0.875rem;">Remember me</span>
                        </label>
                        <a href="/ashels-autospare/auth/forgot-password.php" style="font-size: 0.875rem;">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>
            </div>
            
            <p class="text-center mt-3" style="color: var(--text-secondary);">
                Don't have an account? <a href="/ashels-autospare/auth/register.php">Create one</a>
            </p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
