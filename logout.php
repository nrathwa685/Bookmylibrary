<?php
session_start();

$_SESSION = [];

session_destroy();

/* Delete session cookie */
setcookie(session_name(), '', time() - 3600, '/');

setcookie("success", "You have been logged out successfully.", time() + 2);
header("Location: login.php");
exit();
?>