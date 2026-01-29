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
    $_SESSION['error'] = "Order not found or you cannot cancel it.";
    header("Location: user_orders.php");
    exit;
}

// Only allow cancellation if not already cancelled or completed
if (in_array($order['status'], ['cancelled', 'completed'])) {
    $_SESSION['error'] = "This order cannot be cancelled.";
    header("Location: user_orders.php");
    exit;
}

// Update order status to cancelled
$update = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND buyer_id = ?");
$update->execute([$orderId, $userId]);

// Optionally, add a log entry
$log = $pdo->prepare("INSERT INTO order_status_log (order_id, status, note, created_at) VALUES (?, 'cancelled', 'User requested cancellation', NOW())");
$log->execute([$orderId]);

$_SESSION['success'] = "Order cancelled successfully.";
header("Location: user_orders.php");
exit;
