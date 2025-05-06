<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
    
</head>

<body><?php
session_start();
require_once __DIR__ . '/User.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = User::login($email, $password);
    if ($user) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["role"] = $user["role"];
        header("Location: /Bakasyunan/dash.php");
        exit();
    } else {
        echo "Invalid credentials.";
    }
}
?>

<form method="POST">

    <input type="email" name="email" required placeholder="Email">
    <input type="password" name="password" required placeholder="Password">
    <button type="submit">Login</button>
</form>
</body>
</html>

