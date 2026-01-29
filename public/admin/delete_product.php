<?php
session_start();
require_once '../../inc/db.php';
require_once __DIR__ . '/../../inc/config.php';
include __DIR__ . '/../../inc/head.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: admin_dashboard.php");
    exit;
}

// Determine table automatically
// Admin products table = 'products'
// User submissions table = 'user_products'
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
$table = 'products';

if (!$product) {
    $stmt = $pdo->prepare("SELECT * FROM user_products WHERE id=?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    $table = 'user_products';
}

if ($product) {
    $images = json_decode($product['images'], true);
    if (is_array($images)) {
        foreach ($images as $img) {
            $path = __DIR__ . '/../../public/' . $img;
            if (file_exists($path)) unlink($path);
        }
    }

    // Delete record
    $del = $pdo->prepare("DELETE FROM $table WHERE id=?");
    $del->execute([$id]);
}

header("Location: admin_dashboard.php?deleted=1");
exit;
