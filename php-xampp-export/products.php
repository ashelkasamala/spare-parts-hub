<?php
$pageTitle = 'Products';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDBConnection();

// Get filter parameters
$category = sanitize($_GET['category'] ?? '');
$search = sanitize($_GET['search'] ?? '');
$sort = sanitize($_GET['sort'] ?? 'newest');
$minPrice = (float)($_GET['min_price'] ?? 0);
$maxPrice = (float)($_GET['max_price'] ?? 999999);

// Build query
$where = ["p.is_active = 1"];
$params = [];

if ($category) {
    $where[] = "c.slug = ?";
    $params[] = $category;
}

if ($search) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if ($minPrice > 0) {
    $where[] = "p.price >= ?";
    $params[] = $minPrice;
}

if ($maxPrice < 999999) {
    $where[] = "p.price <= ?";
    $params[] = $maxPrice;
}

$whereClause = implode(' AND ', $where);

// Sort options
$orderBy = match($sort) {
    'price_asc' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'name' => 'p.name ASC',
    default => 'p.created_at DESC'
};

// Get products with pagination
$query = "
    SELECT p.*, c.name as category_name, c.slug as category_slug, i.quantity as stock
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN inventory i ON p.id = i.product_id
    WHERE $whereClause
    ORDER BY $orderBy
";

$pagination = paginate($query, $params, 12);
$products = $pagination['data'];

// Get all categories for filter
$categories = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h1>Our Products</h1>
            <p class="section-description">Browse our extensive collection of quality auto parts</p>
        </div>
        
        <div style="display: grid; grid-template-columns: 280px 1fr; gap: 2rem;">
            <!-- Sidebar Filters -->
            <aside>
                <div class="card" style="padding: 1.5rem; position: sticky; top: calc(var(--header-height) + 1rem);">
                    <h3 style="margin-bottom: 1.5rem;">Filters</h3>
                    
                    <form method="GET" action="">
                        <!-- Search -->
                        <div class="form-group">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search products..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        
                        <!-- Categories -->
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-control">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['slug']); ?>" 
                                        <?php echo $category === $cat['slug'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Price Range -->
                        <div class="form-group">
                            <label class="form-label">Price Range (KES)</label>
                            <div style="display: flex; gap: 0.5rem;">
                                <input type="number" name="min_price" class="form-control" 
                                       placeholder="Min" value="<?php echo $minPrice > 0 ? $minPrice : ''; ?>">
                                <input type="number" name="max_price" class="form-control" 
                                       placeholder="Max" value="<?php echo $maxPrice < 999999 ? $maxPrice : ''; ?>">
                            </div>
                        </div>
                        
                        <!-- Sort -->
                        <div class="form-group">
                            <label class="form-label">Sort By</label>
                            <select name="sort" class="form-control">
                                <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                                <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Name: A to Z</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        
                        <?php if ($search || $category || $minPrice > 0 || $maxPrice < 999999): ?>
                        <a href="/ashels-autospare/products.php" class="btn btn-secondary" style="width: 100%; margin-top: 0.5rem;">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </aside>
            
            <!-- Products Grid -->
            <div>
                <?php if ($search || $category): ?>
                <p style="margin-bottom: 1rem; color: var(--text-secondary);">
                    Showing <?php echo $pagination['total']; ?> result(s)
                    <?php if ($search): ?> for "<?php echo htmlspecialchars($search); ?>"<?php endif; ?>
                    <?php if ($category): ?> in <?php echo htmlspecialchars($category); ?><?php endif; ?>
                </p>
                <?php endif; ?>
                
                <?php if (empty($products)): ?>
                <div class="card text-center" style="padding: 4rem;">
                    <i class="fas fa-search" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                    <h3>No Products Found</h3>
                    <p style="color: var(--text-secondary);">Try adjusting your search or filter criteria.</p>
                    <a href="/ashels-autospare/products.php" class="btn btn-primary">View All Products</a>
                </div>
                <?php else: ?>
                <div class="grid grid-3">
                    <?php foreach ($products as $product): ?>
                    <div class="card product-card">
                        <div class="card-image">
                            <?php if ($product['is_featured']): ?>
                            <span class="card-badge">Featured</span>
                            <?php elseif ($product['stock'] <= 0): ?>
                            <span class="card-badge" style="background: var(--danger);">Out of Stock</span>
                            <?php endif; ?>
                            <img src="<?php echo $product['image_url'] ?: '/ashels-autospare/assets/images/placeholder.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="card-body">
                            <p class="card-text" style="color: var(--accent-primary); font-size: 0.75rem;">
                                <?php echo htmlspecialchars($product['category_name']); ?>
                            </p>
                            <h3 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="card-text"><?php echo htmlspecialchars($product['short_description']); ?></p>
                            
                            <?php if ($product['compare_at_price'] && $product['compare_at_price'] > $product['price']): ?>
                            <p style="margin: 0; color: var(--text-muted); text-decoration: line-through; font-size: 0.875rem;">
                                <?php echo formatCurrency($product['compare_at_price']); ?>
                            </p>
                            <?php endif; ?>
                            
                            <div class="card-price"><?php echo formatCurrency($product['price']); ?></div>
                            
                            <div class="card-actions">
                                <a href="/ashels-autospare/product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-outline btn-sm">
                                    View Details
                                </a>
                                <?php if ($product['stock'] > 0): ?>
                                <button class="btn btn-primary btn-sm" onclick="addToCart(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <?php echo renderPagination($pagination); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
function addToCart(productId) {
    // This would typically make an AJAX call to add the product to cart
    showToast('Product added to cart!', 'success');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
