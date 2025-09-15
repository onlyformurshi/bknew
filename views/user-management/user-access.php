<?php
include '../Includes/header.php'; // Use include to get header/sidebar
require '../../config/config.php';
require_once '../../config/functions.php'; // Include functions file
checkModuleAccess($pdo, 'User Management'); // Check if the user has access to this

// Fetch all roles
$roles = $pdo->query("SELECT id, role_name FROM user_roles ORDER BY role_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all modules
$modules = $pdo->query("SELECT id, module_name FROM modules ORDER BY module_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// If a role is selected, fetch its access
$selectedRoleId = isset($_GET['role_id']) ? intval($_GET['role_id']) : 0;
$access = [];
if ($selectedRoleId) {
    $stmt = $pdo->prepare("SELECT * FROM user_role_access WHERE role_id = ?");
    $stmt->execute([$selectedRoleId]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $access[$row['module_id']] = $row;
    }
}
?>

<div class="app-main__outer">
    <div class="app-main__inner h-100">
        <div class="app-page-title app-page-title-simple">
            <div class="page-title-wrapper d-flex justify-content-between">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../user-management/">User Management</a></li>
                        <li class="breadcrumb-item active" aria-current="page">User Role Access</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">User Role Access Management</h5>
                        <form method="get" class="mb-4">
                            <label for="role_id">Select User Role:</label>
                            <select name="role_id" id="role_id" class="form-control" style="width:auto;display:inline-block;" onchange="this.form.submit()">
                                <option value="">-- Select Role --</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>" <?= $selectedRoleId == $role['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($role['role_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>

                        <?php if ($selectedRoleId): ?>
                        <form method="post" action="save-user-access.php">
                            <input type="hidden" name="role_id" value="<?= $selectedRoleId ?>">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Module</th>
                                        <th>Can View</th>
                                        <th>Can Add</th>
                                        <th>Can Edit</th>
                                        <th>Can Delete</th>
                                        <th>Can View Price</th>
                                        <th>Can View All Program</th> <!-- New column -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($modules as $module): 
                                        $row = $access[$module['id']] ?? [];
                                        $showPriceCheckbox = in_array($module['module_name'], ['Program Management', 'Sponsor Management']);
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($module['module_name']) ?></td>
                                        <td class="text-center">
                                            <input type="checkbox" name="access[<?= $module['id'] ?>][can_view]" value="1" <?= (!empty($row['can_view'])) ? 'checked' : '' ?>>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="access[<?= $module['id'] ?>][can_add]" value="1" <?= (!empty($row['can_add'])) ? 'checked' : '' ?>>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="access[<?= $module['id'] ?>][can_edit]" value="1" <?= (!empty($row['can_edit'])) ? 'checked' : '' ?>>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="access[<?= $module['id'] ?>][can_delete]" value="1" <?= (!empty($row['can_delete'])) ? 'checked' : '' ?>>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($showPriceCheckbox): ?>
                                                <input type="checkbox" name="access[<?= $module['id'] ?>][can_view_price]" value="1" <?= (!empty($row['can_view_price'])) ? 'checked' : '' ?>>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="access[<?= $module['id'] ?>][can_view_program]" value="1" <?= (!empty($row['can_view_program'])) ? 'checked' : '' ?>>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">Save Access</button>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
    </div>
<?php include '../Includes/footer.php'; ?>