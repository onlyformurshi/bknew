<?php
session_start();
session_unset();     // Unset all session variables
session_destroy();   // Destroy the session
?>

<!DOCTYPE html>
<html>
<head>
    <title>Unauthorized</title>
</head>
<body>
    <h2>🚫 Access Denied</h2>
    <p>You do not have permission to access this page. You have been logged out.</p>
    <a href="./views/auth/">Return Home</a>
</body>
</html>
