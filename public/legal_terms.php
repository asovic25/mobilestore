<?php
session_start();
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';
include __DIR__ . '/../inc/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Legal Terms | Rose Store</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root {
  --rose-deep: #5e2a84;
  --rose-medium: #8e44ad;
  --rose-light: #f6d9ff;
  --accent: #E91E63;
  --primary: #6A1B9A;
  --text-dark: #1c0033;
  --text-light: #fff;
  --background-light: #f3e5f5;
}

/* BASE */
body {
  font-family: 'Poppins', sans-serif;
  background: var(--background-light);
}

/* HERO */
.hero {
  background: linear-gradient(135deg,var(--primary),var(--accent));
  color: #fff;
  text-align: center;
  padding: 70px 20px;
  border-radius: 0 0 32px 32px;
}

/* SHADOW EFFECT */
.purple-shadow {
  box-shadow:
   -12px 0 30px rgba(138,43,226,.4),
    12px 0 30px rgba(138,43,226,.4);
  border-radius:20px;
  transition:.4s ease;
}
.purple-shadow:hover {
  transform: translateY(-12px);
  box-shadow:
   -18px 0 55px rgba(138,43,226,.6),
    18px 0 55px rgba(138,43,226,.6);
}

/* CARD STYLE */
.card h3 {
  color: var(--primary);
  font-weight: 600;
}
.card p, .card ul, .card ol {
  margin-top: 15px;
}

/* SCROLL REVEAL */
.reveal {
  opacity:0;
  transform:translateY(40px);
  transition:all .9s ease;
}
.reveal.active {
  opacity:1;
  transform:translateY(0);
}
</style>
</head>
<body>

<!-- HERO -->
<section class="hero reveal">
  <div class="container text-center">
    <h1 class="fw-bold">Legal Terms 🌹</h1>
    <p class="mt-3">Rules, responsibilities & agreements for using Rose Store</p>
  </div>
</section>

<!-- LEGAL TERMS CONTENT -->
<section class="container py-5 reveal">
  <div class="row g-4">

    <div class="col-12 reveal">
      <div class="card p-4 purple-shadow">
        <h3>1. Acceptance of Terms</h3>
        <p>
          By accessing Rose Store, you agree to comply with these legal terms and conditions. If you do not agree, please refrain from using our platform.
        </p>
      </div>
    </div>

    <div class="col-12 reveal">
      <div class="card p-4 purple-shadow">
        <h3>2. Account Responsibilities</h3>
        <ul>
          <li>Maintain the confidentiality of your account and password</li>
          <li>Provide accurate and current information</li>
          <li>Ensure all transactions comply with applicable laws</li>
        </ul>
      </div>
    </div>

    <div class="col-12 reveal">
      <div class="card p-4 purple-shadow">
        <h3>3. Intellectual Property</h3>
        <p>
          All content, logos, and designs on Rose Store are owned by us and protected by law. Unauthorized use is prohibited.
        </p>
      </div>
    </div>

    <div class="col-12 reveal">
      <div class="card p-4 purple-shadow">
        <h3>4. Limitation of Liability</h3>
        <p>
          Rose Store is not liable for any indirect, incidental, or consequential damages resulting from your use of the platform.
        </p>
      </div>
    </div>

    <div class="col-12 reveal">
      <div class="card p-4 purple-shadow">
        <h3>5. Governing Law</h3>
        <p>
          These terms are governed by the laws of Nigeria. Any disputes arising will be subject to the jurisdiction of the Nigerian courts.
        </p>
      </div>
    </div>

  </div>
</section>

<?php include __DIR__ . '/../inc/footer.php'; ?>

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
