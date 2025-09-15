<?php
include '../Includes/header.php';
require '../../config/config.php';
require '../../helpers/security.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'User Management');

$role_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$role_name = '';

if ($role_id) {
    // Editing: fetch role
    $stmt = $pdo->prepare("SELECT * FROM user_roles WHERE id = ?");
    $stmt->execute([$role_id]);
    $role = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($role) {
        $role_name = $role['role_name'];
    }
}
?>

<div class="app-main__outer">
    <div class="app-main__inner h-100">
        <div class="app-page-title app-page-title-simple">
            <div class="page-title-wrapper d-flex justify-content-between">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="user-role.php">User Role Management</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $role_id ? 'Edit' : 'Add' ?> User Role</li>
                    </ol>
                </nav>
            </div>
            <div class="row">
                <div class="col-md-8 ">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title"><?= $role_id ? 'Edit' : 'Add' ?> User Role</h5>
                            <form class="needs-validation" novalidate action="save-user-role.php" method="post">
                                <?php if ($role_id): ?>
                                    <input type="hidden" name="id" value="<?= $role_id ?>">
                                <?php endif; ?>
                                <div class="form-group">
                                    <label for="role_name">Role Name</label>
                                    <input type="text" class="form-control" id="role_name" name="role_name" required maxlength="50" pattern="^[A-Za-z\s]+$" value="<?= htmlspecialchars($role_name) ?>">
                                    <div class="invalid-feedback">
                                        Please enter a valid role name (letters and spaces only, max 50 chars).
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-outline-dark mx-3" type="button" onclick="history.back();">Back</button>
                                    <button class="btn btn-success" type="submit"><?= $role_id ? 'Update' : 'Save' ?></button>
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
<?php include '../Includes/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap validation
    var forms = document.getElementsByClassName('needs-validation');
    Array.prototype.forEach.call(forms, function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});
</script>