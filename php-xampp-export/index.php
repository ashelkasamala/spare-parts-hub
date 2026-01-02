<?php
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';

// Get featured products
$pdo = getDBConnection();
$stmt = $pdo->query("
    SELECT p.*, c.name as category_name, i.quantity as stock
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN inventory i ON p.id = i.product_id
    WHERE p.is_featured = 1 AND p.is_active = 1
    LIMIT 4
");
$featuredProducts = $stmt->fetchAll();

// Get categories
$categories = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order")->fetchAll();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-tools"></i>
                Premium Auto Parts
            </div>
            <h1>Quality Parts for <span class="highlight">Every Vehicle</span></h1>
            <p class="hero-text">
                Your trusted source for genuine auto spare parts. We provide quality components for all makes and models with guaranteed authenticity.
            </p>
            <div class="hero-buttons">
                <a href="/ashels-autospare/products.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-search"></i> Browse Parts
                </a>
                <a href="/ashels-autospare/auth/login.php" class="btn btn-outline btn-lg">
                    <i class="fab fa-google"></i> Login with Gmail
                </a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-value">10K+</div>
                    <div class="stat-label">Products</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">5K+</div>
                    <div class="stat-label">Customers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">15+</div>
                    <div class="stat-label">Years</div>
                </div>
            </div>
        </div>
        <div class="hero-image">
            <img src="/ashels-autospare/assets/images/hero-garage.jpg" alt="Auto parts warehouse">
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="section" id="services">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">Our Services</span>
            <h2 class="section-title">What We Offer</h2>
            <p class="section-description">Comprehensive automotive solutions for individuals and businesses alike.</p>
        </div>
        
        <div class="grid grid-3">
            <div class="card service-card">
                <div class="service-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <h3 class="card-title">Spare Parts Sales</h3>
                <p class="card-text">Genuine parts for all vehicle makes and models with warranty support.</p>
            </div>
            
            <div class="card service-card">
                <div class="service-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <h3 class="card-title">Inventory Management</h3>
                <p class="card-text">Real-time stock tracking and automated reorder notifications.</p>
            </div>
            
            <div class="card service-card">
                <div class="service-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3 class="card-title">Supplier Network</h3>
                <p class="card-text">Established relationships with verified global suppliers.</p>
            </div>
            
            <div class="card service-card">
                <div class="service-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h3 class="card-title">Fast Delivery</h3>
                <p class="card-text">Quick order processing and nationwide delivery options.</p>
            </div>
            
            <div class="card service-card">
                <div class="service-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3 class="card-title">Customer Accounts</h3>
                <p class="card-text">Personalized dashboards with order history and tracking.</p>
            </div>
            
            <div class="card service-card">
                <div class="service-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="card-title">Expert Support</h3>
                <p class="card-text">Technical assistance from experienced automotive professionals.</p>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="section" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">Why Choose Us</span>
            <h2 class="section-title">The Ashel's Advantage</h2>
            <p class="section-description">What sets us apart from the competition.</p>
        </div>
        
        <div class="grid grid-4">
            <div class="card service-card">
                <div class="service-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3 class="card-title">Fast Search</h3>
                <p class="card-text">Find any part instantly with our powerful search.</p>
            </div>
            
            <div class="card service-card">
                <div class="service-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="card-title">Verified Parts</h3>
                <p class="card-text">Every part is quality-tested before dispatch.</p>
            </div>
            
            <div class="card service-card">
                <div class="service-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h3 class="card-title">Secure Payments</h3>
                <p class="card-text">Safe transactions with multiple payment options.</p>
            </div>
            
            <div class="card service-card">
                <div class="service-icon">
                    <i class="fas fa-sync"></i>
                </div>
                <h3 class="card-title">Real-time Stock</h3>
                <p class="card-text">Live inventory updates for accurate availability.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="section" id="products">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">Featured Products</span>
            <h2 class="section-title">Popular Parts</h2>
            <p class="section-description">Top-selling auto parts chosen by our customers.</p>
        </div>
        
        <div class="grid grid-4">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="card product-card">
                <div class="card-image">
                    <?php if ($product['is_featured']): ?>
                    <span class="card-badge">Featured</span>
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
                    <div class="card-price"><?php echo formatCurrency($product['price']); ?></div>
                    <div class="card-actions">
                        <a href="/ashels-autospare/product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-outline btn-sm">View Details</a>
                        <button class="btn btn-primary btn-sm">
                            <i class="fas fa-cart-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($featuredProducts)): ?>
            <div class="text-center" style="grid-column: 1 / -1; padding: 3rem;">
                <p class="text-muted">No featured products available yet.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="/ashels-autospare/products.php" class="btn btn-outline">View All Products <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="section" id="about" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="grid grid-2" style="align-items: center;">
            <div>
                <span class="section-badge">About Us</span>
                <h2 class="section-title">Trusted Auto Parts Since 2009</h2>
                <p>At Ashel's Autospare, we've built our reputation on providing genuine, quality auto parts to customers across Kenya. With over 15 years in the industry, we understand what your vehicle needs.</p>
                <p>Our commitment to authenticity and customer satisfaction has made us the go-to destination for both individual car owners and commercial fleet operators.</p>
                <ul style="list-style: none; margin-top: 1.5rem;">
                    <li style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <i class="fas fa-check-circle" style="color: var(--accent-primary);"></i>
                        100% Genuine Parts Guarantee
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <i class="fas fa-check-circle" style="color: var(--accent-primary);"></i>
                        Expert Technical Support
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <i class="fas fa-check-circle" style="color: var(--accent-primary);"></i>
                        Nationwide Delivery Network
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-check-circle" style="color: var(--accent-primary);"></i>
                        Competitive Wholesale Pricing
                    </li>
                </ul>
            </div>
            <div>
                <img src="/ashels-autospare/assets/images/about-garage.jpg" alt="Ashel's Autospare warehouse" 
                     style="border-radius: 1rem; box-shadow: var(--shadow-lg);">
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="section" id="contact">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">Contact Us</span>
            <h2 class="section-title">Get In Touch</h2>
            <p class="section-description">Have questions? We're here to help.</p>
        </div>
        
        <div class="grid grid-2" style="gap: 4rem;">
            <div>
                <form action="/ashels-autospare/contact.php" method="POST" data-validate>
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="form-group">
                        <label class="form-label" for="name">Your Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="message">Message</label>
                        <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
            
            <div>
                <div class="card" style="padding: 2rem;">
                    <h3 style="margin-bottom: 1.5rem;">Contact Information</h3>
                    
                    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                        <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: var(--accent-glow); border-radius: 0.5rem;">
                            <i class="fas fa-map-marker-alt" style="color: var(--accent-primary);"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 1rem; margin-bottom: 0.25rem;">Address</h4>
                            <p style="color: var(--text-secondary); margin: 0;">Industrial Area, Nairobi, Kenya</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                        <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: var(--accent-glow); border-radius: 0.5rem;">
                            <i class="fas fa-phone" style="color: var(--accent-primary);"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 1rem; margin-bottom: 0.25rem;">Phone</h4>
                            <p style="color: var(--text-secondary); margin: 0;">+254 700 123 456</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                        <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: var(--accent-glow); border-radius: 0.5rem;">
                            <i class="fas fa-envelope" style="color: var(--accent-primary);"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 1rem; margin-bottom: 0.25rem;">Email</h4>
                            <p style="color: var(--text-secondary); margin: 0;">info@ashelsautospare.com</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: var(--accent-glow); border-radius: 0.5rem;">
                            <i class="fas fa-clock" style="color: var(--accent-primary);"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 1rem; margin-bottom: 0.25rem;">Business Hours</h4>
                            <p style="color: var(--text-secondary); margin: 0;">Mon - Sat: 8:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
