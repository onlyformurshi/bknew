<?php
require '../../config/config.php';
require '../../helpers/security.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Program Management');

// Check if user has permission to delete programs
$canDelete = canUsercan_delete($pdo, 'Program Management'); // <-- use new
// If user does not have permission, redirect or show an error
if (!$canDelete) {
    header("Location: ../../unauthorized.php");
    exit;
}

header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $id = intval($_POST['id']);
        
        // Check if centre exists
        $stmt = $pdo->prepare("SELECT id FROM programs WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
            echo "error: centre not found";
            exit;
        }
        
        // Delete the centre
        $deleteStmt = $pdo->prepare("DELETE FROM programs WHERE id = ?");
        $deleteStmt->execute([$id]);
        
        if ($deleteStmt->rowCount() > 0) {
            echo "success";
        } else {
            echo "error: Failed to delete centre";
        }
    } catch (PDOException $e) {
        echo "error: " . $e->getMessage();
    }
} else {
    echo "error: Invalid request";
}
?>