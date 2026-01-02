<?php
require_once __DIR__ . '/functions.php';
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ashel's Autospare - Premium auto parts and accessories. Quality spare parts for all vehicle makes and models.">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?>Ashel's Autospare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/ashels-autospare/assets/css/style.css">
</head>
<body class="dark-mode">
    <!-- Header -->
    <header class="main-header" id="mainHeader">
        <div class="container">
            <a href="/ashels-autospare/" class="logo">
                <i class="fas fa-cog logo-icon"></i>
                <span class="logo-text">ASHEL'S<span class="logo-accent">AUTOSPARE</span></span>
            </a>
            
            <nav class="main-nav" id="mainNav">
                <ul class="nav-list">
                    <li><a href="/ashels-autospare/" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                    <li class="has-dropdown">
                        <a href="/ashels-autospare/services.php" class="nav-link">Services <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown">
                            <li><a href="/ashels-autospare/services.php#spare-parts">Spare Parts Sales</a></li>
                            <li><a href="/ashels-autospare/services.php#inventory">Inventory Management</a></li>
                            <li><a href="/ashels-autospare/services.php#suppliers">Supplier Tracking</a></li>
                            <li><a href="/ashels-autospare/services.php#orders">Order Fulfillment</a></li>
                        </ul>
                    </li>
                    <li><a href="/ashels-autospare/products.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">Products</a></li>
                    <li><a href="/ashels-autospare/about.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">About Us</a></li>
                    <li><a href="/ashels-autospare/contact.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
                </ul>
            </nav>
            
            <div class="header-actions">
                <button class="theme-toggle" id="themeToggle" title="Toggle theme">
                    <i class="fas fa-sun"></i>
                </button>
                
                <?php if (isLoggedIn()): ?>
                    <div class="user-menu">
                        <button class="user-menu-toggle">
                            <i class="fas fa-user-circle"></i>
                            <span><?php echo htmlspecialchars($currentUser['first_name']); ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <ul class="user-dropdown">
                            <?php if (isAdmin()): ?>
                                <li><a href="/ashels-autospare/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a></li>
                            <?php elseif (isStaff()): ?>
                                <li><a href="/ashels-autospare/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Staff Dashboard</a></li>
                            <?php else: ?>
                                <li><a href="/ashels-autospare/customer/dashboard.php"><i class="fas fa-tachometer-alt"></i> My Dashboard</a></li>
                            <?php endif; ?>
                            <li><a href="/ashels-autospare/customer/orders.php"><i class="fas fa-shopping-bag"></i> My Orders</a></li>
                            <li><a href="/ashels-autospare/customer/profile.php"><i class="fas fa-user-cog"></i> Profile</a></li>
                            <li class="divider"></li>
                            <li><a href="/ashels-autospare/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="/ashels-autospare/auth/login.php" class="btn btn-outline btn-sm">Login</a>
                    <a href="/ashels-autospare/auth/register.php" class="btn btn-primary btn-sm">Sign Up</a>
                <?php endif; ?>
                
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>
    
    <!-- Flash Messages -->
    <?php 
    $flash = getFlashMessage();
    if ($flash): 
    ?>
    <div class="flash-messages">
        <?php foreach ($flash as $type => $message): ?>
            <div class="alert alert-<?php echo $type; ?>">
                <?php echo htmlspecialchars($message); ?>
                <button class="alert-close">&times;</button>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <main class="main-content">
