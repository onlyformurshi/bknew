<?php
ob_start(); // Start output buffering
session_start();
require_once '../../config/config.php'; // Adjust path as needed

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../auth/");
    exit();
}

// Optionally, you can also check if the user role is valid for accessing the dashboard
// if ($_SESSION['user_role'] !== 'admin') {
//     header("Location: ../login/index.php"); // Or another page based on role
//     exit();
// }

$isUserManagement = strpos($_SERVER['REQUEST_URI'], '/user-management/') !== false;

// Fetch user access for all modules
$user_role_id = $_SESSION['user_role_id'] ?? null;
$module_access = [];
if ($user_role_id) {
    $stmt = $pdo->prepare("
            SELECT m.id as module_id, m.module_name, ura.can_view
            FROM modules m
            LEFT JOIN user_role_access ura ON ura.module_id = m.id AND ura.role_id = ?
        ");
    $stmt->execute([$user_role_id]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $module_access[$row['module_name']] = $row['can_view'];
    }
}
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>BRAHMAKUMARI</title>
    <link rel="icon" type="image/x-icon" href="../assets/images/bk-logo-fav.png">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="BRAHMAKUMARI">


    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/daterangepicker.css">
    <link rel="stylesheet" href="../assets/css/choosen.css">
    <link rel="stylesheet" href="../assets/css/custome.css">




</head>

<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
        <div class="app-header header-shadow">
            <div class="app-header__logo">
                <div class="logo-sr">
                    <img width="55px" src="../assets/images/bk-logo-fav.png" class="p-1" alt="BRAHMAKUMARI LOGO">
                </div>
                <div class="header__pane ml-auto">
                    <div>
                        <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                            data-class="closed-sidebar">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>





            <div class="app-header__mobile-menu">
                <div>
                    <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="app-header__menu">
                <span>
                    <button type="button"
                        class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                        <span class="btn-icon-wrapper">
                            <i class="fa fa-ellipsis-v fa-w-6"></i>
                        </span>
                    </button>
                </span>
            </div>
            <div class="app-header__content">

                <div class="app-header-right">
                    <div class="header-btn-lg pr-0">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="d-inline-block dropdown ml-3 p-0">
                                    <button type="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false" class="btn-shadow dropdown-toggle btn btn-info"
                                        id="langSelectorBtn">
                                        <span id="currentFlag" class="lang-flag flag-us"></span>
                                        <span id="currentLangText">English</span>
                                    </button>
                                    <div tabindex="-1" role="menu" aria-hidden="true"
                                        class="dropdown-menu dropdown-menu-right">
                                        <ul class="nav flex-column">
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" class="nav-link dropdown-item"
                                                    data-lang="english" data-flag="flag-us" data-text="English">
                                                    <span class="lang-flag flag-us"></span>
                                                    <span>English</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" class="nav-link dropdown-item"
                                                    data-lang="portuguese" data-flag="flag-br" data-text="Português">
                                                    <span class="lang-flag flag-br"></span>
                                                    <span>Português</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" class="nav-link dropdown-item"
                                                    data-lang="french" data-flag="flag-fr" data-text="Français">
                                                    <span class="lang-flag flag-fr"></span>
                                                    <span>Français</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)" class="nav-link dropdown-item"
                                                    data-lang="arabic" data-flag="flag-sa" data-text="العربية">
                                                    <span class="lang-flag flag-sa"></span>
                                                    <span>العربية</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="widget-content-left ml-3 header-user-info">
                                    <div class="widget-heading">
                                        <?= htmlspecialchars($_SESSION['user_name'] ?? 'Guest') ?>
                                    </div>
                                    <div class="widget-subheading">
                                        <?= ucwords(htmlspecialchars($_SESSION['user_role'] ?? 'Unknown')) ?>
                                    </div>
                                </div>


                                <div class="d-inline-block dropdown ml-3 p-0">
                                    <button style="width: 40px;" type="button" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false"
                                        class="btn-shadow dropdown-toggle btn btn-info" fdprocessedid="rwibbh">


                                        <img style="width: 100%;" src="../assets/images/bk-logo-fav.png"
                                            alt="BRAHMAKUMARI LOGO">
                                    </button>
                                    <div tabindex="-1" role="menu" aria-hidden="true"
                                        class="dropdown-menu dropdown-menu-right">
                                        <ul class="nav flex-column">
                                            <li class="nav-item">
                                                <a href="../../logout.php" class="nav-link">
                                                    <i class="nav-link-icon lnr-inbox"></i>
                                                    <span> Logout</span>
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-main">
            <div class="app-sidebar sidebar-shadow">
                <div class="app-header__logo">

                    <div class="header__pane ml-auto">
                        <div>
                            <button type="button" class="hamburger close-sidebar-btn hamburger--elastic "
                                id="toggleButton-header">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>

                        </div>
                    </div>
                </div>
                <div class="app-header__mobile-menu">
                    <div>
                        <img src="../assets/images/bk-logo-fav.png" alt="BRAHMAKUMARI LOGO">
                        <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="app-header__menu">
                    <span>
                        <button type="button"
                            class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                            <span class="btn-icon-wrapper">
                                <i class="fa fa-ellipsis-v fa-w-6"></i>
                            </span>
                        </button>
                    </span>
                </div>
                <div class="scrollbar-sidebar ps ps--active-y">
                    <div class="app-sidebar__inner">
                        <ul class="vertical-nav-menu metismenu">
                            <li>
                                <a href="../dashboard/">
                                    <i class="fa fa-tachometer" aria-hidden="true"></i> Dashboard
                                </a>
                            </li>

                            <?php if (!empty($module_access['Country Management'])): ?>
                                <li>
                                    <a href="../country-management">
                                        <i class="fa fa-globe" aria-hidden="true"></i> Country Management
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($module_access['Regional Management'])): ?>
                                <li>
                                    <a href="../regional-management">
                                        <i class="fa fa-industry" aria-hidden="true"></i> Regional Management
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($module_access['Center Management'])): ?>
                                <li>
                                    <a href="../centre-management">
                                        <i class="fa fa-home" aria-hidden="true"></i> Center Management
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($module_access['Program Management'])): ?>
                                <li>
                                    <a href="../program-management">
                                        <i class="fa fa-tasks" aria-hidden="true"></i> Program Management
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($module_access['Sponsor Management'])): ?>
                                <li>
                                    <a href="../sponsor-management">
                                        <i class="fa fa-address-card" aria-hidden="true"></i> Sponsor Management
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (!empty($module_access['Participant Management'])): ?>
                                <li>
                                    <a href="../participant-management">
                                        <i class="fa fa-address-book" aria-hidden="true"></i>Participant Management
                                    </a>
                                </li>
                            <?php endif; ?>

                            

                            <?php if (!empty($module_access['User Management'])): ?>
                                <li>
                                    <a href="../user-management">
                                        <i class="fa fa-users" aria-hidden="true"></i> User Management
                                        <span class="fa arrow"></span>
                                    </a>
                                    <ul class="nav nav-second-level <?= $isUserManagement ? 'mm-show' : '' ?>" style="padding-left:20px;">
                                        <li>
                                            <a href="../user-management" class="<?= ($_SERVER['REQUEST_URI'] === '/Brahmakumari/views/user-management/' ? 'mm-active' : '') ?>">
                                                <i class="fa fa-user" aria-hidden="true"></i> All Users
                                            </a>
                                        </li>
                                        <li>
                                            <a href="../user-management/user-role.php" class="<?= (strpos($_SERVER['REQUEST_URI'], '/user-management/user-role.php') !== false ? 'mm-active' : '') ?>">
                                                <i class="fa fa-id-badge" aria-hidden="true"></i> User Role
                                            </a>
                                        </li>
                                        <li>
                                            <a href="../user-management/user-access.php" class="<?= (strpos($_SERVER['REQUEST_URI'], '/user-management/user-role.php') !== false ? 'mm-active' : '') ?>">
                                                <i class="fa fa-id-badge" aria-hidden="true"></i> User Role Access
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php endif; ?>

                            <li>
                                <a href="">
                                    <i class="fa fa-cog" aria-hidden="true"></i> Settings
                                </a>
                            </li>
                        </ul>

                    </div>
                    <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                        <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                    </div>
                    <div class="ps__rail-y" style="top: 0px; height: 643px; right: 0px;">
                        <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 610px;"></div>
                    </div>
                </div>
            </div>