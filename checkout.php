<?php
/**
 * AURA Fashion Atelier - Checkout & Order Processing
 */
require_once "config/db.php";
require_login();

$userId = current_user_id();

// Fetch Cart Items
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

// If cart is empty, redirect to cart page
if (empty($items)) {
    set_flash('info', 'Your shopping bag is empty. Please select garments before checkout.');
    header("Location: cart.php");
    exit;
}

$shippingCost = ($subtotal >= 1999.00) ? 0.00 : 149.00;
$grandTotal = $subtotal + $shippingCost;

$error = "";

// Handle Checkout Form Submission BEFORE any HTML output
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $name    = trim($_POST["name"] ?? "");
    $email   = trim($_POST["email"] ?? "");
    $phone   = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $city    = trim($_POST["city"] ?? "");
    $postal  = trim($_POST["postal_code"] ?? "");
    $payment = trim($_POST["payment_method"] ?? "Cash on Delivery (Demo)");

    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($postal)) {
        $error = "Please fill in all required shipping and contact details.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please provide a valid email address for your order confirmation receipt.";
    } elseif (strlen($phone) < 7) {
        $error = "Please provide a valid contact telephone number.";
    } else {
        if (isset($pdo)) {
            try {
                $pdo->beginTransaction();

                // 1. Insert Order
                $orderStmt = $pdo->prepare(
                    "INSERT INTO orders (user_id, customer_name, customer_email, phone, shipping_address, city, postal_code, payment_method, order_status, total_amount) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Confirmed', ?)"
                );
                $orderStmt->execute([
                    $userId,
                    $name,
                    $email,
                    $phone,
                    $address,
                    $city,
                    $postal,
                    $payment,
                    $grandTotal
                ]);
                $orderId = (int)$pdo->lastInsertId();

                // 2. Insert Order Items
                $itemStmt = $pdo->prepare(
                    "INSERT INTO order_items (order_id, product_id, quantity, unit_price) 
                     VALUES (?, ?, ?, ?)"
                );
                foreach ($items as $item) {
                    $itemStmt->execute([
                        $orderId,
                        (int)$item['product_id'],
                        (int)$item['quantity'],
                        (float)$item['price']
                    ]);
                }

                // 3. Clear Cart
                $clearStmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
                $clearStmt->execute([$userId]);

                $pdo->commit();

                set_flash('success', "Order #{$orderId} placed successfully!");
                header("Location: order_confirmation.php?order_id=" . $orderId);
                exit;

            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error = "An error occurred while placing your order. Please try again. (" . h($e->getMessage()) . ")";
            }
        }
    }
}

// Prefill values from session or demo user
$defaultName = $_POST['name'] ?? current_username();
$defaultEmail = $_POST['email'] ?? ($_SESSION['email'] ?? '');
$defaultPhone = $_POST['phone'] ?? '+91 98765 43210';
$defaultAddress = $_POST['address'] ?? '402 Royale Crest, MG Road';
$defaultCity = $_POST['city'] ?? 'Bangalore';
$defaultPostal = $_POST['postal_code'] ?? '560001';

$pageTitle = "Checkout";
require "includes/header.php";
?>

