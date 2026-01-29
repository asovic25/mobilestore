<?php
$orderId = $_GET['order_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Successful | Rose Store</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container text-center">
    <h3 class="text-success mb-4">Your order has been placed successfully!</h3>
    <?php if ($orderId): ?>
        <p>Order ID: <strong><?= htmlspecialchars($orderId) ?></strong></p>
    <?php endif; ?>
    <a href="index.php" class="btn btn-primary">Continue Shopping</a>
</div>
</body>
</html>
