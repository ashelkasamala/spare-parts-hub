<?php
$pageTitle = 'My Dashboard';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

// Get user's recent orders
$stmt = $pdo->prepare("
    SELECT o.*, os.status_name
    FROM orders o
    LEFT JOIN order_status os ON o.status_id = os.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
    LIMIT 5
");
$stmt->execute([$userId]);
$recentOrders = $stmt->fetchAll();

// Get order statistics
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$stmt->execute([$userId]);
$totalOrders = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = ? AND status_id >= 2");
$stmt->execute([$userId]);
$totalSpent = $stmt->fetchColumn();

// Get wishlist count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
$stmt->execute([$userId]);
$wishlistCount = $stmt->fetchColumn();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1>My Dashboard</h1>
                <p style="color: var(--text-secondary);">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-3" style="margin-bottom: 3rem;">
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: var(--accent-glow); border-radius: 0.5rem;">
                        <i class="fas fa-shopping-bag" style="color: var(--accent-primary); font-size: 1.25rem;"></i>
                    </div>
                    <div>
                        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">Total Orders</p>
                        <p style="font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; margin: 0;"><?php echo number_format($totalOrders); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: rgba(34, 197, 94, 0.2); border-radius: 0.5rem;">
                        <i class="fas fa-coins" style="color: var(--success); font-size: 1.25rem;"></i>
                    </div>
                    <div>
                        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">Total Spent</p>
                        <p style="font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; margin: 0;"><?php echo formatCurrency($totalSpent); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: rgba(239, 68, 68, 0.2); border-radius: 0.5rem;">
                        <i class="fas fa-heart" style="color: var(--danger); font-size: 1.25rem;"></i>
                    </div>
                    <div>
                        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">Wishlist Items</p>
                        <p style="font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; margin: 0;"><?php echo number_format($wishlistCount); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-2" style="gap: 2rem;">
            <!-- Recent Orders -->
            <div class="card">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0;">Recent Orders</h3>
                    <a href="/ashels-autospare/customer/orders.php" style="font-size: 0.875rem;">View All</a>
                </div>
                <div style="padding: 1rem;">
                    <?php if (empty($recentOrders)): ?>
                    <div class="text-center" style="padding: 2rem;">
                        <i class="fas fa-shopping-bag" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                        <p style="color: var(--text-muted);">No orders yet.</p>
                        <a href="/ashels-autospare/products.php" class="btn btn-primary btn-sm">Start Shopping</a>
                    </div>
                    <?php else: ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.75rem; color: var(--text-muted);">ORDER</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.75rem; color: var(--text-muted);">DATE</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.75rem; color: var(--text-muted);">STATUS</th>
                                <th style="padding: 0.75rem; text-align: right; font-size: 0.75rem; color: var(--text-muted);">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.75rem;">
                                    <a href="/ashels-autospare/customer/order-detail.php?id=<?php echo $order['id']; ?>">
                                        <?php echo htmlspecialchars($order['order_number']); ?>
                                    </a>
                                </td>
                                <td style="padding: 0.75rem; color: var(--text-secondary);">
                                    <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <span style="padding: 0.25rem 0.5rem; background: var(--accent-glow); color: var(--accent-primary); font-size: 0.75rem; border-radius: 0.25rem;">
                                        <?php echo ucfirst($order['status_name']); ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem; text-align: right;"><?php echo formatCurrency($order['total_amount']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h3 style="margin-bottom: 1rem;">Quick Actions</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <a href="/ashels-autospare/products.php" class="card" style="padding: 1rem; display: flex; align-items: center; gap: 1rem; text-decoration: none;">
                        <div style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: var(--accent-glow); border-radius: 0.5rem;">
                            <i class="fas fa-search" style="color: var(--accent-primary);"></i>
                        </div>
                        <div>
                            <p style="margin: 0; color: var(--text-primary); font-weight: 600;">Browse Products</p>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);">Find the parts you need</p>
                        </div>
                    </a>
                    
                    <a href="/ashels-autospare/customer/orders.php" class="card" style="padding: 1rem; display: flex; align-items: center; gap: 1rem; text-decoration: none;">
                        <div style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: var(--accent-glow); border-radius: 0.5rem;">
                            <i class="fas fa-history" style="color: var(--accent-primary);"></i>
                        </div>
                        <div>
                            <p style="margin: 0; color: var(--text-primary); font-weight: 600;">Order History</p>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);">View all your past orders</p>
                        </div>
                    </a>
                    
                    <a href="/ashels-autospare/customer/profile.php" class="card" style="padding: 1rem; display: flex; align-items: center; gap: 1rem; text-decoration: none;">
                        <div style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: var(--accent-glow); border-radius: 0.5rem;">
                            <i class="fas fa-user-cog" style="color: var(--accent-primary);"></i>
                        </div>
                        <div>
                            <p style="margin: 0; color: var(--text-primary); font-weight: 600;">Account Settings</p>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);">Manage your profile</p>
                        </div>
                    </a>
                    
                    <a href="/ashels-autospare/contact.php" class="card" style="padding: 1rem; display: flex; align-items: center; gap: 1rem; text-decoration: none;">
                        <div style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: var(--accent-glow); border-radius: 0.5rem;">
                            <i class="fas fa-headset" style="color: var(--accent-primary);"></i>
                        </div>
                        <div>
                            <p style="margin: 0; color: var(--text-primary); font-weight: 600;">Contact Support</p>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);">Get help with your orders</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
