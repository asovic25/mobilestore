<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$accepted = $_POST['accepted_terms'] ?? 0;

if (!$email) {
    echo json_encode(['success'=>false,'message'=>'Invalid email address']);
    exit;
}

if (!$accepted) {
    echo json_encode(['success'=>false,'message'=>'You must accept the Legal Terms']);
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'yourgmail@gmail.com';
    $mail->Password   = 'your_app_password';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('yourgmail@gmail.com', 'Rose Store');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Welcome to Rose Store Newsletter 🌹';
    $mail->Body    = '
        <h2>Thank you for subscribing!</h2>
        <p>You will now receive the latest updates and offers from Rose Store.</p>
    ';

    $mail->send();

    echo json_encode(['success'=>true,'message'=>'Subscription successful!']);
} catch (Exception $e) {
    echo json_encode([
        'success'=>false,
        'message'=>'Mail error: '.$mail->ErrorInfo
    ]);
}
