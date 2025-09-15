<?php
session_start();
require_once '../../config/config.php';
require_once '../../helpers/security.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard");
    exit();
}

// Initialize error message
$error = '';

// Debug mode (set to true temporarily)
$debug = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token!";
    } else {
        // Sanitize input
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        if (!empty($email) && !empty($password)) {
            try {
                // ✅ Check if PDO is set
                if (!isset($pdo)) {
                    throw new Exception("Database connection error.");
                }

                // ✅ Debug 1: Check if email is being received correctly
                if ($debug) {
                    error_log("Debug: Received email -> " . $email);
                }

                // Fetch user data securely, JOIN with user_roles to get role_name
                $stmt = $pdo->prepare("
                    SELECT users.id, users.full_name, users.password, users.role AS role_id, user_roles.role_name
                    FROM users
                    LEFT JOIN user_roles ON users.role = user_roles.id
                    WHERE users.email = :email
                ");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // ✅ Debug 2: Check if user exists
                if ($debug) {
                    error_log("Debug: User fetch result -> " . print_r($user, true));
                }

                if ($user) {
                    // ✅ Debug 3: Check stored hash
                    if ($debug) {
                        error_log("Debug: Stored hash -> " . $user['password']);
                    }

                    // Verify password
                    if ($password === decrypt_text($user['password'])) {
                        // Secure session handling
                        session_regenerate_id(true);

                        // Store user data in session
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['full_name'];
                        $_SESSION['user_role_id'] = $user['role_id'];      // Store role ID
                        $_SESSION['user_role'] = $user['role_name'];        // Store role name

                        // print_r($_SESSION); // Debugging line to check session data

                        if (strtolower($_SESSION['user_role']) === 'sponsor') {
                            header("Location: ../sponsors-program-view/index.php");
                        } else {
                            header("Location: ../dashboard/index.php");
                        }
                        exit();
                    } else {
                        $error = "Invalid email or password!";
                        error_log("Debug: Password does not match.");
                    }
                } else {
                    $error = "Invalid email or password!";
                    error_log("Debug: User not found.");
                }
            } catch (PDOException $e) {
                error_log("Database Error: " . $e->getMessage());
                $error = "Something went wrong. Please try again.";
            } catch (Exception $e) {
                error_log("General Error: " . $e->getMessage());
                $error = "Unexpected error occurred.";
            }
        } else {
            $error = "Please fill in all fields!";
        }
    }
}

// Regenerate CSRF token for the next request
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Redirect back to login page with an error message if login fails
if (!empty($error)) {
    header(header: "Location: index.php?error=" . urlencode($error));
    exit();
}
