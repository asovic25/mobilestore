<?php
require_once '../../inc/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];
    $status  = $_POST['status'];
    $note    = $_POST['note'] ?? null;

    // Update main orders table
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $orderId]);

    // Log history
    $log = $pdo->prepare("
        INSERT INTO order_status_log (order_id, status, note)
        VALUES (?, ?, ?)
    ");
    $log->execute([$orderId, $status, $note]);

    header("Location: orders_manage.php?success=updated");
}
