<?php
include '../Includes/header.php';
require '../../config/config.php';

try {
    // Check connection first
    $pdo->query("SELECT 1"); // Simple query to test connection

    // Get user count
    $stmtUsers = $pdo->query("SELECT COUNT(*) FROM users");
    $totalUsers = $stmtUsers->fetchColumn();

    $stmtCountries = $pdo->query("SELECT COUNT(*) FROM countries");
    $totalCountries = $stmtCountries->fetchColumn();

    $stmtPrograms = $pdo->query("SELECT COUNT(*) FROM programs");
    $totalPrograms = $stmtPrograms->fetchColumn();

    // Modified sponsor query to join with user_roles table
    $stmtSponsors = $pdo->query("
        SELECT COUNT(*) 
        FROM users u
        JOIN user_roles ur ON u.role = ur.id
        WHERE ur.role_name = 'Sponsor'
    ");
    $totalSponsors = $stmtSponsors->fetchColumn();

    $stmtCenters = $pdo->query("SELECT COUNT(*) FROM centres");
    $totalCenters = $stmtCenters->fetchColumn();

    $stmtRegionals = $pdo->query("SELECT COUNT(*) FROM regionals");
    $totalRegionals = $stmtRegionals->fetchColumn();

    $stmtParticipants = $pdo->query("SELECT COUNT(*) FROM participants");
    $totalParticipants = $stmtParticipants->fetchColumn();

    // New query to get the total amount of sponsor contributions
    $stmtTotalAmount = $pdo->query("SELECT SUM(amount) FROM sponsorships");
    $totalAmount = $stmtTotalAmount->fetchColumn() ?: 0;
} catch (PDOException $e) {
    // Display the actual error for debugging
    echo "<div class='alert alert-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    $totalUsers = $totalCountries = $totalPrograms = $totalSponsors = $totalCenters = $totalRegionals = $totalParticipants = $totalAmount = 0;
}
?>


<div class="app-main__outer">
    <div class="app-main__inner">

        <div class="app-page-title app-page-title-simple">
            <div class="page-title-wrapper d-flex justify-content-between align-items-center">
                <div class="page-title-heading">
                    <div>
                        <div class="page-title-head center-elem mb-3">
                            <span class="d-inline-block">
                                <i class="lnr-home opacity-6"></i>
                            </span>
                            <span class="d-inline-block">Dashboard</span>
                        </div>
                        <div class="page-title-subheading opacity-7">
                            Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>!
                        </div>
                    </div>
                </div>
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card mb-3 widget-content bg-amy-crisp text-white">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                                <div class="widget-heading">Total Users</div>
                                <div class="widget-subheading">All registered users</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers"><?= $totalUsers ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card mb-3 widget-content bg-arielle-smile text-white">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                                <div class="widget-heading">Countries</div>
                                <div class="widget-subheading">Managed countries</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers"><?= $totalCountries ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card mb-3 widget-content bg-grow-early text-white">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                                <div class="widget-heading">Programs</div>
                                <div class="widget-subheading">Total programs</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers"><?= $totalPrograms ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card mb-3 widget-content bg-happy-green text-white">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                                <div class="widget-heading">Sponsors</div>
                                <div class="widget-subheading">Total sponsors</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers"><?= $totalSponsors ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card mb-3 widget-content bg-plum-plate text-white">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                                <div class="widget-heading">Centers</div>
                                <div class="widget-subheading">Total centers</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers"><?= $totalCenters ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card mb-3 widget-content bg-night-sky text-white">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                                <div class="widget-heading">Regionals</div>
                                <div class="widget-subheading">Total regionals</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers"><?= $totalRegionals ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card mb-3 widget-content bg-strong-bliss text-white">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                                <div class="widget-heading">Participants</div>
                                <div class="widget-subheading">Total participants</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers"><?= $totalParticipants ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card mb-3 widget-content bg-info text-white">
                    <div class="widget-content-outer">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                                <div class="widget-heading">Total Amount</div>
                                <div class="widget-subheading">Sponsor contributions</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers"><?= number_format($totalAmount, 2) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Example: Add more dashboard widgets or charts here -->
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">Quick Overview</div>
                    <div class="card-body">
                        <p class="mb-0">
                            Use the navigation menu to manage users, countries, programs, and sponsors.<br>
                            You can add more widgets, charts, or recent activity here.
                        </p>
                        <!-- Quick Overview Steps -->
                        <ul class="mb-0">
                            <li><strong>1. Dashboard:</strong> View summary statistics for users, countries, programs, sponsors, centers, regionals, participants, and total sponsor contributions.</li>
                            <li><strong>2. User Management:</strong> Add, edit, or delete users. Assign roles such as Sponsor, Admin, or Participant.</li>
                            <li><strong>3. Country, Center & Regional Management:</strong> Manage countries, centers, and regionals for program organization.</li>
                            <li><strong>4. Program Management:</strong> Create new programs, set details, assign centers/regionals, and view/edit program information.</li>
                            <li><strong>5. Sponsor Management:</strong> Add sponsors, manage sponsorships, track sponsor contributions, and view payment status/types.</li>
                            <li><strong>6. Participant Management:</strong> Register participants for programs, view participant details, and manage attendance.</li>
                            <li><strong>7. Marketing Management:</strong> Track marketing expenses for each program (billboard, radio, TV, newspaper, social media, pamphlets, and other costs).</li>
                            <li><strong>8. Reports & Print:</strong> Print program details, sponsorship summaries, and participant tickets for events.</li>
                            <li><strong>9. Permissions:</strong> Access to add, edit, or delete is controlled by user roles and permissions.</li>
                            <li><strong>10. Navigation:</strong> Use the sidebar menu to switch between modules. Use filters and search to quickly find records.</li>
                            <li><strong>11. Support:</strong> For help, contact the support team using the information in the footer.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Your Info</div>
                    <div class="card-body">
                        <strong>Name:</strong> <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?><br>
                        <strong>Role:</strong> <?= htmlspecialchars($_SESSION['user_role'] ?? '') ?><br>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>
</div>

<?php include '../Includes/footer.php'; ?>