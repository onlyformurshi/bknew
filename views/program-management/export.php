<?php

require '../../vendor/autoload.php';
require '../../config/config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Get filters from GET
$params = $_GET;
$where = [];
$queryParams = [];

// Build WHERE conditions (similar to index.php)
$where[] = "programs.status IN ('pending', 'activated')";
if (!empty($params['searchtitle'])) {
    $where[] = "programs.title LIKE :searchtitle";
    $queryParams['searchtitle'] = "%{$params['searchtitle']}%";
}
if (!empty($params['program_number'])) {
    $where[] = "programs.program_number = :program_number";
    $queryParams['program_number'] = $params['program_number'];
}
if (!empty($params['centre_id'])) {
    $where[] = "programs.centre_id = :centre_id";
    $queryParams['centre_id'] = $params['centre_id'];
}
if (!empty($params['regional_id'])) {
    $where[] = "programs.regional_id = :regional_id";
    $queryParams['regional_id'] = $params['regional_id'];
}
if (!empty($params['country_id'])) {
    $where[] = "programs.country_id = :country_id";
    $queryParams['country_id'] = $params['country_id'];
}
if (!empty($params['instructor'])) {
    $where[] = "programs.instructor_name LIKE :instructor";
    $queryParams['instructor'] = "%{$params['instructor']}%";
}
if (!empty($params['completion_status'])) {
    $where[] = "programs.completion_status = :completion_status";
    $queryParams['completion_status'] = $params['completion_status'];
}
$whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

// Fetch data
$sql = "SELECT 
    programs.program_number,
    programs.title,
    centres.centre_name,
    regionals.regional_name,
    countries.country_name,
    (SELECT MIN(session_start) FROM program_sessions_times WHERE program_id = programs.id) AS session_start_min,
    (SELECT MAX(session_end) FROM program_sessions_times WHERE program_id = programs.id) AS session_end_max,
    programs.instructor_name,
    programs.venue,
    (SELECT COUNT(*) FROM participants WHERE program_id = programs.id) AS participant_count,
    programs.max_participants,
    programs.status,
    programs.completion_status,
    programs.created_at
FROM programs
LEFT JOIN centres ON programs.centre_id = centres.id
LEFT JOIN regionals ON programs.regional_id = regionals.id
LEFT JOIN countries ON programs.country_id = countries.id
$whereClause
ORDER BY programs.updated_at DESC";

$stmt = $pdo->prepare($sql);
foreach ($queryParams as $key => $value) {
    $stmt->bindValue(':' . $key, $value);
}
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header
$headers = [
    'Program No', 'Title', 'Centre', 'Region', 'Country', 'Start Date', 'End Date',
    'Instructor', 'Venue', 'Participants', 'Status', 'Completion Status', 'Program Generated DATE'
];
$sheet->fromArray($headers, NULL, 'A1');

// Data
$rowNum = 2;
foreach ($data as $row) {
    $sheet->setCellValue("A$rowNum", $row['program_number']);
    $sheet->setCellValue("B$rowNum", $row['title']);
    $sheet->setCellValue("C$rowNum", $row['centre_name']);
    $sheet->setCellValue("D$rowNum", $row['regional_name']);
    $sheet->setCellValue("E$rowNum", $row['country_name']);
    $sheet->setCellValue("F$rowNum", $row['session_start_min'] ? date('Y-m-d H:i', strtotime($row['session_start_min'])) : '-');
    $sheet->setCellValue("G$rowNum", $row['session_end_max'] ? date('Y-m-d H:i', strtotime($row['session_end_max'])) : '-');
    $sheet->setCellValue("H$rowNum", $row['instructor_name']);
    $sheet->setCellValue("I$rowNum", $row['venue']);
    $sheet->setCellValue("J$rowNum", $row['participant_count'] . '/' . $row['max_participants']);
    $sheet->setCellValue("K$rowNum", ucfirst($row['status']));
    $sheet->setCellValue("L$rowNum", ucfirst($row['completion_status']));
    $sheet->setCellValue("M$rowNum", $row['created_at'] ? date('Y-m-d H:i', strtotime($row['created_at'])) : '-');
    $rowNum++;
}

// Output to browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="programs.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;