<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

$conn = new mysqli("localhost", "root", "", "bakasyunan_bcp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$current_page = basename($_SERVER['PHP_SELF']);

// Fetch available rooms
$rooms_result = $conn->query("SELECT * FROM rooms WHERE status='Available'");

// Handle check-in
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['room_id'])) {
    $room_id = $_POST['room_id'];
    $update_query = $conn->prepare("UPDATE rooms SET status='Occupied' WHERE id=?");
    $update_query->bind_param("i", $room_id);
    if ($update_query->execute()) {
        echo "<script>alert('Room Checked In Successfully'); window.location.href='checkin.php';</script>";
    }
    $update_query->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Check-In | Bakasyunan Resort</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <style>
    body {
        display: flex;
        margin: 0;
        font-family: Arial, sans-serif;
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
        background-color:rgb(6, 6, 6);
    }
    h2 {
        margin-bottom: 20px;
        color: white;
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
        <li><a href="?logout=true" style="color: white;">🚪 Logout</a></li>
    </ul>
</div>

<div class="main-content">
  <h2 class="text-xl font-bold mb-6">Check-In</h2>

  <div class="bg-white p-6 rounded shadow-md w-full">
    <form id="filterForm" class="flex flex-col sm:flex-row sm:items-center sm:space-x-6 mb-6" onsubmit="return false;">
      <label for="category" class="text-gray-700 mb-2 sm:mb-0">Category</label>
      <select id="category" class="border border-gray-300 rounded px-3 py-2 w-full sm:w-48">
        <option value="All">All</option>
        <option value="Single Room">Single Room</option>
        <option value="Deluxe Room">Deluxe Room</option>
      </select>
      <button id="filterBtn" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 mt-4 sm:mt-0">Filter</button>
    </form>

    <div class="mb-4 flex justify-between items-center">
      <div>
        Show
        <select id="entriesSelect" class="border px-2 py-1">
          <option value="10">10</option>
        </select>
        entries
      </div>
      <div>
        Search: <input type="text" id="search" class="border px-2 py-1" placeholder="Search..." />
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full border text-sm text-left" id="roomTable">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2">#</th>
            <th class="px-4 py-2">Category</th>
            <th class="px-4 py-2">Room</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Action</th>
          </tr>
        </thead>
        <tbody class="bg-white">
          <?php $i = 1; while ($row = $rooms_result->fetch_assoc()): ?>
          <tr class="border-t">
            <td class="px-4 py-2"><?= $i++; ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($row['category']); ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($row['room_name']); ?></td>
            <td class="px-4 py-2">
              <span class="bg-green-600 text-white px-2 py-1 text-xs rounded">Available</span>
            </td>
            <td class="px-4 py-2">
              <form method="post">
                <input type="hidden" name="room_id" value="<?= $row['id']; ?>">
                <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 text-sm">Check-In</button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php $conn->close(); ?>
</body>
</html>
