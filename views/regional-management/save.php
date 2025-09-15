<?php
session_start();
require '../../config/config.php';
require '../../helpers/security.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Regional Management');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $regional_name = trim(htmlspecialchars($_POST['regional_name'] ?? '', ENT_QUOTES, 'UTF-8'));
    $country_id = intval($_POST['country_id'] ?? 0);
    $status = "active";

    // Validate required fields
    if (empty($regional_name) || empty($country_id)) {
        $message = "Please fill all required fields";
        header("Location: add.php?id={$id}&message=" . urlencode($message));
        exit;
    }

    try {
        if ($id > 0) {
            // Update existing state
            $stmt = $pdo->prepare("UPDATE regionals 
                SET regional_name = :regional_name, 
                    country_id = :country_id, 
                    updated_at = NOW()
                WHERE id = :id");
            $stmt->execute([
                ':regional_name' => $regional_name,
                ':country_id' => $country_id,
                ':id' => $id
            ]);
            $message = "State updated successfully!";
        } else {
            // Insert new state
            $stmt = $pdo->prepare("INSERT INTO regionals 
                (regional_name, country_id, status, created_at, updated_at) 
                VALUES (:regional_name, :country_id, :status, NOW(), NOW())");
            $stmt->execute([
                ':regional_name' => $regional_name,
                ':country_id' => $country_id,
                ':status' => $status
            ]);
            $message = "Region added successfully!";
        }

        header("Location: index.php?message=" . urlencode($message));
        exit;
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        $message = "An error occurred while saving. Please try again.";
        header("Location: add.php?id={$id}&message=" . urlencode($message));
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}