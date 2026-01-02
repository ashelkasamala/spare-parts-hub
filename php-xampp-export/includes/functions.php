<?php
/**
 * Helper Functions
 */

require_once __DIR__ . '/../config/database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitize input data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user has a specific role
 */
function hasRole($userId, $roleName) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT EXISTS(
            SELECT 1 FROM user_roles ur
            JOIN roles r ON ur.role_id = r.id
            WHERE ur.user_id = ? AND r.role_name = ?
        ) as has_role
    ");
    $stmt->execute([$userId, $roleName]);
    return (bool) $stmt->fetchColumn();
}

/**
 * Check if current user is admin
 */
function isAdmin() {
    if (!isLoggedIn()) return false;
    return hasRole($_SESSION['user_id'], 'admin');
}

/**
 * Check if current user is staff
 */
function isStaff() {
    if (!isLoggedIn()) return false;
    return hasRole($_SESSION['user_id'], 'staff') || isAdmin();
}

/**
 * Get current user data
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Require login - redirect if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: /ashels-autospare/auth/login.php');
        exit;
    }
}

/**
 * Require admin role
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: /ashels-autospare/index.php?error=unauthorized');
        exit;
    }
}

/**
 * Require staff role
 */
function requireStaff() {
    requireLogin();
    if (!isStaff()) {
        header('Location: /ashels-autospare/index.php?error=unauthorized');
        exit;
    }
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Format currency
 */
function formatCurrency($amount, $currency = 'KES') {
    return $currency . ' ' . number_format($amount, 2);
}

/**
 * Generate slug from string
 */
function generateSlug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

/**
 * Get flash message
 */
function getFlashMessage($type = null) {
    if ($type) {
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
    } else {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
    }
    return null;
}

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

/**
 * Log activity
 */
function logActivity($action, $entityType = null, $entityId = null, $oldValues = null, $newValues = null) {
    if (!isLoggedIn()) return;
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        INSERT INTO activity_log (user_id, action, entity_type, entity_id, old_values, new_values, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $_SESSION['user_id'],
        $action,
        $entityType,
        $entityId,
        $oldValues ? json_encode($oldValues) : null,
        $newValues ? json_encode($newValues) : null,
        $_SERVER['REMOTE_ADDR'] ?? null,
        $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
}

/**
 * Paginate results
 */
function paginate($query, $params = [], $perPage = 10) {
    $pdo = getDBConnection();
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($page - 1) * $perPage;
    
    // Get total count
    $countQuery = preg_replace('/SELECT .* FROM/i', 'SELECT COUNT(*) FROM', $query);
    $countQuery = preg_replace('/ORDER BY .*/i', '', $countQuery);
    $stmt = $pdo->prepare($countQuery);
    $stmt->execute($params);
    $total = $stmt->fetchColumn();
    
    // Get paginated results
    $query .= " LIMIT $perPage OFFSET $offset";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    
    return [
        'data' => $results,
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $page,
        'last_page' => ceil($total / $perPage),
        'from' => $offset + 1,
        'to' => min($offset + $perPage, $total)
    ];
}

/**
 * Render pagination links
 */
function renderPagination($pagination) {
    if ($pagination['last_page'] <= 1) return '';
    
    $html = '<nav class="pagination"><ul>';
    
    // Previous
    if ($pagination['current_page'] > 1) {
        $html .= '<li><a href="?page=' . ($pagination['current_page'] - 1) . '">&laquo; Previous</a></li>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $pagination['last_page']; $i++) {
        $active = $i === $pagination['current_page'] ? ' class="active"' : '';
        $html .= '<li' . $active . '><a href="?page=' . $i . '">' . $i . '</a></li>';
    }
    
    // Next
    if ($pagination['current_page'] < $pagination['last_page']) {
        $html .= '<li><a href="?page=' . ($pagination['current_page'] + 1) . '">Next &raquo;</a></li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}
?>
