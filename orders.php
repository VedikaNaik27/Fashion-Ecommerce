<?php
/**
 * AURA Fashion Atelier - My Order History
 */
require_once "config/db.php";
require_login();

$pageTitle = "My Order History";
$userId = current_user_id();

$orders = [];

if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll();

        // Fetch items for each order
        $itemStmt = $pdo->prepare(
            "SELECT oi.*, p.name, p.image, p.category 
             FROM order_items oi 
             JOIN products p ON p.product_id = oi.product_id 
             WHERE oi.order_id = ?"
        );

        foreach ($orders as &$o) {
            $itemStmt->execute([$o['order_id']]);
            $o['items'] = $itemStmt->fetchAll();
        }
        unset($o);
    } catch (PDOException $e) {
        $orders = [];
    }
}

require "includes/header.php";
?>

<div class="catalog-page">
  <div class="container">
    
    <div class="catalog-header">
      <span class="eyebrow">CLIENT ACCOUNT</span>
      <h1 class="section-title">Order History (<?= count($orders) ?>)</h1>
      <p class="section-subtitle">
        Review your past demonstration orders, delivery destinations, and item breakdowns.
      </p>
    </div>

    <?php if (!empty($orders)): ?>
      <div class="order-history-list">
        <?php foreach ($orders as $order): ?>
          <div class="order-history-card">
            
            <div class="order-card-header">
              <div>
                <span class="eyebrow" style="margin-bottom: 2px;">ORDER REFERENCE</span>
                <h3 style="font-family: var(--font-serif); font-size: 22px; color: var(--color-primary);">
                  #<?= (int)$order['order_id'] ?>
                </h3>
                <small style="color: var(--color-text-muted);">
                  Placed on <?= date("F j, Y &bull; g:i A", strtotime($order['order_date'])) ?>
                </small>
              </div>

              <div style="text-align: right;">
                <span class="order-status-badge <?= strtolower($order['order_status']) === 'delivered' ? 'delivered' : 'confirmed' ?>">
                  <?= h($order['order_status'] ?? 'Confirmed') ?>
                </span>
                <div style="font-size: 18px; font-weight: 700; color: var(--color-primary); margin-top: 6px;">
                  <?= format_price($order['total_amount']) ?>
                </div>
              </div>
            </div>

            <!-- Itemized Products in this Order -->
            <div style="margin: 20px 0;">
              <h5 style="font-size: 12px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--color-gold-dark); margin-bottom: 12px;">
                Ordered Silhouettes
              </h5>

              <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px;">
                <?php if (!empty($order['items'])): ?>
                  <?php foreach ($order['items'] as $item): ?>
                    <div style="display: flex; gap: 12px; align-items: center; background: var(--color-bg-paper); padding: 10px 14px; border-radius: var(--radius-xs); border: 1px solid var(--color-border-subtle);">
                      <img src="<?= h($item['image'] ?: 'assets/placeholder.svg') ?>" 
                           alt="<?= h($item['name']) ?>" 
                           style="width: 45px; height: 55px; object-fit: cover; border-radius: 2px;"
                           onerror="this.src='assets/placeholder.svg'">
                      <div style="flex: 1;">
                        <h6 style="font-size: 14px; font-weight: 600; color: var(--color-primary); margin-bottom: 2px;">
                          <?= h($item['name']) ?>
                        </h6>
                        <small style="color: var(--color-text-muted); font-size: 11px;">
                          Qty: <?= (int)$item['quantity'] ?> &bull; Unit: <?= format_price($item['unit_price']) ?>
                        </small>
                      </div>
                      <span style="font-weight: 600; font-size: 13px;">
                        <?= format_price((float)$item['unit_price'] * (int)$item['quantity']) ?>
                      </span>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <p style="font-size: 13px; color: var(--color-text-muted);">Standard Atelier Capsule Order</p>
                <?php endif; ?>
              </div>
            </div>

            <!-- Shipping & Payment Footer Info -->
            <div style="display: flex; justify-content: space-between; align-items: flex-end; border-top: 1px solid var(--color-border-subtle); padding-top: 14px; font-size: 12px; color: var(--color-text-muted); flex-wrap: wrap; gap: 10px;">
              <div>
                <strong>Delivering to:</strong> <?= h($order['customer_name']) ?> &bull; <?= h($order['shipping_address']) ?>, <?= h($order['city'] ?? '') ?>
              </div>
              <div>
                <strong>Payment:</strong> <?= h($order['payment_method']) ?>
              </div>
            </div>

          </div>
        <?php endforeach; ?>
      </div>

    <?php else: ?>
      <div class="empty-state">
        <div class="empty-state-icon">✦</div>
        <h3>No past orders found</h3>
        <p>You have not placed any orders under your AURA account yet. Explore our latest arrivals to experience our tailoring.</p>
        <a href="products.php" class="btn btn-primary">Start Exploring &rarr;</a>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php require "includes/footer.php"; ?>
