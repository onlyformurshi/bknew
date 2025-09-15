<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Information</title>
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

        // Add this block to fetch sessions:
        $sessions = [];
        $sessionListStmt = $pdo->prepare("SELECT * FROM program_sessions_times WHERE program_id = ? ORDER BY session_start");
        $sessionListStmt->execute([$programId]);
        $sessions = $sessionListStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
    ?>

    <?php include 'includes/nav.php';  ?>


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

            <div class="mb-4">
                
                
            </div>
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
                            <!-- Participants Progress -->
                        <?php
                        // Fetch current participant count for this program
                        $current = 0;
                        try {
                            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM participants WHERE program_id = ?");
                            $countStmt->execute([$programId]);
                            $current = (int)$countStmt->fetchColumn();
                        } catch (PDOException $e) {
                            $current = 0;
                        }

                        $max = $program['max_participants'] ?? 0;
                        $percentage = ($max > 0) ? round(($current / $max) * 100) : 0;
                        ?>

                        <div class="progress-section mt-4">
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
                            <!-- Program Schedule Sessions List -->
                            <?php if (!empty($sessions)): ?>
                                <div class="mt-4">
                                    <h5 class="fw-bold mb-2">Program Schedule</h5>
                                    <ul class="list-group">
                                        <?php foreach ($sessions as $session): ?>
                                            <li class="list-group-item">
                                                <span class="fw-semibold"><?= htmlspecialchars($session['session_name']) ?></span>
                                                <br>
                                                <small>
                                                    <?= date('F j, Y', strtotime($session['session_start'])) ?> |
                                                    <?= date('g:i A', strtotime($session['session_start'])) ?> - <?= date('g:i A', strtotime($session['session_end'])) ?>
                                                </small>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Participants Progress -->
                        <?php
                        $max = $program['max_participants'] ?? 0;
                        $current = $program['current_participants'] ?? 0;
                        $percentage = ($max > 0) ? round(($current / $max) * 100) : 0;
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </section>





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

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
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

    <section class="participate-section">
        <div class="participate-container">
            <h2 class="participate-subheading">Join Our Program</h2>
            <p class="participate-description">
                Ready to take the next step in your journey? Our program offers everything you need to succeed.
                With expert guidance, comprehensive resources, and a supportive community, you'll be equipped
                to achieve your goals. Don't miss this opportunity to transform your future.
            </p>
            <button class="participate-button">Participate Now</button>
        </div>
    </section>

    <style>
        .participate-section {
            background-color: #f9f9f9;
            padding: 80px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .participate-container {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .participate-subheading {
            color: #333;
            font-size: 1.5rem;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            display: inline-block;
        }

        .participate-subheading::after {
            content: '';
            position: absolute;
            width: 50px;
            height: 3px;
            background-color: #ff0000;
            /* Your red color */
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        .participate-description {
            color: #555;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .participate-button {
            background-color: #ff0000;
            /* Your red color */
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 0, 0, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .participate-button:hover {
            background-color: #d40000;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 0, 0, 0.4);
        }

        .participate-button:active {
            transform: translateY(1px);
        }

        /* Optional decorative elements */
        .participate-section::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background-color: rgba(255, 0, 0, 0.05);
            border-radius: 50%;
            top: -100px;
            left: -100px;
            z-index: 1;
        }

        .participate-section::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background-color: rgba(255, 0, 0, 0.05);
            border-radius: 50%;
            bottom: -50px;
            right: -50px;
            z-index: 1;
        }
    </style>

    <script>
        document.querySelector('.participate-button').addEventListener('click', function() {
            window.location.href = '../participant-registration/index.php?program-id=<?= $programId ?>';
        });
    </script>
</body>

</html>