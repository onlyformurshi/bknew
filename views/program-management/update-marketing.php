<?php
session_start();
require '../../config/config.php';
require '../../helpers/security.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Program Management');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $program_id = intval($_POST['program_id'] ?? 0);
    if ($program_id <= 0) {
        die("Invalid Program ID.");
    }

    // Add this line to allow file uploads
    // In your form: <form action="update-marketing.php" method="post" enctype="multipart/form-data">

    try {
        $pdo->beginTransaction();

        // === First Delete All Existing Marketing Data for This Program ===
        $tablesToDelete = [
            'program_pamphlets',
            'radio_advertisements',
            'television_advertisements',
            'interview_details',
            'newspaper_advertisements',
            'billboard_advertisements',
            'facebook_advertisements',
            'instagram_advertisements',
            'other_marketing_details',
            'program_bank_accounts'
        ];

        foreach ($tablesToDelete as $table) {
            $stmt = $pdo->prepare("DELETE FROM $table WHERE program_id = ?");
            $stmt->execute([$program_id]);
        }

        // === Pamphlet Details ===
        $designer_names = $_POST['pamphlet_designer_name'] ?? [];
        $designer_costs = $_POST['pamphlet_designer_cost'] ?? [];
        $printer_names = $_POST['pamphlet_printer_name'] ?? [];
        $printing_costs = $_POST['pamphlet_printing_cost'] ?? [];
        $distributor_names = $_POST['pamphlet_distributor_name'] ?? [];
        $distribution_costs = $_POST['pamphlet_distribution_cost'] ?? [];
        $pamphlet_received_amounts = $_POST['pamphlet_received_amount'] ?? [];

        // Handle file uploads
        $designer_invoices = $_FILES['pamphlet_designer_invoice'] ?? [];
        $printing_invoices = $_FILES['pamphlet_printing_invoice'] ?? [];
        $distribution_invoices = $_FILES['pamphlet_distribution_invoice'] ?? [];

        $existing_designer_invoices = $_POST['existing_designer_invoice'] ?? [];
        $existing_printing_invoices = $_POST['existing_printing_invoice'] ?? [];
        $existing_distribution_invoices = $_POST['existing_distribution_invoice'] ?? [];

        $total = count($designer_names);

        $stmtPamphlet = $pdo->prepare("INSERT INTO program_pamphlets (
            program_id, pamphlet_designer_name, pamphlet_designer_cost, pamphlet_designer_invoice,
            pamphlet_printer_name, pamphlet_printing_cost, pamphlet_printing_invoice,
            pamphlet_distributor_name, pamphlet_distribution_cost, pamphlet_distribution_invoice, received_amount, created_at
        ) VALUES (
            :program_id, :designer_name, :designer_cost, :designer_invoice,
            :printer_name, :printing_cost, :printing_invoice,
            :distributor_name, :distribution_cost, :distribution_invoice, :received_amount, NOW()
        )");

        // Directory to save uploads
        $uploadDir = '../../uploads/pamphlets/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        for ($i = 0; $i < $total; $i++) {
            if (
                trim($designer_names[$i]) === '' &&
                trim($designer_costs[$i]) === '' &&
                trim($printer_names[$i]) === '' &&
                trim($printing_costs[$i]) === '' &&
                trim($distributor_names[$i]) === '' &&
                trim($distribution_costs[$i]) === ''
            ) {
                continue; // skip empty row
            }

            // Designer invoice
            $designer_invoice_name = $existing_designer_invoices[$i] ?? null;
            if (isset($designer_invoices['name'][$i]) && $designer_invoices['error'][$i] === UPLOAD_ERR_OK) {
                $ext = pathinfo($designer_invoices['name'][$i], PATHINFO_EXTENSION);
                $designer_invoice_name = uniqid('designer_') . '.' . $ext;
                move_uploaded_file($designer_invoices['tmp_name'][$i], $uploadDir . $designer_invoice_name);
            }

            // Printing invoice
            $printing_invoice_name = $existing_printing_invoices[$i] ?? null;
            if (isset($printing_invoices['name'][$i]) && $printing_invoices['error'][$i] === UPLOAD_ERR_OK) {
                $ext = pathinfo($printing_invoices['name'][$i], PATHINFO_EXTENSION);
                $printing_invoice_name = uniqid('printing_') . '.' . $ext;
                move_uploaded_file($printing_invoices['tmp_name'][$i], $uploadDir . $printing_invoice_name);
            }

            // Distribution invoice
            $distribution_invoice_name = $existing_distribution_invoices[$i] ?? null;
            if (isset($distribution_invoices['name'][$i]) && $distribution_invoices['error'][$i] === UPLOAD_ERR_OK) {
                $ext = pathinfo($distribution_invoices['name'][$i], PATHINFO_EXTENSION);
                $distribution_invoice_name = uniqid('distribution_') . '.' . $ext;
                move_uploaded_file($distribution_invoices['tmp_name'][$i], $uploadDir . $distribution_invoice_name);
            }

            $stmtPamphlet->execute([
                ':program_id' => $program_id,
                ':designer_name' => htmlspecialchars(trim($designer_names[$i])),
                ':designer_cost' => floatval($designer_costs[$i]),
                ':designer_invoice' => $designer_invoice_name,
                ':printer_name' => htmlspecialchars(trim($printer_names[$i])),
                ':printing_cost' => floatval($printing_costs[$i]),
                ':printing_invoice' => $printing_invoice_name,
                ':distributor_name' => htmlspecialchars(trim($distributor_names[$i])),
                ':distribution_cost' => floatval($distribution_costs[$i]),
                ':distribution_invoice' => $distribution_invoice_name,
                ':received_amount' => floatval($pamphlet_received_amounts[$i] ?? 0),
            ]);
        }

        // === Radio Advertisements ===
        $radio_names = $_POST['radio_station_name'] ?? [];
        $radio_costs = $_POST['radio_station_cost'] ?? [];
        $radio_received_amounts = $_POST['radio_received_amount'] ?? [];
        $radio_contacts = $_POST['radio_station_contact'] ?? [];
        $radio_remarks = $_POST['radio_station_remarks'] ?? [];

        // Handle radio invoice file uploads
        $radio_invoices = $_FILES['radio_station_invoice'] ?? [];
        $existing_radio_invoices = $_POST['existing_radio_invoice'] ?? [];

        $radioStmt = $pdo->prepare("INSERT INTO radio_advertisements (
            program_id, name, cost, received_amount, contact, remarks, invoice_file, created_at
        ) VALUES (
            :program_id, :name, :cost, :received_amount, :contact, :remarks, :invoice_file, NOW()
        )");

        // Directory to save radio invoices
        $radioUploadDir = '../../uploads/radio_invoices/';
        if (!is_dir($radioUploadDir)) {
            mkdir($radioUploadDir, 0777, true);
        }

        foreach ($radio_names as $i => $name) {
            if (trim($name) !== '') {
                // Handle invoice file
                $invoice_file_name = $existing_radio_invoices[$i] ?? null;
                if (isset($radio_invoices['name'][$i]) && $radio_invoices['error'][$i] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($radio_invoices['name'][$i], PATHINFO_EXTENSION);
                    $invoice_file_name = uniqid('radio_invoice_') . '.' . $ext;
                    move_uploaded_file($radio_invoices['tmp_name'][$i], $radioUploadDir . $invoice_file_name);
                }

                $radioStmt->execute([
                    ':program_id' => $program_id,
                    ':name' => htmlspecialchars(trim($name)),
                    ':cost' => floatval($radio_costs[$i] ?? 0),
                    ':received_amount' => floatval($radio_received_amounts[$i] ?? 0),
                    ':contact' => htmlspecialchars(trim($radio_contacts[$i] ?? '')),
                    ':remarks' => htmlspecialchars(trim($radio_remarks[$i] ?? '')),
                    ':invoice_file' => $invoice_file_name
                ]);
            }
        }

        // === Television Advertisements ===
        $tv_names = $_POST['television_name'] ?? [];
        $tv_costs = $_POST['television_cost'] ?? [];
        $tv_received_amounts = $_POST['television_received_amount'] ?? [];
        $tv_contacts = $_POST['television_contact'] ?? [];
        $tv_remarks = $_POST['television_remarks'] ?? [];

        // Handle TV invoice file uploads
        $tv_invoices = $_FILES['television_invoice'] ?? [];
        $existing_tv_invoices = $_POST['existing_television_invoice'] ?? [];

        $tvStmt = $pdo->prepare("INSERT INTO television_advertisements (
            program_id, name, cost, received_amount, contact, remarks, invoice_file, created_at
        ) VALUES (
            :program_id, :name, :cost, :received_amount, :contact, :remarks, :invoice_file, NOW()
        )");

        // Directory to save TV invoices
        $tvUploadDir = '../../uploads/television_invoices/';
        if (!is_dir($tvUploadDir)) {
            mkdir($tvUploadDir, 0777, true);
        }

        foreach ($tv_names as $i => $name) {
            if (trim($name) !== '') {
                // Handle invoice file
                $invoice_file_name = $existing_tv_invoices[$i] ?? null;
                if (isset($tv_invoices['name'][$i]) && $tv_invoices['error'][$i] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($tv_invoices['name'][$i], PATHINFO_EXTENSION);
                    $invoice_file_name = uniqid('tv_invoice_') . '.' . $ext;
                    move_uploaded_file($tv_invoices['tmp_name'][$i], $tvUploadDir . $invoice_file_name);
                }

                $tvStmt->execute([
                    ':program_id' => $program_id,
                    ':name' => htmlspecialchars(trim($name)),
                    ':cost' => floatval($tv_costs[$i] ?? 0),
                    ':received_amount' => floatval($tv_received_amounts[$i] ?? 0),
                    ':contact' => htmlspecialchars(trim($tv_contacts[$i] ?? '')),
                    ':remarks' => htmlspecialchars(trim($tv_remarks[$i] ?? '')),
                    ':invoice_file' => $invoice_file_name
                ]);
            }
        }

        // === Interview Details ===
        $interview_details = trim($_POST['interview_details'] ?? '');
        if (!empty($interview_details)) {
            $stmtInterview = $pdo->prepare("INSERT INTO interview_details (
                program_id, details, created_at
            ) VALUES (
                :program_id, :details, NOW()
            )");

            $stmtInterview->execute([
                ':program_id' => $program_id,
                ':details' => htmlspecialchars($interview_details)
            ]);
        }

        // === Newspaper Advertisements ===
        $newspaper_names = $_POST['newspaper_name'] ?? [];
        $newspaper_costs = $_POST['newspaper_cost'] ?? [];
        $newspaper_received_amounts = $_POST['newspaper_received_amount'] ?? [];
        $newspaper_durations = $_POST['newspaper_duration'] ?? [];
        $newspaper_ad_sizes = $_POST['newspaper_ad_size'] ?? [];
        $newspaper_contacts = $_POST['newspaper_contact'] ?? [];
        $newspaper_remarks = $_POST['newspaper_remarks'] ?? [];

        // Handle newspaper invoice file uploads
        $newspaper_invoices = $_FILES['newspaper_invoice'] ?? [];
        $existing_newspaper_invoices = $_POST['existing_newspaper_invoice'] ?? [];

        $newspaperStmt = $pdo->prepare("INSERT INTO newspaper_advertisements (
            program_id, name, cost, received_amount, duration, ad_size, contact, remarks, invoice_file, created_at
        ) VALUES (
            :program_id, :name, :cost, :received_amount, :duration, :ad_size, :contact, :remarks, :invoice_file, NOW()
        )");

        // Directory to save newspaper invoices
        $newspaperUploadDir = '../../uploads/newspaper_invoices/';
        if (!is_dir($newspaperUploadDir)) {
            mkdir($newspaperUploadDir, 0777, true);
        }

        foreach ($newspaper_names as $i => $name) {
            if (trim($name) !== '') {
                // Handle invoice file
                $invoice_file_name = $existing_newspaper_invoices[$i] ?? null;
                if (isset($newspaper_invoices['name'][$i]) && $newspaper_invoices['error'][$i] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($newspaper_invoices['name'][$i], PATHINFO_EXTENSION);
                    $invoice_file_name = uniqid('newspaper_invoice_') . '.' . $ext;
                    move_uploaded_file($newspaper_invoices['tmp_name'][$i], $newspaperUploadDir . $invoice_file_name);
                }

                $newspaperStmt->execute([
                    ':program_id' => $program_id,
                    ':name' => htmlspecialchars(trim($name)),
                    ':cost' => floatval($newspaper_costs[$i] ?? 0),
                    ':received_amount' => floatval($newspaper_received_amounts[$i] ?? 0),
                    ':duration' => htmlspecialchars(trim($newspaper_durations[$i] ?? '')),
                    ':ad_size' => htmlspecialchars(trim($newspaper_ad_sizes[$i] ?? '')),
                    ':contact' => htmlspecialchars(trim($newspaper_contacts[$i] ?? '')),
                    ':remarks' => htmlspecialchars(trim($newspaper_remarks[$i] ?? '')),
                    ':invoice_file' => $invoice_file_name
                ]);
            }
        }

        $billboard_agency_names = $_POST['billboard_agency_name'] ?? [];
        $billboard_costs = $_POST['billboard_cost'] ?? [];
        $billboard_received_amount = $_POST['billboard_received_amount'] ?? [];
        $billboard_invoices = $_FILES['billboard_invoice'] ?? [];
        $existing_billboard_invoices = $_POST['existing_billboard_invoice'] ?? [];

        $billboardStmt = $pdo->prepare("INSERT INTO billboard_advertisements (
    program_id, agency_name, cost, received_amount, invoice_file, created_at
) VALUES (
    :program_id, :agency_name, :cost, :received_amount, :invoice_file, NOW()
)");

        $billboardUploadDir = '../../uploads/billboard_invoices/';
        if (!is_dir($billboardUploadDir)) {
            mkdir($billboardUploadDir, 0777, true);
        }

        foreach ($billboard_agency_names as $i => $agency) {
            if (trim($agency) !== '') {
                $invoice_file_name = $existing_billboard_invoices[$i] ?? null;
                if (isset($billboard_invoices['name'][$i]) && $billboard_invoices['error'][$i] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($billboard_invoices['name'][$i], PATHINFO_EXTENSION);
                    $invoice_file_name = uniqid('billboard_invoice_') . '.' . $ext;
                    move_uploaded_file($billboard_invoices['tmp_name'][$i], $billboardUploadDir . $invoice_file_name);
                }
                $billboardStmt->execute([
                    ':program_id' => $program_id,
                    ':agency_name' => htmlspecialchars(trim($agency)),
                    ':cost' => floatval($billboard_costs[$i] ?? 0),
                    ':received_amount' => floatval($billboard_received_amount[$i] ?? 0),
                    ':invoice_file' => $invoice_file_name,
                ]);
            }
        }

        // === Facebook Advertisements ===

        $facebook_names = $_POST['facebook_name'] ?? [];
        $facebook_costs = $_POST['facebook_cost'] ?? [];
        $facebook_received_amount = $_POST['facebook_received_amount'] ?? [];
        $facebook_invoices = $_FILES['facebook_invoice'] ?? [];
        $existing_facebook_invoices = $_POST['existing_facebook_invoice'] ?? [];

        $facebookStmt = $pdo->prepare("INSERT INTO facebook_advertisements (
    program_id, name, cost, received_amount, invoice_file, created_at
) VALUES (
    :program_id, :name, :cost, :received_amount, :invoice_file, NOW()
)");

        $facebookUploadDir = '../../uploads/facebook_invoices/';
        if (!is_dir($facebookUploadDir)) {
            mkdir($facebookUploadDir, 0777, true);
        }

        foreach ($facebook_names as $i => $name) {
            if (trim($name) !== '') {
                $invoice_file_name = $existing_facebook_invoices[$i] ?? null;
                if (isset($facebook_invoices['name'][$i]) && $facebook_invoices['error'][$i] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($facebook_invoices['name'][$i], PATHINFO_EXTENSION);
                    $invoice_file_name = uniqid('facebook_invoice_') . '.' . $ext;
                    move_uploaded_file($facebook_invoices['tmp_name'][$i], $facebookUploadDir . $invoice_file_name);
                }
                $facebookStmt->execute([
                    ':program_id' => $program_id,
                    ':name' => htmlspecialchars(trim($name)),
                    ':cost' => floatval($facebook_costs[$i] ?? 0),
                    ':received_amount' => floatval($facebook_received_amount[$i] ?? 0),
                    ':invoice_file' => $invoice_file_name,
                ]);
            }
        }

        // === Instagram Advertisements ===
       
