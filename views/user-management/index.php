<?php include '../Includes/header.php'; ?>

<?php
require '../../config/config.php'; // Ensure database connection is established
require '../../helpers/security.php'; // make sure this has encrypt_text()

require_once '../../config/functions.php'; // Include functions file
checkModuleAccess($pdo, 'User Management'); // Check if the user has access to this
try {
    // Fetch total users count
    $stmtTotal = $pdo->query("SELECT COUNT(*) FROM users");
    $totalUsers = $stmtTotal->fetchColumn();

    // Fetch active users count
    $stmtActive = $pdo->query("SELECT COUNT(*) FROM users WHERE LOWER(TRIM(status)) = 'active'");
    $activeUsers = $stmtActive->fetchColumn();

    // Fetch blocked users count
    $stmtBlocked = $pdo->query("SELECT COUNT(*) FROM users WHERE LOWER(TRIM(status)) = 'blocked'");
    $blockedUsers = $stmtBlocked->fetchColumn();
} catch (PDOException $e) {
    $totalUsers = $activeUsers = $blockedUsers = 0;
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
                                <i class="lnr-users opacity-6"></i>
                            </span>
                            <span class="d-inline-block">User Management</span>
                        </div>
                    </div>
                </div>

                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="User_management.php">User Management</a></li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3 widget-content">
                        <div class="widget-content-outer">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left">
                                    <div class="widget-heading">Total Users</div>
                                    <div class="widget-subheading"></div>
                                </div>
                                <div class="widget-content-right">
                                    <div class="widget-numbers text-info"><?= $totalUsers ?></div>
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
                                    <div class="widget-heading">Active Users</div>
                                    <div class="widget-subheading"></div>
                                </div>
                                <div class="widget-content-right">
                                    <div class="widget-numbers text-success"><?= $activeUsers ?></div>
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
                                    <div class="widget-heading">Blocked Users</div>
                                    <div class="widget-subheading"></div>
                                </div>
                                <div class="widget-content-right">
                                    <div class="widget-numbers text-danger"><?= $blockedUsers ?></div>
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
                                    <div class="col-md-3 mb-3">
                                        <label for="searchname">Full Name</label>
                                        <input type="text" class="form-control" id="searchname" name="searchname"
                                            value="<?= isset($_GET['searchname']) ? htmlspecialchars($_GET['searchname']) : '' ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="searchemail">Email</label>
                                        <input type="text" class="form-control" id="searchemail" name="searchemail"
                                            value="<?= isset($_GET['searchemail']) ? htmlspecialchars($_GET['searchemail']) : '' ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="searchphone">Phone Number</label>
                                        <input type="text" class="form-control" id="searchphone" name="searchphone"
                                            value="<?= isset($_GET['searchphone']) ? htmlspecialchars($_GET['searchphone']) : '' ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="searchrole">Role</label>
                                        <select class="form-control" id="searchrole" name="searchrole">
                                            <option value="">All Roles</option>
                                            <?php
                                            try {
                                                $stmtRoles = $pdo->query("SELECT id, role_name FROM user_roles ORDER BY role_name ASC");
                                                $roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($roles as $role) {
                                                    $selected = (isset($_GET['searchrole']) && $_GET['searchrole'] == $role['id']) ? 'selected' : '';
                                                    echo "<option value=\"" . htmlspecialchars($role['id']) . "\" $selected>" . htmlspecialchars($role['role_name']) . "</option>";
                                                }
                                            } catch (PDOException $e) {
                                                echo "<option disabled>Error loading roles</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>"
                                        class="btn btn-outline-dark mx-3"><i class="fa fa-window-restore"
                                            aria-hidden="true"></i> Reset</a>
                                    <button class="btn btn-success" type="submit"><i class="fa fa-search"
                                            aria-hidden="true"></i> Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header d-flex justify-content-center">Users
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
                            <a href="blocked.php"><button class=" mr-2 btn btn-danger" fdprocessedid="07uj0u">🚫 Blocked List</button></a>
                            <button class=" mr-2 btn btn-info" fdprocessedid="07uj0u" data-toggle="modal" data-target="#importModal">📂 Import</button>
                            <a href="add.php"><button class=" mr-2 btn btn-success" fdprocessedid="07uj0u"><i class="fa fa-plus" aria-hidden="true"></i> Add New User</button></a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SI No</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Role</th>
                                        <th>Password</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Set records per page
                                    $recordsPerPage = 10;
                                    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
                                    $offset = ($page - 1) * $recordsPerPage;

                                    // Search parameters
                                    $searchName = !empty($_GET['searchname']) ? '%' . trim($_GET['searchname']) . '%' : null;
                                    $searchEmail = !empty($_GET['searchemail']) ? '%' . trim($_GET['searchemail']) . '%' : null;
                                    $searchPhone = !empty($_GET['searchphone']) ? '%' . trim($_GET['searchphone']) . '%' : null;
                                    $searchRole = !empty($_GET['searchrole']) ? trim($_GET['searchrole']) : null;

                                    try {
                                        // Base query
                                        $query = "SELECT users.*, user_roles.role_name 
                                                  FROM users 
                                                  LEFT JOIN user_roles ON users.role = user_roles.id 
                                                  WHERE 1=1";
                                        $countQuery = "SELECT COUNT(*) FROM users WHERE 1=1";
                                        $params = [];
                                        $countParams = [];

                                        // Add search conditions
                                        if ($searchName !== null) {
                                            $query .= " AND full_name LIKE :searchname";
                                            $countQuery .= " AND full_name LIKE :searchname";
                                            $params[':searchname'] = $searchName;
                                            $countParams[':searchname'] = $searchName;
                                        }

                                        if ($searchEmail !== null) {
                                            $query .= " AND email LIKE :searchemail";
                                            $countQuery .= " AND email LIKE :searchemail";
                                            $params[':searchemail'] = $searchEmail;
                                            $countParams[':searchemail'] = $searchEmail;
                                        }

                                        if ($searchPhone !== null) {
                                            $query .= " AND phone LIKE :searchphone";
                                            $countQuery .= " AND phone LIKE :searchphone";
                                            $params[':searchphone'] = $searchPhone;
                                            $countParams[':searchphone'] = $searchPhone;
                                        }

                                        if ($searchRole !== null && $searchRole !== '') {
                                            $query .= " AND users.role = :role";
                                            $countQuery .= " AND users.role = :role";
                                            $params[':role'] = $searchRole;
                                            $countParams[':role'] = $searchRole;
                                        }

                                        // Get total records count
                                        $stmtCount = $pdo->prepare($countQuery);
                                        foreach ($countParams as $key => $val) {
                                            $stmtCount->bindValue($key, $val);
                                        }
                                        $stmtCount->execute();
                                        $totalRecords = $stmtCount->fetchColumn();
                                        $totalPages = ceil($totalRecords / $recordsPerPage);

                                        // Add sorting and pagination
                                        $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

                                        // Fetch records with pagination
                                        $stmt = $pdo->prepare($query);

                                        // Bind search parameters
                                        foreach ($params as $key => $val) {
                                            $stmt->bindValue($key, $val);
                                        }

                                        // Bind pagination parameters
                                        $stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
                                        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

                                        $stmt->execute();

                                        $counter = $offset + 1;

                                        if ($stmt->rowCount() > 0) {
                                            while ($row = $stmt->fetch()) {
                                                // Determine status & styling
                                                $isActive = strtolower(trim($row['status'])) === 'active';
                                                $blockText = $isActive ? 'Block' : 'Unblock';
                                                $blockIcon = $isActive ? '🚫' : '✅';
                                                $blockColor = $isActive ? 'text-warning' : 'text-success';
                                                $statusBadge = $isActive ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Blocked</span>';

                                                // Format created date
                                                $createdAt = date('M d, Y', strtotime($row['created_at']));

                                                echo "<tr id='row-{$row['id']}'>
                                                    <td class='text-center text-muted'>{$counter}</td>
                                                    <td class='text-center'><strong>{$row['full_name']}</strong></td>
                                                    <td class='text-center'>{$row['email']}</td>
                                                    <td class='text-center'>{$row['phone']}</td>
                                                    <td class='text-center'>" . htmlspecialchars($row['role_name'] ?? '') . "</td>
                                                    <td class='text-center'>" . htmlspecialchars(decrypt_text($row['password'])) . "</td>
                                                    <td class='text-center'>{$statusBadge}</td>
                                                    <td class='text-center'>
                                                        <div class='dropdown'>
                                                            <a style='cursor:pointer;' class=' dropdown-toggle' type='button' id='dropdownMenu{$row['id']}' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                                •••
                                                            </a>
                                                            <div class='dropdown-menu' aria-labelledby='dropdownMenu{$row['id']}'>
                                                                <a class='dropdown-item text-primary' href='#' onclick='editUser({$row['id']})'>✏ Edit</a>
                                                                <a class='dropdown-item text-danger' href='#' onclick='deleteUser({$row['id']})'>🗑 Delete</a>
                                                                <a class='dropdown-item {$blockColor}' href='#' onclick='blockUser({$row['id']}, \"{$row['status']}\")'>
                                                                    {$blockIcon} {$blockText}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>";

                                                $counter++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='8' class='text-center'>No users found</td></tr>";
                                        }
                                    } catch (PDOException $e) {
                                        echo "<tr><td colspan='8' class='text-center text-danger'>Database Error: " . $e->getMessage() . "</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if ($totalPages > 1): ?>
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Users</h5>
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
    </div>
    <?php include '../Includes/footer.php'; ?>
    <script>
        function editUser(id) {
            window.location.href = "add.php?id=" + id;
        }

        function deleteUser(id) {
            swal({
                title: "Are you sure?",
                text: "This user will be permanently deleted!",
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
                                swal("Deleted!", "The user has been removed.", "success");
                                $("#row-" + id).remove();
                            } else {
                                swal("Error!", "Unable to delete the user.", "error");
                            }
                        },
                        error: function() {
                            swal("Error!", "Something went wrong!", "error");
                        }
                    });
                }
            });
        }

        function blockUser(id, currentStatus) {
            let newStatus = (currentStatus.toLowerCase() === "active") ? "Blocked" : "Active";

            swal({
                title: "Change Status?",
                text: "You are about to mark this user as " + newStatus,
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
                                swal("Updated!", "User status changed to " + newStatus, "success")
                                    .then(() => location.reload());
                            } else {
                                swal("Error!", "Unable to update user status.", "error");
                            }
                        },
                        error: function() {
                            swal("Error!", "Something went wrong!", "error");
                        }
                    });
                }
            });
        }

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
            });

            // Get the message from the URL
            const urlParams = new URLSearchParams(window.location.search);
            const message = urlParams.get('message');

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