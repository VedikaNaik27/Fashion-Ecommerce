<?php
/**
 * AURA Fashion Atelier - Homepage
 */
require_once "config/db.php";

$pageTitle = "Modern Minimalist Fashion";

// Query Featured Products
$featuredProducts = [];
$newArrivals = [];

if (isset($pdo)) {
    try {
        $stmtFeatured = $pdo->query("SELECT * FROM products WHERE is_featured = 1 ORDER BY rating DESC LIMIT 4");
        $featuredProducts = $stmtFeatured->fetchAll();

        $stmtNew = $pdo->query("SELECT * FROM products ORDER BY product_id DESC LIMIT 4");
        $newArrivals = $stmtNew->fetchAll();
    } catch (PDOException $e) {
        // Fallback gracefully if database table is empty or error occurs
        $featuredProducts = [];
        $newArrivals = [];
    }
}

require "includes/header.php";
?>

<!-- 1. Hero Section -->
<section class="hero-section">
  <div class="container hero-grid">
    <div class="hero-content">
      <span class="eyebrow">NEW SEASON &bull; AUTUMN / WINTER 2026</span>
      <h1 class="hero-title">Wear your<br><em>confidence.</em></h1>
      <p class="hero-desc">
        Discover thoughtfully selected fashion woven in European linen, Mongolian cashmere, and pure mulberry silk. Minimalist tailoring crafted for effortless modern refinement.
      </p>
      <div class="hero-cta-group">
        <a href="products.php" class="btn btn-primary">Explore Collection &rarr;</a>
        <a href="stylist.php" class="btn btn-outline-gold">AI Stylist Studio</a>
      </div>
    </div>
    
    <div class="hero-art-wrapper">
      <div class="hero-image-frame">
        <img src="assets/images/hero_fashion.svg" alt="AURA Atelier Autumn Winter Collection" onerror="this.src='assets/placeholder.svg'">
      </div>
      <div class="hero-badge-floating">
        <span>AURA ATELIER</span>
        <small>100% Sustainable Craft</small>
      </div>
    </div>
  </div>
</section>

<!-- 2. Brand Value Proposition Ribbon -->
<section class="props-ribbon">
  <div class="container props-grid">
    <div class="prop-card">
      <div class="prop-icon">🚚</div>
      <div class="prop-text">
        <h4>Complimentary Shipping</h4>
        <p>On all domestic orders above ₹1,999</p>
      </div>
    </div>
    <div class="prop-card">
      <div class="prop-icon">🌿</div>
      <div class="prop-text">
        <h4>Sustainable Fabrics</h4>
        <p>100% certified organic and natural fibers</p>
      </div>
    </div>
    <div class="prop-card">
      <div class="prop-icon">↺</div>
      <div class="prop-text">
        <h4>Effortless Returns</h4>
        <p>7-day complimentary doorstep exchanges</p>
      </div>
    </div>
    <div class="prop-card">
      <div class="prop-icon">✦</div>
      <div class="prop-text">
        <h4>Intelligent Styling</h4>
        <p>Interactive AI recommendations &amp; Try-On</p>
      </div>
    </div>
  </div>
</section>

<!-- 3. Shop By Category -->
<section class="categories-section">
  <div class="container">
    <div class="section-header">
      <div>
        <span class="eyebrow">DISCOVER BY EDIT</span>
        <h2 class="section-title">Shop by Category</h2>
        <p class="section-subtitle">Curated wardrobes designed to integrate seamlessly into your everyday style.</p>
      </div>
      <a href="products.php" class="section-link">View Full Catalog &rarr;</a>
    </div>

    <div class="category-cards-grid">
      <!-- Category: Women -->
      <a href="products.php?category=Women" class="category-card">
        <img src="assets/images/cat_women.svg" alt="Women's Collection" onerror="this.src='assets/placeholder.svg'">
        <div class="category-card-overlay"></div>
        <div class="category-card-content">
          <p>Ready-to-Wear</p>
          <h3>Women</h3>
        </div>
      </a>

      <!-- Category: Men -->
      <a href="products.php?category=Men" class="category-card">
        <img src="assets/images/cat_men.svg" alt="Men's Collection" onerror="this.src='assets/placeholder.svg'">
        <div class="category-card-overlay"></div>
        <div class="category-card-content">
          <p>Tailored &amp; Casual</p>
          <h3>Men</h3>
        </div>
      </a>

      <!-- Category: Kids -->
      <a href="products.php?category=Kids" class="category-card">
        <img src="assets/images/cat_kids.svg" alt="Kids' Collection" onerror="this.src='assets/placeholder.svg'">
        <div class="category-card-overlay"></div>
        <div class="category-card-content">
          <p>Organic Essentials</p>
          <h3>Kids</h3>
        </div>
      </a>

      <!-- Category: Accessories -->
      <a href="products.php?category=Accessories" class="category-card">
        <img src="assets/images/cat_accessories.svg" alt="Accessories" onerror="this.src='assets/placeholder.svg'">
        <div class="category-card-overlay"></div>
        <div class="category-card-content">
          <p>Leather &amp; Jewels</p>
          <h3>Accessories</h3>
        </div>
      </a>
    </div>
  </div>
