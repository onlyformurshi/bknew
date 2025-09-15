<?php


// Automatically protect routes based on user_role and current path
if (isset($_SESSION['user_role'])) {
    $userRole = strtolower($_SESSION['user_role']);
    $currentPath = $_SERVER['PHP_SELF']; // e.g., /Brahmakumari/views/user-management/index.php

    // Define which roles can access which directories
    $accessControl = [
        'super admin' => [
            '/centre-management/',
            '/country-management/',
            '/regional-management/',
            '/user-management/',
            '/program-management/',
            '/sponsor-management/',
        ],
        'program admin' => [
            '/program-management/',
            
            
        ],
    ];

    // Check access
    $allowedPaths = $accessControl[$userRole] ?? [];
    $isAllowed = false;

    foreach ($allowedPaths as $allowed) {
        if (strpos($currentPath, $allowed) !== false) {
            $isAllowed = true;
            break;
        }
    }

    if (!$isAllowed) {
        header('Location: ../../unauthorized.php');
        exit();
    }
}

// Function to check if the user has access to a specific path
?>