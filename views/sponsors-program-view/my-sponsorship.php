<?php
include 'Includes/nav.php';
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Sponsors Program View');
// Fetch sponsorships for user_id 45
$user_id = $_SESSION['user_id'];
$sql = "SELECT s.*, p.title, p.venue
        FROM sponsorships s
        LEFT JOIN programs p ON s.program_id = p.id
        WHERE s.user_id = :user_id
        ORDER BY s.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$sponsorships = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate summary stats
$totalPrograms = count(array_unique(array_column($sponsorships, 'program_id')));
$totalInvestment = 0;
$amountPaid = 0;
$outstanding = 0;

foreach ($sponsorships as $s) {
    $totalInvestment += floatval($s['amount']);
    if (strtolower($s['payment_status']) === 'paid') {
        $amountPaid += floatval($s['amount']);
    } else {
        $outstanding += floatval($s['amount']);
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sponsor Our Events - Partnership Opportunities</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #d32f2f;
            --primary-dark: #b71c1c;
            --primary-light: #ff6659;
            --secondary-color: #f44336;
            --primary-gradient: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            --secondary-gradient: linear-gradient(135deg, var(--primary-light) 0%, var(--secondary-color) 100%);
            --card-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --card-shadow-hover: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        body {
            background: #c50000ff;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 80px;
        }

        /* Header Styles */
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand img {
            height: 50px;
            transition: all 0.3s ease;
        }

        .navbar-brand img:hover {
            transform: scale(1.05);
        }

        .nav-link {
            color: #333 !important;
            font-weight: 500;
            padding: 8px 15px !important;
            margin: 0 5px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--primary-color) !important;
            background-color: rgba(211, 47, 47, 0.1);
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 10px 0;
        }

        .dropdown-item {
            padding: 8px 20px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: rgba(211, 47, 47, 0.1);
            color: var(--primary-color);
        }

        .profile-img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .profile-img:hover {
            transform: scale(1.1);
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 8px 32px rgba(220, 53, 69, 0.3);
        }

        .sponsorship-card {
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 2rem;
            position: relative;
        }

        .sponsorship-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            color: white;
            padding: 1.5rem;
            border: none;
            position: relative;
        }

        .card-header-custom::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ff6b6b, #ff8e8e, #ff6b6b);
        }

        .program-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: black;
        }

        .card-body-custom {
            padding: 2rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-value {
            font-weight: 500;
        }

        .amount {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-red);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-paid {
            background-color: green;
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .status-pending {
            background-color: orange;
            color: white;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }

        .status-unpaid {
            background-color: orange;
            color: white;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .type-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--light-red);
            color: var(--primary-red);
            font-size: 1.2rem;
        }

        .summary-stats {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-red);
            display: block;
        }

        .stat-label {
            color: #6c757d;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .dashboard-header {
                padding: 1.5rem 0;
            }
        }
    </Style>

</head>

<body>


    <div class="dashboard-header">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="display-4 mb-2">
                        <i class="fas fa-handshake me-3"></i>
                        Sponsorship Program Dashboard
                    </h1>
                    <p class="lead mb-0">Manage and track your sponsorship investments</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Summary Statistics -->
        <div class="summary-stats">
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number"><?= $totalPrograms ?></span>
                        <div class="stat-label">Total Programs</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number">$<?= number_format($totalInvestment, 2) ?></span>
                        <div class="stat-label">Total Investment</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number">$<?= number_format($amountPaid, 2) ?></span>
                        <div class="stat-label">Amount Paid</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number">$<?= number_format($outstanding, 2) ?></span>
                        <div class="stat-label">Outstanding</div>
                    </div>
                </div>
            </div>
        </div>



        <div class="row">
            <?php foreach ($sponsorships as $sponsorship): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card sponsorship-card">
                        <div class="card-header-custom">
                            <h5 class="program-title">
                                <?= htmlspecialchars($sponsorship['title']) ?>
                            </h5>
                            <div class="small text-muted"><?= htmlspecialchars($sponsorship['venue']) ?></div>
                        </div>
                        <div class="card-body-custom">
                            <div class="detail-row">
                                <div class="detail-label">
                                    <span>Advertisement Type</span>
                                </div>
                                <div class="detail-value"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $sponsorship['category']))) ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">
                                    <i class="fas fa-dollar-sign"></i>
                                    Amount
                                </div>
                                <div class="detail-value amount">$<?= number_format($sponsorship['amount'], 2) ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">
                                    <i class="fas fa-check-circle"></i>
                                    Payment Status
                                </div>
                                <span class="status-badge <?= $sponsorship['payment_status'] === 'paid' ? 'status-paid' : ($sponsorship['payment_status'] === 'Pending' ? 'status-pending' : 'status-unpaid') ?>">
                                    <?= htmlspecialchars($sponsorship['payment_status']) ?>


                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    </div>

    <div class="modal" id="customModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Confirm Sponsorship</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Modal content here -->
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>

</html>