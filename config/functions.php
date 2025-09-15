<?php 
function checkModuleAccess($pdo, $currentPath) {
    // Get current user's role ID from session
    $userRoleId = $_SESSION['user_role_id'] ?? null;

    if (!$userRoleId) {
        header("Location: ../../unauthorized.php");
        exit();
    }

    // Fetch accessible modules for this role
    $stmt = $pdo->prepare("
        SELECT m.id, m.module_name, m.base_path, ura.can_view, ura.can_add, ura.can_edit, ura.can_delete
        FROM user_role_access ura
        JOIN modules m ON ura.module_id = m.id
        WHERE ura.role_id = :role_id
    ");
    $stmt->execute(['role_id' => $userRoleId]);
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check access for current path
    $allowed = false;
    foreach ($modules as $module) {
        if (strcasecmp($module['module_name'], $currentPath) === 0) {
            $allowed = true;
            break;
        }
    }

    if (!$allowed) {
        header("Location: ../../unauthorized.php");
        exit();
    }

    return $modules; // Optional: return list of modules if needed
}

function canUserViewPrice($pdo, $moduleName) {
    $userRoleId = $_SESSION['user_role_id'] ?? null;

    if (!$userRoleId) {
        return false;
    }

    $stmt = $pdo->prepare("
        SELECT can_view_price FROM user_role_access ura
        JOIN modules m ON ura.module_id = m.id
        WHERE ura.role_id = :role_id AND m.module_name = :module_name
        LIMIT 1
    ");
    $stmt->execute([
        'role_id' => $userRoleId,
        'module_name' => $moduleName
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return !empty($result) && $result['can_view_price'] == 1;
}




function canUsercan_add($pdo, $moduleName) {
    $userRoleId = $_SESSION['user_role_id'] ?? null;

    if (!$userRoleId) {
        return false;
    }

    $stmt = $pdo->prepare("
        SELECT can_add FROM user_role_access ura
        JOIN modules m ON ura.module_id = m.id
        WHERE ura.role_id = :role_id AND m.module_name = :module_name
        LIMIT 1
    ");
    $stmt->execute([
        'role_id' => $userRoleId,
        'module_name' => $moduleName
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return !empty($result) && $result['can_add'] == 1;
}



function canUsercan_delete($pdo, $moduleName) {
    $userRoleId = $_SESSION['user_role_id'] ?? null;

    if (!$userRoleId) {
        return false;
    }

    $stmt = $pdo->prepare("
        SELECT can_delete FROM user_role_access ura
        JOIN modules m ON ura.module_id = m.id
        WHERE ura.role_id = :role_id AND m.module_name = :module_name
        LIMIT 1
    ");
    $stmt->execute([
        'role_id' => $userRoleId,
        'module_name' => $moduleName
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return !empty($result) && $result['can_delete'] == 1;
}
function canUsercan_edit($pdo, $moduleName) {
    $userRoleId = $_SESSION['user_role_id'] ?? null;

    if (!$userRoleId) {
        return false;
    }

    $stmt = $pdo->prepare("
        SELECT can_edit FROM user_role_access ura
        JOIN modules m ON ura.module_id = m.id
        WHERE ura.role_id = :role_id AND m.module_name = :module_name
        LIMIT 1
    ");
    $stmt->execute([
        'role_id' => $userRoleId,
        'module_name' => $moduleName
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return !empty($result) && $result['can_edit'] == 1;
}

function canUserViewProgram($pdo, $moduleName) {
    $userRoleId = $_SESSION['user_role_id'] ?? null;
    if (!$userRoleId) {
        return false;
    }
    $stmt = $pdo->prepare("
        SELECT can_view_program FROM user_role_access ura
        JOIN modules m ON ura.module_id = m.id
        WHERE ura.role_id = :role_id AND m.module_name = :module_name
        LIMIT 1
    ");
    $stmt->execute([
        'role_id' => $userRoleId,
        'module_name' => $moduleName
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return !empty($result) && $result['can_view_program'] == 1;
}




function updateProgramCompletionStatus($program_id, $pdo) {
    // Check sessions of this program
    $stmt = $pdo->prepare("SELECT session_start, session_end 
                           FROM program_sessions_times 
                           WHERE program_id = ?");
    $stmt->execute([$program_id]);
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($sessions)) {
        // No sessions = set status to not_started or leave unchanged
        return;
    }

    $now = new DateTime();
    $hasFuture = false;
    $hasPast = false;
    $isOngoing = false;

    foreach ($sessions as $session) {
        $start = new DateTime($session['session_start']);
        $end = new DateTime($session['session_end']);

        if ($now < $start) {
            $hasFuture = true;
        } elseif ($now >= $start && $now <= $end) {
            $isOngoing = true;
            break; // In progress is the highest priority
        } elseif ($now > $end) {
            $hasPast = true;
        }
    }

    if ($isOngoing) {
        $status = 'in_progress';
    } elseif ($hasFuture && !$hasPast) {
        $status = 'not_started';
    } else {
        $status = 'completed';
    }

    // Update the programs table
    $update = $pdo->prepare("UPDATE programs SET completion_status = ? WHERE id = ?");
    $update->execute([$status, $program_id]);
}

?>

