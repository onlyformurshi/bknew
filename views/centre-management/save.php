<?php
session_start();
require '../../config/config.php';
require '../../helpers/security.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Input filtering and validation
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $centre_name = trim(htmlspecialchars($_POST['centre_name'] ?? '', ENT_QUOTES, 'UTF-8'));
    $regional_id = intval($_POST['regional_id'] ?? 0); // Linking to state
    $address = trim(htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES, 'UTF-8'));
    $city = trim(htmlspecialchars($_POST['city'] ?? '', ENT_QUOTES, 'UTF-8'));
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $status = "active";
    $center_coordinator_name = trim(htmlspecialchars($_POST['center_coordinator_name'] ?? '', ENT_QUOTES, 'UTF-8'));

    // Validate required fields
    $requiredFields = [
        'centre Name' => $centre_name,
        'State' => $regional_id,
        'Address' => $address,
        'City' => $city,
        'Phone' => $phone,
        'Email' => $email,
        'Coordinator Name' => $center_coordinator_name,
    ];

    $missingFields = [];
    foreach ($requiredFields as $field => $value) {
        if (empty($value)) {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        $message = "Please fill all required fields: " . implode(', ', $missingFields);
        header("Location: add.php?id={$id}&message=" . urlencode($message));
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format!";
        header("Location: add.php?id={$id}&message=" . urlencode($message));
        exit;
    }

    try {
        if ($id > 0) {
            // Update existing centre
            $stmt = $pdo->prepare("UPDATE centres 
                SET centre_name = :centre_name, 
                    regional_id = :regional_id,
                    address = :address, 
                    city = :city, 
                    phone = :phone, 
                    email = :email,
                    center_coordinator_name = :center_coordinator_name,
                    updated_at = NOW()
                WHERE id = :id");
            $stmt->execute([
                ':centre_name' => $centre_name,
                ':regional_id' => $regional_id,
                ':address' => $address,
                ':city' => $city,
                ':phone' => $phone,
                ':email' => $email,
                ':center_coordinator_name' => $center_coordinator_name,
                ':id' => $id
            ]);
            $message = "centre updated successfully!";
        } else {
            // Insert new centre
            $stmt = $pdo->prepare("INSERT INTO centres 
                (centre_name, regional_id, address, city, phone, email, center_coordinator_name, status, created_at, updated_at) 
                VALUES (:centre_name, :regional_id, :address, :city, :phone, :email, :center_coordinator_name, :status, NOW(), NOW())");
            $stmt->execute([
                ':centre_name' => $centre_name,
                ':regional_id' => $regional_id,
                ':address' => $address,
                ':city' => $city,
                ':phone' => $phone,
                ':email' => $email,
                ':center_coordinator_name' => $center_coordinator_name,
                ':status' => $status
            ]);
            $message = "centre added successfully!";
        }

        // Redirect with success message
        header("Location: index.php?message=" . urlencode($message));
        exit;
    } catch (PDOException $e) {
        // Log error and redirect with error message
        error_log("Database Error: " . $e->getMessage());
        $message = "An error occurred while saving the centre. Please try again.";
        header("Location: add.php?id={$id}&message=" . urlencode($message));
        exit;
    }
}
