<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/fpdf/fpdf.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: user_login.php");
    exit;
}

$user = $_SESSION['user'];
$userId = $user['id'];

$orderId = $_GET['order_id'] ?? null;
if (!$orderId) die("No order selected.");

// Fetch order info
$stmt = $pdo->prepare("
    SELECT 
        o.id,
        o.quantity,
        o.total_price,
        o.status,
        o.created_at,
        o.product_name,
        p.name AS product_name_live,
        p.description AS product_description,
        s.username AS seller_name,
        s.email AS seller_email
    FROM orders o
    LEFT JOIN user_products p ON o.product_id = p.id
    LEFT JOIN users s ON o.seller_id = s.id
    WHERE o.id = ? AND o.buyer_id = ?
    LIMIT 1
");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) die("Order not found or you do not have access to it.");

// Initialize PDF
$pdf = new FPDF();
$pdf->AddPage();

// Use standard Arial font (built-in)
$pdf->SetFont('Arial','B',16);

// Header
$pdf->SetFillColor(106,27,154); 
$pdf->SetTextColor(255,255,255);
$pdf->Cell(0,10,'ROSE STORE',0,1,'C', true);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'Invoice',0,1,'C');
$pdf->Ln(5);

// Invoice Info
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,7,'Invoice No: #'.$order['id'],0,1);
$pdf->Cell(0,7,'Invoice Date: '.date('d M Y, h:i A', strtotime($order['created_at'])),0,1);
$pdf->Ln(5);

// Buyer Details
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Billed To:',0,1);
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,7,'Name: '.($user['fullname'] ?? $user['username']),0,1);
$pdf->Cell(0,7,'Email: '.$user['email'],0,1);
$pdf->Ln(5);

// Seller Info
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Seller Info:',0,1);
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,7,'Seller: '.($order['seller_name'] ?? 'N/A'),0,1);
$pdf->Cell(0,7,'Email: '.($order['seller_email'] ?? 'N/A'),0,1);
$pdf->Ln(7);

// Product Table Header
$pdf->SetFont('Arial','B',11);
$pdf->SetFillColor(106,27,154);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80,8,'Product',1,0,'C', true);
$pdf->Cell(25,8,'Qty',1,0,'C', true);
$pdf->Cell(40,8,'Price',1,0,'C', true);
$pdf->Cell(35,8,'Status',1,1,'C', true);

// Product Table Row
$pdf->SetFont('Arial','',11);
$pdf->SetTextColor(0,0,0);
$productName = $order['product_name'] ?? $order['product_name_live'] ?? 'Product Removed';
$pdf->Cell(80,8,$productName,1);
$pdf->Cell(25,8,$order['quantity'],1,0,'C');
$pdf->Cell(40,8,'₦'.number_format($order['total_price'],2),1,0,'R');
$pdf->Cell(35,8,ucfirst($order['status']),1,1,'C');
$pdf->Ln(10);

// Grand Total
$pdf->SetFont('Arial','B',12);
$pdf->Cell(145,8,'Grand Total',1);
$pdf->Cell(35,8,'₦'.number_format($order['total_price'],2),1,1,'R');
$pdf->Ln(8);

// Footer
$pdf->SetFont('Arial','',10);
$pdf->MultiCell(0,7,"Thank you for shopping with ROSE STORE.\nFor support, contact us anytime.",0,'C');

// Output PDF
$pdf->Output('I', 'Invoice_Order_'.$orderId.'.pdf');
