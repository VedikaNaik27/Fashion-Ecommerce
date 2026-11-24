<?php
/**
 * AURA Fashion Atelier - Order Confirmation Receipt
 */
require_once "config/db.php";
require_login();

$orderId = (int)($_GET['order_id'] ?? 0);
$userId = current_user_id();

$order = null;
$orderItems = [];

if ($orderId > 0 && isset($pdo)) {
    try {
        // Fetch order belonging to current user
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
        $stmt->execute([$orderId, $userId]);
        $order = $stmt->fetch();

        if ($order) {
            // Fetch ordered items
            $itemStmt = $pdo->prepare(
                "SELECT oi.*, p.name, p.image, p.category 
                 FROM order_items oi 
                 JOIN products p ON p.product_id = oi.product_id 
                 WHERE oi.order_id = ?"
            );
            $itemStmt->execute([$orderId]);
            $orderItems = $itemStmt->fetchAll();
        }
    } catch (PDOException $e) {
        $order = null;
    }
}

if (!$order) {
    set_flash('error', 'Order not found or access denied.');
    header("Location: orders.php");
    exit;
}

$pageTitle = "Order #" . $order['order_id'] . " Confirmed";
require "includes/header.php";
?>

<div class="checkout-page">
  <div class="container">
    
    <div class="order-confirmation-card">
      <div class="confirmation-badge-icon">✓</div>
      <span class="eyebrow">ORDER CONFIRMATION</span>
      <h1 class="section-title">Thank You, <?= h($order['customer_name']) ?>!</h1>
      <p class="section-subtitle" style="margin: 0 auto 20px;">
        Your order <strong>#<?= (int)$order['order_id'] ?></strong> has been received and confirmed. A demonstration receipt has been generated below.
      </p>

      <div class="order-receipt-details">
        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--color-border); padding-bottom: 14px; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
          <div>
            <small style="color: var(--color-text-muted); display: block; font-size: 11px;">ORDER NUMBER</small>
            <strong style="font-size: 16px;">#<?= (int)$order['order_id'] ?></strong>
          </div>
          <div>
            <small style="color: var(--color-text-muted); display: block; font-size: 11px;">ORDER DATE</small>
            <span><?= date("M d, Y &bull; h:i A", strtotime($order['order_date'])) ?></span>
          </div>
          <div>
            <small style="color: var(--color-text-muted); display: block; font-size: 11px;">STATUS</small>
            <span class="order-status-badge confirmed"><?= h($order['order_status']) ?></span>
          </div>
          <div>
            <small style="color: var(--color-text-muted); display: block; font-size: 11px;">PAYMENT METHOD</small>
            <span><?= h($order['payment_method']) ?></span>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 13px; margin-bottom: 20px;">
          <div>
            <strong style="display: block; margin-bottom: 4px; color: var(--color-primary);">Delivery Destination:</strong>
            <p style="color: var(--color-text-muted); line-height: 1.5;">
              <?= h($order['customer_name']) ?><br>
              <?= nl2br(h($order['shipping_address'])) ?><br>
              <?= h($order['city'] ?? '') ?> - <?= h($order['postal_code'] ?? '') ?><br>
              Phone: <?= h($order['phone']) ?>
            </p>
          </div>
          <div>
            <strong style="display: block; margin-bottom: 4px; color: var(--color-primary);">Confirmation Contact:</strong>
            <p style="color: var(--color-text-muted); line-height: 1.5;">
              Email: <?= h($order['customer_email'] ?: ($_SESSION['email'] ?? '')) ?><br>
              Estimated Dispatch: Within 24-48 Hours<br>
              Tracking: Courier Simulation Active
            </p>
          </div>
        </div>

        <h4 style="font-family: var(--font-serif); font-size: 18px; margin-bottom: 12px; border-top: 1px solid var(--color-border); padding-top: 16px;">
          Purchased Garments (<?= count($orderItems) ?> items)
        </h4>

        <div style="display: flex; flex-direction: column; gap: 12px;">
          <?php foreach ($orderItems as $item): 
            $lineTotal = (float)$item['unit_price'] * (int)$item['quantity'];
          ?>
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 14px; font-size: 13px; padding-bottom: 10px; border-bottom: 1px dashed var(--color-border-subtle);">
              <img src="<?= h($item['image'] ?: 'assets/placeholder.svg') ?>" 
                   alt="<?= h($item['name']) ?>" 
                   style="width: 44px; height: 54px; object-fit: cover; border-radius: 2px;"
                   onerror="this.src='assets/placeholder.svg'">
              <div style="flex: 1;">
                <strong><?= h($item['name']) ?></strong>
                <small style="color: var(--color-text-muted); display: block; font-size: 11px;">
                  Qty: <?= (int)$item['quantity'] ?> &times; <?= format_price($item['unit_price']) ?>
                </small>
              </div>
              <span style="font-weight: 600;">
                <?= format_price($lineTotal) ?>
              </span>
            </div>
          <?php endforeach; ?>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; font-size: 16px; font-weight: 700; border-top: 1px solid var(--color-border); padding-top: 14px;">
          <span>Total Recorded</span>
          <span><?= format_price($order['total_amount']) ?></span>
        </div>
      </div>

      <div class="demo-disclaimer-box" style="text-align: left;">
        <strong>Academic Demonstration Record:</strong>
        This order has been recorded into the local MySQL database table <code>orders</code> and <code>order_items</code>. You can review all orders in the <strong>My Orders</strong> portal.
      </div>

      <div style="display: flex; gap: 14px; justify-content: center; margin-top: 30px; flex-wrap: wrap;">
        <a href="orders.php" class="btn btn-primary">View Order History</a>
        <a href="products.php" class="btn btn-outline">Continue Shopping &rarr;</a>
      </div>

    </div>

  </div>
</div>

<?php require "includes/footer.php"; ?>
