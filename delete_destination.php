<?php
include 'admin_guard.php';      // Make sure admin is logged in
include '../backend/connect.php'; // Database connection

// Check if id is provided
if(!isset($_GET['id']) || empty($_GET['id'])){
    header("Location: destinations.php?msg=invalid");
    exit();
}

$id = intval($_GET['id']); // sanitize input

// 1️⃣ Fetch image file name to delete from server
$sql = "SELECT image FROM destination WHERE destination_id = $id";
$result = $conn->query($sql);

if($result && $result->num_rows > 0){
    $row = $result->fetch_assoc();
    $imageFile = $row['image'];

    // 2️⃣ Delete related bookings & reviews first
    $conn->query("DELETE FROM booking WHERE destination_id = $id");
    $conn->query("DELETE FROM reviews WHERE destination_id = $id");

    // 3️⃣ Delete image file if exists
    if(!empty($imageFile) && file_exists("../uploads/$imageFile")){
        unlink("../uploads/$imageFile");
    }

    // 4️⃣ Delete the destination from database
    $deleteSql = "DELETE FROM destination WHERE destination_id = $id";
    if($conn->query($deleteSql)){
        // Success: redirect back
        header("Location: destinations.php?msg=deleted");
        exit();
    } else {
        die("Error deleting destination: " . $conn->error);
    }
} else {
    // No such destination found
    header("Location: destinations.php?msg=notfound");
    exit();
}
?>
