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
<title>Privacy & Cookie Policy | Rose Store</title>

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
    <h1 class="fw-bold">Privacy & Cookie Policy 🌹</h1>
    <p class="mt-3">Your privacy and trust are our priority</p>
  </div>
</section>

<!-- PRIVACY CONTENT -->
<section class="container py-5 reveal">
  <div class="row g-4">

    <div class="col-12 reveal">
      <div class="card p-4 purple-shadow">
        <h3>1. Introduction</h3>
        <p>
          At Rose Store, we respect your privacy. This policy outlines how we collect, use, and protect your personal information while you interact with our platform.
        </p>
      </div>
    </div>

    <div class="col-12 reveal">
      <div class="card p-4 purple-shadow">
        <h3>2. Information We Collect</h3>
        <ul>
          <li>Account registration details (name, email, contact info)</li>
          <li>Order and payment information</li>
          <li>Device and browsing data for service improvement</li>
          <li>Marketing preferences</li>
        </ul>
      </div>
    </div>

    <div class="col-12 reveal">
      <div class="card p-4 purple-shadow">
        <h3>3. Cookies</h3>
        <p>
          We use cookies to enhance your experience, remember preferences, and analyze website traffic. You can disable cookies via your browser settings, but some features may not function properly.
        </p>
      </div>
    </div>

    <div class="col-12 reveal">
      <div class="card p-4 purple-shadow">
        <h3>4. How We Use Your Data</h3>
        <ul>
          <li>To process orders and payments</li>
          <li>To provide customer support</li>
          <li>To send newsletters and marketing updates (if subscribed)</li>
          <li>To improve our website and services</li>
        </ul>
      </div>
    </div>

    <div class="col-12 reveal">
      <div class="card p-4 purple-shadow">
        <h3>5. Data Protection</h3>
        <p>
          We implement strict security measures to protect your personal information. Access is limited to authorized personnel only.
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
