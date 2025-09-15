<?php
require '../config/config.php';

if (isset($_GET['regional_id'])) {
    $regional_id = intval($_GET['regional_id']);
    $stmt = $pdo->prepare("SELECT * FROM centres WHERE regional_id = ?");
    $stmt->execute([$regional_id]);
    $centres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($centres) {
        echo json_encode($centres);
    } else {
        echo json_encode([]);
    }
}
?>
