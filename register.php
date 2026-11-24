<?php
/**
 * AURA Fashion Atelier - Client Registration
 */
require_once "config/db.php";

// If already logged in, redirect
if (is_logged_in()) {
    header("Location: products.php");
    exit;
}

$error = "";

// Process Registration Submission BEFORE outputting HTML
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $name     = trim($_POST["name"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm  = $_POST["confirm_password"] ?? "";

    // Comprehensive Server-Side Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = "Please fill in all required registration fields.";
    } elseif (mb_strlen($name) < 2) {
        $error = "Please provide your full name (at least 2 characters).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please provide a valid email address.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters in length.";
    } elseif ($password !== $confirm) {
        $error = "The entered passwords do not match. Please re-enter.";
    } else {
        if (isset($pdo)) {
            try {
                // 1. Check for Duplicate Email
                $checkStmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
                $checkStmt->execute([$email]);
                if ($checkStmt->fetch()) {
                    $error = "This email address is already registered. Please log in instead.";
                } else {
                    // 2. Hash Password securely using password_hash()
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // 3. Insert New User
                    $insertStmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                    $insertStmt->execute([$name, $email, $hashedPassword]);
                    $newUserId = (int)$pdo->lastInsertId();

                    // 4. Log in new user immediately
                    session_regenerate_id(true);
                    $_SESSION["user_id"]  = $newUserId;
                    $_SESSION["username"] = $name;
                    $_SESSION["email"]    = $email;

                    set_flash('success', "Welcome to AURA, {$name}! Your account has been registered.");
                    header("Location: products.php");
                    exit;
                }
            } catch (PDOException $e) {
                $error = "Registration error. Please try again. (" . h($e->getMessage()) . ")";
            }
        } else {
            $error = "Database offline. Please start MySQL in XAMPP.";
        }
    }
}

$pageTitle = "Create an Account";
require "includes/header.php";
?>

<div class="auth-page">
  <div class="container" style="display: flex; justify-content: center;">
    
    <div class="auth-card">
      <div class="auth-header">
        <span class="eyebrow">NEW CLIENT REGISTRATION</span>
        <h1>Join AURA Atelier</h1>
        <p>Create an account to save personal wishlists, receive recommendations, and track orders.</p>
      </div>

      <?php if ($error): ?>
        <div class="toast-alert toast-error" style="margin-bottom: 20px;">
          <span class="toast-indicator"></span>
          <span class="toast-message"><?= h($error) ?></span>
        </div>
      <?php endif; ?>

      <form method="post" action="register.php" class="auth-form">
        <?= csrf_field() ?>

        <div class="form-group">
          <label for="regName">Full Name *</label>
          <input type="text" id="regName" name="name" value="<?= h($_POST['name'] ?? '') ?>" placeholder="e.g. Maya Sharma" required autofocus>
        </div>

        <div class="form-group">
          <label for="regEmail">Email Address *</label>
          <input type="email" id="regEmail" name="email" value="<?= h($_POST['email'] ?? '') ?>" placeholder="name@domain.com" required>
        </div>

        <div class="form-group">
          <label for="regPass">Password (min. 8 characters) *</label>
          <input type="password" id="regPass" name="password" minlength="8" placeholder="Create a secure password" required>
        </div>

        <div class="form-group">
          <label for="regConfirm">Confirm Password *</label>
          <input type="password" id="regConfirm" name="confirm_password" minlength="8" placeholder="Re-enter your password" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; margin-top: 10px;">
          Create My Account &rarr;
        </button>
      </form>

      <div class="auth-footer">
        Already have an account? <a href="login.php">Log in here</a>
      </div>
    </div>

  </div>
</div>

<?php require "includes/footer.php"; ?>
