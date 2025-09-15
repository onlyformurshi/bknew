<?php

// Prevent direct access to this file
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    exit("Direct access not allowed.");
}

// ----------------------------
// ✅ Input Sanitization
// ----------------------------
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// ----------------------------
// ✅ Secure Password Hashing & Verification
// ----------------------------
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verify_password($password, $hashed_password) {
    return password_verify($password, $hashed_password);
}

// ----------------------------
// ✅ CSRF Token Protection
// ----------------------------
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// -----------------------------
// ✅ SQL Injection Prevention
// ----------------------------
function prepare_statement($pdo, $query, $params) {
    $stmt = $pdo->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->execute();
    return $stmt;
}

// ----------------------------
// ✅ Secure Headers
// ----------------------------
function set_security_headers() {
    header("X-Frame-Options: DENY");
    header("X-XSS-Protection: 1; mode=block");
    header("X-Content-Type-Options: nosniff");
    header("Referrer-Policy: no-referrer");
}

// ----------------------------
// ✅ Validate Email & URL
// ----------------------------
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function is_valid_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
}

// ----------------------------
// ✅ Redirect Function
// ----------------------------
function secure_redirect($url) {
    header("Location: " . filter_var($url, FILTER_SANITIZE_URL));
    exit();
}

// ----------------------------
// ✅ Start Secure Session
// ----------------------------
function start_secure_session() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.use_cookies', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
        session_start();
        session_regenerate_id(true);
    }
}

// Initialize security settings
start_secure_session();
set_security_headers();

function encrypt_text($plainText) {
    return base64_encode(openssl_encrypt($plainText, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, ENCRYPTION_IV));
}

function decrypt_text($cipherText) {
    return openssl_decrypt(base64_decode($cipherText), ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, ENCRYPTION_IV);
}





?>
