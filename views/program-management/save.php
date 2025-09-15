<?php
session_start();
require '../../config/config.php';
require '../../helpers/security.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Program Management');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $centre_id = intval($_POST['centre_id'] ?? 0);
    $regional_id = intval($_POST['regional_id'] ?? 0);
    $country_id = intval($_POST['country_id'] ?? 0);
    $title = trim(htmlspecialchars($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8'));
    $description = trim($_POST['description'] ?? '');
    $venue = trim(htmlspecialchars($_POST['venue'] ?? '', ENT_QUOTES, 'UTF-8'));
    $instructor_name = trim(htmlspecialchars($_POST['instructor_name'] ?? '', ENT_QUOTES, 'UTF-8'));
    $max_participants = intval($_POST['max_participants'] ?? 0);
    $current_participants = intval($_POST['current_participants'] ?? 0);
    $marketing_methods = trim($_POST['marketing_methods'] ?? '');
    $status = $_POST['status'] ?? 'pending';
    $status = strtolower($status);
    if (!in_array($status, ['pending', 'activated'])) {
        $status = 'pending';
    }

    // Session times
    $session_names = $_POST['session_name'] ?? [];
    $session_starts = $_POST['session_start'] ?? [];
    $session_ends = $_POST['session_end'] ?? [];

    // Handle the image upload
    $program_img = '';
    if (isset($_FILES['program_img']) && $_FILES['program_img']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['program_img']['tmp_name'];
        $fileName = $_FILES['program_img']['name'];
        $fileType = $_FILES['program_img']['type'];
        $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($fileType, $allowedFileTypes)) {
            $uploadDir = '../../uploads/programs/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $newFileName = uniqid('program_') . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
            $destPath = $uploadDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $program_img = $newFileName;
            } else {
                header("Location: add.php?id={$id}&message=" . urlencode("Error moving the uploaded file."));
                exit;
            }
        } else {
            header("Location: add.php?id={$id}&message=" . urlencode("Only JPEG, PNG, and GIF image files are allowed."));
            exit;
        }
    } elseif (!empty($_POST['old_program_img'])) {
        // No new image uploaded, keep the old one
        $program_img = $_POST['old_program_img'];
    } else {
        // No image at all (new record without image)
        $program_img = '';
    }

    // Validate required fields
    $requiredFields = [
        'Centre' => $centre_id,
        'State' => $regional_id,
        'Country' => $country_id,
        'Title' => $title,
        'Venue' => $venue,
        'Instructor Name' => $instructor_name,
        'Max Participants' => $max_participants
    ];
    $missingFields = [];
    foreach ($requiredFields as $field => $value) {
        if (empty($value) && $value !== 0) {
            $missingFields[] = $field;
        }
    }
    // Validate at least one session
    if (empty($session_names) || empty($session_starts) || empty($session_ends) || count($session_names) == 0) {
        $missingFields[] = 'Session Details';
    }
    if (!empty($missingFields)) {
        header("Location: add.php?id={$id}&message=" . urlencode("Please fill all required fields: " . implode(', ', $missingFields)));
        exit;
    }

    try {
        if ($id > 0) {
            // UPDATE existing program
            $stmt = $pdo->prepare("UPDATE programs SET 
                centre_id = :centre_id,
                country_id = :country_id,
                regional_id = :regional_id,
                title = :title,
                program_img = :program_img,
                description = :description,
                venue = :venue,
                instructor_name = :instructor_name,
                max_participants = :max_participants,
                current_participants = :current_participants,
                marketing_methods = :marketing_methods,
                status = :status,
                updated_at = NOW()
                WHERE id = :id");
            $stmt->execute([
                ':centre_id' => $centre_id,
                ':country_id' => $country_id,
                ':regional_id' => $regional_id,
                ':title' => $title,
                ':program_img' => $program_img,
                ':description' => $description,
                ':venue' => $venue,
                ':instructor_name' => $instructor_name,
                ':max_participants' => $max_participants,
                ':current_participants' => $current_participants,
                ':marketing_methods' => $marketing_methods,
                ':status' => $status,
                ':id' => $id
            ]);
            $program_id = $id;
            $message = "Program updated successfully!";
        } else {
            // Generate unique program_number
            $yearShort = date('y'); // e.g. "25"
            $stmtMax = $pdo->prepare("SELECT MAX(CAST(SUBSTRING_INDEX(program_number, '/', 1) AS UNSIGNED)) AS max_serial 
                FROM programs WHERE RIGHT(program_number, 2) = :year");
            $stmtMax->execute([':year' => $yearShort]);
            $maxSerial = $stmtMax->fetchColumn();
            $nextSerial = str_pad(((int)$maxSerial + 1), 4, '0', STR_PAD_LEFT);
            $program_number = $nextSerial . '/' . $yearShort;

            // INSERT new program
            $stmt = $pdo->prepare("INSERT INTO programs (
                centre_id, country_id, regional_id, title, program_img, description, venue,
                instructor_name, max_participants,
                current_participants, marketing_methods, status,
                program_number, created_at, updated_at
            ) VALUES (
                :centre_id, :country_id, :regional_id, :title, :program_img, :description, :venue,
                :instructor_name, :max_participants,
                :current_participants, :marketing_methods, :status,
                :program_number, NOW(), NOW()
            )");
            $stmt->execute([
                ':centre_id' => $centre_id,
                ':country_id' => $country_id,
                ':regional_id' => $regional_id,
                ':title' => $title,
                ':program_img' => $program_img,
                ':description' => $description,
                ':venue' => $venue,
                ':instructor_name' => $instructor_name,
                ':max_participants' => $max_participants,
                ':current_participants' => $current_participants,
                ':marketing_methods' => $marketing_methods,
                ':status' => $status,
                ':program_number' => $program_number
            ]);
            $program_id = $pdo->lastInsertId();
            $message = "Program added successfully!";
        }

        // --- Handle program_sessions_times ---
        if ($id > 0) {
            // Remove old sessions for this program
            $pdo->prepare("DELETE FROM program_sessions_times WHERE program_id = ?")->execute([$program_id]);
        }
        // Insert new sessions
        $sessionStmt = $pdo->prepare("INSERT INTO program_sessions_times (program_id, session_name, session_start, session_end) VALUES (?, ?, ?, ?)");
        for ($i = 0; $i < count($session_names); $i++) {
            $s_name = trim($session_names[$i]);
            $s_start = $session_starts[$i] ?? '';
            $s_end = $session_ends[$i] ?? '';
            if ($s_name && $s_start && $s_end) {
                $sessionStmt->execute([$program_id, $s_name, $s_start, $s_end]);
            }
        }

        header("Location: index.php?message=" . urlencode($message));
        exit;
    } catch (PDOException $e) {
        error_log("Database Error [Programs]: " . $e->getMessage());
        header("Location: add.php?id={$id}&message=" . urlencode("An error occurred while saving the program."));
        exit;
    }
}
