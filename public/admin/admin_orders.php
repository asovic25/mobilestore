<?php
session_start();
require_once '../../inc/db.php';
require_once __DIR__ . '/../../inc/config.php';
include __DIR__ . '/../../inc/head.php';


// =============================
// SECURITY
// =============================
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// =============================
// FETCH ORDERS
// =============================
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =============================
// HELPERS
// =============================
function get_user_email($pdo, $user_id, $fallback) {
    if (!empty($user_id)) {
        $q = $pdo->prepare("SELECT email FROM users WHERE id = ?");
        $q->execute([$user_id]);
        $u = $q->fetch(PDO::FETCH_ASSOC);
        return $u['email'] ?? $fallback ?? 'N/A';
    }
    return $fallback ?? 'N/A';
}

function count_cart_items($cart_json) {
    if (!$cart_json) return 0;
    $cart = json_decode($cart_json, true);
    return is_array($cart) ? count($cart) : 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Orders | MobileStore</title>
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
.sidebar.show{ transform:translateX(0); }
.sidebar a{ color:#cfd8ff; padding:12px 16px; display:block; border-radius:8px; text-decoration:none; margin-bottom:4px; }
.sidebar a:hover,.sidebar a.active{ background:var(--admin-accent); color:#fff; }
.sidebar h4, .sidebar p{color:#fff;}
#overlay{ position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); opacity:0; visibility:hidden; transition:all 0.3s; z-index:998; }
.sidebar.show + #overlay{ opacity:1; visibility:visible; }

/* Hide toggle on desktop */
#sidebarToggle{ position:fixed; top:10px; left:10px; z-index:1000; }
@media(min-width:992px){ .sidebar{ transform:translateX(0); position:relative; } #sidebarToggle, #overlay{display:none;} }

/* Table & cards */
.badge-status{font-size:.9rem;}
.table-responsive{max-height:75vh; overflow-y:auto;}
</style>
</head>
<body>

<!-- Hamburger Toggle & Overlay -->
<button class="btn btn-primary d-lg-none p-2" id="sidebarToggle"><i class="fas fa-bars"></i></button>
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
  <a href="admin_orders.php" class="active"><i class="fa-solid fa-cart-shopping me-2"></i> Orders</a>
  <a href="admin_reports.php"><i class="fa-solid fa-chart-column me-2"></i> Reports</a>
  <a href="admin_sales_report.php"><i class="fa-solid fa-chart-pie me-2"></i> Sales Report</a>
  <a href="../index.php" target="_blank"><i class="fa-solid fa-house me-2"></i> View Store</a>
  <a href="../logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
</aside>

<!-- MAIN -->
<main class="flex-fill p-4">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="fa fa-shopping-cart me-2 text-primary"></i> Orders Management</h4>
</div>

<?php if(empty($orders)): ?>
    <div class="alert alert-info">No orders found yet.</div>
<?php else: ?>
<div class="table-responsive shadow-sm bg-white rounded">
<table class="table table-bordered align-middle table-hover mb-0">
    <thead class="table-primary text-center">
        <tr>
            <th>#</th>
            <th>Email</th>
            <th>Items</th>
            <th>Total (₦)</th>
            <th>Status</th>
            <th>Reference</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($orders as $i => $o): 
        $email = get_user_email($pdo, $o['buyer_id'] ?? null, $o['buyer_email'] ?? null);
        $itemCount = $o['quantity'] ?? 1;
        $status = strtolower($o['status'] ?? 'pending');
        $badge_class = match($status) {
            'pending' => 'bg-warning text-dark',
            'paid' => 'bg-success',
            'shipped' => 'bg-info text-dark',
            'completed' => 'bg-primary',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary'
        };
    ?>
        <tr class="text-center">
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($email) ?></td>
            <td><?= $itemCount ?></td>
            <td class="fw-bold"><?= number_format($o['total_price'] ?? 0,2) ?></td>
            <td><span class="badge <?= $badge_class ?> badge-status"><?= ucfirst($status) ?></span></td>
            <td><?= htmlspecialchars($o['payment_ref'] ?? '') ?></td>
            <td><?= htmlspecialchars($o['created_at'] ?? '') ?></td>
            <td>
                <form method="POST" action="update_order.php" class="mb-1">
                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                    <select name="status" class="form-select form-select-sm mb-1">
                        <?php foreach(['pending','paid','shipped','completed','cancelled'] as $s): ?>
                            <option value="<?= $s ?>" <?= $status==$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary w-100">Update</button>
                </form>
                <a href="print_invoice.php?order_id=<?= $o['id'] ?>" target="_blank" class="btn btn-sm btn-outline-success w-100 mt-1">
                    <i class="fa fa-print me-1"></i> Invoice
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

</main>
</div>

<footer class="text-center py-3" style="background:var(--admin-primary); color:#fff;">
    <small>© <?= date('Y') ?> MobileStore Admin</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar = document.querySelector('.sidebar');
const toggleBtn = document.getElementById('sidebarToggle');
const overlay = document.getElementById('overlay');

toggleBtn.addEventListener('click', () => sidebar.classList.toggle('show'));
overlay.addEventListener('click', () => sidebar.classList.remove('show'));
const sidebarLinks = sidebar.querySelectorAll('a');
sidebarLinks.forEach(link => link.addEventListener('click', () => { if(window.innerWidth < 992) sidebar.classList.remove('show'); }));
</script>
</body>
</html>
