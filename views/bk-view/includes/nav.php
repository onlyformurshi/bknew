<?php
require_once '../../helpers/site-url.php';
?>
<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="#">
      <img src="../assets/images/bk-logo.png" alt="Brahma Kumaris Logo">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link active" href="<?= SITE_URL ?>/views/bk-view/"><i class="fas fa-home me-1"></i> Home</a>
        </li>
      
      </ul>
    </div>
  </div>
</nav>

<script>
    const SITE_URL = "<?= SITE_URL ?>";
</script>
