<?php
session_start();
require_once '../../inc/db.php';
require_once __DIR__ . '/../../inc/config.php';
include __DIR__ . '/../../inc/head.php';


$id = intval($_GET['id'] ?? 0);
$source = $_GET['source'] ?? 'admin';

if ($source === 'admin') {
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id=c.id WHERE p.id=?");
} else {
    $stmt = $pdo->prepare("SELECT up.*, c.name AS category_name, u.username FROM user_products up JOIN categories c ON up.category_id=c.id JOIN users u ON up.user_id=u.id WHERE up.id=?");
}
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) die("Product not found.");

$images = json_decode($product['images'], true) ?: ['uploads/default.png'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Product</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.product-img { width: 100%; height: 400px; object-fit: cover; }
</style>
</head>
<body>
<div class="container py-5">
    <h3><?= htmlspecialchars($product['name']) ?></h3>
    <p>Category: <?= htmlspecialchars($product['category_name']) ?></p>
    <?php if($source==='user'): ?>
        <p>Submitted by: <?= htmlspecialchars($product['username']) ?></p>
    <?php endif; ?>
    <p>Price: ₦<?= number_format($product['price'],2) ?></p>
    <p>Stock: <?= (int)$product['stock'] ?></p>
    <p>Description: <?= nl2br(htmlspecialchars($product['description'])) ?></p>

    <?php if(count($images) > 1): ?>
    <div id="carouselProduct" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php foreach($images as $i => $img): ?>
            <div class="carousel-item <?= $i===0 ? 'active' : '' ?>">
                <img src="../../public/<?= htmlspecialchars($img) ?>" class="d-block w-100 product-img" alt="">
            </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselProduct" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselProduct" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
    <?php else: ?>
        <img src="../../public/<?= htmlspecialchars($images[0]) ?>" class="product-img mb-4" alt="">
    <?php endif; ?>

    <a href="admin_products.php" class="btn btn-secondary">Back</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
