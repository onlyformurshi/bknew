<?php
include '../Includes/header.php';
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Program Management');
// Check if user has permission to add countries
$canedit = canUsercan_edit($pdo, 'Program Management'); // <-- use new
// If user does not have permission, redirect or show an error
if (!$canedit) {
    header("Location: ../../unauthorized.php");
    exit;
}


$id = "";
$country_id = $regional_id = $centre_id = $title = $description = $venue = "";
$instructor_name = $max_participants = $current_participants = "";
$marketing_methods = $status = "";

$countries = $pdo->query("SELECT * FROM countries WHERE status = 1")->fetchAll(PDO::FETCH_ASSOC);
$regionals = [];
$centres = [];

// Check if editing
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM programs WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $program = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($program) {
        $centre_id = $program['centre_id'];
        $title = $program['title'];
        $description = $program['description'];
        $venue = $program['venue'];
        $instructor_name = $program['instructor_name'];
        $max_participants = $program['max_participants'];
        $current_participants = $program['current_participants'];
        $marketing_methods = $program['marketing_methods'];
        $status = $program['status'];

        // Get state and country from centre
        $stmt = $pdo->prepare("SELECT s.id AS regional_id, s.country_id FROM centres b JOIN regionals s ON b.regional_id = s.id WHERE b.id = :centre_id");
        $stmt->execute(['centre_id' => $centre_id]);
        $location = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($location) {
            $regional_id = $location['regional_id'];
            $country_id = $location['country_id'];
        }

        if ($country_id) {
            $regionals = $pdo->prepare("SELECT * FROM regionals WHERE country_id = ?");
            $regionals->execute([$country_id]);
            $regionals = $regionals->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($regional_id) {
            $centres = $pdo->prepare("SELECT * FROM centres WHERE regional_id = ?");
            $centres->execute([$regional_id]);
            $centres = $centres->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}

$sessions = [];
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM program_sessions_times WHERE program_id = ? ORDER BY id ASC");
    $stmt->execute([$id]);
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="app-main__outer">
    <div class="app-main__inner h-100">
        <div class="app-page-title app-page-title-simple">

            <div class="page-title-wrapper d-flex justify-content-between">
                <div class="page-title-head center-elem mb-3">
                    <span class="d-inline-block">
                        <i class="lnr-calendar-full opacity-6"></i>
                    </span>
                    <span class="d-inline-block"><?= $id ? "Edit Program" : "Add Program" ?></span>
                </div>
                <div class="page-title-heading"></div>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Programssssssss Management</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $id ? "Edit Program" : "Add New Program" ?>
                        </li>
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
                    <form class="needs-validation" novalidate action="save.php" method="post" enctype="multipart/form-data">
                        <div class="main-card mb-3 card">
                            <div class="card-body">
                                <h5 class="card-title">General Details</h5>

                                <div class="form-row">
                                    <!-- Country -->
                                    <div class="col-md-4 mb-3">
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

                                    <!-- State -->
                                    <div class="col-md-4 mb-3">
                                        <label for="regional_id">Region</label>
                                        <select class="form-control select2" id="regional_id" name="regional_id" required>
                                            <option value="">Select Region</option>
                                            <?php foreach ($regionals as $state): ?>
                                                <option value="<?= $state['id'] ?>"
                                                    <?= $regional_id == $state['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($state['regional_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a Regional.</div>
                                    </div>

                                    <!-- centre -->
                                    <div class="col-md-4 mb-3">
                                        <label for="centre_id">centre</label>
                                        <select class="form-control select2" id="centre_id" name="centre_id" required>
                                            <option value="">Select centre</option>
                                            <?php foreach ($centres as $centre): ?>
                                                <option value="<?= $centre['id'] ?>"
                                                    <?= $centre_id == $centre['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($centre['centre_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a centre.</div>
                                    </div>
                                    <!-- Program Title -->
                                    <div class="col-md-4 mb-3">
                                        <label for="title">Program Title</label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            value="<?= htmlspecialchars($title) ?>" required>
                                        <div class="invalid-feedback">Please enter program title.</div>
                                    </div>

                                    <!-- Program Description -->
                                    <div class="col-md-4 mb-3">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"
                                            required><?= htmlspecialchars($description) ?></textarea>
                                        <div class="invalid-feedback">Please enter program description.</div>
                                    </div>

                                    <!-- Venue -->
                                    <div class="col-md-4 mb-3">
                                        <label for="venue">Venue Address</label>
                                        <input type="text" class="form-control" id="venue" name="venue"
                                            value="<?= htmlspecialchars($venue) ?>" required>
                                        <div class="invalid-feedback">Please enter program venue.</div>
                                    </div>

                                    <!-- Instructor Name -->
                                    <div class="col-md-4 mb-3">
                                        <label for="instructor_name">Instructor Name</label>
                                        <input type="text" class="form-control" id="instructor_name" name="instructor_name"
                                            value="<?= htmlspecialchars($instructor_name) ?>" required>
                                        <div class="invalid-feedback">Please enter instructor name.</div>
                                    </div>

                                    <!-- Max Participants -->
                                    <div class="col-md-4 mb-3">
                                        <label for="max_participants">Max Participants</label>
                                        <input type="number" class="form-control" id="max_participants"
                                            name="max_participants" value="<?= htmlspecialchars($max_participants) ?>"
                                            required>
                                        <div class="invalid-feedback">Please enter max participants.</div>
                                    </div>

                                    <!-- Current Participants -->
                                    <div class="col-md-4 mb-3">
                                        <label for="current_participants">Current Participants</label>
                                        <input type="number" class="form-control" id="current_participants"
                                            name="current_participants"
                                            value="<?= htmlspecialchars($current_participants) ?>" required>
                                        <div class="invalid-feedback">Please enter current participants.</div>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-4 mb-3">
                                        <label for="status">Progress</label>
                                        <select class="form-control select2" id="status" name="status" required>
                                            <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="activated" <?= $status == 'activated' ? 'selected' : '' ?>>Activated</option>
                                        </select>
                                        <div class="invalid-feedback">Please select program status.</div>
                                    </div>

                                    <!-- Program Image -->
                                    <div class="col-md-4 mb-3">
                                        <label for="program_img">Program Image</label>
                                        <input type="file" class="form-control" id="program_img" name="program_img" accept="image/*" <?= $id ? '' : 'required' ?>>
                                        <?php if (!empty($program['program_img'])): ?>
                                            <div class="mt-2">
                                                <img src="../../uploads/programs/<?= htmlspecialchars($program['program_img']) ?>" alt="Program Image" style="max-width: 100px; max-height: 100px;">
                                                <input type="hidden" name="old_program_img" value="<?= htmlspecialchars($program['program_img']) ?>">
                                            </div>
                                        <?php endif; ?>
                                        <div class="invalid-feedback">Please upload a program image.</div>
                                    </div>
                                </div>

                            </div>




                        </div>

                        <!-- Session Details -->
                        <div class="main-card mb-3 card">
                            <div class="card-body">
                                <h5 class="card-title">Session Details</h5>
                                <div id="session-container">
                                    <?php if (!empty($sessions)): ?>
                                        <?php foreach ($sessions as $i => $session): ?>
                                            <div class="form-row session-row align-items-end mb-2">
                                                <div class="col-md-4 mb-2">
                                                    <label>Session Name</label>
                                                    <input type="text" class="form-control" name="session_name[]" placeholder="Session Name" required
                                                        value="<?= htmlspecialchars($session['session_name']) ?>">
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label>Start Date & Time</label>
                                                    <input type="datetime-local" class="form-control" name="session_start[]" required
                                                        value="<?= date('Y-m-d\TH:i', strtotime($session['session_start'])) ?>">
                                                    <small class="text-muted">
                                                        <?= date('d/m/Y H:i', strtotime($session['session_start'])) ?>
                                                    </small>
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label>End Date & Time</label>
                                                    <input type="datetime-local" class="form-control" name="session_end[]" required
                                                        value="<?= date('Y-m-d\TH:i', strtotime($session['session_end'])) ?>">
                                                    <small class="text-muted">
                                                        <?= date('d/m/Y H:i', strtotime($session['session_end'])) ?>
                                                    </small>
                                                </div>
                                                <div class="col-md-2 mb-2 d-flex">
                                                    <button type="button" class="btn btn-success btn-sm mr-2 add-session">+</button>
                                                    <button type="button" class="btn btn-danger btn-sm remove-session">−</button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="form-row session-row align-items-end mb-2">
                                            <div class="col-md-4 mb-2">
                                                <label>Session Name</label>
                                                <input type="text" class="form-control" name="session_name[]" placeholder="Session Name" required>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label>Starts Date & Time</label>
                                                <input type="datetime-local" class="form-control" name="session_start[]" required>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label>End Date & Time</label>
                                                <input type="datetime-local" class="form-control" name="session_end[]" required>
                                            </div>
                                            <div class="col-md-2 mb-2 d-flex">
                                                <button type="button" class="btn btn-success btn-sm mr-2 add-session">+</button>
                                                <button type="button" class="btn btn-danger btn-sm remove-session">−</button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button class="btn btn-outline-dark mx-3" type="button" onclick="history.back();">Back</button>
                            <button class="btn btn-success" type="submit">Save</button>
                            <input type="hidden" name="id" value="<?= $id ?>">
                        </div>
                </div>

            </div>
        </div>
        </form>
        <style>
            .select2 {
                width: 100% !important;
            }
            .is-invalid { border-color: #dc3545 !important; }
        </style>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const countrySelect = document.getElementById('country_id');
                const regionalselect = document.getElementById('regional_id');
                const centreSelect = document.getElementById('centre_id');

                // Add event listener for country change
                countrySelect.addEventListener('change', function() {
                    const countryId = this.value;
                    if (!countryId) return;

                    // Fetch regionals based on selected country
                    fetch('../../ajax/get_regionals.php?country_id=' + countryId)
                        .then(response => response.json())
                        .then(data => {
                            regionalselect.innerHTML = '<option value="">Select Region</option>';
                            data.forEach(state => {
                                const option = document.createElement('option');
                                option.value = state.id;
                                option.textContent = state.regional_name;
                                regionalselect.appendChild(option);
                            });
                        });

                    // Clear centre select
                    centreSelect.innerHTML = '<option value="">Select centre</option>';
                });

                // Add event listener for state change
                regionalselect.addEventListener('change', function() {
                    const stateId = this.value;
                    if (!stateId) return;

                    // Fetch centres based on selected state
                    fetch('../../ajax/get_centres.php?regional_id=' + stateId)
                        .then(response => response.json())
                        .then(data => {
                            centreSelect.innerHTML = '<option value="">Select centre</option>';
                            data.forEach(centre => {
                                const option = document.createElement('option');
                                option.value = centre.id;
                                option.textContent = centre.centre_name;
                                centreSelect.appendChild(option);
                            });
                        });
                });
            });
        </script>
        <script>
document.addEventListener("DOMContentLoaded", function() {
    // Add session row
    document.body.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-session')) {
            const container = document.getElementById('session-container');
            const row = e.target.closest('.session-row');
            const clone = row.cloneNode(true);

            // Clear input values in the clone
            clone.querySelectorAll('input').forEach(input => input.value = '');

            container.appendChild(clone);
        }

        // Remove session row
        if (e.target.classList.contains('remove-session')) {
            const container = document.getElementById('session-container');
            if (container.querySelectorAll('.session-row').length > 1) {
                e.target.closest('.session-row').remove();
            } else {
                alert('At least one session is required.');
            }
        }
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Existing dynamic row logic...

    // Session date validation on form submit
    document.querySelector('form.needs-validation').addEventListener('submit', function(e) {
        let valid = true;
        let sessionRows = document.querySelectorAll('#session-container .session-row');
        for (let row of sessionRows) {
            let startInput = row.querySelector('input[name="session_start[]"]');
            let endInput = row.querySelector('input[name="session_end[]"]');
            let start = startInput.value ? new Date(startInput.value) : null;
            let end = endInput.value ? new Date(endInput.value) : null;

            // Remove previous error styles
            startInput.classList.remove('is-invalid');
            endInput.classList.remove('is-invalid');

            // Only check if end is after start
            if (start && end && end <= start) {
                endInput.classList.add('is-invalid');
                valid = false;
            }
        }
        if (!valid) {
            e.preventDefault();
            alert('Please ensure all session end dates are after start dates.');
        }
    });
});
</script>
        <div class="app-wrapper-footer">
            <div class="app-footer">
                <div class="app-footer__inner d-flex justify-content-center">
                    <li style="list-style-type: none;">Developed by <a href="https://cearsleg.com/">Cearsleg
                            Technologies
                            Pvt Ltd</a> </li>
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
</body>

</html>