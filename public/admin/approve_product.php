<?php
session_start();
require_once '../../inc/db.php';
require_once __DIR__ . '/../../inc/config.php';
include __DIR__ . '/../../inc/head.php';


$id = intval($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if (!$id || !$action) {
    header('Location: admin_dashboard.php');
    exit;
}

// Fetch the product from user_products
$stmt = $pdo->prepare("SELECT * FROM user_products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: admin_dashboard.php');
    exit;
}

if ($action === 'approve') {
    // ✅ 1) Update status in user_products
    $upd = $pdo->prepare("UPDATE user_products SET status = 'approved' WHERE id = ?");
    $upd->execute([$id]);

    // ✅ 2) Check if product already exists in products table
    $check = $pdo->prepare("SELECT id FROM products WHERE name = ? AND user_id = ?");
    $check->execute([$product['name'], $product['user_id']]);
    $existing = $check->fetch();

    if (!$existing) {
    // ✅ Determine stock value safely
    $stockValue = isset($product['quantity']) && $product['quantity'] > 0 
        ? (int) $product['quantity'] : 1; // Default 1 if none specified

    $ins = $pdo->prepare("
        INSERT INTO products (user_id, category_id, name, description, images, price, stock, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $ins->execute([
        $product['user_id'],
        $product['category_id'],
        $product['name'],
        $product['description'],
        $product['images'],
        $product['price'],
        $stockValue
    ]);
}

    // ✅ 4) Optional: Remove from user_products after approval to prevent duplicates
    $del = $pdo->prepare("DELETE FROM user_products WHERE id = ?");
    $del->execute([$id]);

} elseif ($action === 'reject') {
    // 🚫 If rejected, just mark as rejected
    $upd = $pdo->prepare("UPDATE user_products SET status = 'rejected' WHERE id = ?");
    $upd->execute([$id]);
}

// ✅ Redirect back
header('Location: admin_dashboard.php');
exit;
?>
