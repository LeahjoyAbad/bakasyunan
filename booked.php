<?php
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if needed
$database = "bakasyunan_bcp";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['guestName'];
    $checkIn = $_POST['checkIn'];
    $checkOut = $_POST['checkOut'];

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, date, status) VALUES (?, ?, ?)");
    $status = "Booked"; // Example status
    $stmt->bind_param("sss", $name, $checkIn, $status);
    $stmt->execute();
    $stmt->close();
}

// Fetch bookings
$bookings = $conn->query("SELECT user_id, date, status FROM bookings ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bakasyunan Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1e1e2f;
            color: white;
            margin: 0;
            padding: 0;
        }
        .dashboard-container {
            padding: 20px;
            text-align: center;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            justify-items: center;
        }
        .chart-container, .booking-container {
            background-color: #2a2a3a;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 600px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid white;
            padding: 8px;
            text-align: center;
        }
        input, button {
            padding: 10px;
            margin: 5px;
            border: none;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>BAKASYUNAN RESORT</h1>
        </header>
        <div class="dashboard-grid">
            <div class="chart-container">
                <h2>Incident Management</h2>
                <canvas id="incidentChart"></canvas>
            </div>
            <div class="chart-container">
                <h2>Security Logs</h2>
                <canvas id="securityLogChart"></canvas>
            </div>
            <div class="booking-container">
                <h2>Book a Stay</h2>
                <form method="POST">
                    <input type="text" name="guestName" placeholder="Guest Name" required>
                    <input type="date" name="checkIn" required>
                    <input type="date" name="checkOut" required>
                    <button type="submit">Book Now</button>
                </form>
                <h2>Bookings</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Check-in</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $bookings->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row["user_id"]) ?></td>
                                <td><?= htmlspecialchars($row["date"]) ?></td>
                                <td><?= htmlspecialchars($row["status"]) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
