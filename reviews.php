<?php
session_start();
include '../backend/connect.php';

// Ensure admin is logged in (optional)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch all reviews with user, guider, and service info
$sql = "
SELECT r.review_id, r.booking_id, r.rating, r.comment, r.created_at,
       CONCAT(u.first_name, ' ', u.last_name) AS user_name, u.email AS user_email,
       CONCAT(g.first_name, ' ', g.last_name) AS guider_name, g.email AS guider_email,
       b.service_name, b.service_date
FROM reviews r
JOIN booking b ON r.booking_id = b.booking_id
JOIN users u ON r.user_id = u.user_id
JOIN guiders g ON r.guider_id = g.guider_id
ORDER BY r.created_at DESC
";

$review_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Reviews</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; background: #f4f6f9; }
.container { margin-top: 50px; }
h1 { text-align: center; color: #023E8A; margin-bottom: 20px; }
.btn-back { text-decoration: none; color: #fff; background-color: #023E8A; padding: 8px 16px; border-radius: 8px; display:inline-block; margin-bottom:10px; }
.btn-back:hover { background-color: #1565c0; }
.table th, .table td { text-align:center; vertical-align:middle; }
.stars { color: #ffb400; }
.table-hover tbody tr:hover { background-color: #e3f2fd; }
</style>
</head>
<body>
<div class="container">
    <h1>All Reviews</h1>
    <a href="dasbord.php" class="btn-back">← Back to Dashboard</a>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Review ID</th>
                    <th>User</th>
                    <th>Guider</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Submitted On</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($review_result && $review_result->num_rows > 0): ?>
                    <?php while ($review = $review_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $review['review_id'] ?></td>
                            <td>
                                <?= htmlspecialchars($review['user_name']) ?><br>
                                <small><?= htmlspecialchars($review['user_email']) ?></small>
                            </td>
                            <td>
                                <?= htmlspecialchars($review['guider_name']) ?><br>
                                <small><?= htmlspecialchars($review['guider_email']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($review['service_name']) ?></td>
                            <td><?= htmlspecialchars($review['service_date']) ?></td>
                            <td>
                                <span class="stars">
                                    <?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?>
                                </span>
                                (<?= $review['rating'] ?>/5)
                            </td>
                            <td><?= htmlspecialchars($review['comment']) ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($review['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center">No reviews found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
