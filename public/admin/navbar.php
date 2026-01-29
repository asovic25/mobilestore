<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin name from session
$adminName = $_SESSION['admin_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin | ROSE E-Commerce</title>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Admin CSS -->
<link href="css/admin.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="admin_dashboard.php">🌹 ROSE Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="adminNavbar">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item me-3 text-white">Hello, <?= htmlspecialchars($adminName) ?></li>
        <li class="nav-item"><a class="nav-link" href="../index.php">Main Site</a></li>
        <li class="nav-item"><a class="nav-link" href="add_product.php">Add Product</a></li>
        <li class="nav-item"><a class="nav-link" href="add_category.php">Add Category</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_logout.php">Logout</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Back to Dashboard</a></li>
      </ul>
    </div>
  </div>
</nav>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

