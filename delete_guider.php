<?php
include 'admin_guard.php';
include '../backend/connect.php';

if (!isset($_GET['id'])) {
    header("Location: guiders.php");
    exit();
}

$guiderId = intval($_GET['id']);

// Delete profile photo
$photoQuery = $conn->query("SELECT guide_photo FROM guiders WHERE guider_id=$guiderId");
if ($photoQuery && $photoQuery->num_rows > 0) {
    $photo = $photoQuery->fetch_assoc()['guide_photo'];
    if(!empty($photo) && file_exists("../gprofile/".$photo)) {
        unlink("../gprofile/".$photo);
    }
}

// Delete guider
$conn->query("DELETE FROM guiders WHERE guider_id=$guiderId");

header("Location: guiders.php?success=Guider deleted successfully");
exit();
?>
