<?php
require '../Includes/header.php';
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Participant Management');
// Check if user has permission to add participants
$canAdd = canUsercan_add($pdo, 'Participant Management'); // <-- use new
// If user does not have permission, redirect or show an error
if (!$canAdd) {
    header("Location: ../../unauthorized.php");
    exit;
}

// Fetch all programs for dropdown
$programs = $pdo->query("SELECT id, title FROM programs ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);

// Default values
$program_id = '';
$full_name = '';
$mobile = '';
$place = '';
$reference_id = '';
$registration_date = date('Y-m-d');
$attendance_status = '';
$payment_status = '';
$terms_accepted = 0;
$additional_notes = '';

// Check if editing
$isEdit = false;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $isEdit = true;
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM participants WHERE id = ?");
    $stmt->execute([$id]);
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($participant) {
        $program_id = $participant['program_id'];
        $full_name = $participant['full_name'];
        $mobile = $participant['mobile'];
        $place = $participant['place'];
        $reference_id = $participant['reference_id'];
        $registration_date = $participant['registration_date']; // <-- Registration Date fetched
        $attendance_status = $participant['attendance_status'];
        $payment_status = $participant['payment_status']; // <-- Payment Status fetched
        $terms_accepted = $participant['terms_accepted'];
        $additional_notes = $participant['additional_notes'];
    }
}
$hear_about_us = '';
if ($isEdit && isset($participant['hear_about_us'])) {
    $hear_about_us = $participant['hear_about_us'];
}
?>

<div class="app-main__outer">
    <div class="app-main__inner h-100">
        <div class="app-page-title app-page-title-simple">
            <div class="page-title-wrapper d-flex justify-content-between">
                <div class="page-title-heading">
                    <h4 class="mb-0"><?= $isEdit ? 'Edit Participant' : 'Add Participant' ?></h4>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Participant Management</a></li>
                        <li class="breadcrumb-item active"><?= $isEdit ? 'Edit Participant' : 'Add Participant' ?></li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-lg-8 mx-auto">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title mb-4"><?= $isEdit ? 'Edit Participant' : 'Participant Registration' ?></h5>
                        <form class="needs-validation" novalidate action="<?= $isEdit ? 'update.php' : 'save.php' ?>" method="post">
                            <?php if ($isEdit): ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                            <?php endif; ?>
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="program_id">Program</label>
                                    <select class="form-control" id="program_id" name="program_id" required>
                                        <option value="">Select Program</option>
                                        <?php foreach ($programs as $prog): ?>
                                            <option value="<?= $prog['id'] ?>" <?= $program_id == $prog['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($prog['title']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select a program.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="reference_id">Reference ID</label>
                                    <input type="text" class="form-control" id="reference_id" name="reference_id" placeholder="Reference ID (optional)" value="<?= htmlspecialchars($reference_id) ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" required placeholder="Enter full name" value="<?= htmlspecialchars($full_name) ?>">
                                    <div class="invalid-feedback">Please enter full name.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mobile">Mobile Number</label>
                                    <input type="tel" class="form-control" id="mobile" name="mobile" required placeholder="Enter mobile number" value="<?= htmlspecialchars($mobile) ?>">
                                    <div class="invalid-feedback">Please enter mobile number.</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="place">Place</label>
                                    <input type="text" class="form-control" id="place" name="place" required placeholder="Enter place/city" value="<?= htmlspecialchars($place) ?>">
                                    <div class="invalid-feedback">Please enter place.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="attendance_status">Attendance Status</label>
                                    <select class="form-control" id="attendance_status" name="attendance_status" required>
                                        <option value="">Select Status</option>
                                        <option value="registered" <?= $attendance_status == 'registered' ? 'selected' : '' ?>>Registered</option>
                                        <option value="attended" <?= $attendance_status == 'attended' ? 'selected' : '' ?>>Attended</option>
                                        <option value="cancelled" <?= $attendance_status == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <div class="invalid-feedback">Please select attendance status.</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="hear_about_us">Where did you hear about us?</label>
                                    <select class="form-control" id="hear_about_us" name="hear_about_us" required>
                                        <option value="">Select</option>
                                        <option value="Friend/Family" <?= $hear_about_us == 'Friend/Family' ? 'selected' : '' ?>>Friend/Family</option>
                                        <option value="Social Media" <?= $hear_about_us == 'Social Media' ? 'selected' : '' ?>>Social Media</option>
                                        <option value="Website" <?= $hear_about_us == 'Website' ? 'selected' : '' ?>>Website</option>
                                        <option value="Event" <?= $hear_about_us == 'Event' ? 'selected' : '' ?>>Event</option>
                                        <option value="Other" <?= $hear_about_us == 'Other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                    <div class="invalid-feedback">Please select how you heard about us.</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="terms_accepted" name="terms_accepted" value="1" required>
                                        <label class="form-check-label" for="terms_accepted">I accept the terms and conditions</label>
                                        <div class="invalid-feedback">You must accept the terms.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-outline-dark mx-3" type="button" onclick="history.back();">Back</button>
                                <button class="btn btn-success" type="submit">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <?php include '../Includes/footer.php'; ?>
    </div>
</div>
<script>
    // Bootstrap validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>