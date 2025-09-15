<?php
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'User Management');
if (isset($_POST['id'], $_POST['status'])) {
    $id = intval($_POST['id']);
    $newStatus = $_POST['status'];
   
    $stmt = $pdo->prepare("UPDATE users SET status = :status WHERE id = :id");
    $stmt->execute(['status' => $newStatus, 'id' => $id]);
    echo $stmt->rowCount() ? "success" : "error";
}
?>