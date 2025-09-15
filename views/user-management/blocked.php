<?php include '../Includes/header.php'; ?>

<?php
require '../../config/config.php'; // Ensure database connection is established

require_once '../../config/functions.php'; // Include functions file
checkModuleAccess($pdo, 'User Management'); // Check if the user has access to this
try {
    // Fetch total blocked users count
    $stmtBlocked = $pdo->query("SELECT COUNT(*) FROM users WHERE LOWER(TRIM(status)) = 'blocked'");
    $blockedusers = $stmtBlocked->fetchColumn();
} catch (PDOException $e) {
    $blockedusers = 0;
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
                            <span class="d-inline-block">Blocked users</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3 widget-content">
                        <div class="widget-content-outer">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left">
                                    <div class="widget-heading">Blocked user</div>
                                    <div class="widget-subheading"></div>
                                </div>
                                <div class="widget-content-right">
                                    <div class="widget-numbers text-danger"><?= $blockedusers ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header d-flex justify-content-center">Blocked user List</div>
                        <div class="table-responsive">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">Si.No</th>
                                        <th class="text-center">Full Name</th>
                                        <th class="text-center">Email</th>
                                        <th class="text-center">Phone</th>
                                        <th class="text-center">Role</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        $stmt = $pdo->query("SELECT * FROM users WHERE LOWER(TRIM(status)) = 'blocked' ORDER BY id ASC");
                                        $counter = 1;
                                        $hasData = false;

                                        while ($row = $stmt->fetch()) {
                                            $hasData = true;
                                            echo "<tr id='row-{$row['id']}'>
                <td class='text-center text-muted'>{$counter}</td>
                <td class='text-center'><strong>{$row['full_name']}</strong></td>
                <td class='text-center'>{$row['email']}</td>
                <td class='text-center'>{$row['phone']}</td>
                <td class='text-center'>{$row['role']}</td>
                <td class='text-center'><span class='badge badge-danger'>Blocked</span></td>
                <td class='text-center'>
                    <div class='dropdown'>
                        <a style='cursor:pointer;' class='dropdown-toggle' type='button' id='dropdownMenu{$row['id']}' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                            •••
                        </a>
                        <div class='dropdown-menu' aria-labelledby='dropdownMenu{$row['id']}'>
                            <a class='dropdown-item text-success' href='#' onclick='unblockuser({$row['id']})'>✅ Unblock</a>
                                                        <a class='dropdown-item text-danger' href='#' onclick='deleteuser({$row['id']})'>🗑 Delete</a>

                        </div>
                    </div>
                </td>
            </tr>";
                                            $counter++;
                                        }

                                        if (!$hasData) {
                                            echo "<tr><td colspan='7' class='text-center text-muted'>No Blocked List Available</td></tr>";
                                        }
                                    } catch (PDOException $e) {
                                        echo "<tr><td colspan='7' class='text-center text-danger'>Database Error: " . $e->getMessage() . "</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../Includes/footer.php'; ?>

    <script>
        function edituser(id) {
            window.location.href = "add.php?id=" + id;
        }

        function deleteuser(id) {
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
                        data: { id: id },
                        success: function (response) {
                            if (response.trim() === "success") {
                                swal("Deleted!", "The user has been removed.", "success");
                                $("#row-" + id).remove();
                            } else {
                                swal("Error!", "Unable to delete the user.", "error");
                            }
                        },
                        error: function () {
                            swal("Error!", "Something went wrong!", "error");
                        }
                    });
                }
            });
        }

        function unblockuser(id) {
            swal({
                title: "Unblock user?",
                text: "You are about to mark this user as Active",
                icon: "warning",
                buttons: true,
                dangerMode: true
            }).then((willUnblock) => {
                if (willUnblock) {
                    $.ajax({
                        url: "block.php",
                        type: "POST",
                        data: { id: id, status: "Active" },
                        success: function (response) {
                            if (response.trim() === "success") {
                                swal("Updated!", "user status changed to Active", "success")
                                    .then(() => location.reload());
                            } else {
                                swal("Error!", "Unable to update user status.", "error");
                            }
                        },
                        error: function () {
                            swal("Error!", "Something went wrong!", "error");
                        }
                    });
                }
            });
        }
    </script>
</div>