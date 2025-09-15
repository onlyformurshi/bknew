<?php
require '../../config/config.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (
    !isset($data['user_id'], $data['program_id'], $data['category'], $data['item_id'], $data['amount']) ||
    !is_numeric($data['amount']) || $data['amount'] <= 0
) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Insert sponsorship
    $stmt = $pdo->prepare("INSERT INTO sponsorships 
        (user_id, program_id, category, item_id, amount) 
        VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $data['program_id'],
        $data['category'],
        $data['item_id'],
        $data['amount']
    ]);

    // If Facebook Advertisement, update received_amount
    if ($data['category'] === 'facebook_advertisements') {
        $updateStmt = $pdo->prepare("UPDATE facebook_advertisements SET received_amount = received_amount + ? WHERE id = ?");
        $updateStmt->execute([
            $data['amount'],
            $data['item_id']
        ]);
    }

    // If Billboard Advertisement, update received_amount
    if ($data['category'] === 'billboard_advertisements') {
        $updateStmt = $pdo->prepare("UPDATE billboard_advertisements SET received_amount = received_amount + ? WHERE id = ?");
        $updateStmt->execute([
            $data['amount'],
            $data['item_id']
        ]);
    }

    // If Instagram Advertisement, update received_amount
    if ($data['category'] === 'instagram_advertisements') {
        $updateStmt = $pdo->prepare("UPDATE instagram_advertisements SET received_amount = received_amount + ? WHERE id = ?");
        $updateStmt->execute([
            $data['amount'],
            $data['item_id']
        ]);
    }

    // If Newspaper Advertisement, update received_amount
    if ($data['category'] === 'newspaper_advertisements') {
        $updateStmt = $pdo->prepare("UPDATE newspaper_advertisements SET received_amount = received_amount + ? WHERE id = ?");
        $updateStmt->execute([
            $data['amount'],
            $data['item_id']
        ]);
    }

    // If Radio Advertisement, update received_amount
    if ($data['category'] === 'radio_advertisements') {
        $updateStmt = $pdo->prepare("UPDATE radio_advertisements SET received_amount = received_amount + ? WHERE id = ?");
        $updateStmt->execute([
            $data['amount'],
            $data['item_id']
        ]);
    }

    // If Television Advertisement, update received_amount
    if ($data['category'] === 'television_advertisements') {
        $updateStmt = $pdo->prepare("UPDATE television_advertisements SET received_amount = received_amount + ? WHERE id = ?");
        $updateStmt->execute([
            $data['amount'],
            $data['item_id']
        ]);
    }

    // If Pamphlet, update received_amount
    if ($data['category'] === 'pamphlet') {
        $updateStmt = $pdo->prepare("UPDATE program_pamphlets SET received_amount = received_amount + ? WHERE id = ?");
        $updateStmt->execute([
            $data['amount'],
            $data['item_id']
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
