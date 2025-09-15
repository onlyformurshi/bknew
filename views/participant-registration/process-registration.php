<?php

require '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $program_id = isset($_POST['program_id']) ? intval($_POST['program_id']) : 0;
    $full_name = trim($_POST['fullName'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $place = trim($_POST['place'] ?? '');
    $terms_accepted = isset($_POST['termsAgreement']) ? 1 : 0;

    // Basic validation
    if ($program_id <= 0 || $full_name === '' || $mobile === '' || $place === '' || !$terms_accepted) {
        die('Invalid input. Please fill all required fields and accept the terms.');
    }

    // Generate unique reference_id
    $reference_id = 'REF-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));

    try {
        $stmt = $pdo->prepare("INSERT INTO participants 
            (program_id, reference_id, full_name, mobile, place, terms_accepted) 
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $program_id,
            $reference_id,
            $full_name,
            $mobile,
            $place,
            $terms_accepted
        ]);
        // Redirect or show success message
        header("Location: ticket-participiants.php?program-id=" . $program_id . "&reference_id=" . urlencode($reference_id) . "&success=1");
        exit;
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    die('Invalid request method.');
}