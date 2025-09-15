<?php
include '../Includes/header.php';
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Center Management');
$canadd = canUsercan_add($pdo, 'Center Management'); // <-- use new
// If user does not have permission, redirect or show an error
if (!$canadd) {
    header("Location: ../../unauthorized.php");
    exit;
}
$centre_name = $address = $city = $phone = $email = "";
$regional_id = "";
$id = "";

// Check if editing
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM centres WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $centre = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($centre) {
        $centre_name = $centre['centre_name'];
        $regional_id = $centre['regional_id'];
        $address = $centre['address'];
        $city = $centre['city'];
        $phone = $centre['phone'];
        $email = $centre['email'];
    }
}

// Fetch regionals
$regionals = $pdo->query("SELECT * FROM regionals")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="app-main__outer">
    <div class="app-main__inner h-100">
        <div class="app-page-title app-page-title-simple">
            <div class="page-title-wrapper d-flex justify-content-between">
                <div class="page-title-heading"></div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">centre Management</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $id ? "Edit centre" : "Add New centre" ?></li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-md-12">

                    <?php if (isset($_GET['message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_GET['message']) ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title"><?= $id ? "Edit centre" : "Add centre" ?></h5>
                            <form class="needs-validation" novalidate action="save.php" method="post">
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="centre_name">centre Name</label>
                                        <input type="text" class="form-control" id="centre_name" name="centre_name"
                                            value="<?= htmlspecialchars($centre_name) ?>" required>
                                        <div class="invalid-feedback">Please enter centre Name.</div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="regional_id">Region</label>
                                        <select class="form-control select2" id="regional_id" name="regional_id" required>
                                            <option value="">Select Region</option>
                                            <?php foreach ($regionals as $state): ?>
                                                <option value="<?= $state['id'] ?>" <?= $regional_id == $state['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($state['regional_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a state.</div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="address">Address</label>
                                        <input type="text" class="form-control" id="address" name="address"
                                            value="<?= htmlspecialchars($address) ?>" required>
                                        <div class="invalid-feedback">Please enter address.</div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="city">City</label>
                                        <input type="text" class="form-control" id="city" name="city"
                                            value="<?= htmlspecialchars($city) ?>" required>
                                        <div class="invalid-feedback">Please enter city.</div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="phone">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            value="<?= htmlspecialchars($phone) ?>" required>
                                        <div class="invalid-feedback">Please enter phone number.</div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="<?= htmlspecialchars($email) ?>" required>
                                        <div class="invalid-feedback">Please enter a valid email.</div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="center_coordinator_name">Center Coordinator Name</label>
                                        <input type="text" class="form-control" id="center_coordinator_name" name="center_coordinator_name"
                                            value="<?= htmlspecialchars($centre['center_coordinator_name'] ?? $center_coordinator_name ?? '') ?>">
                                        <div class="invalid-feedback">Please enter coordinator name.</div>
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

    <style>
        .select2 {
            width: 100% !important;
        }
    </style>

<?php include '../Includes/footer.php'; ?>
