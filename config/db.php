
<?php
/**
 * AURA Fashion E-Commerce - Database Configuration
 */

if (ob_get_level() === 0) {
    ob_start();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Credentials
$db_host = "localhost";
$db_port = "3307"; // Your MySQL port
$db_name = "aura_db";
$db_user = "root";
$db_pass = "";

// Timezone
date_default_timezone_set('Asia/Kolkata');

try {
    $pdo = new PDO(
        "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    $db_connected = true;

} catch (PDOException $e) {
    $db_connected = false;

    error_log("AURA Database Error: " . $e->getMessage());

    die("Unable to connect to the AURA database. Please check your XAMPP MySQL configuration.");
}

// Include helper functions
require_once __DIR__ . '/../includes/functions.php';