<?php
session_start();
require_once '../inc/db.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: user_login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Fetch all orders for this user (buyer)
$stmt = $pdo->prepare("
    SELECT 
        o.id AS order_id,
        o.product_id,
        o.quantity,
        o.price,
        o.total_price,
        o.status,
        o.created_at,
        p.name AS product_name,
        p.images AS product_images
    FROM orders o
    LEFT JOIN products p ON o.product_id = p.id
    WHERE o.buyer_id = :user_id
    ORDER BY o.created_at DESC
");
$stmt->execute([':user_id' => $user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../inc/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Orders | Mobilestore</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root {
    --primary:#6A1B9A; 
    --accent:#E91E63; 
    --secondary:#F3E5F5; 
    --dark:#212121; 
    --white:#fff;
}
body { background-color: var(--secondary); font-family: 'Segoe UI', sans-serif; }
.navbar { background: var(--primary); }
.navbar .navbar-brand { font-weight: bold; }
.navbar .btn-outline-light { border-color: #fff; color: #fff; }
.navbar .btn-outline-light:hover { background-color: #fff; color: var(--primary); }
.card { border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
.table thead { background: var(--primary); color: var(--white); }
.table-hover tbody tr:hover { background-color: #f5e6fa; }
.product-img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
.badge-status { font-size: 0.9em; font-weight: 500; }
.badge-warning { background-color: #f1c40f !important; }
.badge-success { background-color: var(--primary) !important; }
.badge-danger { background-color: var(--accent) !important; }
.badge-secondary { background-color: var(--dark) !important; }
h3 { color: var(--primary); }
.table-responsive { overflow-x: auto; }
</style>
</head>
<body>

<div class="container py-5">
<h3 class="mb-4"><i class="fa fa-receipt"></i> My Orders</h3>

<?php if(!empty($orders)): ?>
    <?php foreach($orders as $order): ?>
        <?php
        $productName = $order['product_name'] ?? 'Unknown Product';
        $images = json_decode($order['product_images'], true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($images)) {
            $images = [$order['product_images']]; // fallback
        }

        // Product image path relative to public folder
        $imgSrc = !empty($images[0]) ? $images[0] : 'uploads/avatars/default.png';

        $statusClass = match(strtolower($order['status'])) {
            'pending' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
        ?>
        <div class="card mb-4 p-3">
            <div class="d-flex align-items-center mb-3">
                <img src="<?= htmlspecialchars($imgSrc) ?>" class="product-img me-3" alt="<?= htmlspecialchars($productName) ?>">
                <div>
                    <h5 class="mb-1"><?= htmlspecialchars($productName) ?></h5>
                    <span class="badge badge-status badge-<?= $statusClass ?>"><?= ucfirst($order['status']) ?></span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Quantity</th>
                            <th>Price (₦)</th>
                            <th>Total (₦)</th>
                            <th>Ordered On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= (int)$order['quantity'] ?></td>
                            <td><?= number_format((float)$order['price'], 2) ?></td>
                            <td><?= number_format((float)$order['total_price'], 2) ?></td>
                            <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">You have not purchased any products yet.</div>
<?php endif; ?>
</div>
<?php include __DIR__ . '/../inc/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
