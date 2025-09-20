<?php
require '../../config/config.php';
require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Build WHERE clause (same as index.php)
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

$query = "SELECT participants.reference_id, participants.full_name, participants.mobile, participants.place, participants.registration_date, participants.attendance_status, participants.hear_about_us, programs.title, programs.program_number
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

// Create Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header
$headers = [
    'Reference ID', 'Program Number', 'Program Title', 'Full Name', 'Mobile', 'Place', 'Where did you hear?', 'Attendance', 'Registered On'
];
$sheet->fromArray($headers, NULL, 'A1');

// Data
$rowNum = 2;
foreach ($participants as $row) {
    $sheet->setCellValue("A$rowNum", $row['reference_id']);
    $sheet->setCellValue("B$rowNum", $row['program_number']);
    $sheet->setCellValue("C$rowNum", $row['title']);
    $sheet->setCellValue("D$rowNum", $row['full_name']);
    $sheet->setCellValue("E$rowNum", $row['mobile']);
    $sheet->setCellValue("F$rowNum", $row['place']);
    $sheet->setCellValue("G$rowNum", $row['hear_about_us']); // New field
    $sheet->setCellValue("H$rowNum", ucfirst($row['attendance_status']));
    $sheet->setCellValue("I$rowNum", date('M d, Y H:i', strtotime($row['registration_date'])));
    $rowNum++;
}

// Output to browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="participants.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;