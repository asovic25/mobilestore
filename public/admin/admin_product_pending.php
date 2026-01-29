<?php
session_start();
require_once '../../inc/db.php'; // admin folder is one level above 'inc'
require_once __DIR__ . '/../../inc/config.php';
include __DIR__ . '/../../inc/head.php';


// ==============================
// Fetch pending user-submitted products
// ==============================
$pendingProducts = $pdo->query("
    SELECT up.*, c.name AS category_name, u.username 
    FROM user_products up
    JOIN categories c ON up.category_id = c.id
    JOIN users u ON up.user_id = u.id
    WHERE up.status = 'pending'
    ORDER BY up.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ==============================
// Image Normalizer (robust)
// ==============================
function normalize_images_for_admin($imagesJson) {
    $imgs = [];

    if (!empty($imagesJson)) {
        $decoded = json_decode($imagesJson, true);
        if (is_array($decoded)) $imgs = $decoded;
    }

    $imgs = array_map(function($img) {
        $img = trim($img);

        // just keep filename if path exists
        $img = basename($img);

        // full path relative to admin folder
        $fullPath = "../uploads/" . $img;

        // check if file exists, else use default
        if (!file_exists(__DIR__ . "/../uploads/" . $img)) {
            return "../uploads/no-image.png";
        }

        return $fullPath;
    }, $imgs);

    return $imgs ?: ["../uploads/no-image.png"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pending Products | Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .card img { height: 220px; object-fit: cover; width: 100%; }
    .card { border: none; border-radius: 10px; overflow: hidden; }
    .card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-color: rgba(0,0,0,0.3);
        border-radius: 50%;
    }
  </style>
</head>
<body class="bg-light p-4">

<div class="container">
  <h3 class="mb-4 text-center text-primary">Pending Product Approvals</h3>

  <div class="row g-4">
    <?php if (empty($pendingProducts)): ?>
      <div class="col-12 text-center">
        <div class="alert alert-info">No pending products at the moment.</div>
      </div>
    <?php endif; ?>

    <?php foreach ($pendingProducts as $product): ?>
      <?php $images = normalize_images_for_admin($product['images']); ?>
      <div class="col-md-4">
        <div class="card shadow-sm bg-white">
          <!-- Image Carousel -->
          <?php if (!empty($images)): ?>
            <div id="carousel<?= $product['id'] ?>" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                <?php foreach ($images as $index => $imgPath): ?>
                  <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <img src="<?= htmlspecialchars($imgPath) ?>" class="d-block w-100" alt="Product Image">
                  </div>
                <?php endforeach; ?>
              </div>
              <?php if (count($images) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?= $product['id'] ?>" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carousel<?= $product['id'] ?>" data-bs-slide="next">
                  <span class="carousel-control-next-icon"></span>
                </button>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <!-- Product Info -->
          <div class="card-body">
            <h5 class="fw-bold text-dark"><?= htmlspecialchars($product['name']) ?></h5>
            <p class="text-muted small mb-1"><?= htmlspecialchars($product['category_name']) ?> • Uploaded by <?= htmlspecialchars($product['username']) ?></p>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <p><strong>Price:</strong> ₦<?= number_format($product['price'], 2) ?></p>
            <p><strong>Stock:</strong> <?= htmlspecialchars($product['stock']) ?></p>

            <div class="d-flex justify-content-between mt-3">
              <a href="approve_product.php?id=<?= $product['id'] ?>&action=approve" class="btn btn-success btn-sm">Approve</a>
              <a href="approve_product.php?id=<?= $product['id'] ?>&action=reject" class="btn btn-danger btn-sm">Reject</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
