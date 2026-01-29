<?php
// inc/paystack_init.php
// NOTE: place this file outside public or restrict via server config in production.
// It accepts JSON body from checkout page.

header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

// Paystack keys - IMPORTANT: store securely (e.g., env variables)
// Fill these with your Paystack keys
$PAYSTACK_SECRET = 'PAYSTACK_SECRET_KEY_HERE';
$PAYSTACK_PUBLIC = 'PAYSTACK_PUBLIC_KEY_HERE';
if (empty($PAYSTACK_SECRET) || empty($PAYSTACK_PUBLIC)) {
    echo json_encode(['status' => false, 'message' => 'Payment gateway not configured', 'public_key' => $PAYSTACK_PUBLIC]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['status'=>false,'message'=>'Invalid payload']);
    exit;
}

$customer_email = $input['customer_email'] ?? '';
$customer_name = $input['customer_name'] ?? '';
$customer_phone = $input['customer_phone'] ?? '';
$amount = floatval($input['amount'] ?? 0);
$cart = $input['cart'] ?? [];

if (!$customer_email || $amount <= 0) {
    echo json_encode(['status'=>false,'message'=>'Missing required fields']);
    exit;
}

// create order in DB with pending status and generate reference
$reference = 'MS_'.bin2hex(random_bytes(6));
try {
    $stmt = $pdo->prepare("INSERT INTO orders (reference, customer_name, customer_email, customer_phone, amount, status, metadata) VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([$reference, $customer_name, $customer_email, $customer_phone, $amount, 'pending', json_encode($cart)]);
    $orderId = $pdo->lastInsertId();
} catch (Exception $e) {
    echo json_encode(['status'=>false,'message'=>'DB error: '.$e->getMessage()]);
    exit;
}

// initialize Paystack transaction
$curl = curl_init();
$postData = [
    'email' => $customer_email,
    'amount' => intval($amount * 100), // kobo
    'reference' => $reference,
    'currency' => 'NGN',
    'callback_url' => null
];
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($postData),
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer {$PAYSTACK_SECRET}",
        "Content-Type: application/json"
    ]
]);
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo json_encode(['status'=>false,'message'=>'cURL Error: '.$err]);
    exit;
}

$resp = json_decode($response, true);
if (!$resp || !$resp['status']) {
    echo json_encode(['status'=>false,'message'=>'Paystack error: '.($resp['message'] ?? 'unknown')]);
    exit;
}

// return the authorization URL & public key & reference to client
echo json_encode(['status'=>true,'data'=>$resp['data'], 'public_key' => $PAYSTACK_PUBLIC]);
exit;