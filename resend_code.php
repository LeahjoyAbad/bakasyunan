<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
$conn = new mysqli("localhost", "root", "", "bakasyunan_bcp");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Check if the code exists in the URL or session
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Check if a code was sent earlier
    $sql = "SELECT * FROM password_resets WHERE email = '$email' AND expires > NOW()";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Code is still valid
        $message = "Your code is still valid. Please enter it to reset your password.";
    } else {
        // Code has expired, generate a new one
        $token = bin2hex(random_bytes(50)); // Generate new token
        $expires = date("Y-m-d H:i:s", strtotime("+1 minute")); // Set expiration for 1 minute

        // Update the database with the new token and expiration time
        $conn->query("UPDATE password_resets SET token = '$token', expires = '$expires' WHERE email = '$email'");

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
            $mail->Body = "Your previous code has expired. Click the link below to reset your password:<br>
                           <a href='http://localhost/verify_code.php?token=$token'>Verify Reset Code</a>";

            $mail->send();
            $message = "A new verification code has been sent to your email.";
        } catch (Exception $e) {
            $message = "Error: Could not send the email.";
        }
    }
} else {
    $message = "No email found. Please request a new verification code.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend Code</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #34495e;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }
        h2 {
            margin-bottom: 20px;
            color: #ccc;
        }
        label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            font-weight: bold;
            color: #ccc;
        }
        input {
            width: 90%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            width: 95%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
</style>
<body>
    <div class="container">
        <h2>Resend Verification Code</h2>
        <p><?php echo $message; ?></p>
        <a href="verify_code.php">Back to Verify Code</a>
    </div>
</body>
</html>
