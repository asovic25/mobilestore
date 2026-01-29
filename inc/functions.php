<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php'; // Connect to the database

// ====================== CATEGORY FUNCTIONS =========================

// Fetch all categories
function fetchCategories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch all products (optional by category)
function fetchProducts($pdo, $categoryId = null) {
    if ($categoryId) {
        $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p 
                               JOIN categories c ON p.category_id = c.id 
                               WHERE c.id = ? ORDER BY p.id DESC");
        $stmt->execute([$categoryId]);
    } else {
        $stmt = $pdo->query("SELECT p.*, c.name AS category_name FROM products p 
                             JOIN categories c ON p.category_id = c.id 
                             ORDER BY p.id DESC");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Add new product
function addProduct($pdo, $name, $category_id, $description, $price, $quantity, $image) {
    $stmt = $pdo->prepare("INSERT INTO products (name, category_id, description, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $category_id, $description, $price, $quantity, $image]);
}

// Get product by ID
function getProductById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Update product
function updateProduct($pdo, $id, $name, $category_id, $description, $price, $quantity, $image = null) {
    if ($image) {
        $stmt = $pdo->prepare("UPDATE products SET name=?, category_id=?, description=?, price=?, quantity=?, image=? WHERE id=?");
        $stmt->execute([$name, $category_id, $description, $price, $quantity, $image, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE products SET name=?, category_id=?, description=?, price=?, quantity=? WHERE id=?");
        $stmt->execute([$name, $category_id, $description, $price, $quantity, $id]);
    }
}

// Delete product
function deleteProduct($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
}
?>
