<?php
session_start();
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';

/* Data */
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$searchQuery = $_GET['search'] ?? '';

/* Shared Header */
include __DIR__ . '/../inc/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>More Info & Customer Support | RoseStore 🌹</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

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

/* Hero */
.hero {
  background: linear-gradient(135deg, var(--primary), var(--accent));
  color: var(--text-light);
  padding: 70px 20px;
  text-align: center;
  border-radius: 0 0 32px 32px;
}

/* Headings */
.text-purple { color: var(--primary); }

/* Accordion */
.accordion-button:not(.collapsed) {
  background: rgba(138,43,226,0.08);
  color: var(--primary);
  font-weight: 600;
}
.accordion-button { font-weight: 600; }

/* PURPLE SHADOW EFFECT */
.purple-shadow {
  background: #fff;
  border-radius: 20px;
  padding: 25px;
  box-shadow: -12px 0 30px rgba(138,43,226,.3), 12px 0 30px rgba(138,43,226,.3);
  transition: transform .4s ease, box-shadow .4s ease;
}
.purple-shadow:hover {
  transform: translateY(-12px);
  box-shadow: -18px 0 55px rgba(138,43,226,.6), 18px 0 55px rgba(138,43,226,.6);
}

/* Support Cards */
.support-card {
  border-radius: 20px;
  padding: 30px 25px;
  text-align: center;
  box-shadow: -12px 0 30px rgba(138,43,226,.3), 12px 0 30px rgba(138,43,226,.3);
  transition: transform .32s ease, box-shadow .32s ease;
}
.support-card:hover {
  transform: translateY(-10px);
  box-shadow: -18px 0 46px rgba(138,43,226,.5), 18px 0 46px rgba(138,43,226,.5);
}
.support-card h5 { color: var(--primary); font-weight:700; }

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
    <h1 class="fw-bold">Customer Support & Information</h1>
    <p class="lead mt-3">
      Your comfort is our priority — everything you need in one place.
    </p>
  </div>
</section>

<!-- MAIN -->
<main class="container py-5">

  <div class="text-center mb-4 reveal">
    <h2 class="fw-bold text-purple">Frequently Asked Questions</h2>
  </div>

  <!-- FAQ -->
  <div class="accordion reveal" id="faqAccordion">

    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
          How do I place an order?
        </button>
      </h2>
      <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Browse our store, add items to your cart, and proceed to checkout using any secure payment option.
        </div>
      </div>
    </div>

    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
          Can I return a product?
        </button>
      </h2>
      <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Yes. Eligible products can be returned within our return window. Please see our return policy for details.
        </div>
      </div>
    </div>

    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
          How long does delivery take?
        </button>
      </h2>
      <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Deliveries usually arrive within 2–5 working days depending on your location.
        </div>
      </div>
    </div>

  </div>

  <!-- SUPPORT -->
  <div class="row g-4 mt-5">
    <?php
    $supports = [
      ['fa-phone','Call Us','+234 703 883 5237'],
      ['fa-envelope','Email Support','support@rosestore.com'],
      ['fa-comments','Live Chat','Instant help from our support team'],
    ];
    foreach($supports as $s):
    ?>
      <div class="col-md-4 reveal">
        <div class="support-card purple-shadow">
          <i class="fa-solid <?= $s[0] ?> fa-2x text-purple mb-3"></i>
          <h5><?= htmlspecialchars($s[1]) ?></h5>
          <p><?= htmlspecialchars($s[2]) ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="text-center mt-5 reveal">
    <h3 class="text-purple fw-bold">We’re Always Here for You 🌹</h3>
    <p class="text-muted">
      Rose Store is committed to seamless, secure, and delightful shopping experiences.
    </p>
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
