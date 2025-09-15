<?php include '../Includes/header.php'; ?>

<?php
require '../../config/config.php'; // Ensure database connection is established
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Center Management');
try {
    // Fetch total blocked centres count
    $stmtBlocked = $pdo->query("SELECT COUNT(*) FROM centres WHERE LOWER(TRIM(status)) = 'blocked'");
    $blockedcentres = $stmtBlocked->fetchColumn();
} catch (PDOException $e) {
    $blockedcentres = 0;
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
                            <span class="d-inline-block">Blocked centre Management</span>
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
                                    <div class="widget-heading">Blocked centres</div>
                                    <div class="widget-subheading"></div>
                                </div>
                                <div class="widget-content-right">
                                    <div class="widget-numbers text-danger"><?= $blockedcentres ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header d-flex justify-content-center">Blocked centre List</div>
                        <div class="table-responsive">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">Si.No</th>
                                        <th class="text-center">centre Name</th>
                                        <th class="text-center">Region</th>
                                        <th class="text-center">Address</th>
                                        <th class="text-center">City</th>
                                        <th class="text-center">Phone</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        $stmt = $pdo->query("SELECT sc.*, b.regional_name
                                                             FROM centres sc
                                                             LEFT JOIN regionals b ON sc.regional_id = b.id
                                                             WHERE LOWER(TRIM(sc.status)) = 'blocked'
                                                             ORDER BY sc.id ASC");
                                        $counter = 1;
                                        $hasData = false;

                                        while ($row = $stmt->fetch()) {
                                            $hasData = true;
                                            echo "<tr id='row-{$row['id']}'>
                                                <td class='text-center text-muted'>{$counter}</td>
                                                <td class='text-center'><strong>" . htmlspecialchars($row['centre_name']) . "</strong></td>
                                                <td class='text-center'>" . htmlspecialchars($row['regional_name']) . "</td>
                                                <td class='text-center'>" . htmlspecialchars($row['address']) . "</td>
                                                <td class='text-center'>" . htmlspecialchars($row['city']) . "</td>
                                                <td class='text-center'>" . htmlspecialchars($row['phone']) . "</td>
                                                <td class='text-center'><span class='badge badge-danger'>Blocked</span></td>
                                                <td class='text-center'>
                                                    <div class='dropdown'>
                                                        <a style='cursor:pointer;' class='dropdown-toggle' type='button' id='dropdownMenu{$row['id']}' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                            •••
                                                        </a>
                                                        <div class='dropdown-menu' aria-labelledby='dropdownMenu{$row['id']}'>
                                                            <a class='dropdown-item text-success' href='#' onclick='unblockcentre({$row['id']})'>✅ Unblock</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>";
                                            $counter++;
                                        }

                                        if (!$hasData) {
                                            echo "<tr><td colspan='8' class='text-center text-muted'>No Blocked centres Available</td></tr>";
                                        }
                                    } catch (PDOException $e) {
                                        echo "<tr><td colspan='8' class='text-center text-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
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
    function unblockcentre(id) {
        swal({
            title: "Unblock centre?",
            text: "You are about to mark this centre as Active",
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
                            swal("Updated!", "centre status changed to Active", "success")
                                .then(() => location.reload());
                        } else if (response.startsWith("error:")) {
                            swal("Error!", response.substring(6), "error");
                        } else {
                            swal("Error!", "Unable to update centre status.", "error");
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

