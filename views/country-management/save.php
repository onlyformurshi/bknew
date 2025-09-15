<?php
session_start();
require '../../config/config.php'; // Ensure this file exists and sets $pdo
require '../../helpers/security.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Country Management');

// Check if user has permission to add countries
$canadd = canUsercan_add($pdo, 'Country Management'); // <-- use new
// If user does not have permission, redirect or show an error
if (!$canadd) {
    header("Location: ../../unauthorized.php");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0; // Check if editing
    $country_name = trim(htmlspecialchars($_POST['country_name'] ?? '', ENT_QUOTES, 'UTF-8'));
    $country_code = trim($_POST['country_code'] ?? ''); // No validation, just trimming
    $currency = trim($_POST['currency'] ?? ''); // No validation, just trimming
    $language = trim(htmlspecialchars($_POST['language'] ?? '', ENT_QUOTES, 'UTF-8'));

    // Check if required fields are empty
    if (empty($country_name) || empty($country_code) || empty($currency)) {
        $message = "All required fields must be filled!";
        $encodedMessage = urlencode($message);
        header("Location: add.php?id={$id}&message={$encodedMessage}");
        exit();
    }

    // Debugging: Check if $pdo is set
    if (!isset($pdo)) {
        die("Database connection not established. Check config.php!");
    }

    try {
        if ($id > 0) {
            // Edit existing record
            $stmt = $pdo->prepare("UPDATE countries 
                                   SET country_name = :country_name, 
                                       country_code = :country_code, 
                                       currency = :currency, 
                                       language = :language 
                                   WHERE id = :id");
            $stmt->execute([
                ':country_name' => $country_name,
                ':country_code' => strtoupper($country_code),
                ':currency' => strtoupper($currency),
                ':language' => $language,
                ':id' => $id
            ]);
            $message = "Country updated successfully!";
        } else {
            // Insert new record
            $stmt = $pdo->prepare("INSERT INTO countries (country_name, country_code, currency, language) 
                                   VALUES (:country_name, :country_code, :currency, :language)");
            $stmt->execute([
                ':country_name' => $country_name,
                ':country_code' => strtoupper($country_code),
                ':currency' => strtoupper($currency),
                ':language' => $language
            ]);
            $message = "Country added successfully!";
        }
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
    }

    // Redirect with a success/error message
    $encodedMessage = urlencode($message);
    header("Location: index.php?message={$encodedMessage}");
    exit();
}
?>