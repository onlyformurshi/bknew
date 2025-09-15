<?php

require '../../config/config.php';

$paymentStatuses = ['pending', 'paid', 'failed'];
$paymentTypes = ['cash', 'online', 'cheque', 'upi'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $program_id = isset($_POST['program_id']) ? intval($_POST['program_id']) : 0;
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0.0;
    $payment_status = isset($_POST['payment_status']) ? trim($_POST['payment_status']) : '';
    $payment_type = isset($_POST['payment_type']) ? trim($_POST['payment_type']) : '';
    $updated_at = date('Y-m-d H:i:s');

    // Basic validation
    if ($user_id > 0 && $program_id > 0 && $category && $item_id > 0 && $amount > 0 && $payment_status && $payment_type) {
        try {
            if ($id > 0) {
                // Update existing
                $stmt = $pdo->prepare("UPDATE sponsorships SET
                    user_id = :user_id,
                    program_id = :program_id,
                    category = :category,
                    item_id = :item_id,
                    amount = :amount,
                    payment_status = :payment_status,
                    payment_type = :payment_type,
                    updated_at = :updated_at
                    WHERE id = :id");
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':program_id' => $program_id,
                    ':category' => $category,
                    ':item_id' => $item_id,
                    ':amount' => $amount,
                    ':payment_status' => $payment_status,
                    ':payment_type' => $payment_type,
                    ':updated_at' => $updated_at,
                    ':id' => $id
                ]);
                header("Location: index.php?status=success&message=" . urlencode("Sponsor contribution updated successfully!"));
                exit;
            } else {
                // Insert new
                $created_at = $updated_at;
                $stmt = $pdo->prepare("INSERT INTO sponsorships 
                    (user_id, program_id, category, item_id, amount, payment_status, payment_type, created_at, updated_at)
                    VALUES 
                    (:user_id, :program_id, :category, :item_id, :amount, :payment_status, :payment_type, :created_at, :updated_at)");
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':program_id' => $program_id,
                    ':category' => $category,
                    ':item_id' => $item_id,
                    ':amount' => $amount,
                    ':payment_status' => $payment_status,
                    ':payment_type' => $payment_type,
                    ':created_at' => $created_at,
                    ':updated_at' => $updated_at
                ]);
                header("Location: index.php?status=success&message=" . urlencode("Sponsor contribution saved successfully!"));
                exit;
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    } else {
        echo "Invalid input. Please fill out all fields correctly.";
    }
} else {
    echo "Invalid request method.";
}