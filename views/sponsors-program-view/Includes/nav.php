<?php require_once '../../helpers/site-url.php'; ?>
<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="<?= SITE_URL ?>/views/sponsors-program-view/">
      <img src="<?= SITE_URL ?>/views/sponsors-program-view/assets/images/bk-logo.png" alt="Brahma Kumawris Logo">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav ms-auto">
        
        <li class="nav-item">
          <a class="nav-link" href="<?= SITE_URL ?>/views/sponsors-program-view/"><i class="fas fa-calendar-alt me-1"></i> Programs</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= SITE_URL ?>/views/sponsors-program-view/how-its-work.php"><i class="fas fa-question-circle me-1"></i> How It Works</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= SITE_URL ?>/views/sponsors-program-view/my-sponsorship.php"><i class="fas fa-hand-holding-heart me-1"></i> My Sponsorships</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://randomuser.me/api/portraits/women/45.jpg" class="profile-img me-1" alt="Profile">
            My Profile
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
            <li><a class="dropdown-item" href="<?= SITE_URL ?>/views/sponsors-program-view/profile.php"><i class="fas fa-cog me-2"></i> Profile</a></li>
            <li><a class="dropdown-item" href="<?= SITE_URL ?>/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>