<?php
session_start();
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/config.php';

include __DIR__ . '/../inc/head.php';
include __DIR__ . '/../inc/header.php'; // centralized header
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Become a Rose Affiliate | Rose Store</title>

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

/* Base */
body{
  font-family:'Poppins',sans-serif;
  background:var(--light);
}
/* HERO */
.hero {
  background: linear-gradient(90deg, var(--rose-deep), var(--accent));
  color: var(--text-light);
  padding: 50px 20px;
  text-align: center;
  border-radius: 0 0 32px 32px;
}
.hero h1 { font-weight: 800; }
.hero a.btn { font-weight: 700; }

/* INFO SECTION */
.info-section {
  background: #fff;
  padding: 40px;
  border-radius: 20px;
  box-shadow: -12px 0 30px rgba(138,43,226,.3), 12px 0 30px rgba(138,43,226,.3);
  transition: transform .4s ease, box-shadow .4s ease;
}

/* STEP CARDS */
.step {
  background: var(--hover-bg);
  border-radius: 20px;
  padding: 30px;
  text-align: center;
  transition: transform .32s ease, box-shadow .32s ease, background .3s ease;
}
.step:hover {
  background: var(--accent);
  color: var(--text-light);
  transform: translateY(-12px);
  box-shadow: -18px 0 46px rgba(138,43,226,.6), 18px 0 46px rgba(138,43,226,.6);
}
.step i { color: var(--primary); font-size: 2.5rem; margin-bottom: 12px; }
.step:hover i { color: #fff; }

/* BUTTONS */
.btn-rose {
  background: linear-gradient(90deg, var(--rose-medium), var(--rose-pink));
  border: none;
  color: var(--text-dark);
  font-weight: 600;
}
.btn-rose:hover {
  background: linear-gradient(90deg, var(--accent), var(--rose-gold));
  color: #000;
}

/* REVEAL ANIMATION */
.reveal {
  opacity: 0;
  transform: translateY(40px);
  transition: all .9s ease;
}
.reveal.active {
  opacity: 1;
  transform: translateY(0);
}
</style>
</head>

<body>

<!-- HERO -->
<section class="hero reveal">
  <div class="container">
    <h1>Become a Rose Affiliate 🌹</h1>
    <p class="lead mt-3">
      Earn with Rose Store — share products, build influence, and make money effortlessly.
    </p>
    <a href="<?= isset($_SESSION['user']) ? 'user_dashboard.php' : 'user_signup.php' ?>"
       class="btn btn-light mt-4 px-5 py-2 fw-bold">
       Join Now
    </a>
  </div>
</section>

<!-- CONTENT -->
<section class="py-5 reveal">
  <div class="container">
    <div class="info-section reveal">
      <h2 class="text-center mb-5 fw-bold text-purple">How It Works</h2>

      <div class="row g-4">
        <div class="col-md-4 reveal">
          <div class="step purple-shadow">
            <i class="fa fa-user-plus"></i>
            <h5>Register</h5>
            <p>Create your account and access the affiliate dashboard.</p>
          </div>
        </div>
        <div class="col-md-4 reveal">
          <div class="step purple-shadow">
            <i class="fa fa-upload"></i>
            <h5>Upload Products</h5>
            <p>Submit products for approval and get them live.</p>
          </div>
        </div>
        <div class="col-md-4 reveal">
          <div class="step purple-shadow">
            <i class="fa fa-coins"></i>
            <h5>Earn Money</h5>
            <p>Earn commission on every successful sale.</p>
          </div>
        </div>
      </div>

      <div class="text-center mt-5 reveal">
        <a href="<?= isset($_SESSION['user']) ? 'user_dashboard.php' : 'user_signup.php' ?>"
           class="btn btn-rose px-5 py-2">
           Start Earning Today
        </a>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../inc/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* SCROLL REVEAL */
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
