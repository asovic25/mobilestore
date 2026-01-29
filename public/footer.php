<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rose Store Footer</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root {
  --primary: #6A1B9A;      /* Deep purple */
  --accent: #E91E63;       /* Rose pink */
  --light: #F3E5F5;
  --white: #ffffff;
  --dark: #1a1a1a;
}

/* Full-width footer */
footer {
  background: linear-gradient(135deg, var(--primary), var(--accent));
  color: var(--white);
  width: 100%;
  padding: 60px 0 40px;
  margin: 0;
  position: relative;
  overflow: hidden;
  box-shadow: 0 -2px 10px rgba(0,0,0,0.2);
}

/* Decorative top curve */
footer::before {
  content: "";
  position: absolute;
  top: -40px;
  left: 0;
  width: 100%;
  height: 40px;
  background: var(--accent);
  clip-path: ellipse(70% 100% at 50% 100%);
}

/* Typography */
footer h5 {
  color: var(--white);
  font-weight: 700;
  margin-bottom: 15px;
  position: relative;
}

footer h5::after {
  content: "";
  display: block;
  width: 40px;
  height: 3px;
  background: var(--accent);
  margin-top: 6px;
  border-radius: 10px;
}

footer ul {
  padding: 0;
  list-style: none;
}

footer ul li {
  margin-bottom: 8px;
}

footer a {
  color: var(--white);
  text-decoration: none;
  transition: all 0.3s ease;
}

footer a:hover {
  color: var(--accent);
  padding-left: 5px;
}

/* Social Icons */
.social-icon {
  color: var(--white);
  font-size: 1.3rem;
  transition: all 0.3s ease;
}

.social-icon:hover {
  color: var(--accent);
  transform: scale(1.2);
}

/* Responsive */
@media (max-width: 768px) {
  footer {
    text-align: center;
  }
  footer h5::after {
    margin: 8px auto 0;
  }
  .social-icon {
    font-size: 1.5rem;
  }
}
</style>
</head>
<body>

<!-- 🌹 Purple Rose Footer -->
<footer>
  <div class="container-fluid px-5 text-center text-md-start">
    <div class="row">
      <div class="col-md-3 mb-4">
        <h5>Rose Store 🌹</h5>
        <ul>
          <li><a href="about.php">About Us</a></li>
          <li><a href="buying_in_rose.php">Buying in Rose Store</a></li>
          <li><a href="payment_options.php">Payment Options</a></li>
          <li><a href="more_info.php">More Info</a></li>
        </ul>
      </div>

      <div class="col-md-3 mb-4">
        <h5>Make Money</h5>
        <ul>
          <li><a href="affiliate.php">Become an Affiliate</a></li>
          <li><a href="contact.php">Contact Us</a></li>
        </ul>
      </div>

      <div class="col-md-3 mb-4">
        <h5>Connect With Us</h5>
        <div class="d-flex justify-content-center justify-content-md-start gap-3 mt-2">
          <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
        </div>
      </div>

      <div class="col-md-3 text-center mt-4 mt-md-0">
        <p class="mb-0">&copy; <?= date('Y') ?> <strong>Rose Store</strong>. All rights reserved.</p>
      </div>
    </div>
  </div>
</footer>

</body>
</html>
