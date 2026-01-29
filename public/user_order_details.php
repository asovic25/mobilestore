<?php
session_start();
require_once '../inc/db.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';
include __DIR__ . '/../inc/header.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: user_login.php");
    exit;
}

$userId  = $_SESSION['user']['id'];
$orderId = $_GET['order_id'] ?? 0;

// Fetch order info: prefer orders.product_name, fallback to user_products
$stmt = $pdo->prepare("
    SELECT 
        o.id,
        o.quantity,
        o.total_price,
        o.status,
        o.created_at,
        o.payment_ref,
        o.product_name,
        p.name AS product_name_live,
        p.description AS product_description,
        s.username AS seller_name,
        s.email AS seller_email
    FROM orders o
    LEFT JOIN user_products p ON o.product_id = p.id
    LEFT JOIN users s ON o.seller_id = s.id
    WHERE o.id = ? AND o.buyer_id = ?
");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch order tracking timeline
$timelineStmt = $pdo->prepare("
    SELECT status, note, created_at
    FROM order_status_log
    WHERE order_id = ?
    ORDER BY created_at ASC
");
$timelineStmt->execute([$orderId]);
$logs = $timelineStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">

<?php if ($order): ?>


<!-- Display success/error messages -->
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

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h4 class="text-primary"><?= htmlspecialchars($order['product_name'] ?? $order['product_name_live'] ?? 'Product Removed') ?></h4>
        <p><?= nl2br(htmlspecialchars($order['product_description'] ?? '')) ?></p>
        <p>Quantity: <strong><?= $order['quantity'] ?></strong></p>
        <p>Seller: <?= htmlspecialchars($order['seller_name'] ?? 'N/A') ?> (<?= htmlspecialchars($order['seller_email'] ?? '-') ?>)</p>
        <p>Total: ₦<?= number_format((float)$order['total_price'], 2) ?></p>
        <p>Payment Reference: <?= htmlspecialchars($order['payment_ref'] ?? '-') ?></p>
        <p>Status: 
            <span class="badge <?= match($order['status']){
                'pending'=>'bg-warning text-dark',
                'paid'=>'bg-success',
                'shipped'=>'bg-info text-dark',
                'completed'=>'bg-primary',
                'cancelled'=>'bg-danger',
                default=>'bg-secondary'
            } ?>">
            <?= ucfirst($order['status']) ?>
            </span>
        </p>

        <!-- Action Buttons -->
        <div class="mt-3">
            <a href="invoice.php?order_id=<?= $order['id'] ?>" class="btn btn-rose btn-sm me-2" target="_blank">
                <i class="fa fa-download me-1"></i> Download Invoice
            </a>

            <?php if(!in_array($order['status'], ['cancelled','completed'])): ?>
                <a href="request_cancel.php?order_id=<?= $order['id'] ?>" class="btn btn-danger btn-sm">
                    <i class="fa fa-ban me-1"></i> Request Cancellation
                </a>
            <?php endif; ?>

            <?php if(in_array($order['status'], ['cancelled','completed'])): ?>
                <a href="delete_order.php?order_id=<?= $order['id'] ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Are you sure you want to delete this order?');">
                   <i class="fa fa-trash me-1"></i> Delete Order
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Order Tracking Timeline -->
<h5 class="mb-3 text-primary">Order Timeline & Delivery Updates</h5>
<?php if ($logs): ?>
    <div class="timeline">
        <?php foreach ($logs as $log): ?>
            <div class="timeline-item card shadow-sm p-3 mb-3">
                <span class="badge bg-primary"><?= ucfirst($log['status']) ?></span>
                <?php if (!empty($log['note'])): ?>
                    <p class="mt-2 mb-1"><?= htmlspecialchars($log['note']) ?></p>
                <?php endif; ?>
                <small class="text-muted"><?= date('d M Y, h:i A', strtotime($log['created_at'])) ?></small>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="text-muted">No tracking updates yet.</p>
<?php endif; ?>

<?php else: ?>
    <div class="alert alert-info text-center">
        Order not found or you do not have permission to view it.
    </div>
<?php endif; ?>

</div>

<style>
:root{
  --primary:#6A1B9A;
  --accent:#E91E63;
  --secondary:#F3E5F5;
  --rose-light:#f6d9ff;
  --text-dark:#1c0033;
  --white:#fff;
}
body{ background-color: var(--secondary); font-family:'Poppins',sans-serif; }
.timeline-item span{ background: var(--primary); color: #fff; padding: 5px 12px; border-radius: 12px; font-size: 0.85rem; }
.timeline-item p{ margin-top: 8px; margin-bottom: 4px; }
.timeline-item small{ font-size: 0.8rem; color: #555; }
.btn-rose{ background-color: var(--primary); color: #fff; font-weight:600; border:none; }
.btn-rose:hover{ background-color:#5A137F; color: var(--accent); }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include __DIR__ . '/../inc/footer.php'; ?>
