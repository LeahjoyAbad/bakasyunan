<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: forgot_password.php"); // Redirect if no email in session
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $email = $_SESSION['email'];

    // Hash the password for security
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the password in the database
    $conn = new mysqli("localhost", "root", "", "bakasyunan_bcp");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);
    $stmt->execute();

    // Clear the session after successful password reset
    unset($_SESSION['email']);

    header("Location: login.php"); // Redirect to login page after password reset
    exit();
}
?>

<!-- HTML form for reset password -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<style>
        body { font-family: Arial, sans-serif; background-color: #34495e; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; flex-direction: column; }
        .container { background-color: #0d1117; padding: 20px; border-radius: 8px;  width: 300px; text-align: center; }
        h2 { margin-bottom: 20px; color: #ccc; }
        label { display: block; text-align: left; margin: 10px 0 5px; font-weight: bold; color: #ccc; }
        input { width: 90%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
        button { width: 95%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #0056b3; }
        .error { color: red; margin-bottom: 10px; }
        
    </style>
<body>
    
<div class="container">
<h2>Reset Your Password</h2>
    <form method="POST">
        <input type="password" name="new_password" placeholder="Enter new password" required>
         <input type="password" name="new_password" placeholder="Enter new password" required>
        <button type="submit">Reset Password</button>
    </form>
    </div>
</body>
</html>
