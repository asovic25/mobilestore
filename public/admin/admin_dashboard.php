<?php
session_start();
require_once '../../inc/db.php';
require_once __DIR__ . '/../../inc/config.php';
include __DIR__ . '/../../inc/head.php';
require_once __DIR__ . '/../../inc/bootstrap.php';
/* ✅ Admin Name */
$adminName = $_SESSION['admin_name'] ?? 'Administrator';

/* ==============================
// Stats
================================ */
$totalProducts = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$pendingCount  = (int)$pdo->query("SELECT COUNT(*) FROM user_products WHERE status='pending'")->fetchColumn();
$approvedCount = (int)$pdo->query("SELECT COUNT(*) FROM user_products WHERE status='approved'")->fetchColumn();
$rejectedCount = (int)$pdo->query("SELECT COUNT(*) FROM user_products WHERE status='rejected'")->fetchColumn();

/* ==============================
// Latest Products
================================ */
$recentPending = $pdo->query("
 SELECT up.*,u.username,c.name AS category_name
 FROM user_products up
 LEFT JOIN users u ON up.user_id=u.id
 LEFT JOIN categories c ON up.category_id=c.id
 WHERE up.status='pending'
 ORDER BY up.created_at DESC LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

$liveProducts = $pdo->query("
 SELECT p.*,u.username,c.name AS category_name
 FROM products p
 LEFT JOIN users u ON p.user_id=u.id
 LEFT JOIN categories c ON p.category_id=c.id
 ORDER BY p.created_at DESC LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

$allProducts = $pdo->query("
 SELECT up.*,u.username,c.name AS category_name
 FROM user_products up
 LEFT JOIN users u ON up.user_id=u.id
 LEFT JOIN categories c ON up.category_id=c.id
 ORDER BY up.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

/* ==============================
// Image Helper
================================ */
function normalize_images($img) {
    $filename = basename($img);
    return ["../uploads/" . ($filename ?: 'no-image.png')];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | RoseStore</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
:root{
  --admin-primary:#0A1D37;
  --admin-secondary:#102E4A;
  --admin-accent:#1E90FF;
  --admin-bg:#F4F6F9;
}
body{ background:var(--admin-bg); }

/* Sidebar */
.sidebar{
  width:260px;
  background:var(--admin-primary);
  min-height:100vh;
  color:#fff;
  padding:2rem 1rem;
  position:fixed;
  top:0;
  left:0;
  transform:translateX(-100%);
  transition: transform 0.3s;
  z-index:999;
}
.sidebar.show{
  transform:translateX(0);
}
.sidebar a{
  color:#cfd8ff;
  padding:12px 16px;
  display:block;
  border-radius:8px;
  text-decoration:none;
  margin-bottom:4px;
}
.sidebar a:hover,.sidebar a.active{
  background:var(--admin-accent);
  color:#fff;
}
.sidebar h4, .sidebar p{color:#fff;}
#overlay{
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background:rgba(0,0,0,0.4);
  opacity:0;
  visibility:hidden;
  transition:all 0.3s;
  z-index:998;
}
.sidebar.show + #overlay{
  opacity:1;
  visibility:visible;
}

/* Hide toggle on desktop */
#sidebarToggle{
  position:fixed;
  top:10px;
  left:10px;
  z-index:1000;
}
@media(min-width:992px){
  .sidebar{
    transform:translateX(0);
    position:relative;
  }
  #sidebarToggle, #overlay{display:none;}
}

/* Cards */
.card-icon{
  font-size:2rem;
  opacity:.3;
}
.img-thumb{
  height:140px;
  object-fit:cover;
}
footer{
  background:var(--admin-primary);
  color:#fff;
  text-align:center;
  padding:10px;
}
</style>
</head>

<body>

<!-- Hamburger Button & Overlay -->
<button class="btn btn-primary d-lg-none p-2" id="sidebarToggle">
  <i class="fas fa-bars"></i>
</button>
<div id="overlay"></div>

<div class="d-flex">

<!-- SIDEBAR -->
<aside class="sidebar">
  <h4 class="fw-bold">RoseStore</h4>
  <p class="small">Admin Panel</p>
  <hr>
  <a class="active" href="#"><i class="fas fa-gauge me-2"></i> Dashboard</a>
  <a href="admin_add_product.php"><i class="fas fa-plus me-2"></i> Add Product</a>
  <a href="add_category.php"><i class="fas fa-tags me-2"></i> Add Category</a>
  <a href="admin_product_pending.php"><i class="fas fa-clock me-2"></i> Pending (<?= $pendingCount ?>)</a>
  <a href="admin_products.php"><i class="fas fa-boxes me-2"></i> Products</a>
  <a href="admin_orders.php"><i class="fas fa-cart-shopping me-2"></i> Orders</a>
  <a href="../index.php" target="_blank"><i class="fas fa-store me-2"></i> View Store</a>
  <a href="../logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</aside>

<!-- MAIN -->
<main class="flex-fill p-4">

<!-- Top Bar -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-0">Welcome, <?= htmlspecialchars($adminName) ?></h4>
    <small class="text-muted">Administrator Dashboard</small>
  </div>
</div>

<!-- STATS -->
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card shadow-sm border-0">
      <div class="card-body d-flex justify-content-between">
        <div>
          <small>Total Products</small>
          <h4><?= $totalProducts ?></h4>
        </div>
        <i class="fas fa-box card-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm border-0">
      <div class="card-body d-flex justify-content-between">
        <div>
          <small>Pending</small>
          <h4><?= $pendingCount ?></h4>
        </div>
        <i class="fas fa-clock card-icon text-warning"></i>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm border-0">
      <div class="card-body d-flex justify-content-between">
        <div>
          <small>Approved</small>
          <h4><?= $approvedCount ?></h4>
        </div>
        <i class="fas fa-check card-icon text-success"></i>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm border-0">
      <div class="card-body d-flex justify-content-between">
        <div>
          <small>Rejected</small>
          <h4><?= $rejectedCount ?></h4>
        </div>
        <i class="fas fa-times card-icon text-danger"></i>
      </div>
    </div>
  </div>
</div>

<!-- APPROVED PRODUCTS -->
<section>
<h5 class="mb-3 fw-bold text-success">Approved Products</h5>
<div class="row g-3">
<?php foreach($liveProducts as $p): $img=normalize_images($p['images']); ?>
<div class="col-md-4">
<div class="card shadow-sm">
<img src="<?= $img[0] ?>" class="img-thumb rounded-top">
<div class="card-body">
<h6><?= htmlspecialchars($p['name']) ?></h6>
<small class="text-muted"><?= $p['category_name'] ?> • <?= $p['username'] ?? 'Admin' ?></small>
</div>
</div>
</div>
<?php endforeach; ?>
</div>
</section>

<!-- TABLE -->
<section class="mt-5">
<h5 class="fw-bold">All Submissions</h5>
<div class="table-responsive shadow-sm bg-white">
<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
<th>#</th><th>Product</th><th>User</th><th>Status</th><th>Price</th><th>Image</th>
</tr>
</thead>
<tbody>
<?php foreach($allProducts as $i=>$p): $img=normalize_images($p['images']); ?>
<tr>
<td><?= $i+1 ?></td>
<td><?= htmlspecialchars($p['name']) ?></td>
<td><?= htmlspecialchars($p['username']) ?></td>
<td>
<?php if($p['status']=='approved'): ?>
<span class="badge bg-success">Approved</span>
<?php elseif($p['status']=='pending'): ?>
<span class="badge bg-warning">Pending</span>
<?php else: ?>
<span class="badge bg-danger">Rejected</span>
<?php endif; ?>
</td>
<td>₦<?= number_format($p['price'],2) ?></td>
<td><img src="<?= $img[0] ?>" width="70"></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</section>

</main>
</div>

<footer>
<small>© <?= date('Y') ?> RoseStore Admin</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle
const sidebar = document.querySelector('.sidebar');
const toggleBtn = document.getElementById('sidebarToggle');
const overlay = document.getElementById('overlay');

toggleBtn.addEventListener('click', () => { sidebar.classList.toggle('show'); });
overlay.addEventListener('click', () => { sidebar.classList.remove('show'); });

// Auto-close sidebar when a link clicked on mobile
const sidebarLinks = sidebar.querySelectorAll('a');
sidebarLinks.forEach(link => {
  link.addEventListener('click', () => {
    if(window.innerWidth < 992) sidebar.classList.remove('show');
  });
});
</script>
</body>
</html>
