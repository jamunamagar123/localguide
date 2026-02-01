<?php
include 'admin_guard.php';
include '../backend/connect.php';

// Counts
$destinationsCount = $conn->query("SELECT COUNT(*) total FROM destination")->fetch_assoc()['total'];
$usersCount        = $conn->query("SELECT COUNT(*) total FROM users")->fetch_assoc()['total'];
$guidesCount       = $conn->query("SELECT COUNT(*) total FROM guiders WHERE status != 'rejected'")->fetch_assoc()['total'];
$bookingsCount     = $conn->query("SELECT COUNT(*) total FROM booking")->fetch_assoc()['total'];
$reviewsCount      = $conn->query("SELECT COUNT(*) total FROM reviews")->fetch_assoc()['total'];

// Fetch Pending Guiders
$pendingGuiders = $conn->query("SELECT * FROM guiders WHERE status='pending' ORDER BY created_at DESC");

// Fetch Recent Pending Bookings
$pendingBookings = $conn->query("
    SELECT b.booking_id, b.created_at, b.status,
           u.first_name AS user_first, u.last_name AS user_last,
           g.first_name AS guide_first, g.last_name AS guide_last,
           d.name AS destination
    FROM booking b
    JOIN users u ON b.user_id = u.user_id
    JOIN guiders g ON b.guider_id = g.guider_id
    JOIN destination d ON b.destination_id = d.destination_id
    WHERE b.status='pending'
    ORDER BY b.created_at DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hello Pokhara — Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Poppins,sans-serif}
body{display:flex;height:100vh;background:#f4f6f9;overflow:hidden}

/* Sidebar */
.sidebar{
  width:240px;background:#023E8A;color:#fff;
  padding:25px 15px;position:fixed;left:0;top:0;height:100vh
}
.sidebar .logo{text-align:center;margin-bottom:30px}
.sidebar a{
  display:flex;align-items:center;gap:10px;
  padding:12px 15px;margin:6px 0;color:#fff;
  text-decoration:none;border-radius:10px;transition:.3s
}
.sidebar a:hover,.sidebar a.active{
  background:#1565c0;transform:translateX(5px)
}

/* Main */
.main-content{
  margin-left:240px;flex:1;display:flex;
  flex-direction:column;height:100vh;overflow-y:auto
}

/* Topbar */
.topbar{
  background:#fff;padding:15px 25px;
  display:flex;justify-content:space-between;
  align-items:center;box-shadow:0 2px 8px rgba(0,0,0,.05)
}
.topbar input{
  padding:8px 15px;border-radius:20px;
  border:1px solid #ccc;width:220px
}
.topbar img{
  height: 50px;
  background-color: #e9ecef;
}
.profile{display:flex;align-items:center;gap:10px}
.profile img{width:40px;height:40px;border-radius:50%}

/* Dashboard */
.dashboard{
  padding:25px;
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
  gap:20px
}
.card{
  background:#fff;padding:25px;border-radius:15px;
  box-shadow:0 4px 12px rgba(0,0,0,.08)
}
.card h3{color:#023E8A;margin-bottom:10px}

/* Tables */
.table-card{background:#fff;padding:20px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,.08);margin-bottom:25px}
.table-card h3{color:#023E8A;margin-bottom:15px}
.table-responsive{overflow-x:auto}

/* Footer */
footer{
  text-align:center;padding:15px;
  background:#fff;border-top:1px solid #eee;font-size:14px
}

/* Responsive */
.menu-toggle{display:none;font-size:24px;cursor:pointer}

@media(max-width:768px){
  .sidebar{left:-240px;transition:.3s;z-index:1000}
  .sidebar.active{left:0}
  .main-content{margin-left:0}
  .menu-toggle{display:block}
}
</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="logo"><h2>Hello Pokhara</h2></div>

  <a href="dasbord.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
  <a href="bookings.php"><i class="bi bi-journal-check"></i> Bookings</a>
  <a href="destinations.php"><i class="bi bi-geo-alt"></i> Destinations</a>
  <a href="users.php"><i class="bi bi-people"></i> Users</a>
  <a href="guides.php"><i class="bi bi-compass"></i> Guides</a>
  <a href="reviews.php"><i class="bi bi-chat-dots"></i> Reviews</a>
  <a href="reports.php"><i class="bi bi-bar-chart"></i> Reports</a>
  <a href="settings.php"><i class="bi bi-gear"></i> Settings</a>
  <a href="../backend/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- Main -->
<div class="main-content">

  <div class="topbar">
    <span class="menu-toggle" onclick="toggleSidebar()"><i class="bi bi-list"></i></span>
    <img src="logo.jpeg">
    <div class="profile">
      <span><?= $_SESSION['name']; ?> Admin</span>
      <img src="<?= $_SESSION['photo'] ?? 'https://ui-avatars.com/api/?name='.urlencode($_SESSION['name']) ?>">
    </div>
  </div>

  <div class="dashboard">
    <div class="card"><h3>Bookings</h3><p><?= $bookingsCount ?></p></div>
    <div class="card"><h3>Destinations</h3><p><?= $destinationsCount ?></p></div>
    <div class="card"><h3>Users</h3><p><?= $usersCount ?></p></div>
    <div class="card"><h3>Guides</h3><p><?= $guidesCount ?></p></div>
    <div class="card"><h3>Reviews</h3><p><?= $reviewsCount ?></p></div>
  </div>

  <!-- Pending Guiders Table -->
  <div class="table-card">
    <h3>Pending Guiders</h3>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Language</th>
            <th>Role</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if($pendingGuiders && $pendingGuiders->num_rows > 0): ?>
            <?php $i=1; while($row=$pendingGuiders->fetch_assoc()): ?>
              <tr>
                <td><?= $i++; ?></td>
                <td><?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone_number']) ?></td>
                <td><?= htmlspecialchars($row['language']) ?></td>
                <td><?= htmlspecialchars($row['role']) ?></td>
                <td>Rs. <?= htmlspecialchars($row['gprice']) ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td>
                  <a href="../backend/approve_guide.php?id=<?= $row['guider_id'] ?>" class="btn btn-sm btn-success mb-1">Approve</a>
                  <a href="../backend/reject_guide.php?id=<?= $row['guider_id'] ?>" class="btn btn-sm btn-danger mb-1">Reject</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" class="text-center">No pending guiders</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Recent Pending Bookings Table -->
  <div class="table-card">
    <h3>Recent Pending Bookings</h3>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Booking ID</th>
            <th>User</th>
            <th>Guider</th>
            <th>Destination</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if($pendingBookings && $pendingBookings->num_rows > 0): ?>
            <?php $i=1; while($row=$pendingBookings->fetch_assoc()): ?>
              <tr>
                <td><?= $i++; ?></td>
                <td><?= $row['booking_id'] ?></td>
                <td><?= htmlspecialchars($row['user_first'].' '.$row['user_last']) ?></td>
                <td><?= htmlspecialchars($row['guide_first'].' '.$row['guide_last']) ?></td>
                <td><?= htmlspecialchars($row['destination']) ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td><?= $row['created_at'] ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center">No pending bookings</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <footer>© 2025 Hello Pokhara Admin Panel</footer>
</div>

<div class="overlay" onclick="toggleSidebar()"></div>

<script>
function toggleSidebar(){
  document.querySelector('.sidebar').classList.toggle('active');
  document.querySelector('.overlay').classList.toggle('active');
}
</script>

</body>
</html>
