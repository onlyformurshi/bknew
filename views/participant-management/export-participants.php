<?php
require '../../config/config.php';

// Build WHERE clause (same as in index.php)
$whereConditions = [];
$queryParams = [];

if (!empty($_GET['program_number'])) {
    $whereConditions[] = "programs.program_number = :program_number";
    $queryParams['program_number'] = $_GET['program_number'];
}
if (!empty($_GET['country_id'])) {
    $whereConditions[] = "programs.country_id = :country_id";
    $queryParams['country_id'] = $_GET['country_id'];
}
if (!empty($_GET['regional_id'])) {
    $whereConditions[] = "programs.regional_id = :regional_id";
    $queryParams['regional_id'] = $_GET['regional_id'];
}
if (!empty($_GET['centre_id'])) {
    $whereConditions[] = "programs.centre_id = :centre_id";
    $queryParams['centre_id'] = $_GET['centre_id'];
}
if (!empty($_GET['full_name'])) {
    $whereConditions[] = "participants.full_name LIKE :full_name";
    $queryParams['full_name'] = "%{$_GET['full_name']}%";
}
if (!empty($_GET['mobile'])) {
    $whereConditions[] = "participants.mobile LIKE :mobile";
    $queryParams['mobile'] = "%{$_GET['mobile']}%";
}
if (!empty($_GET['reference_id'])) {
    $whereConditions[] = "participants.reference_id = :reference_id";
    $queryParams['reference_id'] = $_GET['reference_id'];
}
if (!empty($_GET['attendance_status'])) {
    $whereConditions[] = "participants.attendance_status = :attendance_status";
    $queryParams['attendance_status'] = $_GET['attendance_status'];
}

$whereClause = $whereConditions ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Fetch all filtered participants
$query = "SELECT participants.*, programs.title, programs.program_number
          FROM participants
          LEFT JOIN programs ON participants.program_id = programs.id
          $whereClause
          ORDER BY participants.created_at DESC";
$stmt = $pdo->prepare($query);
foreach ($queryParams as $key => $value) {
    $stmt->bindValue(':' . $key, $value);
}
$stmt->execute();
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output CSV headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=participants_export.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['#', 'Reference ID', 'Program', 'Full Name', 'Mobile', 'Place', 'Attendance', 'Registered On']);

$counter = 1;
foreach ($participants as $row) {
    fputcsv($output, [
        $counter++,
        $row['reference_id'],
        $row['program_number'] . ' - ' . $row['title'],
        $row['full_name'],
        $row['mobile'],
        $row['place'],
        ucfirst($row['attendance_status']),
        date('M d, Y H:i', strtotime($row['registration_date']))
    ]);
}
fclose($output);
exit;