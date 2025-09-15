<?php
include '../Includes/header.php';
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Program Management');
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
                                                        value="<?= htmlspecialchars($insta['name']) ?>" placeholder="Instagram Page Name">
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <input type="number" class="form-control" name="instagram_cost[]"
                                                        value="<?= htmlspecialchars($insta['cost']) ?>" placeholder="Cost">
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <input type="file" class="form-control" name="instagram_invoice[]">
                                                    <?php if (!empty($insta['invoice_file'])): ?>
                                                        <div class="mt-1">
                                                            <a href="../../uploads/instagram_invoices/<?= htmlspecialchars($insta['invoice_file']) ?>" target="_blank" class="btn btn-sm btn-info">View Invoice</a>
                                                        </div>
                                                    <?php endif; ?>
                                                    <input type="hidden" name="existing_instagram_invoice[]" value="<?= htmlspecialchars($insta['invoice_file']) ?>">
                                                </div>
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
                        if (rows.length > 1) {
                            e.target.closest('.tv-row').remove();
                        } else {
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