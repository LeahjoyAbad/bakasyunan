<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

$conn = new mysqli("localhost", "root", "", "bakasyunan_bcp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$current_page = basename($_SERVER['PHP_SELF']);

// Fetch occupied rooms
$rooms_result = $conn->query("SELECT * FROM rooms WHERE status='Occupied'");

// Handle check-out
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['room_id'])) {
    $room_id = $_POST['room_id'];
    $update_query = $conn->prepare("UPDATE rooms SET status='Available' WHERE id=?");
    $update_query->bind_param("i", $room_id);
    if ($update_query->execute()) {
        echo "<script>alert('Room Checked Out Successfully'); window.location.href='checkout.php';</script>";
    }
    $update_query->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-Out | Bakasyunan Resort</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: black;
        }
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px;
            height: 100vh;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 10px;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
        }
        .sidebar ul li.active {
            background: #34495e;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid white;
            text-align: center;
            padding: 10px;
        }
        .checkout-btn {
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>BAKASYUNAN RESORT</h2>
    <ul>
        <li class="<?php echo ($current_page == 'dash.php') ? 'active' : ''; ?>"><a href="dash.php">🏠 Home</a></li>
        <li class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>"><a href="index.php">📋 Booked</a></li>
        <li class="<?php echo ($current_page == 'checkin.php') ? 'active' : ''; ?>"><a href="checkin.php">➡️ Check In</a></li>
        <li class="<?php echo ($current_page == 'checkout.php') ? 'active' : ''; ?>"><a href="checkout.php">⬅️ Check Out</a></li>
        <li class="<?php echo ($current_page == 'room_category.php') ? 'active' : ''; ?>"><a href="room_category.php">📃 Room Category List</a></li>
        <li class="<?php echo ($current_page == 'rooms.php') ? 'active' : ''; ?>"><a href="rooms.php">🛏️ Rooms</a></li>
        <li class="<?php echo ($current_page == 'user_account.php') ? 'active' : ''; ?>"><a href="user_account.php">👤 Users</a></li>
        <li class="<?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>"><a href="settings.php">⚙️ Site Settings</a></li>
        <!-- Logout Button at the bottom -->
        <li class="logout-btn">
            <a href="?logout=true" style="color: white; text-decoration: none;">🚪 Logout</a>
        </li>
    </ul>
</div>
    <div class="main-content">
        <h2>Check-Out</h2>
        <table>
            <tr>
                <th>#</th>
                <th>Category</th>
                <th>Room</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php $i = 1; while ($row = $rooms_result->fetch_assoc()): ?>
            <tr>
                <td><?= $i++; ?></td>
                <td><?= $row['category']; ?></td>
                <td><?= $row['room_name']; ?></td>
                <td><span style="color: red;">Occupied</span></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="room_id" value="<?= $row['id']; ?>">
                        <button type="submit" class="checkout-btn">Check-Out</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>
