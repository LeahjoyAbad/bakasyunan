<?php
require_once __DIR__ . '/database.php';
class User {
    public static function login($email, $password) {
        $conn = new mysqli("localhost", "root", "", "bakasyunan_bcp");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $email = $conn->real_escape_string($email);
        $password = $conn->real_escape_string($password); // Remove this if storing hashed passwords

        $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Check password (remove password_verify if not hashing passwords)
            if ($password === $user["password"]) {
                return $user;
            }
        }

        return false;
    }
}
