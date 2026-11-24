<?php
/**
 * AURA Fashion Atelier - Client Logout
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = [];

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

// Start fresh session to display goodbye flash notice
session_start();
$_SESSION['flash_messages'][] = [
    'type' => 'info',
    'message' => 'You have been safely signed out of your AURA account.'
];

header("Location: login.php");
exit;
