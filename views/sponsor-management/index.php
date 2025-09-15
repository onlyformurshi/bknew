<?php

include '../Includes/header.php';
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Sponsor Management');
$canadd = canUsercan_add($pdo, 'Sponsor Management');
$candelete = canUsercan_delete($pdo, 'Sponsor Management');
$canedit = canUsercan_edit($pdo, 'Sponsor Management');
$showPrice = canUserViewPrice($pdo, 'Sponsor Management');

// Fetch filter options
$users = $pdo->query("SELECT id, full_name FROM users WHERE status='Active' ORDER BY full_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$programs = $pdo->query("SELECT id, title FROM programs ORDER BY title ASC")->fetchAll(PDO::FETCH_ASSOC);

// Get filters from GET
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : '';
$program_id = isset($_GET['program_id']) ? intval($_GET['program_id']) : '';
$category = trim($_GET['category'] ?? '');
$payment_status = trim($_GET['payment_status'] ?? '');
$payment_type = trim($_GET['payment_type'] ?? '');

// Build WHERE clause
$where = [];
$params = [];
if ($user_id) {
    $where[] = "s.user_id = :user_id";
    $params[':user_id'] = $user_id;
}
if ($program_id) {
    $where[] = "s.program_id = :program_id";
    $params[':program_id'] = $program_id;
}
if ($category) {
    $where[] = "s.category LIKE :category";
    $params[':category'] = "%$category%";
}
if ($payment_status) {
    $where[] = "s.payment_status = :payment_status";
    $params[':payment_status'] = $payment_status;
}
if ($payment_type) {
    $where[] = "s.payment_type = :payment_type";
    $params[':payment_type'] = $payment_type;
}
$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Pagination
$recordsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $recordsPerPage;

// Count total
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM sponsorships s $whereSql");
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $recordsPerPage);

// Fetch sponsors
$sql = "SELECT s.*, u.full_name AS sponsor_name
        FROM sponsorships s
        LEFT JOIN users u ON s.user_id = u.id
        $whereSql
        ORDER BY s.created_at DESC
        LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$sponsors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch total amount (sum of amount)
$totalAmountStmt = $pdo->prepare("SELECT SUM(s.amount) FROM sponsorships s $whereSql");
$totalAmountStmt->execute($params);
$totalAmount = $totalAmountStmt->fetchColumn();
$totalAmount = $totalAmount ?: 0;

// Fetch total sponsors (unique sponsor count)
$totalSponsorsStmt = $pdo->prepare("SELECT COUNT(DISTINCT s.user_id) FROM sponsorships s $whereSql");
$totalSponsorsStmt->execute($params);
$totalSponsors = $totalSponsorsStmt->fetchColumn();
$totalSponsors = $totalSponsors ?: 0;

// Fetch total sponsored programs (unique program count)
$totalProgramsStmt = $pdo->prepare("SELECT COUNT(DISTINCT s.program_id) FROM sponsorships s $whereSql");
$totalProgramsStmt->execute($params);
$totalPrograms = $totalProgramsStmt->fetchColumn();
$totalPrograms = $totalPrograms ?: 0;
?>

