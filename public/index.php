<?php
session_start();
require_once '../inc/db.php';
require_once '../inc/functions.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';
require_once __DIR__ . '/../inc/bootstrap.php';
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// ------------------------------------------------------
// FIX: Handle "add to cart" BEFORE loading header.php
// ------------------------------------------------------
if (isset($_GET['action'], $_GET['product_id']) && $_GET['action'] === 'add_to_cart') {
    $productId = $_GET['product_id'];

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += 1;
    } else {
        // Admin product
        $stmtAdmin = $pdo->prepare("SELECT id, name, price, stock, images FROM products WHERE CONCAT('p-', id)=?");
        $stmtAdmin->execute([$productId]);
        $pAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

        // User product
        $stmtUser = $pdo->prepare("SELECT id, name, price, stock, images FROM user_products WHERE CONCAT('u-', id)=?");
        $stmtUser->execute([$productId]);
        $pUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

        $product = $pAdmin ?: $pUser;

        if ($product) {
            $_SESSION['cart'][$productId] = [
                'id' => $productId,
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1,
                'images' => json_decode($product['images'], true) ?: ["uploads/default.png"]
            ];
        }
    }

    // Redirect BEFORE header.php outputs HTML
    header("Location: index.php");
    exit;
}

// ------------------------------------------------------
// Fetch categories and all products FIRST
// ------------------------------------------------------
$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
$categoryFilter = intval($_GET['category'] ?? 0);
$searchQuery = trim($_GET['search'] ?? '');

// Normalizing images
function normalize_images($imagesJson) {
    $images = json_decode($imagesJson, true);
    if (empty($images)) return ['uploads/default.png'];

    return array_map(function($img) {
        $img = trim($img);
        if ($img === '') return 'uploads/default.png';
        $img = str_replace(['../', './'], '', $img);
        if (!str_starts_with($img, 'uploads/')) $img = 'uploads/' . ltrim($img, '/');
        return $img;
    }, $images);
}

// Admin products
$sqlAdmin = "SELECT * FROM products WHERE 1";
$paramsAdmin = [];
if ($categoryFilter) { $sqlAdmin .= " AND category_id=?"; $paramsAdmin[] = $categoryFilter; }
if ($searchQuery !== '') { $sqlAdmin .= " AND name LIKE ?"; $paramsAdmin[] = "%$searchQuery%"; }
$sqlAdmin .= " ORDER BY id DESC";
$stmtAdmin = $pdo->prepare($sqlAdmin);
$stmtAdmin->execute($paramsAdmin);
$productsAdmin = $stmtAdmin->fetchAll(PDO::FETCH_ASSOC);

// User products
$sqlUser = "SELECT * FROM user_products WHERE status='approved'";
$paramsUser = [];
if ($categoryFilter) { $sqlUser .= " AND category_id=?"; $paramsUser[] = $categoryFilter; }
if ($searchQuery !== '') { $sqlUser .= " AND name LIKE ?"; $paramsUser[] = "%$searchQuery%"; }
$sqlUser .= " ORDER BY id DESC";
$stmtUser = $pdo->prepare($sqlUser);
$stmtUser->execute($paramsUser);
$productsUser = $stmtUser->fetchAll(PDO::FETCH_ASSOC);

// Merge products
$products = [];
foreach ($productsAdmin as $p) $products[] = [
    'id'         => 'p-' . $p['id'],
    'name'       => $p['name'],
    'price'      => $p['price'],
    'stock'      => (int)$p['stock'],
    'images'     => normalize_images($p['images']),
    'category_id'=> $p['category_id'],
    'source'     => 'admin'
];
foreach ($productsUser as $p) $products[] = [
    'id'         => 'u-' . $p['id'],
    'name'       => $p['name'],
    'price'      => $p['price'],
    'stock'      => (int)$p['stock'],
    'images'     => normalize_images($p['images']),
    'category_id'=> $p['category_id'],
    'source'     => 'user'
];

// Icons
$icons = ['fa-mobile-screen','fa-laptop','fa-person-dress','fa-desktop','fa-shirt','fa-house','fa-gem','fa-keyboard','fa-baby','fa-gamepad','fa-camera','fa-football','fa-dumbbell','fa-gem'];

