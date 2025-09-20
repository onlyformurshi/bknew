<?php
require '../Includes/header.php';
require '../../config/config.php';
require_once '../../config/functions.php';

checkModuleAccess($pdo, 'Participant Management');
$canadd = canUsercan_add($pdo, 'Participant Management'); // <-- use new function
$candelete = canUsercan_delete($pdo, 'Participant Management'); // <-- use new function
$canedit = canUsercan_edit($pdo, 'Participant Management');
// Fetch programs for dropdown (if needed elsewhere)
$programs = $pdo->query("SELECT id, title, program_number FROM programs ORDER BY program_number")->fetchAll(PDO::FETCH_ASSOC);

// Build WHERE clause
$whereConditions = [];
$queryParams = [];

// Filters
if (!empty($_GET['program_number'])) {
    $whereConditions[] = "programs.program_number = :program_number";
    $queryParams['program_number'] = $_GET['program_number'];
}
if (!empty($_GET['country_id'])) {
    $whereConditions[] = "programs.country_id = :country_id";
    $queryParams['country_id'] = $_GET['country_id'];
}
if (!empty($_GET['regional_id'])) {
    $whereConditions[] = "programs.regional_id = :regional_id";
    $queryParams['regional_id'] = $_GET['regional_id'];
}
if (!empty($_GET['centre_id'])) {
    $whereConditions[] = "programs.centre_id = :centre_id";
    $queryParams['centre_id'] = $_GET['centre_id'];
}
if (!empty($_GET['full_name'])) {
    $whereConditions[] = "participants.full_name LIKE :full_name";
    $queryParams['full_name'] = "%{$_GET['full_name']}%";
}
if (!empty($_GET['mobile'])) {
    $whereConditions[] = "participants.mobile LIKE :mobile";
    $queryParams['mobile'] = "%{$_GET['mobile']}%";
}
if (!empty($_GET['reference_id'])) {
    $whereConditions[] = "participants.reference_id = :reference_id";
    $queryParams['reference_id'] = $_GET['reference_id'];
}
if (!empty($_GET['attendance_status'])) {
    $whereConditions[] = "participants.attendance_status = :attendance_status";
    $queryParams['attendance_status'] = $_GET['attendance_status'];
}

$whereClause = $whereConditions ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Pagination
$recordsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $recordsPerPage;

// Count total
$countQuery = "SELECT COUNT(*) FROM participants
               LEFT JOIN programs ON participants.program_id = programs.id
               $whereClause";
$stmtCount = $pdo->prepare($countQuery);
$stmtCount->execute($queryParams);
$totalRecords = $stmtCount->fetchColumn();
$totalPages = ceil($totalRecords / $recordsPerPage);

// Main query
$query = "SELECT participants.*, programs.title, programs.program_number, programs.country_id, programs.regional_id, programs.centre_id
          FROM participants
          LEFT JOIN programs ON participants.program_id = programs.id
          $whereClause
          ORDER BY participants.created_at DESC
          LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
