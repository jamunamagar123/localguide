<?php
include 'admin_guard.php';
include '../backend/connect.php';

$error = '';
// Handle form submission
if(isset($_POST['add'])){
    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $discount_price = $_POST['discount_price'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $image = "";

    if(!empty($_FILES['image']['name'])){
        $image = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/$image");
    }

    $stmt = $conn->prepare("
      INSERT INTO destination 
      (name, location, description, type, category, price, discount_price, image, latitude, longitude, created_at)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param(
      "ssssssssss",
      $name,
      $location,
      $description,
      $type,
      $category,
      $price,
      $discount_price,
      $image,
      $latitude,
      $longitude
    );
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Destination — Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
a.back-link { display:inline-block; margin-top:15px; color:#023E8A; text-decoration:none; }
a.back-link:hover { text-decoration:underline; }
</style>
</head>
<body>

<div class="container">
    <h2>Add New Destination</h2>
    <?php if($error) echo "<p class='error'>$error</p>"; ?>
    <form method="post" enctype="multipart/form-data">
        <label>Name</label>
        <input type="text" name="name" required>

        <label>Location</label>
        <input type="text" name="location" required>

        <label>Type</label>
        <select name="type" class="form-control" required>
            <option value="">-- Select Type --</option>
            <option value="villa">Villa</option>
            <option value="hotel">Hotel</option>
            <option value="apartment">Apartment</option>
            <option value="tour">Tour</option>
            <option value="activity">Activity</option>
            <option value="flight">Flight</option>
            <option value="offer">Offer</option>
        </select>

        <label>Description</label>
        <textarea name="description" rows="5" required></textarea>

        <label>Category</label>
        <select name="category" class="form-control" required>
            <option value="">-- Select Category --</option>
            <option value="Scenic Point">Scenic Point</option>
            <option value="Adventure">Adventure</option>
            <option value="Trekking">Trekking</option>
            <option value="Water Sports">Water Sports</option>
            <option value="Sightseeing">Sightseeing</option>
            <option value="Cultural">Cultural</option>
        </select>

        <label>Price</label>
        <input type="text" name="price" placeholder="e.g., $500 or Rs 5000" required>

        <label>Discount Price</label>
        <input type="text" name="discount_price" placeholder="e.g., $450 or Rs 4500">

        <label>Latitude</label>
        <input type="text" name="latitude" placeholder="e.g., 28.2096" required>

        <label>Longitude</label>
        <input type="text" name="longitude" placeholder="e.g., 83.9856" required>

        <label>Image</label>
        <input type="file" name="image" required>

        <button type="submit" name="add">Add Destination</button>
    </form>

    <a href="destinations.php" class="back-link">← Back to Destinations</a>
</div>

</body>
</html>
