<?php
require '../Includes/header.php';
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Center Management');
$canadd = canUsercan_add($pdo, 'Center Management'); // <-- use new function
$candelete = canUsercan_delete($pdo, 'Center Management'); // <-- use new function
$canedit = canUsercan_edit($pdo, 'Center Management');

$totalPages = 1;
$totalcentres = $activecentres = $blockedcentres = 0;

try {
    $totalcentres = $pdo->query("SELECT COUNT(*) FROM centres")->fetchColumn();
    $activecentres = $pdo->query("SELECT COUNT(*) FROM centres WHERE LOWER(TRIM(status)) = 'active'")->fetchColumn();
    $blockedcentres = $pdo->query("SELECT COUNT(*) FROM centres WHERE LOWER(TRIM(status)) = 'blocked'")->fetchColumn();

    $regionals = $pdo->query("SELECT id, regional_name FROM regionals ORDER BY regional_name")->fetchAll(PDO::FETCH_ASSOC);
    $countries = $pdo->query("SELECT id, country_name FROM countries ORDER BY country_name")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}
?>
<div class="app-main__outer">
    <div class="app-main__inner">

        <!-- Header -->
        <div class="app-page-title app-page-title-simple">
            <div class="page-title-wrapper d-flex justify-content-between">
                <div class="page-title-heading">
                    <div class="page-title-head center-elem mb-3">
                        <span class="d-inline-block"><i class="lnr-apartment opacity-6"></i></span>
                        <span class="d-inline-block">centre Management</span>
                    </div>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">centre Management</a></li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Metrics -->
        <div class="row">
            <?php
            $metrics = [
                ['Total centres', $totalcentres, 'info'],
                ['Active centres', $activecentres, 'success'],
                ['Blocked centres', $blockedcentres, 'danger']
            ];
            foreach ($metrics as [$title, $count, $color]):
                ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3 widget-content">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                                <div class="widget-heading"><?= $title ?></div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers text-<?= $color ?>"><?= $count ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Search -->
        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Search</h5>
                        <form method="GET">
                            <div class="form-row">
                                <div class="col-md-3 mb-3">
                                    <label>centre Name</label>
                                    <input type="text" name="searchname" class="form-control"
                                        value="<?= htmlspecialchars($_GET['searchname'] ?? '') ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Region</label>
                                    <select name="regional_id" class="form-control select2">
                                        <option value="">All regions</option>
                                        <?php foreach ($regionals as $state): ?>
                                            <option value="<?= $state['id'] ?>" <?= ($_GET['regional_id'] ?? '') == $state['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($state['regional_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Country</label>
                                    <select name="country_id" class="form-control select2">
                                        <option value="">All Countries</option>
                                        <?php foreach ($countries as $country): ?>
                                            <option value="<?= $country['id'] ?>" <?= ($_GET['country_id'] ?? '') == $country['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($country['country_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>"
                                    class="btn btn-outline-dark mx-3">Reset</a>
                                <button class="btn btn-success" type="submit">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- centre Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>centres</span>
                        <div>
                            <a href="blocked.php" class="btn btn-danger mr-2">🚫 Blocked List</a>
                            <button class="btn btn-info mr-2" data-toggle="modal" data-target="#importModal">📂
                                Import</button>
                           
                            <?php if ($canadd): ?>
                                 <a href="add.php" class="btn btn-success"><i class="fa fa-plus"></i> Add centre</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th>Si.No</th>
                                    <th>Name</th>
                                    <th>Region</th>
                                    <th>Country</th>
                                    <th>Address</th>
                                    <th>City</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $page = max(1, intval($_GET['page'] ?? 1));
                                $limit = 10;
                                $offset = ($page - 1) * $limit;

                                $searchParams = [
                                    'searchname' => trim($_GET['searchname'] ?? ''),
                                    'regional_id' => $_GET['regional_id'] ?? null,
                                    'country_id' => $_GET['country_id'] ?? null
                                ];

                                $where = [];
                                $params = [];

                                if ($searchParams['searchname']) {
                                    $where[] = "centres.centre_name LIKE :searchname";
                                    $params['searchname'] = '%' . $searchParams['searchname'] . '%';
                                }
                                if ($searchParams['regional_id']) {
                                    $where[] = "centres.regional_id = :regional_id";
                                    $params['regional_id'] = $searchParams['regional_id'];
                                }
                                if ($searchParams['country_id']) {
                                    $where[] = "regionals.country_id = :country_id";
                                    $params['country_id'] = $searchParams['country_id'];
                                }

                                $whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

                                $countSQL = "SELECT COUNT(*) FROM centres
                                         LEFT JOIN regionals ON centres.regional_id = regionals.id
                                         LEFT JOIN countries ON regionals.country_id = countries.id
                                         $whereSQL";
                                $stmt = $pdo->prepare($countSQL);
                                $stmt->execute($params);
                                $totalRows = $stmt->fetchColumn();
                                $totalPages = ceil($totalRows / $limit);

                                $dataSQL = "SELECT centres.*, regionals.regional_name, countries.country_name 
                                        FROM centres 
                                        LEFT JOIN regionals ON centres.regional_id = regionals.id
                                        LEFT JOIN countries ON regionals.country_id = countries.id
                                        $whereSQL
                                        ORDER BY centres.id ASC 
                                        LIMIT :limit OFFSET :offset";
                                $stmt = $pdo->prepare($dataSQL);
                                foreach ($params as $key => $val) {
                                    $stmt->bindValue(':' . $key, $val);
                                }
                                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                                $stmt->execute();

                                $i = $offset + 1;
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $status = strtolower(trim($row['status']));
                                    $badge = $status === 'active' ? 'success' : 'danger';
                                    echo "<tr>
    <td>{$i}</td>
    <td>{$row['centre_name']}</td>
    <td>{$row['regional_name']}</td>
    <td>{$row['country_name']}</td>
    <td>{$row['address']}</td>
    <td>{$row['city']}</td>
    <td>{$row['phone']}</td>
    <td>{$row['email']}</td>
    <td><span class='badge badge-{$badge}'>" . ucfirst($row['status']) . "</span></td>
    <td>
        <div class='dropdown'>
            <a style='cursor: pointer;' class='dropdown-toggle' data-toggle='dropdown'>•••</a>
            <div class='dropdown-menu'>";
if ($canedit) {
    echo "<a class='dropdown-item text-primary' href='#' onclick='editSubcentre({$row['id']})'>✏ Edit</a>";
}
if ($candelete) {
    echo "<a class='dropdown-item text-danger' href='#' onclick='deleteSubcentre({$row['id']})'>🗑 Delete</a>";
}
echo "      <a class='dropdown-item' href='#' onclick='blockSubcentre({$row['id']}, \"{$row['status']}\")'>" .
    ($status === 'active' ? '🚫 Block' : '✅ Unblock') .
    "</a>
            </div>
        </div>
    </td>
</tr>";
                                    $i++;
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
                            $queryStr = http_build_query($queryParams);

                            for ($i = 1; $i <= $totalPages; $i++) {
                                $active = $i == $page ? 'active' : '';
                                echo "<li class='page-item $active'><a class='page-link' href='?page=$i&$queryStr'>$i</a></li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Modal -->
        <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="importForm" class="modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Import centres</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="file" name="csvFile" class="form-control" accept=".csv" required>
                        <small>CSV format: centre_name, centre_id, address, city, phone, email</small>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>

        <style>
            .select2 {
                width: 100% !important;
            }
        </style>


<?php include '../Includes/footer.php'; ?>
        <script>
            $(document).ready(function () {
                // Initialize select2
                $('.select2').select2();

                // ✅ Show success message if present in URL
                const urlParams = new URLSearchParams(window.location.search);
                const message = urlParams.get('message');
                if (message) {
                    swal({
                        title: "Success",
                        text: decodeURIComponent(message.replace(/\+/g, ' ')),
                        icon: "success",
                        button: "OK"
                    }).then(() => {
                        // Remove message from URL after showing it
                        const url = new URL(window.location.href);
                        url.searchParams.delete('message');
                        window.history.replaceState({}, document.title, url.toString());
                    });
                }

                // Handle import form submission
                $("#importForm").submit(function (e) {
                    e.preventDefault();
                    var formData = new FormData(this);

                    $.ajax({
                        url: "import.php",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            response = response.trim();
                            if (response.startsWith("success:")) {
                                swal("Imported!", response.replace("success: ", ""), "success")
                                    .then(() => location.reload());
                            } else {
                                swal("Error!", response.replace("error: ", ""), "error");
                            }
                        },
                        error: function () {
                            swal("Error!", "Something went wrong!", "error");
                        }
                    });
                });
            });

            function editSubcentre(id) {
                window.location.href = "edit.php?id=" + id;
            }

            function deleteSubcentre(id) {
                swal({
                    title: "Are you sure?",
                    text: "This centre will be permanently deleted!",
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
                                response = response.trim();
                                if (response === "success") {
                                    swal("Deleted!", "The centre has been removed.", "success")
                                        .then(() => {
                                            $("#row-" + id).fadeOut(300, function () {
                                                $(this).remove();
                                                updateSerialNumbers();
                                            });
                                        });
                                } else if (response.startsWith("error:")) {
                                    swal("Error!", response.substring(6), "error");
                                } else {
                                    swal("Error!", "Unknown response from server", "error");
                                }
                            },
                            error: function (xhr, status, error) {
                                swal("Error!", "AJAX Error: " + error, "error");
                            }
                        });
                    }
                });
            }

            function blockSubcentre(id, currentStatus) {
                let newStatus = (currentStatus.toLowerCase() === "active") ? "Blocked" : "Active";
                let actionText = (newStatus === "Blocked") ? "block" : "unblock";

                swal({
                    title: "Confirm",
                    text: `Are you sure you want to ${actionText} this centre?`,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                }).then((confirm) => {
                    if (confirm) {
                        $.ajax({
                            url: "block.php",
                            type: "POST",
                            data: { id: id, status: newStatus },
                            success: function (response) {
                                response = response.trim();
                                if (response === "success") {
                                    swal("Updated!", `Regional has been ${actionText}ed.`, "success")
                                        .then(() => location.reload());
                                } else if (response.startsWith("error:")) {
                                    swal("Error!", response.substring(6), "error");
                                } else {
                                    swal("Error!", "Unknown response from server", "error");
                                }
                            },
                            error: function (xhr, status, error) {
                                swal("Error!", "AJAX Error: " + error, "error");
                            }
                        });
                    }
                });
            }

            function updateSerialNumbers() {
                $("tbody#tabledata-new tr").each(function (index) {
                    $(this).find("td:first").text(index + 1);
                });
            }
        </script>


