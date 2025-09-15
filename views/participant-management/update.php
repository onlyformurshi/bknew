<?php

require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Participant Management');
// Check if user has permission to update participants
$canUpdate = canUsercan_edit($pdo, 'Participant Management'); // <-- use new
// If user does not have permission, redirect or show an error
if (!$canUpdate) {
    header("Location: ../../unauthorized.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = intval($_POST['id']);
    $program_id = isset($_POST['program_id']) ? intval($_POST['program_id']) : 0;
    $reference_id = trim($_POST['reference_id'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $place = trim($_POST['place'] ?? '');
    $attendance_status = $_POST['attendance_status'] ?? '';
    $terms_accepted = isset($_POST['terms_accepted']) ? 1 : 0;

    // Set defaults
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
        header("Location: add.php?id=$id&message=$msg");
        exit;
    }

    // Generate reference_id if empty
    if ($reference_id === '') {
        $reference_id = 'REF-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));
    }

    try {
        $stmt = $pdo->prepare("UPDATE participants SET
            program_id = ?,
            reference_id = ?,
            full_name = ?,
            mobile = ?,
            place = ?,
            attendance_status = ?,
            payment_status = ?,
            terms_accepted = ?,
            updated_at = NOW()
            WHERE id = ?");
        $stmt->execute([
            $program_id,
            $reference_id,
            $full_name,
            $mobile,
            $place,
            $attendance_status,
            $payment_status,
            $terms_accepted,
            $id
        ]);
        header("Location: index.php?message=" . urlencode("Participant updated successfully!"));
        exit;
    } catch (PDOException $e) {
        error_log("DB Error: " . $e->getMessage());
        header("Location: add.php?id=$id&message=" . urlencode("Database error. Please try again."));
        exit;
    }
} else {
    header("Location: add.php");
    exit;
}