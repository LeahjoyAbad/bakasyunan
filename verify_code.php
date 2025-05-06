<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
$conn = new mysqli("localhost", "root", "", "bakasyunan_bcp");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the timezone
date_default_timezone_set('Asia/Manila');

$message = "";
$show_resend_button = false;

// Check if the code is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_code'])) {
    $code = trim($_POST['verify_code']);

    if (!empty($code) && strlen($code) == 6 && ctype_digit($code)) {
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires > NOW()");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($email);
            $stmt->fetch();
            $_SESSION['email'] = $email;

            // Prevent reuse of the token
            $delStmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $delStmt->bind_param("s", $code);
            $delStmt->execute();
            $delStmt->close();

            // Redirect to reset password page if the code is valid
            header("Location: reset_password.php");
            exit();
        } else {
            $message = "Invalid or expired verification code.";
            $show_resend_button = true;
        }
        $stmt->close();
    } else {
        $message = "Please enter a valid 6-digit code.";
        $show_resend_button = true;
    }
}

// Resend code only if incorrect or expired
if (isset($_POST['resend_code'])) {
    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        $token = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        // Insert or update the token
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires) 
                                VALUES (?, ?, ?) 
                                ON DUPLICATE KEY UPDATE token = VALUES(token), expires = VALUES(expires)");
        $stmt->bind_param("sss", $email, $token, $expires);
        $stmt->execute();
        $stmt->close();

        require 'vendor/autoload.php';
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
            $mail->Body = "Your new verification code is: <b>$token</b>";
            $mail->send();
            $message = "A new verification code has been sent to your email.";
            $show_resend_button = false;
        } catch (Exception $e) {
            $message = "Error: Could not send the email. " . $mail->ErrorInfo;
        }
    } else {
        $message = "Session expired. Please request a new reset link.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    body { font-family: Arial, sans-serif; background-color: #34495e; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; flex-direction: column; }
    .container { background-color: #0d1117; padding: 20px; border-radius: 8px; width: 300px; text-align: center; }
    h2 { margin-bottom: 20px; color: #ccc; }
    label { display: block; text-align: left; margin: 10px 0 5px; font-weight: bold; color: #ccc; }
    input { width: 90%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
    button { width: 95%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
    button:hover { background-color: #0056b3; }
    .error { color: red; margin-bottom: 10px; }
</style>
<body>
    <div class="container">
        <h2>Verify Your Code</h2>
        <form method="POST" action="verify_code.php">
            <input type="text" name="verify_code" placeholder="Enter the 6-digit code" required>
            <button type="submit">Verify Code</button>
        </form>
        <p><?php echo $message; ?></p>

        <?php if ($show_resend_button): ?>
            <form method="POST" action="verify_code.php">
                <button type="submit" name="resend_code">Resend Code</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
