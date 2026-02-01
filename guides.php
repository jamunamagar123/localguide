<?php
include 'admin_guard.php'; // Admin authentication
include '../backend/connect.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Delete guider photos
    $imgQuery = $conn->query("SELECT guide_photo, citizenship_photo FROM guiders WHERE guider_id=$id");
    if ($imgQuery && $imgQuery->num_rows > 0) {
        $imgData = $imgQuery->fetch_assoc();
        $paths = ["../gprofile/" . $imgData['guide_photo'], "../gphoto/" . $imgData['citizenship_photo']];
        foreach ($paths as $path) {
            if (!empty($path) && file_exists($path)) unlink($path);
        }
    }

    // Delete reviews
    $conn->query("DELETE FROM reviews WHERE guider_id=$id");

    // Delete guider
    $conn->query("DELETE FROM guiders WHERE guider_id=$id");

    header("Location: guides.php");
    exit();
}

// Fetch all guiders with all relevant signup data
$result = $conn->query("SELECT * FROM guiders ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin — Guiders</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; background: #f4f6f9; }
.container { margin-top: 40px; }
.card-header { display: flex; justify-content: space-between; align-items: center; background-color: #ebeff5ff; color: #0c4298ff; padding: 15px 20px; }
img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; }
td img { max-width: 80px; max-height: 80px; }
.btn { min-width: 90px; }
</style>
</head>

<body>
<div class="container">
<div class="card">
<div class="card-header">
    <h3>Guiders</h3>
    <a href="dasbord.php" class="btn btn-primary">← Back</a>
</div>

<div class="card-body table-responsive">
<table class="table table-bordered table-hover">
<thead class="table-dark">
<tr>
    <th>#</th>
    <th>Profile Photo</th>
    <th>Full Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Language</th>
    <th>Role</th>
    <th>Price (Rs.)</th>
    <th>Citizenship Photo</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>
<?php if ($result && $result->num_rows > 0): ?>
<?php $i = 1; while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $i++; ?></td>

    <!-- Profile Photo -->
    <td>
        <?php
        $profilePath = "../gprofile/" . $row['guide_photo'];
        if (!empty($row['guide_photo']) && file_exists($profilePath)): ?>
            <img src="<?= $profilePath ?>">
        <?php else: ?>
            No Photo
        <?php endif; ?>
    </td>

    <!-- Full Name -->
    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>

    <!-- Email -->
    <td><?= htmlspecialchars($row['email']); ?></td>

    <!-- Phone -->
    <td><?= htmlspecialchars($row['phone_number']); ?></td>

    <!-- Language -->
    <td><?= htmlspecialchars($row['language']); ?></td>

    <!-- Role -->
    <td><?= htmlspecialchars($row['role']); ?></td>

    <!-- Price -->
    <td>Rs. <?= htmlspecialchars($row['gprice']); ?></td>

    <!-- Citizenship Photo -->
    <td>
        <?php
        $citizenPath = "../gphoto/" . $row['citizenship_photo'];
        if (!empty($row['citizenship_photo']) && file_exists($citizenPath)): ?>
            <img src="<?= $citizenPath ?>">
        <?php else: ?>
            No Photo
        <?php endif; ?>
    </td>

    <!-- Status -->
    <td><?= htmlspecialchars(ucfirst($row['status'])); ?></td>

    <!-- Actions -->
    <td>
        <?php if ($row['status'] === 'pending'): ?>
            <a href="../backend/approve_guide.php?id=<?= $row['guider_id']; ?>" class="btn btn-sm btn-success mb-1" onclick="return confirm('Approve this guider?')">
               <i class="bi bi-check-circle"></i> Approve
            </a>
            <a href="../backend/reject_guide.php?id=<?= $row['guider_id']; ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Reject this guider?')">
               <i class="bi bi-x-circle"></i> Reject
            </a>
            <a href="edit_price.php?id=<?= $row['guider_id']; ?>" class="btn btn-sm btn-primary mb-1">
               <i class="bi bi-currency-dollar"></i> Price
            </a>
        <?php elseif ($row['status'] === 'approved'): ?>
            <a href="edit_price.php?id=<?= $row['guider_id']; ?>" class="btn btn-sm btn-primary mb-1">
               <i class="bi bi-currency-dollar"></i> Price
            </a>
        <?php elseif ($row['status'] === 'rejected'): ?>
            <span class="text-muted">No actions</span>
        <?php endif; ?>

        <!-- Delete always visible -->
        <a href="guides.php?delete=<?= $row['guider_id']; ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Are you sure you want to delete this guider?')">
            <i class="bi bi-trash"></i> Delete
        </a>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="11" class="text-center">No guiders found</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</body>
</html>
