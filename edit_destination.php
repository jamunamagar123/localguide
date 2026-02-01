<?php
include 'admin_guard.php';
include '../backend/connect.php';

$id = $_GET['id'] ?? null;
if(!$id) { header("Location: destinations.php"); exit(); }

$sql = "SELECT * FROM destination WHERE destination_id='$id'";
$result = $conn->query($sql);
if($result->num_rows == 0) { header("Location: destinations.php"); exit(); }
$row = $result->fetch_assoc();

if(isset($_POST['update'])){
    $name = $_POST['name'];
    $location = $_POST['location'];
    $type = $_POST['type'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $discount_price = $_POST['discount_price'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $image = $row['image'];
    $image2 = $row['image2'];
    $image3 = $row['image3'];
    $image4 = $row['image4'];
    $image5 = $row['image5'];

    if(!empty($_FILES['image']['name'])){
        $image = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/$image");
    }
    if(!empty($_FILES['image2']['name'])){
        $image2 = time() . '_' . $_FILES['image2']['name'];
        move_uploaded_file($_FILES['image2']['tmp_name'], "../uploads/$image2");
    }
    if(!empty($_FILES['image3']['name'])){
        $image3 = time() . '_' . $_FILES['image3']['name'];
        move_uploaded_file($_FILES['image3']['tmp_name'], "../uploads/$image3");
    }
    if(!empty($_FILES['image4']['name'])){
        $image4 = time() . '_' . $_FILES['image4']['name'];
        move_uploaded_file($_FILES['image4']['tmp_name'], "../uploads/$image4");
    }
    if(!empty($_FILES['image5']['name'])){
        $image5 = time() . '_' . $_FILES['image5']['name'];
        move_uploaded_file($_FILES['image5']['tmp_name'], "../uploads/$image5");
    }

    $sql = "UPDATE destination SET
            name='$name',
            location='$location',
            type='$type',
            description='$description',
            category='$category',
            price='$price',
            discount_price='$discount_price',
            image='$image',
            image2='$image2',
            image3='$image3',
            image4='$image4',
            image5='$image5',
            latitude='$latitude',
            longitude='$longitude'
            WHERE destination_id='$id'";

    if($conn->query($sql)){
        header("Location: destinations.php");
        exit();
    } else {
        $error = "Error updating: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Destination — Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
body { font-family:'Poppins', sans-serif; background:#f4f6f9; margin:0; padding:0;}
.container { max-width:700px; margin:50px auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);}
h2 { color:#023E8A; margin-bottom:25px; text-align:center; }
form label { font-weight:500; margin-top:10px; }
form input, form textarea, form select { width:100%; padding:10px; margin-top:5px; border-radius:8px; border:1px solid #ccc; }
form input[type="file"] { padding:3px; }
button { margin-top:20px; background:#023E8A; color:white; padding:10px 20px; border:none; border-radius:8px; cursor:pointer; transition:0.3s;}
button:hover { background:#1565c0; }
.error { color:red; margin-top:10px; }
select { appearance: none; -webkit-appearance: none; -moz-appearance: none; background:url('data:image/svg+xml;utf8,<svg fill="gray" height="12" viewBox="0 0 24 24" width="12" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 10px center; background-color:white; background-size:12px; }
#map { height:300px; margin-top:15px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
</style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Edit Destination</h2>
    <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" value="<?= htmlspecialchars($row['location']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Type</label>
            <?php $type_db = trim($row['type']); ?>
            <select name="type" class="form-select" required>
                <option value="">-- Select Type --</option>
                <option value="Villa" <?= $type_db === 'Villa' ? 'selected' : '' ?>>Villa</option>
                <option value="Hotel" <?= $type_db === 'Hotel' ? 'selected' : '' ?>>Hotel</option>
                <option value="Apartment" <?= $type_db === 'Apartment' ? 'selected' : '' ?>>Apartment</option>
                <option value="Tour" <?= $type_db === 'Tour' ? 'selected' : '' ?>>Tour</option>
                <option value="Activity" <?= $type_db === 'Activity' ? 'selected' : '' ?>>Activity</option>
                <option value="Flight" <?= $type_db === 'Flight' ? 'selected' : '' ?>>Flight</option>
                <option value="Offers" <?= $type_db === 'Offers' ? 'selected' : '' ?>>Offers</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" rows="5" class="form-control" required><?= htmlspecialchars($row['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <?php $category_db = trim($row['category']); ?>
            <select name="category" class="form-select" required>
                <option value="">-- Select Category --</option>
                <option value="Scenic Point" <?= $category_db === 'Scenic Point' ? 'selected' : '' ?>>Scenic Point</option>
                <option value="Adventure" <?= $category_db === 'Adventure' ? 'selected' : '' ?>>Adventure</option>
                <option value="Trekking" <?= $category_db === 'Trekking' ? 'selected' : '' ?>>Trekking</option>
                <option value="Water Sports" <?= $category_db === 'Water Sports' ? 'selected' : '' ?>>Water Sports</option>
                <option value="Sightseeing" <?= $category_db === 'Sightseeing' ? 'selected' : '' ?>>Sightseeing</option>
                <option value="Cultural" <?= $category_db === 'Cultural' ? 'selected' : '' ?>>Cultural</option>
                <option value="Religious" <?= $category_db === 'Religious' ? 'selected' : '' ?>>Religious</option>
                <option value="Hill Point" <?= $category_db === 'Hill Point' ? 'selected' : '' ?>>Hill Point</option>
                <option value="Senior Point" <?= $category_db === 'Senior Point' ? 'selected' : '' ?>>Senior Point</option>
            </select>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Price</label>
                <input type="text" name="price" value="<?= htmlspecialchars($row['price']) ?>" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Discount Price (optional)</label>
                <input type="text" name="discount_price" value="<?= htmlspecialchars($row['discount_price']) ?>" class="form-control">
            </div>
        </div>

        <!-- Current & Upload Images -->
        <div class="mb-3">
            <label class="form-label">Current Image</label><br>
            <?php if($row['image']): ?>
                <img src="../uploads/<?= $row['image'] ?>" alt="Current Image" style="width:150px; margin-bottom:10px;">
            <?php else: ?>
                <p>No image uploaded</p>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label class="form-label">Main Image</label>
            <input type="file" name="image" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Additional Image 2</label>
            <input type="file" name="image2" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Additional Image 3</label>
            <input type="file" name="image3" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Additional Image 4</label>
            <input type="file" name="image4" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Additional Image 5</label>
            <input type="file" name="image5" class="form-control">
        </div>

        <!-- Latitude & Longitude -->
        <div class="mb-3">
            <label class="form-label">Latitude</label>
            <input type="text" id="latitude" name="latitude" value="<?= htmlspecialchars($row['latitude'] ?? '28.2096') ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Longitude</label>
            <input type="text" id="longitude" name="longitude" value="<?= htmlspecialchars($row['longitude'] ?? '83.9856') ?>" class="form-control" required>
        </div>

        <div id="map"></div>

        <button type="submit" name="update" class="btn btn-primary w-100">Update Destination</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
// Initialize map
var lat = parseFloat(document.getElementById('latitude').value);
var lng = parseFloat(document.getElementById('longitude').value);
var map = L.map('map').setView([lat, lng], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Draggable marker
var marker = L.marker([lat, lng], {draggable:true}).addTo(map);

// Update input fields when marker is dragged
marker.on('dragend', function(e){
    var pos = marker.getLatLng();
    document.getElementById('latitude').value = pos.lat.toFixed(6);
    document.getElementById('longitude').value = pos.lng.toFixed(6);
});
</script>

</body>
</html>
