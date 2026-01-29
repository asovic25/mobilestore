<?php
session_start();
require_once '../inc/db.php';
require_once __DIR__ . '/../inc/config.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: user_login.php");
    exit;
}

$userId = $_SESSION['user']['id'];
$orderId = $_GET['order_id'] ?? 0;

if (!$orderId) {
    header("Location: user_orders.php");
    exit;
}

// Check if order exists and belongs to user
$stmt = $pdo->prepare("SELECT status FROM orders WHERE id = ? AND buyer_id = ?");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    $_SESSION['error'] = "Order not found or you cannot delete it.";
    header("Location: user_orders.php");
    exit;
}

// Only allow delete if cancelled or completed
if (!in_array($order['status'], ['cancelled', 'completed'])) {
    $_SESSION['error'] = "Only cancelled or completed orders can be deleted.";
    header("Location: user_orders.php");
    exit;
}

// Delete the order
$delStmt = $pdo->prepare("DELETE FROM orders WHERE id = ? AND buyer_id = ?");
$delStmt->execute([$orderId, $userId]);

$_SESSION['success'] = "Order deleted successfully.";
header("Location: user_orders.php");
exit;
