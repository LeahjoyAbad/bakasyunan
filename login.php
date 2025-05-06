<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "bakasyunan_bcp");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";

// Check if the form is submitted  
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($email) && !empty($password)) {
        // Check user credentials securely
        $stmt = $conn->prepare("SELECT user_id, role, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // Verify password
            if (password_verify($password, $user["password"])) {
                $_SESSION["user_id"] = $user["id"]; // Correct session assignment
                $_SESSION["role"] = $user["role"];
                header("Location: dash.php"); // Redirect to the dashboard
                exit();
            } else {
                $error_message = "Incorrect password. Please try again.";
            }
        } else {
            $error_message = "Invalid credentials. <br> Don't have an account? <a href='register.php' class='register-link'>Create one here</a>.";
        }
        $stmt->close();
    } else {
        $error_message = "Please enter both email and password.";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #34495e; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; flex-direction: column;  background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
        url("BG.jpg");
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat; }
        .login-container { background-color: white; padding: 20px; border-radius: 8px;  width: 300px; text-align: center; }
        h2 { margin-bottom: 20px; color: black; }
        label { display: block; text-align: left; margin: 10px 0 5px; font-weight: bold; color: black; }
        input { width: 90%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
        button { width: 95%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #0056b3; }
        .error { color: red; margin-bottom: 10px; }
        .register-link { color: #00c8ff; text-decoration: none; }
        .register-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($error_message)) echo "<p class='error'>$error_message</p>"; ?>
        <form method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="Enter your email">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Enter your password">
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php" class="register-link">Create Account</a>.</p>
        <p> <a href="forgot_password.php" class="register-link">Forgot Password</a>.</p>
    </div>
</body>
</html>
