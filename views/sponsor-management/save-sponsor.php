<?php

require '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $user_type = $_POST['user_type'] ?? '';
    $agency_name = trim($_POST['agency_name'] ?? '');
    $password = $_POST['password'] ?? '';

    // Server-side validation
    $errors = [];
    if (!$name || !preg_match('/^[A-Za-z\s]{3,100}$/', $name)) $errors[] = "Invalid name.";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";
    if (!$phone || !preg_match('/^\d{10,15}$/', $phone)) $errors[] = "Invalid phone.";
    if (!$user_type || !in_array($user_type, ['individual', 'agency'])) $errors[] = "Invalid user type.";
    if ($user_type === 'agency' && !$agency_name) $errors[] = "Agency name required.";
    if (!$password || strlen($password) < 6 || strlen($password) > 32) $errors[] = "Password must be 6-32 characters.";

    if ($errors) {
        // Show errors and stop
        echo "<div class='alert alert-danger'>" . implode('<br>', $errors) . "</div>";
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        if ($id > 0) {
            // Update existing sponsor user
            $stmt = $pdo->prepare("UPDATE sponsor_users SET name = :name, email = :email, phone = :phone, user_type = :user_type, agency_name = :agency_name, password = :password WHERE id = :id");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':user_type' => $user_type,
                ':agency_name' => $user_type === 'agency' ? $agency_name : null,
                ':password' => $password_hash,
                ':id' => $id
            ]);
            $message = "Sponsor user updated successfully!";
        } else {
            // Insert new sponsor user
            $stmt = $pdo->prepare("INSERT INTO sponsor_users (name, email, phone, user_type, agency_name, password) VALUES (:name, :email, :phone, :user_type, :agency_name, :password)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':user_type' => $user_type,
                ':agency_name' => $user_type === 'agency' ? $agency_name : null,
                ':password' => $password_hash
            ]);
            $message = "Sponsor user added successfully!";
        }
        header("Location: index.php?message=" . urlencode($message));
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $message = "Email already exists.";
        } else {
            $message = "Database Error: " . $e->getMessage();
        }
        header("Location: index.php?message=" . urlencode($message));
        exit;
    }
} else {
    header("Location: add-sponsor.php");
    exit;
}