<?php
// delete_account.php
// Handles user account deletion
session_start();

// Example: check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Process deletion when confirmed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Example DB connection
    // $conn = new mysqli('localhost', 'root', '', 'mydb');
    // $userId = $_SESSION['user_id'];
    // $conn->query("DELETE FROM users WHERE id = $userId");

    // Destroy session
    session_destroy();

    // Redirect to goodbye page
    header('Location: goodbye.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Delete Account</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #FFF8F2; /* Light Cream */
        color: #222222; /* Dark Grey */
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
    }

    .container {
        background: #FFFFFF; /* White */
        width: 90%;
        max-width: 450px;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-align: center;
    }

    h2 {
        color: #E65100; /* Deep Orange */
        margin-bottom: 15px;
    }

    p {
        color: #222222;
        margin-bottom: 25px;
    }

    .btn-danger {
        background: #DC3545; /* Red */
        color: #fff;
        padding: 12px 20px;
        border: none;
        width: 100%;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        margin-bottom: 15px;
    }

    .btn-danger:hover {
        background: #b52a37;
    }

    .btn-cancel {
        background: #FF9800; /* Pending Orange */
        color: #fff;
        padding: 12px 20px;
        border: none;
        width: 100%;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
    }

    .btn-cancel:hover {
        background: #e08600;
    }

</style>
</head>
<body>

<div class="container">
    <h2>Delete Your Account</h2>
    <p>Are you sure you want to permanently delete your account? This action cannot be undone.</p>

    <form method="POST">
        <button type="submit" class="btn-danger">Yes, Delete My Account</button>
    </form>

    <button onclick="window.location.href='profile.php'" class="btn-cancel">Cancel</button>
</div>

</body>
</html>
