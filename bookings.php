<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

// Database connection
$conn = new mysqli("localhost", "root", "", "bakasyunan_bcp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$current_page = basename($_SERVER['PHP_SELF']);

// Fetch booking count
$booking_result = $conn->query("SELECT COUNT(*) AS count FROM bookings");
$booking_count = $booking_result->fetch_assoc()['count'];

// Fetch rooms count
$rooms_result = $conn->query("SELECT COUNT(*) AS count FROM rooms");
$rooms_count = $rooms_result->fetch_assoc()['count'];

// Handle new booking
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];
    $room_id = $_POST['room_id'];
    $date = $_POST['date'];
    $user_id = 1; // Placeholder for user ID

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, date, status, created_at) VALUES (?, ?, ?, 'Pending', NOW())");
    $stmt->bind_param("iis", $user_id, $room_id, $date);
    
    if ($stmt->execute()) {
        // Send email confirmation
        $mail = new PHPMailer(true);
        try {
             // SMTP Configuration
             $mail->isSMTP();
             $mail->Host = 'smtp.gmail.com'; 
             $mail->SMTPAuth = true;
             $mail->Username = 'abadleahjoy96@gmail.com'; 
             $mail->Password = 'spimztlribsgtfrb'; 
             $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
             $mail->Port = 587;
 
             // Sender & Recipient
             $mail->setFrom('abadleahjoy96@gmail.com', 'Bakasyunan_Resort');
             $mail->addAddress($email);
 
            $mail->isHTML(true);
            $mail->Subject = 'Booking Confirmation';
            $mail->Body = "<p>Your booking is confirmed for room ID: $room_id on $date.</p>";
            $mail->send();
        } catch (Exception $e) {
            echo "Email could not be sent: " . $mail->ErrorInfo;
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAKASYUNAN RESORT</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            margin: 0;
            background-color:black;
        }
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px;
            height: 100vh;
        }
        h2 {
            margin-bottom: 20px;
            color: #ccc;
        }
        .sidebar h2 { text-align: center; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { padding: 10px; cursor: pointer; }
        .sidebar ul li a { color: white; text-decoration: none; display: block; }
        .sidebar ul li:hover, .sidebar ul li.active { background: #34495e; }
        .main-content { flex: 1; padding: 20px; }
        .top-bar { background: #333; color: white; padding: 10px; text-align: right; }
        .dashboard { display: flex; gap: 20px; margin-top: 20px; }
        .card { flex: 1; padding: 20px; text-align: center; border-radius: 5px; color: white; }
        .booking { background: #17a2b8; }
        .rooms { background: #007bff; }

    </style>
</head>
<body>
    <div class="sidebar">
        <h2>BAKASYUNAN RESORT</h2>
        <ul>
            <li class="<?php echo ($current_page == 'dash.php') ? 'active' : ''; ?>"><a href="dash.php">🏠 Home</a></li>
            <li class="<?php echo ($current_page == 'bookings.php') ? 'active' : ''; ?>"><a href="bookings.php">📋 Booked</a></li>
            <li class="<?php echo ($current_page == 'checkin.php') ? 'active' : ''; ?>"><a href="checkin.php">➡️ Check In</a></li>
            <li class="<?php echo ($current_page == 'checkout.php') ? 'active' : ''; ?>"><a href="checkout.php">⬅️ Check Out</a></li>
            <li class="<?php echo ($current_page == 'room_category.php') ? 'active' : ''; ?>"><a href="room_category.php">📃 Room Category List</a></li>
            <li class="<?php echo ($current_page == 'rooms.php') ? 'active' : ''; ?>"><a href="rooms.php">🛏️ Rooms</a></li>
            <li class="<?php echo ($current_page == 'user_account.php') ? 'active' : ''; ?>"><a href="user_account.php">👤 Users</a></li>
            <li class="<?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>"><a href="settings.php">⚙️ Site Settings</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h2>BAKASYUNAN RESORT</h2>
        <div class="top-bar">
            <span>Administrator</span>
        </div>
        <div class="dashboard">
            <div class="card booking">
                <h3>Booking</h3>
                <p><?php echo $booking_count; ?> Active</p>
            </div>
            <div class="card rooms">
                <h3>Rooms</h3>
                <p><?php echo $rooms_count; ?> Available</p>
            </div>
        </div>

        <h3>New Booking</h3>
        <form method="post">
           
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>
