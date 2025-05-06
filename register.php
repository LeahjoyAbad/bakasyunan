
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

// Database connection
$conn = new mysqli("localhost", "root", "", "bakasyunan_bcp");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success_message = ""; 
$show_login_button = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password']; // User-defined password
    $role = $_POST['role']; 

    // Check if email already exists using prepared statements
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $success_message = "Email is already registered. Please use a different email.";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert into database using prepared statements
        $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hashedPassword, $role);

        if ($stmt->execute()) {
            // Send email using PHPMailer
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
                $mail->Subject = 'Registration Successful';
                $mail->Body = "<p>Your registration is successful!</p>
                              ";

                if ($mail->send()) {
                    $success_message = "Registration successful! Your password has been sent to your email.";
                    $show_login_button = true;
                } else {
                    $success_message = "Email could not be sent. Error: " . $mail->ErrorInfo;
                }
                
            } catch (Exception $e) {
                $success_message = "Email could not be sent. Error: " . $mail->ErrorInfo;
            }
        } else {
            $success_message = "Error: " . $conn->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
          body { font-family: Arial, sans-serif; background-color: #34495e; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; flex-direction: column;  background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
        url("BG.jpg");
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat; }
        .register-container { background-color: white; padding: 20px; border-radius: 8px; ; width: 300px; text-align: center; }
        h2 { margin-bottom: 20px; color: #black; }
        input, select { width: 90%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
        button { width: 95%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #218838; }
        .success-message { margin-top: 15px; color: #28a745; font-weight: bold; }
        .error-message { margin-top: 15px; color: #dc3545; font-weight: bold; }
        .login-button { margin-top: 15px; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; }
        .login-button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <form method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="Staff">Receptionist</option>
                <option value="Admin">Admin</option>
                <option value="Guest">Guest</option>
            </select>
            <button type="submit">Register</button>
        </form>
        <?php 
        if (!empty($success_message)) {
            echo "<p class='" . (strpos($success_message, "Error") !== false || strpos($success_message, "already registered") !== false ? "error-message" : "success-message") . "'>$success_message</p>";
        }
        if ($show_login_button) {
            echo "<a href='login.php' class='login-button'>GO TO LOGIN</a>";
        }
        ?>
    </div>
</body>
</html> 