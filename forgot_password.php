<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
$conn = new mysqli("localhost", "root", "", "bakasyunan_bcp");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email exists in the database
    $sql = "SELECT user_id FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Generate a 6-digit verification code
        $verification_code = mt_rand(100000, 999999);
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour")); // Set expiration time

        // Save the code and expiration in the database
        $conn->query("INSERT INTO password_resets (email, token, expires) VALUES ('$email', '$verification_code', '$expires')");

        // Send the reset link to the user's email
        require 'vendor/autoload.php'; // PHPMailer

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'abadleahjoy96@gmail.com';
            $mail->Password = 'spimztlribsgtfrb';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('abadleahjoy96@gmail.com', 'Bakasyunan_Resort');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Your verification code is: <b>$verification_code</b>";

            $mail->send();

            // Store the email in session and redirect to verify_code.php
            $_SESSION['email'] = $email;
            header("Location: verify_code.php");
            exit();

        } catch (Exception $e) {
            $message = "Error: Could not send the email.";
        }
    } else {
        $message = "No account found with this email.";
    }
}
?>

<!-- HTML form for forgot password -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
<h2>Forgot Password</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Send Reset Code</button>
    </form>
    <p><?php echo $message; ?></p>
    </div>
</body>
</html>
