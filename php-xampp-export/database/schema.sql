-- ============================================
-- Ashel's Autospare Database Schema
-- Compatible with MySQL 5.7+ / MariaDB 10.2+
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS ashels_autospare;
USE ashels_autospare;

-- ============================================
-- ENUM-LIKE TABLES FOR ROLES AND STATUS
-- ============================================

-- Roles Table
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default roles
INSERT INTO roles (role_name, description) VALUES
('admin', 'Full system access'),
('staff', 'Manage inventory and orders'),
('customer', 'Browse and purchase products');

-- Order Status Table
CREATE TABLE order_status (
    id INT PRIMARY KEY AUTO_INCREMENT,
    status_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255)
);

INSERT INTO order_status (status_name, description) VALUES
('pending', 'Order placed, awaiting processing'),
('processing', 'Order is being prepared'),
('shipped', 'Order has been shipped'),
('delivered', 'Order delivered to customer'),
('cancelled', 'Order was cancelled');

-- Payment Status Table
CREATE TABLE payment_status (
    id INT PRIMARY KEY AUTO_INCREMENT,
    status_name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO payment_status (status_name) VALUES
('pending'),
('completed'),
('failed'),
('refunded');

-- ============================================
-- CORE TABLES
-- ============================================

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    avatar_url VARCHAR(500),
    google_id VARCHAR(255) UNIQUE,
    email_verified BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_google_id (google_id)
);

-- User Roles (Many-to-Many relationship)
CREATE TABLE user_roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    assigned_by INT,
    UNIQUE KEY unique_user_role (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Categories Table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image_url VARCHAR(500),
    parent_id INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_parent (parent_id)
);

-- Suppliers Table
CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_name VARCHAR(200) NOT NULL,
    contact_person VARCHAR(100),
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100),
    website VARCHAR(255),
    notes TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_company (company_name)
);

-- Products Table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sku VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT,
    short_description VARCHAR(500),
    price DECIMAL(10, 2) NOT NULL,
    cost_price DECIMAL(10, 2),
    compare_at_price DECIMAL(10, 2),
    category_id INT,
    supplier_id INT,
    brand VARCHAR(100),
    model VARCHAR(100),
    year_from INT,
    year_to INT,
    weight DECIMAL(8, 2),
    dimensions VARCHAR(100),
    image_url VARCHAR(500),
    gallery_images JSON,
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    meta_title VARCHAR(200),
    meta_description VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    INDEX idx_sku (sku),
    INDEX idx_slug (slug),
    INDEX idx_category (category_id),
    INDEX idx_featured (is_featured),
    INDEX idx_price (price),
    FULLTEXT INDEX idx_search (name, description, brand, model)
);

-- Inventory Table
CREATE TABLE inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL UNIQUE,
    quantity INT NOT NULL DEFAULT 0,
    reserved_quantity INT DEFAULT 0,
    reorder_level INT DEFAULT 10,
    reorder_quantity INT DEFAULT 50,
    location VARCHAR(100),
    last_restock_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_quantity (quantity),
    INDEX idx_reorder (reorder_level)
);

-- Inventory Log (Track stock changes)
CREATE TABLE inventory_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    quantity_change INT NOT NULL,
    previous_quantity INT NOT NULL,
    new_quantity INT NOT NULL,
    reason ENUM('purchase', 'sale', 'adjustment', 'return', 'damage') NOT NULL,
    reference_id INT,
    reference_type VARCHAR(50),
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_product (product_id),
    INDEX idx_date (created_at)
);

-- Customer Addresses
CREATE TABLE customer_addresses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    address_type ENUM('billing', 'shipping') DEFAULT 'shipping',
    is_default BOOLEAN DEFAULT FALSE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    company VARCHAR(200),
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
);

