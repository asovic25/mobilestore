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
<title>Vendor Hub | Rose Store</title>

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
  font-family:'Poppins',sans-serif;
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

/* CARD STYLING */
.card h3 {
  color: var(--primary);
  font-weight: 600;
}

/* TEAM CARD IMAGE STYLE (optional if you add images) */
.team-img {
  width:130px;
  height:130px;
  object-fit:cover;
  border-radius:50%;
  border:6px solid var(--primary);
  margin-bottom:20px;
  box-shadow:0 12px 22px rgba(0,0,0,.25);
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
    <h1 class="fw-bold">Vendor Hub 🌹</h1>
    <p class="mt-3">Everything you need to grow your business on Rose Store</p>
  </div>
</section>

<!-- VENDOR HUB INFO -->
<section class="container py-5 reveal">
  <div class="row g-4">

    <div class="col-md-6 reveal">
      <div class="card p-4 h-100 purple-shadow">
        <h3>Why Sell on Rose Store?</h3>
        <ul class="mt-3">
          <li>Reach thousands of daily shoppers</li>
          <li>Simple product listing system</li>
          <li>Secure payments & order tracking</li>
          <li>Vendor-friendly dashboard</li>
          <li>Marketing exposure & promotions</li>
        </ul>
      </div>
    </div>

    <div class="col-md-6 reveal">
      <div class="card p-4 h-100 purple-shadow">
        <h3>Who Can Join?</h3>
        <ul class="mt-3">
          <li>Small & large business owners</li>
          <li>Retailers & wholesalers</li>
          <li>ICT & electronics vendors</li>
          <li>Fashion, beauty & lifestyle sellers</li>
        </ul>
      </div>
    </div>

    <div class="col-md-6 reveal">
      <div class="card p-4 h-100 purple-shadow">
        <h3>How It Works</h3>
        <ol class="mt-3">
          <li>Create a seller account</li>
          <li>Upload your products</li>
          <li>Receive orders from customers</li>
          <li>Deliver & get paid</li>
        </ol>
      </div>
    </div>

    <div class="col-md-6 reveal">
      <div class="card p-4 h-100 text-center purple-shadow">
        <h3>Get Started Today</h3>
        <p class="mt-3">Join Rose Store Vendor Hub and grow your business with ease.</p>
        <a href="user_signup.php" class="btn btn-primary mt-3">Become a Vendor 🌹</a>
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
