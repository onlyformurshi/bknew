<?php
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Regional Management');
header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    try {
        $id = intval($_POST['id']);
        $newStatus = in_array(strtolower($_POST['status']), ['active', 'blocked']) ? $_POST['status'] : null;
        
        if (!$newStatus) {
            echo "error: Invalid status value";
            exit;
        }

        // Check if state exists
        $checkStmt = $pdo->prepare("SELECT id FROM regionals WHERE id = ?");
        $checkStmt->execute([$id]);
        
        if ($checkStmt->rowCount() === 0) {
            echo "error: State/Province not found";
            exit;
        }
        
        // Update the state status
        $updateStmt = $pdo->prepare("UPDATE regionals SET status = :status WHERE id = :id");
        $updateStmt->execute(['status' => $newStatus, 'id' => $id]);
        
        if ($updateStmt->rowCount() > 0) {
            echo "success";
        } else {
            echo "error: No changes made - status may be the same";
        }
    } catch (PDOException $e) {
        echo "error: " . $e->getMessage();
    }
} else {
    echo "error: Invalid request";
}
?>