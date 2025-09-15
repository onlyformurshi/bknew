<?php

require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'User Management');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role_id'], $_POST['access'])) {
    $role_id = intval($_POST['role_id']);
    $access = $_POST['access'];

    // Remove old access
    $pdo->prepare("DELETE FROM user_role_access WHERE role_id = ?")->execute([$role_id]);

    // Insert new access
    $stmt = $pdo->prepare("INSERT INTO user_role_access (role_id, module_id, can_view, can_add, can_edit, can_delete, can_view_price, can_view_program) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($access as $module_id => $rights) {
        $can_view = !empty($rights['can_view']) ? 1 : 0;
        $can_add = !empty($rights['can_add']) ? 1 : 0;
        $can_edit = !empty($rights['can_edit']) ? 1 : 0;
        $can_delete = !empty($rights['can_delete']) ? 1 : 0;
        $can_view_price = !empty($rights['can_view_price']) ? 1 : 0;
        $can_view_program = !empty($rights['can_view_program']) ? 1 : 0; // New
        $stmt->execute([$role_id, $module_id, $can_view, $can_add, $can_edit, $can_delete, $can_view_price, $can_view_program]);
    }
    header("Location: user-access.php?role_id=$role_id&message=Access+updated+successfully");
    exit;
}
header("Location: user-access.php?message=Invalid+request");
exit;