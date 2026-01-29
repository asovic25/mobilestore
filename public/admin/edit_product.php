<?php
session_start();
require_once '../../inc/db.php';
require_once __DIR__ . '/../../inc/config.php';
include __DIR__ . '/../../inc/head.php';


// Redirect if no product ID
$id = intval($_GET['id'] ?? 0);
if(!$id){
    header('Location: admin_dashboard.php');
    exit;
}

// Fetch product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$product){
    header('Location: admin_dashboard.php');
    exit;
}

// Fetch categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category_id = intval($_POST['category']);

    // Handle image uploads
    $images = json_decode($product['images'], true) ?: [];
    if(!empty($_FILES['images']['name'][0])){
        foreach($_FILES['images']['tmp_name'] as $i => $tmp){
            $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
            $filename = 'uploads/img_'.uniqid().'.'.$ext;
            move_uploaded_file($tmp, "../../public/".$filename);
            $images[] = $filename;
        }
    }

    // Update product
    $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, category_id=?, images=? WHERE id=?");
    $stmt->execute([$name, $description, $price, $stock, $category_id, json_encode($images), $id]);

    header("Location: admin_dashboard.php?updated=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Product | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
<h3>Edit Product</h3>
<form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label class="form-label">Product Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category" class="form-select">
            <?php foreach($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $c['id']==$product['category_id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Price (₦)</label>
        <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" step="0.01" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Add Images</label>
        <input type="file" name="images[]" class="form-control" multiple>
        <?php 
        $existingImages = json_decode($product['images'], true) ?: [];
        foreach($existingImages as $img): ?>
            <img src="../../public/<?= htmlspecialchars($img) ?>" width="80" class="mt-2 me-2">
        <?php endforeach; ?>
    </div>
    <button class="btn btn-primary">Update Product</button>
    <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
</form>
</div>
</body>
</html>
