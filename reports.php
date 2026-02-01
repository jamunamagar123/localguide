<?php
include 'admin_guard.php';
include '../backend/connect.php';

/* -------- DASHBOARD STATS -------- */
// Total Bookings
$bookingsRes = $conn->query("SELECT COUNT(*) AS total FROM booking");
$totalBookings = $bookingsRes ? intval($bookingsRes->fetch_assoc()['total']) : 0;

// Total Users
$usersRes = $conn->query("SELECT COUNT(*) AS total FROM users");
$totalUsers = $usersRes ? intval($usersRes->fetch_assoc()['total']) : 0;

// Total Guides
$guidesRes = $conn->query("SELECT COUNT(*) AS total FROM guiders");
$totalGuides = $guidesRes ? intval($guidesRes->fetch_assoc()['total']) : 0;

/* -------- Total Revenue Table (Current Month) -------- */
$currentMonth = date('Y-m'); // YYYY-MM format

// Get all unique destination types
$typeRes = $conn->query("SELECT DISTINCT type FROM destination");
$types = [];
while($row = $typeRes->fetch_assoc()){
    $types[] = $row['type'];
}

// Initialize revenue array
$revTypes = [];
$totalRevenue = 0;

// Sum revenue per type for current month
foreach($types as $t){
    $revRes = $conn->query("
        SELECT IFNULL(SUM(b.total_amount),0) AS total
        FROM booking b
        JOIN destination d ON b.destination_id = d.destination_id
        WHERE d.type='$t'
        AND DATE_FORMAT(b.service_date,'%Y-%m')='$currentMonth'
    ");
    $rev = floatval($revRes->fetch_assoc()['total']);
    $revTypes[$t] = $rev;
    $totalRevenue += $rev;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Reports — Hello Pokhara</title>

<!-- Bootstrap CSS & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f6f9;
    margin: 0;
}
.sidebar {
    width: 240px;
    background: #023E8A;
    color: #fff;
    height: 100vh;
    position: fixed;
    padding-top: 20px;
}
.sidebar .nav-link {
    color: #fff;
    margin: 5px 10px;
    border-radius: 8px;
}
.sidebar .nav-link.active, .sidebar .nav-link:hover {
    background: #1565c0;
}
.main-content {
    margin-left: 240px;
    padding: 20px;
}
.card-icon { font-size: 28px; margin-right: 10px; color: #1565c0; }
.chart-box {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}
@media (max-width: 768px){
    .sidebar { position: relative; width: 100%; height: auto;}
    .main-content { margin-left: 0; }
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar d-flex flex-column">
    <h3 class="text-center mb-4">Hello Pokhara</h3>
    <nav class="nav flex-column">
        <a class="nav-link" href="dasbord.php"><i class="bi bi-house-door"></i> Dashboard</a>
        <a class="nav-link" href="bookings.php"><i class="bi bi-journal-bookmark"></i> Bookings</a>
        <a class="nav-link" href="destinations.php"><i class="bi bi-geo-alt"></i> Destinations</a>
        <a class="nav-link" href="users.php"><i class="bi bi-people"></i> Users</a>
        <a class="nav-link" href="guides.php"><i class="bi bi-compass"></i> Guides</a>
        <a class="nav-link" href="reviews.php"><i class="bi bi-chat-dots"></i> Reviews</a>
        <a class="nav-link active" href="reports.php"><i class="bi bi-bar-chart-line"></i> Reports</a>
        <a class="nav-link" href="settings.php"><i class="bi bi-gear"></i> Settings</a>
        <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">

    <h3 class="mb-4">Reports</h3>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3 d-flex align-items-center">
                <i class="bi bi-journal-bookmark card-icon"></i>
                <div>
                    <h6>Total Bookings</h6>
                    <p class="mb-0 fw-bold"><?= $totalBookings ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 d-flex align-items-center">
                <i class="bi bi-currency-exchange card-icon"></i>
                <div>
                    <h6>Total Revenue</h6>
                    <p class="mb-0 fw-bold">NPR <?= number_format($totalRevenue,2) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 d-flex align-items-center">
                <i class="bi bi-people card-icon"></i>
                <div>
                    <h6>Total Users</h6>
                    <p class="mb-0 fw-bold"><?= $totalUsers ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 d-flex align-items-center">
                <i class="bi bi-compass card-icon"></i>
                <div>
                    <h6>Total Guides</h6>
                    <p class="mb-0 fw-bold"><?= $totalGuides ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Table -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card chart-box">
                <h6>Revenue Table — <?= date('F Y') ?></h6>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Destination Type</th>
                            <th>Revenue (NPR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($revTypes as $type => $rev): ?>
                        <tr>
                            <td><?= htmlspecialchars($type) ?></td>
                            <td><?= number_format($rev,2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <th>Total Revenue</th>
                            <th><?= number_format($totalRevenue,2) ?></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
