<?php
require '../../config/config.php';
require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Get filters from GET
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : '';
$program_id = isset($_GET['program_id']) ? intval($_GET['program_id']) : '';
$category = trim($_GET['category'] ?? '');
$payment_status = trim($_GET['payment_status'] ?? '');
$payment_type = trim($_GET['payment_type'] ?? '');

// Build WHERE clause
$where = [];
$params = [];
if ($user_id) {
    $where[] = "s.user_id = :user_id";
    $params[':user_id'] = $user_id;
}
if ($program_id) {
    $where[] = "s.program_id = :program_id";
    $params[':program_id'] = $program_id;
}
if ($category) {
    $where[] = "s.category LIKE :category";
    $params[':category'] = "%$category%";
}
if ($payment_status) {
    $where[] = "s.payment_status = :payment_status";
    $params[':payment_status'] = $payment_status;
}
if ($payment_type) {
    $where[] = "s.payment_type = :payment_type";
    $params[':payment_type'] = $payment_type;
}
$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Fetch sponsors
$sql = "SELECT s.*, u.full_name AS sponsor_name, p.title AS program_title, p.program_number
        FROM sponsorships s
        LEFT JOIN users u ON s.user_id = u.id
        LEFT JOIN programs p ON s.program_id = p.id
        $whereSql
        ORDER BY s.created_at DESC";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->execute();
$sponsors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header
$headers = [
    'Sponsor Name', 'Program Number', 'Program', 'Category', 'Item', 'Amount', 'Payment Status', 'Payment Type', 'Created At'
];
$sheet->fromArray($headers, NULL, 'A1');

// Data
$rowNum = 2;
foreach ($sponsors as $row) {
    // Get item name (optional: you can simplify or use logic from index.php)
    $itemName = $row['item_id'];
    if ($row['category'] && $row['item_id']) {
        $tableMap = [
            'facebook_advertisements' => ['table' => 'facebook_advertisements', 'col' => 'name'],
            'billboard_advertisements' => ['table' => 'billboard_advertisements', 'col' => 'agency_name'],
            'instagram_advertisements' => ['table' => 'instagram_advertisements', 'col' => 'name'],
            'newspaper_advertisements' => ['table' => 'newspaper_advertisements', 'col' => 'name'],
            'radio_advertisements' => ['table' => 'radio_advertisements', 'col' => 'name'],
            'television_advertisements' => ['table' => 'television_advertisements', 'col' => 'name'],
            'pamphlet' => ['table' => 'program_pamphlets', 'col' => 'pamphlet_designer_name'],
        ];
        $cat = $row['category'];
        if (isset($tableMap[$cat])) {
            $tbl = $tableMap[$cat]['table'];
            $col = $tableMap[$cat]['col'];
            $itemStmt = $pdo->prepare("SELECT $col FROM $tbl WHERE id = ?");
            $itemStmt->execute([$row['item_id']]);
            $itemName = $itemStmt->fetchColumn();
        }
    }

    $sheet->setCellValue("A$rowNum", $row['sponsor_name']);
    $sheet->setCellValue("B$rowNum", $row['program_number']);
    $sheet->setCellValue("C$rowNum", $row['program_title']);
    $sheet->setCellValue("D$rowNum", $row['category']);
    $sheet->setCellValue("E$rowNum", $itemName);
    $sheet->setCellValue("F$rowNum", $row['amount']);
    $sheet->setCellValue("G$rowNum", $row['payment_status']);
    $sheet->setCellValue("H$rowNum", $row['payment_type']);
    $sheet->setCellValue("I$rowNum", date('d-M-Y', strtotime($row['created_at'])));
    $rowNum++;
}

// Output to browser
if (ob_get_length()) ob_clean(); // <-- Add this line

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="sponsors.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;