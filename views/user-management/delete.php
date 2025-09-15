<?php
require '../../config/config.php';

require_once '../../config/functions.php';
checkModuleAccess($pdo, 'User Management');
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);

    echo $stmt->rowCount() ? "success" : "error";
}
?>
