<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "bakasyunan_bcp"; // Updated database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Backup file name
$filename = "backup_users_" . date('Y-m-d') . ".csv";
$file = fopen($filename, "w");

// Fetch users from database
$sql = "SELECT user_id, email, role, created_at, failed_attempts, last_failed_attempt, status FROM users";
$result = $conn->query($sql);

// Check if data exists
if ($result->num_rows > 0) {
    // Write column headers
    fputcsv($file, ["User ID", "Email", "Role", "Created At", "Failed Attempts", "Last Failed Attempt", "Status"]);

    // Write data to file
    while ($row = $result->fetch_assoc()) {
        fputcsv($file, $row);
    }
    
    echo "Backup created successfully: <a href='$filename' download>Download Backup</a>";
} else {
    echo "No user data found.";
}

// Close file and database connection
fclose($file);
$conn->close();
?>
