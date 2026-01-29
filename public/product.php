<?php
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';

$slug = $_GET['slug'] ?? '';
$product = fetchProductBySlug($pdo, $slug);
if (!$product) {
    http_response_code(404);
    echo "Product not found";
    exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo h($product['name']); ?> -Rose Mobile Store</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <a href="index.php" class="btn btn-link">&larr; Back to shop</a>
  <div class="row g-4">
    <div class="col-md-5">
      <img src="<?php echo h($product['image'] ?: 'images/mobile3.png'); ?>" class="img-fluid" alt="">
    </div>
    <div class="col-md-7">
      <h2><?php echo h($product['name']); ?></h2>
      <p class="text-muted"><?php echo h($product['description']); ?></p>
      <p class="fw-bold">₦<?php echo number_format($product['price'],2); ?></p>
      <div class="mb-3">
        <label for="qty" class="form-label">Quantity</label>
        <input id="qty" class="form-control" type="number" min="1" value="1" style="max-width:110px;">
      </div>
      <button class="btn btn-primary add-to-cart" data-id="<?php echo $product['id']; ?>" data-name="<?php echo h($product['name']); ?>" data-price="<?php echo $product['price']; ?>">Add to Cart</button>
      <a href="cart.html" class="btn btn-outline-secondary ms-2">Go to Cart</a>
    </div>
  </div>
</div>
<script src="assets/js/cart.js"></script>
</body>
</html>