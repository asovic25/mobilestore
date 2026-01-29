<?php
// public/seller_add_product.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';

// Require seller
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'seller') {
    header('Location: user_login.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $image = $_FILES['image'] ?? null;

    if ($name === '') $errors[] = 'Product name required.';
    if ($price <= 0) $errors[] = 'Price must be > 0.';

    if (empty($errors)) {
        // handle image
        $image_name = null;
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
            $allowed = ['png','jpg','jpeg','webp'];
            if (!in_array($ext, $allowed)) $errors[] = 'Image type not allowed.';
            else {
                $uploadDir = dirname(__DIR__) . '/uploads/products/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $image_name = 'p_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                move_uploaded_file($image['tmp_name'], $uploadDir . $image_name);
            }
        }

        if (empty($errors)) {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/','-', $name)) . '-' . time();
            $stmt = $pdo->prepare("INSERT INTO products (seller_id, name, slug, category_id, price, stock, description, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $name, $slug, $category_id ?: null, $price, $stock, $description, $image_name]);
            $success = 'Product added successfully.';
        }
    }
}

// get categories for select
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html>
<head><meta charset="utf-8"><title>Add Product</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container py-4">
  <h3>Add Product</h3>
  <?php if ($errors) echo '<div class="alert alert-danger"><ul><li>'.implode('</li><li>', array_map('htmlspecialchars', $errors)).'</li></ul></div>'; ?>
  <?php if ($success) echo '<div class="alert alert-success">'.htmlspecialchars($success).'</div>'; ?>
  <form method="post" enctype="multipart/form-data">
    <div class="mb-3"><label>Name</label><input name="name" class="form-control" required></div>
    <div class="mb-3"><label>Category</label>
      <select name="category_id" class="form-select">
        <option value="">Choose category</option>
        <?php foreach($categories as $c): ?>
          <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="row">
      <div class="col-md-4 mb-3"><label>Price</label><input type="number" step="0.01" name="price" class="form-control" required></div>
      <div class="col-md-4 mb-3"><label>Stock</label><input type="number" name="stock" class="form-control" required></div>
      <div class="col-md-4 mb-3"><label>Image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
    </div>
    <div class="mb-3"><label>Description</label><textarea name="description" class="form-control" rows="4"></textarea></div>
    <button class="btn btn-success">Add Product</button>
  </form>
</div>
</body>
</html>
