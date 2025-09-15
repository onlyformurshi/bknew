<?php
require '../../config/config.php';
require '../../helpers/security.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Program Management');
header('Content-Type: application/json');

// Add error handling
try {
    if (isset($_POST['regional_id']) && is_numeric($_POST['regional_id'])) {
        $regional_id = intval($_POST['regional_id']);
        $stmt = $pdo->prepare("SELECT id, centre_name FROM centres WHERE regional_id = ? AND status = 'active' ORDER BY centre_name ASC");
        $stmt->execute([$regional_id]);
        $centres = $stmt->fetchAll(PDO::FETCH_ASSOC);
       
        echo json_encode($centres);
    } else {
        echo json_encode([]);
    }
} catch (PDOException $e) {
    // Log error (in a production environment)
    error_log('Database error: ' . $e->getMessage());
    
    // Return error message
    echo json_encode(['error' => 'Database error occurred']);
}
?>