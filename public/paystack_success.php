<?php
session_start();
require_once '../inc/db.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';

// Ensure reference is present
if (!isset($_GET['reference'])) {
    die("No payment reference provided.");
}

$reference = $_GET['reference'];

// Fetch the order from orders_backup
$stmt = $pdo->prepare("SELECT * FROM orders_backup WHERE reference = ?");
$stmt->execute([$reference]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found for reference: " . htmlspecialchars($reference));
}

// Decode cart data
$cartItems = json_decode($order['cart_data'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Mobile Store</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f2f5f9;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 50px;
        }
        .success-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 30px;
            max-width: 700px;
            width: 100%;
            text-align: center;
        }
        .success-container h1 {
            color: #28a745;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .success-container p {
            color: #555;
            font-size: 16px;
            margin: 5px 0;
        }
        .details {
            margin-top: 25px;
            text-align: left;
        }
        .details h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 5px;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .details th, .details td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .details th {
            background-color: #f9f9f9;
        }
        .total {
            font-weight: bold;
            font-size: 18px;
            text-align: right;
            color: #000;
        }
        .back-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 24px;
            background: #28a745;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }
        .back-btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <h1>✅ Payment Successful!</h1>
        <p>Thank you for your purchase.</p>
        <p><strong>Reference:</strong> <?= htmlspecialchars($reference) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars(ucfirst($order['status'])) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($order['created_at']) ?></p>

        <div class="details">
            <h2>🛍️ Order Summary</h2>
            <?php if (!empty($cartItems)) : ?>
                <table>
                    <tr>
                        <th>Product ID</th>
                        <th>Quantity</th>
                        <th>Price (₦)</th>
                    </tr>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['id']) ?></td>
                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                            <td><?= number_format($item['price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <p class="total">Total Paid: ₦<?= number_format($order['total'], 2) ?></p>
            <?php else: ?>
                <p>No items found in your order.</p>
            <?php endif; ?>
        </div>

        <a href="index.php" class="back-btn">🏠 Return to Home</a>
    </div>
</body>
</html>
