<?php
include '../Includes/header.php';
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Grocery Management');

// Permission check (optional)
$canadd = canUsercan_add($pdo, 'Grocery Management');
if (!$canadd) {
    header("Location: ../../unauthorized.php");
    exit;
}

// Initialize values
$item_name = $item_quantity = $item_status = $date = $added_by = "";
$id = "";

// Edit mode
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM grocerytb WHERE Id = :id");
    $stmt->execute(['id' => $id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($item) {
        $item_name = $item['Item_name'];
        $item_quantity = $item['Item_Quantity'];
        $item_status = $item['Item_status'];
        $date = $item['Date'];
        $added_by = $item['Added_by'];
    }
}
?>

<div class="app-main__outer">
    <div class="app-main__inner h-100">
        <div class="app-page-title app-page-title-simple">
            <div class="page-title-wrapper d-flex justify-content-between">
                <div class="page-title-heading"></div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="index.php">Grocery Management</a>
                        </li>
                        <li class="breadcrumb-item"><a href="add.php"><?= $id ? "Edit Grocery Item" : "Add Grocery Item" ?></a></li>
                    </ol>
                </nav>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title"><?= $id ? "Edit Grocery Item" : "Add Grocery Item" ?></h5>
                            <form class="needs-validation" novalidate action="save.php" method="post">
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="itemName">Item Name</label>
                                        <input type="text" class="form-control" id="itemName" name="item_name" required placeholder="Enter Item Name" value="<?= htmlspecialchars($item_name) ?>">
                                        <div class="invalid-feedback">Please enter item name.</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="itemQuantity">Quantity</label>
                                        <input type="number" class="form-control" id="itemQuantity" name="item_quantity" required min="1" placeholder="Enter Quantity" value="<?= htmlspecialchars($item_quantity) ?>">
                                        <div class="invalid-feedback">Please enter quantity.</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="itemStatus">Status</label>
                                        <select class="form-control" id="itemStatus" name="item_status" required>
                                            <option value="">Select Status</option>
                                            <option value="0" <?= $item_status === "0" ? "selected" : "" ?>>Pending</option>
                                            <option value="1" <?= $item_status === "1" ? "selected" : "" ?>>Bought</option>
                                            <option value="2" <?= $item_status === "2" ? "selected" : "" ?>>Not Available</option>
                                        </select>
                                        <div class="invalid-feedback">Please select status.</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="date">Date</label>
                                        <input type="date" class="form-control" id="date" name="date" required value="<?= htmlspecialchars($date ?: date('Y-m-d')) ?>">
                                        <div class="invalid-feedback">Please select date.</div>
                                    </div>
                                    <div class="col-md-4 mb-3 d-none">
                                        <label for="addedBy">Added By</label>
                                        <input type="text" class="form-control" id="addedBy" name="added_by" required placeholder="Enter Your Name" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>">
                                        <div class="invalid-feedback">Please enter your name.</div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-outline-dark mx-3" type="button" onclick="history.back();">Back</button>
                                    <button class="btn btn-success" type="submit">Save</button>
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php include '../Includes/footer.php'; ?>

<script>
    $(document).ready(function () {
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');
        if (message) {
            let title, icon;
            if (message.toLowerCase().includes('success')) {
                title = "Success";
                icon = "success";
            } else {
                title = "Error";
                icon = "error";
            }
            swal({
                title: title,
                text: message,
                icon: icon
            }).then(() => {
                const url = new URL(window.location.href);
                url.searchParams.delete('message');
                window.history.replaceState({}, document.title, url.toString());
            });
        }
    });
</script>
</body>
</html>