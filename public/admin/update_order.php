<?php
session_start();
require_once '../../inc/db.php';

// =============================
// SECURITY
// =============================
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// =============================
// VALIDATE POST
// =============================
if (!isset($_POST['order_id'], $_POST['status'])) {
    header('Location: admin_orders.php');
    exit;
}

$orderId = intval($_POST['order_id']);
$status = strtolower(trim($_POST['status']));

// Validate allowed statuses
$allowedStatuses = ['pending', 'paid', 'shipped', 'completed', 'cancelled'];
if (!in_array($status, $allowedStatuses)) {
    $_SESSION['error'] = "Invalid status selected.";
    header('Location: admin_orders.php');
    exit;
}

// =============================
// UPDATE ORDER
// =============================
try {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $orderId]);
    $_SESSION['success'] = "Order status updated successfully!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error updating order status: " . $e->getMessage();
}

// Redirect back to admin orders page
header('Location: admin_orders.php');
exit;
