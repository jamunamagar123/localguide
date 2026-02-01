<?php
include 'admin_guard.php';
include '../backend/connect.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: users.php");
    exit();
}

// Fetch all users
$result = $conn->query("SELECT user_id, first_name, last_name, email, created_at FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Users</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f6f9;
}

.container {
    margin-top: 40px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #ebeff5ff;
    color: #0c4298ff;
    padding: 15px 20px;
    border-radius: 5px 5px 0 0;
}

.card-header h3 {
    margin: 0;
}

.table th, .table td {
    vertical-align: middle;
}

.table thead {
    background-color: #1565c0;
    color: #fff;
}

.btn-back {
    text-decoration: none;
    color: #fff;
    font-weight: 500;
    background-color: #1565c0;
    padding: 6px 12px;
    border-radius: 6px;
    transition: 0.3s;
}

.btn-back:hover {
    background-color: #0d47a1;
    text-decoration: none;
}

.btn-sm.btn-primary {
    background-color: #023E8A;
    border-color: #023E8A;
}

.btn-sm.btn-primary:hover {
    background-color: #1565c0;
    border-color: #1565c0;
}

.btn-sm.btn-danger {
    background-color: #d32f2f;
    border-color: #d32f2f;
}

.btn-sm.btn-danger:hover {
    background-color: #b71c1c;
    border-color: #b71c1c;
}

.btn-sm.btn-warning {
    background-color: #ff9800;
    border-color: #ff9800;
    color: #fff;
}

.btn-sm.btn-warning:hover {
    background-color: #fb8c00;
    border-color: #fb8c00;
    color: #fff;
}
</style>
</head>

<body>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Users</h3>
            <div>
                <a href="dasbord.php" class="btn-back me-3">← Back to Dashboard</a>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= htmlspecialchars($row['first_name']); ?></td>
                                <td><?= htmlspecialchars($row['last_name']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td><?= htmlspecialchars($row['created_at']); ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?= $row['user_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="users.php?delete=<?= $row['user_id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this user?')">
                                       Delete
                                    </a>
                                    <a href="user_reviews.php?user_id=<?= $row['user_id']; ?>" 
                                       class="btn btn-sm btn-warning">
                                       Reviews
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
