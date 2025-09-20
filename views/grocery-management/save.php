<?php
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Grocery Management');

// Get POST data
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$item_name = trim($_POST['item_name'] ?? '');
$item_quantity = intval($_POST['item_quantity'] ?? 0);
$item_status = $_POST['item_status'] ?? '';
$date = $_POST['date'] ?? '';
$added_by = trim($_POST['added_by'] ?? '');

// Validation
if ($item_name === '' || $item_quantity <= 0 || $item_status === '' || $date === '' || $added_by === '') {
    header("Location: add.php?message=Please+fill+all+fields");
    exit;
}

try {
    if ($id > 0) {
        // Update
        $stmt = $pdo->prepare("UPDATE grocerytb SET Item_name = ?, Item_Quantity = ?, Item_status = ?, Date = ?, Added_by = ? WHERE Id = ?");
        $stmt->execute([$item_name, $item_quantity, $item_status, $date, $added_by, $id]);
        $msg = "Grocery item updated successfully!";
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO grocerytb (Item_name, Item_Quantity, Item_status, Date, Added_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$item_name, $item_quantity, $item_status, $date, $added_by]);
        $msg = "Grocery item added successfully!";
    }
    header("Location: add.php?message=" . urlencode($msg));
    exit;
} catch (PDOException $e) {
    header("Location: add.php?message=" . urlencode("Database error: " . $e->getMessage()));
    exit;
}
?>