<?php
include 'admin_guard.php';
include '../backend/connect.php';

// Handle Delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);

    // Get image file
   // get all image names
$imgQuery = $conn->query("SELECT image, image2, image3, image4, image5 FROM destination WHERE destination_id=$id");
if ($imgQuery && $imgQuery->num_rows) {
    $imgData = $imgQuery->fetch_assoc();
    
    // loop through each image column
    foreach (['image','image2','image3','image4','image5'] as $imgCol) {
        if (!empty($imgData[$imgCol]) && file_exists("../uploads/".$imgData[$imgCol])) {
            unlink("../uploads/".$imgData[$imgCol]);
        }
    }
}


    // Delete related bookings and reviews
    $conn->query("DELETE FROM booking WHERE destination_id=$id");
    $conn->query("DELETE FROM reviews WHERE destination_id=$id");

    // Delete the destination itself
    $conn->query("DELETE FROM destination WHERE destination_id=$id");

    // Redirect with success message
    header("Location: destinations.php?msg=deleted");
    exit();
}

// Fetch all destinations
$result = $conn->query("SELECT * FROM destination ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Destinations</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; background: #f4f6f9; }
.container { margin-top: 40px; }
.card-header { display: flex; justify-content: space-between; align-items: center; background-color: #ebeff5; color: #0c4298; padding: 15px 20px; border-radius: 5px 5px 0 0; }
.card-header h3 { margin: 0; }
.table th, .table td { vertical-align: middle; }
.table thead { background-color: #1565c0; color: #fff; }
.btn-back { text-decoration: none; color: #fff; font-weight: 500; background-color: #1565c0; padding: 6px 12px; border-radius: 6px; transition: 0.3s; }
.btn-back:hover { background-color: #0d47a1; text-decoration: none; }
.btn-primary, .btn-sm.btn-primary { background-color: #023E8A; border-color: #023E8A; transition: 0.3s; }
.btn-primary:hover, .btn-sm.btn-primary:hover { background-color: #1565c0; border-color: #1565c0; }
.btn-sm.btn-danger { background-color: #d32f2f; border-color: #d32f2f; }
.btn-sm.btn-danger:hover { background-color: #b71c1c; border-color: #b71c1c; }
img { border-radius: 6px; }
</style>
</head>
<body>
<div class="container">
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success text-center">Destination deleted successfully!</div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h3>Destinations</h3>
            <div>
                <a href="dasbord.php" class="btn-back me-3">← Back to Dashboard</a>
                <a href="add_destination.php" class="btn btn-primary">+ Add New Destination</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Discount Price</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result && $result->num_rows > 0): ?>
                        <?php $i=1; while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= htmlspecialchars($row['name']); ?></td>
                                <td><?= htmlspecialchars($row['location']); ?></td>
                                <td><?= htmlspecialchars($row['category']); ?></td>
                                <td><?= htmlspecialchars($row['type']); ?></td>
                                <td><?= htmlspecialchars($row['price']); ?></td>
                                <td><?= htmlspecialchars($row['discount_price']); ?></td>
                                <td>
                                    <?php if(!empty($row['image']) && file_exists("../uploads/".$row['image'])): ?>
                                        <img src="../uploads/<?= $row['image']; ?>" alt="<?= htmlspecialchars($row['name']); ?>" width="80">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit_destination.php?id=<?= $row['destination_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="destinations.php?delete=<?= $row['destination_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this destination?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center">No destinations found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
