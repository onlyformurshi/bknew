<?php
<?php
require '../../config/config.php';
require_once '../../config/functions.php';
// You should check if the user is logged in and fetch their current details
// Example: $user = getCurrentUser($pdo);

$user = [
    'name' => 'John Doe',
    'username' => 'johndoe'
    // Do not fetch password!
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2>Edit Profile</h2>
    <form method="post" action="">
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">New Password <small>(leave blank to keep current)</small></label>
            <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
</body>
</html>