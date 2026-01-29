<?php
session_start();
require_once '../../inc/db.php';
require_once __DIR__ . '/../../inc/config.php';
include __DIR__ . '/../../inc/head.php';


$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = floatval($_POST['price']);
    $stock       = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);
    $admin_id    = 0;

    $uploadDir = dirname(__DIR__, 2) . '/public/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $uploadedImages = [];
    for ($i=1; $i<=3; $i++) {
        $field = 'image'.$i;
        if (!empty($_FILES[$field]['name'])) {
            $tmpName = $_FILES[$field]['tmp_name'];
            $fileName = uniqid('img_') . '_' . basename($_FILES[$field]['name']);
            $targetFile = $uploadDir . $fileName;
            if (move_uploaded_file($tmpName, $targetFile)) {
                $uploadedImages[] = 'uploads/' . $fileName;
            }
        }
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO products 
            (user_id, category_id, name, description, price, stock, images, created_at)
            VALUES (:user_id, :category_id, :name, :description, :price, :stock, :images, NOW())
        ");
        $stmt->execute([
            ':user_id'=>$admin_id,
            ':category_id'=>$category_id,
            ':name'=>$name,
            ':description'=>$description,
            ':price'=>$price,
            ':stock'=>$stock,
            ':images'=>json_encode($uploadedImages)
        ]);
        $success = "✅ Product added successfully!";
    } catch (PDOException $e) {
        $error = "❌ Error adding product: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Product | Admin Panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
:root {
    --navy:#0A1A2F;
    --navy-light:#132A4F;
    --accent:#1F4ED8;
    --bg:#F4F6FA;
}
body { background: var(--bg); }

/* Sidebar */
.sidebar {
    width: 260px;
    min-height: 100vh;
    background: var(--navy);
    color: #fff;
    padding: 2rem 1rem;
    position: fixed;
    top: 0;
    left: -100%;
    transition: 0.3s;
    z-index: 999;
}
.sidebar.show { left: 0; }
.sidebar h4, .sidebar p { color: #fff; }
.sidebar a { color: #cfd8ff; padding: 12px 16px; display:block; text-decoration:none; border-radius:8px; margin-bottom:4px; }
.sidebar a:hover, .sidebar a.active { background: var(--accent); color:#fff; }

#overlay {
    position: fixed; top:0; left:0; width:100%; height:100%;
    background: rgba(0,0,0,0.4);
    opacity:0; visibility:hidden;
    transition: 0.3s; z-index:998;
}
.sidebar.show + #overlay { opacity:1; visibility:visible; }

/* Desktop fix */
@media(min-width:992px) {
    .sidebar { left:0; position:relative; }
    #sidebarToggle, #overlay { display:none; }
}

/* Hamburger */
#sidebarToggle { position: fixed; top:10px; left:10px; z-index:1000; }

/* Form Card */
.form-card { max-width:900px; margin:auto; }

/* Footer */
footer { background: var(--navy); color:#fff; text-align:center; padding:12px 0; margin-top:20px; }
</style>
</head>
<body>

<!-- Hamburger -->
<button class="btn btn-primary d-lg-none" id="sidebarToggle"><i class="fas fa-bars"></i></button>
<div id="overlay"></div>

<div class="d-flex">
    <!-- Sidebar -->
    <aside class="sidebar">
        <h4 class="fw-bold mb-1">RoseStore</h4>
        <p class="small">Admin Panel</p>
        <hr>
        <a href="admin_dashboard.php"><i class="fas fa-gauge me-2"></i> Dashboard</a>
        <a href="admin_add_product.php" class="active"><i class="fas fa-plus me-2"></i> Add Product</a>
        <a href="add_category.php"><i class="fas fa-tags me-2"></i> Add Category</a>
        <a href="admin_products.php"><i class="fas fa-boxes me-2"></i> Products</a>
        <a href="admin_orders.php"><i class="fas fa-cart-shopping me-2"></i> Orders</a>
        <a href="../index.php" target="_blank"><i class="fas fa-store me-2"></i> View Store</a>
        <a href="../logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </aside>

    <!-- Main -->
    <main class="flex-fill p-4">
        <div class="container">
            <div class="mb-4">
                <h4 class="fw-bold">Add New Product</h4>
                <small class="text-muted">Add products directly to your store</small>
            </div>

            <?php if(!empty($success)): ?>
                <div class="alert alert-success shadow-sm"><?= htmlspecialchars($success) ?></div>
            <?php elseif(!empty($error)): ?>
                <div class="alert alert-danger shadow-sm"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm form-card p-4">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Price (₦)</label>
                            <input type="number" name="price" step="0.01" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Stock Quantity</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <?php for($i=1; $i<=3; $i++): ?>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Image <?= $i ?></label>
                            <input type="file" name="image<?= $i ?>" class="form-control" accept="image/*">
                        </div>
                        <?php endfor; ?>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i> Add Product</button>
                        <a href="admin_dashboard.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Back</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<footer>© <?= date('Y') ?> RoseStore Admin Panel</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar = document.querySelector('.sidebar');
const toggleBtn = document.getElementById('sidebarToggle');
const overlay = document.getElementById('overlay');

toggleBtn.addEventListener('click', ()=>sidebar.classList.toggle('show'));
overlay.addEventListener('click', ()=>sidebar.classList.remove('show'));
document.querySelectorAll('.sidebar a').forEach(link=>{
    link.addEventListener('click', ()=>{
        if(window.innerWidth<992) sidebar.classList.remove('show');
    });
});
</script>
</body>
</html>
