<?php
session_start();
require_once '../../inc/db.php';
require_once __DIR__ . '/../../inc/config.php';
include __DIR__ . '/../../inc/head.php';
require_once __DIR__ . '/../../inc/bootstrap.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$adminProducts = $pdo->query("
    SELECT p.*, c.name AS category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$userProducts = $pdo->query("
    SELECT up.*, c.name AS category_name, u.username 
    FROM user_products up
    JOIN categories c ON up.category_id = c.id
    JOIN users u ON up.user_id = u.id
    ORDER BY up.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

function getFirstImage($imagesJson) {
    $images = json_decode($imagesJson, true);
    $firstImage = $images[0] ?? $imagesJson ?? '../uploads/default.png';
    return (!str_starts_with($firstImage,'../') && !str_starts_with($firstImage,'./')) ? '../'.$firstImage : $firstImage;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin | All Products</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
:root {
    --navy: #0A1D37;
    --navy-light: #102E4A;
    --accent: #1E90FF;
    --bg: #F4F6F9;
}
body { background: var(--bg); }

/* Sidebar */
.sidebar {
    width: 240px;
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
.sidebar h4, .sidebar small { color: #fff; }
.sidebar a {
    color: #cfd8ff;
    display: block;
    padding: 10px 14px;
    margin-bottom: 4px;
    border-radius: 6px;
    text-decoration: none;
    transition: 0.2s;
}
.sidebar a:hover, .sidebar a.active { background: var(--accent); color: #fff; }

#overlay {
    position: fixed;
    top:0; left:0;
    width:100%; height:100%;
    background: rgba(0,0,0,0.4);
    opacity:0; visibility:hidden;
    transition: 0.3s;
    z-index: 998;
}
.sidebar.show + #overlay { opacity:1; visibility:visible; }

/* Desktop fix */
@media(min-width:992px){
    .sidebar { left:0; position:relative; }
    #sidebarToggle, #overlay { display:none; }
}

/* Hamburger */
#sidebarToggle {
    position: fixed;
    top: 10px; left:10px;
    z-index:1000;
}

/* Cards & Thumbnails */
.card { border-radius:12px; }
.card-icon{ font-size:2rem; opacity:.3; }
.thumbnail { width: 75px; height: 75px; object-fit: cover; border-radius:6px; border:1px solid #dee2e6; }
.table th, .table td { vertical-align: middle !important; }

footer { background: var(--navy); color:#cfd8ff; padding:12px 0; text-align:center; }
</style>
</head>
<body>

<!-- Hamburger -->
<button class="btn btn-primary d-lg-none" id="sidebarToggle"><i class="fas fa-bars"></i></button>
<div id="overlay"></div>

<div class="d-flex">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="text-center mb-4">
            <h4 class="fw-bold">MobileStore</h4>
            <small class="text-light">Admin Panel</small>
        </div>
        <nav class="nav flex-column gap-1">
            <a class="nav-link" href="admin_dashboard.php"><i class="fa-solid fa-chart-line me-2"></i>Dashboard</a>
            <a class="nav-link" href="admin_add_product.php"><i class="fa-solid fa-plus me-2"></i>Add Product</a>
            <a class="nav-link active" href="admin_products.php"><i class="fa-solid fa-boxes-stacked me-2"></i>All Products</a>
            <a class="nav-link" href="add_category.php"><i class="fa-solid fa-folder-plus me-2"></i>Add Category</a>
            <a class="nav-link" href="manage_category.php"><i class="fa-solid fa-list me-2"></i>Manage Categories</a>
            <hr class="text-light">
            <a class="nav-link text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
        </nav>
    </aside>

    <!-- MAIN -->
    <main class="flex-fill p-4">
        <h3 class="fw-bold text-primary mb-4">Products Management</h3>

        <!-- ADMIN PRODUCTS -->
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-primary text-white fw-bold">Admin Products</div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($adminProducts as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><img src="<?= getFirstImage($p['images']) ?>" class="thumbnail"></td>
                            <td class="fw-semibold"><?= htmlspecialchars($p['name']) ?></td>
                            <td><?= htmlspecialchars($p['category_name']) ?></td>
                            <td>₦<?= number_format($p['price'], 2) ?></td>
                            <td><?= (int)$p['stock'] ?></td>
                            <td class="text-center">
                                <a href="view_product.php?id=<?= $p['id'] ?>&source=admin" class="btn btn-sm btn-info">View</a>
                                <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="delete_product.php?id=<?= $p['id'] ?>&source=admin" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- USER PRODUCTS -->
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white fw-bold">User Submitted Products</div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th><th>Image</th><th>Name</th><th>Category</th><th>User</th><th>Price</th><th>Stock</th><th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userProducts as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><img src="<?= getFirstImage($p['images']) ?>" class="thumbnail"></td>
                            <td class="fw-semibold"><?= htmlspecialchars($p['name']) ?></td>
                            <td><?= htmlspecialchars($p['category_name']) ?></td>
                            <td><?= htmlspecialchars($p['username']) ?></td>
                            <td>₦<?= number_format($p['price'], 2) ?></td>
                            <td><?= (int)$p['stock'] ?></td>
                            <td class="text-center">
                                <a href="view_product.php?id=<?= $p['id'] ?>&source=user" class="btn btn-sm btn-info">View</a>
                                <a href="delete_product.php?id=<?= $p['id'] ?>&source=user" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user product?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<footer>© <?= date('Y') ?> MobileStore • Admin Panel</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar = document.querySelector('.sidebar');
const toggleBtn = document.getElementById('sidebarToggle');
const overlay = document.getElementById('overlay');

toggleBtn.addEventListener('click', () => { sidebar.classList.toggle('show'); });
overlay.addEventListener('click', () => { sidebar.classList.remove('show'); });

document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', () => {
        if(window.innerWidth < 992) sidebar.classList.remove('show');
    });
});
</script>
</body>
</html>