<div class="checkout-page">
  <div class="container">
    
    <div class="catalog-header">
      <span class="eyebrow">FINAL STEP</span>
      <h1 class="section-title">Shipping &amp; Checkout</h1>
      <p class="section-subtitle">
        Enter your shipping destination to complete your demonstration order.
      </p>
    </div>

    <?php if ($error): ?>
      <div class="toast-alert toast-error" style="margin-bottom: 24px;">
        <span class="toast-indicator"></span>
        <span class="toast-message"><?= h($error) ?></span>
      </div>
    <?php endif; ?>

    <div class="checkout-grid">
      
      <!-- Left: Delivery & Payment Details Form -->
      <div class="checkout-card">
        <h3 class="summary-title" style="margin-bottom: 24px;">1. Shipping Information</h3>
        
        <form method="post" action="checkout.php" id="checkoutForm">
          <?= csrf_field() ?>

          <div class="form-group-grid">
            <div class="form-group full">
              <label for="fullNameInput">Full Name *</label>
              <input type="text" id="fullNameInput" name="name" value="<?= h($defaultName) ?>" required>
            </div>

            <div class="form-group">
              <label for="emailInput">Email Address (for receipt) *</label>
              <input type="email" id="emailInput" name="email" value="<?= h($defaultEmail) ?>" required>
            </div>

            <div class="form-group">
              <label for="phoneInput">Contact Phone Number *</label>
              <input type="tel" id="phoneInput" name="phone" value="<?= h($defaultPhone) ?>" required>
            </div>

            <div class="form-group full">
              <label for="addressInput">Street Address &bull; Apartment/Suite *</label>
              <textarea id="addressInput" name="address" rows="3" required><?= h($defaultAddress) ?></textarea>
            </div>

            <div class="form-group">
              <label for="cityInput">City *</label>
              <input type="text" id="cityInput" name="city" value="<?= h($defaultCity) ?>" required>
            </div>

            <div class="form-group">
              <label for="postalInput">Postal / PIN Code *</label>
              <input type="text" id="postalInput" name="postal_code" value="<?= h($defaultPostal) ?>" required>
            </div>
          </div>

          <h3 class="summary-title" style="margin-top: 32px; margin-bottom: 18px;">2. Payment Method (Demo Simulation)</h3>
          
          <div class="payment-methods-grid">
            <label class="payment-radio-card">
              <input type="radio" name="payment_method" value="Cash on Delivery (Demo)" checked>
              <span class="payment-radio-label">
                <span><strong>Cash on Delivery (Simulation)</strong></span>
                <span class="badge-subtle">Recommended</span>
              </span>
            </label>

            <label class="payment-radio-card">
              <input type="radio" name="payment_method" value="UPI / QR Code (Demo)">
              <span class="payment-radio-label">
                <span><strong>UPI / QR Code (Simulation)</strong></span>
                <span class="badge-subtle">Demo</span>
              </span>
            </label>

            <label class="payment-radio-card">
              <input type="radio" name="payment_method" value="Credit/Debit Card (Demo)">
              <span class="payment-radio-label">
                <span><strong>Credit or Debit Card (Simulation)</strong></span>
                <span class="badge-subtle">Demo</span>
              </span>
            </label>
          </div>

          <!-- Academic Demo Disclaimer Box -->
          <div class="demo-disclaimer-box">
            <strong>Demonstration Notice:</strong>
            This is an academic mini-project demonstration for college evaluation. Checkout records the order in MySQL and generates a receipt, but <strong>no real payment gateway or financial transaction is processed</strong>.
          </div>

          <button type="submit" class="btn btn-primary btn-block" style="padding: 16px; font-size: 13px;">
            Place Demonstration Order &bull; <?= format_price($grandTotal) ?> &rarr;
          </button>
        </form>

      </div>

      <!-- Right: Itemized Order Summary Review -->
      <div class="order-summary-box">
        <h3 class="summary-title">Order Review (<?= count($items) ?> items)</h3>
        
        <div class="checkout-items-list">
          <?php foreach ($items as $item): 
            $lineTotal = (float)$item['price'] * (int)$item['quantity'];
          ?>
            <div class="checkout-item-compact">
              <img src="<?= h($item['image'] ?: 'assets/placeholder.svg') ?>" 
                   alt="<?= h($item['name']) ?>"
                   onerror="this.src='assets/placeholder.svg'">
              <div style="flex: 1;">
                <h5 style="font-family: var(--font-serif); font-size: 15px; color: var(--color-primary);"><?= h($item['name']) ?></h5>
                <small style="color: var(--color-text-muted); font-size: 11px;">
                  Size: <?= h($item['size'] ?? 'M') ?> &bull; Qty: <?= (int)$item['quantity'] ?>
                </small>
              </div>
              <span style="font-weight: 600; font-size: 13px;">
                <?= format_price($lineTotal) ?>
              </span>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="summary-row">
          <span>Bag Subtotal</span>
          <span><?= format_price($subtotal) ?></span>
        </div>

        <div class="summary-row">
          <span>Delivery</span>
          <span>
            <?php if ($shippingCost == 0): ?>
              <span class="shipping-free-pill">Complimentary</span>
            <?php else: ?>
              <?= format_price($shippingCost) ?>
            <?php endif; ?>
          </span>
        </div>

        <div class="summary-row total-row">
          <span>Total Payable</span>
          <span><?= format_price($grandTotal) ?></span>
        </div>

        <div style="margin-top: 20px; font-size: 12px; color: var(--color-text-muted); text-align: center;">
          <a href="cart.php" style="text-decoration: underline;">&larr; Return to Shopping Bag to edit items</a>
        </div>
      </div>

    </div>

  </div>
</div>

<?php require "includes/footer.php"; ?>