// Safe category name
$categoryName = '';
if ($categoryFilter) {
    $catIndex = array_search($categoryFilter, array_column($categories,'id'));
    if ($catIndex !== false) $categoryName = $categories[$catIndex]['name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<meta name="title" content="Rose Store | Shop Quality Products Online in Nigeria">
<meta name="description" content="Rose Store is a modern online shopping platform in Nigeria offering quality electronics, fashion, beauty, and lifestyle products at affordable prices with fast delivery.">
<meta name="keywords" content="Rose Store, online shopping Nigeria, ecommerce Nigeria, buy electronics online, fashion store Nigeria, beauty products Nigeria">
<meta name="author" content="Rose Store">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://www.rosestore.com/">

<link rel="icon" type="image/png" href="assets/favicon.png">
<link rel="shortcut icon" type="image/png" href="assets/favicon.png">
<title>Rose Store | Home</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{
  --primary:#6A1B9A;
  --accent:#E91E63;
  --secondary:#F3E5F5;
  --rose-light:#f6d9ff;
  --text-dark:#1c0033;
  --white:#fff;
}

body{
  background-color:var(--secondary);
  font-family:'Poppins',sans-serif;
  color:var(--text-dark);
}

/* Cart */
.cart-icon{position:relative;}
.cart-count{
  position:absolute;
  top:-6px;
  right:-8px;
  background:var(--accent);
  color:#fff;
  font-size:12px;
  border-radius:50%;
  padding:2px 6px;
}

/* Category grid */
.category-listing{
  width:90%;
  margin:0 auto;
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(120px,1fr));
  gap:10px;
  background:var(--secondary);
  padding:10px 0 20px;
  border-radius:12px;
}

.cat-item{
  text-align:center;
  color:var(--primary);
  background:var(--rose-light);
  border-radius:10px;
  padding:12px 0;
  transition:all 0.3s ease;
}

.cat-item:hover{
  background:var(--primary);
  color:#fff;
  transform:translateY(-3px);
  box-shadow:0 6px 18px rgba(106,27,154,0.2);
}

.cat-item i{
  font-size:22px;
  margin-bottom:6px;
}

/* Product cards */
.product-img{
  width:100%;
  height:150px;
  object-fit:cover;
  border-radius:8px;
}

.card{
  border:none;
  border-radius:14px;
  background:#fff;
  transition:all 0.3s ease;
}

.card:hover{
  transform:translateY(-4px);
  box-shadow:0 8px 22px rgba(106,27,154,0.18);
}

.card-body{font-size:14px;}
.card-title{
  font-size:14px;
  font-weight:600;
  height:36px;
  overflow:hidden;
}

.price{
  font-weight:600;
  color:var(--primary);
  margin:4px 0;
}

/* Buttons */
.btn-purple{
  background-color:var(--primary)!important;
  color:#fff!important;
  font-weight:600;
  border:none;
}

.btn-purple:hover{
  background-color:#5A137F!important;
  color:var(--accent)!important;
}
/* Badges */
.badge.bg-purple{
  background-color:var(--primary);
}
/* HERO */
.hero-img{
  height:400px;
  object-fit:cover;
  width:100%;
  border-radius:14px;
}
/* Carousel caption */
.carousel-caption {
  background: rgba(246, 217, 255, 0.96); /* light rose */
  color: var(--text-dark);
  padding: 1.5rem 2rem;
  border-radius: 20px;
  box-shadow: 0 6px 20px rgba(106, 27, 154, 0.18);
  max-width: 80%;
  margin: auto;
  transition: all 0.3s ease;
}

.carousel-caption:hover {
  background: rgba(165, 105, 189, 0.95); /* slightly lighter purple hover */
  color: #fff;
  transform: scale(1.03);
}

/* Promo text overlay */
.promo-text {
  position: absolute;
  bottom: 20px;
  left: 20px;
  background: rgba(246, 217, 255, 0.96); /* light rose */
  color: var(--text-dark);
  padding: 1rem 1.5rem;
  border-radius: 18px;
  box-shadow: 0 4px 14px rgba(106, 27, 154, 0.18);
  transition: all 0.3s ease;
}

.promo-text:hover {
  background: rgba(165, 105, 189, 0.95); /* slightly lighter purple hover */
  color: #fff;
  transform: scale(1.03);
}

/* Carousel dots */
.carousel-indicators [data-bs-target]{
  background-color:var(--primary);
  width:8px;
  height:8px;
  border-radius:50%;
  opacity:0.6;
}
.carousel-indicators .active{
  opacity:1;
}
/* Mobile */
@media (max-width:767px){
  .category-menu{width:260px;}
}
/* Images fill the promo block */
.promo-right img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 14px;
}




</style>
<!-- OPEN GRAPH -->
<meta property="og:title" content="Rose Store | Shop Online in Nigeria">
<meta property="og:description" content="Luxury meets affordability. Shop quality products online with fast delivery.">
<meta property="og:type" content="website">
<meta property="og:url" content="https://www.rosestore.com/">
<meta property="og:image" content="https://www.rosestore.com/assets/images/seo/og-image.jpg">
<meta name="twitter:card" content="summary_large_image">
</head>
<body>
<?php include __DIR__ . '/../inc/header.php'; ?>

