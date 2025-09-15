<?php include '../Includes/header.php'; ?>

<?php
require '../../config/config.php'; // Ensure database connection is established
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Country Management');
$canadd = canUsercan_add($pdo, 'Country Management'); // <-- use new function
$candelete = canUsercan_delete($pdo, 'Country Management'); // <-- use new function
$canedit = canUsercan_edit($pdo, 'Country Management');

try {
    // Fetch total countries count
    $stmtTotal = $pdo->query("SELECT COUNT(*) FROM countries");
    $totalCountries = $stmtTotal->fetchColumn();

    // Fetch active countries count
    $stmtActive = $pdo->query("SELECT COUNT(*) FROM countries WHERE LOWER(TRIM(status)) = 'active'");
    $activeCountries = $stmtActive->fetchColumn();

    // Fetch blocked countries count
    $stmtBlocked = $pdo->query("SELECT COUNT(*) FROM countries WHERE LOWER(TRIM(status)) = 'blocked'");
    $blockedCountries = $stmtBlocked->fetchColumn();
} catch (PDOException $e) {
    $totalCountries = $activeCountries = $blockedCountries = 0;
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
                            <span class="d-inline-block">Country Management</span>
                        </div>
                    </div>
                </div>

                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Country Management</a></li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3 widget-content">
                        <div class="widget-content-outer">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left">
                                    <div class="widget-heading">Total Country</div>
                                    <div class="widget-subheading"></div>
                                </div>
                                <div class="widget-content-right">
                                    <div class="widget-numbers text-info"><?= $totalCountries ?></div>
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
                                    <div class="widget-heading">Active Country</div>
                                    <div class="widget-subheading"></div>
                                </div>
                                <div class="widget-content-right">
                                    <div class="widget-numbers text-success"><?= $activeCountries ?></div>
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
                                    <div class="widget-heading">Blocked Country</div>
                                    <div class="widget-subheading"></div>
                                </div>
                                <div class="widget-content-right">
                                    <div class="widget-numbers text-danger"><?= $blockedCountries ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row ">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title">Search</h5>
                            <form action="" method="GET">
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="searchname">Country Name</label>
                                        <input type="text" class="form-control" id="searchname" name="searchname"
                                            value="<?= isset($_GET['searchname']) ? htmlspecialchars($_GET['searchname']) : '' ?>">
                                        <div class="invalid-feedback">
                                            Please enter a country name.
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <!-- Reset Button redirects to the page without query parameters -->
                                    <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>"
                                        class="btn btn-outline-dark mx-3"><i class="fa fa-window-restore"
                                            aria-hidden="true"></i>
                                        Reset</a>
                                    <button class="btn btn-success" type="submit"><i class="fa fa-search"
                                            aria-hidden="true"></i>
                                        Search</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header d-flex justify-content-center">Country
                            <div class="btn-actions-pane-right">
                                <div role="group" class="btn-group-sm btn-group">
                                    <div class="app-header-left py-1">
                                        <div class="search-wrapper">
                                            <div class="input-holder">
                                            </div>
                                            <button id="search-close-new" class="close"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="blocked.php"><button class=" mr-2 btn btn-danger" fdprocessedid="07uj0u">🚫
                                    Blocked List
                                </button></a>


                            

                            <?php if ($canadd): ?>
                                <a href="add.php"><button class=" mr-2 btn btn-success" fdprocessedid="07uj0u"><i
                                            class="fa fa-plus" aria-hidden="true"></i>
                                        Add New Country
                                    </button></a>
                            <?php endif; ?>


                        </div>
                        <div class="table-responsive">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">Si.No</th>
                                        <th class="text-center">Country Name</th>
                                        <th class="text-center">Country Code</th>
                                        <th class="text-center">Currency</th>
                                        <th class="text-center">Language</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tabledata-new">
                                    <?php
                                    // Set records per page
                                    $recordsPerPage = 10;
                                    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
                                    $offset = ($page - 1) * $recordsPerPage;
                                    $searchQuery = !empty($_GET['searchname']) ? trim($_GET['searchname']) : "";

                                    try {
                                        // Get total records count
                                        $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM countries WHERE country_name LIKE :search");
                                        $stmtCount->execute(['search' => "%$searchQuery%"]);
                                        $totalRecords = $stmtCount->fetchColumn();
                                        $totalPages = ceil($totalRecords / $recordsPerPage);

                                        // Fetch records with pagination
                                        $stmt = $pdo->prepare("SELECT * FROM countries WHERE country_name LIKE :search ORDER BY id ASC LIMIT :limit OFFSET :offset");
                                        $stmt->bindValue(':search', "%$searchQuery%", PDO::PARAM_STR);
                                        $stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
                                        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                                        $stmt->execute();

                                        $counter = $offset + 1;

                                        while ($row = $stmt->fetch()) {
                                            // Determine status & styling
                                            $isActive = strtolower(trim($row['status'])) === 'active';
                                            $blockText = $isActive ? 'Block' : 'Unblock';
                                            $blockIcon = $isActive ? '🚫' : '✅';
                                            $blockColor = $isActive ? 'text-warning' : 'text-success';
                                            $statusBadge = $isActive ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Blocked</span>';

                                            echo "<tr id='row-{$row['id']}'>
    <td class='text-center text-muted'>{$counter}</td>
    <td class='text-center'><strong>{$row['country_name']}</strong></td>
    <td class='text-center'>{$row['country_code']}</td>
    <td class='text-center'>{$row['currency']}</td>
    <td class='text-center'>{$row['language']}</td>
    <td class='text-center'>{$statusBadge}</td>
    <td class='text-center'>
        <div class='dropdown'>
            <a style='cursor:pointer;' class=' dropdown-toggle' type='button' id='dropdownMenu{$row['id']}' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                •••
            </a>
            <div class='dropdown-menu' aria-labelledby='dropdownMenu{$row['id']}'>";
if ($canedit) {
    echo "<a class='dropdown-item text-primary' href='#' onclick='editCountry({$row['id']})'>✏ Edit</a>";
}
if ($candelete) {
    echo "<a class='dropdown-item text-danger' href='#' onclick='deleteCountry({$row['id']})'>🗑 Delete</a>";
}
echo "      <a class='dropdown-item {$blockColor}' href='#' onclick='blockCountry({$row['id']}, \"{$row['status']}\")'>
                {$blockIcon} {$blockText}
            </a>
            </div>
        </div>
    </td>
</tr>";

                                            $counter++;
                                        }
                                    } catch (PDOException $e) {
                                        echo "<tr><td colspan='7' class='text-center text-danger'>Database Error: " . $e->getMessage() . "</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>








                            <script>
                                function editCountry(id) {
                                    window.location.href = "edit.php?id=" + id;
                                }

                                function deleteCountry(id) {
                                    swal({
                                        title: "Are you sure?",
                                        text: "This country will be permanently deleted!",
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
                                                    if (response.trim() === "success") {
                                                        swal("Deleted!", "The country has been removed.", "success");
                                                        $("#row-" + id).remove();
                                                    } else {
                                                        swal("Error!", "Unable to delete the country.", "error");
                                                    }
                                                },
                                                error: function() {
                                                    swal("Error!", "Something went wrong!", "error");
                                                }
                                            });
                                        }
                                    });
                                }

                                function blockCountry(id, currentStatus) {
                                    let newStatus = (currentStatus.toLowerCase() === "active") ? "Blocked" : "Active";

                                    swal({
                                        title: "Change Status?",
                                        text: "You are about to mark this country as " + newStatus,
                                        icon: "warning",
                                        buttons: true,
                                        dangerMode: true
                                    }).then((willBlock) => {
                                        if (willBlock) {
                                            $.ajax({
                                                url: "block.php",
                                                type: "POST",
                                                data: {
                                                    id: id,
                                                    status: newStatus
                                                },
                                                success: function(response) {
                                                    if (response.trim() === "success") {
                                                        swal("Updated!", "Country status changed to " + newStatus, "success")
                                                            .then(() => location.reload());
                                                    } else {
                                                        swal("Error!", "Unable to update country status.", "error");
                                                    }
                                                },
                                                error: function() {
                                                    swal("Error!", "Something went wrong!", "error");
                                                }
                                            });
                                        }
                                    });
                                }
                            </script>

                        </div>
                    </div>
                    <ul class="pagination pagination-sm d-flex justify-content-end">
                        <?php
                        $queryParams = $_GET;
                        unset($queryParams['page']); // Remove old page number
                        unset($queryParams['message']); // Remove success message

                        $queryString = http_build_query($queryParams);

                        if ($page > 1): ?>
                            <li class="page-item">
                                <a href="?page=<?= $page - 1 ?><?= !empty($queryString) ? '&' . $queryString : '' ?>"
                                    class="page-link" aria-label="Previous">
                                    <span aria-hidden="true">«</span>
                                    <span class="sr-only">Previous</span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled"><span class="page-link">«</span></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a href="?page=<?= $i ?><?= !empty($queryString) ? '&' . $queryString : '' ?>"
                                    class="page-link"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a href="?page=<?= $page + 1 ?><?= !empty($queryString) ? '&' . $queryString : '' ?>"
                                    class="page-link" aria-label="Next">
                                    <span aria-hidden="true">»</span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled"><span class="page-link">»</span></li>
                        <?php endif; ?>
                    </ul>



                </div>
            </div>
        </div>
    </div>
    <?php include '../Includes/footer.php'; ?>
    <script>
        $(document).ready(function() {
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


            }); // <-- This was missing!
        }); // <-- This was also missing!
    </script>
    <script>
        $(document).ready(function() {
            // Get the message from the URL
            const urlParams = new URLSearchParams(window.location.search);
            const message = urlParams.get('message'); // Get message from URL

            if (message) {
                let title = "Success";
                let icon = "success";

                // Show SweetAlert popup
                swal({
                    title: title,
                    text: message,
                    icon: icon
                }).then(() => {
                    // Remove 'message' parameter from URL after showing alert
                    const url = new URL(window.location.href);
                    url.searchParams.delete('message');
                    window.history.replaceState({}, document.title, url.toString());
                });
            }
        });
    </script>
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Blocked Countries</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="importForm" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="csvFile">Upload CSV File</label>
                            <input type="file" class="form-control" id="csvFile" name="csvFile" accept=".csv" required>
                        </div>
                        <button type="submit" class="btn btn-success">Upload & Import</button>
                    </form>

                </div>
            </div>
        </div>

        </body>

        </html>