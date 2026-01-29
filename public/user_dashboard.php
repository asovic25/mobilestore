<?php
// public/user_dashboard.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../inc/db.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';

// Redirect if not logged in
if (!isset($_SESSION['user']['id'])) {
    header("Location: user_login.php");
    exit;
}

$user = $_SESSION['user'];
$userId = $user['id'];

// Fetch categories for navbar
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent orders for this user (as buyer)
$stmt = $pdo->prepare("
    SELECT 
        o.id,
        o.quantity,
        o.total_price,
        o.status,
        o.created_at,
        p.name AS product_name,
        s.username AS seller_name
    FROM orders o
    JOIN user_products p ON o.product_id = p.id
    JOIN users s ON o.seller_id = s.id
    WHERE o.buyer_id = :user_id
    ORDER BY o.created_at DESC
    LIMIT 5
");
$stmt->execute([':user_id' => $userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Avatar path
$avatarPath = "uploads/avatars/" . ($user['avatar'] ?? 'default.png');
if (!file_exists(__DIR__ . '/' . $avatarPath)) {
    $avatarPath = "uploads/avatars/default.png";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard | Rose Store</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root {
  --primary: #6A1B9A;
  --accent: #E91E63;
  --secondary: #F3E5F5;
  --white: #fff;
}
body {
  background-color: var(--secondary);
  font-family: 'Poppins', sans-serif;
  transition: 0.3s;
}
.profile-img {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 50%;
  border: 4px solid var(--primary);
}
.dashboard-card { transition: transform 0.3s; }
.dashboard-card:hover { transform: scale(1.03); }
.navbar { background-color: var(--primary)!important; }
.card-header { background-color: var(--primary); color: #fff; }
.btn-rose { background-color: var(--primary); color: #fff; font-weight:600; border:none; }
.btn-rose:hover { background-color:#5A137F; color: var(--accent); }
.order-line { display:flex; flex-wrap:wrap; gap:1rem; margin-top:1rem; }
.order-card { background:#fff; border-radius:10px; padding:15px; flex:1 1 250px; box-shadow:0 3px 10px rgba(0,0,0,0.1); }
.order-card span { display:block; margin-bottom:5px; }
.order-card .text-muted { font-size:0.85rem; }
footer.footer-section { background-color: var(--primary); color: #fff; padding:2rem 0; }
footer a { color: #fff; text-decoration: none; }
footer a:hover { color: var(--accent); text-decoration: underline; }
</style>
</head>
<body>

<?php include __DIR__ . '/../inc/header.php'; ?>

<div class="container py-5 text-center">
  <img src="<?= htmlspecialchars($avatarPath) ?>" class="profile-img mb-3" alt="Avatar">
  <h4><?= htmlspecialchars($user['fullname'] ?? $user['username']) ?></h4>
  <p class="text-muted"><?= htmlspecialchars($user['email'] ?? 'Not available') ?></p>
  <p><strong>Role:</strong> <?= htmlspecialchars($user['role'] ?? 'buyer') ?></p>
  <a href="edit_profile.php" class="btn btn-rose btn-sm mt-2">Edit Profile</a>
</div>

<div class="container mb-4">
  <div class="row g-3">
  <div class="col-md-4">
  <div class="card shadow-sm dashboard-card">
    <div class="card-body text-center">
      <i class="fa-solid fa-cart-shopping fa-2x mb-2" style="color: var(--primary);"></i>
      <h5>Orders</h5>
      <p><?= count($orders) ?> recent orders</p>
      <a href="user_orders.php" class="btn btn-rose btn-sm mt-2">
        <i class="fa fa-truck me-1"></i> Track Orders
      </a>
    </div>
  </div>
</div>


    <div class="col-md-4">
      <div class="card shadow-sm dashboard-card">
        <div class="card-body text-center">
          <i class="fa-solid fa-box fa-2x mb-2" style="color: var(--primary);"></i>
          <h5>My Products</h5>
          <p>View all products you submitted</p>
          <a href="user_products.php" class="btn btn-rose btn-sm mt-2">View Products</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm dashboard-card">
        <div class="card-body text-center">
          <i class="fa-solid fa-plus fa-2x mb-2" style="color: var(--primary);"></i>
          <h5>Add New Product</h5>
          <p>Submit new items for approval</p>
          <a href="user_add_product.php" class="btn btn-warning btn-sm mt-2">Add Product</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container">
  <h5 class="mb-3">Recent Orders</h5>
  <div class="order-line">
    <?php if ($orders): ?>
      <?php foreach ($orders as $order): ?>
        <div class="order-card">
          <span><strong><?= htmlspecialchars($order['product_name']) ?></strong></span>
          <span>Qty: <?= $order['quantity'] ?></span>
          <span>Total: ₦<?= number_format((float)$order['total_price'], 2) ?></span>
          <span>Status: <?= htmlspecialchars($order['status']) ?></span>
          <span class="text-muted"><?= date('d M Y', strtotime($order['created_at'])) ?></span>
          <span>Seller: <?= htmlspecialchars($order['seller_name']) ?></span>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted">No recent orders</p>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../inc/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
