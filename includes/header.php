<?php
/**
 * AURA Fashion E-Commerce - Site Header & Navigation
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require configuration and database connection if not already loaded
if (!isset($pdo)) {
    require_once __DIR__ . '/../config/db.php';
}

$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$navCartCount = isset($pdo) ? get_cart_count($pdo) : 0;
$navWishlistCount = isset($pdo) ? get_wishlist_count($pdo) : 0;
$flashes = get_flashes();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? h($pageTitle) . ' | AURA Atelier' : 'AURA — Modern Luxury Fashion Brand' ?></title>
  <meta name="description" content="AURA is a minimalist luxury fashion brand providing timeless apparel, curated outfits, AI stylist recommendations, and virtual try-on previews.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<!-- Announcement Bar -->
<div class="announcement-bar">
  <div class="container announcement-inner">
    <span>COMPLIMENTARY EXPRESS DELIVERY OVER ₹1,999</span>
    <span class="announcement-divider">|</span>
    <span>USE CODE <strong>AURA10</strong> FOR 10% OFF</span>
    <span class="announcement-divider">|</span>
    <span class="demo-tag">ACADEMIC DEMO PROJECT</span>
  </div>
</div>

<!-- Main Site Header -->
<header class="site-header">
  <div class="header-container">
    
    <!-- Mobile Hamburger Toggle -->
    <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle Navigation Menu">
      <span class="bar"></span>
      <span class="bar"></span>
      <span class="bar"></span>
    </button>

    <!-- Brand Logo -->
    <div class="logo-area">
      <a class="logo" href="index.php">AURA<span class="gold-dot">.</span></a>
      <span class="logo-sub">ATELIER</span>
    </div>

    <!-- Desktop Navigation -->
    <nav class="main-nav" id="mainNav">
      <a href="index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Home</a>
      <a href="products.php" class="<?= $currentPage === 'products.php' && empty($_GET['category']) ? 'active' : '' ?>">All Collection</a>
      <a href="products.php?category=Women" class="<?= ($_GET['category'] ?? '') === 'Women' ? 'active' : '' ?>">Women</a>
      <a href="products.php?category=Men" class="<?= ($_GET['category'] ?? '') === 'Men' ? 'active' : '' ?>">Men</a>
      <a href="products.php?category=Kids" class="<?= ($_GET['category'] ?? '') === 'Kids' ? 'active' : '' ?>">Kids</a>
      <a href="products.php?category=Accessories" class="<?= ($_GET['category'] ?? '') === 'Accessories' ? 'active' : '' ?>">Accessories</a>
      <a href="stylist.php" class="nav-highlight <?= $currentPage === 'stylist.php' ? 'active' : '' ?>">
        AI Stylist <span class="badge-subtle">Demo</span>
      </a>
      <a href="tryon.php" class="nav-highlight <?= $currentPage === 'tryon.php' ? 'active' : '' ?>">
        Virtual Try-On <span class="badge-subtle">Demo</span>
      </a>
    </nav>

    <!-- Header Action Icons / Links -->
    <div class="header-actions">
      <!-- Quick Search Form -->
      <form class="header-search-form" action="products.php" method="get">
        <input type="text" name="q" placeholder="Search curated edit..." value="<?= h($_GET['q'] ?? '') ?>" aria-label="Search">
        <button type="submit" aria-label="Submit search">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
      </form>

      <!-- Wishlist Link with Live Counter -->
      <a href="wishlist.php" class="action-link" title="Saved Wishlist" aria-label="Wishlist">
        <span class="action-icon">♡</span>
        <span class="action-label">Wishlist</span>
        <span class="counter-badge <?= $navWishlistCount > 0 ? 'visible' : '' ?>" id="wishlistCounter"><?= $navWishlistCount ?></span>
      </a>

      <!-- Cart Link with Live Counter -->
      <a href="cart.php" class="action-link bag-link" title="Shopping Bag" aria-label="Shopping Bag">
        <span class="action-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        </span>
        <span class="action-label">Bag</span>
        <span class="counter-badge <?= $navCartCount > 0 ? 'visible' : '' ?>" id="cartCounter"><?= $navCartCount ?></span>
      </a>

      <!-- User Account Menu -->
      <div class="account-menu-wrapper">
        <?php if (is_logged_in()): ?>
          <div class="user-dropdown">
            <button class="user-dropdown-btn" id="userMenuToggle">
              <span class="user-avatar"><?= strtoupper(substr(current_username(), 0, 1)) ?></span>
              <span class="user-greeting"><?= h(current_username()) ?></span>
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div class="dropdown-menu" id="userDropdownMenu">
              <div class="dropdown-header">
                <strong><?= h(current_username()) ?></strong>
                <small><?= h($_SESSION['email'] ?? 'Member') ?></small>
              </div>
              <a href="orders.php">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                My Orders
              </a>
              <a href="wishlist.php">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                My Wishlist (<?= $navWishlistCount ?>)
              </a>
              <a href="cart.php">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                Shopping Bag (<?= $navCartCount ?>)
              </a>
              <hr class="dropdown-divider">
              <a href="logout.php" class="logout-link">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Log Out
              </a>
            </div>
          </div>
        <?php else: ?>
          <div class="auth-links">
            <a href="login.php" class="auth-link">Sign In</a>
            <span class="auth-divider">/</span>
            <a href="register.php" class="auth-link auth-register">Register</a>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <!-- Mobile Slide Drawer Navigation -->
  <div class="mobile-drawer" id="mobileDrawer">
    <div class="mobile-drawer-header">
      <div class="logo">AURA<span class="gold-dot">.</span></div>
      <button class="mobile-close-btn" id="mobileCloseBtn" aria-label="Close Navigation Menu">&times;</button>
    </div>
    <div class="mobile-drawer-content">
      <form class="mobile-search-form" action="products.php" method="get">
        <input type="text" name="q" placeholder="Search apparel, bags, jewels..." value="<?= h($_GET['q'] ?? '') ?>">
        <button type="submit">Search</button>
      </form>
      <nav class="mobile-nav-links">
        <a href="index.php">Home</a>
        <a href="products.php">All Collection</a>
        <a href="products.php?category=Women">Women</a>
        <a href="products.php?category=Men">Men</a>
        <a href="products.php?category=Kids">Kids</a>
        <a href="products.php?category=Accessories">Accessories</a>
        <hr>
        <a href="stylist.php" class="highlight-drawer-link">
          AI Stylist Studio <span class="badge-subtle">Demo</span>
        </a>
        <a href="tryon.php" class="highlight-drawer-link">
          Virtual Try-On Room <span class="badge-subtle">Demo</span>
        </a>
        <hr>
        <a href="wishlist.php">Saved Wishlist (<?= $navWishlistCount ?>)</a>
        <a href="cart.php">Shopping Bag (<?= $navCartCount ?>)</a>
        <?php if (is_logged_in()): ?>
          <a href="orders.php">My Order History</a>
          <a href="logout.php" class="drawer-logout">Sign Out (<?= h(current_username()) ?>)</a>
        <?php else: ?>
          <a href="login.php">Sign In</a>
          <a href="register.php" class="drawer-register">Create an Account</a>
        <?php endif; ?>
      </nav>
    </div>
  </div>
  <div class="mobile-backdrop" id="mobileBackdrop"></div>
</header>

<!-- Database Connection Notice (Graceful fallback if MySQL is offline) -->
<?php if (isset($db_connected) && $db_connected === false): ?>
<div class="database-warning-banner">
  <div class="container warning-inner">
    <strong>Database Setup Required:</strong> Could not connect to MySQL database <code>aura_db</code>.
    Please ensure Apache and MySQL are running in your <strong>XAMPP Control Panel</strong>, and import <code>database.sql</code> via <a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a>.
  </div>
</div>
<?php endif; ?>

<!-- Flash Toast Notifications -->
<?php if (!empty($flashes)): ?>
<div class="toast-container" id="toastContainer">
  <?php foreach ($flashes as $flash): ?>
    <div class="toast-alert toast-<?= h($flash['type']) ?>" role="alert">
      <span class="toast-indicator"></span>
      <span class="toast-message"><?= h($flash['message']) ?></span>
      <button type="button" class="toast-close" onclick="this.parentElement.remove()" aria-label="Dismiss">&times;</button>
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Main Page Content Container -->
<main class="site-main">
