<?php
require '../Includes/header.php';
require '../../config/config.php';
require_once '../../config/functions.php';
require_once '../../helpers/site-url.php';
checkModuleAccess($pdo, 'Program Management');
$canadd = canUsercan_add($pdo, 'Program Management'); // <-- use new function
$candelete = canUsercan_delete($pdo, 'Program Management'); // <-- use new function
$canedit = canUsercan_edit($pdo, 'Program Management');

// Initialize variables
$totalPages = 1;
$totalPrograms = $activePrograms = $blockedPrograms = 0;

try {
    // Fetch Program counts
    $stmtTotal = $pdo->query("SELECT COUNT(*) FROM programs");
    $totalPrograms = $stmtTotal->fetchColumn();

    $stmtActive = $pdo->query("SELECT COUNT(*) FROM programs WHERE LOWER(TRIM(status)) = 'active'");
    $activePrograms = $stmtActive->fetchColumn();

    $stmtBlocked = $pdo->query("SELECT COUNT(*) FROM programs WHERE LOWER(TRIM(status)) = 'blocked'");
    $blockedPrograms = $stmtBlocked->fetchColumn();

    // Fetch centres, regionals and countries for dropdowns
    $centres = $pdo->query("SELECT id, centre_name FROM centres WHERE status = 'Active' ORDER BY centre_name")->fetchAll(PDO::FETCH_ASSOC);
    $regionals = $pdo->query("SELECT id, regional_name FROM regionals ORDER BY regional_name")->fetchAll(PDO::FETCH_ASSOC);
    $countries = $pdo->query("SELECT id, country_name FROM countries ORDER BY country_name")->fetchAll(PDO::FETCH_ASSOC);

    $programNumbers = $pdo->query("SELECT program_number FROM programs ORDER BY program_number")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}
?>

