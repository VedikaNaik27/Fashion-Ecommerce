<?php
/**
 * AURA Fashion Atelier - Wishlist Action Handler
 */
require_once "config/db.php";

if (!is_logged_in()) {
    set_flash('info', 'Please sign in to save garments to your wishlist.');
    $ref = $_SERVER['HTTP_REFERER'] ?? 'products.php';
    header("Location: login.php?next=" . urlencode($ref));
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $productId = (int)($_POST["product_id"] ?? 0);
    $action    = trim($_POST["action"] ?? "toggle");
    $userId    = (int)$_SESSION["user_id"];

    if ($productId > 0 && isset($pdo)) {
        try {
            if ($action === "toggle") {
                $check = $pdo->prepare("SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?");
                $check->execute([$userId, $productId]);
                if ($check->fetch()) {
                    $del = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
                    $del->execute([$userId, $productId]);
                    set_flash('info', 'Removed garment from your wishlist.');
                } else {
                    $ins = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
                    $ins->execute([$userId, $productId]);
                    set_flash('success', 'Garment saved to your wishlist.');
                }
            } elseif ($action === "remove") {
                $del = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
                $del->execute([$userId, $productId]);
                set_flash('info', 'Garment removed from your wishlist.');
            } elseif ($action === "move_to_cart") {
                // Add to cart with quantity 1
                $cartStmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE quantity = quantity + 1");
                $cartStmt->execute([$userId, $productId]);

                // Remove from wishlist
                $del = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
                $del->execute([$userId, $productId]);

                set_flash('success', 'Moved garment to your shopping bag.');
            }
        } catch (PDOException $e) {
            set_flash('error', 'Could not update wishlist. Please try again.');
        }
    }
}

$redirect = $_SERVER['HTTP_REFERER'] ?? 'wishlist.php';
header("Location: " . $redirect);
exit;
