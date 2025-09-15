<?php
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Sponsors Program View');
// Check if user has permission to view sponsors programs
// Fetch filter dropdown options
$countries = $pdo->query("SELECT id, country_name FROM countries ORDER BY country_name")->fetchAll(PDO::FETCH_ASSOC);
$regionals = $pdo->query("SELECT id, regional_name FROM regionals ORDER BY regional_name")->fetchAll(PDO::FETCH_ASSOC);
$centres = $pdo->query("SELECT id, centre_name FROM centres WHERE status = 'Active' ORDER BY centre_name")->fetchAll(PDO::FETCH_ASSOC);

// Get filter values from GET
$filters = [
  'country_id' => $_GET['country_id'] ?? '',
  'regional_id' => $_GET['regional_id'] ?? '',
  'centre_id' => $_GET['centre_id'] ?? ''
];

// Build WHERE clause
$where = [];
$params = [];
if ($filters['country_id']) {
  $where[] = "programs.country_id = :country_id";
  $params['country_id'] = $filters['country_id'];
}
if ($filters['regional_id']) {
  $where[] = "programs.regional_id = :regional_id";
  $params['regional_id'] = $filters['regional_id'];
}
if ($filters['centre_id']) {
  $where[] = "programs.centre_id = :centre_id";
  $params['centre_id'] = $filters['centre_id'];
}

// Add status filter based on user access
if (canUserViewProgram($pdo, 'Sponsors Program View')) {
    // Show both pending and activated
    $where[] = "programs.status IN ('pending', 'activated')";
} else {
    // Show only activated
    $where[] = "programs.status = 'activated'";
}

$whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

// Pagination setup
$perPage = 12;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Count total programs for pagination
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM programs
    LEFT JOIN centres ON programs.centre_id = centres.id
    LEFT JOIN regionals ON programs.regional_id = regionals.id
    LEFT JOIN countries ON programs.country_id = countries.id
    $whereClause");
$countStmt->execute($params);
$totalPrograms = $countStmt->fetchColumn();
$totalPages = ceil($totalPrograms / $perPage);

