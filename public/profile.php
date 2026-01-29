<?php
session_start();

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Example DB Fetch
// $conn = new mysqli('localhost', 'root', '', 'mydb');
// $userId = $_SESSION['user_id'];
// $user = $conn->query("SELECT * FROM users WHERE id=$userId")->fetch_assoc();

// Dummy Example Data (Replace with DB values)
$user = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+234 810 000 0000',
    'avatar' => 'uploads/default.png',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Profile</title>
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

body {
    font-family: Arial, sans-serif;
    background: var(--rose-light);
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    color: var(--text-dark);
}

.profile-card {
    background: var(--text-light);
    width: 90%;
    max-width: 450px;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    text-align: center;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--rose-medium);
    margin-bottom: 15px;
}

h2 {
    color: var(--primary);
    margin-bottom: 5px;
}

p {
    margin: 8px 0;
    font-size: 16px;
}

.label {
    font-weight: bold;
    color: var(--rose-deep);
}

.btn {
    display: block;
    width: 100%;
    padding: 12px;
    margin-top: 15px;
    background: var(--primary);
    color: var(--text-light);
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 16px;
    transition: 0.3s;
}
.btn:hover {
    background: var(--accent);
}

.btn-delete {
    background: #b00020;
}
.btn-delete:hover {
    background: #7a0016;
}

</style>
</head>
<body>

<div class="profile-card">

    <img src="<?php echo $user['avatar']; ?>" class="profile-avatar" alt="Profile Picture">

    <h2><?php echo $user['name']; ?></h2>

    <p><span class="label">Email:</span> <?php echo $user['email']; ?></p>
    <p><span class="label">Phone:</span> <?php echo $user['phone']; ?></p>

    <button class="btn" onclick="window.location.href='edit_profile.php'">Edit Profile</button>
    <button class="btn btn-delete" onclick="window.location.href='delete_account.php'">Delete Account</button>
    <button class="btn" style="background: var(--rose-gold); color: var(--text-dark);" onclick="window.location.href='index.php'">Back to Home</button>
</div>

</body>
</html>