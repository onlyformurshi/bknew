<?php

require '../../config/config.php';
require_once '../../config/functions.php';
// Fetch user info from session or database
$user = [
    'name' => 'John Doe',
    'username' => 'johndoe'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
      body { background: #f5f5f5; min-height: 100vh; padding-top: 80px; }
      .profile-card {
        background: #fff; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        max-width: 500px; margin: 40px auto; padding: 2rem 2.5rem;
      }
      .profile-card h2 { color: #d32f2f; font-weight: 700; }
      .profile-img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 1rem; }
      .navbar-brand img { height: 48px; }
    </style>
</head>
<body>
<?php include 'Includes/nav.php'; ?>

<div class="container">
  <div class="profile-card mt-5 text-center">
    <img src="https://randomuser.me/api/portraits/women/45.jpg" class="profile-img" alt="Profile">
    <h2 class="mb-3"><?= htmlspecialchars($user['name']) ?></h2>
    <p class="mb-1"><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
    <!-- Add more user details here if needed -->
    <a href="settings.php" class="btn btn-outline-primary mt-4"><i class="fas fa-cog me-2"></i>Edit Profile</a>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>