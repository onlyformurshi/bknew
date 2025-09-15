<?php
// filepath: d:\xampp\htdocs\Brahmakumari\views\participant-management\save.php

require '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $program_id = isset($_POST['program_id']) ? intval($_POST['program_id']) : 0;
    $reference_id = trim($_POST['reference_id'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $place = trim($_POST['place'] ?? '');
    $attendance_status = $_POST['attendance_status'] ?? '';
    $terms_accepted = isset($_POST['terms_accepted']) ? 1 : 0;

    // Set defaults
    $registration_date = date('Y-m-d');
    $payment_status = "Pending";

    // Basic validation
    $errors = [];
    if ($program_id <= 0) $errors[] = "Program is required.";
    if ($full_name === '') $errors[] = "Full name is required.";
    if ($mobile === '') $errors[] = "Mobile number is required.";
    if ($place === '') $errors[] = "Place is required.";
    if ($attendance_status === '') $errors[] = "Attendance status is required.";
    if (!$terms_accepted) $errors[] = "You must accept the terms.";

    if (!empty($errors)) {
        $msg = urlencode(implode(' ', $errors));
        header("Location: add.php?message=$msg");
        exit;
    }

    // Generate reference_id if empty
    if ($reference_id === '') {
        $reference_id = 'REF-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO participants 
            (program_id, reference_id, full_name, mobile, place, registration_date, attendance_status, payment_status, terms_accepted, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([
            $program_id,
            $reference_id,
            $full_name,
            $mobile,
            $place,
            $registration_date,
            $attendance_status,
            $payment_status,
            $terms_accepted
        ]);
        header("Location: index.php?message=" . urlencode("Participant added successfully!"));
        exit;
    } catch (PDOException $e) {
        error_log("DB Error: " . $e->getMessage());
        header("Location: add.php?message=" . urlencode("Database error. Please try again."));
        exit;
    }
} else {
    header("Location: add.php");
    exit;
}