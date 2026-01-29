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
// FETCH SALES DATA FROM orders
// Only count orders where status = 'Completed'
// =============================
$stmt = $pdo->query("SELECT * FROM orders WHERE status = 'Completed'");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalRevenue = 0;
$totalOrders = count($orders);
$totalItems = 0;
$dailySales = [];
$productSales = [];

foreach ($orders as $order) {
    $totalRevenue += $order['total_price'] ?? 0;
    $qty = $order['quantity'] ?? 1;
    $totalItems += $qty;

    // Resolve product name
    if (!empty($order['product_id'])) {
        $stmtProd = $pdo->prepare("SELECT name FROM products WHERE id = ?");
        $stmtProd->execute([$order['product_id']]);
        $prod = $stmtProd->fetch(PDO::FETCH_ASSOC);
        $productName = $prod['name'] ?? 'Unknown Product';
    } else {
        $productName = 'Unknown Product';
    }

    $productSales[$productName] = ($productSales[$productName] ?? 0) + $qty;

    $date = date('Y-m-d', strtotime($order['created_at']));
    $dailySales[$date] = ($dailySales[$date] ?? 0) + ($order['total_price'] ?? 0);
}

// Sort top products
arsort($productSales);

// Prepare last 7 days chart
$labels = [];
$values = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $labels[] = $day;
    $values[] = $dailySales[$day] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sales Report | MobileStore Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
:root {
    --navy: #0A1D37;
    --navy-light: #102E4A;
    --accent: #1E90FF;
    --bg: #F4F6F9;
}
body { background: var(--bg); font-family: Arial, sans-serif; }
.sidebar { background: var(--navy); color: white; min-height: 100vh; width: 240px; position: fixed; top: 0; left: 0; transform: translateX(-100%); transition: transform 0.3s; z-index: 999; }
.sidebar.show { transform: translateX(0); }
.sidebar a { color: white; text-decoration: none; display: block; padding: 10px 15px; }
.sidebar a:hover, .sidebar a.active { background: var(--navy-light); border-radius: 5px; }
.card { border-left: 5px solid var(--accent); }
.table-primary { background-color: var(--navy-light) !important; color: white !important; }
.text-primary { color: var(--accent) !important; }
.text-success { color: #28a745 !important; }
.text-warning { color: #ffc107 !important; }
.btn-primary { background-color: var(--accent) !important; border-color: var(--accent) !important; }
.chart-container { position: relative; height: 250px; }

/* Hamburger toggle */
#sidebarToggle { position: fixed; top: 10px; left: 10px; z-index: 1000; }
#overlay { position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); opacity:0; visibility:hidden; transition:0.3s; z-index:998; }
.sidebar.show + #overlay { opacity:1; visibility:visible; }
@media(min-width:992px){ .sidebar{ transform:translateX(0); position:relative; } #sidebarToggle, #overlay{display:none;} }
</style>
</head>
<body>

<!-- Hamburger Toggle & Overlay -->
<button class="btn btn-primary d-lg-none p-2" id="sidebarToggle"><i class="fas fa-bars"></i></button>
<div id="overlay"></div>

<div class="d-flex">
    <!-- Sidebar -->
    <aside class="sidebar p-3">
        <h4 class="fw-bold mb-4">MobileStore Admin</h4>
        <nav class="nav flex-column">
            <a href="admin_dashboard.php" class="nav-link"><i class="fa-solid fa-chart-line me-2"></i> Dashboard</a>
            <a href="admin_add_product.php" class="nav-link"><i class="fa-solid fa-plus me-2"></i> Add Product</a>
            <a href="add_category.php" class="nav-link"><i class="fa-solid fa-folder-plus me-2"></i> Add Category</a>
            <a href="admin_products.php" class="nav-link"><i class="fa-solid fa-box me-2"></i> Products</a>
            <a href="admin_orders.php" class="nav-link"><i class="fa-solid fa-cart-shopping me-2"></i> Orders</a>
            <a href="admin_sales_report.php" class="nav-link active"><i class="fa-solid fa-chart-pie me-2"></i> Sales Report</a>
            <a href="../index.php" target="_blank" class="nav-link"><i class="fa-solid fa-house me-2"></i> View Store</a>
            <a href="../logout.php" class="nav-link text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
        </nav>
    </aside>

    <!-- Main -->
    <main class="flex-fill p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4><i class="fa-solid fa-chart-pie me-2 text-primary"></i> Sales & Revenue Report</h4>
            <a href="admin_dashboard.php" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left me-1"></i> Back</a>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6>Total Revenue</h6>
                        <h3 class="text-success">₦<?= number_format($totalRevenue, 2) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6>Total Orders</h6>
                        <h3 class="text-primary"><?= $totalOrders ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6>Total Items Sold</h6>
                        <h3 class="text-warning"><?= $totalItems ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="mb-3"><i class="fa-solid fa-chart-line me-2 text-primary"></i> Last 7 Days Sales</h5>
                <canvas id="salesChart" class="chart-container"></canvas>
            </div>
        </div>

        <!-- Top Products -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5><i class="fa-solid fa-crown me-2 text-accent"></i> Top Selling Products</h5>
                    <button onclick="exportCSV()" class="btn btn-success btn-sm"><i class="fa fa-download me-1"></i> Export CSV</button>
                </div>

                <?php if (empty($productSales)): ?>
                    <div class="alert alert-info">No products sold yet.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Quantity Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach ($productSales as $name => $qty): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= htmlspecialchars($name) ?></td>
                                        <td><?= $qty ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script>
const ctx = document.getElementById('salesChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Daily Revenue (₦)',
            data: <?= json_encode($values) ?>,
            borderColor: 'var(--accent)',
            tension: 0.3,
            fill: true,
            backgroundColor: 'rgba(30,144,255,0.1)'
        }]
    },
    options: { scales: { y: { beginAtZero: true } } }
});

function exportCSV() {
    let csv = "Product,Quantity Sold\n";
    <?php foreach ($productSales as $name => $qty): ?>
        csv += "<?= addslashes($name) ?>,<?= $qty ?>\n";
    <?php endforeach; ?>
    const blob = new Blob([csv], { type: "text/csv" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "sales_report.csv";
    link.click();
}

// Sidebar toggle
const sidebar = document.querySelector('.sidebar');
const toggleBtn = document.getElementById('sidebarToggle');
const overlay = document.getElementById('overlay');

toggleBtn.addEventListener('click', () => { sidebar.classList.toggle('show'); });
overlay.addEventListener('click', () => { sidebar.classList.remove('show'); });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
