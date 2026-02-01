<?php
include 'admin_guard.php';
include '../backend/connect.php';

// Handle Delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM booking WHERE booking_id=$id");
    header("Location: bookings.php");
    exit();
}

// Fetch all bookings
$result = $conn->query("SELECT * FROM booking ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Bookings</title>
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
    background-color: #ebeff5; 
    color: #0c4298;
    padding: 15px 20px;
    border-radius: 5px 5px 0 0;
}

.card-header h3 { margin: 0; }

.btn-back {
    text-decoration: none;
    color: #fff; 
    font-weight: 500;
    background-color: #1565c0;
    padding: 6px 12px;
    border-radius: 6px;
    transition: 0.3s;
}

.btn-back:hover { background-color: #0d47a1; text-decoration:none; }

.table thead { background-color: #1565c0; color: #fff; }
.table tbody tr td { vertical-align: middle; font-size: 0.9rem; }

.status-waiting { background-color: #fbc02d; color: #fff; padding:4px 8px; border-radius:5px; font-weight:500; }
.status-accepted { background-color: #43a047; color: #fff; padding:4px 8px; border-radius:5px; font-weight:500; }
.status-cancelled { background-color: #e53935; color: #fff; padding:4px 8px; border-radius:5px; font-weight:500; }

.btn-sm.btn-primary { background-color: #023E8A; border-color: #023E8A; }
.btn-sm.btn-primary:hover { background-color: #1565c0; border-color: #1565c0; }
.btn-sm.btn-danger { background-color: #d32f2f; border-color: #d32f2f; }
.btn-sm.btn-danger:hover { background-color: #b71c1c; border-color: #b71c1c; }

@media(max-width: 768px){
    .table thead { display: none; }
    .table, .table tbody, .table tr, .table td { display: block; width: 100%; }
    .table tr { margin-bottom: 15px; }
    .table td { text-align: right; padding-left: 50%; position: relative; }
    .table td::before { 
        content: attr(data-label); 
        position: absolute;
        left: 15px;
        width: 45%; 
        text-align: left;
        font-weight: 600;
    }
}
</style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Bookings</h3>
            <a href="dasbord.php" class="btn-back">← Back to Dashboard</a>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Participants</th>
                        <th>Original Price</th>
                        <th>Discount Price</th>
                        <th>Savings</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Booked At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result && $result->num_rows > 0): ?>
                        <?php $i=1; while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td data-label="#"><?= $i++; ?></td>
                                <td data-label="Full Name"><?= htmlspecialchars($row['full_name']) ?></td>
                                <td data-label="Email"><?= htmlspecialchars($row['email']) ?></td>
                                <td data-label="Phone"><?= htmlspecialchars($row['phone']) ?></td>
                                <td data-label="Service"><?= htmlspecialchars($row['service_name']) ?></td>
                                <td data-label="Date"><?= htmlspecialchars($row['service_date']) ?></td>
                                <td data-label="Participants"><?= htmlspecialchars($row['number_of_people']) ?></td>
                                <td data-label="Original Price">$<?= number_format($row['original_price'],2) ?></td>
                                <td data-label="Discount Price">$ <?= number_format($row['discounted_price'],2) ?></td>
                                <td data-label="Savings">$ <?= number_format($row['savings'],2) ?></td>
                                <td data-label="Total">$ <?= number_format($row['total_amount'],2) ?></td>
                                <td data-label="Status">
                                    <?php 
                                        $status = strtolower($row['booking_status']);
                                        if($status=='waiting') echo '<span class="status-waiting">Waiting</span>';
                                        elseif($status=='accepted') echo '<span class="status-accepted">Accepted</span>';
                                        elseif($status=='cancelled') echo '<span class="status-cancelled">Cancelled</span>';
                                        else echo htmlspecialchars($row['booking_status']);
                                    ?>
                                </td>
                                <td data-label="Payment"><?= htmlspecialchars($row['payment_method']) ?></td>
                                <td data-label="Booked At"><?= $row['created_at'] ?></td>
                                <td data-label="Actions">
                                    <a href="edit_booking.php?id=<?= $row['booking_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="bookings.php?delete=<?= $row['booking_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this booking?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="15" class="text-center">No bookings found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
