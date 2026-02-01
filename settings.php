<?php
include 'admin_guard.php';
include '../backend/connect.php';

// Get admin email from session
$adminEmail = $_SESSION['email'] ?? '';

// Fetch admin record from database
$adminRes = $conn->query("SELECT * FROM admin WHERE email='$adminEmail' LIMIT 1");
if(!$adminRes || $adminRes->num_rows == 0){
    echo "Admin record not found in database. Cannot update settings.";
    exit;
}
$admin = $adminRes->fetch_assoc();

$success = '';
$error = '';

// Handle profile update (only name)
if(isset($_POST['update_profile'])){
    $name = $conn->real_escape_string($_POST['name']);

    $update = $conn->query("UPDATE admin SET name='$name' WHERE admin_id=".$admin['admin_id']);
    if($update){
        $success = "Profile updated successfully!";
        $admin['name'] = $name; // update local variable
    } else {
        $error = "Failed to update profile.";
    }
}

// Handle password change
if(isset($_POST['change_password'])){
    $current = $_POST['current_password'];
    $new     = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if(!password_verify($current, $admin['password'])){
        $error = "Current password is incorrect.";
    } elseif($new !== $confirm){
        $error = "New passwords do not match.";
    } else {
        $new_hash = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE admin SET password='$new_hash' WHERE admin_id=".$admin['admin_id']);
        $success = "Password changed successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Settings</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f4f6f9; font-family: 'Poppins', sans-serif; }
.container { margin-top: 40px; max-width: 700px; }
.card-header { background-color: #ebeff5; color:#0c4298; font-weight:600; }
.btn-back { text-decoration:none; color:#fff; background:#1565c0; padding:6px 12px; border-radius:6px; }
.btn-back:hover { background:#0d47a1; text-decoration:none; }
</style>
</head>
<body>

<div class="container">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Profile Settings</span>
            <a href="dasbord.php" class="btn-back">← Back to Dashboard</a>
        </div>
        <div class="card-body">
            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($admin['name']) ?>" required>
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Change Password</div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
