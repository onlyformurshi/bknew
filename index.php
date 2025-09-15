<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: views/auth/");
    exit;
} else {
    header("Location: views/country-management/index.php");
    exit;
}
?>
