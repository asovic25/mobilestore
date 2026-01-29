<?php
// inc/verify_paystack.php
require_once __DIR__ . '/db.php';

$reference = $_GET['reference'] ?? '';
if (!$reference) {
    echo "Missing reference.";
    exit;
}

// Paystack secret key
$PAYSTACK_SECRET = 'PAYSTACK_SECRET_KEY_HERE'; // same as above
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/".urlencode($reference),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer {$PAYSTACK_SECRET}"
    ]
]);
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
    echo "Error verifying transaction: " . htmlspecialchars($err);
    exit;
}
$resp = json_decode($response, true);
if (!$resp || !isset($resp['status'])) {
    echo "Invalid verification response.";
    exit;
}

if ($resp['status'] && $resp['data']['status'] === 'success') {
    // mark order as paid
    $stmt = $pdo->prepare("UPDATE orders SET status = ?, metadata = JSON_SET(COALESCE(metadata,'{}'), '$.paystack', ?) WHERE reference = ?");
    $stmt->execute(['paid', json_encode($resp['data']), $reference]);

    // Optionally: create order_items records from metadata cart
    // Show success to user
    echo "<h2>Payment successful!</h2>";
    echo "<p>Reference: ".htmlspecialchars($reference)."</p>";
    echo "<p>Amount: ₦".number_format($resp['data']['amount']/100,2)."</p>";
    echo '<p><a href="/public/index.php">Back to shop</a></p>';
    // Clear cart client-side: you can instruct user to clear or use JS
} else {
    // mark failed
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE reference = ?");
    $stmt->execute(['failed', $reference]);
    echo "<h2>Payment failed or incomplete.</h2>";
    echo "<p>Reference: ".htmlspecialchars($reference)."</p>";
    echo '<p><a href="/public/cart.html">Back to cart</a></p>';
}