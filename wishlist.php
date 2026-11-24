<?php
/**
 * AURA Fashion Atelier - Wishlist Page
 */
require_once "config/db.php";
require_login();

$pageTitle = "My Saved Wishlist";

$items = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare(
            "SELECT w.wishlist_id, w.created_at AS saved_at, p.* 
             FROM wishlist w 
             JOIN products p ON p.product_id = w.product_id 
             WHERE w.user_id = ? 
             ORDER BY w.created_at DESC"
        );
        $stmt->execute([current_user_id()]);
        $items = $stmt->fetchAll();
    } catch (PDOException $e) {
        $items = [];
    }
}

require "includes/header.php";
?>

<div class="catalog-page">
  <div class="container">
    
    <div class="catalog-header">
      <span class="eyebrow">SAVED ATELIER EDITS</span>
      <h1 class="section-title">My Wishlist (<?= count($items) ?>)</h1>
      <p class="section-subtitle">
        Your curated selection of favorite silhouettes. Keep track of items for upcoming seasons or move them directly into your shopping bag.
      </p>
    </div>

    <?php if (!empty($items)): ?>
      <div class="product-grid">
        <?php foreach ($items as $p): ?>
          <article class="product-card">
            <div class="product-thumb-wrap">
              <a href="product_details.php?id=<?= (int)$p['product_id'] ?>">
                <img src="<?= h($p['image'] ?: 'assets/placeholder.svg') ?>" 
                     alt="<?= h($p['name']) ?>" 
                     onerror="this.src='assets/placeholder.svg'">
              </a>
              <?php if (!empty($p['tag'])): ?>
                <span class="product-badge gold"><?= h($p['tag']) ?></span>
              <?php endif; ?>
            </div>
            
            <div class="product-info">
              <div class="product-meta">
                <span class="product-category"><?= h($p['category']) ?></span>
                <?= render_rating_stars($p['rating'] ?? 4.5) ?>
              </div>
              
              <h3 class="product-name">
                <a href="product_details.php?id=<?= (int)$p['product_id'] ?>"><?= h($p['name']) ?></a>
              </h3>
              
              <div class="product-price-row">
                <span class="product-price"><?= format_price($p['price']) ?></span>
                <span class="product-stock-pill">Available</span>
              </div>

              <!-- Wishlist Actions: Move to Bag or Remove -->
              <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 14px;">
                <form method="post" action="wishlist_action.php">
                  <?= csrf_field() ?>
                  <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">
                  <button type="submit" name="action" value="move_to_cart" class="btn btn-primary btn-block btn-sm">
                    Move to Shopping Bag
                  </button>
                </form>

                <form method="post" action="wishlist_action.php">
                  <?= csrf_field() ?>
                  <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">
                  <button type="submit" name="action" value="remove" class="btn btn-outline btn-block btn-sm" style="color: var(--color-error); border-color: var(--color-border);">
                    Remove from Wishlist
                  </button>
                </form>
              </div>

            </div>
          </article>
        <?php endforeach; ?>
      </div>

    <?php else: ?>
      <div class="empty-state">
        <div class="empty-state-icon">♡</div>
        <h3>Your wishlist is currently empty</h3>
        <p>You have not saved any garments to your personal wishlist yet. Explore our seasonal collections and tap the heart icon to save looks.</p>
        <a href="products.php" class="btn btn-primary">Discover the Collection &rarr;</a>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php require "includes/footer.php"; ?>
