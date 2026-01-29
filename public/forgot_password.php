<?php
// public/forgot_password.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $error = 'Please enter your email address.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Store reset token in DB
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
            $stmt->execute([$token, $expires_at, $email]);

            // Email content
            $resetLink = "http://localhost/mobilestore/public/reset_password.php?token=$token";
            $subject = "Password Reset Request - MobileStore";
            $body = "Hello " . htmlspecialchars($user['username']) . ",\n\n"
                  . "You requested to reset your password. Click the link below:\n\n"
                  . "$resetLink\n\n"
                  . "This link will expire in 1 hour.\n\n"
                  . "If you didn't request this, you can ignore this email.\n\n"
                  . "- MobileStore Team";

            // Send email (ensure mail() is configured in php.ini)
            if (mail($email, $subject, $body)) {
                $message = "A password reset link has been sent to your email.";
            } else {
                $error = "Failed to send email. Please contact support.";
            }
        } else {
            $error = "Email not found in our records.";
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Forgot Password | MobileStore</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #e8f0fe, #ffffff);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .forgot-card {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      padding: 30px;
      width: 100%;
      max-width: 420px;
    }
    .forgot-card h3 {
      text-align: center;
      color: #198754;
      font-weight: 600;
      margin-bottom: 25px;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-4">
      <div class="forgot-card">
        <h3>Recover Account</h3>

        <?php if ($error): ?>
          <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($message): ?>
          <div class="alert alert-success text-center"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Enter your registered email</label>
            <input type="email" name="email" class="form-control" required placeholder="example@gmail.com">
          </div>
          <div class="d-grid mt-3">
            <button type="submit" class="btn btn-success btn-lg">Send Reset Link</button>
          </div>
        </form>

        <div class="text-center mt-3">
          <a href="user_login.php" class="text-decoration-none">Back to Login</a>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
