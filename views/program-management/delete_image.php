<?php
require '../../config/config.php';
header('Content-Type: application/json');
require '../../helpers/security.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Program Management');

// Check if user is logged in (add your authentication check here)
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(['success' => false, 'message' => 'You must be logged in']);
//     exit;
// }

try {
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $image_id = intval($_POST['id']);
        
        // Get the image path before deleting
        $stmt = $pdo->prepare("SELECT file_path, program_id FROM program_media WHERE id = ?");
        $stmt->execute([$image_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($image) {
            // Begin transaction
            $pdo->beginTransaction();
            
            // Delete from database
            $delete_stmt = $pdo->prepare("DELETE FROM program_media WHERE id = ?");
            $result = $delete_stmt->execute([$image_id]);
            
            if ($result) {
                // Delete file from server if it exists
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $image['file_path'])) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $image['file_path']);
                }
                
                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);
            } else {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Failed to delete image from database']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Image not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid image ID']);
    }
} catch (PDOException $e) {
    // Rollback transaction if there was a database error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log('Database error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>