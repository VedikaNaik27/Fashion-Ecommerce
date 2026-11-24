<?php
/**
 * AURA Fashion Atelier - Product Catalog & Filtering
 */
require_once "config/db.php";

$pageTitle = "Curated Collection";

$category = trim($_GET["category"] ?? "");
$search   = trim($_GET["q"] ?? "");
$sort     = trim($_GET["sort"] ?? "newest");
$priceRange = trim($_GET["price_range"] ?? "");

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

// Filter by Category
if ($category !== "") {
    $sql .= " AND category = ?";
    $params[] = $category;
}

// Search by Keyword
if ($search !== "") {
    $sql .= " AND (name LIKE ? OR description LIKE ? OR category LIKE ? OR style_tag LIKE ?)";
    $term = "%" . $search . "%";
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}

// Filter by Price Range
if ($priceRange === "under_1000") {
    $sql .= " AND price < 1000";
} elseif ($priceRange === "1000_2500") {
    $sql .= " AND price >= 1000 AND price <= 2500";
} elseif ($priceRange === "above_2500") {
    $sql .= " AND price > 2500";
}

// Sorting logic
switch ($sort) {
    case "price_asc":
        $sql .= " ORDER BY price ASC";
        break;
    case "price_desc":
        $sql .= " ORDER BY price DESC";
        break;
    case "rating":
        $sql .= " ORDER BY rating DESC";
        break;
    case "newest":
    default:
        $sql .= " ORDER BY product_id DESC";
        break;
}

$products = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();
    } catch (PDOException $e) {
        $products = [];
    }
}

require "includes/header.php";
?>

