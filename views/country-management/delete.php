<?php
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Country Management');
// Check if user has permission to delete countries
$canDelete = canUserViewPrice($pdo, 'Country Management'); // <-- use new
// If user does not have permission, redirect or show an error
if (!$canDelete) {
    header("Location: ../../unauthorized.php");
    exit;
}
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare("DELETE FROM countries WHERE id = :id");
    $stmt->execute(['id' => $id]);

    echo $stmt->rowCount() ? "success" : "error";
}
?>
