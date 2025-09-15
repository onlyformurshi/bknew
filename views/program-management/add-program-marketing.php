<?php
include '../Includes/header.php';
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Program Management');
$showPrice = canUserViewPrice($pdo, 'Program Management'); // <-- use new function

$program_id = intval($_GET['id'] ?? 0);
$program = null;
$marketing_data = [];

if ($program_id > 0) {
    try {
        // Fetch program details
        $stmt = $pdo->prepare("SELECT id, program_number, title FROM programs WHERE id = ?");
        $stmt->execute([$program_id]);
        $program = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$program) {
            header("Location: index.php?message=" . urlencode("Program not found."));
            exit;
        }

        // Fetch pamphlet details
        $stmt = $pdo->prepare("SELECT * FROM program_pamphlets WHERE program_id = ?");
        $stmt->execute([$program_id]);
        $marketing_data['pamphlets'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch radio advertisements
        $stmt = $pdo->prepare("SELECT * FROM radio_advertisements WHERE program_id = ?");
        $stmt->execute([$program_id]);
        $marketing_data['radio'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch television advertisements
        $stmt = $pdo->prepare("SELECT * FROM television_advertisements WHERE program_id = ?");
        $stmt->execute([$program_id]);
        $marketing_data['television'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch interview details
        $stmt = $pdo->prepare("SELECT * FROM interview_details WHERE program_id = ?");
        $stmt->execute([$program_id]);
        $marketing_data['interview'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch newspaper advertisements
        $stmt = $pdo->prepare("SELECT * FROM newspaper_advertisements WHERE program_id = ?");
        $stmt->execute([$program_id]);
        $marketing_data['newspaper'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch billboard advertisements
        $stmt = $pdo->prepare("SELECT * FROM billboard_advertisements WHERE program_id = ?");
        $stmt->execute([$program_id]);
        $marketing_data['billboard'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch facebook advertisements
        $stmt = $pdo->prepare("SELECT * FROM facebook_advertisements WHERE program_id = ?");
        $stmt->execute([$program_id]);
        $marketing_data['facebook'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch instagram advertisements
        $stmt = $pdo->prepare("SELECT * FROM instagram_advertisements WHERE program_id = ?");
        $stmt->execute([$program_id]);
        $marketing_data['instagram'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch other marketing details
        $stmt = $pdo->prepare("SELECT * FROM other_marketing_details WHERE program_id = ?");
        $stmt->execute([$program_id]);
        $marketing_data['other'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch bank account details
        $stmt = $pdo->prepare("SELECT * FROM program_bank_accounts WHERE program_id = ?");
        $stmt->execute([$program_id]);
        $marketing_data['account'] = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching program marketing data: " . $e->getMessage();
    }
} else {
    header("Location: index.php?message=" . urlencode("Invalid program ID."));
    exit;
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
                    <span class="d-inline-block">Edit Program Marketing Details</span>
                </div>
                <div class="page-title-heading"></div>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Program Management</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Marketing</li>
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

                    <form action="update-marketing.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="program_id" value="<?= $program['id'] ?>">
                        <div class="form-group">
                            <label>Selected Program:</label>
                            <div class="form-control-plaintext font-weight-bold">
                                <?= htmlspecialchars($program['program_number']) ?> - <?= htmlspecialchars($program['title']) ?>
                            </div>
                        </div>

                        <!-- Pamphlet Details Section -->
                        <div class="main-card mb-3 card">
                            <div class="card-body">
                                <h5 class="card-title">Pamphlet Details</h5>
                                <div id="pamphlet-container">
                                    <?php if (!empty($marketing_data['pamphlets'])): ?>
                                        <?php foreach ($marketing_data['pamphlets'] as $index => $pamphlet): ?>
                                            <div class="pamphlet-row row border rounded p-2 mb-2 position-relative">
                                                <div class="col-md-4 col-6 mb-3">
                                                    <label>Pamphlet Designer Name</label>
                                                    <input type="text" class="form-control" name="pamphlet_designer_name[]"
                                                        value="<?= htmlspecialchars($pamphlet['pamphlet_designer_name']) ?>">
                                                </div>
                                                <?php if ($showPrice): ?>
                                                    <div class="col-md-4 col-6 mb-3">
                                                        <label>Design Cost</label>
                                                        <input type="number" class="form-control" name="pamphlet_designer_cost[]"
                                                            value="<?= htmlspecialchars($pamphlet['pamphlet_designer_cost']) ?>">
                                                    </div>
                                                <?php endif; ?>

                                                <div class="col-md-4 col-6 mb-3">
                                                    <label>Designer Invoice</label>
                                                    <input type="file" class="form-control" name="pamphlet_designer_invoice[]">
                                                    <?php if (!empty($pamphlet['pamphlet_designer_invoice'])): ?>
                                                        <span>
                                                            <a href="../../uploads/pamphlets/<?php echo htmlspecialchars($pamphlet['pamphlet_designer_invoice']); ?>" target="_blank">
                                                                <?php echo htmlspecialchars($pamphlet['pamphlet_designer_invoice']); ?>
                                                            </a>
                                                            <a href="../../uploads/pamphlets/<?php echo htmlspecialchars($pamphlet['pamphlet_designer_invoice']); ?>" target="_blank" title="View Invoice">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                        </span>
                                                    <?php endif; ?>
                                                    <input type="hidden" name="existing_designer_invoice[]" value="<?php echo htmlspecialchars($pamphlet['pamphlet_designer_invoice']); ?>">
                                                </div>
                                                <div class="col-md-4 col-6 mb-3">
                                                    <label>Pamphlet Printer Name</label>
                                                    <input type="text" class="form-control" name="pamphlet_printer_name[]"
                                                        value="<?= htmlspecialchars($pamphlet['pamphlet_printer_name']) ?>">
                                                </div>
                                                <?php if ($showPrice): ?><div class="col-md-4 col-6 mb-3">
                                                        <label>Printing Cost</label>
                                                        <input type="number" class="form-control" name="pamphlet_printing_cost[]"
                                                            value="<?= htmlspecialchars($pamphlet['pamphlet_printing_cost']) ?>">
                                                    </div><?php endif; ?>


                                                <div class="col-md-4 col-6 mb-3">
                                                    <label>Printing Invoice</label>
                                                    <input type="file" class="form-control" name="pamphlet_printing_invoice[]">
                                                    <?php if (!empty($pamphlet['pamphlet_printing_invoice'])): ?>
                                                        <span>
                                                            <a href="../../uploads/pamphlets/<?php echo htmlspecialchars($pamphlet['pamphlet_printing_invoice']); ?>" target="_blank">
                                                                <?php echo htmlspecialchars($pamphlet['pamphlet_printing_invoice']); ?>
                                                            </a>
                                                            <a href="../../uploads/pamphlets/<?php echo htmlspecialchars($pamphlet['pamphlet_printing_invoice']); ?>" target="_blank" title="View Invoice">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                        </span>
                                                    <?php endif; ?>
                                                    <input type="hidden" name="existing_printing_invoice[]" value="<?php echo htmlspecialchars($pamphlet['pamphlet_printing_invoice']); ?>">
                                                </div>
                                                <div class="col-md-4 col-6 mb-3">
                                                    <label>Pamphlet Distributor Name</label>
                                                    <input type="text" class="form-control" name="pamphlet_distributor_name[]"
                                                        value="<?= htmlspecialchars($pamphlet['pamphlet_distributor_name']) ?>">
                                                </div>
                                                <?php if ($showPrice): ?>
                                                    <div class="col-md-4 col-6 mb-3">
                                                        <label>Distribution Cost</label>
                                                        <input type="number" class="form-control" name="pamphlet_distribution_cost[]"
                                                            value="<?= htmlspecialchars($pamphlet['pamphlet_distribution_cost']) ?>">
                                                    </div>
                                                <?php endif; ?>
                                                <div class="col-md-4 col-6 mb-3">
                                                    <label>Distribution Invoice</label>
                                                    <input type="file" class="form-control" name="pamphlet_distribution_invoice[]">
                                                    <?php if (!empty($pamphlet['pamphlet_distribution_invoice'])): ?>
                                                        <span>
                                                            <a href="../../uploads/pamphlets/<?php echo htmlspecialchars($pamphlet['pamphlet_distribution_invoice']); ?>" target="_blank">
                                                                <?php echo htmlspecialchars($pamphlet['pamphlet_distribution_invoice']); ?>
                                                            </a>
                                                            <a href="../../uploads/pamphlets/<?php echo htmlspecialchars($pamphlet['pamphlet_distribution_invoice']); ?>" target="_blank" title="View Invoice">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                        </span>
                                                    <?php endif; ?>
                                                    <input type="hidden" name="existing_distribution_invoice[]" value="<?php echo htmlspecialchars($pamphlet['pamphlet_distribution_invoice']); ?>">
                                                </div>
                                                <input type="hidden" name="pamphlet_received_amount[]" value="<?= htmlspecialchars($pamphlet['received_amount'] ?? 0) ?>">
                                                <div class="btn-group position-absolute" style="top: 5px; right: 10px;">
                                                    <button type="button" class="btn btn-sm btn-success add-row">+</button>
                                                    <button type="button" class="btn btn-sm btn-danger remove-row">−</button>
                                                </div>
                                            </div>
                                            <hr style="border: 1px solid red;">
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <!-- Default empty row if no pamphlet data exists -->
                                        <div class="pamphlet-row row border rounded p-2 mb-2 position-relative">
                                            <div class="col-md-2 col-6 mb-3">
                                                <label>Pamphlet Designer Name</label>
                                                <input type="text" class="form-control" name="pamphlet_designer_name[]">
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <label>Design Cost</label>
                                                <input type="number" class="form-control" name="pamphlet_designer_cost[]">
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <label>Designer Invoice</label>
                                                <input type="file" class="form-control" name="pamphlet_designer_invoice[]">
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <label>Pamphlet Printer Name</label>
                                                <input type="text" class="form-control" name="pamphlet_printer_name[]">
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <label>Printing Cost</label>
                                                <input type="number" class="form-control" name="pamphlet_printing_cost[]">
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <label>Printing Invoice</label>
                                                <input type="file" class="form-control" name="pamphlet_printing_invoice[]">
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <label>Pamphlet Distributor Name</label>
                                                <input type="text" class="form-control" name="pamphlet_distributor_name[]">
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <label>Distribution Cost</label>
                                                <input type="number" class="form-control" name="pamphlet_distribution_cost[]">
                                            </div>
                                            <div class="col-md-2 col-6 mb-3">
                                                <label>Distribution Invoice</label>
                                                <input type="file" class="form-control" value="" name="pamphlet_distribution_invoice[]">
                                            </div>
                                            <div class="btn-group position-absolute" style="top: 5px; right: 10px;">
                                                <button type="button" class="btn btn-sm btn-success add-row">+</button>
                                                <button type="button" class="btn btn-sm btn-danger remove-row">−</button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>


                        <!-- Radio and Television Section -->
                        <div class="main-card mb-3 card">
                            <div class="card-body">
                                <h5 class="card-title">Radio Station and Television Advertisement</h5>

                                <div id="radio-station-wrapper">
                                    <?php if (!empty($marketing_data['radio'])): ?>
                                        <?php foreach ($marketing_data['radio'] as $radio): ?>
                                            <div class="form-row border p-2 mb-3 rounded bg-light radio-row">
                                                <div class="col-md-3 mb-3">
                                                    <label>Radio Station Name</label>
                                                    <input type="text" class="form-control" name="radio_station_name[]"
                                                        value="<?= htmlspecialchars($radio['name']) ?>">
                                                </div>

                                                <?php if ($showPrice): ?><div class="col-md-2 mb-3">
                                                        <label>Cost</label>
                                                        <input type="number" class="form-control" name="radio_station_cost[]"
                                                            value="<?= htmlspecialchars($radio['cost']) ?>">
                                                    </div>
                                                <?php endif; ?>
                                                <div class="col-md-2 mb-3">
                                                    <label>Contact</label>
                                                    <input type="text" class="form-control" name="radio_station_contact[]"
                                                        value="<?= htmlspecialchars($radio['contact']) ?>">
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label>Remarks</label>
                                                    <textarea class="form-control" name="radio_station_remarks[]" rows="1"><?= htmlspecialchars($radio['remarks']) ?></textarea>
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label>Invoice File</label>
                                                    <input type="file" class="form-control" name="radio_station_invoice[]">
                                                    <?php if (!empty($radio['invoice_file'])): ?>
                                                        <div class="mt-1">
                                                            <a href="../../uploads/radio_invoices/<?= htmlspecialchars($radio['invoice_file']) ?>"
                                                                target="_blank" class="btn btn-sm btn-info">
                                                                View Invoice
                                                            </a>
                                                            <input type="hidden" name="existing_radio_invoice[]" value="<?= htmlspecialchars($radio['invoice_file']) ?>">
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <!-- Add this hidden input for received_amount -->
                                                <input type="hidden" name="radio_received_amount[]" value="<?= htmlspecialchars($radio['received_amount'] ?? 0) ?>">
                                                <div class="col-md-1 mb-3 d-flex align-items-end">
                                                    <button type="button" class="btn btn-success btn-sm add-radio">+</button>
                                                    <button type="button" class="btn btn-danger btn-sm remove-radio ml-2">−</button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <!-- Default empty row if no radio data exists -->
                                        <div class="form-row border p-2 mb-3 rounded bg-light radio-row">
                                            <div class="col-md-3 mb-3">
                                                <label>Radio Station Name</label>
                                                <input type="text" class="form-control" name="radio_station_name[]">
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label>Cost</label>
                                                <input type="number" class="form-control" name="radio_station_cost[]">
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label>Contact</label>
                                                <input type="text" class="form-control" name="radio_station_contact[]">
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label>Remarks</label>
                                                <textarea class="form-control" name="radio_station_remarks[]" rows="1"></textarea>
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label>Invoice File</label>
                                                <input type="file" class="form-control" name="radio_station_invoice[]">
                                            </div>
                                            <div class="col-md-1 mb-3 d-flex align-items-end">
                                                <button type="button" class="btn btn-success btn-sm add-radio">+</button>
                                                <button type="button" class="btn btn-danger btn-sm remove-radio ml-2">−</button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <hr>


                                <div id="television-wrapper">
                                    <?php if (!empty($marketing_data['television'])): ?>
                                        <?php foreach ($marketing_data['television'] as $tv): ?>
                                            <div class="form-row border p-2 mb-3 rounded bg-light tv-row">
                                                <div class="col-md-3 mb-3">
                                                    <label>Television Name</label>
                                                    <input type="text" class="form-control" name="television_name[]"
                                                        value="<?= htmlspecialchars($tv['name']) ?>">
                                                </div>

                                                <?php if ($showPrice): ?><div class="col-md-3 mb-3">
                                                        <label>Cost</label>
                                                        <input type="number" class="form-control" name="television_cost[]"
                                                            value="<?= htmlspecialchars($tv['cost']) ?>">
                                                    </div>
                                                <?php endif; ?>
                                                <div class="col-md-3 mb-3">
                                                    <label>Contact</label>
                                                    <input type="text" class="form-control" name="television_contact[]"
                                                        value="<?= htmlspecialchars($tv['contact']) ?>">
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label>Remarks</label>
                                                    <textarea class="form-control" name="television_remarks[]" rows="1"><?= htmlspecialchars($tv['remarks']) ?></textarea>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label>Invoice File</label>
                                                    <input type="file" name="television_invoice[]" class="form-control">
                                                    <?php if (!empty($tv['invoice_file'])): ?>
                                                        <div class="mt-1">
                                                            <a href="../../uploads/television_invoices/<?= htmlspecialchars($tv['invoice_file']) ?>"
                                                                target="_blank" class="btn btn-sm btn-info">
                                                                View Invoice
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                    <input type="hidden" name="existing_television_invoice[]" value="<?= htmlspecialchars($tv['invoice_file']) ?>">
                                                </div>
                                                <!-- Add this hidden input for received_amount -->
                                                <input type="hidden" name="television_received_amount[]" value="<?= htmlspecialchars($tv['received_amount'] ?? 0) ?>">
                                                <div class="col-md-1 mb-3 d-flex align-items-end">
                                                    <button type="button" class="btn btn-success btn-sm add-tv">+</button>
                                                    <button type="button" class="btn btn-danger btn-sm remove-tv ml-2">−</button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <!-- Default empty row if no TV data exists -->
                                        <div class="form-row border p-2 mb-3 rounded bg-light tv-row">
                                            <div class="col-md-3 mb-3">
                                                <label>Television Name</label>
                                                <input type="text" class="form-control" name="television_name[]">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label>Cost</label>
                                                <input type="number" class="form-control" name="television_cost[]">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label>Contact</label>
                                                <input type="text" class="form-control" name="television_contact[]">
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label>Remarks</label>
                                                <textarea class="form-control" name="television_remarks[]" rows="1"></textarea>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label>Invoice File</label>
                                                <input type="file" name="television_invoice[]" class="form-control">
                                                <input type="hidden" name="existing_television_invoice[]" value="">
                                            </div>
                                            <div class="col-md-1 mb-3 d-flex align-items-end">
                                                <button type="button" class="btn btn-success btn-sm add-tv">+</button>
                                                <button type="button" class="btn btn-danger btn-sm remove-tv ml-2">−</button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label>Interview Name & Time</label>
                                    <textarea class="form-control" name="interview_details" rows="3"
                                        placeholder="e.g. Interview on XYZ Channel at 6 PM"><?=
                                                                                            !empty($marketing_data['interview']) ? htmlspecialchars($marketing_data['interview']['details']) : '' ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Newspaper Advertisements Section -->
                        <div class="main-card mb-3 card">
                            <div class="card-body">
                                <h5 class="card-title d-flex justify-content-between align-items-center">
                                    Newspaper Advertisements
                                </h5>

                                <div id="newspaperContainer" class="form-row">
                                    <?php if (!empty($marketing_data['newspaper'])): ?>
                                        <?php foreach ($marketing_data['newspaper'] as $news): ?>
                                            <div class="newspaper-row w-100 d-flex flex-wrap border p-2 mb-2">
                                                <div class="col-md-2 mb-2">
                                                    <input type="text" class="form-control" name="newspaper_name[]"
                                                        value="<?= htmlspecialchars($news['name']) ?>" placeholder="Newspaper Name">
                                                </div>

                                                <?php if ($showPrice): ?><div class="col-md-1 mb-2">
                                                        <input type="number" class="form-control" name="newspaper_cost[]"
                                                            value="<?= htmlspecialchars($news['cost']) ?>" placeholder="Cost">
                                                    </div><?php endif; ?>
                                                <div class="col-md-2 mb-2">
                                                    <input type="text" class="form-control" name="newspaper_duration[]"
                                                        value="<?= htmlspecialchars($news['duration']) ?>" placeholder="Duration (e.g. 3 days)">
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <input type="text" class="form-control" name="newspaper_ad_size[]"
                                                        value="<?= htmlspecialchars($news['ad_size']) ?>" placeholder="Ad Size (e.g. Full page)">
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <input type="text" class="form-control" name="newspaper_contact[]"
                                                        value="<?= htmlspecialchars($news['contact']) ?>" placeholder="Contact Person">
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <input type="text" class="form-control" name="newspaper_remarks[]"
                                                        value="<?= htmlspecialchars($news['remarks']) ?>" placeholder="Remarks">
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <input type="file" class="form-control" name="newspaper_invoice[]">
                                                    <?php if (!empty($news['invoice_file'])): ?>
                                                        <div class="mt-1">
                                                            <a href="../../uploads/newspaper_invoices/<?= htmlspecialchars($news['invoice_file']) ?>" target="_blank" class="btn btn-sm btn-info">View Invoice</a>
                                                        </div>
                                                    <?php endif; ?>
                                                    <input type="hidden" name="existing_newspaper_invoice[]" value="<?= htmlspecialchars($news['invoice_file']) ?>">
                                                </div>
                                                <input type="hidden" name="newspaper_received_amount[]" value="<?= htmlspecialchars($news['received_amount'] ?? 0) ?>">
                                                <div class="col-md-1 d-flex align-items-center">
                                                    <button type="button" class="btn btn-success btn-sm mr-2" onclick="addNewspaperRow(this)">+</button>
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeNewspaperRow(this)">−</button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <!-- Default empty row if no newspaper data exists -->
                                        <div class="newspaper-row w-100 d-flex flex-wrap border p-2 mb-2">
                                            <div class="col-md-2 mb-2">
                                                <input type="text" class="form-control" name="newspaper_name[]" placeholder="Newspaper Name">
                                            </div>
                                            <div class="col-md-1 mb-2">
                                                <input type="number" class="form-control" name="newspaper_cost[]" placeholder="Cost">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <input type="text" class="form-control" name="newspaper_duration[]" placeholder="Duration (e.g. 3 days)">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <input type="text" class="form-control" name="newspaper_ad_size[]" placeholder="Ad Size (e.g. Full page)">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <input type="text" class="form-control" name="newspaper_contact[]" placeholder="Contact Person">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <input type="text" class="form-control" name="newspaper_remarks[]" placeholder="Remarks">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <input type="file" class="form-control" name="newspaper_invoice[]">
                                                <input type="hidden" name="existing_newspaper_invoice[]" value="">
                                            </div>
                                            <div class="col-md-1 d-flex align-items-center">
                                                <button type="button" class="btn btn-success btn-sm mr-2" onclick="addNewspaperRow(this)">+</button>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeNewspaperRow(this)">−</button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Billboard Advertisements Section -->
                        <div class="main-card mb-3 card">
                            <div class="card-body">
                                <h5 class="card-title d-flex justify-content-between align-items-center">
                                    Billboard Advertisements
                                </h5>

                                <div id="billboardContainer" class="form-row">
                                    <?php if (!empty($marketing_data['billboard'])): ?>
                                        <?php foreach ($marketing_data['billboard'] as $billboard): ?>
                                            <div class="billboard-row w-100 d-flex flex-wrap border p-2 mb-2">
                                                <div class="col-md-5 mb-2">
                                                    <input type="text" class="form-control" name="billboard_agency_name[]"
                                                        value="<?= htmlspecialchars($billboard['agency_name']) ?>" placeholder="Agency Name">
                                                </div>
                                                <?php if ($showPrice): ?>
                                                    <div class="col-md-3 mb-2">
                                                        <input type="number" class="form-control" name="billboard_cost[]"
                                                            value="<?= htmlspecialchars($billboard['cost']) ?>" placeholder="Advertisement Cost" step="any">
                                                    </div>
                                                <?php endif; ?>
                                                <!-- Add this hidden input for received_amount -->
                                                <input type="hidden" name="billboard_received_amount[]" value="<?= htmlspecialchars($billboard['received_amount'] ?? 0) ?>">
                                                <div class="col-md-3 mb-2">
                                                    <input type="file" class="form-control" name="billboard_invoice[]">
                                                    <?php if (!empty($billboard['invoice_file'])): ?>
                                                        <div class="mt-1">
                                                            <a href="../../uploads/billboard_invoices/<?= htmlspecialchars($billboard['invoice_file']) ?>" target="_blank" class="btn btn-sm btn-info">View Invoice</a>
                                                        </div>
                                                    <?php endif; ?>
                                                    <input type="hidden" name="existing_billboard_invoice[]" value="<?= htmlspecialchars($billboard['invoice_file']) ?>">
                                                </div>
                                                <div class="col-md-1 d-flex align-items-center">
                                                    <button type="button" class="btn btn-sm btn-success mr-2" onclick="addBillboardRow()">+</button>
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeBillboardRow(this)">−</button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <!-- Default empty row if no billboard data exists -->
                                        <div class="billboard-row w-100 d-flex flex-wrap border p-2 mb-2">
                                            <div class="col-md-5 mb-2">
                                                <input type="text" class="form-control" name="billboard_agency_name[]" placeholder="Agency Name">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <input type="number" class="form-control" name="billboard_cost[]" placeholder="Advertisement Cost">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <input type="file" class="form-control" name="billboard_invoice[]">
                                                <input type="hidden" name="existing_billboard_invoice[]" value="">
                                            </div>
                                            <div class="col-md-1 d-flex align-items-center">
                                                <button type="button" class="btn btn-sm btn-success mr-2" onclick="addBillboardRow()">+</button>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeBillboardRow(this)">−</button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Facebook Advertisements Section -->
                        <div class="main-card mb-3 card">
                            <div class="card-body">
                                <h5 class="card-title d-flex justify-content-between align-items-center">
                                    Facebook Advertisements
                                </h5>

                                <div id="facebookContainer" class="form-row">
                                    <?php if (!empty($marketing_data['facebook'])): ?>
                                        <?php foreach ($marketing_data['facebook'] as $fb): ?>
                                            <div class="facebook-row w-100 d-flex flex-wrap border p-2 mb-2">
                                                <div class="col-md-4 mb-2">
                                                    <input type="text" class="form-control" name="facebook_name[]"
                                                        value="<?= htmlspecialchars($fb['name']) ?>" placeholder="Facebook Page Name">
                                                </div>
                                                <?php if ($showPrice): ?>
                                                    <div class="col-md-3 mb-2">
                                                        <input type="number" class="form-control" name="facebook_cost[]"
                                                            value="<?= htmlspecialchars($fb['cost']) ?>" placeholder="Cost">
                                                    </div>
                                                <?php endif; ?>
                                                <input type="hidden" class="form-control" name="facebook_received_amount[]"
                                                        value="<?= htmlspecialchars($fb['received_amount']) ?>" placeholder="Cost">
                                                <div class="col-md-3 mb-2">
                                                    <input type="file" class="form-control" name="facebook_invoice[]">
                                                    <?php if (!empty($fb['invoice_file'])): ?>
                                                        <div class="mt-1">
                                                            <a href="../../uploads/facebook_invoices/<?= htmlspecialchars($fb['invoice_file']) ?>" target="_blank" class="btn btn-sm btn-info">View Invoice</a>
                                                        </div>
                                                    <?php endif; ?>
                                                    <input type="hidden" name="existing_facebook_invoice[]" value="<?= htmlspecialchars($fb['invoice_file']) ?>">
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <button type="button" class="btn btn-sm btn-success mr-2" onclick="addFacebookRow()">+</button>
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeFacebookRow(this)">−</button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <!-- Default empty row if no facebook data exists -->
                                        <div class="facebook-row w-100 d-flex flex-wrap border p-2 mb-2">
                                            <div class="col-md-4 mb-2">
                                                <input type="text" class="form-control" name="facebook_name[]" placeholder="Facebook Page Name">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <input type="number" class="form-control" name="facebook_cost[]" placeholder="Cost">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <input type="file" class="form-control" name="facebook_invoice[]">
                                                <input type="hidden" name="existing_facebook_invoice[]" value="">
                                            </div>
                                            <div class="col-md-2 d-flex align-items-center">
                                                <button type="button" class="btn btn-sm btn-success mr-2" onclick="addFacebookRow()">+</button>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeFacebookRow(this)">−</button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Instagram Advertisements Section -->
                        <div class="main-card mb-3 card">
                            <div class="card-body">

                                <h5 class="card-title d-flex justify-content-between align-items-center">
                                    Instagram Advertisement
                                </h5>

                                <div id="instagramContainer" class="form-row">
                                    <?php if (!empty($marketing_data['instagram'])): ?>
                                        <?php foreach ($marketing_data['instagram'] as $insta): ?>
                                            <div class="instagram-row w-100 d-flex flex-wrap border p-2 mb-2">
                                                <div class="col-md-4 mb-2">
                                                    <input type="text" class="form-control" name="instagram_name[]"
                                                        value="<?= htmlspecialchars($insta['name'] ?? '') ?>" placeholder="Instagram Page Name">
                                                </div>
                                                <?php if ($showPrice): ?>
                                                    <div class="col-md-3 mb-2">
                                                        <input type="number" class="form-control" name="instagram_cost[]"
                                                            value="<?= htmlspecialchars($insta['cost']) ?>" placeholder="Cost">
                                                    </div>
                                                <?php endif; ?>
                                                <input type="hidden" class="form-control" name="instagram_received_amount[]"
                                                <div class="col-md-3 mb-2">
                                                    <input type="file" class="form-control" name="instagram_invoice[]">
                                                    <?php if (!empty($insta['invoice_file'])): ?>
                                                        <div class="mt-1">
                                                            <a href="../../uploads/instagram_invoices/<?= htmlspecialchars($insta['invoice_file']) ?>" target="_blank" class="btn btn-sm btn-info">View Invoice</a>
                                                        </div>
                                                    <?php endif; ?>
                                                    <input type="hidden" name="existing_instagram_invoice[]" value="<?= htmlspecialchars($insta['invoice_file']) ?>">
                                                </div>
                                                <!-- Add this hidden input for received_amount -->
                                                <input type="hidden" name="instagram_received_amount[]" value="<?= htmlspecialchars($insta['received_amount'] ?? 0) ?>">
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <button type="button" class="btn btn-success btn-sm mr-2" onclick="addInstagramRow()">+</button>
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeInstagramRow(this)">−</button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <!-- Default empty row if no instagram data exists -->
                                        <div class="instagram-row w-100 d-flex flex-wrap border p-2 mb-2">
                                            <div class="col-md-4 mb-2">
                                                <input type="text" class="form-control" name="instagram_name[]" placeholder="Instagram Page Name">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <input type="number" class="form-control" name="instagram_cost[]" placeholder="Cost">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <input type="file" class="form-control" name="instagram_invoice[]">
                                                <input type="hidden" name="existing_instagram_invoice[]" value="">
                                            </div>
                                            <div class="col-md-2 d-flex align-items-center">
                                                <button type="button" class="btn btn-success btn-sm mr-2" onclick="addInstagramRow()">+</button>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeInstagramRow(this)">−</button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Other Marketing Details Section -->
                        <div class="main-card mb-3 card">
                            <div class="card-body">
                                <h5 class="card-title">Other Advertisement & Marketing Details</h5>
                                <div class="form-row">
                                    <!-- Literature By -->
                                    <div class="col-md-4 mb-3">
                                        <label for="literature_by">Literature By</label>
                                        <textarea class="form-control" id="literature_by" name="literature_by" rows="2"
                                            placeholder="Enter literature by details..."><?=
                                                                                            !empty($marketing_data['other']) ? htmlspecialchars($marketing_data['other']['literature_by']) : '' ?></textarea>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="literature_cost">Literature Cost</label>
                                        <input type="number" class="form-control" id="literature_cost" name="literature_cost"
                                            value="<?= !empty($marketing_data['other']) ? htmlspecialchars($marketing_data['other']['literature_cost']) : '' ?>"
                                            placeholder="Enter literature cost">
                                    </div>

                                    <!-- Other Marketing Material -->
                                    <div class="col-md-4 mb-3">
                                        <label for="other_marketing_material">Other Marketing Material</label>
                                        <textarea class="form-control" id="other_marketing_material" name="other_marketing_material" rows="2"
                                            placeholder="Enter marketing materials used..."><?=
                                                                                            !empty($marketing_data['other']) ? htmlspecialchars($marketing_data['other']['other_marketing_material']) : '' ?></textarea>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="marketing_material_cost">Marketing Material Cost</label>
                                        <input type="number" class="form-control" id="marketing_material_cost" name="marketing_material_cost"
                                            value="<?= !empty($marketing_data['other']) ? htmlspecialchars($marketing_data['other']['marketing_material_cost']) : '' ?>"
                                            placeholder="Enter material cost">
                                    </div>

                                    <!-- Other Essentials -->
                                    <div class="col-md-4 mb-3">
                                        <label for="other_essential">Other Essentials</label>
                                        <textarea class="form-control" id="other_essential" name="other_essential" rows="2"
                                            placeholder="Enter other essential details..."><?=
                                                                                            !empty($marketing_data['other']) ? htmlspecialchars($marketing_data['other']['other_essential']) : '' ?></textarea>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="other_essential_cost">Other Essentials Cost</label>
                                        <input type="number" class="form-control" id="other_essential_cost" name="other_essential_cost"
                                            value="<?= !empty($marketing_data['other']) ? htmlspecialchars($marketing_data['other']['other_essential_cost']) : '' ?>"
                                            placeholder="Enter essential cost">
                                    </div>

                                    <!-- Logistic -->
                                    <div class="col-md-4 mb-3">
                                        <label for="logistic">Logistic</label>
                                        <textarea class="form-control" id="logistic" name="logistic" rows="2"
                                            placeholder="Enter logistic information..."><?=
                                                                                        !empty($marketing_data['other']) ? htmlspecialchars($marketing_data['other']['logistic']) : '' ?></textarea>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="logistic_cost">Logistic Cost</label>
                                        <input type="number" class="form-control" id="logistic_cost" name="logistic_cost"
                                            value="<?= !empty($marketing_data['other']) ? htmlspecialchars($marketing_data['other']['logistic_cost']) : '' ?>"
                                            placeholder="Enter logistic cost">
                                    </div>

                                    <!-- Marketing Agency -->
                                    <div class="col-md-4 mb-3">
                                        <label for="marketing_agency">Marketing Agency</label>
                                        <textarea class="form-control" id="marketing_agency" name="marketing_agency" rows="2"
                                            placeholder="Enter marketing agency name and cost..."><?=
                                                                                                    !empty($marketing_data['other']) ? htmlspecialchars($marketing_data['other']['marketing_agency']) : '' ?></textarea>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="marketing_agency_cost">Marketing Agency Cost</label>
                                        <input type="number" class="form-control" id="marketing_agency_cost" name="marketing_agency_cost"
                                            value="<?= !empty($marketing_data['other']) ? htmlspecialchars($marketing_data['other']['marketing_agency_cost']) : '' ?>"
                                            placeholder="Enter agency cost">
                                    </div>

                                    <!-- Accommodation -->
                                    <div class="col-md-4 mb-3">
                                        <label for="accommodation">Accommodation</label>
                                        <textarea class="form-control" id="accommodation" name="accommodation" rows="2"
                                            placeholder="Enter accommodation details..."><?=
                                                                                            !empty($marketing_data['other']) ? htmlspecialchars($marketing_data['other']['accommodation']) : '' ?></textarea>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="accommodation_cost">Accommodation Cost</label>
                                        <input type="number" class="form-control" id="accommodation_cost" name="accommodation_cost"
                                            value="<?= !empty($marketing_data['other']) ? htmlspecialchars($marketing_data['other']['accommodation_cost']) : '' ?>"
                                            placeholder="Enter accommodation cost">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Information Section -->
                        <div class="main-card mb-3 card">
                            <div class="card-body">
                                <h5 class="card-title">Account Information</h5>
                                <div class="form-row">
                                    <!-- Account Holder's Name -->
                                    <div class="col-md-4 col-12 mb-3">
                                        <label for="account_holder_name">Account Holder's Name</label>
                                        <input type="text" class="form-control" id="account_holder_name" name="account_holder_name"
                                            value="<?= !empty($marketing_data['account']) ? htmlspecialchars($marketing_data['account']['account_holder_name']) : '' ?>">
                                    </div>
                                    <!-- Bank Name -->
                                    <div class="col-md-4 col-12 mb-3">
                                        <label for="bank_name">Bank Name</label>
                                        <input type="text" class="form-control" id="bank_name" name="bank_name"
                                            value="<?= !empty($marketing_data['account']) ? htmlspecialchars($marketing_data['account']['bank_name']) : '' ?>">
                                    </div>
                                    <!-- Account Number -->
                                    <div class="col-md-4 col-12 mb-3">
                                        <label for="account_number">Account Number</label>
                                        <input type="text" class="form-control" id="account_number" name="account_number"
                                            value="<?= !empty($marketing_data['account']) ? htmlspecialchars($marketing_data['account']['account_number']) : '' ?>">
                                    </div>
                                    <!-- IFSC Code -->
                                    <div class="col-md-4 col-12 mb-3">
                                        <label for="ifsc_code">IFSC Code</label>
                                        <input type="text" class="form-control" id="ifsc_code" name="ifsc_code"
                                            value="<?= !empty($marketing_data['account']) ? htmlspecialchars($marketing_data['account']['ifsc_code']) : '' ?>">
                                    </div>
                                    <!-- Branch -->
                                    <div class="col-md-4 col-12 mb-3">
                                        <label for="branch">Branch</label>
                                        <input type="text" class="form-control" id="branch" name="branch"
                                            value="<?= !empty($marketing_data['account']) ? htmlspecialchars($marketing_data['account']['branch']) : '' ?>">
                                    </div>
                                    <!-- UPI ID -->
                                    <div class="col-md-4 col-12 mb-3">
                                        <label for="upi_id">UPI ID</label>
                                        <input type="text" class="form-control" id="upi_id" name="upi_id"
                                            value="<?= !empty($marketing_data['account']) ? htmlspecialchars($marketing_data['account']['upi_id']) : '' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button class="btn btn-outline-dark mx-3" type="button" onclick="history.back();">Back</button>
                            <button class="btn btn-success" type="submit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- JavaScript for dynamic row management -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle all dynamic row operations using event delegation
                document.body.addEventListener('click', function(e) {
                    // Add new pamphlet row
                    if (e.target.classList.contains('add-row')) {
                        const currentRow = e.target.closest('.pamphlet-row');
                        const newRow = currentRow.cloneNode(true);

                        // Clear all input values except hidden fields that aren't for existing files
                        newRow.querySelectorAll('input').forEach(input => {
                            if (input.type !== 'hidden') {
                                input.value = '';
                            } else if (input.name.includes('existing_')) {
                                // Clear hidden fields for existing files
                                input.value = '';
                            }
                        });

                        // Clear any file display links
                        newRow.querySelectorAll('span a').forEach(link => {
                            link.parentElement.style.display = 'none';
                        });

                        document.getElementById('pamphlet-container').appendChild(newRow);
                    }

                    // Remove pamphlet row
                    if (e.target.classList.contains('remove-row')) {
                        const currentRow = e.target.closest('.pamphlet-row');
                        const container = document.getElementById('pamphlet-container');
                        if (container.querySelectorAll('.pamphlet-row').length > 1) {
                            currentRow.remove();
                        } else {
                            alert('At least one pamphlet row must be present.');
                        }
                    }

                    // Add radio advertisement row
                    if (e.target.classList.contains('add-radio')) {
                        const row = e.target.closest('.radio-row');
                        const clone = row.cloneNode(true);

                        // Clear all input and textarea values
                        clone.querySelectorAll('input, textarea').forEach(el => {
                            if (el.type !== 'hidden') {
                                el.value = '';
                            }
                            // Also clear hidden fields for existing files
                            if (el.name.includes('existing_')) {
                                el.value = '';
                            }
                        });

                        // Remove any existing invoice display elements
                        const invoiceDisplay = clone.querySelector('.mt-1');
                        if (invoiceDisplay) {
                            invoiceDisplay.remove();
                        }

                        document.getElementById('radio-station-wrapper').appendChild(clone);
                    }

                    // Remove radio advertisement row
                    if (e.target.classList.contains('remove-radio')) {
                        const rows = document.querySelectorAll('.radio-row');
                        if (rows.length > 1) {
                            e.target.closest('.radio-row').remove();
                        } else {
                            alert('At least one radio advertisement must be present.');
                        }
                    }

                    // Add television advertisement row
                    if (e.target.classList.contains('add-tv')) {
                        const row = e.target.closest('.tv-row');
                        const clone = row.cloneNode(true);

                        // Clear all input and textarea values
                        clone.querySelectorAll('input, textarea').forEach(el => {
                            if (el.type !== 'hidden') {
                                el.value = '';
                            }
                            // Also clear hidden fields for existing files
                            if (el.name.includes('existing_')) {
                                el.value = '';
                            }
                        });

                        // Remove any existing invoice display elements
                        const invoiceDisplay = clone.querySelector('.mt-1');
                        if (invoiceDisplay) {
                            invoiceDisplay.remove();
                        }

                        document.getElementById('television-wrapper').appendChild(clone);
                    }

                    // Remove television advertisement row
                    if (e.target.classList.contains('remove-tv')) {
                        const rows = document.querySelectorAll('.tv-row');
                        if (rows.length > 1) {} else {
                            alert('At least one television advertisement must be present.');
                        }
                    }
                });

                // Newspaper rows
                function addNewspaperRow() {
                    const container = document.getElementById('newspaperContainer');
                    const row = document.querySelector('.newspaper-row');
                    const clone = row.cloneNode(true);

                    // Clear all input values
                    clone.querySelectorAll('input').forEach(input => {
                        if (input.type !== 'hidden') {
                            input.value = '';
                        }
                        // Also clear hidden fields for existing files
                        if (input.name.includes('existing_')) {
                            input.value = '';
                        }
                    });

                    // Remove any existing invoice display elements
                    const invoiceDisplay = clone.querySelector('.mt-1');
                    if (invoiceDisplay) {
                        invoiceDisplay.remove();
                    }

                    container.appendChild(clone);
                }

                function removeNewspaperRow(btn) {
                    const rows = document.querySelectorAll('.newspaper-row');
                    if (rows.length > 1) {
                        btn.closest('.newspaper-row').remove();
                    } else {
                        alert('At least one newspaper advertisement must be present.');
                    }
                }

                // Billboard rows
                function addBillboardRow() {
                    const container = document.getElementById('billboardContainer');
                    const row = document.querySelector('.billboard-row');
                    const clone = row.cloneNode(true);

                    // Clear all input values
                    clone.querySelectorAll('input').forEach(input => {
                        if (input.type !== 'hidden') {
                            input.value = '';
                        }
                        // Also clear hidden fields for existing files
                        if (input.name.includes('existing_')) {
                            input.value = '';
                        }
                    });

                    // Remove any existing invoice display elements
                    const invoiceDisplay = clone.querySelector('.mt-1');
                    if (invoiceDisplay) {
                        invoiceDisplay.remove();
                    }

                    container.appendChild(clone);
                }

                function removeBillboardRow(btn) {
                    const rows = document.querySelectorAll('.billboard-row');
                    if (rows.length > 1) {
                        btn.closest('.billboard-row').remove();
                    } else {
                        alert('At least one billboard advertisement must be present.');
                    }
                }

                // Facebook rows
                function addFacebookRow() {
                    const container = document.getElementById('facebookContainer');
                    const row = document.querySelector('.facebook-row');
                    const clone = row.cloneNode(true);

                    // Clear all input values
                    clone.querySelectorAll('input').forEach(input => {
                        if (input.type !== 'hidden') {
                            input.value = '';
                        }
                        // Also clear hidden fields for existing files
                        if (input.name.includes('existing_')) {
                            input.value = '';
                        }
                    });

                    // Remove any existing invoice display elements
                    const invoiceDisplay = clone.querySelector('.mt-1');
                    if (invoiceDisplay) {
                        invoiceDisplay.remove();
                    }

                    container.appendChild(clone);
                }

                function removeFacebookRow(button) {
                    const rows = document.querySelectorAll('.facebook-row');
                    if (rows.length > 1) {
                        button.closest('.facebook-row').remove();
                    } else {
                        alert('At least one Facebook advertisement must be present.');
                    }
                }

                // Instagram rows
                function addInstagramRow() {
                    const container = document.getElementById('instagramContainer');
                    const row = document.querySelector('.instagram-row');
                    const clone = row.cloneNode(true);

                    // Clear all input values
                    clone.querySelectorAll('input').forEach(input => {
                        if (input.type !== 'hidden') {
                            input.value = '';
                        }
                        // Also clear hidden fields for existing files
                        if (input.name.includes('existing_')) {
                            input.value = '';
                        }
                    });

                    // Remove any existing invoice display elements
                    const invoiceDisplay = clone.querySelector('.mt-1');
                    if (invoiceDisplay) {
                        invoiceDisplay.remove();
                    }

                    container.appendChild(clone);
                }

                function removeInstagramRow(button) {
                    const rows = document.querySelectorAll('.instagram-row');
                    if (rows.length > 1) {
                        button.closest('.instagram-row').remove();
                    } else {
                        alert('At least one Instagram advertisement must be present.');
                    }
                }

                // Make functions available globally
                window.addNewspaperRow = addNewspaperRow;
                window.removeNewspaperRow = removeNewspaperRow;
                window.addBillboardRow = addBillboardRow;
                window.removeBillboardRow = removeBillboardRow;
                window.addFacebookRow = addFacebookRow;
                window.removeFacebookRow = removeFacebookRow;
                window.addInstagramRow = addInstagramRow;
                window.removeInstagramRow = removeInstagramRow;
            });
        </script>



        <?php include '../Includes/footer.php'; ?>