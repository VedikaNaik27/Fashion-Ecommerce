<?php
/**
 * AURA Fashion Atelier - Shopping Bag Action Handler
 */
require_once "config/db.php";

if (!is_logged_in()) {
    set_flash('info', 'Please sign in to add garments to your shopping bag.');
    $ref = $_SERVER['HTTP_REFERER'] ?? 'products.php';
    header("Location: login.php?next=" . urlencode($ref));
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $productId = (int)($_POST["product_id"] ?? 0);
    $action    = trim($_POST["action"] ?? "add");
    $size      = trim($_POST["size"] ?? "M");
    $quantity  = max(1, min(10, (int)($_POST["quantity"] ?? 1)));
    $userId    = (int)$_SESSION["user_id"];

    if (isset($pdo)) {
        try {
            if ($action === "add" && $productId > 0) {
                // Verify product exists and get name
                $pStmt = $pdo->prepare("SELECT name FROM products WHERE product_id = ?");
                $pStmt->execute([$productId]);
                $p = $pStmt->fetch();

                if ($p) {
                    $stmt = $pdo->prepare(
                        "INSERT INTO cart (user_id, product_id, quantity, size) 
                         VALUES (?, ?, ?, ?) 
                         ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), size = VALUES(size)"
                    );
                    $stmt->execute([$userId, $productId, $quantity, $size]);
                    set_flash('success', "Added {$p['name']} to your shopping bag.");
                } else {
                    set_flash('error', 'Selected garment does not exist.');
                }
            } elseif ($action === "update" && $productId > 0) {
                $newQty = max(1, min(10, (int)($_POST["quantity"] ?? 1)));
                $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$newQty, $userId, $productId]);
                set_flash('info', 'Bag quantity updated.');
            } elseif ($action === "remove" && $productId > 0) {
                $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$userId, $productId]);
                set_flash('info', 'Garment removed from shopping bag.');
            } elseif ($action === "clear") {
                $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
                $stmt->execute([$userId]);
                set_flash('info', 'Shopping bag cleared.');
            }
        } catch (PDOException $e) {
            set_flash('error', 'Could not update your bag. Please try again.');
        }
    }
}

$redirect = $_POST['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? 'cart.php';
// Prevent open redirects
if (!preg_match('/^[a-zA-Z0-9_\-\.\/]+\.php(\?.*)?$/', $redirect)) {
    $redirect = 'cart.php';
}
header("Location: " . $redirect);
exit;