$instagram_names = $_POST['instagram_name'] ?? [];
$instagram_costs = $_POST['instagram_cost'] ?? [];
$instagram_received_amounts = $_POST['instagram_received_amount'] ?? [];
$instagram_invoices = $_FILES['instagram_invoice'] ?? [];
$existing_instagram_invoices = $_POST['existing_instagram_invoice'] ?? [];

$instagramStmt = $pdo->prepare("INSERT INTO instagram_advertisements (
    program_id, name, cost, received_amount, invoice_file, created_at
) VALUES (
    :program_id, :name, :cost, :received_amount, :invoice_file, NOW()
)");

foreach ($instagram_names as $i => $name) {
    if (trim($name) !== '') {
        $invoice_file_name = $existing_instagram_invoices[$i] ?? null;
        if (isset($instagram_invoices['name'][$i]) && $instagram_invoices['error'][$i] === UPLOAD_ERR_OK) {
            $ext = pathinfo($instagram_invoices['name'][$i], PATHINFO_EXTENSION);
            $invoice_file_name = uniqid('instagram_invoice_') . '.' . $ext;
            move_uploaded_file($instagram_invoices['tmp_name'][$i], $instagramUploadDir . $invoice_file_name);
        }
        $instagramStmt->execute([
            ':program_id' => $program_id,
            ':name' => htmlspecialchars(trim($name)),
            ':cost' => floatval($instagram_costs[$i] ?? 0),
            ':received_amount' => floatval($instagram_received_amounts[$i] ?? 0),
            ':invoice_file' => $invoice_file_name,
        ]);
    }
}

        // === Other Marketing Details ===
        $literature_by = $_POST['literature_by'] ?? '';
        $literature_cost = $_POST['literature_cost'] ?? 0;

        $other_marketing_material = $_POST['other_marketing_material'] ?? '';
        $marketing_material_cost = $_POST['marketing_material_cost'] ?? 0;

        $other_essential = $_POST['other_essential'] ?? '';
        $other_essential_cost = $_POST['other_essential_cost'] ?? 0;

        $logistic = $_POST['logistic'] ?? '';
        $logistic_cost = $_POST['logistic_cost'] ?? 0;

        $marketing_agency = $_POST['marketing_agency'] ?? '';
        $marketing_agency_cost = $_POST['marketing_agency_cost'] ?? 0;

        $accommodation = $_POST['accommodation'] ?? '';
        $accommodation_cost = $_POST['accommodation_cost'] ?? 0;

        if (
            !empty($literature_by) ||
            !empty($literature_cost) ||
            !empty($other_marketing_material) ||
            !empty($marketing_material_cost) ||
            !empty($other_essential) ||
            !empty($other_essential_cost) ||
            !empty($logistic) ||
            !empty($logistic_cost) ||
            !empty($marketing_agency) ||
            !empty($marketing_agency_cost) ||
            !empty($accommodation) ||
            !empty($accommodation_cost)
        ) {
            $stmt = $pdo->prepare("INSERT INTO other_marketing_details (
                program_id, literature_by, literature_cost, 
                other_marketing_material, marketing_material_cost, 
                other_essential, other_essential_cost, 
                logistic, logistic_cost, 
                marketing_agency, marketing_agency_cost, 
                accommodation, accommodation_cost, 
                created_at
            ) VALUES (
                :program_id, :literature_by, :literature_cost, 
                :other_marketing_material, :marketing_material_cost, 
                :other_essential, :other_essential_cost, 
                :logistic, :logistic_cost, 
                :marketing_agency, :marketing_agency_cost, 
                :accommodation, :accommodation_cost, 
                NOW()
            )");

            $stmt->execute([
                ':program_id' => $program_id,
                ':literature_by' => $literature_by,
                ':literature_cost' => $literature_cost,
                ':other_marketing_material' => $other_marketing_material,
                ':marketing_material_cost' => $marketing_material_cost,
                ':other_essential' => $other_essential,
                ':other_essential_cost' => $other_essential_cost,
                ':logistic' => $logistic,
                ':logistic_cost' => $logistic_cost,
                ':marketing_agency' => $marketing_agency,
                ':marketing_agency_cost' => $marketing_agency_cost,
                ':accommodation' => $accommodation,
                ':accommodation_cost' => $accommodation_cost,
            ]);
        }

        // === Account Information ===
        $account_holder_name = $_POST['account_holder_name'] ?? '';
        $bank_name = $_POST['bank_name'] ?? '';
        $account_number = $_POST['account_number'] ?? '';
        $ifsc_code = $_POST['ifsc_code'] ?? '';
        $branch = $_POST['branch'] ?? '';
        $upi_id = $_POST['upi_id'] ?? '';

        if (
            !empty($account_holder_name) ||
            !empty($bank_name) ||
            !empty($account_number) ||
            !empty($ifsc_code) ||
            !empty($branch) ||
            !empty($upi_id)
        ) {
            $account_stmt = $pdo->prepare("INSERT INTO program_bank_accounts (
                program_id, account_holder_name, bank_name, account_number, ifsc_code, branch, upi_id, created_at
            ) VALUES (
                :program_id, :account_holder_name, :bank_name, :account_number, :ifsc_code, :branch, :upi_id, NOW()
            )");

            $account_stmt->execute([
                ':program_id' => $program_id,
                ':account_holder_name' => $account_holder_name,
                ':bank_name' => $bank_name,
                ':account_number' => $account_number,
                ':ifsc_code' => $ifsc_code,
                ':branch' => $branch,
                ':upi_id' => $upi_id,
            ]);
        }

        // === Commit All ===
        $pdo->commit();

        header("Location: index.php?message=" . urlencode("Marketing details updated successfully."));
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Marketing Update Error: " . $e->getMessage());
        die("An error occurred while updating marketing details.");
    }
} else {
    header("Location: index.php?message=" . urlencode("Invalid request method."));
    exit;
}
