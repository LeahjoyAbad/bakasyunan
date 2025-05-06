<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bakasyunan Resort - Home</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    /* Overall body styling with a dark overlay for text clarity */
    body {
      margin: 0;
      background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
        url("BG.jpg");
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      font-family: Arial, sans-serif;
      color: #fff;
    }

    /* Header and navigation styling with shadows for depth */
    header {
      padding: 20px;
      text-align: center;
      text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
    }

    nav ul {
      list-style: none;
      padding: 0;
      margin: 20px 0;
    }

    nav ul li {
      display: inline-block;
      margin-right: 15px;
    }

    nav ul li a {
      text-decoration: none;
      color: #fff;
      padding: 8px 15px;
      background: rgba(0, 0, 0, 0.3);
      border-radius: 5px;
      box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
      transition: background 0.3s;
    }

    nav ul li a:hover {
      background: rgba(0, 0, 0, 0.5);
    }

    /* Main content styling with text shadow */
    main {
      min-height: 60vh;
      padding: 20px;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.6);
    }

    h1 {
      font-size: 2.5em;
      margin: 0;
    }

    h2 {
      color: #fff;
      margin-top: 0;
    }

    /* Wave element styling */
    .wave {
      position: relative;
      width: 100%;
      height: 150px;
      background: #fff;
      margin-top: -1px; /* Overlap slightly with content */
    }

    .wave svg {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 100%;
    }

    /* Footer styling with subtle shadow */
    footer {
      padding: 10px;
      text-align: center;
      background: rgba(0, 0, 0, 0.4);
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
    }
  </style>
</head>
<body>
  <?php 
    session_start(); 
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest'; 
  ?>
  <header>
    <h1>Bakasyunan Resort</h1>
    <nav>
      <ul>
        <?php if(isset($_SESSION['user_id'])): ?>
          <?php if($role == 'admin'): ?>
            <li><a href="admin_dash.php">Admin Dashboard</a></li>
          <?php elseif($role == 'receptionist'): ?>
            <li><a href="recep_dash.php">Receptionist Dashboard</a></li>
          <?php elseif($role == 'guest'): ?>
            <li><a href="guest_dash.php">Guest Dashboard</a></li>
          <?php endif; ?>
          <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>
  <main>
    <h2>Welcome to Bakasyunan Resort</h2>
    <p>Experience the ultimate relaxation with our luxurious facilities and breathtaking views.</p>
  </main>
  
  <!-- Wave design element using SVG -->
  <div class="wave">
    <svg viewBox="0 0 500 150" preserveAspectRatio="none">
      <path d="M0.00,49.98 C150.00,150.00 349.59,-49.98 500.00,49.98 L500.00,150.00 L0.00,150.00 Z" style="stroke: none; fill: #fff;"></path>
    </svg>
  </div>
  
  <footer>
    <p>&copy; 2025 Bakasyunan Resort. All rights reserved.</p>
  </footer>
</body>
</html>
