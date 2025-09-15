<?php
function user_has_access($pdo, $role_id, $module_id, $action = 'can_view') {
    $allowed_actions = ['can_view', 'can_add', 'can_edit', 'can_delete'];
    if (!in_array($action, $allowed_actions)) return false;

    $stmt = $pdo->prepare("SELECT $action FROM user_role_access WHERE role_id = ? AND module_id = ?");
    $stmt->execute([$role_id, $module_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return !empty($result) && !empty($result[$action]);
}



