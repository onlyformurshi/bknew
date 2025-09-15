<?php include '../Includes/header.php'; ?>

<?php
require '../../config/config.php'; // Ensure database connection is established
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Country Management');
try {
    // Fetch total blocked countries count
    $stmtBlocked = $pdo->query("SELECT COUNT(*) FROM countries WHERE LOWER(TRIM(status)) = 'blocked'");
    $blockedCountries = $stmtBlocked->fetchColumn();
} catch (PDOException $e) {
    $blockedCountries = 0;
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
                            <span class="d-inline-block">Blocked Country Management</span>
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

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header d-flex justify-content-center">Blocked Country List</div>
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
                                <tbody>
                                    <?php
                                    try {
                                        $stmt = $pdo->query("SELECT * FROM countries WHERE LOWER(TRIM(status)) = 'blocked' ORDER BY id ASC");
                                        $counter = 1;
                                        $hasData = false;

                                        while ($row = $stmt->fetch()) {
                                            $hasData = true;
                                            echo "<tr id='row-{$row['id']}'>
                <td class='text-center text-muted'>{$counter}</td>
                <td class='text-center'><strong>{$row['country_name']}</strong></td>
                <td class='text-center'>{$row['country_code']}</td>
                <td class='text-center'>{$row['currency']}</td>
                <td class='text-center'>{$row['language']}</td>
                <td class='text-center'><span class='badge badge-danger'>Blocked</span></td>
                <td class='text-center'>
                    <div class='dropdown'>
                        <a style='cursor:pointer;' class='dropdown-toggle' type='button' id='dropdownMenu{$row['id']}' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                            •••
                        </a>
                        <div class='dropdown-menu' aria-labelledby='dropdownMenu{$row['id']}'>
                            <a class='dropdown-item text-success' href='#' onclick='unblockCountry({$row['id']})'>✅ Unblock</a>
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
        function editCountry(id) {
            window.location.href = "add.php?id=" + id;
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
                        data: { id: id },
                        success: function (response) {
                            if (response.trim() === "success") {
                                swal("Deleted!", "The country has been removed.", "success");
                                $("#row-" + id).remove();
                            } else {
                                swal("Error!", "Unable to delete the country.", "error");
                            }
                        },
                        error: function () {
                            swal("Error!", "Something went wrong!", "error");
                        }
                    });
                }
            });
        }

        function unblockCountry(id) {
            swal({
                title: "Unblock Country?",
                text: "You are about to mark this country as Active",
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
                                swal("Updated!", "Country status changed to Active", "success")
                                    .then(() => location.reload());
                            } else {
                                swal("Error!", "Unable to update country status.", "error");
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