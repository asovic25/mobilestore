<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../inc/db.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: user_login.php");
    exit;
}

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $user_id = $_SESSION['user']['id'];

    $stmt = $pdo->prepare("DELETE FROM user_products WHERE id = ? AND user_id = ? AND status = 'rejected'");
    $stmt->execute([$product_id, $user_id]);
}

header("Location: user_products.php");
exit;
?>
