<?php
require '../../config/config.php';
require '../../helpers/security.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Program Management');

// Get program ID from GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid program ID.");
}
$programId = intval($_GET['id']);

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
    die("Program not found.");
}

// Format dates
$startDateTime = new DateTime($program['start_datetime']);
$endDateTime = new DateTime($program['end_datetime']);

// Format cost
$costDisplay = $program['is_free'] ? 'Free' : '$' . number_format($program['cost'], 2);

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Details - <?= htmlspecialchars($program['title']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background: #ffffff;
            font-size: 14px;
        }

        .print-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm 20mm;
            background: white;
            min-height: 100vh;
        }

        /* Header Section */
        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            margin-top: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }

        .logo-container {
            width:40%;

        }

        .logo-container img {
            width: 100%;
            height: auto;
        }

        .header-info {
            text-align: right;
            font-size: 12px;
            color: #6b7280;
        }

        .header-info .date {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .header-info .address {
            font-style: normal;
        }

        .report-title {
            text-align: center;
            margin: 20px 0 30px;
            position: relative;
        }

        .report-title h1 {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }

        .report-title .subtitle {
            font-size: 14px;
            color: #6b7280;
            font-weight: 400;
        }

        .program-meta {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .program-number {
            background: #f3f4f6;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            color: #374151;
            font-size: 12px;
        }

        /* Status Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 20px;
            color: white;
        }

        .badge-success {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .badge-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        /* Section Styling */
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            position: relative;
        }

        .section-title::before {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 50px;
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        }

        /* Grid Layout */
        .grid {
            display: grid;
            gap: 15px;
        }

        .grid-2 {
            grid-template-columns: 1fr 1fr;
        }

        /* Info Cards */
        .info-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
        }

        .info-item {
            margin-bottom: 12px;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 14px;
            font-weight: 500;
            color: #111827;
            line-height: 1.5;
        }

        /* Program Image */
        .program-image {
            width: 100%;
            max-width: 250px;
            height: auto;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        /* Tables */
        .modern-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            font-size: 12px;
        }

        .modern-table thead {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        }

        .modern-table th {
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e5e7eb;
        }

        .modern-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }

        .modern-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Marketing Subsections */
        .marketing-subsection {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .subsection-title {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        /* Interview Details Special Styling */
        .interview-details {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            font-size: 13px;
            line-height: 1.6;
            color: #475569;
            border-left: 4px solid #3b82f6;
        }

        /* Account Information */
        .account-info {
            background: #fefefe;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            border-left: 4px solid #10b981;
            font-size: 13px;
        }

        .account-list {
            list-style: none;
            padding: 0;
        }

        .account-list li {
            padding: 6px 0;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .account-list li:last-child {
            border-bottom: none;
        }

        .account-list strong {
            color: #374151;
            font-weight: 500;
            min-width: 120px;
        }

        /* Print-specific styles */
        .no-print {
            display: none;
        }

        @page {
            size: A4;
            margin: 15mm 20mm;
        }

        @media print {
            body {
                font-size: 12px;
                line-height: 1.4;
                background: white;
            }

            .print-container {
                padding: 0;
                margin: 0;
                max-width: none;
            }

            .print-header {
                margin-bottom: 15px;
                padding-bottom: 10px;
            }

            .report-title {
                margin: 15px 0 20px;
            }

            .report-title h1 {
                font-size: 20px;
            }

            .section {
                margin-bottom: 20px;
            }

            .section-title {
                font-size: 16px;
                margin-bottom: 12px;
            }

            /* Ensure tables don't break across pages */
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            thead {
                display: table-header-group;
            }

            /* Force sections to stay together */
            .section, .marketing-subsection {
                page-break-inside: avoid;
            }

            /* Ensure colors print correctly */
            .badge-success {
                background: #10b981 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge-danger {
                background: #ef4444 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .section-title::before {
                background: #3b82f6 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }

            .program-meta {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="print-container">
        <!-- Header with Logo and Info -->
        <div class="print-header">
            <div class="logo-container">
                <img src="http://localhost/Brahmakumari/views/auth/assets/images/bk-logo.png" alt="Brahma Kumaris Logo">
            </div>
            <div class="header-info">
                <div class="date"><?= date('F j, Y') ?></div>
                <div class="address">
                    Brahma Kumaris<br>
                    <?= htmlspecialchars($program['centre_name'] ?? 'Center Name') ?><br>
                    <?= htmlspecialchars($program['regional_name'] ?? 'Region') ?>, 
                    <?= htmlspecialchars($program['country_name'] ?? 'Country') ?>
                </div>
            </div>
        </div>

        <!-- Report Title Section -->
        <div class="report-title">
            <h1><?= htmlspecialchars($program['title']) ?></h1>
            <div class="subtitle">Program Details Report</div>
            <div class="program-meta">
                <div class="program-number">
                    Program #<?= htmlspecialchars($program['program_number']) ?>
                </div>
                <span class="badge <?= $statusBadge ?>">
                    <?= ucfirst($program['status']) ?>
                </span>
            </div>
        </div>

        <!-- Program Overview Section -->
        <section class="section">
            <h2 class="section-title">Program Overview</h2>
            <div class="grid grid-2">
                <div class="info-card">
                    <?php if (!empty($program['program_img'])): ?>
                        <img src="../../uploads/programs/<?= htmlspecialchars($program['program_img']) ?>"
                            alt="Program Image" class="program-image">
                    <?php endif; ?>
                </div>

                <div class="info-card">
                    <div class="info-item">
                        <div class="info-label">Centre</div>
                        <div class="info-value"><?= htmlspecialchars($program['centre_name'] ?? 'N/A') ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Location</div>
                        <div class="info-value">
                            <?= htmlspecialchars($program['venue']) ?><br>
                            <?= htmlspecialchars($program['regional_name'] ?? '') ?>,
                            <?= htmlspecialchars($program['country_name'] ?? '') ?>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Instructor</div>
                        <div class="info-value"><?= htmlspecialchars($program['instructor_name']) ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Cost</div>
                        <div class="info-value"><?= $costDisplay ?></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-2" style="margin-top: 15px;">
                <div class="info-card">
                    <div class="info-item">
                        <div class="info-label">Start Date & Time</div>
                        <div class="info-value"><?= $startDateTime->format('F j, Y • g:i A') ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">End Date & Time</div>
                        <div class="info-value"><?= $endDateTime->format('F j, Y • g:i A') ?></div>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-item">
                        <div class="info-label">Participants</div>
                        <div class="info-value">
                            <?= $program['current_participants'] ?> / <?= $program['max_participants'] ?> enrolled
                        </div>
                    </div>

                    <?php if (!empty($program['marketing_methods'])): ?>
                        <div class="info-item">
                            <div class="info-label">Marketing Methods</div>
                            <div class="info-value"><?= htmlspecialchars($program['marketing_methods']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($program['description'])): ?>
                <div class="info-card" style="margin-top: 15px;">
                    <div class="info-item">
                        <div class="info-label">Program Description</div>
                        <div class="info-value">
                            <?= nl2br(htmlspecialchars($program['description'])) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <!-- Bank Account Information -->
        <?php if (
            !empty($marketing['account']) &&
            (!empty($marketing['account']['account_holder_name']) ||
                !empty($marketing['account']['bank_name']) ||
                !empty($marketing['account']['account_number']) ||
                !empty($marketing['account']['ifsc_code']) ||
                !empty($marketing['account']['branch']) ||
                !empty($marketing['account']['upi_id']))
        ): ?>
            <section class="section">
                <h2 class="section-title">Payment Information</h2>
                <div class="account-info">
                    <ul class="account-list">
                        <?php if (!empty($marketing['account']['account_holder_name'])): ?>
                            <li>
                                <strong>Account Holder:</strong>
                                <span><?= htmlspecialchars($marketing['account']['account_holder_name']) ?></span>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($marketing['account']['bank_name'])): ?>
                            <li>
                                <strong>Bank Name:</strong>
                                <span><?= htmlspecialchars($marketing['account']['bank_name']) ?></span>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($marketing['account']['account_number'])): ?>
                            <li>
                                <strong>Account Number:</strong>
                                <span><?= htmlspecialchars($marketing['account']['account_number']) ?></span>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($marketing['account']['ifsc_code'])): ?>
                            <li>
                                <strong>IFSC Code:</strong>
                                <span><?= htmlspecialchars($marketing['account']['ifsc_code']) ?></span>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($marketing['account']['branch'])): ?>
                            <li>
                                <strong>Branch:</strong>
                                <span><?= htmlspecialchars($marketing['account']['branch']) ?></span>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($marketing['account']['upi_id'])): ?>
                            <li>
                                <strong>UPI ID:</strong>
                                <span><?= htmlspecialchars($marketing['account']['upi_id']) ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </section>
        <?php endif; ?>

        <!-- Marketing Details Section -->
        <section class="section">
            <h2 class="section-title">Marketing Campaign Details</h2>

            <!-- Pamphlet Marketing -->
            <?php if (!empty($marketing['pamphlets'])): ?>
                <div class="marketing-subsection">
                    <h3 class="subsection-title">Pamphlet Marketing</h3>
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Designer</th>
                                <th>Design Cost</th>
                                <th>Printer</th>
                                <th>Print Cost</th>
                                <th>Distributor</th>
                                <th>Distribution Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($marketing['pamphlets'] as $pamphlet): ?>
                                <tr>
                                    <td><?= htmlspecialchars($pamphlet['pamphlet_designer_name']) ?></td>
                                    <td><?= htmlspecialchars($pamphlet['pamphlet_designer_cost']) ?></td>
                                    <td><?= htmlspecialchars($pamphlet['pamphlet_printer_name']) ?></td>
                                    <td><?= htmlspecialchars($pamphlet['pamphlet_printing_cost']) ?></td>
                                    <td><?= htmlspecialchars($pamphlet['pamphlet_distributor_name']) ?></td>
                                    <td><?= htmlspecialchars($pamphlet['pamphlet_distribution_cost']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Radio Advertising -->
            <?php if (!empty($marketing['radio'])): ?>
                <div class="marketing-subsection">
                    <h3 class="subsection-title">Radio Advertising</h3>
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Station Name</th>
                                <th>Cost</th>
                                <th>Contact</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($marketing['radio'] as $radio): ?>
                                <tr>
                                    <td><?= htmlspecialchars($radio['name']) ?></td>
                                    <td><?= htmlspecialchars($radio['cost']) ?></td>
                                    <td><?= htmlspecialchars($radio['contact']) ?></td>
                                    <td><?= htmlspecialchars($radio['remarks']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Television Advertising -->
            <?php if (!empty($marketing['television'])): ?>
                <div class="marketing-subsection">
                    <h3 class="subsection-title">Television Advertising</h3>
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Channel Name</th>
                                <th>Cost</th>
                                <th>Contact</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($marketing['television'] as $tv): ?>
                                <tr>
                                    <td><?= htmlspecialchars($tv['name']) ?></td>
                                    <td><?= htmlspecialchars($tv['cost']) ?></td>
                                    <td><?= htmlspecialchars($tv['contact']) ?></td>
                                    <td><?= htmlspecialchars($tv['remarks']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Interview Details -->
            <?php if (!empty($marketing['interview']['details'])): ?>
                <div class="marketing-subsection">
                    <h3 class="subsection-title">Interview Details</h3>
                    <div class="interview-details">
                        <?= nl2br(htmlspecialchars($marketing['interview']['details'])) ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Newspaper Advertising -->
            <?php if (!empty($marketing['newspaper'])): ?>
                <div class="marketing-subsection">
                    <h3 class="subsection-title">Newspaper Advertising</h3>
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Publication</th>
                                <th>Cost</th>
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
                                    <td><?= htmlspecialchars($news['cost']) ?></td>
                                    <td><?= htmlspecialchars($news['duration']) ?></td>
                                    <td><?= htmlspecialchars($news['ad_size']) ?></td>
                                    <td><?= htmlspecialchars($news['contact']) ?></td>
                                    <td><?= htmlspecialchars($news['remarks']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Billboard Advertising -->
            <?php if (!empty($marketing['billboard'])): ?>
                <div class="marketing-subsection">
                    <h3 class="subsection-title">Billboard Advertising</h3>
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Agency Name</th>
                                <th>Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($marketing['billboard'] as $bill): ?>
                                <tr>
                                    <td><?= htmlspecialchars($bill['agency_name']) ?></td>
                                    <td><?= htmlspecialchars($bill['cost']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Social Media Marketing -->
            <?php if (!empty($marketing['facebook']) || !empty($marketing['instagram'])): ?>
                <div class="marketing-subsection">
                    <h3 class="subsection-title">Social Media Marketing</h3>

                    <?php if (!empty($marketing['facebook'])): ?>
                        <h4 style="margin: 12px 0 8px 0; color: #1877f2; font-weight: 600;">Facebook Advertising</h4>
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Campaign Name</th>
                                    <th>Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($marketing['facebook'] as $fb): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($fb['name']) ?></td>
                                        <td><?= htmlspecialchars($fb['cost']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <?php if (!empty($marketing['instagram'])): ?>
                        <h4 style="margin: 12px 0 8px 0; color: #e4405f; font-weight: 600;">Instagram Advertising</h4>
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Campaign Name</th>
                                    <th>Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($marketing['instagram'] as $insta): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($insta['name']) ?></td>
                                        <td><?= htmlspecialchars($insta['cost']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Other Marketing Details -->
            <?php
            $other = $marketing['other'];
            $hasOther = !empty($other['literature_by']) || !empty($other['literature_cost']) ||
                !empty($other['other_marketing_material']) || !empty($other['marketing_material_cost']) ||
                !empty($other['other_essential']) || !empty($other['other_essential_cost']) ||
                !empty($other['logistic']) || !empty($other['logistic_cost']) ||
                !empty($other['marketing_agency']) || !empty($other['marketing_agency_cost']) ||
                !empty($other['accommodation']) || !empty($other['accommodation_cost']);
            ?>
            <?php if ($hasOther): ?>
                <div class="marketing-subsection">
                    <h3 class="subsection-title">Additional Marketing Activities</h3>
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Marketing Type</th>
                                <th>Details</th>
                                <th>Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($other['literature_by']) || !empty($other['literature_cost'])): ?>
                                <tr>
                                    <td>Literature</td>
                                    <td><?= htmlspecialchars($other['literature_by']) ?></td>
                                    <td><?= htmlspecialchars($other['literature_cost']) ?></td>
                                </tr>
                            <?php endif; ?>

                            <?php if (!empty($other['other_marketing_material']) || !empty($other['marketing_material_cost'])): ?>
                                <tr>
                                    <td>Marketing Materials</td>
                                    <td><?= htmlspecialchars($other['other_marketing_material']) ?></td>
                                    <td><?= htmlspecialchars($other['marketing_material_cost']) ?></td>
                                </tr>
                            <?php endif; ?>

                            <?php if (!empty($other['other_essential']) || !empty($other['other_essential_cost'])): ?>
                                <tr>
                                    <td>Other Essentials</td>
                                    <td><?= htmlspecialchars($other['other_essential']) ?></td>
                                    <td><?= htmlspecialchars($other['other_essential_cost']) ?></td>
                                </tr>
                            <?php endif; ?>

                            <?php if (!empty($other['logistic']) || !empty($other['logistic_cost'])): ?>
                                <tr>
                                    <td>Logistics</td>
                                    <td><?= htmlspecialchars($other['logistic']) ?></td>
                                    <td><?= htmlspecialchars($other['logistic_cost']) ?></td>
                                </tr>
                            <?php endif; ?>

                            <?php if (!empty($other['marketing_agency']) || !empty($other['marketing_agency_cost'])): ?>
                                <tr>
                                    <td>Marketing Agency</td>
                                    <td><?= htmlspecialchars($other['marketing_agency']) ?></td>
                                    <td><?= htmlspecialchars($other['marketing_agency_cost']) ?></td>
                                </tr>
                            <?php endif; ?>

                            <?php if (!empty($other['accommodation']) || !empty($other['accommodation_cost'])): ?>
                                <tr>
                                    <td>Accommodation</td>
                                    <td><?= htmlspecialchars($other['accommodation']) ?></td>
                                    <td><?= htmlspecialchars($other['accommodation_cost']) ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <!-- Footer -->
        <div style="text-align: center; font-size: 11px; color: #6b7280; margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb;">
            Generated on <?= date('F j, Y \a\t g:i A') ?> | Brahma Kumaris Management System
        </div>
    </div>
</body>

</html>