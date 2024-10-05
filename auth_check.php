<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // If not logged in, redirect to the home page (index.php)
    header('Location: index.php');
    exit; // Ensure no further code is executed after the redirect
}
?>
