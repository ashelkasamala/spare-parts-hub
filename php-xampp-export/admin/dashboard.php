<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/functions.php';
requireStaff();

$pdo = getDBConnection();

// Get statistics
$stats = [];

// Total products
$stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1");
$stats['products'] = $stmt->fetchColumn();

// Total orders
$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$stats['orders'] = $stmt->fetchColumn();

// Total customers
$stmt = $pdo->query("
    SELECT COUNT(DISTINCT u.id) 
    FROM users u
    JOIN user_roles ur ON u.id = ur.user_id
    JOIN roles r ON ur.role_id = r.id
    WHERE r.role_name = 'customer'
");
$stats['customers'] = $stmt->fetchColumn();

// Total revenue
$stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status_id >= 2");
$stats['revenue'] = $stmt->fetchColumn();

// Recent orders
$recentOrders = $pdo->query("
    SELECT o.*, os.status_name, u.first_name, u.last_name, u.email
    FROM orders o
    LEFT JOIN order_status os ON o.status_id = os.id
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 5
")->fetchAll();

// Low stock products
$lowStock = $pdo->query("
    SELECT p.*, i.quantity, i.reorder_level
    FROM products p
    JOIN inventory i ON p.id = i.product_id
    WHERE i.quantity <= i.reorder_level AND p.is_active = 1
    ORDER BY i.quantity ASC
    LIMIT 5
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1>Dashboard</h1>
                <p style="color: var(--text-secondary);">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="/ashels-autospare/admin/products.php?action=new" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Product
                </a>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-4" style="margin-bottom: 3rem;">
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: var(--accent-glow); border-radius: 0.5rem;">
                        <i class="fas fa-box" style="color: var(--accent-primary); font-size: 1.25rem;"></i>
                    </div>
                    <div>
                        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">Products</p>
                        <p style="font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; margin: 0;"><?php echo number_format($stats['products']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: rgba(34, 197, 94, 0.2); border-radius: 0.5rem;">
                        <i class="fas fa-shopping-cart" style="color: var(--success); font-size: 1.25rem;"></i>
                    </div>
                    <div>
                        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">Orders</p>
                        <p style="font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; margin: 0;"><?php echo number_format($stats['orders']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: rgba(59, 130, 246, 0.2); border-radius: 0.5rem;">
                        <i class="fas fa-users" style="color: var(--info); font-size: 1.25rem;"></i>
                    </div>
                    <div>
                        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">Customers</p>
                        <p style="font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; margin: 0;"><?php echo number_format($stats['customers']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: rgba(234, 179, 8, 0.2); border-radius: 0.5rem;">
                        <i class="fas fa-coins" style="color: var(--warning); font-size: 1.25rem;"></i>
                    </div>
                    <div>
                        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">Revenue</p>
                        <p style="font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; margin: 0;"><?php echo formatCurrency($stats['revenue']); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-2" style="gap: 2rem;">
            <!-- Recent Orders -->
            <div class="card">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0;">Recent Orders</h3>
                    <a href="/ashels-autospare/admin/orders.php" style="font-size: 0.875rem;">View All</a>
                </div>
                <div style="padding: 1rem;">
                    <?php if (empty($recentOrders)): ?>
                    <p class="text-center" style="padding: 2rem; color: var(--text-muted);">No orders yet.</p>
                    <?php else: ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.75rem; color: var(--text-muted);">ORDER</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.75rem; color: var(--text-muted);">CUSTOMER</th>
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.75rem; color: var(--text-muted);">STATUS</th>
                                <th style="padding: 0.75rem; text-align: right; font-size: 0.75rem; color: var(--text-muted);">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($order['order_number']); ?></td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
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
            
            <!-- Low Stock Alert -->
            <div class="card">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0;">Low Stock Alert</h3>
                    <a href="/ashels-autospare/admin/products.php?filter=low_stock" style="font-size: 0.875rem;">View All</a>
                </div>
                <div style="padding: 1rem;">
                    <?php if (empty($lowStock)): ?>
                    <p class="text-center" style="padding: 2rem; color: var(--text-muted);">All products are well stocked!</p>
                    <?php else: ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <th style="padding: 0.75rem; text-align: left; font-size: 0.75rem; color: var(--text-muted);">PRODUCT</th>
                                <th style="padding: 0.75rem; text-align: right; font-size: 0.75rem; color: var(--text-muted);">IN STOCK</th>
                                <th style="padding: 0.75rem; text-align: right; font-size: 0.75rem; color: var(--text-muted);">REORDER AT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lowStock as $product): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.75rem;">
                                    <div>
                                        <p style="margin: 0; font-weight: 600;"><?php echo htmlspecialchars($product['name']); ?></p>
                                        <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars($product['sku']); ?></p>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem; text-align: right;">
                                    <span style="color: <?php echo $product['quantity'] <= 0 ? 'var(--danger)' : 'var(--warning)'; ?>;">
                                        <?php echo $product['quantity']; ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem; text-align: right;"><?php echo $product['reorder_level']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div style="margin-top: 2rem;">
            <h3>Quick Actions</h3>
            <div class="grid grid-4" style="margin-top: 1rem;">
                <a href="/ashels-autospare/admin/products.php" class="card" style="padding: 1.5rem; text-align: center; text-decoration: none;">
                    <i class="fas fa-box" style="font-size: 2rem; color: var(--accent-primary); margin-bottom: 0.75rem;"></i>
                    <p style="margin: 0; color: var(--text-primary);">Manage Products</p>
                </a>
                
                <a href="/ashels-autospare/admin/orders.php" class="card" style="padding: 1.5rem; text-align: center; text-decoration: none;">
                    <i class="fas fa-shopping-cart" style="font-size: 2rem; color: var(--accent-primary); margin-bottom: 0.75rem;"></i>
                    <p style="margin: 0; color: var(--text-primary);">View Orders</p>
                </a>
                
                <a href="/ashels-autospare/admin/categories.php" class="card" style="padding: 1.5rem; text-align: center; text-decoration: none;">
                    <i class="fas fa-tags" style="font-size: 2rem; color: var(--accent-primary); margin-bottom: 0.75rem;"></i>
                    <p style="margin: 0; color: var(--text-primary);">Categories</p>
                </a>
                
                <a href="/ashels-autospare/admin/suppliers.php" class="card" style="padding: 1.5rem; text-align: center; text-decoration: none;">
                    <i class="fas fa-truck" style="font-size: 2rem; color: var(--accent-primary); margin-bottom: 0.75rem;"></i>
                    <p style="margin: 0; color: var(--text-primary);">Suppliers</p>
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
