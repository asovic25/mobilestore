<?php
session_start();
require_once '../inc/db.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';

if (!isset($_SESSION['user'])) {
    header('Location: user_login.php');
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['id'];
$avatarPath = 'uploads/avatars/' . ($user['avatar'] ?? 'default.png');
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = floatval($_POST['price']);
    $stock       = intval($_POST['stock']);
    $category_id = intval($_POST['category_id'] ?? 0);

    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $uploadedImages = [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $i => $filename) {
            $tmpName = $_FILES['images']['tmp_name'][$i];
            if (!$tmpName) continue;
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $fileName = uniqid('img_', true) . '.' . strtolower($ext);
            $targetFile = $uploadDir . $fileName;
            if (move_uploaded_file($tmpName, $targetFile)) {
                $uploadedImages[] = 'uploads/' . $fileName;
            }
        }
    }
    $imagesJson = json_encode($uploadedImages, JSON_UNESCAPED_SLASHES);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO user_products 
            (user_id, category_id, name, description, price, stock, images, status, created_at)
            VALUES (:user_id, :category_id, :name, :description, :price, :stock, :images, 'pending', NOW())
        ");
        $stmt->execute([
            ':user_id' => $user_id,
            ':category_id' => $category_id,
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':stock' => $stock,
            ':images' => $imagesJson
        ]);
        $success = "✅ Product submitted successfully for admin approval!";
    } catch (PDOException $e) {
        $error = "❌ Error adding product: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Product | Rose Store</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root {
    --primary:#6A1B9A;
    --accent:#E91E63;
    --secondary:#F3E5F5;
    --white:#fff;
}
body { background-color: var(--secondary); font-family: 'Poppins', sans-serif; margin-bottom:0; }
.navbar { background-color: var(--primary) !important; }
.navbar .navbar-brand, .navbar .nav-link, .navbar span { color: var(--white) !important; }
.navbar .btn-light { color: var(--primary); font-weight: 600; }

h2 { color: var(--primary); font-weight: 700; margin-bottom: 30px; text-align: center; }
.card { border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); background: var(--white); transition: transform 0.3s; padding: 30px; }
.card:hover { transform: translateY(-3px); }
.btn-rose { background-color: var(--primary); color: var(--white); font-weight: 600; transition: all 0.3s; }
.btn-rose:hover { background-color: #5A137F; color: var(--accent); }

input, select, textarea { border-radius: 8px; }
.product-img-preview { width: 80px; height: 80px; object-fit: cover; margin-right: 10px; border-radius: 5px; }

.image-preview-container { display:flex; gap:10px; margin-top:10px; }
</style>
</head>
<body>

<?php include __DIR__ . '/../inc/header.php'; ?>

<div class="container mt-5 mb-5" style="max-width:700px;">
    <h2>Add New Product</h2>

    <?php if(!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif(!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="card mx-auto">
        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="name" required class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Price (₦)</label>
            <input type="number" name="price" step="0.01" required class="form-control" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" required class="form-control" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Stock Quantity</label>
            <input type="number" name="stock" min="0" required class="form-control" value="<?= htmlspecialchars($_POST['stock'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Upload Images (max 3)</label>
            <input type="file" name="images[]" accept="image/*" multiple class="form-control" id="productImages">
            <div class="image-preview-container" id="imagePreview"></div>
        </div>

        <button type="submit" class="btn btn-rose w-100"><i class="fa fa-plus"></i> Submit Product</button>
    </form>
</div>

<?php include __DIR__ . '/../inc/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Preview uploaded images
const productImages = document.getElementById('productImages');
const imagePreview = document.getElementById('imagePreview');

productImages.addEventListener('change', function() {
    imagePreview.innerHTML = '';
    const files = Array.from(this.files).slice(0,3);
    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'product-img-preview';
            imagePreview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});
</script>

</body>
</html>
