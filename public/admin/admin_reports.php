<?php
session_start();
require_once '../../inc/db.php';
require_once __DIR__ . '/../../inc/config.php';
include __DIR__ . '/../../inc/head.php';

/* ==============================
// SECURITY
============================= */
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

/* ==============================
// FETCH SALES DATA
============================= */
// Total Orders
$totalOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// Total Revenue (Paid Orders)
$totalRevenue = (float)$pdo->query("SELECT SUM(total_price) FROM orders WHERE status = 'paid'")->fetchColumn();

// Pending Orders
$pendingOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();

// Recent 10 Orders
$recentOrders = $pdo->query("
    SELECT o.*, u.email 
    FROM orders o
    LEFT JOIN users u ON o.buyer_id = u.id
    ORDER BY o.created_at DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// Top Selling Products
$topProducts = [];
$paidOrders = $pdo->query("SELECT product_id, quantity FROM orders WHERE status='paid'")->fetchAll(PDO::FETCH_ASSOC);

foreach ($paidOrders as $o) {
    $productName = 'Unknown Product';
    if (!empty($o['product_id'])) {
        $stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
        $stmt->execute([$o['product_id']]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($prod) $productName = $prod['name'];
    }
    $qty = $o['quantity'] ?? 1;
    $topProducts[$productName] = ($topProducts[$productName] ?? 0) + $qty;
}

arsort($topProducts);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Reports | MobileStore</title>

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

/* Cards & tables */
.card-icon{font-size:2rem; opacity:.3;}
.table-responsive{max-height:75vh; overflow-y:auto;}
</style>
</head>
<body>

<!-- Hamburger Toggle & Overlay -->
<button class="btn btn-primary d-lg-none p-2" id="sidebarToggle">
  <i class="fas fa-bars"></i>
</button>
<div id="overlay"></div>

<div class="d-flex">

<!-- SIDEBAR -->
<aside class="sidebar">
  <h4 class="fw-bold">MobileStore Admin</h4>
  <p class="small">Admin Panel</p>
  <hr>
  <a href="admin_dashboard.php"><i class="fa-solid fa-chart-line me-2"></i> Dashboard</a>
  <a href="admin_add_product.php"><i class="fa-solid fa-plus me-2"></i> Add Product</a>
  <a href="add_category.php"><i class="fa-solid fa-folder-plus me-2"></i> Add Category</a>
  <a href="admin_products.php"><i class="fa-solid fa-box me-2"></i> Products</a>
  <a href="admin_orders.php"><i class="fa-solid fa-cart-shopping me-2"></i> Orders</a>
  <a href="admin_reports.php" class="active"><i class="fa-solid fa-chart-column me-2"></i> Reports</a>
  <a href="admin_sales_report.php"><i class="fa-solid fa-chart-pie me-2"></i> Sales Report</a>
  <a href="../index.php" target="_blank"><i class="fa-solid fa-house me-2"></i> View Store</a>
  <a href="../logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
</aside>

<!-- MAIN -->
<main class="flex-fill p-4">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="fa fa-chart-column me-2 text-primary"></i> Sales Reports & Statistics</h4>
</div>

<!-- Overview Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <h6>Total Orders</h6>
                <h3 class="fw-bold text-primary"><?= $totalOrders ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <h6>Total Revenue</h6>
                <h3 class="fw-bold text-success">₦<?= number_format($totalRevenue, 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <h6>Pending Orders</h6>
                <h3 class="fw-bold text-warning"><?= $pendingOrders ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Top Products -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-primary text-white fw-bold">
        <i class="fa fa-trophy me-2"></i>Top Selling Products
    </div>
    <div class="card-body">
        <?php if (!empty($topProducts)): ?>
        <div class="table-responsive">
        <table class="table table-bordered table-striped text-center mb-0">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity Sold</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topProducts as $name => $qty): ?>
                <tr>
                    <td><?= htmlspecialchars($name) ?></td>
                    <td><?= $qty ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php else: ?>
            <p>No sales data available yet.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Orders -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-secondary text-white fw-bold">
        <i class="fa fa-clock me-2"></i>Recent Orders
    </div>
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-hover text-center mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Email</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentOrders as $i => $o): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($o['email']) ?></td>
                    <td>₦<?= number_format($o['total_price'],2) ?></td>
                    <td>
                        <?php if ($o['status']=='paid'): ?>
                        <span class="badge bg-success">Paid</span>
                        <?php elseif ($o['status']=='pending'): ?>
                        <span class="badge bg-warning text-dark">Pending</span>
                        <?php else: ?>
                        <span class="badge bg-secondary"><?= htmlspecialchars($o['status']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($o['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

</main>
</div>

<footer class="text-center py-3" style="background:var(--admin-primary); color:#fff;">
<small>© <?= date('Y') ?> MobileStore Admin</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle
const sidebar = document.querySelector('.sidebar');
const toggleBtn = document.getElementById('sidebarToggle');
const overlay = document.getElementById('overlay');

toggleBtn.addEventListener('click', () => { sidebar.classList.toggle('show'); });
overlay.addEventListener('click', () => { sidebar.classList.remove('show'); });

const sidebarLinks = sidebar.querySelectorAll('a');
sidebarLinks.forEach(link => {
  link.addEventListener('click', () => {
    if(window.innerWidth < 992) sidebar.classList.remove('show');
  });
});
</script>

</body>
</html>
