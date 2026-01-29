<?php
// DO NOT start session here – handled by pages
require_once __DIR__ . '/db.php';

/* Load categories once */
if (!isset($categories)) {
    $categories = $pdo
        ->query("SELECT id, name FROM categories ORDER BY name ASC")
        ->fetchAll(PDO::FETCH_ASSOC);
}

/* Safe cart count */
$cartQty = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartQty += (int)($item['quantity'] ?? 0);
    }
}

$icons = [
  'fa-mobile','fa-laptop','fa-headphones','fa-tv',
  'fa-plug','fa-gamepad','fa-camera','fa-clock'
];
?>


<nav class="navbar navbar-expand-lg sticky-top shadow purple-rose-nav">
  <div class="container">

    <!-- LEFT -->
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-light rounded-circle" id="menuToggle" type="button">
        <i class="fa fa-bars"></i>
      </button>

      <a class="navbar-brand fw-bold fs-4 text-white" href="index.php">
        🌹 Rose Store
      </a>
    </div>

    <!-- SEARCH -->
    <form class="d-none d-lg-flex mx-3 flex-grow-1" method="GET" action="index.php" style="max-width:520px;">
      <input class="form-control me-2 rounded-pill"
             type="search"
             name="search"
             placeholder="Search products…"
             value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
      <button class="btn btn-rose-gradient rounded-pill px-4" type="submit">
        <i class="fa fa-search"></i>
      </button>
    </form>

    <!-- RIGHT -->
    <div class="d-flex align-items-center gap-3">

      <!-- Cart -->
      <a href="cart.php" class="text-white position-relative">
        <i class="fa fa-shopping-cart fa-lg"></i>
        <span class="cart-count"><?= $cartQty ?></span>
      </a>

      <?php if (!empty($_SESSION['user'])): ?>

        <!-- AVATAR DROPDOWN -->
        <div class="dropdown">
          <img src="uploads/avatars/<?= htmlspecialchars($_SESSION['user']['avatar'] ?? 'default.png') ?>"
               width="38" height="38"
               class="rounded-circle border border-light dropdown-toggle"
               id="avatarDropdown"
               data-bs-toggle="dropdown"
               aria-expanded="false"
               style="cursor:pointer;">

          <ul class="dropdown-menu dropdown-menu-end rose-dropdown shadow" aria-labelledby="avatarDropdown">
            <li>
              <a class="dropdown-item" href="profile.php">
                <i class="fa fa-user me-2"></i> View Profile
              </a>
            </li>
            <li>
              <a class="dropdown-item text-danger" href="delete_account.php">
                <i class="fa fa-trash me-2"></i> Delete Account
              </a>
            </li>
          </ul>
        </div>

        <span class="text-white fw-semibold d-none d-md-inline">
          <?= htmlspecialchars($_SESSION['user']['username']) ?>
        </span>

        <a href="user_dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
        <a href="logout.php" class="btn btn-light btn-sm text-dark">Logout</a>

      <?php else: ?>
        <a href="user_login.php" class="btn btn-outline-light btn-sm">Login</a>
        <a href="user_signup.php" class="btn btn-light btn-sm text-dark">Sign Up</a>
      <?php endif; ?>

    </div>

  </div>

  <!-- CATEGORY MENU -->
  <div id="categoryMenu" class="category-menu">
    <div class="menu-header">🌸 Shop by Category</div>

    <?php foreach ($categories as $i => $cat): ?>
      <a href="index.php?category=<?= (int)$cat['id'] ?>">
        <i class="fa <?= $icons[$i % count($icons)] ?> me-2"></i>
        <?= htmlspecialchars($cat['name']) ?>
      </a>
    <?php endforeach; ?>
  </div>
</nav>


<style>
:root {
  --rose-deep:#5e2a84;
  --rose-medium:#8e44ad;
  --rose-light:#f6d9ff;
  --rose-gold:#f3c623;
  --primary:#6A1B9A;
  --text-dark:#1c0033;
  --text-light:#fff;
  --hover-bg:#e8c6ff;
}

/* Navbar */
.purple-rose-nav {
  background: linear-gradient(90deg,var(--rose-deep),var(--rose-medium));
}

/* Gradient Button */
.btn-rose-gradient {
  background: linear-gradient(90deg,var(--rose-medium),var(--rose-light));
  border: none;
  color: var(--text-dark);
}

/* Cart */
.cart-count {
  background: var(--rose-gold);
  color:#000;
  border-radius:50%;
  font-size:.7rem;
  padding:2px 6px;
  position:absolute;
  top:-8px;
  right:-10px;
}

/* Avatar Dropdown */
.rose-dropdown {
  border-radius:12px;
  background:var(--rose-light);
}
.rose-dropdown a:hover {
  background:var(--hover-bg);
}

/* Category Menu */
.category-menu {
  position:fixed;
  top:0;
  left:-260px;
  width:260px;
  height:100%;
  background:var(--rose-light);
  transition:left .3s ease;
  z-index:1060;
  overflow-y:auto;
}
.category-menu.active { left:0; }

.category-menu .menu-header {
  background: linear-gradient(90deg,var(--primary),var(--rose-medium));
  color:#fff;
  padding:1rem;
  font-weight:700;
  text-align:center;
}

.category-menu a {
  display:flex;
  align-items:center;
  padding:10px 16px;
  border-bottom:1px solid rgba(0,0,0,.08);
  text-decoration:none;
  color:var(--text-dark);
}
.category-menu a:hover {
  background:var(--hover-bg);
  padding-left:22px;
}
</style>


<script>
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('menuToggle');
  const menu = document.getElementById('categoryMenu');

  btn.addEventListener('click', e => {
    e.stopPropagation();
    menu.classList.toggle('active');
  });

  document.addEventListener('click', e => {
    if (!menu.contains(e.target) && !btn.contains(e.target)) {
      menu.classList.remove('active');
    }
  });
});
</script>
