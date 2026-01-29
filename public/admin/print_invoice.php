<?php
require_once '../../inc/db.php';
require_once __DIR__ . '/../../inc/config.php';
include __DIR__ . '/../../inc/head.php';


$order_id = (int)($_GET['order_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM orders_backup WHERE id=?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) die("Order not found.");

// Decode cart
$cart = json_decode($order['cart_data'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice #<?= $order['id'] ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { padding: 20px; font-family: Arial, sans-serif; }
</style>
</head>
<body>
<div class="container">
    <h3>MobileStore Invoice</h3>
    <hr>
    <p><strong>Order ID:</strong> <?= $order['id'] ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
    <p><strong>Status:</strong> <?= ucfirst($order['status']) ?></p>
    <p><strong>Reference:</strong> <?= htmlspecialchars($order['reference']) ?></p>
    <p><strong>Date:</strong> <?= $order['created_at'] ?></p>
    <hr>
    <h5>Cart Items</h5>
    <?php if($cart): ?>
        <ul class="list-group">
        <?php foreach($cart as $item): ?>
            <li class="list-group-item">
                <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                Qty: <?= $item['quantity'] ?? 1 ?> — ₦<?= number_format($item['price'],2) ?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No items found.</p>
    <?php endif; ?>
    <hr>
    <h5>Total: ₦<?= number_format($order['total'],2) ?></h5>
    <button class="btn btn-primary mt-3" onclick="window.print()">Print Invoice</button>
</div>
</body>
</html>
