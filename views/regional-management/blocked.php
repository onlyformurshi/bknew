<?php include '../Includes/header.php'; ?>

<?php
require '../../config/config.php'; // Ensure database connection is established
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Regional Management');
try {
    // Fetch total blocked regionals count
    $stmtBlocked = $pdo->query("SELECT COUNT(*) FROM regionals WHERE LOWER(TRIM(status)) = 'blocked'");
    $blockedregionals = $stmtBlocked->fetchColumn();
} catch (PDOException $e) {
    $blockedregionals = 0;
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
                            <span class="d-inline-block">Blocked Regionals </span>
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
                                    <div class="widget-heading">Blocked Regionals</div>
                                    <div class="widget-subheading"></div>
                                </div>
                                <div class="widget-content-right">
                                    <div class="widget-numbers text-danger"><?= $blockedregionals ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header d-flex justify-content-center">Blocked Regionals</div>
                        <div class="table-responsive">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">Si.No</th>
                                        <th class="text-center">Regional Name</th>
                                        <th class="text-center">Country</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        $stmt = $pdo->query("SELECT regionals.*, 
                                                             countries.country_name
                                                           FROM regionals 
                                                           LEFT JOIN countries ON regionals.country_id = countries.id
                                                           WHERE LOWER(TRIM(regionals.status)) = 'blocked' 
                                                           ORDER BY regionals.id ASC");
                                        $counter = 1;
                                        $hasData = false;

                                        while ($row = $stmt->fetch()) {
                                            $hasData = true;
                                            echo "<tr id='row-{$row['id']}'>
                                                <td class='text-center text-muted'>{$counter}</td>
                                                <td class='text-center'><strong>" . htmlspecialchars($row['regional_name']) . "</strong></td>
                                                <td class='text-center'>" . htmlspecialchars($row['country_name']) . "</td>
                                                <td class='text-center'><span class='badge badge-danger'>Blocked</span></td>
                                                <td class='text-center'>
                                                    <div class='dropdown'>
                                                        <a style='cursor:pointer;' class='dropdown-toggle' type='button' id='dropdownMenu{$row['id']}' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                            •••
                                                        </a>
                                                        <div class='dropdown-menu' aria-labelledby='dropdownMenu{$row['id']}'>
                                                            <a class='dropdown-item text-success' href='#' onclick='unblockState({$row['id']})'>✅ Unblock</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>";
                                            $counter++;
                                        }

                                        if (!$hasData) {
                                            echo "<tr><td colspan='5' class='text-center text-muted'>No Blocked Regional Available</td></tr>";
                                        }
                                    } catch (PDOException $e) {
                                        echo "<tr><td colspan='5' class='text-center text-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
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
        function unblockState(id) {
            swal({
                title: "Unblock Regional?",
                text: "You are about to mark this Regional as Active",
                icon: "warning",
                buttons: true,
                dangerMode: true
            }).then((willUnblock) => {
                if (willUnblock) {
                    $.ajax({
                        url: "block.php",
                        type: "POST",
                        data: { id: id, status: "Active" },
                        success: function(response) {
                            response = response.trim();
                            if (response === "success") {
                                swal("Updated!", "Regional status changed to Active", "success")
                                    .then(() => location.reload());
                            } else if (response.startsWith("error:")) {
                                swal("Error!", response.substring(6), "error");
                            } else {
                                swal("Error!", "Unable to update Regional status.", "error");
                            }
                        },
                        error: function(xhr, status, error) {
                            swal("Error!", "AJAX Error: " + error, "error");
                        }
                    });
                }
            });
        }
    </script>
</div>