<div class="catalog-page">
  <div class="container">
    
    <!-- Catalog Header -->
    <div class="catalog-header">
      <span class="eyebrow">THE AURA EDIT</span>
      <h1 class="section-title">
        <?= $category ? h($category) . " Collection" : "All Garments &amp; Objects" ?>
      </h1>
      <p class="section-subtitle">
        Timeless wardrobe foundations crafted with deliberate simplicity and ethical craftsmanship.
      </p>
    </div>

    <!-- Filter & Sort Toolbar -->
    <div class="filter-toolbar">
      
      <!-- Category Quick Filter Pills -->
      <div class="category-filter-pills">
        <a href="products.php<?= $search ? '?q=' . urlencode($search) : '' ?>" 
           class="filter-pill <?= empty($category) ? 'active' : '' ?>">All (All)</a>
        
        <a href="products.php?category=Women<?= $search ? '&q=' . urlencode($search) : '' ?>" 
           class="filter-pill <?= $category === 'Women' ? 'active' : '' ?>">Women</a>
        
        <a href="products.php?category=Men<?= $search ? '&q=' . urlencode($search) : '' ?>" 
           class="filter-pill <?= $category === 'Men' ? 'active' : '' ?>">Men</a>
        
        <a href="products.php?category=Kids<?= $search ? '&q=' . urlencode($search) : '' ?>" 
           class="filter-pill <?= $category === 'Kids' ? 'active' : '' ?>">Kids</a>
        
        <a href="products.php?category=Accessories<?= $search ? '&q=' . urlencode($search) : '' ?>" 
           class="filter-pill <?= $category === 'Accessories' ? 'active' : '' ?>">Accessories</a>
      </div>

      <!-- Search & Sort Controls Form -->
      <form class="filter-controls-right" method="get" action="products.php">
        <?php if ($category): ?>
          <input type="hidden" name="category" value="<?= h($category) ?>">
        <?php endif; ?>

        <!-- Search Input -->
        <input type="text" name="q" placeholder="Search keywords..." value="<?= h($search) ?>" 
               style="padding: 7px 12px; border: 1px solid var(--color-border); border-radius: var(--radius-xs); font-size: 12px; width: 170px;">

        <!-- Price Range Filter -->
        <div class="sort-select-wrap">
          <label for="priceRangeSelect">Price:</label>
          <select name="price_range" id="priceRangeSelect" onchange="this.form.submit()">
            <option value="">All Prices</option>
            <option value="under_1000" <?= $priceRange === 'under_1000' ? 'selected' : '' ?>>Under ₹1,000</option>
            <option value="1000_2500" <?= $priceRange === '1000_2500' ? 'selected' : '' ?>>₹1,000 - ₹2,500</option>
            <option value="above_2500" <?= $priceRange === 'above_2500' ? 'selected' : '' ?>>Above ₹2,500</option>
          </select>
        </div>

        <!-- Sort Select -->
        <div class="sort-select-wrap">
          <label for="sortSelect">Sort:</label>
          <select name="sort" id="sortSelect" onchange="this.form.submit()">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest Arrivals</option>
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
            <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Highest Rated</option>
          </select>
        </div>

        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
      </form>

    </div>

    <!-- Active Filters Summary Banner -->
    <?php if ($category || $search || $priceRange): ?>
      <div class="active-filter-banner">
        <div>
          Showing <strong><?= count($products) ?></strong> item<?= count($products) === 1 ? '' : 's' ?>
          <?php if ($category): ?> &bull; Category: <em><?= h($category) ?></em><?php endif; ?>
          <?php if ($search): ?> &bull; Search: &ldquo;<em><?= h($search) ?></em>&rdquo;<?php endif; ?>
          <?php if ($priceRange): ?> &bull; Filter: <em><?= h(str_replace('_', ' ', $priceRange)) ?></em><?php endif; ?>
        </div>
        <a href="products.php" class="clear-filter-btn">&times; Clear All Filters</a>
      </div>
    <?php endif; ?>

    <!-- Product Grid -->
    <div class="product-grid">
      <?php foreach ($products as $p): ?>
        <article class="product-card">
          <div class="product-thumb-wrap">
            <a href="product_details.php?id=<?= (int)$p['product_id'] ?>">
              <img src="<?= h($p['image'] ?: 'assets/placeholder.svg') ?>" alt="<?= h($p['name']) ?>" onerror="this.src='assets/placeholder.svg'">
            </a>
            <?php if (!empty($p['tag'])): ?>
              <span class="product-badge <?= strpos($p['tag'], 'Atelier') !== false || strpos($p['tag'], 'Best') !== false ? 'gold' : '' ?>">
                <?= h($p['tag']) ?>
              </span>
            <?php endif; ?>
            <form method="post" action="wishlist_action.php" class="card-wishlist-form">
              <?= csrf_field() ?>
              <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">
              <button type="submit" name="action" value="toggle" class="wishlist-btn-round" title="Save to Wishlist" aria-label="Save to Wishlist">
                ♡
              </button>
            </form>
          </div>
          
          <div class="product-info">
            <div class="product-meta">
              <span class="product-category"><?= h($p['category']) ?></span>
              <?= render_rating_stars($p['rating'] ?? 4.5) ?>
            </div>
            
            <h3 class="product-name">
              <a href="product_details.php?id=<?= (int)$p['product_id'] ?>"><?= h($p['name']) ?></a>
            </h3>
            
            <p class="product-desc-snippet"><?= h($p['description']) ?></p>
            
            <div class="product-price-row">
              <span class="product-price"><?= format_price($p['price']) ?></span>
              <span class="product-stock-pill"><?= (int)($p['stock'] ?? 50) > 0 ? 'In Stock' : 'Pre-order' ?></span>
            </div>

            <div class="product-actions-bar">
              <form method="post" action="cart_action.php" style="width: 100%;">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">
                <button type="submit" name="action" value="add" class="btn-add-bag" style="width: 100%;">
                  + Add to Bag
                </button>
              </form>
              <a href="product_details.php?id=<?= (int)$p['product_id'] ?>" class="btn-view-item" title="View details">
                &rarr;
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <!-- Empty State -->
    <?php if (empty($products)): ?>
      <div class="empty-state">
        <div class="empty-state-icon">✦</div>
        <h3>No products found</h3>
        <p>We could not find any garments matching your selected filters or search query. Try broadening your criteria or reset the filters.</p>
        <a href="products.php" class="btn btn-primary">Reset All Filters</a>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php require "includes/footer.php"; ?>
