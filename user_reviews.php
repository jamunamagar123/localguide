<?php
// user_reviews.php
include 'admin_guard.php';
include '../backend/connect.php';

// --- Step 1: Check connection ---
if (!$conn) {
    die("Database connection variable \$conn not found!");
}

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// --- Step 2: Get user_id from query string ---
if (!isset($_GET['user_id'])) {
    header("Location: user.php");
    exit();
}
$user_id = (int)$_GET['user_id'];

// --- Step 3: Fetch user information ---
$stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE user_id = ?");
if (!$stmt) { die("Prepare failed (user): " . $conn->error); }
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();

if ($userResult->num_rows === 0) {
    die("User not found!");
}

$user = $userResult->fetch_assoc();
$stmt->close();

// --- Step 4: Fetch reviews for this user ---
$sql = "SELECT r.review_id, r.rating, r.comment, r.created_at, 
               CONCAT(g.first_name, ' ', g.last_name) AS guider_name
        FROM reviews r
        LEFT JOIN guiders g ON r.guider_id = g.guider_id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) { die("Prepare failed (reviews): " . $conn->error); }
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reviews = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin – User Reviews</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f4f6f9; font-family: Arial, sans-serif; }
.container { margin-top: 40px; }
.card-header { display: flex; justify-content: space-between; align-items: center; background: #ebeff5; padding: 15px 20px; border-radius: 5px 5px 0 0; }
.card-header h3 { margin: 0; }
.btn-back { text-decoration: none; color: #fff; background-color: #1565c0; padding: 6px 12px; border-radius: 6px; }
.btn-back:hover { background-color: #0d47a1; }
.table thead { background-color: #1565c0; color: #fff; }
</style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Reviews of <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
            <a href="users.php" class="btn-back">← Back to Users</a>
        </div>
        <div class="card-body">
            <?php if($reviews && $reviews->num_rows > 0): ?>
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Guider</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; while($row = $reviews->fetch_assoc()): ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= htmlspecialchars($row['guider_name'] ?? 'N/A'); ?></td>
                                <td><?= htmlspecialchars($row['rating']); ?> ⭐</td>
                                <td><?= htmlspecialchars($row['comment']); ?></td>
                                <td><?= $row['created_at']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">No reviews found for this user.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