foreach ($queryParams as $key => $value) {
    $stmt->bindValue(':' . $key, $value);
}
$stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="app-main__outer">
    <div class="app-main__inner">
        <div class="row">

            <div class="col-md-6 col-xl-4">
                <div class="card mb-3 widget-content">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                                <div class="widget-heading">Total Participants</div>
                                <div class="widget-subheading">Count based on current filters</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers text-info"><?= $totalRecords ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <div class="app-page-title app-page-title-simple">
            <div class="page-title-wrapper d-flex justify-content-between">
                <div class="page-title-heading">
                    <div>
                        <div class="page-title-head center-elem mb-3">
                            <span class="d-inline-block">
                                <i class="lnr-users opacity-6"></i>
                            </span>
                            <span class="d-inline-block">Participants Management</span>
                        </div>
                    </div>
                </div>
                <div>

                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Filter Participants</h5>
                        <form action="" method="GET">
                            <div class="form-row">
                                <div class="col-md-2 mb-3">
                                    <label for="program_number">Program Number</label>
                                    <input type="text" class="form-control" id="program_number" name="program_number" value="<?= htmlspecialchars($_GET['program_number'] ?? '') ?>">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="country_id">Country</label>
                                    <select class="form-control select2" id="country_id" name="country_id">
                                        <option value="">All Countries</option>
                                        <?php
                                        $countries = $pdo->query("SELECT id, country_name FROM countries ORDER BY country_name")->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($countries as $country): ?>
                                            <option value="<?= $country['id'] ?>" <?= (isset($_GET['country_id']) && $_GET['country_id'] == $country['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($country['country_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="regional_id">Region</label>
                                    <select class="form-control select2" id="regional_id" name="regional_id">
                                        <option value="">All Regions</option>
                                        <?php
                                        $regionals = $pdo->query("SELECT id, regional_name FROM regionals ORDER BY regional_name")->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($regionals as $regional): ?>
                                            <option value="<?= $regional['id'] ?>" <?= (isset($_GET['regional_id']) && $_GET['regional_id'] == $regional['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($regional['regional_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="centre_id">Centre</label>
                                    <select class="form-control select2" id="centre_id" name="centre_id">
                                        <option value="">All Centres</option>
                                        <?php
                                        $centres = $pdo->query("SELECT id, centre_name FROM centres ORDER BY centre_name")->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($centres as $centre): ?>
                                            <option value="<?= $centre['id'] ?>" <?= (isset($_GET['centre_id']) && $_GET['centre_id'] == $centre['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($centre['centre_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="reference_id">Reference ID</label>
                                    <input type="text" class="form-control" id="reference_id" name="reference_id" value="<?= htmlspecialchars($_GET['reference_id'] ?? '') ?>">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="full_name">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($_GET['full_name'] ?? '') ?>">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="mobile">Mobile</label>
                                    <input type="text" class="form-control" id="mobile" name="mobile" value="<?= htmlspecialchars($_GET['mobile'] ?? '') ?>">
                                </div>
                                <div class="col-md-1 mb-3">
                                    <label for="attendance_status">Attendance</label>
                                    <select class="form-control" id="attendance_status" name="attendance_status">
                                        <option value="">All</option>
                                        <option value="registered" <?= ($_GET['attendance_status'] ?? '') == 'registered' ? 'selected' : '' ?>>Registered</option>
                                        <option value="attended" <?= ($_GET['attendance_status'] ?? '') == 'attended' ? 'selected' : '' ?>>Attended</option>
                                        <option value="cancelled" <?= ($_GET['attendance_status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>" class="btn btn-outline-dark mx-3">
                                    <i class="fa fa-window-restore"></i> Reset
                                </a>
                                <button class="btn btn-success" type="submit">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Participants Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">

                    <div class="">
                        <div class="d-flex justify-content-between card-header d-flex justify-content-center mt-3">
                            Participants List
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-info mr-3" id="exportBtn">
                                    <i class="fa fa-download"></i> Export as Excel
                                </button>
                                <?php if ($canadd): ?>
                                <a href="add.php" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add Participant
                                </a>

                            <?php endif; ?>
                            </div>

                            


                        </div>

                    </div>

                    <div class="card-body">




                        <div class="table-responsive">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Reference ID</th>
                                        <th class="text-center">Program</th>
                                        <th class="text-center">Full Name</th>
                                        <th class="text-center">Mobile</th>
                                        <th class="text-center">Place</th>
                                        <th class="text-center">Where did you hear?</th> <!-- New column -->
                                        <th class="text-center">Attendance</th>
                                        <th class="text-center">Registered On</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $counter = $offset + 1;
                                    foreach ($participants as $row): ?>
                                        <tr>
                                            <td class="text-center"><?= $counter ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['reference_id']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['program_number']) ?> - <?= htmlspecialchars($row['title']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['full_name']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['mobile']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['place']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['hear_about_us']) ?></td> <!-- New cell -->
                                            <td class="text-center"><?= ucfirst($row['attendance_status']) ?></td>
                                            <td class="text-center"><?= date('M d, Y H:i', strtotime($row['registration_date'])) ?></td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <a style="cursor:pointer;" class="dropdown-toggle" type="button" id="dropdownMenu<?= $row['id'] ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        •••
                                                    </a>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenu<?= $row['id'] ?>">

                                                        <?php if ($canedit): ?>
                                                            <a class="dropdown-item text-primary" href="#" onclick="editParticipant(<?= $row['id'] ?>)">✏ Edit</a>
                                                        <?php endif; ?>

                                                        <?php if ($candelete): ?>
                                                            <a class="dropdown-item text-danger" href="#" onclick="deleteParticipant(<?= $row['id'] ?>)">🗑 Delete</a>
                                                        <?php endif; ?>
                                                        <a class="dropdown-item text-success" href="#" onclick="remindWhatsapp('<?= htmlspecialchars($row['mobile']) ?>', '<?= htmlspecialchars($row['full_name']) ?>', '<?= htmlspecialchars($row['title']) ?>', '<?= htmlspecialchars($row['program_number']) ?>')">
                                                            <i class="fa fa-whatsapp"></i> Remind via WhatsApp
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php $counter++;
                                    endforeach; ?>
                                    <?php if (empty($participants)): ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-danger">No participants found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            <ul class="pagination pagination-sm">
                                <?php
                                $queryParams = $_GET;
                                unset($queryParams['page']);
                                $queryString = http_build_query($queryParams);

                                // Previous page link
                                if ($page > 1) {
                                    echo '<li class="page-item">
                                        <a href="?page=' . ($page - 1) . (!empty($queryString) ? '&' . $queryString : '') . '" 
                                           class="page-link" aria-label="Previous">
                                            <span aria-hidden="true">«</span>
                                        </a>
                                      </li>';
                                } else {
                                    echo '<li class="page-item disabled"><span class="page-link">«</span></li>';
                                }

                                // Page links
                                for ($i = 1; $i <= $totalPages; $i++) {
                                    $active = $i == $page ? 'active' : '';
                                    echo '<li class="page-item ' . $active . '">
                                        <a href="?page=' . $i . (!empty($queryString) ? '&' . $queryString : '') . '" 
                                           class="page-link">' . $i . '</a>
                                      </li>';
                                }

                                // Next page link
                                if ($page < $totalPages) {
                                    echo '<li class="page-item">
                                        <a href="?page=' . ($page + 1) . (!empty($queryString) ? '&' . $queryString : '') . '" 
                                           class="page-link" aria-label="Next">
                                            <span aria-hidden="true">»</span>
                                        </a>
                                      </li>';
                                } else {
                                    echo '<li class="page-item disabled"><span class="page-link">»</span></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mb-3">
                    <span>Total Participants: <?= $totalRecords ?></span>

                </div>
            </div>

            <style>
                .select2 {
                    width: 100% !important;
                }
            </style>
            <?php include '../Includes/footer.php'; ?>

            <script>
                document.getElementById('exportBtn').addEventListener('click', function() {
                    // Get current filters
                    const params = new URLSearchParams(window.location.search);
                    window.open('export-participants.php?' + params.toString(), '_blank');
                });

                function editParticipant(id) {
                    swal({
                        title: "Edit Participant",
                        text: "Do you want to edit this participant?",
                        icon: "info",
                        buttons: true,
                    }).then((willEdit) => {
                        if (willEdit) {
                            window.location.href = "add.php?id=" + id;
                        }
                    });
                }

                function deleteParticipant(id) {
                    swal({
                        title: "Are you sure?",
                        text: "This participant will be permanently deleted!",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true
                    }).then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "delete.php",
                                type: "POST",
                                data: {
                                    id: id
                                },
                                success: function(response) {
                                    if (response.trim() === "success") {
                                        swal("Deleted!", "The participant has been removed.", "success")
                                            .then(() => location.reload());
                                    } else {
                                        swal("Error!", "Unable to delete the participant.", "error");
                                    }
                                },
                                error: function() {
                                    swal("Error!", "Something went wrong!", "error");
                                }
                            });
                        }
                    });
                }

                function remindWhatsapp(mobile, name, programTitle, programNumber) {
                    let phone = mobile.replace(/\D/g, ''); // Remove non-digits
                    phone = phone.replace(/^(\+)+/, '');   // Remove leading plus if present
                    let message = `Dear ${name},%0A%0AThis is a gentle reminder for your upcoming program:%0AProgram: ${programTitle} (No: ${programNumber})%0A%0APlease be present as scheduled.%0A%0AThank you!`;
                    let url = `https://wa.me/${phone}?text=${message}`;
                    window.open(url, '_blank');
                }

                // Show SweetAlert for success/error messages from URL
                $(document).ready(function() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const message = urlParams.get('message');
                    if (message) {
                        swal({
                            title: "Success",
                            text: message,
                            icon: "success"
                        }).then(() => {
                            const url = new URL(window.location.href);
                            url.searchParams.delete('message');
                            window.history.replaceState({}, document.title, url.toString());
                        });
                    }
                });
            </script>