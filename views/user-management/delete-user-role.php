<?php

require '../../config/config.php';

require_once '../../config/functions.php';
checkModuleAccess($pdo, 'User Management');
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Optional: Prevent deleting critical roles by ID/name
    // $stmt = $pdo->prepare("SELECT role_name FROM user_roles WHERE id = ?");
    // $stmt->execute([$id]);
    // $role = $stmt->fetchColumn();
    // if ($role === 'Super Admin') {
    //     header("Location: user-role.php?message=" . urlencode("Cannot delete Super Admin role."));
    //     exit();
    // }

    $stmt = $pdo->prepare("DELETE FROM user_roles WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: user-role.php?message=" . urlencode("Role deleted successfully."));
        exit();
    } else {
        header("Location: user-role.php?message=" . urlencode("Failed to delete role."));
        exit();
    }
} else {
    header("Location: user-role.php?message=" . urlencode("Invalid request."));
    exit();
}