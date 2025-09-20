<?php
require '../Includes/header.php';
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Program Management');
$showPrice = canUserViewPrice($pdo, 'Program Management'); // <-- use new function
// Check if ID is provided
$candelete = canUsercan_delete($pdo, 'Program Management'); // <-- use new function
$canedit = canUsercan_edit($pdo, 'Program Management');
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}


$programId = intval($_GET['id']);

try {
    // Fetch program details
    $stmt = $pdo->prepare("SELECT 
        programs.*,
        centres.centre_name,
        regionals.regional_name,
        countries.country_name
    FROM programs
    LEFT JOIN centres ON programs.centre_id = centres.id
    LEFT JOIN regionals ON programs.regional_id = regionals.id
    LEFT JOIN countries ON programs.country_id = countries.id
    WHERE programs.id = :id");

    $stmt->bindParam(':id', $programId, PDO::PARAM_INT);
    $stmt->execute();

    $program = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$program) {
        header("Location: index.php?message=Program+not+found");
        exit();
    }

    // Status badge
    $isActive = strtolower(trim($program['status'])) === 'active';
    $statusBadge = $isActive ? 'badge-success' : 'badge-danger';

    // Fetch all marketing details
    $marketing = [
        'pamphlets'   => [],
        'radio'       => [],
        'television'  => [],
        'interview'   => null,
        'newspaper'   => [],
        'billboard'   => [],
        'facebook'    => [],
        'instagram'   => [],
        'other'       => null,
        'account'     => null,
    ];

    $stmt = $pdo->prepare("SELECT * FROM program_pamphlets WHERE program_id = ?");
    $stmt->execute([$programId]);
    $marketing['pamphlets'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM radio_advertisements WHERE program_id = ?");
    $stmt->execute([$programId]);
    $marketing['radio'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM television_advertisements WHERE program_id = ?");
    $stmt->execute([$programId]);
    $marketing['television'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM interview_details WHERE program_id = ?");
    $stmt->execute([$programId]);
    $marketing['interview'] = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM newspaper_advertisements WHERE program_id = ?");
    $stmt->execute([$programId]);
    $marketing['newspaper'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM billboard_advertisements WHERE program_id = ?");
    $stmt->execute([$programId]);
    $marketing['billboard'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM facebook_advertisements WHERE program_id = ?");
    $stmt->execute([$programId]);
    $marketing['facebook'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM instagram_advertisements WHERE program_id = ?");
    $stmt->execute([$programId]);
    $marketing['instagram'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM other_marketing_details WHERE program_id = ?");
    $stmt->execute([$programId]);
    $marketing['other'] = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM program_bank_accounts WHERE program_id = ?");
    $stmt->execute([$programId]);
    $marketing['account'] = $stmt->fetch(PDO::FETCH_ASSOC);

    $sessionStmt = $pdo->prepare("SELECT 
        MIN(session_start) AS session_start_min, 
        MAX(session_end) AS session_end_max 
        FROM program_sessions_times WHERE program_id = ?");
    $sessionStmt->execute([$programId]);
    $sessionTimes = $sessionStmt->fetch(PDO::FETCH_ASSOC);

    $startDateTime = $sessionTimes['session_start_min'] ? new DateTime($sessionTimes['session_start_min']) : null;
    $endDateTime = $sessionTimes['session_end_max'] ? new DateTime($sessionTimes['session_end_max']) : null;

    // All Session Times
    $allSessionsStmt = $pdo->prepare("SELECT session_name, session_start, session_end FROM program_sessions_times WHERE program_id = ? ORDER BY session_start ASC");
    $allSessionsStmt->execute([$programId]);
    $allSessions = $allSessionsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch actual participant count for this program
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM participants WHERE program_id = ?");
    $stmt->execute([$programId]);
    $actualParticipantCount = $stmt->fetchColumn();
} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}
?>
<?php
$totalMarketingExpense = 0;

// Billboard Advertisements
$stmt = $pdo->prepare("SELECT SUM(cost) FROM billboard_advertisements WHERE program_id = ?");
$stmt->execute([$programId]);
$totalMarketingExpense += floatval($stmt->fetchColumn());

// Facebook Advertisements
$stmt = $pdo->prepare("SELECT SUM(cost) FROM facebook_advertisements WHERE program_id = ?");
$stmt->execute([$programId]);
$totalMarketingExpense += floatval($stmt->fetchColumn());

// Instagram Advertisements
$stmt = $pdo->prepare("SELECT SUM(cost) FROM instagram_advertisements WHERE program_id = ?");
$stmt->execute([$programId]);
$totalMarketingExpense += floatval($stmt->fetchColumn());

// Newspaper Advertisements
$stmt = $pdo->prepare("SELECT SUM(cost) FROM newspaper_advertisements WHERE program_id = ?");
$stmt->execute([$programId]);
$totalMarketingExpense += floatval($stmt->fetchColumn());

// Radio Advertisements
$stmt = $pdo->prepare("SELECT SUM(cost) FROM radio_advertisements WHERE program_id = ?");
$stmt->execute([$programId]);
$totalMarketingExpense += floatval($stmt->fetchColumn());

// Television Advertisements
$stmt = $pdo->prepare("SELECT SUM(cost) FROM television_advertisements WHERE program_id = ?");
$stmt->execute([$programId]);
$totalMarketingExpense += floatval($stmt->fetchColumn());

// Pamphlet Details (designer, printing, distribution costs)
$stmt = $pdo->prepare("SELECT SUM(pamphlet_designer_cost) + SUM(pamphlet_printing_cost) + SUM(pamphlet_distribution_cost) FROM program_pamphlets WHERE program_id = ?");
$stmt->execute([$programId]);
$totalMarketingExpense += floatval($stmt->fetchColumn());

// Other Marketing Details (all cost fields)
$stmt = $pdo->prepare("
    SELECT 
        COALESCE(SUM(literature_cost),0) +
        COALESCE(SUM(marketing_material_cost),0) +
        COALESCE(SUM(other_essential_cost),0) +
        COALESCE(SUM(logistic_cost),0) +
        COALESCE(SUM(marketing_agency_cost),0) +
        COALESCE(SUM(accommodation_cost),0)
    FROM other_marketing_details
    WHERE program_id = ?
");
$stmt->execute([$programId]);
$totalMarketingExpense += floatval($stmt->fetchColumn());
?>

<div class="app-main__outer">
    <div class="app-main__inner">
        <div class="app-page-title app-page-title-simple">
            <div class="page-title-wrapper d-flex justify-content-between">
                <div class="page-title-heading">
                    <div>
                        <div class="page-title-head center-elem mb-3">
                            <span class="d-inline-block">
                                <i class="lnr-calendar-full opacity-6"></i>
                            </span>
                            <span class="d-inline-block">Program Details</span>
                        </div>
                    </div>
                </div>
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Program Management</a></li>
                        <li class="breadcrumb-item active">View Program</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0"><?= htmlspecialchars($program['title']) ?></h5>
                            <small class="text-muted">Program Number: <strong><?= htmlspecialchars($program['program_number']) ?></strong></small>
                        </div>
                        <div>
                            <!-- Completion Status Badge -->

                            <?php
                            $cs = strtolower($program['completion_status']);
                            if ($cs === 'completed') {
                                $csBadge = 'badge-success';
                                $csText = 'Completed';
                            } elseif ($cs === 'in_progress') {
                                $csBadge = 'badge-warning';
                                $csText = 'In Progress';
                            } else {
                                $csBadge = 'badge-secondary';
                                $csText = 'Not Started';
                            }
                            ?>
                            <span class="badge <?= $csBadge ?>"><?= $csText ?></span>

                            <?php
                            $statusLower = strtolower(trim($program['status']));
                            if ($statusLower === 'activated') {
                                $statusBadge = 'badge-success';
                                $statusText = 'Activated';
                            } elseif ($statusLower === 'pending') {
                                $statusBadge = 'badge-warning';
                                $statusText = 'Entry is Pending';
                            } else {
                                $statusBadge = 'badge-danger';
                                $statusText = ucfirst($program['status']);
                            }
                            ?>
                            <span class="badge <?= $statusBadge ?>"><?= $statusText ?></span>
                            <?php if ($canedit): ?>
                                <a href="edit.php?id=<?= $program['id'] ?>" class="btn btn-primary btn-sm ml-2">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            <?php endif; ?>
                             <a href="add-program-marketing.php?id=<?= $program['id'] ?>" class="btn btn-primary btn-sm ml-2">
                                <i class="fa fa-edit"></i> Update Marketing Details
                            </a>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Program Image -->
                            <div class="col-md-4 mb-4">
                                <div class="program-image-container">
                                    <?php if (!empty($program['program_img'])): ?>
                                        <img src="../../uploads/programs/<?= htmlspecialchars($program['program_img']) ?>"
                                            alt="Program Image" class="img-fluid rounded">
                                    <?php else: ?>
                                        <div class="no-image-placeholder d-flex align-items-center justify-content-center">
                                            <i class="fa fa-image fa-5x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Program Details -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item mb-3">
                                            <h6 class="font-weight-bold" class="detail-label">Centre:</h6>
                                            <p class="detail-value">
                                                <?= htmlspecialchars($program['centre_name'] ?? 'N/A') ?>
                                            </p>
                                        </div>

                                        <div class="detail-item mb-3">
                                            <h6 class="font-weight-bold" class="detail-label">Location:</h6>
                                            <p class="detail-value">
                                                <?= htmlspecialchars($program['venue']) ?><br>
                                                <?= htmlspecialchars($program['regional_name'] ?? '') ?>,
                                                <?= htmlspecialchars($program['country_name'] ?? '') ?>
                                            </p>
                                        </div>

                                        <div class="detail-item mb-3">
                                            <h6 class="font-weight-bold" class="detail-label">Schedule:</h6>
                                            <p class="detail-value">
                                                <?php if ($startDateTime && $endDateTime): ?>
                                                    <strong>Start:</strong> <?= $startDateTime->format('M d, Y - h:i A') ?><br>
                                                    <strong>End:</strong> <?= $endDateTime->format('M d, Y - h:i A') ?>
                                                <?php else: ?>
                                                    <span>No session schedule available.</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="detail-item mb-3">
                                            <h6 class="font-weight-bold" class="detail-label">Instructor:</h6>
                                            <p class="detail-value"><?= htmlspecialchars($program['instructor_name']) ?></p>
                                        </div>

                                        <div class="detail-item mb-3">
                                            <h6 class="font-weight-bold" class="detail-label">Participants:</h6>
                                            <p class="detail-value">
                                                <?= $actualParticipantCount ?> /
                                                <?= $program['max_participants'] ?>
                                            </p>
                                            <small class="text-muted">
                                                Program Generated: <?= date('M d, Y', strtotime($program['created_at'])) ?>
                                            </small>
                                        </div>


                                    </div>
                                </div>

                                <!-- Marketing Methods -->
                                <?php if (!empty($program['marketing_methods'])): ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="detail-item mb-3">
                                                <h6 class="font-weight-bold" class="detail-label">Marketing Methods:</h6>
                                                <p class="detail-value">
                                                    <?= htmlspecialchars($program['marketing_methods']) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Description -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="detail-item">
                                            <h6 class="font-weight-bold" class="detail-label">Description:</h6>
                                            <div class="detail-value">
                                                <?= nl2br(htmlspecialchars($program['description'] ?? 'No description provided')) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Session Details -->
                        <?php if (!empty($allSessions)): ?>
                            <div class="detail-item mb-3">
                                <h6 class="font-weight-bold detail-label">All Sessions:</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>Session Name</th>
                                                <th>Start Date & Time</th>
                                                <th>End Date & Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($allSessions as $sess): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($sess['session_name']) ?></td>
                                                    <td><?= date('M d, Y - h:i A', strtotime($sess['session_start'])) ?></td>
                                                    <td><?= date('M d, Y - h:i A', strtotime($sess['session_end'])) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- MARKETING DETAILS SECTION -->
                        <hr>
                        <div class="mt-4">
                            <h4 class="mb-3">Marketing Details</h4>

                            <!-- Pamphlet Details -->
                            <?php if (!empty($marketing['pamphlets'])): ?>
                                <h6 class="font-weight-bold mt-4 mb-2">Pamphlet Details</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>Designer</th>
                                                <?php if ($showPrice): ?><th>Designer Cost</th><?php endif; ?>
                                                <th>Printer</th>
                                                <?php if ($showPrice): ?><th>Printing Cost</th><?php endif; ?>
                                                <th>Distributor</th>
                                                <?php if ($showPrice): ?><th>Distribution Cost</th><?php endif; ?>
                                                <th>Designer Invoice</th>
                                                <th>Printing Invoice</th>
                                                <th>Distribution Invoice</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($marketing['pamphlets'] as $pamphlet): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($pamphlet['pamphlet_designer_name']) ?></td>
                                                    <?php if ($showPrice): ?><td><?= htmlspecialchars($pamphlet['pamphlet_designer_cost']) ?></td><?php endif; ?>
                                                    <td><?= htmlspecialchars($pamphlet['pamphlet_printer_name']) ?></td>
                                                    <?php if ($showPrice): ?><td><?= htmlspecialchars($pamphlet['pamphlet_printing_cost']) ?></td><?php endif; ?>
                                                    <td><?= htmlspecialchars($pamphlet['pamphlet_distributor_name']) ?></td>
                                                    <?php if ($showPrice): ?><td><?= htmlspecialchars($pamphlet['pamphlet_distribution_cost']) ?></td><?php endif; ?>
                                                    <td>
                                                        <?php if (!empty($pamphlet['pamphlet_designer_invoice'])): ?>
                                                            <a href="../../uploads/pamphlets/<?= htmlspecialchars($pamphlet['pamphlet_designer_invoice']) ?>" target="_blank" class="btn btn-sm btn-info">View Invoice</a>
                                                        <?php else: ?>
                                                            <span class="text-muted">No Invoice</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($pamphlet['pamphlet_printing_invoice'])): ?>
                                                            <a href="../../uploads/pamphlets/<?= htmlspecialchars($pamphlet['pamphlet_printing_invoice']) ?>" target="_blank" class="btn btn-sm btn-info">View Invoice</a>
                                                        <?php else: ?>
                                                            <span class="text-muted">No Invoice</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($pamphlet['pamphlet_distribution_invoice'])): ?>
                                                            <a href="../../uploads/pamphlets/<?= htmlspecialchars($pamphlet['pamphlet_distribution_invoice']) ?>" target="_blank" class="btn btn-sm btn-info">View Invoice</a>
                                                        <?php else: ?>
                                                            <span class="text-muted">No Invoice</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <!-- Radio Advertisements -->
                            <?php if (!empty($marketing['radio'])): ?>
                                <h6 class="font-weight-bold mt-4 mb-2">Radio Advertisements</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <?php if ($showPrice): ?><th>Cost</th><?php endif; ?>
                                                <th>Contact</th>
                                                <th>Remarks</th>
                                                <th>Invoice</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($marketing['radio'] as $radio): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($radio['name']) ?></td>
                                                    <?php if ($showPrice): ?><td><?= htmlspecialchars($radio['cost']) ?></td><?php endif; ?>
                                                    <td><?= htmlspecialchars($radio['contact']) ?></td>
                                                    <td><?= htmlspecialchars($radio['remarks']) ?></td>
                                                    <td>
                                                        <?php if (!empty($radio['invoice_file'])): ?>
                                                            <a href="../../uploads/radio_invoices/<?= htmlspecialchars($radio['invoice_file']) ?>" target="_blank" class="btn btn-sm btn-info">View Invoice</a>
                                                        <?php else: ?>
                                                            <span class="text-muted">No Invoice</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>



                            <!-- Television Advertisements -->
                            <?php if (!empty($marketing['television'])): ?>
                                <h6 class="font-weight-bold mt-4 mb-2">Television Advertisements</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <?php if ($showPrice): ?><th>Cost</th><?php endif; ?>
                                                <th>Contact</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($marketing['television'] as $tv): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($tv['name']) ?></td>
                                                    <?php if ($showPrice): ?><td><?= htmlspecialchars($tv['cost']) ?></td><?php endif; ?>
                                                    <td><?= htmlspecialchars($tv['contact']) ?></td>
                                                    <td><?= htmlspecialchars($tv['remarks']) ?></td>
                                                    <td>
                                                        <?php if (!empty($tv['invoice_file'])): ?>
                                                            <a href="../../uploads/television_invoices/<?= htmlspecialchars($tv['invoice_file']) ?>" target="_blank" class="btn btn-sm btn-info">View Invoice</a>
                                                        <?php else: ?>
                                                            <span class="text-muted">No Invoice</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <!-- Interview Details -->
                            <?php if (!empty($marketing['interview']['details'])): ?>
                                <h6 class="font-weight-bold mt-4 mb-2">Interview Details</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <tbody>
                                            <tr>
                                                <td><?= nl2br(htmlspecialchars($marketing['interview']['details'])) ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <!-- Newspaper Advertisements -->
                            <?php if (!empty($marketing['newspaper'])): ?>
                                <h6 class="font-weight-bold mt-4 mb-2">Newspaper Advertisements</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <?php if ($showPrice): ?><th>Cost</th><?php endif; ?>
                                                <th>Duration</th>
                                                <th>Ad Size</th>
                                                <th>Contact</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($marketing['newspaper'] as $news): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($news['name']) ?></td>
                                                    <?php if ($showPrice): ?><td><?= htmlspecialchars($news['cost']) ?></td><?php endif; ?>
                                                    <td><?= htmlspecialchars($news['duration']) ?></td>
                                                    <td><?= htmlspecialchars($news['ad_size']) ?></td>
                                                    <td><?= htmlspecialchars($news['contact']) ?></td>
                                                    <td><?= htmlspecialchars($news['remarks']) ?></td>
                                                    <td>
                                                        <?php if (!empty($news['invoice_file'])): ?>
                                                            <a href="../../uploads/newspaper_invoices/<?= htmlspecialchars($news['invoice_file']) ?>" target="_blank" class="btn btn-sm btn-info">View Invoice</a>
                                                        <?php else: ?>
                                                            <span class="text-muted">No Invoice</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <!-- Billboard Advertisements -->
                            <?php if (!empty($marketing['billboard'])): ?>
                                <h6 class="font-weight-bold mt-4 mb-2">Billboard Advertisements</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>Agency Name</th>
                                                <?php if ($showPrice): ?><th>Cost</th><?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($marketing['billboard'] as $bill): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($bill['agency_name']) ?></td>
                                                    <?php if ($showPrice): ?><td><?= htmlspecialchars($bill['cost']) ?></td><?php endif; ?>
                                                    <td>
                                                        <?php if (!empty($bill['invoice_file'])): ?>
                                                            <a href="../../uploads/billboard_invoices/<?= htmlspecialchars($bill['invoice_file']) ?>" target="_blank" class="btn btn-sm btn-info">View Invoice</a>
                                                        <?php else: ?>
                                                            <span class="text-muted">No Invoice</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <!-- Facebook Advertisements -->
                            <?php if (!empty($marketing['facebook'])): ?>
                                <h6 class="font-weight-bold mt-4 mb-2">Facebook Advertisements</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <?php if ($showPrice): ?><th>Cost</th><?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($marketing['facebook'] as $fb): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($fb['name']) ?></td>
                                                    <?php if ($showPrice): ?><td><?= htmlspecialchars($fb['cost']) ?></td><?php endif; ?>
                                                    <td>
                                                        <?php if (!empty($fb['invoice_file'])): ?>
                                                            <a href="../../uploads/facebook_invoices/<?= htmlspecialchars($fb['invoice_file']) ?>" target="_blank" class="btn btn-sm btn-info">View Invoice</a>
                                                        <?php else: ?>
                                                            <span class="text-muted">No Invoice</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <!-- Instagram Advertisements -->

                            <?php if (!empty($marketing['instagram'])): ?>
                                <h6 class="font-weight-bold mt-4 mb-2">Instagram Advertisements</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <?php if ($showPrice): ?><th>Cost</th><?php endif; ?>
                                                <th>Invoice</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($marketing['instagram'] as $insta): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($insta['name']) ?></td>
                                                    <?php if ($showPrice): ?><td><?= htmlspecialchars($insta['cost']) ?></td><?php endif; ?>
                                                    <td>
                                                        <?php if (!empty($insta['invoice_file'])): ?>
                                                            <a href="../../uploads/instagram_invoices/<?= htmlspecialchars($insta['invoice_file']) ?>" target="_blank" class="btn btn-sm btn-info">View Invoice</a>
                                                        <?php else: ?>
                                                            <span class="text-muted">No Invoice</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <!-- Other Marketing Details -->
                            <?php
                            $other = $marketing['other'];
                            $hasOther =
                                !empty($other['literature_by']) ||
                                !empty($other['literature_cost']) ||
                                !empty($other['other_marketing_material']) ||
                                !empty($other['marketing_material_cost']) ||
                                !empty($other['other_essential']) ||
                                !empty($other['other_essential_cost']) ||
                                !empty($other['logistic']) ||
                                !empty($other['logistic_cost']) ||
                                !empty($other['marketing_agency']) ||
                                !empty($other['marketing_agency_cost']) ||
                                !empty($other['accommodation']) ||
                                !empty($other['accommodation_cost']);
                            ?>
                            <?php if ($hasOther): ?>
                                <h6 class="font-weight-bold mt-4 mb-2">Other Marketing Details</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Value</th>
                                                <?php if ($showPrice): ?><th>Cost</th><?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($other['literature_by']) || !empty($other['literature_cost'])): ?>
                                                <tr>
                                                    <td>Literature By</td>
                                                    <td><?= htmlspecialchars($other['literature_by']) ?></td>
                                                    <?php if ($showPrice): ?><td><?= htmlspecialchars($other['literature_cost']) ?></td><?php endif; ?>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if (!empty($other['other_marketing_material']) || !empty($other['marketing_material_cost'])): ?>
                                                <tr>
                                                    <td>Other Marketing Material</td>
                                                    <td><?= htmlspecialchars($other['other_marketing_material']) ?></td>
                                                    <?php if ($showPrice): ?><td><?= htmlspecialchars($other['marketing_material_cost']) ?></td><?php endif; ?>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if (!empty($other['other_essential']) || !empty($other['other_essential_cost'])): ?>
                                                <tr>
                                                    <td>Other Essentials</td>
                                                    <td><?= htmlspecialchars($other['other_essential']) ?></td>
                                                    <?php if ($showPrice): ?><td><?= htmlspecialchars($other['other_essential_cost']) ?></td><?php endif; ?>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if (!empty($other['logistic']) || !empty($other['logistic_cost'])): ?>
                                                <tr>
                                                    <td>Logistic</td>
                                                    <td><?= htmlspecialchars($other['logistic']) ?></td>
                                                    <?php if ($showPrice): ?><td><?= htmlspecialchars($other['logistic_cost']) ?></td><?php endif; ?>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if (!empty($other['marketing_agency']) || !empty($other['marketing_agency_cost'])): ?>
                                                <tr>
                                                    <td>Marketing Agency</td>
                                                    <td><?= htmlspecialchars($other['marketing_agency']) ?></td>
                                                    <?php if ($showPrice): ?><td><?= htmlspecialchars($other['marketing_agency_cost']) ?></td><?php endif; ?>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if (!empty($other['accommodation']) || !empty($other['accommodation_cost'])): ?>
                                                <tr>
                                                    <td>Accommodation</td>
                                                    <td><?= htmlspecialchars($other['accommodation']) ?></td>
                                                    <?php if ($showPrice): ?><td><?= htmlspecialchars($other['accommodation_cost']) ?></td><?php endif; ?>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <?php if (
                                !empty($marketing['account']) &&
                                (!empty($marketing['account']['account_holder_name']) ||
                                    !empty($marketing['account']['bank_name']) ||
                                    !empty($marketing['account']['account_number']) ||
                                    !empty($marketing['account']['ifsc_code']) ||
                                    !empty($marketing['account']['branch']) ||
                                    !empty($marketing['account']['upi_id']))
                            ): ?>
                                <!-- Account Information -->
                                <?php if ($showPrice): ?>
                                    <div class="alert alert-info mb-3">
                                        <strong>Total Marketing Cost:</strong>
                                        ₹<?= number_format($totalMarketingExpense, 2) ?>
                                    </div>
                                <?php endif; ?>
                                <h6 class="font-weight-bold mt-4 mb-2" class="mt-3">Account Information</h6>
                                <ul>
                                    <?php if (!empty($marketing['account']['account_holder_name'])): ?>
                                        <li>Account Holder: <?= htmlspecialchars($marketing['account']['account_holder_name']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($marketing['account']['bank_name'])): ?>
                                        <li>Bank Name: <?= htmlspecialchars($marketing['account']['bank_name']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($marketing['account']['account_number'])): ?>
                                        <li>Account Number: <?= htmlspecialchars($marketing['account']['account_number']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($marketing['account']['ifsc_code'])): ?>
                                        <li>IFSC Code: <?= htmlspecialchars($marketing['account']['ifsc_code']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($marketing['account']['branch'])): ?>
                                        <li>Branch: <?= htmlspecialchars($marketing['account']['branch']) ?></li>
                                    <?php endif; ?>
                                    <?php if (!empty($marketing['account']['upi_id'])): ?>
                                        <li>UPI ID: <?= htmlspecialchars($marketing['account']['upi_id']) ?></li>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <!-- END MARKETING DETAILS SECTION -->
                    </div>
                    <?php // Fetch associated media
                    $mediaStmt = $pdo->prepare("SELECT * FROM program_media WHERE program_id = :pid ORDER BY uploaded_at DESC");
                    $mediaStmt->bindParam(':pid', $programId, PDO::PARAM_INT);
                    $mediaStmt->execute();
                    $programMedia = $mediaStmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <!-- GLightbox CSS -->
                    <link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet" />
                    <!-- GLightbox JS -->
                    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
                    <script>
                        const lightbox = GLightbox({
                            selector: '.glightbox'
                        });
                    </script>




                    <style>
                        .media-preview video,
                        .media-preview img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        }

                        .media-preview video {
                            max-width: 100%;
                            max-height: 100%;
                            object-fit: cover;
                        }

                        .media-preview img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        }

                        .play-overlay {
                            pointer-events: none;
                        }
                    </style>

                    <div class="card-footer d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Programs
                        </a>
                        <div>
                            <?php if ($candelete): ?>
                                <button onclick="deleteProgram(<?= $program['id'] ?>)" class="btn btn-danger">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-info" data-toggle="modal" data-target="#uploadMediaModal">
                                <i class="fa fa-upload"></i> Upload Media
                            </button>
                           
                            
                            <a href="Print/index.php?id=<?= $programId ?>" class="btn btn-info btn-sm">
                                <i class="fa fa-print"></i> Print Details
                            </a>
                            <!-- Removed Sponsor Now button and modal -->
                        </div>
                    </div>

                    <?php if (!empty($programMedia)): ?>
                        <hr>
                        <div class="card-body">
                            <h5 class="mb-3">Media Gallery</h5>
                            <div class="row">
                                <?php foreach ($programMedia as $media): ?>
                                    <?php
                                    $filePath = '../../uploads/programs/' . htmlspecialchars($media['file_path']);
                                    $ext = strtolower(pathinfo($media['file_path'], PATHINFO_EXTENSION));
                                    $isVideo = in_array($ext, ['mp4', 'mov']);
                                    ?>
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                        <div class="card  shadow-sm border-0">
                                            <div class="media-preview position-relative"
                                                style="height: 180px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                <?php if ($isVideo): ?>
                                                    <a href="<?= $filePath ?>" target="_blank" rel="noopener noreferrer">
                                                        <video src="<?= $filePath ?>" muted
                                                            style="max-height: 100%; width: auto; object-fit: cover;"></video>
                                                        <div class="play-overlay position-absolute w-100 h-100 d-flex justify-content-center align-items-center"
                                                            style="top:0;left:0; background: rgba(0,0,0,0.4);">
                                                            <i class="fa fa-play-circle text-white" style="font-size: 2rem;"></i>
                                                        </div>
                                                    </a>

                                                <?php else: ?>
                                                    <a target="_blank" href="<?= $filePath ?>" class="glightbox"
                                                        data-title="Program Image">
                                                        <img src="<?= $filePath ?>" alt="Program Media"
                                                            class="img-fluid w-100 h-100" style="object-fit: cover;">
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-body p-2 text-center">
                                                <small class="text-muted">Uploaded:
                                                    <?= date("M d, Y", strtotime($media['uploaded_at'])) ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<!-- Media Viewer Modal -->
<div class="modal fade" id="mediaModal" tabindex="-1" aria-labelledby="mediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-body p-0">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                    data-bs-dismiss="modal" aria-label="Close"></button>
                <div id="mediaContent" class="w-100 text-center" style="max-height: 90vh; overflow: hidden;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Media Modal -->
<div class="modal fade" id="uploadMediaModal" tabindex="-1" role="dialog" aria-labelledby="uploadMediaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="mediaUploadForm" enctype="multipart/form-data" method="POST" action="upload_media.php">
            <input type="hidden" name="program_id" value="<?= $programId ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadMediaModalLabel">Upload Program Media</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="mediaFiles">Select Photos or Videos</label>
                        <input type="file" class="form-control-file" name="media_files[]" id="mediaFiles" multiple
                            accept="image/*,video/*">
                        <small class="form-text text-muted">Supported formats: JPG, PNG, MP4, MOV</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>
<style>
    .program-image-container {
        height: 250px;
        background-color: #f8f9fa;
        border-radius: 5px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .program-image-container img {
        max-height: 100%;
        width: auto;
        object-fit: cover;
    }

    .no-image-placeholder {
        height: 100%;
        width: 100%;
        background-color: #e9ecef;
    }

    .detail-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.25rem;
    }

    .detail-value {
        color: #212529;
        padding-left: 1rem;
    }
</style>

<script>
    function deleteProgram(id) {
        swal({
            title: "Are you sure?",
            text: "This Program will be permanently deleted!",
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
                        response = response.trim();
                        if (response === "success") {
                            swal("Deleted!", "The Program has been removed.", "success")
                                .then(() => window.location.href = "index.php");
                        } else if (response.startsWith("error:")) {
                            swal("Error!", response.substring(6), "error");
                        } else {
                            swal("Error!", "Unknown response from server", "error");
                        }
                    },
                    error: function(xhr, status, error) {
                        swal("Error!", "AJAX Error: " + error, "error");
                    }
                });
            }
        });
    }

    function blockProgram(id, currentStatus) {
        let newStatus = (currentStatus.toLowerCase() === "active") ? "Blocked" : "Active";
        let actionText = (newStatus === "Blocked") ? "block" : "unblock";

        swal({
            title: "Confirm",
            text: "Are you sure you want to " + actionText + " this Program?",
            icon: "warning",
            buttons: true,
            dangerMode: true
        }).then((confirm) => {
            if (confirm) {
                $.ajax({
                    url: "block.php",
                    type: "POST",
                    data: {
                        id: id,
                        status: newStatus
                    },
                    success: function(response) {
                        response = response.trim();
                        if (response === "success") {
                            swal("Updated!", "Program has been " + actionText + "ed.", "success")
                                .then(() => location.reload());
                        } else if (response.startsWith("error:")) {
                            swal("Error!", response.substring(6), "error");
                        } else {
                            swal("Error!", "Unknown response from server", "error");
                        }
                    },
                    error: function(xhr, status, error) {
                        swal("Error!", "AJAX Error: " + error, "error");
                    }
                });
            }
        });
    }
</script>
<script>
    // Fix for the JavaScript handling
    $(document).ready(function() {
    $('#mediaUploadForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            // Show loading indicator
            let submitBtn = $(this).find('button[type="submit"]');
            let originalText = submitBtn.html();
            submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
            submitBtn.prop('disabled', true);

            // Reset button
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);

            if (response.trim() === 'success') {
                swal({
                    title: "Success!",
                    text: "Media uploaded successfully!",
                    icon: "success"
                }).then(() => {
                    // Reload page to show new media
                    location.reload();
                });
                $('#uploadMediaModal').modal('hide');
            } else if (response.startsWith("error:")) {
                swal("Error", response.substring(6), "error");
            } else {
                swal("Unexpected Response", response, "warning");
            }
        },
        error: function(xhr, status, error) {
            // Reset button
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);

            swal("AJAX Error", error, "error");
        }
    });
    });
    });
</script>



<?php include '../Includes/footer.php'; ?>