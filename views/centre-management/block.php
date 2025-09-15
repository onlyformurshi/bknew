<?php
require '../../config/config.php';
require '../../helpers/security.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Center Management');
header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    try {
        $id = intval($_POST['id']);
        $newStatus = in_array(strtolower($_POST['status']), ['active', 'blocked']) ? $_POST['status'] : null;
        
        if (!$newStatus) {
            echo "error: Invalid status value";
            exit;
        }

        // Check if sub-centre exists
        $checkStmt = $pdo->prepare("SELECT id FROM centres WHERE id = ?");
        $checkStmt->execute([$id]);
        
        if ($checkStmt->rowCount() === 0) {
            echo "error: Sub-centre not found";
            exit;
        }
        
        // Update the sub-centre status
        $updateStmt = $pdo->prepare("UPDATE centres SET status = :status, updated_at = NOW() WHERE id = :id");
        $updateStmt->execute(['status' => $newStatus, 'id' => $id]);
        
        if ($updateStmt->rowCount() > 0) {
            echo "success";
        } else {
            echo "error: No changes made - status may be the same";
        }
    } catch (PDOException $e) {
        error_log("Sub-centre status update error: " . $e->getMessage());
        echo "error: Database error occurred";
    }
} else {
    echo "error: Invalid request";
}
?>