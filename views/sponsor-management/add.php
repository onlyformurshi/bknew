<?php
require '../Includes/header.php';
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Sponsor Management');
$canadd = canUsercan_add($pdo, 'Sponsor Management');

// Redirect if user cannot add
if (!$canadd) {
    header('Location: ../../unauthorized.php');
    exit;
}

// Get the role_id for Sponsor from user_roles
$sponsorRoleStmt = $pdo->prepare("SELECT id FROM user_roles WHERE role_name = 'Sponsor' LIMIT 1");
$sponsorRoleStmt->execute();
$sponsorRoleId = $sponsorRoleStmt->fetchColumn();

// Fetch sponsors only if sponsor role ID is found
$sponsors = [];
if ($sponsorRoleId) {
    $sponsorStmt = $pdo->prepare("SELECT id, full_name, agency_name FROM users WHERE role = ? AND status = 'Active' ORDER BY full_name ASC");
    $sponsorStmt->execute([$sponsorRoleId]);
    $sponsors = $sponsorStmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch programs
$programs = $pdo->query("SELECT id, title, program_number FROM programs WHERE status IN ('pending', 'activated') ORDER BY title ASC")->fetchAll(PDO::FETCH_ASSOC);

// Categories
$categories = [
    'facebook_advertisements' => 'Facebook',
    'billboard_advertisements' => 'Billboard',
    'instagram_advertisements' => 'Instagram',
    'newspaper_advertisements' => 'Newspaper',
    'radio_advertisements' => 'Radio',
    'television_advertisements' => 'Television',
    'pamphlet' => 'Pamphlet'
];

// Fetch items by category
$itemsByCategory = [];
foreach (array_keys($categories) as $cat) {
    $table = $cat === 'pamphlet' ? 'program_pamphlets' : $cat;
    $nameCol = $cat === 'billboard_advertisements' ? 'agency_name' : ($cat === 'pamphlet' ? 'pamphlet_designer_name' : 'name');
    $stmt = $pdo->query("SELECT id, $nameCol AS item_name FROM $table ORDER BY item_name ASC");
    $itemsByCategory[$cat] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Payment types and status
$paymentTypes = ['Cash', 'Online', 'Cheque'];
$paymentStatuses = ['Pending', 'Completed'];

?>

<div class="app-main__outer">
    <div class="app-main__inner h-100">
        <div class="app-page-title app-page-title-simple">
            <div class="page-title-wrapper d-flex justify-content-between">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Sponsor Management</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Sponsor Contribution</li>
                    </ol>
                </nav>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-hand-holding-heart me-2"></i> Add Sponsor Contribution</h5>
                            <form class="needs-validation" novalidate action="save.php" method="post" id="sponsorForm">
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="user_id">Sponsor Name</label>
                                        <select class="form-control select2" name="user_id" id="user_id" required>
                                            <option value="">Select Sponsor</option>
                                            <?php foreach ($sponsors as $s): ?>
                                                <option value="<?= $s['id'] ?>">
                                                    <?= htmlspecialchars($s['full_name']) ?>
                                                    <?= $s['agency_name'] ? ' (' . htmlspecialchars($s['agency_name']) . ')' : '' ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a sponsor.</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="program_id">Program</label>
                                        <select class="form-control select2" name="program_id" id="program_id" required>
                                            <option value="">Select Program</option>
                                            <?php foreach ($programs as $p): ?>
                                                <option value="<?= $p['id'] ?>">
                                                    <?= htmlspecialchars($p['title']) ?> (<?= htmlspecialchars($p['program_number']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a program.</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="category">Category</label>
                                        <select class="form-control select2" name="category" id="category" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $key => $cat): ?>
                                                <option value="<?= $key ?>"><?= htmlspecialchars($cat) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a category.</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="item_id">Item</label>
                                        <select class="form-control select2" name="item_id" id="item_id" required>
                                            <option value="">Select Item</option>
                                        </select>
                                        <div class="invalid-feedback">Please select an item.</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="amount">Amount</label>
                                        <input type="number" class="form-control" name="amount" id="amount" min="1" required>
                                        <div class="invalid-feedback">Please enter a valid amount.</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="payment_status">Payment Status</label>
                                        <select class="form-control" name="payment_status" id="payment_status" required>
                                            <option value="">Select Status</option>
                                            <?php foreach ($paymentStatuses as $status): ?>
                                                <option value="<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($status) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select payment status.</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="payment_type">Payment Type</label>
                                        <select class="form-control" name="payment_type" id="payment_type">
                                            <option value="">Select Payment Type</option>
                                            <?php foreach ($paymentTypes as $type): ?>
                                                <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <!-- Remove 'required' and invalid-feedback -->
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-outline-dark mx-3" type="button" onclick="history.back();">Back</button>
                                    <button class="btn btn-danger px-4" type="submit"><i class="fa fa-save me-2"></i> Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 </div>
</div>
<?php include '../Includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();

        let itemBalances = {};

        function loadItems() {
            const category = $('#category').val();
            const programId = $('#program_id').val();

            if (category && programId) {
                $.getJSON('get-items.php', {
                    category,
                    program_id: programId
                }, function(data) {
                    const $itemSelect = $('#item_id');
                    $itemSelect.empty().append('<option value="">Select Item</option>');
                    itemBalances = {}; // Reset
                    data.forEach(item => {
                        $itemSelect.append(
                            $('<option>', {
                                value: item.id,
                                text: item.item_name + ' (Balance: ' + item.balance_amount + ')'
                            })
                        );
                        itemBalances[item.id] = item.balance_amount;
                    });
                    $itemSelect.trigger('change');
                });
            }
        }

        $('#category, #program_id').on('change', loadItems);

        $('#sponsorForm').on('submit', function(e) {
            const itemId = $('#item_id').val();
            const amount = parseFloat($('#amount').val());
            const balance = itemBalances[itemId];

            if (itemId && balance !== undefined && amount > balance) {
                e.preventDefault();
                alert('Entered amount is greater than the available balance (' + balance + ').');
                return false;
            }

            // Remove payment_type required validation
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            $(this).addClass('was-validated');
        });
    });
</script>

<style>
    .select2 {
        width: 100% !important;
    }
</style>
