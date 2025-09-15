<?php
require '../config/config.php';

header('Content-Type: application/json');

if (isset($_GET['country_id']) && is_numeric($_GET['country_id'])) {
    $country_id = (int) $_GET['country_id'];

    try {
        $stmt = $pdo->prepare("SELECT id, regional_name FROM regionals WHERE country_id = :country_id AND status = 1");
        $stmt->execute(['country_id' => $country_id]);
        $regionals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($regionals);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid country_id']);
}
