<?php
/**
 * AURA Fashion Atelier - AI Stylist Recommendation Studio (Interactive Demo)
 */
require_once "config/db.php";

$pageTitle = "AI Stylist Studio (Demo)";

// Inputs
$occasion   = trim($_GET['occasion'] ?? 'office');
$department = trim($_GET['department'] ?? 'Women');
$aesthetic  = trim($_GET['aesthetic'] ?? 'minimalist');
$palette    = trim($_GET['palette'] ?? 'neutral');

$recommendedProducts = [];
$stylistAdvice = "";
$capsuleTheme = "";

// Algorithmic Recommendation Engine matching against MySQL database
if (isset($pdo)) {
    try {
        $categoryFilter = "";
        $params = [];

        if ($department === 'Women') {
            $categoryFilter = " AND (category = 'Women' OR category = 'Accessories')";
        } elseif ($department === 'Men') {
            $categoryFilter = " AND (category = 'Men' OR category = 'Accessories')";
        } else {
            $categoryFilter = ""; // All
        }

        // Search based on occasion mood
        if ($occasion === 'office') {
            $capsuleTheme = "Executive Minimalist &bull; Polished Tailoring";
            $stylistAdvice = "For professional and office engagements, we focus on architectural silhouettes cut from structured wool and breathable linen. A sharp single-breasted blazer or crisp overshirt paired with fluid trousers establishes refined presence without stiffness.";
            $sql = "SELECT * FROM products WHERE (name LIKE '%Blazer%' OR name LIKE '%Trousers%' OR name LIKE '%Oxford%' OR name LIKE '%Shirt%' OR name LIKE '%Bag%') $categoryFilter ORDER BY rating DESC LIMIT 3";
        } elseif ($occasion === 'party') {
            $capsuleTheme = "Evening Soiree &bull; Liquid Silk &amp; Golden Accents";
            $stylistAdvice = "For celebratory evenings and cocktail occasions, texture creates drama. We contrast fluid mulberry silk with 18K gold-plated jewelry and polished handcrafted leather accessories to achieve understated luxury.";
            $sql = "SELECT * FROM products WHERE (name LIKE '%Silk%' OR name LIKE '%Dress%' OR name LIKE '%Hoops%' OR name LIKE '%Boots%' OR name LIKE '%Coat%') $categoryFilter ORDER BY rating DESC LIMIT 3";
        } elseif ($occasion === 'brunch') {
            $capsuleTheme = "Weekend Leisure &bull; Breezy Natural Flax";
            $stylistAdvice = "Weekend outings call for weightless ease. Pure European linen allows airflow while retaining crisp drape. Accompany with a neutral crossbody bag to keep hands free.";
            $sql = "SELECT * FROM products WHERE (name LIKE '%Linen%' OR name LIKE '%Tee%' OR name LIKE '%Set%' OR name LIKE '%Bag%' OR name LIKE '%Midi%') $categoryFilter ORDER BY rating DESC LIMIT 3";
        } elseif ($occasion === 'travel') {
            $capsuleTheme = "First-Class Transits &bull; Weatherproof Layers";
            $stylistAdvice = "Travel demands wrinkle-resistant layering and thermal adaptability. A cotton gabardine trench coat over soft organic knits offers versatile protection across destinations.";
            $sql = "SELECT * FROM products WHERE (name LIKE '%Trench%' OR name LIKE '%Cashmere%' OR name LIKE '%Scarf%' OR name LIKE '%Boots%') $categoryFilter ORDER BY rating DESC LIMIT 3";
        } else {
            // General curated fallback
            $capsuleTheme = "Essential Capsule &bull; Signature Classics";
            $stylistAdvice = "A balanced look begins with one structured statement piece anchored by clean monochrome staples. Every item selected coordinates naturally with neutral tones.";
            $sql = "SELECT * FROM products WHERE is_featured = 1 $categoryFilter ORDER BY rating DESC LIMIT 3";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $recommendedProducts = $stmt->fetchAll();

        // If specific search yielded fewer than 3 items, backfill with top-rated items
        if (count($recommendedProducts) < 3) {
            $existingIds = array_column($recommendedProducts, 'product_id');
            $placeholders = !empty($existingIds) ? 'AND product_id NOT IN (' . implode(',', array_map('intval', $existingIds)) . ')' : '';
            $backfill = $pdo->query("SELECT * FROM products WHERE 1=1 $categoryFilter $placeholders ORDER BY rating DESC LIMIT " . (3 - count($recommendedProducts)))->fetchAll();
            $recommendedProducts = array_merge($recommendedProducts, $backfill);
        }

    } catch (PDOException $e) {
        $recommendedProducts = [];
    }
}

// Calculate capsule total
$capsuleTotal = 0;
foreach ($recommendedProducts as $rp) {
    $capsuleTotal += (float)$rp['price'];
}

require "includes/header.php";
?>

<div class="stylist-page">
  <div class="container">
    
    <!-- Hero Banner with Transparent Demo Disclosure -->
    <div class="stylist-intro-card">
      <div>
        <span class="eyebrow">INTELLIGENT OUTFIT CURATION</span>
        <h1>The AURA Stylist Desk</h1>
        <p>
          Discover curated capsule looks tailored to your upcoming engagements, personal aesthetics, and seasonal palettes.
        </p>
      </div>
      
      <div class="demo-disclaimer-box" style="background: rgba(255,255,255,0.08); border-color: var(--color-gold); color: #e8dcd0; margin: 0;">
        <strong style="color: #ffffff;">Demonstration Disclosure:</strong><br>
        This page demonstrates a rule-based algorithmic styling studio powered by your local MySQL product catalog. It does not connect to external commercial AI APIs.
      </div>
    </div>

    <!-- Consultation Preferences Form -->
    <div class="stylist-controls-card">
      <span class="eyebrow">STEP 1 &bull; DEFINE YOUR BRIEF</span>
      <h3 style="font-family: var(--font-serif); font-size: 24px; margin-bottom: 20px;">What are you preparing for?</h3>
      
      <form method="get" action="stylist.php">
        <div class="stylist-form-grid">
          
          <div class="form-group">
            <label for="occasionSelect">Occasion / Event</label>
            <select name="occasion" id="occasionSelect" onchange="this.form.submit()">
              <option value="office" <?= $occasion === 'office' ? 'selected' : '' ?>>Office &bull; Boardroom &bull; Formal</option>
              <option value="party" <?= $occasion === 'party' ? 'selected' : '' ?>>Evening Gala &bull; Cocktail &bull; Soiree</option>
              <option value="brunch" <?= $occasion === 'brunch' ? 'selected' : '' ?>>Weekend Brunch &bull; Casual Day Out</option>
              <option value="travel" <?= $occasion === 'travel' ? 'selected' : '' ?>>Travel &bull; Vacation &bull; Airport</option>
            </select>
          </div>

          <div class="form-group">
            <label for="departmentSelect">Wardrobe Department</label>
            <select name="department" id="departmentSelect" onchange="this.form.submit()">
              <option value="Women" <?= $department === 'Women' ? 'selected' : '' ?>>Women's Atelier</option>
              <option value="Men" <?= $department === 'Men' ? 'selected' : '' ?>>Men's Tailoring</option>
              <option value="All" <?= $department === 'All' ? 'selected' : '' ?>>All Departments</option>
            </select>
          </div>

          <div class="form-group">
            <label for="aestheticSelect">Style Aesthetic</label>
            <select name="aesthetic" id="aestheticSelect" onchange="this.form.submit()">
              <option value="minimalist" <?= $aesthetic === 'minimalist' ? 'selected' : '' ?>>Quiet Luxury &bull; Minimalist</option>
              <option value="classic" <?= $aesthetic === 'classic' ? 'selected' : '' ?>>Modern Classic &bull; Structured</option>
              <option value="relaxed" <?= $aesthetic === 'relaxed' ? 'selected' : '' ?>>Effortless &bull; Relaxed Linen</option>
            </select>
          </div>

          <div class="form-group">
            <label for="paletteSelect">Color Harmony</label>
            <select name="palette" id="paletteSelect" onchange="this.form.submit()">
              <option value="neutral" <?= $palette === 'neutral' ? 'selected' : '' ?>>Warm Sand, Beige &amp; Ivory</option>
              <option value="monochrome" <?= $palette === 'monochrome' ? 'selected' : '' ?>>Onyx Black &amp; Charcoal</option>
              <option value="gold" <?= $palette === 'gold' ? 'selected' : '' ?>>Subtle Gold &amp; Olive Tones</option>
            </select>
          </div>

        </div>

        <button type="submit" class="btn btn-primary">
          Generate Curated Look &rarr;
        </button>
      </form>
    </div>

    <!-- Capsule Recommendation Results -->
    <?php if (!empty($recommendedProducts)): ?>
      <div class="capsule-results-wrapper">
        
        <div class="capsule-meta-box">
          <span class="eyebrow">CURATED CAPSULE LOOK</span>
          <h3><?= h($capsuleTheme) ?></h3>
          <p><?= h($stylistAdvice) ?></p>
        </div>

        <div class="section-header" style="margin-bottom: 24px;">
          <div>
            <h4 style="font-family: var(--font-serif); font-size: 22px;">Recommended Pieces (<?= count($recommendedProducts) ?> items)</h4>
            <small style="color: var(--color-text-muted);">Curated from the AURA active MySQL catalog</small>
          </div>
          <div style="font-size: 16px; font-weight: 700; color: var(--color-primary);">
            Full Capsule Total: <?= format_price($capsuleTotal) ?>
          </div>
        </div>

        <div class="product-grid" style="grid-template-columns: repeat(3, 1fr);">
          <?php foreach ($recommendedProducts as $idx => $p): ?>
            <article class="product-card">
              <div class="product-thumb-wrap">
                <a href="product_details.php?id=<?= (int)$p['product_id'] ?>">
                  <img src="<?= h($p['image'] ?: 'assets/placeholder.svg') ?>" 
                       alt="<?= h($p['name']) ?>" 
                       onerror="this.src='assets/placeholder.svg'">
                </a>
                <span class="product-badge gold">Piece #<?= $idx + 1 ?></span>
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
                </div>

                <div class="product-actions-bar">
                  <form method="post" action="cart_action.php" style="width: 100%;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">
                    <button type="submit" name="action" value="add" class="btn-add-bag" style="width: 100%;">
                      + Add Piece to Bag
                    </button>
                  </form>
                  <a href="tryon.php?product_id=<?= (int)$p['product_id'] ?>" class="btn-view-item" title="Test in Virtual Fitting Room">
                    Fitting
                  </a>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>

        <div style="margin-top: 30px; text-align: center; border-top: 1px solid var(--color-border); padding-top: 24px;">
          <p style="color: var(--color-text-muted); font-size: 13px; margin-bottom: 16px;">
            Want to see how these silhouettes drape before deciding?
          </p>
          <a href="tryon.php" class="btn btn-outline-gold">Open Virtual Try-On Studio &rarr;</a>
        </div>

      </div>
    <?php endif; ?>

  </div>
</div>

<?php require "includes/footer.php"; ?>
