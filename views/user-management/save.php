<?php
session_start();
require '../../config/config.php';
require '../../helpers/security.php'; // make sure this has encrypt_text()

// Include functions file for common functions
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'User Management'); // Check if the user has access to this
function redirectWithMessage($id, $message) {
    header("Location: add.php?id={$id}&message=" . urlencode($message));
    exit();
}

// If POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    // Sanitize input
    $full_name = filter_var(trim($_POST['full_name'] ?? ''), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $phone = preg_replace('/\D/', '', $_POST['phone'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $sponsor_type = trim($_POST['sponsor_type'] ?? '');
    $agency_name = trim($_POST['agency_name'] ?? '');
    $status = 'active';

    // Validate required fields
    if (!$full_name || !$email || !$phone || !$role) {
        redirectWithMessage($id, "All fields are required.");
    }
    if ($role === 'Sponsor') {
        if (!$sponsor_type) {
            redirectWithMessage($id, "Sponsor type is required.");
        }
        if ($sponsor_type === 'Agency' && !$agency_name) {
            redirectWithMessage($id, "Agency name is required.");
        }
    } else {
        $sponsor_type = null;
        $agency_name = null;
    }

    // Check for duplicate email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
    $stmt->execute([':email' => $email, ':id' => $id]);
    if ($stmt->fetch()) {
        redirectWithMessage($id, "Email already exists.");
    }

    // Handle password (encrypt if provided)
    $password_input = $_POST['password'] ?? '';
    $password_encrypted = !empty($password_input) ? encrypt_text($password_input) : null;

    // Check for valid role
    $role_id = intval($_POST['role'] ?? 0);
    $stmt = $pdo->prepare("SELECT role_name FROM user_roles WHERE id = ?");
    $stmt->execute([$role_id]);
    $role_row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$role_row) {
        redirectWithMessage($id, "Invalid role selected.");
    }

    try {
        if ($id > 0) {
            // Update
            $sql = "UPDATE users SET full_name = :full_name, email = :email, phone = :phone, role = :role_id, sponsor_type = :sponsor_type, agency_name = :agency_name";
            $params = [
                ':full_name' => $full_name,
                ':email' => $email,
                ':phone' => $phone,
                ':role_id' => $role_id,
                ':sponsor_type' => $sponsor_type,
                ':agency_name' => $agency_name,
                ':id' => $id
            ];

            if ($password_encrypted) {
                $sql .= ", password = :password";
                $params[':password'] = $password_encrypted;
            }

            $sql .= " WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $message = "User updated successfully.";
        } else {
            // Insert
            if (!$password_encrypted) {
                redirectWithMessage(0, "Password is required for new users.");
            }

            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, role, sponsor_type, agency_name, password, status)
                                   VALUES (:full_name, :email, :phone, :role_id, :sponsor_type, :agency_name, :password, :status)");
            $stmt->execute([
                ':full_name' => $full_name,
                ':email' => $email,
                ':phone' => $phone,
                ':role_id' => $role_id,
                ':sponsor_type' => $sponsor_type,
                ':agency_name' => $agency_name,
                ':password' => $password_encrypted,
                ':status' => $status
            ]);

            $message = "User added successfully.";  
        }

        header("Location: index.php?message=" . urlencode($message));
        exit();

    } catch (Exception $e) {
        redirectWithMessage($id, "Database error: " . $e->getMessage());
    }
}
$role_id = intval($_POST['role'] ?? 0);
?>
