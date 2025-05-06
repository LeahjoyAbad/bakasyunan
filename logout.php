<?php
session_start();

// Destroy the session to log the user out
session_unset();
session_destroy();

// Redirect to the homepage
header("Location: homepage.php"); // Adjust this path if your homepage is in a different file
exit();
?>
