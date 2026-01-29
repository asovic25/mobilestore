<?php
session_start();
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/config.php';

include __DIR__ . '/../inc/head.php';
include __DIR__ . '/../inc/header.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $subject = trim($_POST['subject'] ?? '');
  $message = trim($_POST['message'] ?? '');

  if ($name === '') $errors[] = 'Name is required.';
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
  if ($subject === '') $errors[] = 'Subject is required.';
  if ($message === '') $errors[] = 'Message cannot be empty.';

  $attachment_filename = null;
  if (!empty($_FILES['attachment']['name'])) {
    $attachment = $_FILES['attachment'];
    if ($attachment['error'] === UPLOAD_ERR_OK) {
      $allowed = ['png','jpg','jpeg','pdf','doc','docx'];
      $ext = strtolower(pathinfo($attachment['name'], PATHINFO_EXTENSION));
      if (!in_array($ext, $allowed)) $errors[] = 'Attachment must be png, jpg, jpeg, pdf, doc or docx.';
      if ($attachment['size'] > 5 * 1024 * 1024) $errors[] = 'Attachment must be less than 5MB.';
    } else {
      $errors[] = 'Attachment upload failed.';
    }
  }

  if (!$errors) {
    if (!empty($attachment) && $attachment['error'] === UPLOAD_ERR_OK) {
      $uploadDir = dirname(__DIR__) . '/uploads/contact/';
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
      $attachment_filename = 'contact_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
      move_uploaded_file($attachment['tmp_name'], $uploadDir . $attachment_filename);
    }

    $stmt = $pdo->prepare(
      "INSERT INTO contact_messages (name, email, subject, message, attachment)
       VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->execute([$name, $email, $subject, $message, $attachment_filename]);

    $success = '🌹 Thank you! Your message has been sent successfully.';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Contact Us | Rose Store 🌹</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
:root {
  --rose-deep: #5e2a84;
  --rose-medium: #8e44ad;
  --rose-light: #f6d9ff;
  --rose-pink: #ffb3d9;
  --rose-gold: #f3c623;
  --accent: #E91E63;
  --primary: #6A1B9A;
  --text-dark: #1c0033;
  --text-light: #fff;
  --hover-bg: #e8c6ff;
}

body {
  font-family: 'Poppins', sans-serif;
  background: var(--rose-light);
  color: var(--text-dark);
  margin: 0;
  line-height: 1.6;
}

/* HERO */
.hero {
  background: linear-gradient(135deg, var(--rose-deep), var(--accent));
  color: var(--text-light);
  text-align: center;
  padding: 70px 20px;
  border-radius: 0 0 35px 35px;
}
.hero h1 { font-weight: 800; }
.hero p { font-size: 1.2rem; }

/* CONTACT CARD */
.contact-card {
  background: #fff;
  border-radius: 20px;
  padding: 40px;
  box-shadow: 0 12px 35px rgba(94,42,132,.15);
  transition: transform .3s ease, box-shadow .3s ease;
}
.contact-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 18px 48px rgba(94,42,132,.25);
}

/* FORM ELEMENTS */
.form-control {
  border-radius: 12px;
  padding: 12px 15px;
}
.form-control:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 0.2rem rgba(138,43,226,.25);
}

/* BUTTON */
.btn-rose {
  background: linear-gradient(90deg, var(--rose-medium), var(--rose-pink));
  border: none;
  font-weight: 600;
  color: var(--text-dark);
  transition: all .3s ease;
}
.btn-rose:hover {
  background: linear-gradient(90deg, var(--accent), var(--rose-gold));
  color: #000;
}

/* ALERTS */
.alert {
  border-radius: 15px;
  font-weight: 500;
}
</style>
</head>

<body>

<!-- HERO -->
<section class="hero">
  <div class="container">
    <h1>Contact Us</h1>
    <p>We’d love to hear from you — send us a message and we’ll get back to you promptly.</p>
  </div>
</section>

<!-- CONTACT FORM -->
<main class="container py-5" style="max-width:720px;">
  <?php if ($errors): ?>
    <div class="alert alert-danger reveal">
      <ul class="mb-0">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert alert-success reveal"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <div class="contact-card reveal">
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input class="form-control" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" class="form-control" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Subject</label>
        <input class="form-control" name="subject" required value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Message</label>
        <textarea class="form-control" rows="5" name="message" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
      </div>

      <div class="mb-4">
        <label class="form-label">Attachment (optional)</label>
        <input type="file" class="form-control" name="attachment">
      </div>

      <button class="btn btn-rose w-100 py-2">
        <i class="fa fa-paper-plane me-2"></i>Send Message
      </button>
    </form>
  </div>
</main>

<?php include __DIR__ . '/../inc/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- SCROLL REVEAL EFFECT -->
<script>
const reveals = document.querySelectorAll('.reveal');
const revealOnScroll = () => {
  reveals.forEach(el => {
    const top = el.getBoundingClientRect().top;
    if(top < window.innerHeight - 60){
      el.classList.add('active');
    }
  });
};
window.addEventListener('scroll', revealOnScroll);
revealOnScroll();
</script>

</body>
</html>
