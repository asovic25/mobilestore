<?php
session_start();
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$searchQuery = $_GET['search'] ?? '';
include __DIR__ . '/../inc/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us | Rose Store</title>

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
}


/* BASE */
body{
  font-family:'Poppins',sans-serif;
  background:var(--light);
}

/* HERO */
.hero{
  background:linear-gradient(135deg,var(--primary),var(--accent));
  color:#fff;
  text-align:center;
  padding:70px 20px;
  border-radius:0 0 32px 32px;
}

/* SHADOW EFFECT */
.purple-shadow{
  box-shadow:
   -12px 0 30px rgba(138,43,226,.4),
    12px 0 30px rgba(138,43,226,.4);
  border-radius:20px;
  transition:.4s ease;
}
.purple-shadow:hover{
  transform:translateY(-12px);
  box-shadow:
   -18px 0 55px rgba(138,43,226,.6),
    18px 0 55px rgba(138,43,226,.6);
}
/* TEAM CARD */
.team-card{
  background:#fff;
  border-radius:22px;
  padding:40px 25px;
  text-align:center;
  height:100%;
  box-shadow:
   -12px 0 30px rgba(138,43,226,.4),
    12px 0 30px rgba(138,43,226,.4);
  transition:.4s ease;
}
.team-card:hover{
  transform:translateY(-12px);
  box-shadow:
   -18px 0 55px rgba(138,43,226,.6),
    18px 0 55px rgba(138,43,226,.6);
}

/* TEAM IMAGE */
.team-img{
  width:130px;
  height:130px;
  object-fit:cover;
  border-radius:50%;
  border:6px solid var(--primary);
  margin-bottom:20px;
  box-shadow:0 12px 22px rgba(0,0,0,.25);
}

/* ANIMATION */
.reveal{
  opacity:0;
  transform:translateY(40px);
  transition:all .9s ease;
}
.reveal.active{
  opacity:1;
  transform:translateY(0);
}

.text-purple{color:var(--primary);}
</style>
</head>

<body>

<!-- HERO -->
<section class="hero reveal">
  <div class="container">
    <h1 class="fw-bold">About Rose Store 🌹</h1>
    <p class="mt-3">Luxury, quality & trust — redefining online shopping.</p>
  </div>
</section>

<!-- ABOUT -->
<section class="container py-5 reveal">
  <h2 class="text-purple mb-3">Who We Are</h2>
  <p>
    Rose Store is a modern e-commerce platform built to deliver elegance,
    affordability, and reliability. We provide customers with carefully
    selected products across fashion, electronics, beauty and lifestyle.
  </p>
  <p>
    Our platform combines technology, simplicity, and premium design to
    make online shopping seamless and enjoyable.
  </p>
</section>

<!-- VISION & MISSION -->
<section class="container py-5">
  <div class="row g-4">

    <div class="col-md-6 reveal">
      <div class="card p-4 h-100 purple-shadow">
        <h3 class="text-purple">Our Vision</h3>
        <p>
          To become one of Africa’s most trusted and stylish online
          marketplaces, empowering customers through technology and innovation.
        </p>
      </div>
    </div>

    <div class="col-md-6 reveal">
      <div class="card p-4 h-100 purple-shadow">
        <h3 class="text-purple">Our Mission</h3>
        <ul>
          <li>Deliver quality products at competitive prices</li>
          <li>Ensure seamless and secure shopping</li>
          <li>Build customer trust and loyalty</li>
          <li>Continuously improve our services</li>
        </ul>
      </div>
    </div>

  </div>
</section>

<!-- TEAM -->
<section class="container py-5">
  <div class="text-center mb-5 reveal">
    <h2 class="text-purple">Meet Our Team</h2>
    <p class="text-muted">The minds driving Rose Store forward</p>
  </div>

  <div class="row g-4 justify-content-center">

    <div class="col-lg-4 col-md-6 reveal">
      <div class="team-card">
        <img src="assets/images/team/ceo.jpg" class="team-img" alt="CEO">
        <h5 class="fw-bold">Victor Nnamdi Asogwa</h5>
        <p class="text-purple">Founder, CEO & Creator</p>
        <p class="text-muted small">
          Visionary leader responsible for product strategy, platform
          architecture, and brand growth.
        </p>
      </div>
    </div>

    <div class="col-lg-4 col-md-6 reveal">
      <div class="team-card">
        <img src="assets/images/team/procurement.jpg" class="team-img" alt="Procurement">
        <h5 class="fw-bold">Amara Precious</h5>
        <p class="text-purple">Head of Procurement</p>
        <p class="text-muted small">
          Manages product sourcing, supplier relations, and quality control
          to guarantee dependable offerings.
        </p>
      </div>
    </div>

    <div class="col-lg-4 col-md-6 reveal">
      <div class="team-card">
        <img src="assets/images/team/operations.png" class="team-img" alt="Operations">
        <h5 class="fw-bold">Daniel Okeke</h5>
        <p class="text-purple">Operations & Customer Experience Lead</p>
        <p class="text-muted small">
          Oversees fulfillment, customer satisfaction and smooth daily
          operations across the platform.
        </p>
      </div>
    </div>

  </div>
</section>

<?php include __DIR__ . '/../inc/footer.php'; ?>

<script>
/* SIMPLE SCROLL ANIMATION */
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