-- Orders Table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    user_id INT,
    status_id INT NOT NULL DEFAULT 1,
    subtotal DECIMAL(10, 2) NOT NULL,
    tax_amount DECIMAL(10, 2) DEFAULT 0,
    shipping_amount DECIMAL(10, 2) DEFAULT 0,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    total_amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'KES',
    billing_address_id INT,
    shipping_address_id INT,
    shipping_method VARCHAR(100),
    tracking_number VARCHAR(100),
    notes TEXT,
    customer_notes TEXT,
    ordered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (status_id) REFERENCES order_status(id),
    FOREIGN KEY (billing_address_id) REFERENCES customer_addresses(id) ON DELETE SET NULL,
    FOREIGN KEY (shipping_address_id) REFERENCES customer_addresses(id) ON DELETE SET NULL,
    INDEX idx_order_number (order_number),
    INDEX idx_user (user_id),
    INDEX idx_status (status_id),
    INDEX idx_date (ordered_at)
);

-- Order Items Table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(200) NOT NULL,
    product_sku VARCHAR(50),
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
);

-- Payments Table
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    payment_method ENUM('cash', 'mpesa', 'card', 'bank_transfer') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'KES',
    status_id INT NOT NULL DEFAULT 1,
    transaction_id VARCHAR(100),
    payment_reference VARCHAR(100),
    payment_details JSON,
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (status_id) REFERENCES payment_status(id),
    INDEX idx_order (order_id),
    INDEX idx_transaction (transaction_id)
);

-- Shopping Cart
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    session_id VARCHAR(100),
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_session (session_id)
);

-- Wishlist
CREATE TABLE wishlist (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wishlist (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Product Reviews
CREATE TABLE product_reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(200),
    review TEXT,
    is_verified_purchase BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    INDEX idx_rating (rating)
);

-- Contact Messages
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    replied_at TIMESTAMP NULL,
    replied_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (replied_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_read (is_read),
    INDEX idx_date (created_at)
);

-- Newsletter Subscribers
CREATE TABLE newsletter_subscribers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    is_active BOOLEAN DEFAULT TRUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL
);

-- Activity Log (Audit Trail)
CREATE TABLE activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_date (created_at)
);

-- Sessions Table (for PHP session management)
CREATE TABLE sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    payload TEXT,
    last_activity INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_activity (last_activity)
);

-- ============================================
-- VIEWS FOR COMMON QUERIES
-- ============================================

-- Products with inventory view
CREATE VIEW v_products_with_inventory AS
SELECT 
    p.*,
    c.name AS category_name,
    s.company_name AS supplier_name,
    i.quantity AS stock_quantity,
    i.reserved_quantity,
    (i.quantity - COALESCE(i.reserved_quantity, 0)) AS available_quantity,
    CASE 
        WHEN i.quantity <= 0 THEN 'out_of_stock'
        WHEN i.quantity <= i.reorder_level THEN 'low_stock'
        ELSE 'in_stock'
    END AS stock_status
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN suppliers s ON p.supplier_id = s.id
LEFT JOIN inventory i ON p.id = i.product_id;

-- Orders with details view
CREATE VIEW v_orders_summary AS
SELECT 
    o.*,
    os.status_name AS status,
    u.email AS customer_email,
    CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
    COUNT(oi.id) AS total_items,
    SUM(oi.quantity) AS total_quantity
FROM orders o
LEFT JOIN order_status os ON o.status_id = os.id
LEFT JOIN users u ON o.user_id = u.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id;

-- ============================================
-- STORED PROCEDURES
-- ============================================

DELIMITER //

-- Check if user has a specific role
CREATE FUNCTION has_role(p_user_id INT, p_role_name VARCHAR(50))
RETURNS BOOLEAN
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE role_exists BOOLEAN DEFAULT FALSE;
    
    SELECT EXISTS(
        SELECT 1 
        FROM user_roles ur
        JOIN roles r ON ur.role_id = r.id
        WHERE ur.user_id = p_user_id AND r.role_name = p_role_name
    ) INTO role_exists;
    
    RETURN role_exists;
END //

