<?php
require_once '../../inc/db.php';
require_once __DIR__ . '/../../inc/config.php';
include __DIR__ . '/../../inc/head.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $stock = intval($_POST['stock']);
    $stmt = $pdo->prepare("UPDATE user_products SET stock = ? WHERE id = ?");
    $stmt->execute([$stock, $id]);
    header("Location: admin_dashboard.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
$product = null;
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM user_products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm p-4 mx-auto" style="max-width:500px;">
        <h4 class="mb-3 text-center">Edit Stock</h4>
        <?php if ($product): ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">
            <div class="mb-3">
                <label class="form-label">Product</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Stock Quantity</label>
                <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?>" required>
            </div>
            <button class="btn btn-primary w-100">Update Stock</button>
        </form>
        <?php else: ?>
            <p class="text-danger text-center">Invalid product ID.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
