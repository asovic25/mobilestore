<?php
// public/user_login.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';
require_once __DIR__ . '/../inc/bootstrap.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }

    $identifier = trim($_POST['identifier'] ?? ''); // email or username
    $password = $_POST['password'] ?? '';

    if ($identifier === '' || $password === '') {
        $error = 'Please provide your login details.';
    } else {
        // Fetch user by email or username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if (!$user['is_active']) {
                $error = 'Account not activated. Check your email.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'avatar' => $user['avatar'],
                    'fullname' => $user['fullname'] ?? $user['username'],
                    'email' => $user['email']
                ];

                header('Location: user_dashboard.php');
                exit;
            }
        } else {
            $error = 'Invalid credentials. Please try again.';
        }
    }
}

?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>User Login | Rose Store</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root {
    --primary:#6A1B9A;
    --accent:#E91E63;
    --secondary:#F3E5F5;
    --white:#fff;
}
body {
    background: linear-gradient(135deg, var(--secondary), #fff);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Poppins', sans-serif;
    overflow: hidden;
    position: relative;
}
body::before, body::after {
    content: '';
    position: absolute;
    width: 200px;
    height: 200px;
    background: url('../images/rose_icon.png') no-repeat center/contain;
    opacity: 0.1;
    z-index: 0;
}
body::before { top: 10%; left: 5%; transform: rotate(15deg); }
body::after { bottom: 15%; right: 5%; transform: rotate(-20deg); }

.login-card {
    position: relative;
    background: var(--white);
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    padding: 40px 30px;
    width: 100%;
    max-width: 420px;
    transition: transform 0.3s;
    z-index: 1;
}
.login-card:hover { transform: translateY(-5px); }
.login-card h3 {
    text-align: center;
    color: var(--primary);
    margin-bottom: 25px;
    font-weight: 700;
}
.form-label { font-weight: 500; color: #333; }
.btn-rose {
    background-color: var(--primary);
    color: var(--white);
    font-weight: 600;
    border: none;
    transition: all 0.3s;
}
.btn-rose:hover { background-color: #5A137F; color: var(--accent); }
.forgot-link {
    display: block;
    text-align: right;
    font-size: 0.9rem;
    margin-top: -10px;
    margin-bottom: 15px;
    color: var(--primary);
    text-decoration: none;
}
.forgot-link:hover { text-decoration: underline; color: var(--accent); }
.alert-danger {
    background-color: #f8d7da;
    color: #842029;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
}
.register-link {
    display: block;
    text-align: center;
    margin-top: 20px;
    font-size: 0.95rem;
}
.register-link a {
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
}
.register-link a:hover { color: var(--accent); }

.home-link {
    text-align: center;
    margin-top: -10px;
    margin-bottom: 20px;
}

.home-link a {
    font-size: 26px;
    color: var(--primary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.home-link a:hover {
    background-color: var(--secondary);
    color: var(--accent);
    transform: translateY(-2px);
}

</style>
</head>
<body>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-4">
      <div class="login-card">
        <h3>Welcome Back 🌹</h3>

        <div class="home-link">
    <a href="index.php" title="Go to Home">
        <i class="fas fa-house"></i>
    </a>
</div>

        <?php if ($error): ?>
          <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

       <form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">

    <div class="mb-3">
        <label for="identifier" class="form-label">Email or Username</label>
        <input type="text" class="form-control" id="identifier" name="identifier"
               value="<?= htmlspecialchars($_POST['identifier'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>

          <a href="forgot_password.php" class="forgot-link">Forgot your password?</a>

          <div class="d-grid mt-3">
            <button type="submit" class="btn btn-rose btn-lg">Login</button>
          </div>

          <div class="register-link">
            Don't have an account? <a href="user_signup.php">Sign Up</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>
