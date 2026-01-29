<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/bootstrap.php';
require_once __DIR__ . '/../inc/csrf.php'; // CSRF functions
include __DIR__ . '/../inc/head.php';

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

// Ensure user is logged in
if (!isset($_SESSION['user']['id'])) {
    header("Location: user_login.php");
    exit;
}

$user = $_SESSION['user'];
$buyer_id = $user['id'];
$buyer_email = $user['email'] ?? 'guest@example.com';

// Update cart items dynamically with database stock
foreach ($_SESSION['cart'] as $id => &$item) {
    $prodId = preg_replace('/^[pu]-/', '', $item['id']);
    if (str_starts_with($item['id'], 'p-')) {
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id=?");
    } else {
        $stmt = $pdo->prepare("SELECT stock FROM user_products WHERE id=?");
    }
    $stmt->execute([$prodId]);
    $dbStock = $stmt->fetchColumn();
    $item['stock'] = (int)$dbStock;
    if ($item['quantity'] > $item['stock']) {
        $item['quantity'] = $item['stock'];
    }
    $item['subtotal'] = $item['price'] * $item['quantity'];
}
unset($item);

// Calculate total amount
$totalAmount = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalAmount += $item['subtotal'];
}

// Convert to kobo for Paystack
$paystackAmount = $totalAmount * 100;

// Paystack Public Key
$paystackPublicKey = "pk_test_22491780fe7103419103a8d9e51a18632512a8d6";

// Generate a unique reference for this transaction
$reference = uniqid("rosestore_");

// Save cart and buyer info in session for verification
$_SESSION['checkout'] = [
    'buyer_id' => $buyer_id,
    'cart' => $_SESSION['cart'],
    'total_amount' => $totalAmount,
    'reference' => $reference
];

// CSRF token
$csrfToken = csrf_token();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout | Rose Store</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root {
    --primary:#6A1B9A;
    --accent:#E91E63;
    --secondary:#F3E5F5;
    --white:#fff;
}

/* Add this to your existing <style> section */
body {
    background: var(--secondary);
    font-family: 'Poppins', sans-serif;
    
}

.container.checkout-container {
    background: var(--white);
    padding: 30px;
    border-radius: 20px;
    margin-top: 50px;
    margin-bottom: 30px; /* extra space before footer */
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

h3 { color: var(--primary); margin-bottom: 25px; text-align:center; }
.table { background: rgba(250,250,250,0.9); }
.table th { color: var(--primary); }
.btn-pay {
    background: var(--primary);
    color: var(--white);
    font-weight: 600;
    width: 100%;
    padding: 12px;
    border-radius: 12px;
    margin-top: 20px;
    border: none;
}
.btn-pay:hover { background: #5A137F; color: var(--accent); }
a.back-link {
    display: inline-block;
    margin-top: 15px;
    color: var(--primary);
    text-decoration: underline;
}
a.back-link:hover { color: var(--accent); }
@media (max-width:768px) {
    .container.checkout-container { padding: 20px; margin-top: 20px; }
    .table th, .table td { font-size: 0.9rem; }
}
</style>
</head>
<body>

<?php include __DIR__ . '/../inc/header.php'; ?>

<div class="container checkout-container">
    <h3>Checkout</h3>
    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price (₦)</th>
                <th>Quantity</th>
                <th>Subtotal (₦)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($_SESSION['cart'] as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= number_format($item['price'],2) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= number_format($item['subtotal'],2) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" class="text-end"><strong>Total</strong></td>
                <td><strong>₦<?= number_format($totalAmount,2) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <form id="checkoutForm" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <button type="button" class="btn btn-pay btn-lg" id="payBtn">Pay with Paystack</button>
    </form>
    <a class="back-link" href="user_dashboard.php">← Back to Dashboard</a>
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
document.getElementById('payBtn').addEventListener('click', function() {
    let handler = PaystackPop.setup({
        key: '<?= $paystackPublicKey ?>',
        email: '<?= $buyer_email ?>',
        amount: <?= $paystackAmount ?>,
        currency: 'NGN',
        ref: '<?= $reference ?>',
        callback: function(response){
            window.location.href = "paystack_verify.php?reference=" + response.reference + "&csrf_token=<?= $csrfToken ?>";
        },
        onClose: function(){
            alert('Transaction cancelled.');
        }
    });
    handler.openIframe();
});
</script>

<?php include __DIR__ . '/../inc/footer.php'; ?>
</body>
</html>
