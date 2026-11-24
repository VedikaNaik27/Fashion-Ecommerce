<?php
/**
 * AURA Fashion Atelier - Product Details Page
 */
require_once "config/db.php";

$productId = (int)($_GET['id'] ?? 0);

if ($productId <= 0) {
    set_flash('error', 'Invalid product selected.');
    header("Location: products.php");
    exit;
}

$product = null;
$relatedProducts = [];

if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            set_flash('error', 'Requested product could not be found.');
            header("Location: products.php");
            exit;
        }

        // Fetch up to 4 related products from the same category
        $stmtRel = $pdo->prepare("SELECT * FROM products WHERE category = ? AND product_id != ? LIMIT 4");
        $stmtRel->execute([$product['category'], $productId]);
        $relatedProducts = $stmtRel->fetchAll();
    } catch (PDOException $e) {
        set_flash('error', 'Error loading product details.');
        header("Location: products.php");
        exit;
    }
}

$pageTitle = $product['name'] ?? 'Product Details';
require "includes/header.php";
?>

<div class="product-details-page">
  <div class="container">
    
    <!-- Breadcrumb trail -->
    <nav class="breadcrumbs" aria-label="Breadcrumb">
      <a href="index.php">Home</a>
      <span>&rsaquo;</span>
      <a href="products.php">Collection</a>
      <span>&rsaquo;</span>
      <a href="products.php?category=<?= urlencode($product['category']) ?>"><?= h($product['category']) ?></a>
      <span>&rsaquo;</span>
      <span><?= h($product['name']) ?></span>
    </nav>

    <!-- 2-Column Product Detail Layout -->
    <div class="product-details-grid">
      
      <!-- Gallery Column (Left) -->
      <div class="product-gallery">
        <div class="main-image-container">
          <img src="<?= h($product['image'] ?: 'assets/placeholder.svg') ?>" 
               alt="<?= h($product['name']) ?>" 
               onerror="this.src='assets/placeholder.svg'">
        </div>
      </div>

      <!-- Purchase & Info Column (Right) -->
      <div class="product-buy-panel">
        <span class="eyebrow"><?= h($product['category']) ?> &bull; <?= h($product['tag'] ?? 'ATELIER') ?></span>
        <h1 class="product-title-large"><?= h($product['name']) ?></h1>
        
        <div style="margin-bottom: 14px;">
          <?= render_rating_stars($product['rating'] ?? 4.5) ?>
        </div>

        <div class="product-price-large">
          <?= format_price($product['price']) ?>
        </div>

        <p class="product-description-full">
          <?= nl2br(h($product['description'])) ?>
        </p>

        <!-- Purchase Action Form -->
        <form method="post" action="cart_action.php" id="productAddForm">
          <?= csrf_field() ?>
          <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
          <input type="hidden" name="action" value="add">

          <!-- Size Selector -->
          <div class="option-group">
            <span class="option-label">Select Size</span>
            <div class="size-selector">
              <label>
                <input type="radio" name="size" value="XS" style="display:none;">
                <span class="size-pill" onclick="document.querySelectorAll('.size-pill').forEach(p=>p.classList.remove('active')); this.classList.add('active');">XS</span>
              </label>
              <label>
                <input type="radio" name="size" value="S" style="display:none;">
                <span class="size-pill" onclick="document.querySelectorAll('.size-pill').forEach(p=>p.classList.remove('active')); this.classList.add('active');">S</span>
              </label>
              <label>
                <input type="radio" name="size" value="M" checked style="display:none;">
                <span class="size-pill active" onclick="document.querySelectorAll('.size-pill').forEach(p=>p.classList.remove('active')); this.classList.add('active');">M</span>
              </label>
              <label>
                <input type="radio" name="size" value="L" style="display:none;">
                <span class="size-pill" onclick="document.querySelectorAll('.size-pill').forEach(p=>p.classList.remove('active')); this.classList.add('active');">L</span>
              </label>
              <label>
                <input type="radio" name="size" value="XL" style="display:none;">
                <span class="size-pill" onclick="document.querySelectorAll('.size-pill').forEach(p=>p.classList.remove('active')); this.classList.add('active');">XL</span>
              </label>
            </div>
          </div>

          <!-- Quantity Stepper -->
          <div class="option-group">
            <span class="option-label">Quantity</span>
            <div class="quantity-stepper">
              <button type="button" class="btn-qty-minus">&minus;</button>
              <input type="number" name="quantity" value="1" min="1" max="10" readonly>
              <button type="button" class="btn-qty-plus">&plus;</button>
            </div>
          </div>

          <!-- CTA Buttons -->
          <div class="cta-row-details">
            <button type="submit" class="btn btn-primary" style="flex: 2;">
              Add to Shopping Bag &rarr;
            </button>
            
            <a href="tryon.php?product_id=<?= (int)$product['product_id'] ?>" class="btn btn-outline-gold" style="flex: 1;" title="Virtual Fitting Room">
              Try In Studio
            </a>
          </div>
        </form>

        <!-- Wishlist Form -->
        <form method="post" action="wishlist_action.php" style="margin-bottom: 24px;">
          <?= csrf_field() ?>
          <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
          <button type="submit" name="action" value="toggle" class="btn btn-outline btn-block">
            ♡ Save to My Wishlist
          </button>
        </form>

        <!-- Specifications & Care Accordion -->
        <div class="accordion-specs">
          <div class="spec-item">
            <span>🌿</span>
            <span><strong>Ethical Materials:</strong> Sourced sustainably from certified organic mills.</span>
          </div>
          <div class="spec-item">
            <span>🚚</span>
            <span><strong>Complimentary Delivery:</strong> Ships within 24-48 hours. Free shipping over ₹1,999.</span>
          </div>
          <div class="spec-item">
            <span>↺</span>
            <span><strong>7-Day Returns:</strong> Hassle-free exchanges or store credit returns.</span>
          </div>
          <div class="spec-item">
            <span>✦</span>
            <span><strong>Authentic Atelier:</strong> Hand-finished seams and pre-washed natural fibers.</span>
          </div>
        </div>

      </div>

    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
      <div style="border-top: 1px solid var(--color-border); padding-top: 50px; margin-top: 40px;">
        <div class="section-header">
          <div>
            <span class="eyebrow">COMPLETE THE LOOK</span>
            <h2 class="section-title">You May Also Admire</h2>
          </div>
          <a href="products.php?category=<?= urlencode($product['category']) ?>" class="section-link">More in <?= h($product['category']) ?> &rarr;</a>
        </div>

        <div class="product-grid">
          <?php foreach ($relatedProducts as $p): ?>
            <article class="product-card">
              <div class="product-thumb-wrap">
                <a href="product_details.php?id=<?= (int)$p['product_id'] ?>">
                  <img src="<?= h($p['image'] ?: 'assets/placeholder.svg') ?>" alt="<?= h($p['name']) ?>" onerror="this.src='assets/placeholder.svg'">
                </a>
                <form method="post" action="wishlist_action.php" class="card-wishlist-form">
                  <?= csrf_field() ?>
                  <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">
                  <button type="submit" name="action" value="toggle" class="wishlist-btn-round" title="Save to Wishlist" aria-label="Save to Wishlist">
                    ♡
                  </button>
                </form>
              </div>
              <div class="product-info">
                <span class="product-category"><?= h($p['category']) ?></span>
                <h3 class="product-name">
                  <a href="product_details.php?id=<?= (int)$p['product_id'] ?>"><?= h($p['name']) ?></a>
                </h3>
                <div class="product-price-row">
                  <span class="product-price"><?= format_price($p['price']) ?></span>
                </div>
                <div class="product-actions-bar">
                  <form method="post" action="cart_action.php" style="width: 100%;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">
                    <button type="submit" name="action" value="add" class="btn-add-bag" style="width: 100%;">
                      + Add to Bag
                    </button>
                  </form>
                  <a href="product_details.php?id=<?= (int)$p['product_id'] ?>" class="btn-view-item">&rarr;</a>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php require "includes/footer.php"; ?>
