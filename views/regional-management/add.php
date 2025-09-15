<?php
include '../Includes/header.php';
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Regional Management');
$canadd = canUsercan_add($pdo, 'Regional Management'); // <-- use new
// If user does not have permission, redirect or show an error
if (!$canadd) {
    header("Location: ../../unauthorized.php");
    exit;
}


$regional_name = "";
$country_id = "";
$id = "";

// Check if editing
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM regionals WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $state = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($state) {
        $regional_name = $state['regional_name'];
        $country_id = $state['country_id'];
    }
}

$countries = $pdo->query("SELECT * FROM countries")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="app-main__outer">
    <div class="app-main__inner h-100">
        <div class="app-page-title app-page-title-simple">
            <div class="page-title-wrapper d-flex justify-content-between">
                <div class="page-title-heading"></div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Regional Management</a></li>
                        <li class="breadcrumb-item"><a href="add.php">Add New Region</a></li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title"><?= $id ? "Edit Region" : "Add Region" ?></h5>
                            <form class="needs-validation" novalidate action="save.php" method="post">
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="regional_name">Region Name</label>
                                        <input type="text" class="form-control" id="regional_name" name="regional_name"
                                            value="<?= htmlspecialchars($regional_name) ?>" required>
                                        <div class="invalid-feedback">Please enter Regional Name.</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="country_id">Country</label>
                                        <select class="form-control select2" id="country_id" name="country_id" required>
                                            <option value="">Select Country</option>
                                            <?php foreach ($countries as $country): ?>
                                                <option value="<?= $country['id'] ?>"
                                                    <?= $country_id == $country['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($country['country_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a country.</div>
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