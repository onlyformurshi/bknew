<?php include '../Includes/header.php'; ?>

<?php
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Regional Management');
$canadd = canUsercan_add($pdo, 'Regional Management'); // <-- use new function
$candelete = canUsercan_delete($pdo, 'Regional Management'); // <-- use new function
$canedit = canUsercan_edit($pdo, 'Regional Management');
// Initialize variables
$totalPages = 1;
$totalregionals = $activeregionals = $blockedregionals = 0;

try {
    // Fetch state counts
    $stmtTotal = $pdo->query("SELECT COUNT(*) FROM regionals");
    $totalregionals = $stmtTotal->fetchColumn();

    $stmtActive = $pdo->query("SELECT COUNT(*) FROM regionals WHERE LOWER(TRIM(status)) = 'active'");
    $activeregionals = $stmtActive->fetchColumn();

    $stmtBlocked = $pdo->query("SELECT COUNT(*) FROM regionals WHERE LOWER(TRIM(status)) = 'blocked'");
    $blockedregionals = $stmtBlocked->fetchColumn();

    // Fetch countries for dropdown
    $countries = $pdo->query("SELECT id, country_name FROM countries ORDER BY country_name")->fetchAll(PDO::FETCH_ASSOC);
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
                                <i class="lnr-apartment opacity-6"></i>
                            </span>
                            <span class="d-inline-block">Regional Management</span>
                        </div>
                    </div>
                </div>
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Regional Management</a></li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-6 col-xl-4">
                <div class="card mb-3 widget-content">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                                <div class="widget-heading">Total Regions</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers text-info"><?= $totalregionals ?></div>
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
                                <div class="widget-heading">Active Regions</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers text-success"><?= $activeregionals ?></div>
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
                                <div class="widget-heading">Blocked Regions</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers text-danger"><?= $blockedregionals ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Search</h5>
                        <form action="" method="GET">
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="searchname">Region Name</label>
                                    <input placeholder="Enter Region Name" type="text" class="form-control" id="searchname" name="searchname"
                                        value="<?= isset($_GET['searchname']) ? htmlspecialchars($_GET['searchname']) : '' ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="d-felx" for="country_id">Country</label>
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
                            <div class="d-flex justify-content-end">
                                <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>"
                                    class="btn btn-outline-dark mx-3">
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
                        Regions
                        <div class="btn-actions-pane-right">
                            <a href="blocked.php" class="btn btn-danger mr-2">🚫 Blocked List</a>
                            <button class="btn btn-info mr-2" data-toggle="modal" data-target="#importModal">📂 Import</button>
                            <?php if ($canadd): ?>
                                <a href="add.php" class="btn btn-success">➕ Add New</a>
                            <?php endif; ?>

                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">Si.No</th>
                                    <th class="text-center">Region Name</th>
                                    <th class="text-center">Country</th>
                                    <th class="text-center">Status</th>
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
                                    'searchname' => !empty($_GET['searchname']) ? trim($_GET['searchname']) : "",
                                    'country_id' => isset($_GET['country_id']) && $_GET['country_id'] !== "" ? intval($_GET['country_id']) : null,
                                    'status' => isset($_GET['status']) && $_GET['status'] !== "" ? $_GET['status'] : null
                                ];

                                try {
                                    // Build WHERE conditions
                                    $whereConditions = [];
                                    $queryParams = [];

                                    if (!empty($searchParams['searchname'])) {
                                        $whereConditions[] = "regionals.regional_name LIKE :searchname";
                                        $queryParams['searchname'] = "%{$searchParams['searchname']}%";
                                    }

                                    if ($searchParams['country_id'] !== null) {
                                        $whereConditions[] = "regionals.country_id = :country_id";
                                        $queryParams['country_id'] = $searchParams['country_id'];
                                    }

                                    if ($searchParams['status'] !== null) {
                                        $whereConditions[] = "regionals.status = :status";
                                        $queryParams['status'] = $searchParams['status'];
                                    }

                                    $whereClause = $whereConditions ? "WHERE " . implode(" AND ", $whereConditions) : "";

                                    // Count query
                                    $countQuery = "SELECT COUNT(*) FROM regionals 
                                                 LEFT JOIN countries ON regionals.country_id = countries.id
                                                 $whereClause";
                                    $stmtCount = $pdo->prepare($countQuery);
                                    $stmtCount->execute($queryParams);
                                    $totalRecords = $stmtCount->fetchColumn();
                                    $totalPages = ceil($totalRecords / $recordsPerPage);

                                    // Main query
                                    $query = "SELECT regionals.*, 
                                             countries.country_name
                                           FROM regionals 
                                           LEFT JOIN countries ON regionals.country_id = countries.id
                                           $whereClause
                                           ORDER BY regionals.id ASC 
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
                                ?>
                                        <tr id="row-<?= $row['id'] ?>">
                                            <td class="text-center text-muted"><?= $counter ?></td>
                                            <td class="text-center"><strong><?= htmlspecialchars($row['regional_name']) ?></strong></td>
                                            <td class="text-center"><?= htmlspecialchars($row['country_name']) ?></td>
                                            <td class="text-center"><span class="badge <?= $statusBadge ?>"><?= ucfirst($row['status']) ?></span></td>
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
                                                                onclick="editState(<?= $row['id'] ?>)">✏ Edit</a>
                                                        <?php endif; ?>
                                                        <?php if ($candelete): ?>
                                                            
                                                            <a class="dropdown-item text-danger" href="#"
                                                                onclick="deleteState(<?= $row['id'] ?>)">🗑 Delete</a>
                                                                <?php endif; ?>
                                                        <a class="dropdown-item <?= $blockColor ?>" href="#"
                                                            onclick="blockState(<?= $row['id'] ?>, '<?= $row['status'] ?>')">
                                                            <?= $blockIcon ?> <?= $blockText ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                <?php
                                        $counter++;
                                    }
                                } catch (PDOException $e) {
                                    echo '<tr><td colspan="5" class="text-center text-danger">Error loading state data: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
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
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Regional</h5>
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
                            CSV format should include: State Name, country_id, status
                        </small>
                    </div>
                    <button type="submit" class="btn btn-success">Upload & Import</button>
                </form>
            </div>
        </div>

        <?php include '../Includes/footer.php'; ?>

        <!-- JavaScript -->
        <script>
            $(document).ready(function() {
                // Initialize select2
                $('.select2').select2();

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
            });

            function editState(id) {
                window.location.href = "edit.php?id=" + id;
            }

            function deleteState(id) {
                swal({
                    title: "Are you sure?",
                    text: "This Region will be permanently deleted!",
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
                                    swal("Deleted!", "The Region has been removed.", "success")
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

            function blockState(id, currentStatus) {
                let newStatus = (currentStatus.toLowerCase() === "active") ? "Blocked" : "Active";
                let actionText = (newStatus === "Blocked") ? "block" : "unblock";

                swal({
                    title: "Confirm",
                    text: `Are you sure you want to ${actionText} this Region?`,
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
                                    swal("Updated!", `Region has been ${actionText}ed.`, "success")
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
        </script>

        <style>
            .select2 {
                width: 100% !important;
            }
        </style>