// Fetch paginated programs
// Fix: Get earliest session_start for each program
$stmt = $pdo->prepare("SELECT programs.*, centres.centre_name, regionals.regional_name, countries.country_name,
    (SELECT MIN(session_start) FROM program_sessions_times WHERE program_id = programs.id) AS first_session_start,
    (SELECT MAX(session_end) FROM program_sessions_times WHERE program_id = programs.id) AS last_session_end
    FROM programs
    LEFT JOIN centres ON programs.centre_id = centres.id
    LEFT JOIN regionals ON programs.regional_id = regionals.id
    LEFT JOIN countries ON programs.country_id = countries.id
    $whereClause
    ORDER BY first_session_start DESC
    LIMIT :limit OFFSET :offset");
foreach ($params as $key => $val) {
  $stmt->bindValue(is_int($key) ? $key + 1 : ':' . $key, $val);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
$programNumbers = $pdo->query("SELECT program_number FROM programs ORDER BY program_number")->fetchAll(PDO::FETCH_ASSOC);

// Filter programs based on user permissions
$showPending = canUserViewProgram($pdo, 'Sponsors Program View');
if ($showPending) {
    // Show both pending and activated programs
    $programs = array_filter($programs, function($program) {
        return in_array($program['status'], ['pending', 'activated']);
    });
} else {
    // Show only activated programs
    $programs = array_filter($programs, function($program) {
        return $program['status'] === 'activated';
    });
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
      background: #f5f5f5;
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

    /* Hero Section */
    .hero-section {
      background: var(--primary-gradient);
      color: white;
      padding: 3rem 0;
      position: relative;
      overflow: hidden;
      margin-top: -20px;
    }

    .hero-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    }

    .hero-content {
      position: relative;
      z-index: 2;
    }

    /* Filter Section */
    .filter-section {
      background: white;
      border-radius: 10px;
      box-shadow: var(--card-shadow);
      padding: 1.5rem;
      margin-bottom: 2rem;
    }

    .filter-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .filter-buttons {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
    }

    .filter-dropdown {
      position: relative;
    }

    .filter-dropdown-menu {
      position: absolute;
      top: 100%;
      right: 0;
      width: 900px;
      /* wider dropdown */
      background: white;
      border-radius: 10px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      padding: 1rem;
      z-index: 100;
      display: none;
    }


    .filter-dropdown-menu.show {
      display: block;
    }

    .filter-dropdown-item {
      margin-bottom: 0.8rem;
    }

    .filter-dropdown-item label {
      font-size: 0.85rem;
      font-weight: 500;
      color: #4a5568;
      display: block;
      margin-bottom: 0.3rem;
    }

    .filter-dropdown-item input,
    .filter-dropdown-item select {
      width: 100%;
      padding: 0.5rem;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      font-size: 0.85rem;
    }

    .filter-dropdown-footer {
      display: flex;
      justify-content: space-between;
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid #e2e8f0;
    }

    /* Program Cards */
    .programs-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1.5rem;
    }

    .program-card {
      background: white;
      border-radius: 15px;
      box-shadow: var(--card-shadow);
      transition: all 0.3s ease;
      overflow: hidden;
      border: none;
      position: relative;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .program-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: var(--secondary-gradient);
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }

    .program-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--card-shadow-hover);
    }

    .program-card:hover::before {
      transform: scaleX(1);
    }

    .program-image {
      height: 150px;
      overflow: hidden;
      position: relative;
    }

    .program-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .program-card:hover .program-image img {
      transform: scale(1.05);
    }

    .program-badge {
      position: absolute;
      top: 0.8rem;
      right: 0.8rem;
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(5px);
      padding: 0.3rem 0.8rem;
      border-radius: 12px;
      font-size: 0.7rem;
      font-weight: 600;
      color: var(--primary-color);
      z-index: 2;
    }

    .card-header-custom {
      background: white;
      border: none;
      padding: 1rem;
      position: relative;
    }

    .program-number {
      background: var(--primary-gradient);
      color: white;
      padding: 0.2rem 0.7rem;
      border-radius: 15px;
      font-size: 0.75rem;
      font-weight: 600;
      display: inline-block;
      margin-bottom: 0.5rem;
    }

    .program-title {
      font-size: 1rem;
      font-weight: 700;
      color: #2d3748;
      margin-bottom: 0.5rem;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .card-body-custom {
      padding: 0 1rem 1rem;
      flex-grow: 1;
    }

    .info-item {
      display: flex;
      align-items: center;
      margin-bottom: 0.6rem;
      padding: 0.2rem 0;
    }

    .info-icon {
      width: 28px;
      height: 28px;
      background: var(--primary-gradient);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 0.6rem;
      color: white;
      font-size: 0.7rem;
      flex-shrink: 0;
    }

    .info-content {
      flex: 1;
      min-width: 0;
    }

    .info-label {
      font-size: 0.7rem;
      color: #718096;
      font-weight: 500;
      margin-bottom: 0.1rem;
    }

    .info-value {
      font-size: 0.8rem;
      color: #2d3748;
      font-weight: 600;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .participants-bar {
      background: #e2e8f0;
      height: 6px;
      border-radius: 3px;
      overflow: hidden;
      margin-top: 0.3rem;
    }

    .participants-fill {
      background: var(--secondary-gradient);
      height: 100%;
      border-radius: 3px;
      transition: width 0.8s ease;
      width: 50%;
    }

    .btn-marketing {
      background: var(--secondary-gradient);
      border: none;
      color: white;
      padding: 0.5rem 1.2rem;
      border-radius: 20px;
      font-weight: 600;
      font-size: 0.8rem;
      transition: all 0.3s ease;
      width: 100%;
    }

    .btn-marketing:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(244, 67, 54, 0.3);
    }

    .section-title {
      font-size: 2rem;
      font-weight: 700;
      color: var(--primary-color);
      margin-bottom: 1rem;
    }

    .section-subtitle {
      color: #718096;
      font-size: 1rem;
      margin-bottom: 2rem;
    }

    .pagination-container {
      display: flex;
      justify-content: center;
      margin-top: 2rem;
    }

    .page-item.active .page-link {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .page-link {
      color: var(--primary-color);
    }

    @media (max-width: 768px) {
      .hero-section {
        padding: 2rem 0;
      }

      .section-title {
        font-size: 1.8rem;
      }

      .programs-container {
        grid-template-columns: 1fr;
      }

      .filter-dropdown-menu {
        width: 400px;
      }
    }

    .fade-in {
      animation: fadeIn 0.6s ease-in;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body>


  <!-- Header -->
  <?php include 'Includes/nav.php';  ?>


  <!-- Hero Section -->
  <section class="hero-section">
    <div class="container">
      <div class="row justify-content-center text-center hero-content">
        <div class="col-lg-8">
          <h1 class="display-5 fw-bold mb-3">Sponsor With Us</h1>
          <p class="lead mb-4">Join our mission to create meaningful experiences and reach your target audience through strategic event sponsorship</p>
          <div class="d-flex justify-content-center gap-4 flex-wrap">
            <div class="text-center">
              <h3 class="fw-bold">50+</h3>
              <small>Events Annually</small>
            </div>
            <div class="text-center">
              <h3 class="fw-bold">10K+</h3>
              <small>Total Reach</small>
            </div>
            <div class="text-center">
              <h3 class="fw-bold">95%</h3>
              <small>Satisfaction Rate</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  

  <!-- Programs Section -->
  <section class="py-5">
    <div class="container">
      <div class="row justify-content-center text-center mb-4">
        <div class="col-lg-8">
          <h2 class="section-title">Upcoming Programs for Sponsorship</h2>
          <p class="section-subtitle">Discover exciting partnership opportunities with our upcoming events and workshops</p>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="filter-section">
        <div class="filter-header">
          <h5 class="mb-0">Filter Programs</h5>
          <div class="filter-buttons">
            <button type="reset" class="btn btn-sm btn-outline-secondary" onclick="window.location='<?= strtok($_SERVER['REQUEST_URI'], '?') ?>'">Reset</button>

            <div class="filter-dropdown">
              <button class="btn btn-sm btn-primary dropdown-toggle" id="filterDropdownButton">
                <i class="fas fa-filter me-1"></i>Filters
              </button>
              <div class="filter-dropdown-menu" id="filterDropdownMenu">
                <form method="GET" id="filterForm">
                  <div class="row">
                    <div class="col-md-4 mb-3">
                      <label for="country_id">Country</label>
                      <select id="country_id" name="country_id" class="form-select select2">
                        <option value="">All Countries</option>
                        <?php foreach ($countries as $country): ?>
                          <option value="<?= $country['id'] ?>"
                            <?= (isset($_GET['country_id']) && $_GET['country_id'] == $country['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($country['country_name']) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-4 mb-3">
                      <label for="regional_id">Region</label>
                      <select id="regional_id" name="regional_id" class="form-select select2">
                        <option value="">All Regions</option>
                        <?php foreach ($regionals as $state): ?>
                          <option value="<?= $state['id'] ?>"
                            <?= (isset($_GET['regional_id']) && $_GET['regional_id'] == $state['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($state['regional_name']) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-4 mb-3">
                      <label for="centre_id">Centre</label>
                      <select id="centre_id" name="centre_id" class="form-select select2">
                        <option value="">All Centres</option>
                        <?php foreach ($centres as $centre): ?>
                          <option value="<?= $centre['id'] ?>"
                            <?= (isset($_GET['centre_id']) && $_GET['centre_id'] == $centre['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($centre['centre_name']) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="reset" class="btn btn-sm btn-outline-secondary" onclick="window.location='<?= strtok($_SERVER['REQUEST_URI'], '?') ?>'">Reset</button>
                    <button type="submit" class="btn btn-sm btn-primary">Apply Filters</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search programs...">
          </div>
        </div>
      </div>

      <!-- Programs Grid -->
      <div class="programs-container">
        <?php if (empty($programs)): ?>
          <div class="text-center w-100">No programs found.</div>
          <?php else: foreach ($programs as $program): ?>
            <div class="program-card fade-in">
              <div class="program-image">
                <img src="<?= !empty($program['program_img']) ? '../../uploads/programs/' . htmlspecialchars($program['program_img']) : 'https://via.placeholder.com/500x150?text=No+Image' ?>" alt="<?= htmlspecialchars($program['title']) ?>">
              </div>
              <div class="card-header-custom">
                <div class="program-number">Program #<?= htmlspecialchars($program['program_number']) ?></div>
                <h3 class="program-title"><?= htmlspecialchars($program['title']) ?></h3>
              </div>
              <div class="card-body-custom">
                <div class="info-item">
                  <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                  <div class="info-content">
                    <div class="info-label">Location</div>
                    <div class="info-value"><?= htmlspecialchars($program['venue']) ?>, <?= htmlspecialchars($program['regional_name']) ?>, <?= htmlspecialchars($program['country_name']) ?></div>
                  </div>
                </div>
                <div class="info-item">
                  <div class="info-icon"><i class="fas fa-calendar-alt"></i></div>
                  <div class="info-content">
                    <div class="info-label">Date</div>
                    <div class="info-value">
                      <?php
                      if ($program['first_session_start']) {
                        echo date('F j, Y', strtotime($program['first_session_start']));
                      } else {
                        echo 'N/A';
                      }
                      ?>
                    </div>
                  </div>
                </div>
                <div class="info-item">
                  <div class="info-icon"><i class="fas fa-users"></i></div>
                  <div class="info-content">
                    <div class="info-label">Participants</div>
                    <div class="info-value"><?= $program['current_participants'] ?> / <?= $program['max_participants'] ?> registered</div>
                    <div class="participants-bar">
                      <div class="participants-fill" style="width: <?= $program['max_participants'] ? round($program['current_participants'] / $program['max_participants'] * 100) : 0 ?>%;"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer bg-transparent border-0 p-3">
                <a href="program-details.php?id=<?= $program['id'] ?>" class="btn btn-marketing">
                  <i class="fas fa-eye me-2"></i>View Details
                </a>
              </div>
            </div>
        <?php endforeach;
        endif; ?>
      </div>

      <!-- Pagination -->
      <div class="pagination-container">
        <nav aria-label="Program pagination">
          <ul class="pagination">
            <li class="page-item <?= $page === 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page - 1 ?>" tabindex="-1" aria-disabled="true">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?= $page === $totalPages ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-5" style="background: var(--primary-gradient);">
    <div class="container">
      <div class="row justify-content-center text-center text-white">
        <div class="col-lg-8">
          <h2 class="display-5 fw-bold mb-3">Ready to Sponsor With Us?</h2>
          <p class="lead mb-4">Join leading brands who trust us to deliver exceptional sponsorship experiences</p>
          <div class="d-flex gap-3 justify-content-center flex-wrap">
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Filter dropdown functionality
    const filterDropdownButton = document.getElementById('filterDropdownButton');
    const filterDropdownMenu = document.getElementById('filterDropdownMenu');
    const cancelFilters = document.getElementById('cancelFilters');
    const resetFilters = document.getElementById('resetFilters');
    const applyFilters = document.getElementById('applyFilters');

    filterDropdownButton.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      filterDropdownMenu.classList.toggle('show');
    });

    cancelFilters.addEventListener('click', function() {
      filterDropdownMenu.classList.remove('show');
    });

    resetFilters.addEventListener('click', function() {
      // Reset all filter inputs
      document.getElementById('programNumber').value = '';
      document.getElementById('programTitle').value = '';
      document.getElementById('country').value = '';
      document.getElementById('region').value = '';
      document.getElementById('centre').value = '';
      document.getElementById('instructor').value = '';
      document.getElementById('dateFrom').value = '';
      document.getElementById('dateTo').value = '';
      document.getElementById('programType').value = '';
      document.getElementById('searchInput').value = '';

      // Here you would typically re-fetch or show all programs
      alert('All filters have been reset. Showing all programs.');
    });

    applyFilters.addEventListener('click', function() {
      filterDropdownMenu.classList.remove('show');

      // Get all filter values
      const programNumber = document.getElementById('programNumber').value;
      const programTitle = document.getElementById('programTitle').value;
      const country = document.getElementById('country').value;
      const region = document.getElementById('region').value;
      const centre = document.getElementById('centre').value;
      const instructor = document.getElementById('instructor').value;
      const dateFrom = document.getElementById('dateFrom').value;
      const dateTo = document.getElementById('dateTo').value;
      const programType = document.getElementById('programType').value;

      // Here you would typically filter the programs based on these values
      console.log('Applying filters:', {
        programNumber,
        programTitle,
        country,
        region,
        centre,
        instructor,
        dateFrom,
        dateTo,
        programType
      });

      alert('Filters applied successfully!');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (!filterDropdownMenu.contains(e.target) && e.target !== filterDropdownButton) {
        filterDropdownMenu.classList.remove('show');
      }
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();
      const programCards = document.querySelectorAll('.program-card');

      programCards.forEach(card => {
        const title = card.querySelector('.program-title').textContent.toLowerCase();
        const number = card.querySelector('.program-number').textContent.toLowerCase();
        const location = card.querySelector('.info-value').textContent.toLowerCase();

        if (title.includes(searchTerm) || number.includes(searchTerm) || location.includes(searchTerm)) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    });

    // Add smooth animations on scroll
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, observerOptions);

    // Observe all cards
    document.querySelectorAll('.program-card').forEach(card => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
      observer.observe(card);
    });

    // Add click handlers for buttons
    document.querySelectorAll('.btn-marketing').forEach(btn => {
      btn.addEventListener('click', function() {
        // Add loading state
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
        this.disabled = true;

        // Simulate loading
        setTimeout(() => {
          this.innerHTML = originalText;
          this.disabled = false;
          // Here you would typically redirect to the marketing details page
          alert('Redirecting to program details page...');
        }, 1500);
      });
    });

    // Animate participant bars on page load
    setTimeout(() => {
      document.querySelectorAll('.participants-fill').forEach(bar => {
        bar.style.transition = 'width 1s ease';
      });
    }, 500);
  </script>
</body>

</html>