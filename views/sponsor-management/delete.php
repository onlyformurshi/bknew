<?php

require '../../config/config.php';
require_once '../../config/functions.php';

checkModuleAccess($pdo, 'Sponsor Management');
$candelete = canUsercan_delete($pdo, 'Sponsor Management');
// Redirect if user cannot delete
if (!$candelete) {
    header('Location: ../../unauthorized.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM sponsorships WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount()) {
            echo "success";
        } else {
            echo "not_found";
        }
    } catch (PDOException $e) {
        echo "error";
    }
} else {
    echo "invalid";
}