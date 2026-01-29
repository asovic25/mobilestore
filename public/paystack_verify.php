<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once '../inc/db.php';
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/csrf.php'; // CSRF functions
include __DIR__ . '/../inc/head.php';

// 1️⃣ Validate reference & CSRF token
$reference = $_GET['reference'] ?? '';
$csrfToken = $_GET['csrf_token'] ?? '';

if (empty($reference) || !verify_csrf_token($csrfToken)) {
    die("Invalid payment reference or CSRF token. Cannot process order.");
}

// 2️⃣ Verify transaction from Paystack
$secretKey = "sk_test_741d14601a8445e743784ff12fe27eccd1365db3";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/{$reference}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ["Authorization: Bearer {$secretKey}"]
]);
$response = curl_exec($curl);
curl_close($curl);

$result = json_decode($response, true);

if (!$result['status'] || $result['data']['status'] !== 'success') {
    die("Payment verification failed. Please try again.");
}

// 3️⃣ Extract transaction data
$amountPaid = $result['data']['amount'] / 100;
$buyerEmail = $result['data']['customer']['email'] ?? 'guest@example.com';
$buyerId = $_SESSION['user']['id'] ?? null;

// 4️⃣ Ensure cart is not empty
if (empty($_SESSION['checkout']['cart'])) {
    die("Cart is empty. Cannot process order.");
}

// 5️⃣ Insert orders
$cart = $_SESSION['checkout']['cart'];
$total = 0;
$cartItemsHTML = '';

foreach ($cart as $item) {
    $prodId = preg_replace('/^[pu]-/', '', $item['id']);
    $quantity = (int)($item['quantity'] ?? 1);
    $price = $item['price'] ?? 0;
    $sellerId = $item['seller_id'] ?? 1;

    $stmt = $pdo->prepare("
        INSERT INTO orders 
        (product_id, buyer_id, seller_id, quantity, price, total_price, payment_ref, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'paid', NOW())
    ");
    $stmt->execute([$prodId, $buyerId, $sellerId, $quantity, $price, $price*$quantity, $reference]);

    // Reduce stock
    if (str_starts_with($item['id'], 'p-')) {
        $pdo->prepare("UPDATE products SET stock = GREATEST(stock - ?, 0) WHERE id = ?")
            ->execute([$quantity, $prodId]);
    } else {
        $pdo->prepare("UPDATE user_products SET stock = GREATEST(stock - ?, 0) WHERE id = ?")
            ->execute([$quantity, $prodId]);
    }

    $subtotal = $price * $quantity;
    $total += $subtotal;
    $cartItemsHTML .= "
        <tr>
            <td>" . htmlspecialchars($item['name']) . "</td>
            <td>{$quantity}</td>
            <td>₦" . number_format($price, 2) . "</td>
            <td>₦" . number_format($subtotal, 2) . "</td>
        </tr>
    ";
}

// 6️⃣ Clear cart
unset($_SESSION['cart'], $_SESSION['checkout']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Successful | Rose Store</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root {
    --primary:#6A1B9A; 
    --accent:#E91E63; 
    --secondary:#F3E5F5; 
    --white:#fff;
}
body {
    font-family: 'Poppins', sans-serif;
    background: var(--secondary);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}
.container {
    background: var(--white);
    padding: 40px 30px;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    max-width: 700px;
    width: 100%;
    text-align: center;
}
h2 { color: var(--primary); margin-bottom: 15px; }
.checkmark {
    font-size: 60px;
    background: var(--primary);
    color: var(--white);
    border-radius: 50%;
    width: 80px;
    height: 80px;
    line-height: 80px;
    margin: 0 auto 20px;
}
.table thead { background-color: var(--accent); color: #fff; }
.table tbody tr:hover { background-color: #f9f0ff; }
a.btn { border-radius: 30px; padding: 10px 25px; font-weight: 600; margin: 10px 5px 0; }
a.btn-primary { background-color: var(--primary); border:none; color: #fff; }
a.btn-primary:hover { background-color: #5A137F; color: var(--accent); }
a.btn-secondary { background-color: var(--accent); border:none; color: #fff; }
a.btn-secondary:hover { background-color: #9c1b6f; color: #fff; }
@media (max-width:768px){
    .container { padding:20px; }
    .table th, .table td { font-size: 0.9rem; }
}
</style>
</head>
<body>

<div class="container">
    <div class="checkmark">✔</div>
    <h2>Payment Successful!</h2>
    <p>Thank you for your purchase 🎉</p>
    <p><strong>Reference:</strong> <?= htmlspecialchars($reference) ?></p>

    <h4 class="mt-4">Order Summary</h4>
    <table class="table table-bordered mt-2">
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?= $cartItemsHTML ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total:</th>
                <th>₦<?= number_format($total, 2) ?></th>
            </tr>
        </tfoot>
    </table>

    <a href="index.php" class="btn btn-primary">Back to Store</a>
    <a href="order.php" class="btn btn-secondary">View Your Orders</a>
</div>

</body>
</html>
