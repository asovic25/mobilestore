

<?php
// inc/head.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="Rose Store">
<title>Rose Store</title>
   <!-- favicon -->
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/favicon.png">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Custom CSS -->
<style>
:root {
  --primary:#6A1B9A;
  --accent:#E91E63;
  --secondary:#F3E5F5;
  --rose-light:#f6d9ff;
  --text-dark:#1c0033;
  --white:#fff;
}

body {
  font-family: 'Poppins', sans-serif;
  background-color: var(--secondary);
  color: var(--text-dark);
  margin: 0;
  padding: 0;
  transition: all 0.3s;
}

a { text-decoration: none; transition: all 0.3s; }
a:hover { color: var(--accent); }

h1,h2,h3,h4,h5,h6 { color: var(--primary); }

.btn-rose {
  background-color: var(--primary);
  color: var(--white);
  font-weight: 600;
  border: none;
  transition: all 0.3s;
}
.btn-rose:hover {
  background-color: #5A137F;
  color: var(--accent);
}

.text-primary { color: var(--primary) !important; }
.text-accent { color: var(--accent) !important; }

.container { max-width: 1200px; }

/* Timeline for order tracking */
.timeline {
  border-left: 4px solid var(--primary);
  padding-left: 20px;
  margin-left: 10px;
}
.timeline-item {
  margin-bottom: 25px;
}
.timeline-item span {
  background: var(--primary);
  color: var(--white);
  padding: 5px 12px;
  border-radius: 12px;
  font-size: 0.85rem;
}
</style>
</head>
<body>
