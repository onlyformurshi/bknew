<?php

include '../Includes/header.php';
require '../../config/config.php';
require_once '../../config/functions.php'; // Include functions file
checkModuleAccess($pdo, 'User Management'); // Check if the user has access to this

// Fetch all user roles
try {
    $stmt = $pdo->query("SELECT * FROM user_roles ORDER BY created_at DESC");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $roles = [];
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
                                <i class="fa fa-id-badge opacity-6"></i>
                            </span>
                            <span class="d-inline-block">User Role Management</span>
                        </div>
                    </div>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">User Roles</li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>User Roles</span>
                            <a href="add-user-role.php" class="btn btn-success btn-sm">
                                <i class="fa fa-plus"></i> Add Role
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Role Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($roles) > 0): ?>
                                            <?php $i = 1; foreach ($roles as $role): ?>
                                                <tr>
                                                    <td><?= $i++ ?></td>
                                                    <td><?= htmlspecialchars($role['role_name']) ?></td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <a style="cursor:pointer;" class="dropdown-toggle" type="button" id="dropdownMenu<?= $role['id'] ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                •••
                                                            </a>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenu<?= $role['id'] ?>">
                                                                <a class="dropdown-item" style="cursor:pointer;" onclick="editUser(<?= $role['id'] ?>)"><i class="fa fa-edit"></i> Edit</a>
                                                                <a class="dropdown-item text-danger" href="#" onclick="deleteRole(<?= $role['id'] ?>); return false;"><i class="fa fa-trash"></i> Delete</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center">No roles found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> </div> </div> </div>
           
        

<?php include '../Includes/footer.php'; ?>

<!-- SweetAlert for messages -->
<script src="../assets/scripts/sweetalert.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show SweetAlert if message param exists
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');
        if (message) {
            swal({
                title: "Success",
                text: message,
                icon: "success"
            }).then(() => {
                // Remove 'message' parameter from URL after showing alert
                const url = new URL(window.location.href);
                url.searchParams.delete('message');
                window.history.replaceState({}, document.title, url.toString());
            });
        }
    });

    function deleteRole(id) {
        swal({
            title: "Are you sure?",
            text: "This role will be permanently deleted!",
            icon: "warning",
            buttons: true,
            dangerMode: true
        }).then((willDelete) => {
            if (willDelete) {
                // Redirect to delete script (implement delete-user-role.php)
                window.location.href = "delete-user-role.php?id=" + id;
            }
        });
    }

    function editUser(id) {
        window.location.href = "add-user-role.php?id=" + id;
    }
</script>