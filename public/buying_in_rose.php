<?php
// buying_in_rose.php
session_start();
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';

// Use the centralized header for navbar/category menu
include __DIR__ . '/../inc/header.php';

// Fetch categories for navbar (header.php may already handle this)
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$searchQuery = $_GET['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Buying in RoseStore | RoseStore 🌹</title>

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

/* Base */
body{
  font-family:'Poppins',sans-serif;
  background:var(--light);
}

/* HERO */
.hero {
  background: linear-gradient(135deg, var(--primary), var(--accent));
  color: var(--text-light);
  text-align: center;
  padding: 70px 20px;
  border-radius: 0 0 32px 32px;
}
.hero .divider {
  width: 70px;
  height: 4px;
  background: var(--primary);
  margin: 12px auto 20px;
  border-radius: 3px;
}

/* PURPLE SHADOW EFFECT */
.purple-shadow {
  background: #fff;
  border-radius: 20px;
  padding: 30px 25px;
  box-shadow: -12px 0 30px rgba(138,43,226,.4), 12px 0 30px rgba(138,43,226,.4);
  transition: transform .4s ease, box-shadow .4s ease;
}
.purple-shadow:hover {
  transform: translateY(-12px);
  box-shadow: -18px 0 55px rgba(138,43,226,.6), 18px 0 55px rgba(138,43,226,.6);
}

/* FEATURE CARD */
.feature-card {
  border-radius: 20px;
  padding: 25px;
  text-align: center;
  box-shadow: -12px 0 30px rgba(138,43,226,.3), 12px 0 30px rgba(138,43,226,.3);
  transition: transform .32s ease, box-shadow .32s ease;
  background-color: #fff; /* changed to white */
}

.feature-card:hover {
  transform: translateY(-8px);
  box-shadow: -18px 0 46px rgba(138,43,226,.4), 18px 0 46px rgba(138,43,226,.4);
}
.feature-card h5 { color: var(--primary); font-weight:700; }

/* SUPPORT CARD */
.support-card {
  border-radius: 20px;
  padding: 25px;
  text-align: center;
  box-shadow: -12px 0 30px rgba(138,43,226,.3), 12px 0 30px rgba(138,43,226,.3);
  transition: transform .32s ease, box-shadow .32s ease;
  background-color: #fff; /* changed to white */
}

.support-card:hover {
  transform: translateY(-8px);
  box-shadow: -18px 0 46px rgba(138,43,226,.4), 18px 0 46px rgba(138,43,226,.4);
}

.support-card h5 { color: var(--primary); font-weight:700; }

/* TEXT COLORS */
.text-purple { color: var(--primary); }

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

/* Accordion */
.accordion-button:not(.collapsed) { color: var(--primary); font-weight:600; }

</style>
</head>
<body>

<!-- HERO -->
<section class="hero reveal">
  <div class="container">
    <h1 class="fw-bold">Buying in RoseStore 🌹</h1>
    <div class="divider"></div>
    <p class="lead mt-3">Effortless shopping, genuine products, and fast delivery — all in one place.</p>
  </div>
</section>

<!-- CONTENT -->
<main class="container py-5">

  <!-- Why Shop Section -->
  <div class="text-center mb-4 reveal">
    <h2 class="text-purple fw-bold">Why Shop with Us</h2>
    <div style="height:3px;width:70px;background:var(--primary);margin:10px auto 0;border-radius:2px;"></div>
  </div>

  <div class="row g-4">
    <?php
    $features = [
      ['Fast & Reliable Delivery', 'Receive your orders quickly and safely at your doorstep. Trusted logistics partners ensure timely deliveries.'],
      ['Quality Products Guaranteed', 'We source products from verified suppliers and inspect each item before listing.'],
      ['Secure Payments', 'Your payment safety is our priority; we use secure, encrypted gateways.'],
      ['Customer-Centric Support', 'Our friendly support team is always ready to help with queries or issues.'],
      ['Affordable Pricing', 'Competitive prices so you get value for every naira spent.'],
      ['Hassle-Free Returns', 'Easy returns and exchanges — we make it simple if something isn’t right.'],
    ];
    foreach ($features as $f):
    ?>
      <div class="col-12 col-md-6 col-lg-4 reveal">
        <div class="feature-card h-100">
          <div class="mb-3" style="width:70px;height:70px;margin:0 auto;border-radius:12px;display:flex;align-items:center;justify-content:center;background:linear-gradient(180deg, rgba(138,68,173,0.08), rgba(138,68,173,0.03));color:var(--primary);font-size:24px;">
            <i class="fa-solid fa-check"></i>
          </div>
          <h5><?= htmlspecialchars($f[0]) ?></h5>
          <p class="text-muted"><?= htmlspecialchars($f[1]) ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- FAQs -->
  <div class="row mt-5 reveal">
    <div class="col-lg-8 mx-auto">
      <div class="accordion" id="faqAccordion">
        <div class="accordion-item">
          <h2 class="accordion-header" id="faq1Header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true">
              How long does delivery take?
            </button>
          </h2>
          <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
            <div class="accordion-body">Delivery typically takes 2–5 business days depending on location. You will receive tracking information once shipped.</div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="faq2Header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
              What payment methods are accepted?
            </button>
          </h2>
          <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body">We accept cards (Visa, Mastercard, Verve), Paystack, Flutterwave, bank transfers and COD where available.</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Support Cards -->
  <div class="row g-4 mt-5">
    <div class="col-12 col-md-4 reveal">
      <div class="support-card h-100">
        <div class="mb-3"><i class="fa-solid fa-phone fa-2x text-purple"></i></div>
        <h5>Call Us</h5>
        <p class="text-muted">Speak directly with our customer care team: <br><strong>+234 703 883 5237</strong></p>
      </div>
    </div>
    <div class="col-12 col-md-4 reveal">
      <div class="support-card h-100">
        <div class="mb-3"><i class="fa-solid fa-envelope fa-2x text-purple"></i></div>
        <h5>Email Support</h5>
        <p class="text-muted">Send us your questions: <br><strong>support@rosestore.com</strong></p>
      </div>
    </div>
    <div class="col-12 col-md-4 reveal">
      <div class="support-card h-100">
        <div class="mb-3"><i class="fa-solid fa-comments fa-2x text-purple"></i></div>
        <h5>Live Chat</h5>
        <p class="text-muted">Use the chat widget on the site to connect with an agent in real-time.</p>
      </div>
    </div>
  </div>

  <div class="text-center mt-5 reveal">
    <h3 class="text-purple fw-bold">We’re Always Here for You</h3>
    <p class="lead text-muted">Your satisfaction is our priority — we handle every question and feedback with care.</p>
  </div>

</main>

<?php include __DIR__ . '/../inc/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* SCROLL ANIMATION */
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
