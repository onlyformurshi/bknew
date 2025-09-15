<?php
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Regional Management');
$canDelete = canUsercan_delete($pdo, 'Regional Management'); // <-- use new
// If user does not have permission, redirect or show an error
if (!$canDelete) {
    header("Location: ../../unauthorized.php");
    exit;
}
header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $id = intval($_POST['id']);
        
        // Check if state exists
        $stmt = $pdo->prepare("SELECT id FROM regionals WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
            echo "error: State/Province not found";
            exit;
        }
        
        // Delete the state
        $deleteStmt = $pdo->prepare("DELETE FROM regionals WHERE id = ?");
        $deleteStmt->execute([$id]);
        
        if ($deleteStmt->rowCount() > 0) {
            echo "success";
        } else {
            echo "error: Failed to delete state/province";
        }
    } catch (PDOException $e) {
        echo "error: " . $e->getMessage();
    }
} else {
    echo "error: Invalid request";
}
?>