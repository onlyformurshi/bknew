<?php

require '../../config/config.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';
$program_id = isset($_GET['program_id']) ? intval($_GET['program_id']) : 0;

if (!$category || !$program_id) {
    echo json_encode([]);
    exit;
}

// Map category to table and column
$table = $category === 'pamphlet' ? 'program_pamphlets' : $category;
$nameCol = $category === 'billboard_advertisements' ? 'agency_name' : ($category === 'pamphlet' ? 'pamphlet_designer_name' : 'name');

// Prepare and execute query
$stmt = $pdo->prepare("SELECT id, $nameCol AS item_name, cost, received_amount FROM $table WHERE program_id = ? ORDER BY item_name ASC");
$stmt->execute([$program_id]);
$items = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $row['balance_amount'] = floatval($row['cost']) - floatval($row['received_amount']);
    $items[] = $row;
}
echo json_encode($items);