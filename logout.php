<?php

session_start();
unset($_SESSION['name']);
unset($_SESSION['user_id']);
session_destroy(); // Destroy the session
header('Location: index.php'); // Redirect to the login page
?>