-- Generate unique order number
CREATE FUNCTION generate_order_number()
RETURNS VARCHAR(50)
DETERMINISTIC
BEGIN
    DECLARE new_order_number VARCHAR(50);
    SET new_order_number = CONCAT('ORD-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));
    RETURN new_order_number;
END //

-- Update inventory after order
CREATE PROCEDURE update_inventory_after_order(
    IN p_product_id INT,
    IN p_quantity INT,
    IN p_order_id INT
)
BEGIN
    DECLARE current_qty INT;
    
    SELECT quantity INTO current_qty FROM inventory WHERE product_id = p_product_id;
    
    UPDATE inventory 
    SET quantity = quantity - p_quantity,
        updated_at = NOW()
    WHERE product_id = p_product_id;
    
    INSERT INTO inventory_log (product_id, quantity_change, previous_quantity, new_quantity, reason, reference_id, reference_type)
    VALUES (p_product_id, -p_quantity, current_qty, current_qty - p_quantity, 'sale', p_order_id, 'order');
END //

DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================

DELIMITER //

-- Auto-create inventory record when product is created
CREATE TRIGGER after_product_insert
AFTER INSERT ON products
FOR EACH ROW
BEGIN
    INSERT INTO inventory (product_id, quantity) VALUES (NEW.id, 0);
END //

-- Log inventory changes
CREATE TRIGGER after_inventory_update
AFTER UPDATE ON inventory
FOR EACH ROW
BEGIN
    IF OLD.quantity != NEW.quantity THEN
        INSERT INTO inventory_log (product_id, quantity_change, previous_quantity, new_quantity, reason)
        VALUES (NEW.product_id, NEW.quantity - OLD.quantity, OLD.quantity, NEW.quantity, 'adjustment');
    END IF;
END //

DELIMITER ;

-- ============================================
-- SAMPLE DATA
-- ============================================

-- Insert admin user (password: Admin123!)
INSERT INTO users (email, password_hash, first_name, last_name, email_verified, is_active)
VALUES ('admin@ashelsautospare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', TRUE, TRUE);

-- Assign admin role
INSERT INTO user_roles (user_id, role_id) VALUES (1, 1);

-- Insert sample categories
INSERT INTO categories (name, slug, description, sort_order) VALUES
('Engine Parts', 'engine-parts', 'Engine components and accessories', 1),
('Brakes', 'brakes', 'Brake pads, discs, and brake systems', 2),
('Suspension', 'suspension', 'Suspension parts and shock absorbers', 3),
('Filters', 'filters', 'Oil, air, and fuel filters', 4),
('Electrical', 'electrical', 'Batteries, alternators, and electrical components', 5),
('Body Parts', 'body-parts', 'Exterior and interior body components', 6);

-- Insert sample supplier
INSERT INTO suppliers (company_name, contact_person, email, phone, address, city, country)
VALUES ('AutoParts Kenya Ltd', 'John Kamau', 'info@autopartskenya.co.ke', '+254 700 123456', 'Industrial Area', 'Nairobi', 'Kenya');

-- Insert sample products
INSERT INTO products (sku, name, slug, description, short_description, price, category_id, supplier_id, brand, is_featured) VALUES
('ENG-001', 'Toyota Camry Engine Mount', 'toyota-camry-engine-mount', 'High-quality engine mount for Toyota Camry 2015-2023', 'Durable engine mount for smooth performance', 4500.00, 1, 1, 'Toyota', TRUE),
('BRK-001', 'Ceramic Brake Pads Set', 'ceramic-brake-pads-set', 'Premium ceramic brake pads for superior stopping power', 'Low dust, quiet operation brake pads', 3200.00, 2, 1, 'Brembo', TRUE),
('SUS-001', 'Front Shock Absorber Pair', 'front-shock-absorber-pair', 'Heavy-duty front shock absorbers for SUVs', 'Enhanced ride comfort and control', 8500.00, 3, 1, 'Monroe', TRUE),
('FLT-001', 'Premium Oil Filter', 'premium-oil-filter', 'High-efficiency oil filter for all vehicle types', 'Removes 99% of contaminants', 850.00, 4, 1, 'Bosch', TRUE);

-- Update inventory for sample products
UPDATE inventory SET quantity = 50, reorder_level = 10 WHERE product_id = 1;
UPDATE inventory SET quantity = 100, reorder_level = 20 WHERE product_id = 2;
UPDATE inventory SET quantity = 30, reorder_level = 5 WHERE product_id = 3;
UPDATE inventory SET quantity = 200, reorder_level = 50 WHERE product_id = 4;
