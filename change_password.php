

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
</head>
<?php
session_start();
require_once __DIR__ . '/database.php';

if (!isset($_SESSION["user_id"])) {
    echo "You must be logged in to change your password.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    if ($new_password !== $confirm_password) {
        echo "New passwords do not match.";
    } else {
        // Fetch current password from the database
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $current_password === $user["password"]) { // If using hashing, replace with password_verify()
            // Update the password in the database
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt->execute([$new_password, $user_id])) {
                echo "Password changed successfully!";
            } else {
                echo "Error updating password.";
            }
        } else {
            echo "Current password is incorrect.";
        }
    }
}
?>
<body>
    <form method="POST">
        <input type="password" name="current_password" placeholder="Current Password" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
        <button type="submit">Change Password</button>
    </form>
</body>
</html>
