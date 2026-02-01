<?php
include 'admin_guard.php';
include '../backend/connect.php';

$id = intval($_GET['id']);

if (isset($_POST['update'])) {
    $price = floatval($_POST['gprice']);
    $conn->query("UPDATE guiders SET gprice=$price WHERE guider_id=$id");
    header("Location: guides.php");
    exit();
}

$result = $conn->query("SELECT first_name, last_name, gprice FROM guiders WHERE guider_id=$id");
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Guider Price</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f4f6f9;
    font-family: 'Poppins', sans-serif;
}

.price-card {
    max-width: 420px;
    margin: 80px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.price-card h4 {
    color: #0d47a1;
    font-weight: 600;
    margin-bottom: 10px;
}

.guider-name {
    font-size: 0.95rem;
    color: #666;
    margin-bottom: 20px;
}

label {
    font-weight: 500;
}

.btn-success {
    width: 100%;
    font-weight: 500;
}

.btn-back {
    display: inline-block;
    margin-top: 15px;
    text-align: center;
    width: 100%;
    text-decoration: none;
    background: #6c757d;
    color: #fff;
    padding: 8px;
    border-radius: 6px;
    transition: 0.3s;
}

.btn-back:hover {
    background: #5a6268;
    color: #fff;
}
</style>
</head>

<body>

<div class="price-card">
    <h4>Edit Price</h4>
    <div class="guider-name">
        <?= htmlspecialchars($data['first_name'].' '.$data['last_name']); ?>
    </div>

    <form method="POST">
        <div class="mb-3">
            <label>Price (Rs.)</label>
            <input type="number"
                   name="gprice"
                   step="0.01"
                   value="<?= htmlspecialchars($data['gprice'] ?? 0); ?>"
                   class="form-control"
                   required>
        </div>

        <button type="submit" name="update" class="btn btn-success">
            Update Price
        </button>
    </form>

    <!-- Back Button -->
    <a href="guides.php" class="btn-back">
        ← Back to Guides
    </a>
</div>

</body>
</html>
