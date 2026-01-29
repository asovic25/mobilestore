<?php
if(isset($_SESSION['success'])){
    echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
    unset($_SESSION['success']);
}
if(isset($_SESSION['error'])){
    echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
    unset($_SESSION['error']);
}
?>


<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once '../inc/db.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';

// Redirect if not logged in
if (!isset($_SESSION['user']['id'])) {
    header("Location: user_login.php");
    exit;
}

$userId = $_SESSION['user']['id'];

// Fetch user orders (only what exists in your DB)
$stmt = $pdo->prepare("SELECT * FROM orders WHERE buyer_id = ? ORDER BY id DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders | Rose Store</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{
  --primary:#6A1B9A;
  --accent:#E91E63;
  --secondary:#F3E5F5;
  --rose-light:#f6d9ff;
  --text-dark:#1c0033;
  --white:#fff;
}

body{ background:var(--secondary); font-family:'Poppins',sans-serif; }

.order-card{
  background: var(--white);
  border-radius:14px;
  padding:20px;
  box-shadow:0 6px 16px rgba(0,0,0,.08);
  margin-bottom:20px;
}

.badge-status{
  padding:.45rem .9rem;
  border-radius:20px;
  font-size:.8rem;
}

.pending{background:#ff9800;color:#000;}
.paid{background:#28a745;}
.shipped{background:#0d6efd;}
.completed{background:#6f42c1;}
.cancelled{background:#dc3545;}

.btn-links a{
  display:block;
  margin-top:5px;
}
</style>
</head>
<body>

<?php include __DIR__ . '/../inc/header.php'; ?>

<div class="container py-5">

<h3 class="text-center mb-4" style="color:var(--primary)">My Orders</h3>

<?php if($orders): ?>
  <?php foreach($orders as $o): ?>
    <?php
        $statusClass = strtolower($o['status']);
    ?>
    <div class="order-card">
      <div class="d-flex justify-content-between flex-wrap">
        <div>
          <p><strong>Order ID:</strong> <?= $o['id'] ?></p>
          <p><strong>Status:</strong> <span class="badge badge-status <?= $statusClass ?>"><?= ucfirst($o['status']) ?></span></p>
        </div>

        <div class="btn-links text-end">
          <a href="user_order_details.php?order_id=<?= $o['id'] ?>" class="btn btn-outline-primary btn-sm">
            <i class="fa fa-eye"></i> View Order
          </a>
          <a href="order_track.php?order_id=<?= $o['id'] ?>" class="btn btn-outline-info btn-sm">
            <i class="fa fa-truck"></i> Track Order
          </a>
          <a href="invoice.php?order_id=<?= $o['id'] ?>" target="_blank" class="btn btn-outline-success btn-sm">
            <i class="fa fa-print"></i> Invoice
          </a>
          <a href="request_cancel.php?order_id=<?= $o['id'] ?>" class="btn btn-outline-danger btn-sm">
            <i class="fa fa-ban"></i> Cancel Order
          </a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <div class="alert alert-info text-center">
    You have not made any orders yet.
  </div>
<?php endif; ?>

</div>

<?php include __DIR__ . '/../inc/footer.php'; ?>
</body>
</html>
