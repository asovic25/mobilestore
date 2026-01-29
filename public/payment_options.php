<?php
session_start();
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';

/* Shared Navbar */
include __DIR__ . '/../inc/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Payment Options | RoseStore 🌹</title>

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
  padding: 70px 20px;
  text-align: center;
  color: var(--text-light);
  background: linear-gradient(135deg, var(--primary), var(--accent));
  border-radius: 0 0 32px 32px;
}

/* PURPLE SHADOW EFFECT */
.purple-shadow {
  background: #fff;
  border-radius: 20px;
  padding: 25px;
  box-shadow: -12px 0 30px rgba(138,43,226,.4), 12px 0 30px rgba(138,43,226,.4);
  transition: transform .4s ease, box-shadow .4s ease;
}
.purple-shadow:hover {
  transform: translateY(-12px);
  box-shadow: -18px 0 55px rgba(138,43,226,.6), 18px 0 55px rgba(138,43,226,.6);
}

/* PAYMENT CARD */
.payment-card {
  border-radius: 20px;
  text-align: center;
  padding: 30px 25px;
  box-shadow: -12px 0 30px rgba(138,43,226,.3), 12px 0 30px rgba(138,43,226,.3);
  transition: transform .32s ease, box-shadow .32s ease;
}
.payment-card:hover {
  transform: translateY(-8px);
  box-shadow: -18px 0 46px rgba(138,43,226,.4), 18px 0 46px rgba(138,43,226,.4);
}
.payment-card h5 { color: var(--primary); font-weight:700; }

/* ICON CIRCLE */
.icon-circle {
  width: 74px;
  height: 74px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  font-size: 28px;
  background: linear-gradient(180deg, rgba(142,68,173,0.08), rgba(142,68,173,0.03));
  color: var(--primary);
}

/* Text */
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


</style>
</head>

<body>

<!-- HERO -->
<section class="hero reveal">
  <div class="container">
    <h1 class="fw-bold">Payment Options</h1>
    <p class="lead mt-3">Secure, flexible and convenient payment methods you can trust.</p>
  </div>
</section>

<!-- CONTENT -->
<main class="container py-5">

  <div class="row g-4">
    <?php
    $payments = [
      ['fa-university','Online Bank Transfer','Pay securely through supported Nigerian banks with instant confirmation.'],
      ['fa-credit-card','Debit & Credit Cards','Visa, Mastercard, Verve and more — protected with 3D Secure encryption.'],
      ['fa-mobile-screen-button','Mobile Wallets','Use Paystack, Flutterwave and supported mobile wallet options.'],
      ['fa-hand-holding-dollar','Cash on Delivery','Available in selected locations — pay only when your item arrives.'],
      ['fa-calendar-check','Installment Plans','Buy now and spread payments over time with our finance partners.'],
      ['fa-shield-halved','Secure Checkout','SSL-encrypted checkout ensures your payment and personal data stay safe.'],
    ];

    foreach($payments as $p):
    ?>
      <div class="col-md-6 col-lg-4 reveal">
        <div class="payment-card h-100 purple-shadow">
          <div class="icon-circle"><i class="fa-solid <?= $p[0] ?>"></i></div>
          <h5><?= htmlspecialchars($p[1]) ?></h5>
          <p class="text-muted"><?= htmlspecialchars($p[2]) ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="text-center mt-5 reveal">
    <h3 class="text-purple fw-bold">Your Payment, Our Promise 🌹</h3>
    <p class="text-muted">Transparent, secure and stress-free payments — every time you shop.</p>
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
