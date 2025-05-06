<?php
session_start();
require_once __DIR__ . '/incident.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "bakasyunan_bcp");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $incident_type = trim(htmlspecialchars($_POST['incident_type']));
    $description = trim(htmlspecialchars($_POST['description']));
    $status = trim(htmlspecialchars($_POST['status']));
    $user_id = $_SESSION['user_id'];
    $reported_at = date("Y-m-d H:i:s");

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO incidents (incident_type, description, status, user_id, reported_at) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssis", $incident_type, $description, $status, $user_id, $reported_at);
        
        if ($stmt->execute()) {
            $message = "<div class='success'>Incident reported successfully!</div>";
        } else {
            $message = "<div class='error'>Error reporting incident: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='error'>Database error: " . $conn->error . "</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report an Incident</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1e1e2f;
            color: white;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: auto;
            padding: 20px;
            background-color: #2a2a3a;
            border-radius: 8px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
            margin-top: 50px;
        }
        input, textarea, select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: none;
        }
        button {
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .success {
            color: #4CAF50;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Report an Incident</h2>
        <?php if (isset($message)) echo $message; ?>
        <form method="POST">
            <label for="incident_type">Incident Type</label>
            <select name="incident_type" id="incident_type" required>
                <option value="Security Breach">Security Breach</option>
                <option value="Data Loss">Data Loss</option>
                <option value="System Outage">System Outage</option>
                <option value="Other">Other</option>
            </select>

            <label for="description">Description</label>
            <textarea name="description" id="description" required placeholder="Describe the incident"></textarea>

            <label for="status">Status</label>
            <select name="status" id="status" required>
                <option value="Pending">Pending</option>
                <option value="Resolved">Resolved</option>
                <option value="In Progress">In Progress</option>
            </select>

            <button type="submit">Report Incident</button>
        </form>
    </div>
</body>
</html>