<div class="app-main__outer">
    <div class="app-main__inner">
        <div class="row">
            <div class="col-md-6 col-xl-4">
                <div class="card mb-3 widget-content">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                                <div class="widget-heading">Total Amount</div>
                                <div class="widget-subheading">Sum of all sponsor contributions</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers text-info"><?= number_format($totalAmount, 2) ?></div>
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
                                <div class="widget-heading">Total Sponsors</div>
                                <div class="widget-subheading">Unique sponsors</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers text-success"><?= $totalSponsors ?></div>
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
                                <div class="widget-heading">Total Sponsored Programs</div>
                                <div class="widget-subheading">Unique programs</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers text-danger"><?= $totalPrograms ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="main-card mb-3 card">
            <div class="card-body mb-5">
                <div class="card-header d-flex justify-content-between">
                    <span>Search</span>
                </div>
                <form method="GET" class="mb-4">
                    <div class="form-row">
                        <div class="col-md-2 mb-2">
                            <label for="user_id">Sponsor Name</label>
                            <select name="user_id" id="user_id" class="form-control select2" onchange="this.form.submit()">
                                <option value="">All Sponsors</option>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= $u['id'] ?>" <?= $user_id == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="program_id">Program</label>
                            <select name="program_id" id="program_id" class="form-control select2" onchange="this.form.submit()">
                                <option value="">All Programs</option>
                                <?php foreach ($programs as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $program_id == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="category">Category</label>
                            <input type="text" name="category" id="category" class="form-control" placeholder="Category" value="<?= htmlspecialchars($category) ?>">
                        </div>
                        <?php if ($showPrice): ?>
                            <div class="col-md-2 mb-2">
                                <label for="payment_status">Payment Status</label>
                                <select name="payment_status" id="payment_status" class="form-control">
                                    <option value="">All</option>
                                    <option value="pending" <?= $payment_status == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="paid" <?= $payment_status == 'paid' ? 'selected' : '' ?>>Paid</option>
                                    <option value="failed" <?= $payment_status == 'failed' ? 'selected' : '' ?>>Failed</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="payment_type">Payment Type</label>
                                <select name="payment_type" id="payment_type" class="form-control">
                                    <option value="">All</option>
                                    <option value="cash" <?= $payment_type == 'cash' ? 'selected' : '' ?>>Cash</option>
                                    <option value="online" <?= $payment_type == 'online' ? 'selected' : '' ?>>Online</option>
                                    <option value="cheque" <?= $payment_type == 'cheque' ? 'selected' : '' ?>>Cheque</option>
                                    <option value="upi" <?= $payment_type == 'upi' ? 'selected' : '' ?>>UPI</option>
                                </select>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-2 mb-2 d-flex align-items-end">
                            <button class="btn btn-success mr-2" type="submit"><i class="fa fa-search"></i> Search</button>
                            <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>" class="btn btn-outline-dark"><i class="fa fa-window-restore"></i> Reset</a>
                        </div>
                    </div>
                </form>
            </div>


            <div class="card-body">
                <div class="card-header d-flex justify-content-between">
                    <span>Sponsor List</span>
                    <?php if ($canadd): ?>
                        <a href="add.php" class="btn btn-success"><i class="fa fa-plus"></i> Add Sponsor</a>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sponsor Name</th>
                                <th>Program</th>
                                <th>Category</th>
                                <th>Item</th>
                                <?php if ($showPrice): ?>
                                    <th>Amount</th>
                                    <th>Payment Status</th>
                                    <th>Payment Type</th>
                                <?php endif; ?>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = $offset + 1;
                            foreach ($sponsors as $row): ?>
                                <tr>
                                    <td><?= $counter++ ?></td>
                                    <td><?= htmlspecialchars($row['sponsor_name']) ?></td>
                                    <td>
                                        <?php
                                        $programTitle = '';
                                        foreach ($programs as $p) {
                                            if ($p['id'] == $row['program_id']) {
                                                $programTitle = $p['title'];
                                                break;
                                            }
                                        }
                                        echo htmlspecialchars($programTitle);
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['category']) ?></td>
                                    <td>
                                        <?php
                                        $itemName = '';
                                        if ($row['category'] && $row['item_id']) {
                                            // Map category to table and column
                                            $tableMap = [
                                                'facebook_advertisements' => ['table' => 'facebook_advertisements', 'col' => 'name'],
                                                'billboard_advertisements' => ['table' => 'billboard_advertisements', 'col' => 'agency_name'],
                                                'instagram_advertisements' => ['table' => 'instagram_advertisements', 'col' => 'name'],
                                                'newspaper_advertisements' => ['table' => 'newspaper_advertisements', 'col' => 'name'],
                                                'radio_advertisements' => ['table' => 'radio_advertisements', 'col' => 'name'],
                                                'television_advertisements' => ['table' => 'television_advertisements', 'col' => 'name'],
                                                'pamphlet' => ['table' => 'program_pamphlets', 'col' => 'pamphlet_designer_name'],
                                            ];
                                            $cat = $row['category'];
                                            if (isset($tableMap[$cat])) {
                                                $tbl = $tableMap[$cat]['table'];
                                                $col = $tableMap[$cat]['col'];
                                                $itemStmt = $pdo->prepare("SELECT $col FROM $tbl WHERE id = ?");
                                                $itemStmt->execute([$row['item_id']]);
                                                $itemName = $itemStmt->fetchColumn();
                                            }
                                        }
                                        echo htmlspecialchars($itemName ?: $row['item_id']);
                                        ?>
                                    </td>
                                    <?php if ($showPrice): ?>
                                        <td><?= htmlspecialchars($row['amount']) ?></td>
                                        <td><?= htmlspecialchars($row['payment_status']) ?></td>
                                        <td><?= htmlspecialchars($row['payment_type']) ?></td>
                                    <?php endif; ?>
                                    <td><?= date('d-M-Y', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <a style="cursor:pointer;" class="dropdown-toggle" type="button" id="dropdownMenu<?= $row['id'] ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                •••
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenu<?= $row['id'] ?>">
                                                <?php if ($canedit): ?>
                                                    <a class="dropdown-item text-primary" href="#" onclick="window.location.href='edit.php?id=<?= $row['id'] ?>'">✏ Edit</a>
                                                <?php endif; ?>
                                                <?php if ($candelete): ?>
                                                    <a class="dropdown-item text-danger" href="#" onclick="deleteSponsor(<?= $row['id'] ?>)">🗑 Delete</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($sponsors)): ?>
                                <tr>
                                    <td colspan="<?= $showPrice ? '10' : '7' ?>" class="text-center text-danger">No sponsors found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <ul class="pagination pagination-sm d-flex justify-content-end">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a href="?page=<?= $i ?>" class="page-link"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?= isset($_GET['message']) ? htmlspecialchars($_GET['message']) : "Sponsor contribution saved successfully!" ?>',
            confirmButtonColor: '#d33'
        }).then(function() {
            // Remove status and message from URL
            const url = new URL(window.location.href);
            url.searchParams.delete('status');
            url.searchParams.delete('message');
            window.location.replace(url.pathname + url.search);
        });
    </script>
<?php endif; ?>
<script>
    function deleteSponsor(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This sponsor will be permanently deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "delete.php",
                    type: "POST",
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response.trim() === "success") {
                            Swal.fire({
                                icon: "success",
                                title: "Deleted!",
                                text: "The sponsor has been removed.",
                                confirmButtonColor: "#d33"
                            }).then(() => location.reload());
                        } else {
                            Swal.fire("Error!", "Unable to delete the sponsor.", "error");
                        }
                    },
                    error: function() {
                        Swal.fire("Error!", "Something went wrong!", "error");
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
<style>
    .select2 {
        width: 100% !important;
    }
</style>

<?php include '../Includes/footer.php'; ?>