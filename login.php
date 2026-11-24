<?php
/**
 * AURA Fashion Atelier - Client Login
 */
require_once "config/db.php";

// If already logged in, redirect to catalog
if (is_logged_in()) {
    header("Location: products.php");
    exit;
}

$error = "";
$next = trim($_GET["next"] ?? $_POST["next"] ?? "products.php");

// Sanitize redirect target to avoid open redirect vulnerabilities
if (!preg_match('/^[a-zA-Z0-9_\-\.\/]+\.php(\?.*)?$/', $next)) {
    $next = "products.php";
}

// Process Login Form Submission BEFORE outputting HTML
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {
        $error = "Please enter both your email address and password.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email format.";
    } else {
        if (isset($pdo)) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user["password"])) {
                    // Prevent session fixation
                    session_regenerate_id(true);

                    $_SESSION["user_id"]  = (int)$user["user_id"];
                    $_SESSION["username"] = $user["username"];
                    $_SESSION["email"]    = $user["email"];

                    set_flash('success', "Welcome back, {$user['username']}!");
                    header("Location: " . $next);
                    exit;
                } else {
                    $error = "Invalid email or password. Please verify your credentials.";
                }
            } catch (PDOException $e) {
                $error = "Authentication service error. Please try again later.";
            }
        } else {
            $error = "Database connection offline. Please check XAMPP MySQL.";
        }
    }
}

$pageTitle = "Client Login";
require "includes/header.php";
?>

<div class="auth-page">
  <div class="container" style="display: flex; justify-content: center;">
    
    <div class="auth-card">
      <div class="auth-header">
        <span class="eyebrow">WELCOME BACK</span>
        <h1>Account Sign In</h1>
        <p>Access your saved wishlist, shopping bag, and order tracking.</p>
      </div>

      <?php if ($error): ?>
        <div class="toast-alert toast-error" style="margin-bottom: 20px;">
          <span class="toast-indicator"></span>
          <span class="toast-message"><?= h($error) ?></span>
        </div>
      <?php endif; ?>

      <form method="post" action="login.php" class="auth-form">
        <?= csrf_field() ?>
        <input type="hidden" name="next" value="<?= h($next) ?>">

        <div class="form-group">
          <label for="loginEmail">Email Address</label>
          <input type="email" id="loginEmail" name="email" value="<?= h($_POST['email'] ?? '') ?>" placeholder="name@domain.com" required autofocus>
        </div>

        <div class="form-group">
          <label for="loginPassword">Password</label>
          <input type="password" id="loginPassword" name="password" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; margin-top: 10px;">
          Sign In to Account &rarr;
        </button>
      </form>

      <!-- Demo Credentials Helper Box -->
      <div class="demo-account-hint">
        <strong>Pre-loaded Demo Member Account:</strong><br>
        Email: <code>demo@aurafashion.com</code><br>
        Password: <code>password123</code>
      </div>

      <div class="auth-footer">
        Don't have an AURA account yet? <a href="register.php">Create an Account</a>
      </div>
    </div>

  </div>
</div>

<?php require "includes/footer.php"; ?>