<!-- MAIN CONTENT WRAPPER -->
<main class="main-content">
  <!-- HERO -->
  <div class="container-fluid hero-row py-3">
    <div class="row justify-content-center gx-3">
      <div class="col-lg-8">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner rounded shadow-sm">
            <!-- SLIDE 1 -->
            <div class="carousel-item active">
              <img src="../images/hero3.jpg" class="d-block w-100 hero-img" alt="Welcome to Rose Store">
              <div class="carousel-caption">
                <h2>Welcome to Rose Store</h2>
                <p>Your one-stop shop for quality products & amazing deals 🌹</p>
              </div>
            </div>
            <!-- SLIDE 2 -->
            <div class="carousel-item">
              <img src="../images/elect.jpg" class="d-block w-100 hero-img" alt="Electronics Deals">
              <div class="carousel-caption">
                <h2>Discover Beautiful Deals</h2>
                <p>Shop your favorites effortlessly</p>
              </div>
            </div>
            <!-- SLIDE 3 -->
            <div class="carousel-item">
              <img src="../images/delivery.jpg" class="d-block w-100 hero-img" alt="Fast Delivery">
              <div class="carousel-caption">
                <h2>Fast Delivery Nationwide</h2>
                <p>Get your orders quickly & safely</p>
              </div>
            </div>
            <!-- SLIDE 4 -->
            <div class="carousel-item">
              <img src="../images/super.jpeg" class="d-block w-100 hero-img" alt="Shop Fashion & Lifestyle">
              <div class="carousel-caption">
                <h2>Style Meets Affordability</h2>
                <p>Fashion, lifestyle & essentials designed for you</p>
              </div>
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
          <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
        </div>
      </div>

      <div class="col-lg-3 d-none d-lg-block promo-right">
        <div class="position-relative">
          <img src="../images/phone3.jpeg" class="w-100 rounded">
          <div class="promo-text">Hot mobile sales at amazing prices 📱</div>
        </div>
        <div class="position-relative">
          <img src="../images/logo7.jpg" class="w-100 rounded">
          <div class="promo-text">Get quality electronics with warranty ⚡</div>
        </div>
      </div>
    </div>

    <!-- Category Grid -->
    <div class="category-listing mb-4">
      <div class="container">
        <div class="row g-2 justify-content-center">
          <?php foreach($categories as $i=>$cat): $icon=$icons[$i%count($icons)]; ?>
            <div class="col-4 col-sm-3 col-md-2">
              <a href="index.php?category=<?= (int)$cat['id'] ?>" class="cat-item d-block"><i class="fa <?= $icon ?>"></i><div style="font-weight:500;font-size:12px;"><?= htmlspecialchars($cat['name']) ?></div></a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Product Grid -->
  <div class="container py-3">
    <h4 class="mb-4"><?= $categoryFilter && $categoryName ? "Products in ".htmlspecialchars($categoryName) : "All Products" ?></h4>
    <div class="row g-3">
      <?php if(count($products)>0): foreach($products as $product): ?>
        <div class="col-6 col-md-4 col-lg-2">
          <div class="card h-100 shadow-sm">
            <?php $images = $product['images']; ?>
            <div id="carousel<?= htmlspecialchars($product['id']) ?>" class="carousel slide" data-bs-ride="carousel">
              <?php if(count($images) > 1): ?>
              <div class="carousel-indicators">
                <?php foreach($images as $i => $img): ?>
                  <button type="button" data-bs-target="#carousel<?= htmlspecialchars($product['id']) ?>" data-bs-slide-to="<?= $i ?>" class="<?= $i===0?'active':'' ?>" aria-current="<?= $i===0?'true':'' ?>" aria-label="Slide <?= $i+1 ?>"></button>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
              
              <div class="carousel-inner">
                <?php foreach($images as $i => $img): ?>
                  <div class="carousel-item <?= $i===0 ? 'active' : '' ?>">
                    <img src="<?= htmlspecialchars($img) ?>" class="d-block w-100 product-img" alt="<?= htmlspecialchars($product['name']) ?>">
                  </div>
                <?php endforeach; ?>
              </div>
              
              <?php if(count($images) > 1): ?>
              <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?= htmlspecialchars($product['id']) ?>" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carousel<?= htmlspecialchars($product['id']) ?>" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
              </button>
              <?php endif; ?>
            </div>

            <div class="card-body d-flex flex-column">
              <div class="card-title"><?= htmlspecialchars($product['name']) ?></div>
              <div class="price">₦<?= number_format($product['price'],2) ?></div>
              <div class="mt-2"><?= $product['stock']>0?'<span class="badge bg-purple">In stock</span>':'<span class="badge bg-danger">Out of stock</span>' ?></div>
              <a href="index.php?action=add_to_cart&product_id=<?= urlencode($product['id']) ?>" class="btn btn-purple btn-sm mt-auto <?= $product['stock']==0?'disabled':'' ?>">Order</a>
            </div>
          </div>
        </div>
      <?php endforeach; else: ?>
        <div class="col-12 text-center text-muted">No products found.</div>
      <?php endif; ?>
    </div>
  </div>
</main>
<!-- END MAIN CONTENT -->

<!-- FOOTER -->
<?php include __DIR__ . '/../inc/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const menuToggle=document.getElementById('menuToggle'),categoryMenu=document.getElementById('categoryMenu');
menuToggle.addEventListener('click',()=>categoryMenu.classList.toggle('show-menu'));
document.addEventListener('click',e=>{const inside=categoryMenu.contains(e.target)||menuToggle.contains(e.target);if(!inside&&categoryMenu.classList.contains('show-menu'))categoryMenu.classList.remove('show-menu');});
</script>
</body>
