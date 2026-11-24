<?php
/**
 * AURA Fashion E-Commerce - Helper Functions
 * Reusable utility functions for security, sessions, formatting, and database queries.
 */

/**
 * Escape output for safe HTML display (XSS prevention)
 */
function h($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency in Indian Rupees
 */
function format_price($amount) {
    return '₹' . number_format((float)$amount, 2);
}

/**
 * Check if a user is currently logged in
 */
function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

/**
 * Get the current logged-in user ID
 */
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current logged-in username
 */
function current_username() {
    return $_SESSION['username'] ?? 'Guest';
}

/**
 * Require login for protected pages
 */
function require_login($redirect_uri = null) {
    if (!is_logged_in()) {
        $target = $redirect_uri ?: ($_SERVER['REQUEST_URI'] ?? 'index.php');
        set_flash('error', 'Please log in to your AURA account to continue.');
        header("Location: login.php?next=" . urlencode($target));
        exit;
    }
}

/**
 * Flash notification messaging system
 */
function set_flash($type, $message) {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][] = [
        'type'    => in_array($type, ['success', 'error', 'info', 'warning']) ? $type : 'info',
        'message' => $message
    ];
}

function get_flashes() {
    $flashes = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $flashes;
}

/**
 * CSRF Protection Helpers
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

function verify_csrf() {
    $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    if (empty($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        set_flash('error', 'Security token expired or invalid. Please try submitting again.');
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        header("Location: " . $referer);
        exit;
    }
}

/**
 * Cart and Wishlist Count Helpers
 */
function get_cart_count($pdo, $user_id = null) {
    if (!$user_id) {
        $user_id = current_user_id();
    }
    if (!$user_id || !$pdo) {
        return 0;
    }
    try {
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

function get_wishlist_count($pdo, $user_id = null) {
    if (!$user_id) {
        $user_id = current_user_id();
    }
    if (!$user_id || !$pdo) {
        return 0;
    }
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

/**
 * Truncate long text cleanly
 */
function truncate_text($text, $limit = 90) {
    $text = trim((string)$text);
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    return mb_substr($text, 0, $limit) . '...';
}

/**
 * Star Rating Display HTML Helper
 */
function render_rating_stars($rating = 4.5) {
    $rating = (float)$rating;
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;
    
    $html = '<span class="star-rating" title="' . number_format($rating, 1) . ' out of 5">';
    for ($i = 0; $i < $full; $i++) {
        $html .= '<span class="star full">★</span>';
    }
    if ($half) {
        $html .= '<span class="star half">★</span>';
    }
    for ($i = 0; $i < $empty; $i++) {
        $html .= '<span class="star empty">☆</span>';
    }
    $html .= '<span class="rating-num">(' . number_format($rating, 1) . ')</span></span>';
    return $html;
}
