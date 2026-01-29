<?php
session_start();
require_once '../inc/db.php';
require_once '../inc/functions.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';

// Initialize cart if empty
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Remove or Update Quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove'])) {
        $removeId = $_POST['remove'];
        unset($_SESSION['cart'][$removeId]);
        header("Location: cart.php");
        exit;
    }

    foreach ($_POST['quantity'] as $productId => $qty) {
        if ($qty <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId]['quantity'] = $qty;
        }
    }
    header("Location: cart.php");
    exit;
}

// Update cart items dynamically with database stock
foreach ($_SESSION['cart'] as $id => &$item) {
    $prodId = preg_replace('/^[pu]-/', '', $item['id']);
    if (str_starts_with($item['id'], 'p-')) {
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id=?");
    } else {
        $stmt = $pdo->prepare("SELECT stock FROM user_products WHERE id=?");
    }
    $stmt->execute([$prodId]);
    $dbStock = $stmt->fetchColumn();
    $item['stock'] = (int)$dbStock;
    if ($item['quantity'] > $item['stock']) {
        $item['quantity'] = $item['stock'];
    }
    $item['subtotal'] = $item['price'] * $item['quantity'];
}
unset($item);

// Calculate total and cart count
$total = 0;
$cartCount = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['subtotal'];
    $cartCount += $item['quantity'];
}

$avatarPath = "uploads/avatars/" . ($_SESSION['user']['avatar'] ?? 'default.png');
if (!file_exists(__DIR__ . '/' . $avatarPath)) $avatarPath = "uploads/avatars/default.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cart | Rose Store</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{
    --primary:#6A1B9A;
    --accent:#E91E63;
    --secondary:#F3E5F5;
    --white:#fff;
    --dark:#212121;
}
body { background-color: var(--secondary); font-family:'Poppins',sans-serif; }
.cart-card { background: var(--white); border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 20px; }
.product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; cursor: pointer; }
.btn-purple { background-color: var(--primary); color: #fff; border: none; transition: 0.3s; }
.btn-purple:hover { background-color:#5A137F; }
.btn-success { background-color: var(--accent); border: none; color: #fff; }
.btn-success:hover { background-color:#d81b60; }
.btn-remove { background-color: #dc3545; color: #fff; border: none; }
.btn-remove:hover { background-color:#b71c1c; }
.total-box { background-color: #F3E5F5; padding: 15px; border-radius: 12px; font-weight: 600; font-size: 1.3em; text-align: right; margin-top: 15px; color: var(--primary);}
.img-preview-hover {
    position: absolute;
    display: none;
    z-index: 999;
    max-width: 200px;
    max-height: 200px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    transition: 0.3s;
}
.cart-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background-color: var(--accent);
    color: #fff;
    font-size: 0.7rem;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 50%;
}
@media (max-width: 767px) {
    table, thead, tbody, th, td, tr { display: block; }
    thead { display: none; }
    tr { margin-bottom: 15px; background: #fff; border-radius: 12px; padding: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    td { border: none; padding: 5px 0; display: flex; justify-content: space-between; }
    td::before { content: attr(data-label); font-weight: 600; color: var(--primary); }
    .product-img { width: 50px; height: 50px; }
}
</style>
</head>
<body>

<?php include __DIR__ . '/../inc/header.php'; ?>

<div class="container py-5">
    <h3 class="mb-4 text-primary"><i class="fa-solid fa-cart-shopping me-2"></i>Your Cart</h3>

    <?php if(empty($_SESSION['cart'])): ?>
        <div class="alert alert-info">Your cart is empty.</div>
    <?php else: ?>
    <form method="POST">
        <div class="cart-card table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price (₦)</th>
                        <th>Quantity</th>
                        <th>Subtotal (₦)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($_SESSION['cart'] as $productId => $item): ?>
                    <tr>
                        <td data-label="Product">
                            <?php if(!empty($item['image'])): ?>
                                <img src="<?= htmlspecialchars($item['image']) ?>" class="product-img me-2 hover-img" data-img="<?= htmlspecialchars($item['image']) ?>">
                            <?php endif; ?>
                            <?= htmlspecialchars($item['name']) ?>
                        </td>
                        <td data-label="Price">₦<?= number_format($item['price'],2) ?></td>
                        <td data-label="Quantity">
                            <input type="number" name="quantity[<?= $productId ?>]" value="<?= $item['quantity'] ?>" min="0" max="<?= $item['stock'] ?>" class="form-control" style="width:80px;">
                        </td>
                        <td data-label="Subtotal">₦<?= number_format($item['subtotal'],2) ?></td>
                        <td data-label="Action">
                            <button type="submit" name="remove" value="<?= $productId ?>" class="btn btn-remove btn-sm">
                                <i class="fa-solid fa-trash"></i> Remove
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="total-box">
            Total: ₦<?= number_format($total,2) ?>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-3">
            <button type="submit" class="btn btn-purple"><i class="fa-solid fa-sync me-1"></i> Update Cart</button>
            <a href="checkout.php" class="btn btn-success"><i class="fa-solid fa-credit-card me-1"></i> Proceed to Checkout</a>
        </div>
    </form>
    <?php endif; ?>
</div>

<img id="hoverPreview" class="img-preview-hover">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Image hover preview
const hoverImg = document.getElementById('hoverPreview');
document.querySelectorAll('.hover-img').forEach(img => {
    img.addEventListener('mouseenter', e => {
        hoverImg.src = e.target.dataset.img;
        hoverImg.style.display = 'block';
    });
    img.addEventListener('mousemove', e => {
        hoverImg.style.top = e.pageY + 15 + 'px';
        hoverImg.style.left = e.pageX + 15 + 'px';
    });
    img.addEventListener('mouseleave', () => {
        hoverImg.style.display = 'none';
    });
});

// Dynamic subtotal update on quantity change
document.querySelectorAll('input[name^="quantity"]').forEach(input => {
    input.addEventListener('input', () => {
        const row = input.closest('tr');
        const price = parseFloat(row.querySelector('td[data-label="Price"]').innerText.replace(/₦|,/g,''));
        const subtotalCell = row.querySelector('td[data-label="Subtotal"]');
        const qty = parseInt(input.value) || 0;
        subtotalCell.innerText = '₦' + (price * qty).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits:2});

        let total = 0;
        document.querySelectorAll('td[data-label="Subtotal"]').forEach(td => {
            total += parseFloat(td.innerText.replace(/₦|,/g,''));
        });
        document.querySelector('.total-box').innerText = 'Total: ₦' + total.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
    });
});
</script>

<?php include __DIR__ . '/../inc/footer.php'; ?>
</body>
</html>
