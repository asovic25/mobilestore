<?php
// public/user_products.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../inc/db.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';
if (!isset($_SESSION['user']['id'])) {
    header("Location: user_login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Fetch user's products
$stmt = $pdo->prepare("
    SELECT id, name, price, category_id, status, images, created_at
    FROM user_products
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$user_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories for display
$categoriesStmt = $pdo->query("SELECT id, name FROM categories");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_KEY_PAIR); // id => name
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Products | Rose Store</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* 🌸 Purple Rose Palette B */
:root {
  --rose-deep: #5e2a84;
  --rose-medium: #8e44ad;
  --rose-light: #c67acb;
  --rose-pink: #ffb3d9;
  --rose-gold: #f3c623;
  --rose-bg: #f7f3fa;
  --accent: #E91E63;
  --primary: #6A1B9A;
  --secondary: #F3E5F5;
  --white: #fff;
  --dark: #222;
}

body {
  background: var(--secondary);
  font-family: 'Poppins', sans-serif;
}

/* ===== Navbar ===== */
.navbar {
  background: linear-gradient(90deg, var(--rose-deep), var(--rose-medium));
}
.navbar .navbar-brand, .navbar .nav-link, .navbar span {
  color: var(--white) !important;
}
.navbar .btn-light {
  color: var(--primary);
  font-weight: 600;
}

/* ===== Cards ===== */
h3 {
  color: var(--primary);
  font-weight: 700;
}
.card {
  border-radius: 15px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.1);
  background: var(--white);
  transition: transform 0.3s;
  position: relative;
}
.card:hover {
  transform: translateY(-5px);
}
img.product-img {
  border-radius: 10px;
  object-fit: cover;
}

/* ===== Table ===== */
.table thead {
  background-color: var(--primary);
  color: var(--white);
}
.table-hover tbody tr:hover {
  background-color: rgba(233, 30, 99, 0.1);
}

/* ===== Badges ===== */
.badge-approved { background-color: #28a745; }
.badge-rejected { background-color: #dc3545; }
.badge-pending { background-color: #6c757d; }

/* ===== Buttons ===== */
.btn-rose {
  background-color: var(--primary);
  color: var(--white);
  font-weight: 600;
  transition: all 0.3s;
  border: none;
}
.btn-rose:hover {
  background: linear-gradient(90deg, var(--rose-medium), var(--rose-light));
  color: var(--white);
}

/* ===== Delete Icon (with Tooltip) ===== */
.delete-icon {
  position: absolute;
  top: 10px;
  right: 15px;
  background-color: #8E24AA;
  color: var(--white);
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 16px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  text-decoration: none;
  opacity: 0.9;
  cursor: pointer;
}
.delete-icon:hover {
  background-color: var(--accent);
  transform: scale(1.12);
  opacity: 1;
  box-shadow: 0 6px 14px rgba(233,30,99,0.3);
}

/* Tooltip Styling */
.delete-icon::after {
  content: attr(data-tooltip);
  position: absolute;
  bottom: 120%;
  right: 50%;
  transform: translateX(50%) scale(0.8);
  background-color: rgba(78, 21, 100, 0.95);
  color: var(--white);
  padding: 6px 10px;
  border-radius: 6px;
  font-size: 13px;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: all 0.25s ease-in-out;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Tooltip Arrow */
.delete-icon::before {
  content: '';
  position: absolute;
  bottom: 110%;
  right: 50%;
  transform: translateX(50%);
  border-width: 6px;
  border-style: solid;
  border-color: rgba(78, 21, 100, 0.95) transparent transparent transparent;
  opacity: 0;
  transition: all 0.25s ease-in-out;
}

/* Show Tooltip on Hover */
.delete-icon:hover::after,
.delete-icon:hover::before {
  opacity: 1;
  transform: translateX(50%) scale(1);
}
</style>
</head>

<body>

<!-- 🌸 User Products Section -->
<div class="container py-5">

  <!-- Back to Dashboard Link -->
  <div class="mb-3">
    <a href="user_dashboard.php" class="btn btn-secondary">
      <i class="fa fa-arrow-left"></i> Back to Dashboard
    </a>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fa fa-box"></i> My Products</h3>
    <a href="user_add_product.php" class="btn btn-rose"><i class="fa fa-plus"></i> Add New Product</a>
  </div>

  <?php if(!empty($products)): ?>
    <div class="row g-4">
      <?php foreach($products as $product): 
        // ============= IMAGE SECTION =============
        $raw = $product['images'];
        $imgs = [];
        $trim = trim($raw);
        if ($trim === '') {
            $imgs = [];
        } elseif (($maybe = json_decode($trim, true)) && is_array($maybe)) {
            $imgs = $maybe;
        } else {
            $parts = array_filter(array_map('trim', explode(',', $trim)));
            $imgs = $parts;
        }

        $firstImg = !empty($imgs[0]) ? (string)$imgs[0] : '';
        $firstImg = trim($firstImg);
        if ($firstImg !== '' && !preg_match('#^https?://#i', $firstImg)) {
            $firstImg = preg_replace('#^(\.\./|/)+#', '', $firstImg);
        }

        $imgSrc = 'uploads/products/default.png';
        if ($firstImg !== '' && preg_match('#^https?://#i', $firstImg)) {
            $imgSrc = $firstImg;
        } elseif ($firstImg !== '') {
            $candidates = [
                __DIR__ . '/uploads/products/' . basename($firstImg),
                __DIR__ . '/uploads/' . basename($firstImg),
                __DIR__ . '/' . $firstImg,
            ];

            foreach ($candidates as $idx => $fs) {
                if (file_exists($fs) && is_file($fs)) {
                    if ($idx === 0) {
                        $imgSrc = 'uploads/products/' . rawurlencode(basename($firstImg));
                    } elseif ($idx === 1) {
                        $imgSrc = 'uploads/' . rawurlencode(basename($firstImg));
                    } else {
                        $imgSrc = ltrim($firstImg, './\\');
                    }
                    break;
                }
            }
        }
        // ============= END IMAGE SECTION =============

        $status = strtolower($product['status']);
        $badgeClass = match($status) {
            'approved' => 'badge-approved',
            'rejected' => 'badge-rejected',
            default    => 'badge-pending',
        };
      ?>
      <div class="col-md-6 col-lg-4">
        <div class="card p-3 h-100">
          <img src="<?= htmlspecialchars($imgSrc) ?>" class="w-100 mb-3 product-img" height="180" alt="<?= htmlspecialchars($product['name']) ?>">
          <h5 class="fw-bold"><?= htmlspecialchars($product['name']) ?></h5>
          <p class="mb-1"><strong>Category:</strong> <?= htmlspecialchars($categories[$product['category_id']] ?? 'N/A') ?></p>
          <p class="mb-1"><strong>Price:</strong> ₦<?= number_format((float)$product['price'],2) ?></p>
          <p class="mb-1"><strong>Submitted:</strong> <?= date('d M Y', strtotime($product['created_at'])) ?></p>
          <span class="badge <?= $badgeClass ?> mb-2"><?= ucfirst($status) ?></span>

          <?php if ($status === 'rejected'): ?>
          <a href="user_delete_product.php?id=<?= $product['id'] ?>"
             class="delete-icon"
             onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')"
             data-tooltip="Delete Product">
             <i class="fa fa-trash"></i>
          </a>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="card text-center p-5">
      <h5 class="text-muted mb-3">You haven’t added any products yet.</h5>
      <a href="user_add_product.php" class="btn btn-rose"><i class="fa fa-plus"></i> Add Your First Product</a>
    </div>
  <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
