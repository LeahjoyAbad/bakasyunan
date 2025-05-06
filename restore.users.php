<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "bakasyunan_bcp"; // Ensure the correct database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Backup file name (latest backup)
$filename = "backup_users_" . date('Y-m-d') . ".csv";

// Check if the file exists
if (!file_exists($filename)) {
    die("Backup file not found.");
}

// Open the CSV file
$file = fopen($filename, "r");
if ($file === false) {
    die("Error opening the backup file.");
}

// Skip the first row (headers)
fgetcsv($file);

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO users (user_id, email, role, created_at, failed_attempts, last_failed_attempt, status) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE email=VALUES(email), role=VALUES(role), created_at=VALUES(created_at), failed_attempts=VALUES(failed_attempts), last_failed_attempt=VALUES(last_failed_attempt), status=VALUES(status)");

// Check if the statement was prepared successfully
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

// Read and insert each row
while (($row = fgetcsv($file)) !== false) {
    $stmt->bind_param("isssiss", $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6]);
    $stmt->execute();
}

// Close the file and statement
fclose($file);
$stmt->close();
$conn->close();

echo "Users restored successfully from the backup!";
?>
