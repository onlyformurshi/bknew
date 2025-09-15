<?php
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Participant Management');
// Check if user has permission to delete participants
$canDelete = canUsercan_delete($pdo, 'Participant Management'); // <-- use new
// If user does not have permission, redirect or show an error
if (!$canDelete) {
    header("Location: ../../unauthorized.php");
    exit;
}

header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $id = intval($_POST['id']);
        $stmt = $pdo->prepare("SELECT id FROM participants WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) {
            echo "error: participant not found";
            exit;
        }
        $deleteStmt = $pdo->prepare("DELETE FROM participants WHERE id = ?");
        $deleteStmt->execute([$id]);
        if ($deleteStmt->rowCount() > 0) {
            echo "success";
        } else {
            echo "error: Failed to delete participant";
        }
    } catch (PDOException $e) {
        echo "error: " . $e->getMessage();
    }
} else {
    echo "error: Invalid request";
}
?>