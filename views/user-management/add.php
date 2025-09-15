<?php
include '../Includes/header.php';
require '../../config/config.php';
require '../../helpers/security.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'User Management');

// Fetch roles from user_roles table
$roleOptions = [];
$stmt = $pdo->query("SELECT id, role_name FROM user_roles ORDER BY role_name ASC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $roleOptions[] = $row;
}

// Initialize values
$id = $full_name = $email = $phone = $role_id = $password = "";

// Editing?
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $full_name = $user['full_name'];
        $email = $user['email'];
        $phone = $user['phone'];
        $role_id = $user['role']; // Now this should store the role_id
    }
}
?>

<div class="app-main__outer">
    <div class="app-main__inner h-100">
        <div class="app-page-title app-page-title-simple">
            <div class="page-title-wrapper d-flex justify-content-between">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="User_management.php">User Management</a></li>
                        <li class="breadcrumb-item"><a href="add_User.php"><?= $id ? 'Edit' : 'Add' ?> User</a></li>
                    </ol>
                </nav>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title"><?= $id ? "Edit User" : "Add User" ?></h5>
                            <form class="needs-validation" novalidate action="save.php" method="post" id="userForm">
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="full_name">Full Name</label>
                                        <input type="text" class="form-control" name="full_name" id="full_name" required
                                            minlength="3" maxlength="50" pattern="^[A-Za-z\s]+$"
                                            value="<?= htmlspecialchars($full_name) ?>">
                                        <div class="invalid-feedback">
                                            Please enter a valid full name (letters and spaces only, 3-50 characters).
                                        </div>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="email" id="email" required
                                            maxlength="100" value="<?= htmlspecialchars($email) ?>">
                                        <div class="invalid-feedback">
                                            Please enter a valid email address (e.g., user@example.com).
                                        </div>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="phone">Phone</label>
                                        <input type="tel" class="form-control" name="phone" id="phone"
                                            pattern="^\d{10,15}$" required value="<?= htmlspecialchars($phone) ?>">
                                        <div class="invalid-feedback">
                                            Please enter a valid phone number (10-15 digits only).
                                        </div>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="password">Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="password" id="password"
                                                <?= !$id ? 'required' : '' ?> minlength="6" maxlength="32">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">
                                            <?= $id ? 'Leave blank to keep current password' : '6-32 characters' ?>
                                        </small>
                                        <div class="invalid-feedback">
                                            Password must be 6-32 characters.
                                        </div>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                        <div class="password-strength mt-1">
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar" role="progressbar"></div>
                                            </div>
                                            <small class="password-strength-text text-muted"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="role">Role</label>
                                        <select class="form-control select2" name="role" id="role" required>
                                            <option value="">Select Role</option>
                                            <?php foreach ($roleOptions as $option): ?>
                                                <option value="<?= $option['id'] ?>" data-name="<?= htmlspecialchars($option['role_name']) ?>"
                                                    <?= ($role_id ?? '') == $option['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($option['role_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select a role.
                                        </div>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3 d-none" id="sponsorTypeDiv">
                                        <label for="sponsor_type">Sponsor Type</label>
                                        <select class="form-control" name="sponsor_type" id="sponsor_type">
                                            <option value="">Select Type</option>
                                            <option value="Individual" <?= ($_POST['sponsor_type'] ?? '') === 'Individual' ? 'selected' : '' ?>>Individual</option>
                                            <option value="Agency" <?= ($_POST['sponsor_type'] ?? '') === 'Agency' ? 'selected' : '' ?>>Agency</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select sponsor type.
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3 d-none" id="agencyNameDiv">
                                        <label for="agency_name">Agency Name</label>
                                        <input type="text" class="form-control" name="agency_name" id="agency_name" maxlength="100" value="<?= htmlspecialchars($_POST['agency_name'] ?? '') ?>">
                                        <div class="invalid-feedback">
                                            Please enter agency name.
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-outline-dark mx-3" type="button" onclick="history.back();">Back</button>
                                    <button class="btn btn-success" type="submit" id="submitBtn">Save</button>
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    </body>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation
            const form = document.getElementById('userForm');
            const inputs = form.querySelectorAll('input, select');
            const passwordInput = document.getElementById('password');
            const passwordStrengthBar = document.querySelector('.password-strength .progress-bar');
            const passwordStrengthText = document.querySelector('.password-strength-text');

            // Toggle password visibility
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    const input = this.closest('.input-group').querySelector('input');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

            // Password strength indicator
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;

                    // Length check
                    if (password.length >= 6) strength += 1;
                    if (password.length >= 10) strength += 1;

                    // Complexity checks
                    if (/[A-Z]/.test(password)) strength += 1;
                    if (/[0-9]/.test(password)) strength += 1;
                    if (/[^A-Za-z0-9]/.test(password)) strength += 1;

                    // Update UI
                    const strengthPercent = (strength / 5) * 100;
                    passwordStrengthBar.style.width = strengthPercent + '%';

                    // Color and text based on strength
                    if (password.length === 0) {
                        passwordStrengthBar.className = 'progress-bar';
                        passwordStrengthText.textContent = '';
                    } else if (strength <= 2) {
                        passwordStrengthBar.className = 'progress-bar bg-danger';
                        passwordStrengthText.textContent = 'Weak';
                        passwordStrengthText.className = 'password-strength-text text-danger';
                    } else if (strength <= 3) {
                        passwordStrengthBar.className = 'progress-bar bg-warning';
                        passwordStrengthText.textContent = 'Moderate';
                        passwordStrengthText.className = 'password-strength-text text-warning';
                    } else {
                        passwordStrengthBar.className = 'progress-bar bg-success';
                        passwordStrengthText.textContent = 'Strong';
                        passwordStrengthText.className = 'password-strength-text text-success';
                    }
                });
            }

            // Real-time validation for each input
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    validateField(this);
                });

                input.addEventListener('blur', function() {
                    validateField(this);
                });
            });

            // Form submission handler
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();

                    // Force validation of all fields
                    inputs.forEach(input => {
                        validateField(input);
                    });

                    // Scroll to first invalid field
                    const firstInvalid = form.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstInvalid.focus();
                    }
                }

                form.classList.add('was-validated');
            });

            // Custom validation function
            function validateField(field) {
                const isValid = field.checkValidity();
                const feedback = field.nextElementSibling;

                if (field.tagName === 'SELECT' && field.value === '') {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                } else if (isValid) {
                    field.classList.add('is-valid');
                    field.classList.remove('is-invalid');
                } else {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                }

                // Special handling for password field when editing
                if (field.id === 'password' && <?= $id ? 'true' : 'false' ?> && field.value === '') {
                    field.classList.remove('is-invalid');
                    field.classList.remove('is-valid');
                }
            }

            // Initialize validation for pre-filled fields
            inputs.forEach(input => {
                if (input.value) validateField(input);
            });

            // Show/hide sponsor type and agency name fields based on role
            const roleSelect = document.getElementById('role');
            const sponsorTypeDiv = document.getElementById('sponsorTypeDiv');
            const sponsorTypeSelect = document.getElementById('sponsor_type');
            const agencyNameDiv = document.getElementById('agencyNameDiv');
            const agencyNameInput = document.getElementById('agency_name');

            function toggleSponsorFields() {
                // Get selected option and its data-name
                const selectedOption = roleSelect.options[roleSelect.selectedIndex];
                const roleName = selectedOption ? selectedOption.getAttribute('data-name') : '';

                if (roleName === 'Sponsor') {
                    sponsorTypeDiv.classList.remove('d-none');
                    sponsorTypeSelect.required = true;
                    if (sponsorTypeSelect.value === 'Agency') {
                        agencyNameDiv.classList.remove('d-none');
                        agencyNameInput.required = true;
                    } else {
                        agencyNameDiv.classList.add('d-none');
                        agencyNameInput.required = false;
                        agencyNameInput.value = '';
                    }
                } else {
                    sponsorTypeDiv.classList.add('d-none');
                    sponsorTypeSelect.required = false;
                    sponsorTypeSelect.value = '';
                    agencyNameDiv.classList.add('d-none');
                    agencyNameInput.required = false;
                    agencyNameInput.value = '';
                }
            }

            roleSelect.addEventListener('change', toggleSponsorFields);
            sponsorTypeSelect.addEventListener('change', toggleSponsorFields);

            // Initialize on page load
            toggleSponsorFields();
        });
    </script>
    <div class="app-wrapper-footer">
        <div class="app-footer">
            <div class="app-footer__inner d-flex justify-content-center">
                <li style="list-style-type: none;">Developed by <a
                        href="https://cearsleg.com/">Cearsleg Technologies Pvt Ltd</a> </li>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<script src="../assets/scripts/jquery.min.js"></script>
<script src="../assets/scripts/moment.min.js"></script>
<script src="../assets/scripts/daterangepicker.min.js"></script>
<script type="text/javascript" src="../assets/scripts/main.js"></script>
<script src="https://use.fontawesome.com/f9637666d8.js"></script>
<script src="../assets/scripts/choosen.jquery.js"></script>
<script src="../assets/scripts/sweetalert.min.js"></script>


</html>