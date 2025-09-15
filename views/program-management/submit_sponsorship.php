<?php

require '../../config/config.php';
require '../../helpers/security.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Program Management');
// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // Sanitize and assign form inputs
    $sponsor_id = isset($_POST['sponsor_id']) ? intval($_POST['sponsor_id']) : 0;
    $program_id = isset($_POST['program_id']) ? intval($_POST['program_id']) : 0;
    $description = isset($_POST['contribution_description']) ? trim($_POST['contribution_description']) : '';
    $amount = isset($_POST['sponsor_amount']) ? floatval($_POST['sponsor_amount']) : 0.0;
    $sponsored_at = date('Y-m-d H:i:s');

    // Basic validation
    // Use the PDO instance from config.php
    global $pdo;

    if ($sponsor_id > 0 && $program_id > 0 && $description !== '' && $amount > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO sponsorships (sponsor_id, program_id, contribution_description, amount, sponsored_at)
                               VALUES (:sponsor_id, :program_id, :description, :amount, :sponsored_at)");
            $stmt->execute([
                ':sponsor_id' => $sponsor_id,
                ':program_id' => $program_id,
                ':description' => $description,
                ':amount' => $amount,
                ':sponsored_at' => $sponsored_at
            ]);

            header("Location: index.php?status=success");
            
            exit;
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    } else {
        echo "Invalid input. Please fill out all fields correctly.";
    }

} else {
    echo "Invalid request method.";
}
?>