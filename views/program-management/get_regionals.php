<?php
require '../../config/config.php';
header('Content-Type: application/json');
require '../../helpers/security.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Program Management');

// Add error handling
try {
    if (isset($_POST['country_id']) && is_numeric($_POST['country_id'])) {
        $country_id = intval($_POST['country_id']);
        $stmt = $pdo->prepare("SELECT id, regional_name FROM regionals WHERE country_id = ? AND status = 'active' ORDER BY regional_name ASC");
        $stmt->execute([$country_id]);
        $regionals = $stmt->fetchAll(PDO::FETCH_ASSOC);
       
        echo json_encode($regionals);
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