<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Prevent back button access */
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

/* Check login */
if (!isset($_SESSION['id'])) {
    setcookie("success", "You have been logged out successfully.", time() + 2);
    header("Location: ../login.php");
    exit();
}