<div class="app-main__outer">
    <div class="app-main__inner">
        <div class="app-page-title app-page-title-simple">
            <div class="page-title-wrapper d-flex justify-content-between">
                <div class="page-title-heading">
                    <div>
                        <div class="page-title-head center-elem mb-3">
                            <span class="d-inline-block">
                                <i class="lnr-calendar-full opacity-6"></i>
                            </span>
                            <span class="d-inline-block">Program Management</span>
                        </div>
                    </div>
                </div>
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Program Management</a></li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-xl-4">
                <div class="card mb-3 widget-content">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                                <div class="widget-heading">Total Programs</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers text-info"><?= $totalPrograms ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card mb-3 widget-content">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                                <div class="widget-heading">Active Programs</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers text-success"><?= $activePrograms ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- Search Form with Multiple Filters -->
        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Search</h5>
                        


                        <form action="" method="GET">
                            <!-- Row 1 -->
                            <div class="form-row">
                                <div class="col-md-4 mb-3">
                                    <label for="program_number">Program Number</label>
                                    <select class="form-control select2" id="program_number" name="program_number">
                                        <option value="">All Program Numbers</option>
                                        <?php foreach ($programNumbers as $pn): ?>
                                            <option value="<?= htmlspecialchars($pn['program_number']) ?>"
                                                <?= (isset($_GET['program_number']) && $_GET['program_number'] == $pn['program_number']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($pn['program_number']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="searchtitle">Program Title</label>
                                    <input placeholder="Enter Program Title" type="text" class="form-control" id="searchtitle" name="searchtitle"
                                        value="<?= isset($_GET['searchtitle']) ? htmlspecialchars($_GET['searchtitle']) : '' ?>">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="d-flex" for="country_id">Country</label>
                                    <select class="form-control select2" id="country_id" name="country_id">
                                        <option value="">All Countries</option>
                                        <?php foreach ($countries as $country): ?>
                                            <option value="<?= $country['id'] ?>"
                                                <?= (isset($_GET['country_id']) && $_GET['country_id'] == $country['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($country['country_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Row 2 -->
                            <div class="form-row">
                                <div class="col-md-4 mb-3">
                                    <label class="d-flex" for="regional_id">Regions</label>
                                    <select class="form-control select2" id="regional_id" name="regional_id">
                                        <option value="">All Regions</option>
                                        <?php foreach ($regionals as $state): ?>
                                            <option value="<?= $state['id'] ?>"
                                                <?= (isset($_GET['regional_id']) && $_GET['regional_id'] == $state['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($state['regional_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="d-flex" for="centre_id">Centre</label>
                                    <select class="form-control select2" id="centre_id" name="centre_id">
                                        <option value="">All Centres</option>
                                        <?php foreach ($centres as $centre): ?>
                                            <option value="<?= $centre['id'] ?>"
                                                <?= (isset($_GET['centre_id']) && $_GET['centre_id'] == $centre['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($centre['centre_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="instructor">Instructor Name</label>
                                    <input placeholder="Enter Instructor Name" type="text" class="form-control" id="instructor" name="instructor"
                                        value="<?= isset($_GET['instructor']) ? htmlspecialchars($_GET['instructor']) : '' ?>">
                                </div>
                            </div>

                            <!-- Row 3 -->
                            <!-- You can remove date_from and date_to fields if not needed -->
                            <div class="form-row">
                                <div class="col-md-4 mb-3">
                                    <label for="date_from">Date From</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from"
                                        value="<?= isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : '' ?>">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="date_to">Date To</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to"
                                        value="<?= isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : '' ?>">
                                </div>
                            </div>

                            <!-- Row 4 -->
                            <div class="form-row">
                                <div class="col-md-4 mb-3">
                                    <label for="completion_status">Completion Status</label>
                                    <select class="form-control select2" id="completion_status" name="completion_status">
                                        <option value="">All Status</option>
                                        <option value="not_started" <?= (isset($_GET['completion_status']) && $_GET['completion_status'] == 'not_started') ? 'selected' : '' ?>>Not Started</option>
                                        <option value="in_progress" <?= (isset($_GET['completion_status']) && $_GET['completion_status'] == 'in_progress') ? 'selected' : '' ?>>In Progress</option>
                                        <option value="completed" <?= (isset($_GET['completion_status']) && $_GET['completion_status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-end">
                                <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>" class="btn btn-outline-dark mx-3">
                                    <i class="fa fa-window-restore" aria-hidden="true"></i> Reset
                                </a>
                                <button class="btn btn-success" type="submit">
                                    <i class="fa fa-search" aria-hidden="true"></i> Search
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-header d-flex justify-content-center mt-3">
                        Programs
                        <div class="btn-actions-pane-right">
                            <button class="btn btn-warning mr-2" id="exportExcelBtn">⬇ Export to Excel</button>
                            <?php if ($canadd): ?>
                                <a href="add.php" class="btn btn-success">
                                    <i class="fa fa-plus" aria-hidden="true"></i> Add New Program
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">Si.No</th>
                                    <th class="text-center">Program.No</th>
                                    <th class="text-center">Title</th>
                                    <th class="text-center">centre</th>
                                    <th class="text-center">Region</th>
                                    <th class="text-center">Country</th>
                                    <th class="text-center">Start Date</th>
                                    <th class="text-center">End Date</th>
                                    <th class="text-center">Instructor</th>
                                    <th class="text-center">Venue</th>
                                    <th class="text-center">Participants</th>
                                    <th class="text-center">Entry Progress</th>
                                    <th class="text-center">Completion Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tabledata-new">
                                <?php
                                $recordsPerPage = 10;
                                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
                                $offset = ($page - 1) * $recordsPerPage;

                                // Get search parameters
                                $searchParams = [
                                    'searchtitle' => !empty($_GET['searchtitle']) ? trim($_GET['searchtitle']) : "",
                                    'program_number' => !empty($_GET['program_number']) ? trim($_GET['program_number']) : "",
                                    'centre_id' => isset($_GET['centre_id']) && $_GET['centre_id'] !== "" ? intval($_GET['centre_id']) : null,
                                    'regional_id' => isset($_GET['regional_id']) && $_GET['regional_id'] !== "" ? intval($_GET['regional_id']) : null,
                                    'country_id' => isset($_GET['country_id']) && $_GET['country_id'] !== "" ? intval($_GET['country_id']) : null,
                                    'instructor' => !empty($_GET['instructor']) ? trim($_GET['instructor']) : "",
                                    'date_from' => !empty($_GET['date_from']) ? trim($_GET['date_from']) : null,
                                    'date_to' => !empty($_GET['date_to']) ? trim($_GET['date_to']) : null,
                                    'status' => isset($_GET['status']) && $_GET['status'] !== "" ? $_GET['status'] : null,
                                    'completion_status' => isset($_GET['completion_status']) && $_GET['completion_status'] !== "" ? $_GET['completion_status'] : null
                                ];

                                try {
                                    // Build WHERE conditions
                                    $whereConditions = [];
                                    $queryParams = [];

                                    $showPending = canUserViewProgram($pdo, 'Program Management');
                                    if ($showPending) {
                                        // Show both pending and activated programs
                                        $whereConditions[] = "programs.status IN ('pending', 'activated')";
                                    } else {
                                        // Show only activated programs
                                        $whereConditions[] = "programs.status = 'activated'";
                                    }

                                    if (!empty($searchParams['searchtitle'])) {
                                        $whereConditions[] = "programs.title LIKE :searchtitle";
                                        $queryParams['searchtitle'] = "%{$searchParams['searchtitle']}%";
                                    }

                                    if ($searchParams['centre_id'] !== null) {
                                        $whereConditions[] = "programs.centre_id = :centre_id";
                                        $queryParams['centre_id'] = $searchParams['centre_id'];
                                    }

                                    if ($searchParams['regional_id'] !== null) {
                                        $whereConditions[] = "programs.regional_id = :regional_id";
                                        $queryParams['regional_id'] = $searchParams['regional_id'];
                                    }

                                    if ($searchParams['country_id'] !== null) {
                                        $whereConditions[] = "programs.country_id = :country_id";
                                        $queryParams['country_id'] = $searchParams['country_id'];
                                    }

                                    if (!empty($searchParams['instructor'])) {
                                        $whereConditions[] = "programs.instructor_name LIKE :instructor";
                                        $queryParams['instructor'] = "%{$searchParams['instructor']}%";
                                    }

                                    if ($searchParams['status'] !== null) {
                                        $whereConditions[] = "programs.status = :status";
                                        $queryParams['status'] = $searchParams['status'];
                                    }
                                    if (!empty($searchParams['program_number'])) {
                                        $whereConditions[] = "programs.program_number = :program_number";
                                        $queryParams['program_number'] = $searchParams['program_number'];
                                    }

                                    if ($searchParams['date_from'] !== null) {
                                        $whereConditions[] = "EXISTS (
                                            SELECT 1 FROM program_sessions_times s
                                            WHERE s.program_id = programs.id AND s.session_start >= :date_from
                                        )";
                                        $queryParams['date_from'] = $searchParams['date_from'] . " 00:00:00";
                                    }

                                    if ($searchParams['date_to'] !== null) {
                                        $whereConditions[] = "EXISTS (
                                            SELECT 1 FROM program_sessions_times s
                                            WHERE s.program_id = programs.id AND s.session_end <= :date_to
                                        )";
                                        $queryParams['date_to'] = $searchParams['date_to'] . " 23:59:59";
                                    }

                                    if ($searchParams['completion_status'] !== null) {
                                        $whereConditions[] = "programs.completion_status = :completion_status";
                                        $queryParams['completion_status'] = $searchParams['completion_status'];
                                    }

                                    $whereClause = $whereConditions ? "WHERE " . implode(" AND ", $whereConditions) : "";

                                    // Count query
                                    $countQuery = "SELECT COUNT(*) FROM programs 
                                                 LEFT JOIN centres ON programs.centre_id = centres.id
                                                 LEFT JOIN regionals ON programs.regional_id = regionals.id
                                                 LEFT JOIN countries ON programs.country_id = countries.id
                                                 $whereClause";
                                    $stmtCount = $pdo->prepare($countQuery);
                                    $stmtCount->execute($queryParams);
                                    $totalRecords = $stmtCount->fetchColumn();
                                    $totalPages = ceil($totalRecords / $recordsPerPage);

                                    // Main query
                                    $query = "SELECT 
    programs.*, 
    centres.centre_name,
    regionals.regional_name,
    countries.country_name,
    (SELECT MIN(session_start) FROM program_sessions_times WHERE program_id = programs.id) AS session_start_min,
    (SELECT MAX(session_end) FROM program_sessions_times WHERE program_id = programs.id) AS session_end_max,
    (SELECT COUNT(*) FROM participants WHERE program_id = programs.id) AS participant_count
FROM programs 
LEFT JOIN centres ON programs.centre_id = centres.id
LEFT JOIN regionals ON programs.regional_id = regionals.id
LEFT JOIN countries ON programs.country_id = countries.id
$whereClause
ORDER BY programs.program_number DESC, programs.updated_at DESC 
LIMIT :limit OFFSET :offset";

                                    $stmt = $pdo->prepare($query);

                                    // Bind all parameters
                                    foreach ($queryParams as $key => $value) {
                                        $stmt->bindValue(':' . $key, $value);
                                    }

                                    $stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
                                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                                    $stmt->execute();

                                    $counter = $offset + 1;

                                    while ($row = $stmt->fetch()) {
                                        $isActive = strtolower(trim($row['status'])) === 'active';
                                        $blockText = $isActive ? 'Block' : 'Unblock';
                                        $blockIcon = $isActive ? '🚫' : '✅';
                                        $blockColor = $isActive ? 'text-warning' : 'text-success';
                                        $statusBadge = $isActive ? 'badge-success' : 'badge-danger';

                                        // Participants display: use actual count from participants table
                                        $participantsDisplay = $row['participant_count'] . '/' . $row['max_participants'];
                                ?>
                                        <tr id="row-<?= $row['id'] ?>">
                                            <td class="text-center text-muted"><?= $counter ?></td>
                                            <td class="text-center"><strong><?= htmlspecialchars($row['program_number']) ?></strong></td>
                                            <td class="text-center"><strong><?= htmlspecialchars($row['title']) ?></strong></td>
                                            <td class="text-center"><?= htmlspecialchars($row['centre_name']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['regional_name']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['country_name']) ?></td>
                                            <td class="text-center">
                                                <?= $row['session_start_min'] ? date('M d, Y H:i', strtotime($row['session_start_min'])) : '-' ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $row['session_end_max'] ? date('M d, Y H:i', strtotime($row['session_end_max'])) : '-' ?>
                                            </td>
                                            <td class="text-center"><?= htmlspecialchars($row['instructor_name']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['venue']) ?></td>
                                            <td class="text-center"><?= $participantsDisplay ?></td>
                                            <td class="text-center">
                                                <?php
                                                $statusLower = strtolower(trim($row['status']));
                                                if ($statusLower === 'activated') {
                                                    $statusBadge = 'badge-success';
                                                } elseif ($statusLower === 'pending') {
                                                    $statusBadge = 'badge-warning';
                                                } else {
                                                    $statusBadge = 'badge-danger';
                                                }
                                                ?>
                                                <span class="badge <?= $statusBadge ?>"><?= ucfirst($row['status']) ?></span>
                                            </td>
                                            <td class="text-center">
    <?php
    $cs = strtolower($row['completion_status']);
    if ($cs === 'completed') {
        $csBadge = 'badge-success';
        $csText = 'Completed';
    } elseif ($cs === 'in_progress') {
        $csBadge = 'badge-warning';
        $csText = 'In Progress';
    } else {
        $csBadge = 'badge-secondary';
        $csText = 'Not Started';
    }
    ?>
    <span class="badge <?= $csBadge ?>"><?= $csText ?></span>
</td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <a style="cursor:pointer;" class="dropdown-toggle" type="button"
                                                        id="dropdownMenu<?= $row['id'] ?>" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        •••
                                                    </a>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenu<?= $row['id'] ?>">

                                                        <?php if ($canedit): ?>
                                                            <a class="dropdown-item text-primary" href="#"
                                                                onclick="editProgram(<?= $row['id'] ?>)">✏ Edit Program</a>
                                                        <?php endif; ?>
                                                        <a class="dropdown-item text-primary" href="#"
                                                            onclick="AddmarketingProgram(<?= $row['id'] ?>)">+ Add Marketing Details</a>
                                                        <a class="dropdown-item text-primary" href="#"
                                                            onclick="viewProgram(<?= $row['id'] ?>)">👁️ View Details</a>
                                                        <a class="dropdown-item text-primary" href="#"
                                                            onclick="registrationlinkview(<?= $row['id'] ?>)">📝 Get Registration Link</a>

                                                        <?php if ($candelete): ?>
                                                            <a class="dropdown-item text-danger" href="#"
                                                                onclick="deleteProgram(<?= $row['id'] ?>)">🗑️ Delete
                                                            <?php endif; ?>
                                                            
                                                            <a class="dropdown-item text-primary" href="#"
                                                                onclick="printProgram(<?= $row['id'] ?>)">🖨️ Print</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                <?php
                                        $counter++;
                                    }
                                } catch (PDOException $e) {
                                    echo '<tr><td colspan="11" class="text-center text-danger">Error loading Program data: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        <ul class="pagination pagination-sm">
                            <?php
                            $queryParams = $_GET;
                            unset($queryParams['page']);
                            $queryString = http_build_query($queryParams);

                            // Previous page link
                            if ($page > 1) {
                                echo '<li class="page-item">
                                        <a href="?page=' . ($page - 1) . (!empty($queryString) ? '&' . $queryString : '') . '" 
                                           class="page-link" aria-label="Previous">
                                            <span aria-hidden="true">«</span>
                                        </a>
                                      </li>';
                            } else {
                                echo '<li class="page-item disabled"><span class="page-link">«</span></li>';
                            }

                            // Page links
                            for ($i = 1; $i <= $totalPages; $i++) {
                                $active = $i == $page ? 'active' : '';
                                echo '<li class="page-item ' . $active . '">
                                        <a href="?page=' . $i . (!empty($queryString) ? '&' . $queryString : '') . '" 
                                           class="page-link">' . $i . '</a>
                                      </li>';
                            }

                            // Next page link
                            if ($page < $totalPages) {
                                echo '<li class="page-item">
                                        <a href="?page=' . ($page + 1) . (!empty($queryString) ? '&' . $queryString : '') . '" 
                                           class="page-link" aria-label="Next">
                                            <span aria-hidden="true">»</span>
                                        </a>
                                      </li>';
                            } else {
                                echo '<li class="page-item disabled"><span class="page-link">»</span></li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Programs</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="importForm" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="csvFile">Upload CSV File</label>
                            <input type="file" class="form-control" id="csvFile" name="csvFile" accept=".csv" required>
                            <small class="form-text text-muted">
                                CSV format should include: title, centre_id, venue, instructor_name, max_participants
                            </small>
                        </div>
                        <button type="submit" class="btn btn-success">Upload & Import</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
    <?php include '../Includes/footer.php'; ?>
    <script>
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2();

            // Country-state-centre cascading dropdown
            $('#country_id').on('change', function() {
                var countryId = $(this).val();
                if (countryId) {
                    // Empty state and centre dropdowns
                    $('#regional_id').empty().append('<option value="">All regionals</option>');
                    $('#centre_id').empty().append('<option value="">All centres</option>');

                    // Fetch regionals for selected country
                    $.ajax({
                        url: 'get_regionals.php',
                        type: 'POST',
                        data: {
                            country_id: countryId
                        },
                        dataType: 'json',
                        success: function(data) {
                            $.each(data, function(key, value) {
                                $('#regional_id').append('<option value="' + value.id + '">' + value.regional_name + '</option>');
                            });
                        }
                    });
                }
            });

            $('#regional_id').on('change', function() {
                var stateId = $(this).val();
                if (stateId) {
                    // Empty centre dropdown
                    $('#centre_id').empty().append('<option value="">All centres</option>');

                    // Fetch centres for selected state
                    $.ajax({
                        url: 'get_centres.php',
                        type: 'POST',
                        data: {
                            regional_id: stateId
                        },
                        dataType: 'json',
                        success: function(data) {
                            $.each(data, function(key, value) {
                                $('#centre_id').append('<option value="' + value.id + '">' + value.centre_name + '</option>');
                            });
                        }
                    });
                }
            });

            // Handle import form submission
            $("#importForm").submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: "import.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        response = response.trim();
                        if (response.startsWith("success:")) {
                            swal("Imported!", response.replace("success: ", ""), "success")
                                .then(() => location.reload());
                        } else {
                            swal("Error!", response.replace("error: ", ""), "error");
                        }
                    },
                    error: function() {
                        swal("Error!", "Something went wrong!", "error");
                    }
                });
            });

            // Show success message if present in URL
            const urlParams = new URLSearchParams(window.location.search);
            const message = urlParams.get('message');
            if (message) {
                swal({
                    title: "Success",
                    text: decodeURIComponent(message.replace(/\+/g, ' ')),
                    icon: "success",
                    button: "OK"
                }).then(() => {
                    // Remove message from URL
                    const url = new URL(window.location.href);
                    url.searchParams.delete('message');
                    window.history.replaceState({}, document.title, url.toString());
                });
            }

            $("#exportExcelBtn").click(function() {
    // Build query string from current filters
    const params = new URLSearchParams(window.location.search);
    window.open("export.php?" + params.toString(), "_blank");
});
        });

        function editProgram(id) {
            window.location.href = "edit.php?id=" + id;
        }

        function AddmarketingProgram(id) {
            window.location.href = "add-program-marketing.php?id=" + id;
        }

        function viewProgram(id) {
            window.location.href = "view.php?id=" + id;
        }

        function printProgram(id) {
            window.location.href = "Print/?id=" + id;
        }


        function deleteProgram(id) {
            swal({
                title: "Are you sure?",
                text: "This Program will be permanently deleted!",
                icon: "warning",
                buttons: true,
                dangerMode: true
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "delete.php",
                        type: "POST",
                        data: {
                            id: id
                        },
                        success: function(response) {
                            response = response.trim();
                            if (response === "success") {
                                swal("Deleted!", "The Program has been removed.", "success")
                                    .then(() => {
                                        // Remove the row from the table
                                        $("#row-" + id).fadeOut(300, function() {
                                            $(this).remove();
                                            // Update the serial numbers
                                            updateSerialNumbers();
                                        });
                                    });
                            } else if (response.startsWith("error:")) {
                                swal("Error!", response.substring(6), "error");
                            } else {
                                swal("Error!", "Unknown response from server", "error");
                            }
                        },
                        error: function(xhr, status, error) {
                            swal("Error!", "AJAX Error: " + error, "error");
                        }
                    });
                }
            });
        }

        function blockProgram(id, currentStatus) {
            let newStatus = (currentStatus.toLowerCase() === "active") ? "Blocked" : "Active";
            let actionText = (newStatus === "Blocked") ? "block" : "unblock";

            swal({
                title: "Confirm",
                text: "Are you sure you want to " + actionText + " this Program?",
                icon: "warning",
                buttons: true,
                dangerMode: true
            }).then((confirm) => {
                if (confirm) {
                    $.ajax({
                        url: "block.php",
                        type: "POST",
                        data: {
                            id: id,
                            status: newStatus
                        },
                        success: function(response) {
                            response = response.trim();
                            if (response === "success") {
                                swal("Updated!", "Program has been " + actionText + "ed.", "success")
                                    .then(() => location.reload());
                            } else if (response.startsWith("error:")) {
                                swal("Error!", response.substring(6), "error");
                            } else {
                                swal("Error!", "Unknown response from server", "error");
                            }
                        },
                        error: function(xhr, status, error) {
                            swal("Error!", "AJAX Error: " + error, "error");
                        }
                    });
                }
            });
        }

        function updateSerialNumbers() {
            $("tbody#tabledata-new tr").each(function(index) {
                $(this).find("td:first").text(index + 1);
            });
        }

        function registrationlinkview(programId) {
            const link = `${SITE_URL}/views/participant-registration/index.php?program-id=${programId}`;
            swal({
                title: "Registration Link",
                content: {
                    element: "input",
                    attributes: {
                        value: link,
                        readonly: true,
                        id: "reg-link-input"
                    }
                },
                buttons: {
                    copy: {
                        text: "Copy Link",
                        value: "copy"
                    },
                    close: {
                        text: "Close",
                        value: null
                    }
                }
            }).then((value) => {
                if (value === "copy") {
                    const input = document.getElementById("reg-link-input");
                    input.select();
                    input.setSelectionRange(0, 99999); // For mobile devices
                    document.execCommand("copy");
                    swal("Copied!", "Registration link copied to clipboard.", "success");
                }
            });
        }
    </script>
    <script>
        const SITE_URL = "<?= SITE_URL ?>";
    </script>
</div>
</body>

</html>