<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/../../inc/db.php';
require_once __DIR__ . '/../../inc/functions.php';
require_once __DIR__ . '/../../inc/config.php';
include __DIR__ . '/../../inc/head.php';


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

// Delete category
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: manage_category.php');
    exit;
}

// Edit category
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $new_name = trim($_POST['new_name']);

    if ($new_name !== '') {
        $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$new_name, $id]);
        $message = '<div class="alert alert-success shadow-sm">✅ Category updated successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger shadow-sm">❌ Category name cannot be empty!</div>';
    }
}

// Fetch categories + product count
$sql = "SELECT c.*, COUNT(p.id) AS product_count
        FROM categories c
        LEFT JOIN products p ON p.category_id = c.id
        GROUP BY c.id
        ORDER BY c.id DESC";
$categories = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Categories | Admin Panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
:root{
  --navy:#0A1A2F;
  --navy-light:#132A4F;
  --accent:#1F4ED8;
  --bg:#F4F6FA;
}

body{ background:var(--bg); min-height:100vh; }

/* Sidebar */
.sidebar{
  width:260px;
  min-height:100vh;
  background:var(--navy);
  color:#fff;
  padding:2rem 1rem;
  position: fixed;
  top:0;
  left:-100%;
  transition:0.3s;
  z-index:999;
}
.sidebar.show { left:0; }
.sidebar a{ color:#cfd8ff; padding:12px 16px; display:block; text-decoration:none; border-radius:8px; margin-bottom:4px; }
.sidebar a:hover, .sidebar a.active{ background:var(--accent); color:#fff; }

/* Overlay */
#overlay{
  position:fixed; top:0; left:0; width:100%; height:100%;
  background:rgba(0,0,0,0.4); opacity:0; visibility:hidden;
  transition:0.3s; z-index:998;
}
.sidebar.show + #overlay { opacity:1; visibility:visible; }

/* Desktop fix */
@media(min-width:992px){
  .sidebar{ left:0; position:relative; }
  #sidebarToggle, #overlay{ display:none; }
}

/* Hamburger */
#sidebarToggle{ position:fixed; top:10px; left:10px; z-index:1000; }

/* Table */
.table td, .table th{ vertical-align:middle; }

/* Footer */
footer{ background: var(--navy); color:#fff; text-align:center; padding:12px 0; margin-top:20px; }
</style>
</head>

<body>

<!-- Hamburger -->
<button class="btn btn-primary d-lg-none" id="sidebarToggle"><i class="fas fa-bars"></i></button>
<div id="overlay"></div>

<div class="d-flex">
  <!-- Sidebar -->
  <aside class="sidebar p-4">
    <h4 class="fw-bold mb-1">MobileStore</h4>
    <p class="small mb-3">Admin Panel</p>
    <hr>
    <a href="admin_dashboard.php"><i class="fas fa-gauge me-2"></i> Dashboard</a>
    <a href="admin_add_product.php"><i class="fas fa-plus me-2"></i> Add Product</a>
    <a href="add_category.php"><i class="fas fa-tags me-2"></i> Add Category</a>
    <a href="manage_category.php" class="active"><i class="fas fa-list me-2"></i> Manage Categories</a>
    <a href="admin_products.php"><i class="fas fa-boxes me-2"></i> Products</a>
    <a href="admin_orders.php"><i class="fas fa-cart-shopping me-2"></i> Orders</a>
    <a href="../index.php" target="_blank"><i class="fas fa-store me-2"></i> View Store</a>
    <a href="../logout.php" class="text-danger"><i class="fas fa-right-from-bracket me-2"></i> Logout</a>
  </aside>

  <!-- Main -->
  <main class="flex-fill p-4">
    <div class="container-fluid">

      <div class="mb-4">
        <h4 class="fw-bold">Manage Categories</h4>
        <small class="text-muted">Edit, update or delete product categories</small>
      </div>

      <?= $message ?>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Category List</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Category Name</th>
                  <th>Products</th>
                  <th width="300">Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php if(count($categories)===0): ?>
                <tr><td colspan="4" class="text-center py-4 text-muted">No categories found</td></tr>
              <?php endif; ?>
              <?php foreach($categories as $i=>$cat): ?>
                <tr>
                  <td><?= $i+1 ?></td>
                  <td class="fw-semibold"><?= htmlspecialchars($cat['name']) ?></td>
                  <td><span class="badge bg-success"><?= $cat['product_count'] ?></span></td>
                  <td>
                    <form method="POST" class="d-flex flex-wrap gap-2">
                      <input type="hidden" name="edit_id" value="<?= $cat['id'] ?>">
                      <input type="text" name="new_name" class="form-control form-control-sm" placeholder="Rename category" required>
                      <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i></button>
                      <a href="?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category?')"><i class="fas fa-trash"></i></a>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>

<footer class="text-center py-3 mt-4">© <?= date('Y') ?> MobileStore — Admin Panel</footer>

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
