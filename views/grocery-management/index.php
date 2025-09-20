<?php
require '../Includes/header.php';
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Grocery Management');
$canadd = canUsercan_add($pdo, 'Grocery Management');
$canedit = canUsercan_edit($pdo, 'Grocery Management');
$candelete = canUsercan_delete($pdo, 'Grocery Management');

// --- Filter Logic ---
$where = [];
$params = [];

if (!empty($_GET['item_name'])) {
    $where[] = "Item_name LIKE :item_name";
    $params['item_name'] = '%' . $_GET['item_name'] . '%';
}
if (!empty($_GET['item_quantity'])) {
    $where[] = "Item_Quantity = :item_quantity";
    $params['item_quantity'] = $_GET['item_quantity'];
}
if (isset($_GET['item_status']) && $_GET['item_status'] !== '') {
    $where[] = "Item_status = :item_status";
    $params['item_status'] = $_GET['item_status'];
}
if (!empty($_GET['date_from'])) {
    $where[] = "Date >= :date_from";
    $params['date_from'] = $_GET['date_from'];
}
if (!empty($_GET['date_to'])) {
    $where[] = "Date <= :date_to";
    $params['date_to'] = $_GET['date_to'];
}
if (!empty($_GET['added_by'])) {
    $where[] = "Added_by = :added_by";
    $params['added_by'] = $_GET['added_by'];
}

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

// --- Pagination ---
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

// --- Count for pagination ---
$countSQL = "SELECT COUNT(*) FROM grocerytb $whereSQL";
$stmt = $pdo->prepare($countSQL);
$stmt->execute($params);
$totalRows = $stmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// --- Fetch Data ---
$dataSQL = "SELECT * FROM grocerytb $whereSQL ORDER BY Date DESC, Id DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($dataSQL);
foreach ($params as $key => $val) {
    $stmt->bindValue(':' . $key, $val);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$groceryItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- For Added By dropdown ---
$users = $pdo->query("SELECT DISTINCT Added_by FROM grocerytb ORDER BY Added_by")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="app-main__outer">
    <div class="app-main__inner">

        <!-- Page Title & Breadcrumb -->
        <div class="app-page-title app-page-title-simple">
            <div class="page-title-wrapper d-flex justify-content-between">
                <div class="page-title-heading">
                    <div class="page-title-head center-elem mb-3">
                        <span class="d-inline-block"><i class="fas fa-shopping-basket opacity-6"></i></span>
                        <span class="d-inline-block">Grocery Management</span>
                    </div>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Grocery Management </a></li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Filter Grocery Items</h5>
                        <form method="GET">
                            <div class="form-row">
                                <div class="col-md-2 mb-3">
                                    <label>Item Name</label>
                                    <input type="text" name="item_name" class="form-control"
                                        value="<?= htmlspecialchars($_GET['item_name'] ?? '') ?>">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label>Quantity</label>
                                    <input type="text" name="item_quantity" class="form-control"
                                        value="<?= htmlspecialchars($_GET['item_quantity'] ?? '') ?>">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label>Status</label>
                                    <select name="item_status" class="form-control">
                                        <option value="">All</option>
                                        <option value="0" <?= ($_GET['item_status'] ?? '') === "0" ? "selected" : "" ?>>Pending</option>
                                        <option value="1" <?= ($_GET['item_status'] ?? '') === "1" ? "selected" : "" ?>>Bought</option>
                                        <option value="2" <?= ($_GET['item_status'] ?? '') === "2" ? "selected" : "" ?>>Not Available</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" class="form-control"
                                        value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label>Added By</label>
                                    <select name="added_by" class="form-control">
                                        <option value="">All</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?= htmlspecialchars($user['Added_by']) ?>"
                                                <?= ($_GET['added_by'] ?? '') === $user['Added_by'] ? "selected" : "" ?>>
                                                <?= htmlspecialchars($user['Added_by']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>" class="btn btn-outline-dark mx-3">Reset</a>
                                <button class="btn btn-success" type="submit">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grocery Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Grocery Items</span>
                        <div>
                            <?php if ($canadd): ?>
                                <a href="add.php" class="btn btn-success"><i class="fa fa-plus"></i> Add Item</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th>Si.No</th>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Added By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = $offset + 1;
                                foreach ($groceryItems as $row):
                                    $statusText = $row['Item_status'] == 0 ? 'Pending' : ($row['Item_status'] == 1 ? 'Bought' : 'Not Available');
                                    $badge = $row['Item_status'] == 1 ? 'success' : ($row['Item_status'] == 2 ? 'danger' : 'warning');
                                ?>
                                    <tr>
                                        <td><?= $i ?></td>
                                        <td><?= htmlspecialchars($row['Item_name']) ?></td>
                                        <td><?= htmlspecialchars($row['Item_Quantity']) ?></td>
                                        <td><span class="badge badge-<?= $badge ?>"><?= $statusText ?></span></td>
                                        <td><?= htmlspecialchars($row['Date']) ?></td>
                                        <td><?= htmlspecialchars($row['Added_by']) ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <a style="cursor: pointer;" class="dropdown-toggle" data-toggle="dropdown">•••</a>
                                                <div class="dropdown-menu">
                                                    <?php if ($canedit): ?>
                                                        <a class="dropdown-item text-primary" href="add.php?id=<?= $row['Id'] ?>">✏ Edit</a>
                                                    <?php endif; ?>
                                                    <?php if ($candelete): ?>
                                                        <a class="dropdown-item text-danger" href="delete.php?id=<?= $row['Id'] ?>" onclick="return confirm('Are you sure you want to delete this item?');">🗑 Delete</a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php $i++; endforeach; ?>
                                <?php if (empty($groceryItems)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No grocery items found.</td>
                                    </tr>
                                <?php endif; ?>
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

                            for ($p = 1; $p <= $totalPages; $p++) {
                                $active = $p == $page ? 'active' : '';
                                echo "<li class='page-item $active'><a class='page-link' href='?page=$p&$queryStr'>$p</a></li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        

    </div>
</div>

<?php include '../Includes/footer.php'; ?>