</section>

<!-- 4. Featured Curations -->
<?php if (!empty($featuredProducts)): ?>
<section class="catalog-page" style="padding-top: 10px;">
  <div class="container">
    <div class="section-header">
      <div>
        <span class="eyebrow">CURATOR'S PICKS</span>
        <h2 class="section-title">Featured Pieces</h2>
        <p class="section-subtitle">Our most cherished iconic designs, praised for their fit, drape, and timeless longevity.</p>
      </div>
      <a href="products.php" class="section-link">Shop All (<?= count($featuredProducts) ?>+) &rarr;</a>
    </div>

    <div class="product-grid">
      <?php foreach ($featuredProducts as $p): ?>
        <article class="product-card">
          <div class="product-thumb-wrap">
            <a href="product_details.php?id=<?= (int)$p['product_id'] ?>">
              <img src="<?= h($p['image'] ?: 'assets/placeholder.svg') ?>" alt="<?= h($p['name']) ?>" onerror="this.src='assets/placeholder.svg'">
            </a>
            <?php if (!empty($p['tag'])): ?>
              <span class="product-badge <?= strpos($p['tag'], 'Atelier') !== false ? 'gold' : '' ?>"><?= h($p['tag']) ?></span>
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
              <span class="product-stock-pill">In Stock</span>
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
  </div>
</section>
<?php endif; ?>

<!-- 5. Editorial Promotional Spotlight -->
<section class="editorial-spotlight">
  <div class="container">
    <div class="editorial-banner">
      <div class="editorial-text">
        <span class="eyebrow">AURA PHILOSOPHY</span>
        <h2 class="editorial-title">The Art of the<br>Capsule Wardrobe.</h2>
        <p class="editorial-desc">
          We reject fast-fashion waste in favor of enduring silhouettes cut from organic linen, virgin wool, and pure silk. Each garment is engineered to coordinate with every other piece in your wardrobe.
        </p>
        <a href="products.php" class="btn btn-gold">Explore The Wardrobe &rarr;</a>
      </div>
      <div class="editorial-image">
        <img src="assets/images/hero_fashion.svg" alt="AURA Editorial Capsule" onerror="this.src='assets/placeholder.svg'">
      </div>
    </div>
  </div>
</section>

<!-- 6. New Arrivals Section -->
<?php if (!empty($newArrivals)): ?>
<section class="catalog-page" style="padding-top: 10px;">
  <div class="container">
    <div class="section-header">
      <div>
        <span class="eyebrow">JUST ARRIVED</span>
        <h2 class="section-title">New Season Additions</h2>
        <p class="section-subtitle">Freshly curated garments cut for the current season, ready to wear.</p>
      </div>
      <a href="products.php?sort=newest" class="section-link">View All New &rarr;</a>
    </div>

    <div class="product-grid">
      <?php foreach ($newArrivals as $p): ?>
        <article class="product-card">
          <div class="product-thumb-wrap">
            <a href="product_details.php?id=<?= (int)$p['product_id'] ?>">
              <img src="<?= h($p['image'] ?: 'assets/placeholder.svg') ?>" alt="<?= h($p['name']) ?>" onerror="this.src='assets/placeholder.svg'">
            </a>
            <span class="product-badge">NEW IN</span>
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
              <span class="product-stock-pill">Available</span>
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
  </div>
</section>
<?php endif; ?>

<!-- 7. Interactive Demos Highlight Boxes (AI Stylist & Try-On) -->
<section class="features-split-section">
  <div class="container features-split-grid">
    
    <div class="feature-box">
      <div class="feature-box-content">
        <span class="eyebrow">INTELLIGENT CURATION</span>
        <h3>Personal AI Stylist</h3>
        <p>
          Unsure what to wear for an upcoming wedding, interview, or weekend getaway? Let our interactive algorithmic stylist curate a complete look matching your vibe and budget.
        </p>
      </div>
      <a href="stylist.php" class="btn btn-outline">Launch Stylist Desk &rarr;</a>
    </div>

    <div class="feature-box">
      <div class="feature-box-content">
        <span class="eyebrow">CLIENT STUDIO DEMO</span>
        <h3>Virtual Try-On Room</h3>
        <p>
          Upload your own portrait photo or choose from our studio mannequins to test colors, draping, and scale against our latest apparel catalog before ordering.
        </p>
      </div>
      <a href="tryon.php" class="btn btn-outline">Enter Fitting Room &rarr;</a>
    </div>

  </div>
</section>

<?php require "includes/footer.php"; ?>
