<?php
/**
 * AURA Fashion Atelier - Shopping Bag View
 */
require_once "config/db.php";
require_login();

$pageTitle = "Shopping Bag";
$userId = current_user_id();

$items = [];
$subtotal = 0.0;

if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare(
            "SELECT c.cart_id, c.quantity, c.size, p.product_id, p.name, p.price, p.image, p.category 
             FROM cart c 
             JOIN products p ON p.product_id = c.product_id 
             WHERE c.user_id = ? 
             ORDER BY c.created_at DESC"
        );
        $stmt->execute([$userId]);
        $items = $stmt->fetchAll();

        foreach ($items as $item) {
            $subtotal += ((float)$item['price'] * (int)$item['quantity']);
        }
    } catch (PDOException $e) {
        $items = [];
    }
}

// Shipping rule: Free if subtotal >= 1999, otherwise ₹149
$freeShippingThreshold = 1999.00;
$shippingCost = ($subtotal >= $freeShippingThreshold || $subtotal == 0) ? 0.00 : 149.00;
$amountRemainingForFreeShipping = max(0, $freeShippingThreshold - $subtotal);
$grandTotal = $subtotal + $shippingCost;

require "includes/header.php";
?>

<div class="cart-page">
  <div class="container">
    
    <div class="catalog-header">
      <span class="eyebrow">ORDER REVIEW</span>
      <h1 class="section-title">Your Shopping Bag (<?= count($items) ?>)</h1>
      <p class="section-subtitle">
        Review your chosen pieces before entering our secure demonstration checkout.
      </p>
    </div>

    <?php if (!empty($items)): ?>
      
      <!-- Free Shipping Milestone Progress -->
      <?php if ($amountRemainingForFreeShipping > 0): ?>
        <div class="free-shipping-progress">
          Add <strong><?= format_price($amountRemainingForFreeShipping) ?></strong> more to qualify for <strong>Complimentary Express Delivery</strong>!
        </div>
      <?php else: ?>
        <div class="free-shipping-progress" style="background: var(--color-success-bg); color: var(--color-success); border-color: #cde6d7;">
          ✓ You have qualified for <strong>Complimentary Express Delivery</strong>!
        </div>
      <?php endif; ?>

      <div class="cart-layout-grid">
        
        <!-- Left: Cart Items Table -->
        <div class="cart-items-panel">
          
          <div class="cart-header-row">
            <span>Product</span>
            <span>Price</span>
            <span>Quantity</span>
            <span>Subtotal</span>
            <span></span>
          </div>

          <?php foreach ($items as $item): 
            $lineTotal = (float)$item['price'] * (int)$item['quantity'];
          ?>
            <div class="cart-item-row">
              
              <!-- Product info -->
              <div class="cart-item-product">
                <a href="product_details.php?id=<?= (int)$item['product_id'] ?>">
                  <img src="<?= h($item['image'] ?: 'assets/placeholder.svg') ?>" 
                       alt="<?= h($item['name']) ?>" 
                       class="cart-item-thumb"
                       onerror="this.src='assets/placeholder.svg'">
                </a>
                <div class="cart-item-info">
                  <small><?= h($item['category']) ?> &bull; Size: <?= h($item['size'] ?? 'M') ?></small>
                  <h4>
                    <a href="product_details.php?id=<?= (int)$item['product_id'] ?>"><?= h($item['name']) ?></a>
                  </h4>
                </div>
              </div>

              <!-- Unit Price -->
              <div class="cart-item-price">
                <?= format_price($item['price']) ?>
              </div>

              <!-- Quantity Form -->
              <div>
                <form method="post" action="cart_action.php" style="display: inline-flex; align-items: center; gap: 6px;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="product_id" value="<?= (int)$item['product_id'] ?>">
                  <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" max="10" 
                         style="width: 50px; padding: 6px 8px; border: 1px solid var(--color-border); border-radius: var(--radius-xs); text-align: center; font-size: 13px;">
                  <button type="submit" class="btn btn-sm" style="padding: 6px 10px; background: var(--color-bg-paper); border: 1px solid var(--color-border);" title="Update quantity">
                    &#x21bb;
                  </button>
                </form>
              </div>

              <!-- Line Total -->
              <div class="cart-item-subtotal">
                <?= format_price($lineTotal) ?>
              </div>

              <!-- Remove Item Button -->
              <div>
                <form method="post" action="cart_action.php">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="remove">
                  <input type="hidden" name="product_id" value="<?= (int)$item['product_id'] ?>">
                  <button type="submit" class="cart-remove-btn" title="Remove item" aria-label="Remove item">
                    &times;
                  </button>
                </form>
              </div>

            </div>
          <?php endforeach; ?>

          <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px;">
            <a href="products.php" class="section-link">&larr; Continue Browsing</a>
            
            <form method="post" action="cart_action.php" onsubmit="return confirm('Are you sure you want to empty your shopping bag?');">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="clear">
              <button type="submit" style="color: var(--color-error); font-size: 12px; font-weight: 600;">
                Clear Entire Bag
              </button>
            </form>
          </div>

        </div>

        <!-- Right: Order Summary Box -->
        <div class="order-summary-box">
          <h3 class="summary-title">Summary</h3>
          
          <div class="summary-row">
            <span>Bag Subtotal</span>
            <span id="subtotalValue" data-subtotal="<?= $subtotal ?>" data-shipping="<?= $shippingCost ?>">
              <?= format_price($subtotal) ?>
            </span>
          </div>

          <div class="summary-row">
            <span>Estimated Delivery</span>
            <span>
              <?php if ($shippingCost == 0): ?>
                <span class="shipping-free-pill">Complimentary</span>
              <?php else: ?>
                <?= format_price($shippingCost) ?>
              <?php endif; ?>
            </span>
          </div>

          <!-- Demo Discount Row (Dynamic via JS) -->
          <div class="summary-row" id="discountRow" style="display: none; color: var(--color-success);">
            <span>Demonstration Promo (10%)</span>
            <span id="discountAmount">-₹0.00</span>
          </div>

          <!-- Promo Code Demo Form -->
          <div class="promo-box">
            <label for="promoCodeInput" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: var(--color-text-muted);">
              Promotional Code
            </label>
            <div class="promo-form">
              <input type="text" id="promoCodeInput" placeholder="Try code: AURA10">
              <button type="button" id="applyPromoBtn">Apply</button>
            </div>
            <div id="promoFeedback" style="font-size: 11px; margin-top: 5px;"></div>
          </div>

          <div class="summary-row total-row">
            <span>Grand Total</span>
            <span id="grandTotalDisplay"><?= format_price($grandTotal) ?></span>
          </div>

          <div style="margin-top: 26px;">
            <a href="checkout.php" class="btn btn-primary btn-block">
              Proceed to Checkout &rarr;
            </a>
          </div>

          <div class="demo-notice-mini" style="margin-top: 18px; text-align: center;">
            <small>Demonstration order &bull; No charges or credit card needed</small>
          </div>
        </div>

      </div>

    <?php else: ?>
      <div class="empty-state">
        <div class="empty-state-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto;">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <path d="M16 10a4 4 0 0 1-8 0"/>
          </svg>
        </div>
        <h3>Your shopping bag is empty</h3>
        <p>You haven't added any garments to your bag yet. Explore our curated edit and find pieces designed to last a lifetime.</p>
        <a href="products.php" class="btn btn-primary">Start Shopping &rarr;</a>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php require "includes/footer.php"; ?>
