<?php
require_once "../inc/db.php";
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';

if (!isset($_GET['reference'])) {
    die("Invalid request");
}

$reference = $_GET['reference'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE reference = ?");
$stmt->execute([$reference]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found");
}

$product_id = $order['product_id'];
$amount = $order['amount'] * 100; // Convert to kobo

// Fetch product name
$stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pay with Paystack | Rose Store</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://js.paystack.co/v1/inline.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f5f0f8;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}
.card {
    background: #fff;
    border-radius: 15px;
    padding: 40px;
    box-shadow: 0 4px 25px rgba(0,0,0,0.15);
    max-width: 500px;
    width: 100%;
    text-align: center;
    animation: fadeIn 1s ease;
}
h2 {
    color: #6f42c1;
    margin-bottom: 15px;
}
.amount {
    font-size: 1.5rem;
    font-weight: 600;
    color: #6f42c1;
    margin-bottom: 30px;
}
.btn-pay {
    background: #6f42c1;
    color: #fff;
    border-radius: 30px;
    padding: 12px 30px;
    font-size: 1.2rem;
    font-weight: 600;
    transition: 0.3s;
}
.btn-pay:hover {
    background: #5a329e;
    color: #fff;
}
.fa-credit-card {
    margin-right: 10px;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>

<div class="card">
    <i class="fa-solid fa-credit-card fa-3x mb-3" style="color:#6f42c1;"></i>
    <h2>Pay for: <?= htmlspecialchars($product['name']) ?></h2>
    <p class="amount">₦<?= number_format($order['amount'], 2) ?></p>
    <button class="btn btn-pay btn-lg" onclick="payWithPaystack()">
        <i class="fa-solid fa-wallet"></i> Pay Now
    </button>
    <div class="mt-4">
        <a href="cart.php" style="text-decoration:none; color:#6f42c1;">Back to Cart</a> |
        <a href="index.php" style="text-decoration:none; color:#6f42c1;">Continue Shopping</a>
    </div>
</div>

<script>
function payWithPaystack() {
    let handler = PaystackPop.setup({
        key: 'pk_test_81dfc65e7f8b6b35a1981ac0df1dc67d71a7a2e7', // 🔴 replace with your key
        email: 'customer@email.com', // ideally use logged-in user email
        amount: <?= $amount ?>,
        currency: 'NGN',
        ref: '<?= $reference ?>',
        callback: function(response){
            window.location.href = 'payment_success.php?reference=' + response.reference;
        },
        onClose: function(){
            alert('Transaction was not completed.');
        }
    });
    handler.openIframe();
}
</script>

</body>
</html>
