<!-- inc/footer.php -->

<footer class="rose-footer">

  <!-- =========================
      🌹 MAJOR FOOTER
  ========================== -->
  <div class="container-fluid px-4 px-md-5 py-5 text-white">
    <div class="row g-4 align-items-start">

      <!-- Logo / About -->
      <div class="col-md-3">
        <div class="footer-logo mb-3">
          <img src="assets/favicon.png" alt="Rose Store Logo">
        </div>
        <ul class="list-unstyled footer-links">
          <li><a href="about.php">About Us</a></li>
          <li><a href="buying_in_rose.php">Buying on Rose Store</a></li>
          <li><a href="payment_options.php">Payment Options</a></li>
          <li><a href="more_info.php">More Info</a></li>
        </ul>
      </div>

      <!-- Make Money -->
      <div class="col-md-3">
        <h5 class="footer-title">Make Money</h5>
        <ul class="list-unstyled footer-links">
          <li><a href="sell_on_rosestore.php">Sell on Rose Store</a></li>
          <li><a href="vendor_hub.php">Vendor Hub</a></li>
          <li><a href="affiliate.php">Affiliate Program</a></li>
          <li><a href="contact.php">Contact Us</a></li>
          <li><a href="legal_terms.php">Legal Terms</a></li>
          <li><a href="privacy_cookie.php">Privacy and Cookie Policy</a></li>
        </ul>
      </div>

      <!-- Newsletter -->
      <div class="col-md-4">
        <h5 class="footer-title">Stay Updated 💌</h5>
        <p class="small text-light opacity-75">
          Subscribe to receive updates on exclusive offers and new arrivals.
        </p>

        <form id="newsletter-form" class="newsletter-form">
          <input type="email" class="form-control mb-2" name="email" placeholder="Enter your email" required>
          <button type="submit" class="btn btn-rose w-100 mb-2">
            Subscribe
          </button>

          <div class="small text-light newsletter-legal">
            <p class="mb-1">
              Agree to Rose Store’s <a href="privacy_cookie.php">Privacy & Cookie Policy</a>.  
              You can unsubscribe at any time.
            </p>
            <label class="d-flex align-items-start gap-2">
              <input type="checkbox" name="accepted_terms" required>
              <span>I accept the <a href="legal_terms.php">Legal Terms</a></span>
            </label>
          </div>
        </form>
      </div>

      <!-- Social Networks -->
      <div class="col-md-2">
        <h5 class="footer-title">Our Social Networks</h5>
        <p class="small opacity-75">
          Follow Rose Store on our social channels for news, updates, and exclusive offers.
        </p>
        <div class="d-flex gap-3 fs-4">
          <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-x-twitter"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
        </div>
      </div>

    </div>
  </div>

  <!-- =========================
      🌸 MINOR FOOTER
  ========================== -->
  <div class="minor-footer">
    <div class="container-fluid px-4 px-md-5">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <!-- LEFT -->
        <div class="small order-2 order-md-1 text-center text-md-start">
          Designed by <strong>TEKVIK ICT</strong>
        </div>

        <!-- RIGHT -->
        <div class="small order-1 order-md-2 text-center text-md-end">
          &copy; <?= date('Y') ?> Rose Store. All rights reserved.
        </div>
      </div>
    </div>
  </div>

</footer>

<style>
/* =========================
   🌹 FOOTER CORE
========================= */
.rose-footer {
  background: linear-gradient(135deg, #4b006e, #6a1b9a, #e91e63);
  color: #fff;
  margin-top: auto;
}

/* Logo */
.footer-logo img {
  max-width: 80px;
  height: auto;
  border-radius: 50%;          
  background: var(--primary);  
  padding: 6px;                
  box-shadow: 0 6px 18px rgba(0,0,0,0.25);
}

/* Titles */
.footer-title {
  font-weight: 700;
  margin-bottom: 15px;
}

/* Links */
.footer-links li {
  margin-bottom: 8px;
}

.footer-links a {
  color: #ffd1f0;
  text-decoration: none;
  font-size: 0.95rem;
}

.footer-links a:hover {
  color: #fff;
}

/* =========================
   💌 NEWSLETTER
========================= */
.newsletter-form input {
  border-radius: 8px;
  border: none;
  padding: 10px;
}

.newsletter-form input:focus {
  box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.4);
}

.btn-rose {
  background: #e91e63;
  color: #fff;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-rose:hover {
  background: #ff5c9d;
}

/* =========================
   🌐 SOCIAL ICONS
========================= */
.social-icon {
  color: #fff;
  transition: transform 0.3s, color 0.3s;
}

.social-icon:hover {
  color: #ffd1f0;
  transform: translateY(-3px) scale(1.1);
}

/* =========================
   🌸 MINOR FOOTER
========================= */
.minor-footer {
  background: rgba(0,0,0,0.25);
  padding: 12px 0;
  border-top: 1px solid rgba(255,255,255,0.15);
}

.minor-footer .small {
  opacity: 0.85;
}

/* =========================
   📱 RESPONSIVE
========================= */
@media (max-width: 768px) {
  .minor-footer .d-flex {
    flex-direction: column;
    gap: 6px;
    text-align: center;
  }
}
</style>

<script>
// =========================
// Newsletter Subscription AJAX
// =========================
const form = document.getElementById('newsletter-form');
form.addEventListener('submit', function(e){
    e.preventDefault();
    const email = form.querySelector('input[name="email"]').value;
    const accepted_terms = form.querySelector('input[name="accepted_terms"]').checked;

    fetch('subscribe_newsletter.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body: `email=${encodeURIComponent(email)}&accepted_terms=${accepted_terms?1:0}`
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if(data.success) form.reset();
    })
    .catch(err => console.log(err));
});
</script>
