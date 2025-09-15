<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Information.</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style2.css">
    <style>
        .pamphlet-card .list-group-item {
            background: #f8f9fa;
            border: none;
            padding: 0.75rem 1rem;
        }

        .pamphlet-card .card-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .card-title.text-primary {
            font-size: 1.15rem;
            font-weight: 600;
        }

        .list-group-item {
            background: #f8f9fa;
            border: none;
            padding: 0.7rem 1rem;
        }
    </style>
</head>

<body class="bg-light">
    <?php
    require '../../config/config.php';
    require_once '../../config/functions.php';
    checkModuleAccess($pdo, 'Sponsors Program View');
    



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

        // Get session times
        $sessionStmt = $pdo->prepare("SELECT 
            MIN(session_start) AS session_start_min, 
            MAX(session_end) AS session_end_max 
            FROM program_sessions_times WHERE program_id = ?");
        $sessionStmt->execute([$programId]);
        $sessionTimes = $sessionStmt->fetch(PDO::FETCH_ASSOC);

        $startDateTime = $sessionTimes['session_start_min'] ? new DateTime($sessionTimes['session_start_min']) : null;

        // Fetch radio advertisements for this program
        $radioStmt = $pdo->prepare("SELECT id, name, cost, received_amount, contact, remarks FROM radio_advertisements WHERE program_id = ?");
        $radioStmt->execute([$programId]);
        $radioAds = $radioStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch television advertisements for this program
        $tvStmt = $pdo->prepare("SELECT id, name, cost, received_amount, contact, remarks FROM television_advertisements WHERE program_id = ?");
        $tvStmt->execute([$programId]);
        $televisionAds = $tvStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch newspaper advertisements for this program
        $newspaperAds = [];
        $newsStmt = $pdo->prepare("SELECT id, name, cost, received_amount, contact, remarks FROM newspaper_advertisements WHERE program_id = ?");
        $newsStmt->execute([$programId]);
        $newspaperAds = $newsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch billboard advertisements for this program
        $billboardAds = [];
        $billStmt = $pdo->prepare("SELECT id, agency_name, cost, received_amount FROM billboard_advertisements WHERE program_id = ?");
        $billStmt->execute([$programId]);
        $billboardAds = $billStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch facebook advertisements for this program
        $facebookAds = [];
        $fbStmt = $pdo->prepare("SELECT id, name, cost, received_amount FROM facebook_advertisements WHERE program_id = ?");
        $fbStmt->execute([$programId]);
        $facebookAds = $fbStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch instagram advertisements for this program
        $instagramAds = [];
        $instaStmt = $pdo->prepare("SELECT id, name, cost, received_amount FROM instagram_advertisements WHERE program_id = ?");
        $instaStmt->execute([$programId]);
        $instagramAds = $instaStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch pamphlet details for this program
        $pamphlets = [];
        $pamphletStmt = $pdo->prepare("SELECT 
    id,
    program_id,
    pamphlet_designer_name,
    pamphlet_designer_cost,
    pamphlet_designer_invoice,
    pamphlet_printer_name,
    pamphlet_printing_cost,
    pamphlet_printing_invoice,
    pamphlet_distributor_name,
    pamphlet_distribution_cost,
    pamphlet_distribution_invoice,
    received_amount,
    created_at
FROM program_pamphlets WHERE program_id = ?");
        $pamphletStmt->execute([$programId]);
        $pamphlets = $pamphletStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch bank account information for this program
        $bankAccounts = [];
        $bankStmt = $pdo->prepare("SELECT * FROM program_bank_accounts WHERE program_id = ?");
        $bankStmt->execute([$programId]);
        $bankAccounts = $bankStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch sponsorships for this user and program
        $userId = 45; // Replace with $_SESSION['user_id'] if using sessions
        $sponsorshipStmt = $pdo->prepare("SELECT * FROM sponsorships WHERE user_id = ? AND program_id = ?");
        $sponsorshipStmt->execute([$userId, $programId]);
        $userSponsorships = [];
        while ($row = $sponsorshipStmt->fetch(PDO::FETCH_ASSOC)) {
            $userSponsorships[$row['category'] . '_' . $row['item_id']] = $row;
        }
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }

    // Check if there's any details to display
    $hasAnyDetails =
        !empty($radioAds) ||
        !empty($televisionAds) ||
        !empty($newspaperAds) ||
        !empty($billboardAds) ||
        !empty($facebookAds) ||
        !empty($instagramAds) ||
        !empty($pamphlets) ||
        !empty($bankAccounts);
    ?>

    <?php include 'Includes/nav.php';  ?>


    <div class="hero-banner">
        <div class="floating-elements">
            <div class="floating-circle"></div>
            <div class="floating-circle"></div>
            <div class="floating-circle"></div>
        </div>

        <div class="hero-content">
            <h1 class="hero-title"><?= htmlspecialchars($program['title']) ?></h1>

            <p class="hero-description">
                <?= htmlspecialchars($program['venue']) ?><br>
                <?= htmlspecialchars($program['regional_name'] ?? '') ?>,
                <?= htmlspecialchars($program['country_name'] ?? '') ?>
            </p>

            <p class="hero-date">
                <?php if ($startDateTime): ?>
                    <?= $startDateTime->format('D, M d Y') ?>
                <?php else: ?>
                    Date not specified
                <?php endif; ?>
            </p>

            
        </div>

        <div class="scroll-arrow">
            <i class="fas fa-chevron-down"></i>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll for arrow
        document.querySelector('.scroll-arrow').addEventListener('click', function() {
            window.scrollBy({
                top: window.innerHeight,
                behavior: 'smooth'
            });
        });

        // Add some interactive sparkle effect on mouse move
        document.addEventListener('mousemove', function(e) {
            if (Math.random() > 0.98) {
                createSparkle(e.clientX, e.clientY);
            }
        });

        function createSparkle(x, y) {
            const sparkle = document.createElement('div');
            sparkle.style.position = 'fixed';
            sparkle.style.left = x + 'px';
            sparkle.style.top = y + 'px';
            sparkle.style.width = '4px';
            sparkle.style.height = '4px';
            sparkle.style.background = 'white';
            sparkle.style.borderRadius = '50%';
            sparkle.style.pointerEvents = 'none';
            sparkle.style.zIndex = '1000';
            sparkle.style.animation = 'sparkle 1s ease-out forwards';

            document.body.appendChild(sparkle);

            setTimeout(() => {
                sparkle.remove();
            }, 1000);
        }

        // Add sparkle animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes sparkle {
                0% {
                    transform: scale(0);
                    opacity: 1;
                }
                50% {
                    transform: scale(1);
                    opacity: 1;
                }
                100% {
                    transform: scale(0);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>

    <section class="about-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <!-- Left Column - Image -->
                <div class="col-lg-6 col-md-6 mb-4 mb-md-0">
                    <div class="about-image-wrapper">
                        <img src="<?= !empty($program['program_img'])
                                        ? '../../uploads/programs/' . htmlspecialchars($program['program_img'])
                                        : 'https://via.placeholder.com/800x400?text=No+Image' ?>"
                            alt="<?= htmlspecialchars($program['title']) ?>"
                            class="about-image img-fluid">
                    </div>
                </div>

                <!-- Right Column - Content -->
                <div class="col-lg-6 col-md-6">
                    <div class="about-content">
                        <h2 class="about-title">About This Program</h2>
                        <div class="about-description">
                            <p>
                                <?= htmlspecialchars($program['description'] ?? '') ?>
                            </p>
                        </div>

                        <!-- Instructor Details -->
                        <div class="instructor-section">
                            <h3 class="section-subtitle">Expert Instructors</h3>
                            <div class="instructor-grid">
                                <div class="instructor-item">
                                    <div class="instructor-avatar">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div class="instructor-info">
                                        <h4><?= htmlspecialchars($program['instructor_name'] ?? '') ?></h4>
                                      
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Participants Progress -->
                        <?php
                        $max = $program['max_participants'] ?? 0;
                        $current =  12;
                        $percentage = ($max > 0) ? round(($current / $max) * 100) : 0;
                        ?>

                        <div class="progress-section">
                            <h3 class="section-subtitle">Participant Progress</h3>
                            <div class="progress-stats">
                                <div class="stat-item">
                                    <div class="stat-number"><?= $percentage ?>%</div>
                                    <div class="stat-label">Registration Complete</div>
                                    <div class="progress-bar-custom">
                                        <div class="progress-fill" style="width: <?= $percentage ?>%"></div>
                                    </div>
                                </div>
                                

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="container">
        <?php if ($hasAnyDetails): ?>
            <?php if (!empty($radioAds)): ?>
                <div class="marketing-section">
                    <div class="section-header">
                        <i class="fas fa-broadcast-tower"></i>
                        <span>Radio Advertisements</span>
                    </div>
                    <div class="section-content">
                        <div class="marketing-grid">
                            <?php foreach ($radioAds as $radio): ?>
                                <?php
                                $remaining = floatval($radio['cost']) - floatval($radio['received_amount']);
                                if (
                                    empty($radio['name']) ||
                                    empty($radio['cost']) ||
                                    empty($radio['contact']) ||
                                    empty($radio['remarks']) ||
                                    $remaining <= 0
                                ) {
                                    continue;
                                }
                                ?>
                                <div class="marketing-card">
                                    <div class="marketing-card-header">
                                        <div class="marketing-card-title"><?= htmlspecialchars($radio['name']) ?></div>

                                    </div>
                                    <div class="marketing-card-body">
                                        <div class="marketing-detail">
                                            <div class="marketing-detail-label">Contact</div>
                                            <div class="marketing-detail-value"><?= htmlspecialchars($radio['contact']) ?></div>
                                        </div>
                                        <div class="marketing-detail">
                                            <div class="marketing-detail-label">Remarks</div>
                                            <div class="marketing-detail-value"><?= htmlspecialchars($radio['remarks']) ?></div>
                                        </div>
                                        <div class="marketing-detail">
                                            <div class="marketing-detail-label">Total</div>
                                            <div class="marketing-detail-value"><?= htmlspecialchars($radio['cost']) ?></div>
                                        </div>
                                        <div class="marketing-card-cost">
                                            $<?= number_format($remaining, 2) ?>
                                            <span class="text-muted small">(Remaining)</span>
                                        </div>
                                        <div class="sponsor-section">
                                            <input type="number" class="sponsor-amount" placeholder="Amount" min="1" max="<?= $remaining ?>">
                                            <!-- Radio Advertisements -->
                                            <button class="sponsor-btn"
                                                data-program-id="<?= $programId ?>"
                                                data-category="radio_advertisements"
                                                data-item-id="<?= $radio['id'] ?>">
                                                <i class="fas fa-hand-holding-heart"></i>
                                                Sponsor Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($televisionAds)): ?>
                <div class="marketing-section">
                    <div class="section-header">
                        <i class="fas fa-tv"></i>
                        <span>Television Advertisements</span>
                    </div>
                    <div class="section-content">
                        <div class="marketing-grid">
                            <?php foreach ($televisionAds as $tv): ?>
                                <?php
                                $remaining = floatval($tv['cost']) - floatval($tv['received_amount']);
                                if (
                                    empty($tv['name']) ||
                                    empty($tv['cost']) ||
                                    empty($tv['contact']) ||
                                    empty($tv['remarks']) ||
                                    $remaining <= 0
                                ) {
                                    continue;
                                }
                                ?>
                                <div class="marketing-card">
                                    <div class="marketing-card-header">
                                        <div class="marketing-card-title"><?= htmlspecialchars($tv['name']) ?></div>

                                    </div>
                                    <div class="marketing-card-body">
                                        <div class="marketing-detail">
                                            <div class="marketing-detail-label">Contact</div>
                                            <div class="marketing-detail-value"><?= htmlspecialchars($tv['contact']) ?></div>
                                        </div>
                                        <div class="marketing-detail">
                                            <div class="marketing-detail-label">Remarks</div>
                                            <div class="marketing-detail-value"><?= htmlspecialchars($tv['remarks']) ?></div>
                                        </div>
                                        <div class="marketing-detail">
                                            <div class="marketing-detail-label">Total</div>
                                            <div class="marketing-detail-value"><?= htmlspecialchars($tv['cost']) ?></div>
                                        </div>
                                        <div class="marketing-card-cost">
                                            $<?= number_format($remaining, 2) ?>
                                            <span class="text-muted small">(Remaining)</span>
                                        </div>
                                        <div class="sponsor-section">
                                            <input type="number" class="sponsor-amount" placeholder="Amount" min="1" max="<?= $remaining ?>">
                                            <!-- Television Advertisements -->
                                            <button class="sponsor-btn"
                                                data-program-id="<?= $programId ?>"
                                                data-category="television_advertisements"
                                                data-item-id="<?= $tv['id'] ?>">
                                                <i class="fas fa-hand-holding-heart"></i>
                                                Sponsor Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($newspaperAds)): ?>
                <div class="marketing-section">
                    <div class="section-header">
                        <i class="fas fa-newspaper"></i>
                        <span>Newspaper Advertisements</span>
                    </div>
                    <div class="section-content">
                        <div class="marketing-grid">
                            <?php foreach ($newspaperAds as $news): ?>
                                <?php
                                $remaining = floatval($news['cost']) - floatval($news['received_amount']);
                                if (
                                    empty($news['name']) ||
                                    empty($news['cost']) ||
                                    empty($news['contact']) ||
                                    empty($news['remarks']) ||
                                    $remaining <= 0
                                ) {
                                    continue;
                                }
                                ?>
                                <div class="marketing-card">
                                    <div class="marketing-card-header">
                                        <div class="marketing-card-title"><?= htmlspecialchars($news['name']) ?></div>

                                    </div>
                                    <div class="marketing-card-body">
                                        <div class="marketing-detail">
                                            <div class="marketing-detail-label">Contact</div>
                                            <div class="marketing-detail-value"><?= htmlspecialchars($news['contact']) ?></div>
                                        </div>
                                        <div class="marketing-detail">
                                            <div class="marketing-detail-label">Remarks</div>
                                            <div class="marketing-detail-value"><?= htmlspecialchars($news['remarks']) ?></div>
                                        </div>
                                        <div class="marketing-detail">
                                            <div class="marketing-detail-label">Total</div>
                                            <div class="marketing-detail-value"><?= htmlspecialchars($news['cost']) ?></div>
                                        </div>
                                        <div class="marketing-card-cost">
                                            $<?= number_format($remaining, 2) ?>
                                            <span class="text-muted small">(Remaining)</span>
                                        </div>
                                        <div class="sponsor-section">
                                            <input type="number" class="sponsor-amount" placeholder="Amount" min="1" max="<?= $remaining ?>">
                                            <!-- Newspaper Advertisements -->
                                            <button class="sponsor-btn"
                                                data-program-id="<?= $programId ?>"
                                                data-category="newspaper_advertisements"
                                                data-item-id="<?= $news['id'] ?>">
                                                <i class="fas fa-hand-holding-heart"></i>
                                                Sponsor Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($billboardAds)): ?>
                <div class="marketing-section">
                    <div class="section-header">
                        <i class="fas fa-building"></i>
                        <span>Billboard Advertisements</span>
                    </div>
                    <div class="section-content">
                        <div class="marketing-grid">
                            <?php foreach ($billboardAds as $bill): ?>
                                <?php
                                $remaining = floatval($bill['cost']) - floatval($bill['received_amount']);
                                if (
                                    empty($bill['agency_name']) ||
                                    empty($bill['cost']) ||
                                    $remaining <= 0
                                ) {
                                    continue;
                                }
                                ?>
                                <div class="marketing-card">
                                    <div class="marketing-card-header">
                                        <div class="marketing-card-title"><?= htmlspecialchars($bill['agency_name']) ?></div>
                                        
                                    </div>
                                    
                                    <div class="marketing-card-body">
                                        <div class="marketing-detail">
                                            <div class="marketing-detail-label">Total</div>
                                            <div class="marketing-detail-value"><?= htmlspecialchars($bill['cost']) ?></div>
                                        </div>
                                        <div class="marketing-card-cost">
                                            $<?= number_format($remaining, 2) ?>
                                            <span class="text-muted small">(Remaining)</span>
                                        </div>
                                        <div class="sponsor-section">
                                            <input type="number" class="sponsor-amount" placeholder="Amount" min="1" max="<?= $remaining ?>">
                                            <!-- Billboard Advertisements -->
                                            <button class="sponsor-btn"
                                                data-program-id="<?= $programId ?>"
                                                data-category="billboard_advertisements"
                                                data-item-id="<?= $bill['id'] ?>">
                                                <i class="fas fa-hand-holding-heart"></i>
                                                Sponsor Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($facebookAds)): ?>
                <div class="marketing-section">
                    <div class="section-header">
                        <i class="fab fa-facebook"></i>
                        <span>Facebook Advertisements</span>
                    </div>
                    <div class="section-content">
                        <div class="marketing-grid">
                            <?php foreach ($facebookAds as $fb): ?>
                                <?php
                                $remaining = floatval($fb['cost']) - floatval($fb['received_amount']);
                                if (
                                    empty($fb['name']) ||
                                    empty($fb['cost']) ||
                                    $remaining <= 0
                                ) {
                                    continue;
                                }
                                ?>
                                <div class="marketing-card">
                                    <div class="marketing-card-header">
                                        <div class="marketing-card-title"><?= htmlspecialchars($fb['name']) ?></div>
                                        
                                    </div>
                                    <div class="marketing-card-body">
                                        <div class="marketing-detail">
                                            <div class="marketing-detail-label">Total</div>
                                            <div class="marketing-detail-value"><?= htmlspecialchars($fb['cost']) ?></div>
                                        <div class="marketing-card-cost">
                                            $<?= number_format($remaining, 2) ?>
                                            <span class="text-muted small">(Remaining)</span>
                                        </div>
                                        <div class="sponsor-section">
                                            <input type="number" class="sponsor-amount" placeholder="Amount" min="1" max="<?= $remaining ?>">
                                            <!-- Facebook Advertisements -->
                                            <button class="sponsor-btn"
                                                data-program-id="<?= $programId ?>"
                                                data-category="facebook_advertisements"
                                                data-item-id="<?= $fb['id'] ?>">
                                                <i class="fas fa-hand-holding-heart"></i>
                                                Sponsor Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($instagramAds)): ?>
                <div class="marketing-section">
                    <div class="section-header">
                        <i class="fab fa-instagram"></i>
                        <span>Instagram Advertisements</span>
                    </div>
                    <div class="section-content">
                        <div class="marketing-grid">
                            <?php foreach ($instagramAds as $insta): ?>
                                <?php
                                $remaining = floatval($insta['cost']) - floatval($insta['received_amount']);
                                if (
                                    empty($insta['name']) ||
                                    empty($insta['cost']) ||
                                    $remaining <= 0
                                ) {
                                    continue;
                                }
                                ?>
                                <div class="marketing-card">
                                    <div class="marketing-card-header">
                                        <div class="marketing-card-title"><?= htmlspecialchars($insta['name']) ?></div>
                                        
                                    </div>
                                    <div class="marketing-card-body">
                                        <div class="marketing-detail">
                                            <div class="marketing-detail-label">Total</div>
                                            <div class="marketing-detail-value"><?= htmlspecialchars($insta['cost']) ?></div>
                                        <div class="marketing-card-cost">
                                            $<?= number_format($remaining, 2) ?>
                                            <span class="text-muted small">(Remaining)</span>
                                        </div>
                                        <div class="sponsor-section">
                                            <input type="number" class="sponsor-amount" placeholder="Amount" min="1" max="<?= $remaining ?>">
                                            <!-- Instagram Advertisements -->
                                            <button class="sponsor-btn"
                                                data-program-id="<?= $programId ?>"
                                                data-category="instagram_advertisements"
                                                data-item-id="<?= $insta['id'] ?>">
                                                <i class="fas fa-hand-holding-heart"></i>
                                                Sponsor Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($pamphlets)): ?>
                <div class="marketing-section">
                    <div class="section-header">
                        <i class="fas fa-file-alt"></i>
                        <span>Pamphlet Details</span>
                    </div>
                    <div class="section-content">
                        <div class="row g-4">
                            <?php foreach ($pamphlets as $pamphlet): ?>
                                <?php
                                $totalAmount =
                                    floatval($pamphlet['pamphlet_designer_cost']) +
                                    floatval($pamphlet['pamphlet_printing_cost']) +
                                    floatval($pamphlet['pamphlet_distribution_cost']);
                                $remaining = $totalAmount - floatval($pamphlet['received_amount']);
                                if ($remaining <= 0) {
                                    continue;
                                }
                                ?>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card h-100 shadow-sm pamphlet-card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3">Pamphlet</h5>
                                            <ul class="list-group list-group-flush mb-3">
                                                <li class="list-group-item">
                                                    <strong>Designer:</strong> <?= htmlspecialchars($pamphlet['pamphlet_designer_name']) ?><br>
                                                    <strong>Cost:</strong> $<?= htmlspecialchars($pamphlet['pamphlet_designer_cost']) ?><br>
                                                    <?php if (!empty($pamphlet['pamphlet_designer_invoice'])): ?>
                                                        <a href="<?= htmlspecialchars($pamphlet['pamphlet_designer_invoice']) ?>" target="_blank">Designer Invoice</a>
                                                    <?php endif; ?>
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>Printer:</strong> <?= htmlspecialchars($pamphlet['pamphlet_printer_name']) ?><br>
                                                    <strong>Cost:</strong> $<?= htmlspecialchars($pamphlet['pamphlet_printing_cost']) ?><br>
                                                    <?php if (!empty($pamphlet['pamphlet_printing_invoice'])): ?>
                                                        <a href="<?= htmlspecialchars($pamphlet['pamphlet_printing_invoice']) ?>" target="_blank">Printing Invoice</a>
                                                    <?php endif; ?>
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>Distributor:</strong> <?= htmlspecialchars($pamphlet['pamphlet_distributor_name']) ?><br>
                                                    <strong>Cost:</strong> $<?= htmlspecialchars($pamphlet['pamphlet_distribution_cost']) ?><br>
                                                    <?php if (!empty($pamphlet['pamphlet_distribution_invoice'])): ?>
                                                        <a href="<?= htmlspecialchars($pamphlet['pamphlet_distribution_invoice']) ?>" target="_blank">Distribution Invoice</a>
                                                    <?php endif; ?>
                                                </li>
                                            </ul>
                                            <div class="mb-3">
                                                <strong>Total Amount:</strong> $<?= number_format($totalAmount, 2) ?><br>
                                                <div class="marketing-card-cost">
                                                    $<?= number_format($remaining, 2) ?>
                                                    <span class="text-muted small">(Remaining)</span>
                                                </div>
                                            </div>
                                            <div class="sponsor-section">
                                                <input type="number" class="sponsor-amount" placeholder="Sponsorship Amount" min="1" max="<?= $remaining ?>">
                                                <!-- Pamphlet -->
                                                <button class="sponsor-btn"
                                                    data-program-id="<?= $programId ?>"
                                                    data-category="pamphlet"
                                                    data-item-id="<?= $pamphlet['id'] ?>">
                                                    <i class="fas fa-hand-holding-heart"></i>
                                                    Sponsor Now
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($bankAccounts)): ?>
                <div class="marketing-section">
                    <div class="section-header">
                        <i class="fas fa-university"></i>
                        <span>Account Information</span>
                    </div>
                    <div class="section-content">
                        <div class="row g-4">
                            <?php foreach ($bankAccounts as $account): ?>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card h-100 shadow-sm border-primary">
                                        <div class="card-body">
                                            <h5 class="card-title text-primary mb-3">
                                                <?= htmlspecialchars($account['account_holder_name']) ?>
                                            </h5>
                                            <ul class="list-group list-group-flush mb-3">
                                                <li class="list-group-item">
                                                    <strong>Bank:</strong> <?= htmlspecialchars($account['bank_name']) ?>
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>Account Number:</strong> <?= htmlspecialchars($account['account_number']) ?>
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>IFSC Code:</strong> <?= htmlspecialchars($account['ifsc_code']) ?>
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>Branch:</strong> <?= htmlspecialchars($account['branch']) ?>
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>UPI ID:</strong> <?= htmlspecialchars($account['upi_id']) ?>
                                                </li>
                                            </ul>
                                            <div class="text-muted small">
                                                Added: <?= htmlspecialchars($account['created_at']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($bankAccounts)): ?>
                                <div class="col-12">
                                    <div class="alert alert-info">No account information available for this program.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info my-5 text-center" style="font-size:1.2rem;">
                There is no sponsorship details.
            </div>
        <?php endif; ?>
    </div>


    <style>
        :root {
            --primary-red: #dc2626;
            --dark-red: #b91c1c;
            --light-red: #fef2f2;
            --red-50: #fef2f2;
            --red-100: #fee2e2;
            --red-600: #dc2626;
            --red-700: #b91c1c;
            --red-800: #991b1b;
        }

        .modal-backdrop {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(2px);
        }

        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            color: white;
            border: none;
            padding: 1.5rem 2rem;
            position: relative;
        }

        .modal-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            opacity: 0.1;
        }

        .modal-title {
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            z-index: 1;
        }

        .modal-title::before {
            content: '\f004';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.8;
            position: relative;
            z-index: 1;
        }

        .btn-close:hover {
            opacity: 1;
            transform: scale(1.1);
            transition: all 0.2s ease;
        }

        .modal-body {
            padding: 2rem;
            background: white;
        }

        .modal-body p {
            color: #6b7280;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .detail-item {
            background: var(--red-50);
            border: 2px solid var(--red-100);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .detail-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.15);
            border-color: var(--red-600);
        }

        .detail-label {
            font-weight: 700;
            color: var(--red-800);
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-weight: 600;
            color: #1f2937;
            font-size: 1.1rem;
        }

        .amount-value {
            color: var(--primary-red);
            font-size: 1.3rem;
            font-weight: 800;
        }

        .info-note {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-left: 4px solid var(--primary-red);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
            position: relative;
        }

        .info-note::before {
            content: '\f05a';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--primary-red);
            position: absolute;
            top: 1rem;
            left: -10px;
            background: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .info-note p {
            margin: 0;
            padding-left: 1rem;
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.5;
        }

        .modal-footer {
            background: #f8fafc;
            border: none;
            padding: 1.5rem 2rem;
            gap: 1rem;
            justify-content: flex-end;
        }

        .btn {
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }

        .btn-secondary {
            background: #e5e7eb;
            border: 2px solid #d1d5db;
            color: #6b7280;
        }

        .btn-secondary:hover {
            background: #d1d5db;
            border-color: #9ca3af;
            color: #4b5563;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            border: 2px solid var(--primary-red);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--dark-red) 0%, var(--red-800) 100%);
            border-color: var(--dark-red);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(220, 38, 38, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* Animation for modal appearance */
        .modal.fade .modal-dialog {
            transition: transform 0.4s ease-out, opacity 0.4s ease-out;
            transform: translate(0, -50px) scale(0.95);
        }

        .modal.show .modal-dialog {
            transform: translate(0, 0) scale(1);
        }

        /* Icon animations */
        .detail-item:hover .detail-label::before {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* Responsive design */
        @media (max-width: 576px) {
            .modal-header {
                padding: 1.25rem 1.5rem;
            }
            
            .modal-body {
                padding: 1.5rem;
            }
            
            .modal-footer {
                padding: 1.25rem 1.5rem;
            }
            
            .btn {
                padding: 0.625rem 1.5rem;
                font-size: 0.85rem;
            }
        }
    </style>
    <!-- Sponsorship Confirmation Modal -->
    <div class="modal fade" id="sponsorConfirmModal" tabindex="-1" aria-labelledby="sponsorConfirmModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="sponsorConfirmModalLabel">Confirm Sponsorship</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Please review your sponsorship details before confirming:</p>
            <div><strong>Program:</strong> <span id="modalProgramName"></span></div>
            <div><strong>Item:</strong> <span id="modalItemName"></span></div>
            <div><strong>Your Sponsorship:</strong> $<span id="modalAmount"></span></div>
            <div class="mt-3 text-muted small">
              After confirmation, you'll receive an email with payment instructions and sponsorship certificate.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="confirmSponsorBtn">Confirm</button>
          </div>
        </div>
      </div>
    </div>

    <script>
        let pendingSponsorship = null; // Store details for confirmation

        document.querySelectorAll('.sponsor-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const section = btn.closest('.sponsor-section');
                const amountInput = section.querySelector('.sponsor-amount');
                const amount = parseFloat(amountInput.value);

                // Find the max allowed amount
                let maxAmount = null;
                // For pamphlet
                if (btn.dataset.category === 'pamphlet') {
                    const totalAmountElem = section.closest('.card-body').querySelector('.mb-3 strong');
                    if (totalAmountElem) {
                        maxAmount = parseFloat(totalAmountElem.textContent.replace(/[^0-9.]/g, ''));
                    }
                } else {
                    const costElem = section.closest('.marketing-card').querySelector('.marketing-card-cost');
                    if (costElem) {
                        maxAmount = parseFloat(costElem.textContent.replace(/[^0-9.]/g, ''));
                    }
                }

                if (!amount || amount <= 0) {
                    alert('Please enter a valid amount.');
                    return;
                }
                if (maxAmount !== null && amount > maxAmount) {
                    alert('You cannot sponsor more than the required amount ($' + maxAmount + ').');
                    return;
                }

                // Get item name for modal
                let itemName = '';
                if (btn.dataset.category === 'pamphlet') {
                    itemName = section.closest('.card-body').querySelector('.card-title').textContent.trim();
                } else {
                    itemName = section.closest('.marketing-card').querySelector('.marketing-card-title').textContent.trim();
                }

                // Store details for confirmation
                pendingSponsorship = {
                    user_id: 45,
                    program_id: btn.dataset.programId,
                    category: btn.dataset.category,
                    item_id: btn.dataset.itemId,
                    amount: amount,
                    amountInput: amountInput // for clearing after success
                };

                // Fill modal details
                document.getElementById('modalProgramName').textContent = programName;
                document.getElementById('modalItemName').textContent = itemName;
                document.getElementById('modalAmount').textContent = amount.toFixed(2);

                // Show modal
                var modal = new bootstrap.Modal(document.getElementById('sponsorConfirmModal'));
                modal.show();
            });
        });

        // Handle confirm button in modal
        document.getElementById('confirmSponsorBtn').addEventListener('click', function() {
            if (!pendingSponsorship) return;
            fetch('submit_sponsorship.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user_id: pendingSponsorship.user_id,
                    program_id: pendingSponsorship.program_id,
                    category: pendingSponsorship.category,
                    item_id: pendingSponsorship.item_id,
                    amount: pendingSponsorship.amount
                })
            })
            .then(res => res.json())
            .then(data => {
                var modalEl = document.getElementById('sponsorConfirmModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
                if (data.success) {
                    alert('Sponsorship submitted!');
                    if (pendingSponsorship.amountInput) pendingSponsorship.amountInput.value = '';
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Could not submit.'));
                }
                pendingSponsorship = null;
            });
        });

        const programName = <?= json_encode($program['title']) ?>;
    </script>
</body>

</html>