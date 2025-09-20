<?php
require '../../../config/config.php';
require_once '../../../config/functions.php';
checkModuleAccess($pdo, 'Program Management');

$showPrice = canUserViewPrice($pdo, 'Program Management');

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

// Fetch all sessions for this program
$sessionsStmt = $pdo->prepare("SELECT session_name, session_start, session_end FROM program_sessions_times WHERE program_id = ? ORDER BY session_start ASC");
$sessionsStmt->execute([$programId]);
$sessions = $sessionsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get min/max session times for summary
$startDateTime = null;
$endDateTime = null;
if (!empty($sessions)) {
  $startDateTime = new DateTime($sessions[0]['session_start']);
  $endDateTime = new DateTime($sessions[count($sessions) - 1]['session_end']);
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

// Calculate total marketing expense for display
$totalMarketingExpense = 0;
$stmt = $pdo->prepare("SELECT SUM(cost) FROM billboard_advertisements WHERE program_id = ?");
$stmt->execute([$programId]);
$totalMarketingExpense += floatval($stmt->fetchColumn());

$stmt = $pdo->prepare("SELECT SUM(cost) FROM facebook_advertisements WHERE program_id = ?");
$stmt->execute([$programId]);
$totalMarketingExpense += floatval($stmt->fetchColumn());

$stmt = $pdo->prepare("SELECT SUM(cost) FROM instagram_advertisements WHERE program_id = ?");
$stmt->execute([$programId]);
$totalMarketingExpense += floatval($stmt->fetchColumn());

$stmt = $pdo->prepare("SELECT SUM(cost) FROM newspaper_advertisements WHERE program_id = ?");
$stmt->execute([$programId]);
$totalMarketingExpense += floatval($stmt->fetchColumn());

$stmt = $pdo->prepare("SELECT SUM(cost) FROM radio_advertisements WHERE program_id = ?");
$stmt->execute([$programId]);
$totalMarketingExpense += floatval($stmt->fetchColumn());

$stmt = $pdo->prepare("SELECT SUM(cost) FROM television_advertisements WHERE program_id = ?");
$stmt->execute([$programId]);
$totalMarketingExpense += floatval($stmt->fetchColumn());

$stmt = $pdo->prepare("SELECT SUM(pamphlet_designer_cost) + SUM(pamphlet_printing_cost) + SUM(pamphlet_distribution_cost) FROM program_pamphlets WHERE program_id = ?");
$stmt->execute([$programId]);
$totalMarketingExpense += floatval($stmt->fetchColumn());

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

// Fetch actual participant count for this program
$stmt = $pdo->prepare("SELECT COUNT(*) FROM participants WHERE program_id = ?");
$stmt->execute([$programId]);
$actualParticipantCount = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Program Print</title>
  <link rel="stylesheet" href="style.css" type="text/css" media="all" />
</head>
<body>
  <div>
    <!-- Header Section -->
    <div class="py-4">
      <div class="px-14 py-6">
        <table class="w-full border-collapse border-spacing-0">
          <tbody>
            <tr>
              <td class="w-full align-top">
                <div>
                  <img src="http://localhost/Brahmakumari/views/auth/assets/images/bk-logo.png" style="height: 60px;" />
                </div>
              </td>
              <td class="align-top">
                <div class="text-sm">
                  <table class="border-collapse border-spacing-0">
                    <tbody>
                      <tr>
                        <td class="border-r pr-4">
                          <div>
                            <p class="whitespace-nowrap text-slate-400 text-right">Date</p>
                            <p class="whitespace-nowrap font-bold text-main text-right"><?= date('F j, Y') ?></p>
                          </div>
                        </td>
                        <td class="pl-4">
                          <div>
                            <p class="whitespace-nowrap text-slate-400 text-right">Program Number</p>
                            <p class="whitespace-nowrap font-bold text-main text-right"><?= htmlspecialchars($program['program_number']) ?></p>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Program Details Section -->
      <div class="bg-slate-100 px-14 py-6 text-sm">
        <table class="w-full border-collapse border-spacing-0">
          <tbody>
            <tr>
              <td class="w-1/3 align-top">
                <div class="text-sm text-neutral-600">
                  <p class="font-bold"><?= htmlspecialchars($program['title']) ?></p>
                  <p>Program Number: <?= htmlspecialchars($program['program_number']) ?></p>
                  <p>Location: <?= htmlspecialchars($program['venue']) ?><br>
                    <?= htmlspecialchars($program['regional_name']) ?>, <?= htmlspecialchars($program['country_name']) ?>
                  </p>
                  <p>Centre: <?= htmlspecialchars($program['centre_name']) ?></p>
                  <p>Program Generated Date: <?= date('F j, Y', strtotime($program['created_at'])) ?></p>
                  <?php
                  if ($startDateTime && $endDateTime) {
                    $startDate = $startDateTime->format('F j, Y');
                    $endDate = $endDateTime->format('F j, Y');
                    if ($startDate === $endDate) {
                      $startTime = $startDateTime->format('g:i A');
                      $endTime = $endDateTime->format('g:i A');
                      echo "<p class='whitespace-nowrap'>Date & Time: {$startDate} • {$startTime} - {$endTime}</p>";
                    } else {
                      echo "<p class='whitespace-nowrap'>Start Date & Time: {$startDateTime->format('F j, Y • g:i A')}</p>";
                      echo "<p class='whitespace-nowrap'>End Date & Time: {$endDateTime->format('F j, Y • g:i A')}</p>";
                    }
                  } else {
                    echo "<p class='whitespace-nowrap'>No session schedule available.</p>";
                  }
                  ?>
                  <p>Instructor: <?= htmlspecialchars($program['instructor_name']) ?></p>
                  <p>Participants: <?= $actualParticipantCount ?> / <?= $program['max_participants'] ?> enrolled</p>
                  <?php if (!empty($program['marketing_methods'])): ?>
                    <p>Marketing Methods: <?= htmlspecialchars($program['marketing_methods']) ?></p>
                  <?php endif; ?>
                  <?php if ($showPrice): ?>
                    <div class="alert alert-info mb-3">
                      <strong>Total Marketing Cost:</strong>
                      ₹<?= number_format($totalMarketingExpense, 2) ?>
                    </div>
                  <?php endif; ?>
                </div>
              </td>
              <td class="w-1/2 align-top text-right">
                <div class="text-sm text-neutral-600" style="display: flex; justify-content: end;">
                  <?php if (!empty($program['program_img'])): ?>
                    <img style="width: 200px;" src="http://localhost/Brahmakumari/uploads/programs/<?= htmlspecialchars($program['program_img']) ?>" alt="">
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <?php if (!empty($program['description'])): ?>
          <p style='color:gray'>Description: <?= nl2br(htmlspecialchars($program['description'])) ?></p>
        <?php endif; ?>

        <!-- All Sessions Table -->
        <?php if (!empty($sessions)): ?>
          <div class="px-0 py-4 text-sm text-neutral-700">
            <p class="font-bold text-left">All Sessions</p>
            <table class="w-full border-collapse border-spacing-0">
              <thead>
                <tr>
                  <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">#</td>
                  <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Session Name</td>
                  <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Start Date & Time</td>
                  <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">End Date & Time</td>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($sessions as $i => $sess): ?>
                  <tr>
                    <td class="border-b py-3 pl-3"><?= $i + 1 ?>.</td>
                    <td class="border-b py-3 pl-2"><?= htmlspecialchars($sess['session_name']) ?></td>
                    <td class="border-b py-3 pl-2"><?= date('M d, Y - h:i A', strtotime($sess['session_start'])) ?></td>
                    <td class="border-b py-3 pl-2"><?= date('M d, Y - h:i A', strtotime($sess['session_end'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

      <!-- Pamphlet Marketing Table -->
      <?php
      $showPrice = canUserViewPrice($pdo, 'Program Management');
      ?>
      <?php if (!empty($marketing['pamphlets'])): ?>
        <div class="px-14 py-10 text-sm text-neutral-700">
          <p class="font-bold text-left">Pamphlet Marketing</p>
          <table class="w-full border-collapse border-spacing-0">
            <thead>
              <tr>
                <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">#</td>
                <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Designer</td>
                <?php if ($showPrice): ?><td class="border-b-2 border-main pb-3 pl-2 text-right font-bold text-main">Design Cost</td><?php endif; ?>
                <td class="border-b-2 border-main pb-3 pl-2 text-center font-bold text-main">Printer</td>
                <?php if ($showPrice): ?><td class="border-b-2 border-main pb-3 pl-2 text-center font-bold text-main">Print Cost</td><?php endif; ?>
                <td class="border-b-2 border-main pb-3 pl-2 text-right font-bold text-main">Distributor</td>
                <?php if ($showPrice): ?><td class="border-b-2 border-main pb-3 pl-2 pr-3 text-right font-bold text-main">Distribution Cost</td><?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($marketing['pamphlets'] as $i => $pamphlet): ?>
                <tr>
                  <td class="border-b py-3 pl-3"><?= $i + 1 ?>.</td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($pamphlet['pamphlet_designer_name']) ?></td>
                  <?php if ($showPrice): ?><td class="border-b py-3 pl-2 text-right"><?= htmlspecialchars($pamphlet['pamphlet_designer_cost']) ?></td><?php endif; ?>
                  <td class="border-b py-3 pl-2 text-center"><?= htmlspecialchars($pamphlet['pamphlet_printer_name']) ?></td>
                  <?php if ($showPrice): ?><td class="border-b py-3 pl-2 text-center"><?= htmlspecialchars($pamphlet['pamphlet_printing_cost']) ?></td><?php endif; ?>
                  <td class="border-b py-3 pl-2 text-right"><?= htmlspecialchars($pamphlet['pamphlet_distributor_name']) ?></td>
                  <?php if ($showPrice): ?><td class="border-b py-3 pl-2 pr-3 text-right"><?= htmlspecialchars($pamphlet['pamphlet_distribution_cost']) ?></td><?php endif; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <!-- Radio Advertisements -->
      <?php if (!empty($marketing['radio'])): ?>
        <div class="px-14 py-4 text-sm text-neutral-700">
          <p class="font-bold text-left">Radio Advertisements</p>
          <table class="w-full border-collapse border-spacing-0">
            <thead>
              <tr>
                <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">Station Name</td>
                <?php if ($showPrice): ?><td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Cost</td><?php endif; ?>
                <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Contact</td>
                <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Remarks</td>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($marketing['radio'] as $radio): ?>
                <tr>
                  <td class="border-b py-3 pl-3"><?= htmlspecialchars($radio['name']) ?></td>
                  <?php if ($showPrice): ?><td class="border-b py-3 pl-2"><?= htmlspecialchars($radio['cost']) ?></td><?php endif; ?>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($radio['contact']) ?></td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($radio['remarks']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <!-- Television Advertisements -->
      <?php if (!empty($marketing['television'])): ?>
        <div class="px-14 py-4 text-sm text-neutral-700">
          <p class="font-bold text-left">Television Advertisements</p>
          <table class="w-full border-collapse border-spacing-0">
            <thead>
              <tr>
                <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">Channel Name</td>
                <?php if ($showPrice): ?><td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Cost</td><?php endif; ?>
                <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Contact</td>
                <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Remarks</td>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($marketing['television'] as $tv): ?>
                <tr>
                  <td class="border-b py-3 pl-3"><?= htmlspecialchars($tv['name']) ?></td>
                  <?php if ($showPrice): ?><td class="border-b py-3 pl-2"><?= htmlspecialchars($tv['cost']) ?></td><?php endif; ?>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($tv['contact']) ?></td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($tv['remarks']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <!-- Newspaper Advertisements -->
      <?php if (!empty($marketing['newspaper'])): ?>
        <div class="px-14 py-4 text-sm text-neutral-700">
          <p class="font-bold text-left">Newspaper Advertisements</p>
          <table class="w-full border-collapse border-spacing-0">
            <thead>
              <tr>
                <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">Publication</td>
                <?php if ($showPrice): ?><td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Cost</td><?php endif; ?>
                <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Duration</td>
                <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Ad Size</td>
                <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Contact</td>
                <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Remarks</td>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($marketing['newspaper'] as $news): ?>
                <tr>
                  <td class="border-b py-3 pl-3"><?= htmlspecialchars($news['name']) ?></td>
                  <?php if ($showPrice): ?><td class="border-b py-3 pl-2"><?= htmlspecialchars($news['cost']) ?></td><?php endif; ?>

                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($news['duration']) ?></td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($news['ad_size']) ?></td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($news['contact']) ?></td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($news['remarks']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <!-- Billboard Advertisements -->
      <?php if (!empty($marketing['billboard'])): ?>
        <div class="px-14 py-4 text-sm text-neutral-700">
          <p class="font-bold text-left">Billboard Advertisements</p>
          <table class="w-full border-collapse border-spacing-0">
            <thead>
              <tr>
                <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">Agency Name</td>
                <?php if ($showPrice): ?>
                  <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Cost</td>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($marketing['billboard'] as $bill): ?>
                <tr>
                  <td class="border-b py-3 pl-3"><?= htmlspecialchars($bill['agency_name']) ?></td>
                  <?php if ($showPrice): ?>
                    <td class="border-b py-3 pl-2"><?= htmlspecialchars($bill['cost']) ?></td>
                  <?php endif; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <!-- Facebook Advertisements -->
      <?php if (!empty($marketing['facebook'])): ?>
        <div class="px-14 py-4 text-sm text-neutral-700">
          <p class="font-bold text-left">Facebook Advertisements</p>
          <table class="w-full border-collapse border-spacing-0">
            <thead>
              <tr>
                <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">Campaign Name</td>
                <?php if ($showPrice): ?><td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Cost</td><?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($marketing['facebook'] as $fb): ?>
                <tr>
                  <td class="border-b py-3 pl-3"><?= htmlspecialchars($fb['name']) ?></td>
                  <?php if ($showPrice): ?><td class="border-b py-3 pl-2"><?= htmlspecialchars($fb['cost']) ?></td><?php endif; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <!-- Instagram Advertisements -->
      <?php if (!empty($marketing['instagram'])): ?>
        <div class="px-14 py-4 text-sm text-neutral-700">
          <p class="font-bold text-left">Instagram Advertisements</p>
          <table class="w-full border-collapse border-spacing-0">
            <thead>
              <tr>
                <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">Campaign Name</td>
                <?php if ($showPrice): ?><td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Cost</td><?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($marketing['instagram'] as $insta): ?>
                <tr>
                  <td class="border-b py-3 pl-3"><?= htmlspecialchars($insta['name']) ?></td>
                  <?php if ($showPrice): ?><td class="border-b py-3 pl-2"><?= htmlspecialchars($insta['cost']) ?></td><?php endif; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
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
        <div class="px-14 py-4 text-sm text-neutral-700">
          <p class="font-bold text-left">Other Marketing Details</p>
          <table class="w-full border-collapse border-spacing-0">
            <thead>
              <tr>
                <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">Type</td>
                <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Value</td>
                <?php if ($showPrice): ?><td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Cost</td><?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($other['literature_by']) || !empty($other['literature_cost'])): ?>
                <tr>
                  <td class="border-b py-3 pl-3">Literature By</td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($other['literature_by']) ?></td>
                  <?php if ($showPrice): ?><td class="border-b py-3 pl-2"><?= htmlspecialchars($other['literature_cost']) ?></td><?php endif; ?>
                </tr>
              <?php endif; ?>
              <?php if (!empty($other['other_marketing_material']) || !empty($other['marketing_material_cost'])): ?>
                <tr>
                  <td class="border-b py-3 pl-3">Other Marketing Material</td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($other['other_marketing_material']) ?></td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($other['marketing_material_cost']) ?></td>
                </tr>
              <?php endif; ?>
              <?php if (!empty($other['other_essential']) || !empty($other['other_essential_cost'])): ?>
                <tr>
                  <td class="border-b py-3 pl-3">Other Essentials</td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($other['other_essential']) ?></td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($other['other_essential_cost']) ?></td>
                </tr>
              <?php endif; ?>
              <?php if (!empty($other['logistic']) || !empty($other['logistic_cost'])): ?>
                <tr>
                  <td class="border-b py-3 pl-3">Logistic</td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($other['logistic']) ?></td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($other['logistic_cost']) ?></td>
                </tr>
              <?php endif; ?>
              <?php if (!empty($other['marketing_agency']) || !empty($other['marketing_agency_cost'])): ?>
                <tr>
                  <td class="border-b py-3 pl-3">Marketing Agency</td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($other['marketing_agency']) ?></td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($other['marketing_agency_cost']) ?></td>
                </tr>
              <?php endif; ?>
              <?php if (!empty($other['accommodation']) || !empty($other['accommodation_cost'])): ?>
                <tr>
                  <td class="border-b py-3 pl-3">Accommodation</td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($other['accommodation']) ?></td>
                  <td class="border-b py-3 pl-2"><?= htmlspecialchars($other['accommodation_cost']) ?></td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <!-- Payment Details -->
      <?php if (!empty($marketing['account'])): ?>
        <div class="px-14 text-sm text-neutral-700">
          <p class="text-main font-bold">PAYMENT DETAILS</p>
          <?php if (!empty($marketing['account']['bank_name'])): ?>
            <p><?= htmlspecialchars($marketing['account']['bank_name']) ?></p>
          <?php endif; ?>
          <?php if (!empty($marketing['account']['ifsc_code'])): ?>
            <p>Bank/Sort Code: <?= htmlspecialchars($marketing['account']['ifsc_code']) ?></p>
          <?php endif; ?>
          <?php if (!empty($marketing['account']['account_number'])): ?>
            <p>Account Number: <?= htmlspecialchars($marketing['account']['account_number']) ?></p>
          <?php endif; ?>
          <p>Payment Reference: <?= htmlspecialchars($program['program_number']) ?></p>
        </div>
      <?php endif; ?>

      <!-- Notes Section -->
      <div class="px-14 py-10 text-sm text-neutral-700">
        <p class="text-main font-bold">Notes</p>
        <p class="italic">This is a system-generated print. For any queries, contact the program administrator.</p>
      </div>

      <!-- Footer -->
      <footer class="fixed bottom-0 left-0 bg-slate-100 w-full text-neutral-600 text-center text-xs py-3">
        Brahma Kumaris | info@company.com | +1-202-555-0106
      </footer>
    </div>
  </div>
  <script>
    window.onload = function() {
      window.print();
    };
  </script>
</body>
</html>

<!-- List All Invoices After Full Details -->
<div class="px-14 py-10 text-sm text-neutral-700">
  <p class="font-bold text-left">All Marketing Invoices</p>
  <table class="w-full border-collapse border-spacing-0">
    <thead>
      <tr>
        <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">Type</td>
        <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Description</td>
        <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Invoice</td>
      </tr>
    </thead>
    <tbody>
      <!-- Pamphlet Invoices -->
      <?php foreach ($marketing['pamphlets'] as $pamphlet): ?>
        <?php if (!empty($pamphlet['pamphlet_designer_invoice'])): ?>
          <tr>
            <td>Pamphlet Designer</td>
            <td><?= htmlspecialchars($pamphlet['pamphlet_designer_name']) ?></td>
            <td>
              <a href="../../../uploads/pamphlets/<?= htmlspecialchars($pamphlet['pamphlet_designer_invoice']) ?>" target="_blank">View Invoice</a>
            </td>
          </tr>
        <?php endif; ?>
        <?php if (!empty($pamphlet['pamphlet_printing_invoice'])): ?>
          <tr>
            <td>Pamphlet Printing</td>
            <td><?= htmlspecialchars($pamphlet['pamphlet_printer_name']) ?></td>
            <td>
              <a href="../../../uploads/pamphlets/<?= htmlspecialchars($pamphlet['pamphlet_printing_invoice']) ?>" target="_blank">View Invoice</a>
            </td>
          </tr>
        <?php endif; ?>
        <?php if (!empty($pamphlet['pamphlet_distribution_invoice'])): ?>
          <tr>
            <td>Pamphlet Distribution</td>
            <td><?= htmlspecialchars($pamphlet['pamphlet_distributor_name']) ?></td>
            <td>
              <a href="../../../uploads/pamphlets/<?= htmlspecialchars($pamphlet['pamphlet_distribution_invoice']) ?>" target="_blank">View Invoice</a>
            </td>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>

      <!-- Radio Invoices -->
      <?php foreach ($marketing['radio'] as $radio): ?>
        <?php if (!empty($radio['invoice_file'])): ?>
          <tr>
            <td>Radio Advertisement</td>
            <td><?= htmlspecialchars($radio['name']) ?></td>
            <td>
              <a href="../../../uploads/radio_invoices/<?= htmlspecialchars($radio['invoice_file']) ?>" target="_blank">View Invoice</a>
            </td>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>

      <!-- Television Invoices -->
      <?php foreach ($marketing['television'] as $tv): ?>
        <?php if (!empty($tv['invoice_file'])): ?>
          <tr>
            <td>Television Advertisement</td>
            <td><?= htmlspecialchars($tv['name']) ?></td>
            <td>
              <a href="../../../uploads/television_invoices/<?= htmlspecialchars($tv['invoice_file']) ?>" target="_blank">View Invoice</a>
            </td>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>

      <!-- Newspaper Invoices -->
      <?php foreach ($marketing['newspaper'] as $news): ?>
        <?php if (!empty($news['invoice_file'])): ?>
          <tr>
            <td>Newspaper Advertisement</td>
            <td><?= htmlspecialchars($news['name']) ?></td>
            <td>
              <a href="../../../uploads/newspaper_invoices/<?= htmlspecialchars($news['invoice_file']) ?>" target="_blank">View Invoice</a>
            </td>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>

      <!-- Billboard Invoices -->
      <?php foreach ($marketing['billboard'] as $bill): ?>
        <?php if (!empty($bill['invoice_file'])): ?>
          <tr>
            <td>Billboard Advertisement</td>
            <td><?= htmlspecialchars($bill['agency_name']) ?></td>
            <td>
              <a href="../../../uploads/billboard_invoices/<?= htmlspecialchars($bill['invoice_file']) ?>" target="_blank">View Invoice</a>
            </td>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>

      <!-- Facebook Invoices -->
      <?php foreach ($marketing['facebook'] as $fb): ?>
        <?php if (!empty($fb['invoice_file'])): ?>
          <tr>
            <td>Facebook Advertisement</td>
            <td><?= htmlspecialchars($fb['name']) ?></td>
            <td>
              <a href="../../../uploads/facebook_invoices/<?= htmlspecialchars($fb['invoice_file']) ?>" target="_blank">View Invoice</a>
            </td>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>

      <!-- Instagram Invoices -->
      <?php foreach ($marketing['instagram'] as $insta): ?>
        <?php if (!empty($insta['invoice_file'])): ?>
          <tr>
            <td>Instagram Advertisement</td>
            <td><?= htmlspecialchars($insta['name']) ?></td>
            <td>
              <a href="../../../uploads/instagram_invoices/<?= htmlspecialchars($insta['invoice_file']) ?>" target="_blank">View Invoice</a>
            </td>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>