
<?php
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'User Management');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $role_name = trim($_POST['role_name'] ?? '');

    // Validate
    if (!$role_name || !preg_match('/^[A-Za-z\s]+$/', $role_name)) {
        $redirect = $role_id ? "add-user-role.php?id=$role_id" : "add-user-role.php";
        header("Location: $redirect?message=" . urlencode("Invalid role name."));
        exit();
    }

    // Check duplicate (exclude self if editing)
    $stmt = $pdo->prepare("SELECT id FROM user_roles WHERE role_name = :role_name AND id != :id");
    $stmt->execute(['role_name' => $role_name, 'id' => $role_id]);
    if ($stmt->fetch()) {
        $redirect = $role_id ? "add-user-role.php?id=$role_id" : "add-user-role.php";
        header("Location: $redirect?message=" . urlencode("Role already exists."));
        exit();
    }

    if ($role_id) {
        // Update
        $stmt = $pdo->prepare("UPDATE user_roles SET role_name = :role_name WHERE id = :id");
        if ($stmt->execute(['role_name' => $role_name, 'id' => $role_id])) {
            header("Location: user-role.php?message=" . urlencode("Role updated successfully."));
            exit();
        } else {
            header("Location: add-user-role.php?id=$role_id&message=" . urlencode("Database error."));
            exit();
        }
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO user_roles (role_name) VALUES (:role_name)");
        if ($stmt->execute(['role_name' => $role_name])) {
            header("Location: user-role.php?message=" . urlencode("Role added successfully."));
            exit();
        } else {
            header("Location: add-user-role.php?message=" . urlencode("Database error."));
            exit();
        }
    }
} else {
    header("Location: add-user-role.php");
    exit();
}