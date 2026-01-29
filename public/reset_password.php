<?php
// public/reset_password.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';

$token = $_GET['token'] ?? '';
$error = '';
$message = '';

if ($token === '') {
    $error = 'Invalid or missing reset token.';
} else {
    // Verify token
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error = 'Reset link is invalid or has expired.';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($password === '' || $confirm === '') {
            $error = 'Please fill in all fields.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
            $stmt->execute([$hashed, $user['id']]);
            $message = 'Your password has been reset successfully. You can now log in.';
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Reset Password | MobileStore</title>
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
    .reset-card {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      padding: 30px;
      width: 100%;
      max-width: 420px;
    }
    .reset-card h3 {
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
      <div class="reset-card">
        <h3>Reset Password</h3>

        <?php if ($error): ?>
          <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($message): ?>
          <div class="alert alert-success text-center"><?= htmlspecialchars($message) ?></div>
          <div class="text-center mt-3">
            <a href="user_login.php" class="btn btn-success">Login Now</a>
          </div>
        <?php endif; ?>

        <?php if (!$message && !$error): ?>
        <form method="POST">
          <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
          </div>
          <div class="d-grid mt-3">
            <button type="submit" class="btn btn-success btn-lg">Reset Password</button>
          </div>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

</body>
</html>
