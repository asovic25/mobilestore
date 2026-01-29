<?php
require_once '../../inc/db.php';
require_once '../../inc/functions.php';
require_once __DIR__ . '/../../inc/config.php';
include __DIR__ . '/../../inc/head.php';

// Handle approval or rejection
if (isset($_GET['action']) && isset($_GET['id'])) {
    $productId = $_GET['id'];
    $action = $_GET['action'];

    $status = ($action === 'approve') ? 'approved' : 'rejected';

    $stmt = $pdo->prepare("UPDATE user_products SET status = ? WHERE id = ?");
    $stmt->execute([$status, $productId]);

    header("Location: manage_user_products.php?msg=Product $status successfully");
    exit;
}

// Fetch all user-submitted products
$stmt = $pdo->query("SELECT up.*, u.email, c.name AS category_name 
                     FROM user_products up
                     LEFT JOIN users u ON up.user_id = u.id
                     LEFT JOIN categories c ON up.category_id = c.id
                     ORDER BY up.created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage User Products - Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="admin_dashboard.php">📦 Admin Panel</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="adminNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a href="admin_dashboard.php" class="nav-link">Dashboard</a></li>
        <li class="nav-item"><a href="manage_user_products.php" class="nav-link active">Manage User Products</a></li>
        <li class="nav-item"><a href="../logout.php" class="nav-link text-warning">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="container py-4">
  <h2 class="text-center mb-4 text-primary">Manage User-Submitted Products</h2>

  <?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success text-center"><?= htmlspecialchars($_GET['msg']) ?></div>
  <?php endif; ?>

  <div class="table-responsive bg-white rounded shadow-sm p-3">
    <table class="table table-bordered align-middle">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Product</th>
          <th>Description</th>
          <th>Price</th>
          <th>Category</th>
          <th>User Email</th>
          <th>Images</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($products) > 0): ?>
          <?php foreach ($products as $i => $product): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= htmlspecialchars($product['name']) ?></td>
              <td><?= htmlspecialchars(substr($product['description'], 0, 50)) . '...' ?></td>
              <td>₦<?= number_format($product['price'], 2) ?></td>
              <td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td>
              <td><?= htmlspecialchars($product['email'] ?? 'Unknown') ?></td>
              <td>
                <?php
                  $images = explode(',', $product['images']);
                  foreach ($images as $img) {
                    $img = trim($img);
                    if (!empty($img)) {
                      echo '<img src="../uploads/' . htmlspecialchars($img) . '" width="50" class="me-1 rounded border" />';
                    }
                  }
                ?>
              </td>
              <td>
                <?php
                  $statusClass = match($product['status']) {
                      'approved' => 'text-success fw-bold',
                      'rejected' => 'text-danger fw-bold',
                      default => 'text-warning fw-bold'
                  };
                  echo '<span class="' . $statusClass . '">' . ucfirst($product['status']) . '</span>';
                ?>
              </td>
              <td>
                <?php if ($product['status'] === 'pending'): ?>
                  <a href="?action=approve&id=<?= $product['id'] ?>" class="btn btn-success btn-sm mb-1">Approve</a>
                  <a href="?action=reject&id=<?= $product['id'] ?>" class="btn btn-danger btn-sm">Reject</a>
                <?php else: ?>
                  <button class="btn btn-secondary btn-sm" disabled>No Action</button>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" class="text-center text-muted">No user products found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
